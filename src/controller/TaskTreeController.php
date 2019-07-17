<?php

include(dirname(__FILE__)."/../utilities/CustomFieldUtilities.php");

final class TaskTreeController extends PhabricatorController {    

  private $selectBox;  

  public function handleRequest(AphrontRequest $request) {    
    $viewer = $request->getUser();

    $tasks_control = id(new AphrontFormTokenizerControl())
      ->setLabel(pht('Tasks'))
      ->setName('tasks')
      ->setLimit(1)
      ->setDatasource(new PhabricatorTasksDatasource());

    $form = id(new AphrontFormView())
      ->setViewer($viewer)
      ->appendControl($tasks_control)
      ->appendChild(
        id(new AphrontFormSubmitControl())
          ->setValue(pht('Submit'))
      );

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addTextCrumb(pht('Task tree'));   
      
    $page = $this->newPage()
      ->setTitle('Task tree')
      ->setCrumbs($crumbs)
      ->appendChild($form);
      
    if ($request->isFormPost()) {
      $tasks = $request->getArr('tasks');            

      //$tasks_control->setValue($tasks);      

      if (count($tasks) == 1) {
        $resArr = $this->getEstimatedHoursByStatuses($viewer, $tasks[0]);

        $total = array_sum($resArr);
        $rows[] = array('Open', $resArr['open']);
        $rows[] = array('Waiting', $resArr['waiting']);
        $rows[] = array('Test', $resArr['test']);
        $rows[] = array('Resolved', $resArr['resolved']);
        $rows[] = array('Total', $total);

        $sumTable = id(new AphrontTableView($rows))
          ->setNoDataString(pht('No tasks.'))
          ->setHeaders(
            array(              
              pht('Status'),
              pht('Σ (hours)'),              
          ))
          ->setColumnClasses(
            array(              
              'status',
              'sum',
          ));

        $sumTable->setRowClasses(['','','','','highlighted']);

        $page->appendChild(array($sumTable));
        $emptyHeader = id(new PHUIHeaderView())
          ->setHeader('');
        $page->appendChild(array($emptyHeader));


        $task_graph = id(new ModifiedManiphestTaskGraph())
        ->setViewer($viewer)
        ->setSeedPHID($tasks[0])
        //->setLoadEntireGraph(true)      
        ->loadGraph();

        $graph_table = $task_graph->newGraphTable();
        $className = get_class($graph_table);  

        $page->appendChild(array($graph_table));
      }
    }      

    return $page;
  }

  private function getEstimatedHoursByStatuses($viewer, $phid) {
    $tasks = id(new ManiphestTaskQuery())
    ->setViewer($viewer)
    ->withPHIDs(array($phid))
    ->execute();

    $result = array();

    foreach ($tasks as $task) {            
      $queue = new SplQueue();
      $queue->push($task);
      $visited = array($task->getId());
      $open = 0;
      $waiting = 0;
      $test = 0;
      $resolved = 0;

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
        $status = $actTask->getStatus();
        $et = getCustomFieldValue($actTask, MANIPHEST_IBA_ESTIMATED_TIME);
        if ($status == 'open') {
          $open += $et;  
        }
        else if ($status == 'waiting'){
          $waiting += $et;
        }
        else if ($status == 'test'){
          $test += $et;
        }
        else if ($status == 'resolved'){
          $resolved += $et;
        }
      }                        
      $result['open'] = $open;
      $result['waiting'] = $waiting;
      $result['test'] = $test;
      $result['resolved'] = $resolved;
    }

    return $result;
  }  


  
}
