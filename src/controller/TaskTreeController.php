<?php

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
        $rows[] = array('Open', 0);
        $rows[] = array('Waiting', 0);
        $rows[] = array('Test', 0);
        $rows[] = array('Resolved', 0);

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

  
}
