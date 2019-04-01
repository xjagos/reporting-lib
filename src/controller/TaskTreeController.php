<?php

final class TaskTreeController extends PhabricatorController {    
  public function handleRequest(AphrontRequest $request) {    
    $viewer = $request->getUser();     

    $graph_limit = 100;    
    $task_graph = id(new ModifiedManiphestTaskGraph())
    ->setViewer($viewer)
    //->setSeedPHID($task->getPHID())
    ->setSeedPHID('PHID-TASK-qyx7u24traenypqshlaw')
    ->setLimit($graph_limit)
    ->loadGraph();

    $graph_table = $task_graph->newGraphTable();

    $className = get_class($graph_table);

    $crumbs = $this->buildApplicationCrumbs();
    //$crumbs->addTextCrumb(pht('Task tree')); 
    $crumbs->addTextCrumb($className); 
    
    return $this->newPage()
    ->setTitle('Task tree')
    ->setCrumbs($crumbs)
    ->appendChild(array($graph_table));
  }

  
}
