<?php

final class ModifiedManiphestTaskGraph
  extends PhabricatorObjectGraph {

  private $seedMaps = array();

  protected function getEdgeTypes() {
    return array(
      ManiphestTaskDependedOnByTaskEdgeType::EDGECONST,
      ManiphestTaskDependsOnTaskEdgeType::EDGECONST,
    );
  }

  protected function getParentEdgeType() {
    return ManiphestTaskDependsOnTaskEdgeType::EDGECONST;
  }

  protected function newQuery() {
    return new ManiphestTaskQuery();
  }

  protected function isClosed($object) {
    return $object->isClosed();
  }

  private function getHours($viewer, $phid) {
    $tasks = id(new ManiphestTaskQuery())
    ->setViewer($viewer)
    ->withPHIDs(array($phid))
    ->execute();

    $result = array();

    foreach ($tasks as $task) {      
      $et = getCustomFieldValue($task, MANIPHEST_IBA_ESTIMATED_TIME);
      $et = $et == null? 0 : $et;
      $at = getCustomFieldValue($task, MANIPHEST_IBA_ACTUAL_TIME);      
      $at = $at == null? 0 : $at;
      $ett = getCustomFieldValue($task, MANIPHEST_IBA_ESTIMATED_TIME_TESTING);      
      $ett = $ett == null? 0 : $ett;
      $att = getCustomFieldValue($task, MANIPHEST_IBA_ACTUAL_TIME_TESTING);      
      $att = $att == null? 0 : $att;

      $queue = new SplQueue();
      $queue->push($task);
      $visited = array($task->getId());
      $et_sum = 0;
      $at_sum = 0;
      $ett_sum = 0;
      $att_sum = 0;

      while (!$queue->isEmpty()) {
        $actTask = $queue->dequeue();

        $children = id(new ManiphestTaskQuery())
        ->setViewer($viewer)
        ->withParentTaskIDs(array($actTask->getId()))
        ->execute();
              
        foreach ($children as $child) {  
          if(!in_array($child->getId(), $visited)) {
            array_push($visited, $child->getId());
            $queue->push($child);
          }
        }
        $et_sum += getCustomFieldValue($actTask, MANIPHEST_IBA_ESTIMATED_TIME);
        $at_sum += getCustomFieldValue($actTask, MANIPHEST_IBA_ACTUAL_TIME);
        $ett_sum += getCustomFieldValue($actTask, MANIPHEST_IBA_ESTIMATED_TIME_TESTING);
        $att_sum += getCustomFieldValue($actTask, MANIPHEST_IBA_ACTUAL_TIME_TESTING);
      }                  

      $result['et_res'] = $et.' / '.$et_sum;
      $result['at_res'] = $at.' / '.$at_sum;
      $result['ett_res'] = $ett.' / '.$ett_sum;
      $result['att_res'] = $att.' / '.$att_sum;
    }

    return $result;
  }  

  protected function newTableRow($phid, $object, $trace) {
    $viewer = $this->getViewer();

    Javelin::initBehavior('phui-hovercards');

    if ($object) {
      $status = $object->getStatus();
      $priority = $object->getPriority();
      $status_icon = ManiphestTaskStatus::getStatusIcon($status);
      $status_name = ManiphestTaskStatus::getTaskStatusName($status);

      $priority_color = ManiphestTaskPriority::getTaskPriorityColor($priority);
      if ($object->isClosed()) {
        $priority_color = 'grey';
      }

      $status = array(
        id(new PHUIIconView())->setIcon($status_icon, $priority_color),
        ' ',
        $status_name,
      );

      $owner_phid = $object->getOwnerPHID();
      if ($owner_phid) {
        $assigned = $viewer->renderHandle($owner_phid);
      } else {
        $assigned = phutil_tag('em', array(), pht('None'));
      }

      $link = javelin_tag(
        'a',
        array(
          'href' => $object->getURI(),
          'sigil' => 'hovercard',
          'meta' => array(
            'hoverPHID' => $object->getPHID(),
          ),
        ),
        $object->getTitle());

      $link = array(
        phutil_tag(
          'span',
          array(
            'class' => 'object-name',
          ),
          $object->getMonogram()),
        ' ',
        $link,
      );
    } else {
      $status = null;
      $assigned = null;
      $link = $viewer->renderHandle($phid);
    }

    if ($this->isParentTask($phid)) {
      $marker = 'fa-chevron-circle-up bluegrey';
      $marker_tip = pht('Direct Parent');
    } else if ($this->isChildTask($phid)) {
      $marker = 'fa-chevron-circle-down bluegrey';
      $marker_tip = pht('Direct Subtask');
    } else {
      $marker = null;
    }

    if ($marker) {
      $marker = id(new PHUIIconView())
        ->setIcon($marker)
        ->addSigil('has-tooltip')
        ->setMetadata(
          array(
            'tip' => $marker_tip,
            'align' => 'E',
          ));
    }
    
    $result = $this->getHours($viewer, $phid);    

    return array(
      $marker,
      $trace,
      $status,
      //$assigned,
      $link,  
      $result['et_res'],      
      $result['at_res'],            
      $result['ett_res'],      
      $result['att_res']
    );
  }

  protected function newTable(AphrontTableView $table) {
    return $table
      ->setHeaders(
        array(
          null,
          null,
          pht('Status'),
          //pht('Assigned'),
          pht('Task'),
          pht('Estimated'),
          pht('Actual'),
          pht('Estimated - testing'),
          pht('Actual - testing'),
        ))
      ->setColumnClasses(
        array(
          'nudgeright',
          'threads',
          'graph-status',
          //null,
          'wide pri object-link',
          'task-estimated',
          'task-actual',
          'task-estimated-testing',
          'task-actual-testing'
        ))
      ->setColumnVisibility(
        array(
          true,
          !$this->getRenderOnlyAdjacentNodes(),
        ));
  }

  private function isParentTask($task_phid) {
    $map = $this->getSeedMap(ManiphestTaskDependedOnByTaskEdgeType::EDGECONST);
    return isset($map[$task_phid]);
  }

  private function isChildTask($task_phid) {
    $map = $this->getSeedMap(ManiphestTaskDependsOnTaskEdgeType::EDGECONST);
    return isset($map[$task_phid]);
  }

  private function getSeedMap($type) {
    if (!isset($this->seedMaps[$type])) {
      $maps = $this->getEdges($type);
      $phids = idx($maps, $this->getSeedPHID(), array());
      $phids = array_fuse($phids);
      $this->seedMaps[$type] = $phids;
    }

    return $this->seedMaps[$type];
  }

  protected function newEllipsisRow() {
    return array(
      null,
      null,
      null,
      null,
      pht("\xC2\xB7 \xC2\xB7 \xC2\xB7"),
    );
  }
}
