<?php

final class PhabricatorTasksDatasource
  extends PhabricatorTypeaheadDatasource {

  public function getBrowseTitle() {
    return pht('Browse Task');
  }

  public function getPlaceholderText() {
    return pht('Type a task name...');
  }

  public function getDatasourceApplicationClass() {
    return 'PhabricatorManiphestApplication';
  }

  public function loadResults() {
    $viewer = $this->getViewer();

    $query = id(new ManiphestTaskQuery())
      ->setOrderVector(array('id'));      

    $this->setLimit(1000); 
    $tasks = $this->executeQuery($query);  
    
    $searchedValue = strtolower($this->getQuery());
    $results = array();
    foreach ($tasks as $task) {
      $taskTitle = $task->getTitle();
      $fullName = 'T'.$task->getId().': '.$taskTitle;
      $fullNameLC = strtolower($fullName);
        if ($searchedValue == null || strpos($fullNameLC, $searchedValue) !== false) {
        $phid = $task->getPHID();                        
        $result = id(new PhabricatorTypeaheadResult())
            ->setName($fullName)
            ->setURI('/p/'.$taskTitle.'/')
            ->setPHID($phid)
            ->setPriorityString($taskTitle)
            ->setPriorityType('task')
            ->setAutocomplete('@'.$taskTitle);

        //$result->setIcon('fa-envelope-o');
        $statusName = ManiphestTaskStatus::getTaskStatusName($task->getStatus());
        $result->addAttribute(
            array(
                null,
                ' ',
                $statusName,
            ));

        $result->setDisplayType('Display type');

        $results[] = $result;
      }
    }

    return $results;    
  }
}