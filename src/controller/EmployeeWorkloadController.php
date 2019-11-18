<?php

const MANIPHEST_IBA_TESTER = 'std:maniphest:iba:tester';
const MANIPHEST_IBA_ESTIMATED_COMPLETION_DATE = 'std:maniphest:iba:estimated-completion-date';

const USER_REPORTING_OVERLOAD_RATIO = 'std:user:reporting:overload-ratio';
const USER_REPORTING_WORKTIME_TARIF = 'std:user:reporting:worktime-tarif';
const USER_REPORTING_ACCESS_CONTROL_LIST = 'std:user:reporting:access-control-list';

const STATUS_OPEN = 'open';
const STATUS_WAITING = 'waiting';
const STATUS_TEST = 'test';

const TT_PERIOD = 'The period for which workload is shown.';
const TT_WORKTIME_TARIF = 'Value between 0.0 - 1.0 (0.5 = half time - 4 hours a day, 1.0 = full time - 8 hours a day).It can be set by custom field: worktime-tarif. If it is not set, default value is 1.0 (8 hours a day).';
const TT_OVERLOAD_RATIO = 'Value between 0.0 - 1.0. Determines part of working time spended on meetings, business trips etc. It can be set by custom field: overload-ratio. If it is not set, default value is 0.2.';
const TT_HOURS_PERIOD = 'It is calculated as working days in period * worktime tarif * (1 - overload ratio).';
const TT_HOURS_FROM_TODAY = 'It is calculated as working days from today to the end of period * worktime tarif * (1 - overload ratio).';
const TT_WD_PLANNED_HOURS = 'It is calculated as estimated time - actual implementation time. It includes tasks in sprint, progress or test with status open, test or waiting.';
const TT_WD_PLANNED_HOURS_IN_PERIOD = 'It is calculated as estimated time - actual implementation time. It includes tasks in sprint, progress or test with status open, test or waiting. Tasks ending in chosen interval.';
const TT_OTA_PLANNED_HOURS = 'It is calculated as estimated time - actual implementation time. It includes tasks in sprint or in progress with status open or waiting.';
const TT_OTA_PLANNED_HOURS_IN_PERIOD = 'It is calculated as estimated time - actual implementation time. It includes tasks in sprint or in progress with status open or waiting. Tasks ending in chosen interval.';
const TT_OTE_PLANNED_HOURS = 'It is calculated as estimated time testing - actual time testing. It includes tasks in test with status test.';
const TT_OTE_PLANNED_HOURS_IN_PERIOD = 'It is calculated as estimated time testing - actual time testing. It includes tasks in test with status test. Tasks ending in chosen interval.';
const TT_TASKS_AFTER_COMPL_DATE = 'List of tasks after estimated date which are in sprint or progress with status open or waiting';
const TT_TESTS_AFTER_COMPL_DATE = 'List of tests after estimated date which are in test with status test';


final class EmployeeWorkloadController extends PhabricatorController {    
  private $viewer;

  // Page elements
  private $form;
  private $selectBox;
  private $startDateController;  
  private $endDateController; 

  private $options;
  private $startDate;
  private $endDate;

  //Udaje o zobrazenem uzivateli
  private $chosenUser;
  private $userName; 
  private $overloadRatio;
  private $tarif;

  private $openTasksArr;
  private $openTestsArr;

  public function handleRequest(AphrontRequest $request) {    
    $this->viewer = $request->getUser();
    $this->form = $this->createInputForm();    

    $title = pht('Employee Workload');
    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addTextCrumb(pht('Workload'));
        
    if ($request->isFormPost()) {      
      // Get selected username from selectbox
      $chosenNum = $request->getStr('name');
      $this->userName = $this->options[$chosenNum];

      // Load PhabricatorUser by userName
      $this->chosenUser = id(new PhabricatorPeopleQuery())
        ->setViewer($this->viewer)
        ->withUsernames(array($this->userName))
        ->needProfileImage(true)
        ->executeOne();
      
      // Load user custom fields: overloadRatio and tarif
      $this->overloadRatio = getCustomFieldValue($this->chosenUser, USER_REPORTING_OVERLOAD_RATIO);
      $this->overloadRatio = $this->overloadRatio == null ? 0.2 : $this->overloadRatio;
      $this->tarif = getCustomFieldValue($this->chosenUser, USER_REPORTING_WORKTIME_TARIF);
      $this->tarif = $this->tarif == null ? 1.0 : $this->tarif;

      // Get selected dates
      $this->startDate = $this->startDateController->readValueFromRequest($request);            
      $this->endDate = $this->endDateController->readValueFromRequest($request);                  
            
      // SelectBox set to value which was selected
      $this->selectBox->setValue($chosenNum);  
      
      // Create view with information about user workload
      $userBox = $this->buildUserView();  

      $this->form->appendChild($userBox);
    }
    
    return $this->newPage()
      ->setTitle($title)
      ->setCrumbs($crumbs)
      ->appendChild(array($this->form));
  }

  private function checkACL($userName) {
    $user = id(new PhabricatorPeopleQuery())
    ->setViewer($this->viewer)        
    ->withUsernames(array($userName))
    ->executeOne();

    $usersList = getCustomFieldValue($this->viewer, USER_REPORTING_ACCESS_CONTROL_LIST);

    if($usersList != null && count($usersList) > 0) {
      foreach ($usersList as $user) {      
        if ($user->getUsername() == $this->viewer->getUsername()) {
          return true;
        }
      }
    }
    return false;    
  }

  private function getTimezone() {
    $viewer = $this->getViewer();

    $user_zone = $viewer->getTimezoneIdentifier();
    $zone = new DateTimeZone($user_zone);
    return $zone;
  }

  private function getFirstDayOfWeek() {
    // +1 because of week starts as Sunday, but we want Monday
    $monday = mktime(0, 0, 0, date("n"), date("j") - date("N") + 1);
    $result = id(AphrontFormDateControlValue::newFromEpoch(
      $this->viewer,
      $monday));
    return $result;
  }

  private function getLastDayOfWeek() {    
    $monday = mktime(0, 0, 0, date("n"), date("j") - date("N") + 7);
    $result = id(AphrontFormDateControlValue::newFromEpoch(
      $this->viewer,
      $monday));
    return $result;
  }

  /* Function returning input form */
  private function createInputForm() {    
    // Get users name
    $user = new PhabricatorUser();
    $conn = $user->establishConnection('w');
    $rows = queryfx_all(
      $conn,
      'SELECT userName FROM %T',
      $user->getTableName());
    
    // Fill options with users names
    $this->options = array();    
    foreach ($rows as $row) {
      //if($this->checkACL($row['userName'])) {
      if(true) {
        array_push($this->options, $row['userName']);
      }          
    }
    if(!in_array($this->viewer->getUsername(), $this->options)) {
      array_push($this->options, $this->viewer->getUsername());
    }
    

    // Create select box      
    $this->selectBox = id(new AphrontFormSelectControl())
                        ->setLabel(pht('User'))                        
                        ->setName('name')                                                                    
                        ->setOptions($this->options);
  

    // Create start date picker                        
    $this->startDateController = id(new AphrontFormDateControl())
                              ->setUser($this->viewer)                              
                              ->setValue($this->getFirstDayOfWeek())
                              ->setName('startDate')
                              //->setInitialTime($time)                                
                              ->setLabel(pht('Start'))
                              ->setIsTimeDisabled(true);

    // Create end date picker    
    $this->endDateController = id(new AphrontFormDateControl())
                              ->setUser($this->viewer)
                              ->setValue($this->getLastDayOfWeek())
                              ->setName('endDate')
                              ->setLabel(pht('End'))
                              ->setIsTimeDisabled(true);
        
    // Create input form                              
    $form = id(new AphrontFormView())
            //->setFullWidth(true)
            ->setUser($this->viewer)
            ->appendChild(id($this->selectBox))      
            ->appendChild(id($this->startDateController))
            ->appendChild(id($this->endDateController))                      
            ->appendChild(
                id(new AphrontFormSubmitControl())
                ->setValue(pht('Submit'))
              );    

    $panel = id(new PHUIObjectBoxView())
             ->setForm($form)
             ->setHeaderText(pht('Employee Workload'));             

    return $panel;
  }

  private function getItemWithTooltip($value, $tooltip) {
    $item = id(new PHUIListItemView())
    ->setName($value)
    ->setTooltip($tooltip);

    $result = id(new PHUIListView())
    ->addMenuItem($item);

    return $result;
  }

  private function getItemWithTooltipX($value, $tooltip) {
    $item = id(new PHUIListItemView())
    ->setName($value)
    ->setTooltip($tooltip);

    $result = id(new PHUIListView())
    ->addMenuItem($item);

    return $result;
  }

  /**
   * @return PHUIFormLayoutView view with information about employee workload
   */
  private function buildUserView() {
    // Header of result view
    $picture = $this->chosenUser->getProfileImageURI();  
        
    $header = id(new PHUIHeaderView())
      ->setHeader($this->userName)
      ->setSubHeader($this->getItemWithTooltip($this->getChosenEpoqueStr(), pht(TT_PERIOD)))
      ->setImage($picture);        


    // Open tasks view      
    $tasksList = $this->getTasksList();   

    $workload = $this->calculateWorkload($this->openTasksArr);

    $openTasksPr = id(new PHUIPropertyListView())
    ->addProperty($this->getItemWithTooltip(pht('Planned hours'), pht(TT_OTA_PLANNED_HOURS)), $workload['all'])
    ->addProperty($this->getItemWithTooltip(pht('Planned hours - tasks ending in period'), pht(TT_OTA_PLANNED_HOURS_IN_PERIOD)), $workload['in_period']);

    $openTasks = id(new PHUIObjectBoxView())
    ->appendChild($openTasksPr)    
    ->appendChild($tasksList)
    ->setHeaderText(pht('Open tasks'))      
    ->setBackground(PHUIObjectBoxView::BLUE_PROPERTY); 

    // Open tests view
    $testsList = $this->getTestingTasksList();
    
    //$tasksForTesting = $this->getTaskForTesting();
    $testingWorkload = $this->calculateWorkload($this->openTestsArr, false);

    $openTestsPr = id(new PHUIPropertyListView())                
    ->addProperty($this->getItemWithTooltip(pht('Planned hours'), pht(TT_OTE_PLANNED_HOURS)), $testingWorkload['all'])
    ->addProperty($this->getItemWithTooltip(pht('Planned hours - tasks ending in period'), pht(TT_OTE_PLANNED_HOURS_IN_PERIOD)), $testingWorkload['in_period']);

    $openTests = id(new PHUIObjectBoxView())
      ->appendChild($openTestsPr)
      ->appendChild($testsList)
      ->setHeaderText(pht('Open tests'))      
      ->setBackground(PHUIObjectBoxView::BLUE_PROPERTY); 


    // Workload details view
    //$totalEstHours = $workload + $testingWorkload;
    $workingHoursPeriod = $this->getWorkingHours($this->getWorkingDays($this->startDate, $this->endDate, array()));    
    $workingHoursFromToday = $this->getWorkingHours($this->getWorkingDays(strtotime('today midnight'),$this->endDate, array()));
    
    $wlDetails = id(new ModifiedPHUIPropertyListView())      
      ->addProperty($this->getItemWithTooltip(pht('Worktime tarif'), pht(TT_WORKTIME_TARIF)), $this->tarif)      
      ->addProperty($this->getItemWithTooltip(pht('Overload ratio'), pht(TT_OVERLOAD_RATIO)), $this->overloadRatio)
      ->addProperty($this->getItemWithTooltip(pht('Total estimated hours in period'), pht(TT_HOURS_PERIOD)), $workingHoursPeriod)
      ->addProperty($this->getItemWithTooltip(pht('Total estimated hours (from today)'), pht(TT_HOURS_FROM_TODAY)), $workingHoursFromToday)
      ->addProperty($this->getItemWithTooltip(pht('Planned hours'), pht(TT_WD_PLANNED_HOURS)), $workload['all'] + $testingWorkload['all'])
      ->addProperty($this->getItemWithTooltip(pht('Planned hours - tasks ending in period'), pht(TT_WD_PLANNED_HOURS_IN_PERIOD)), $workload['in_period'] + $testingWorkload['in_period']);
      //->addProperty(pht('Total estimated hours'), $totalEstHours.'/'.$workingHours);        

    $workloadDetails = id(new PHUIObjectBoxView())    
      ->appendChild($wlDetails)      
      ->setHeaderText(pht('Workload details'))
      ->setBackground(PHUIObjectBoxView::BLUE_PROPERTY);                 


    // Result view
    $view = id(new PHUIFormLayoutView())
      ->appendChild($header)
      ->appendChild($workloadDetails)            
      ->appendChild($openTasks) 
      ->appendChild($openTests);

    return $view;
  }

  private function getChosenEpoqueStr() {
    
    return pht(date('D', $this->startDate)). date(' d-m-Y', $this->startDate).' - '.pht(date('D', $this->endDate)). date(' d-m-Y', $this->endDate);
  }

  private function removeTasksEndingBeforeStart($tasks) {
    $result = array();    
    foreach($tasks as $task) {
      $completionDate = getCustomFieldValue($task, MANIPHEST_IBA_ESTIMATED_COMPLETION_DATE);
      if($completionDate >= $this->startDate) {
        array_push($result, $task);
      }
    }    
    return $result;
  }

  private function getUndoneTasks($tasks) {
    $result = array();    
    foreach($tasks as $task) {
      $completionDate = getCustomFieldValue($task, MANIPHEST_IBA_ESTIMATED_COMPLETION_DATE);
      if($completionDate < $this->startDate) {
        array_push($result, $task);
      }
    }    
    return $result;
  }

  private function getTasksList() {    
    $columns = $this->getColumns(['Sprint', 'In Progress']);    
    $list = array();

    $colPHIDs = array();
    foreach ($columns as $column) {
        array_push($colPHIDs, $column->getPHID());
    }
    /* Tasks after estimated end date */               
    $tasks = id(new ManiphestTaskQuery())
          ->setViewer($this->viewer)
          ->withOwners(array($this->chosenUser->getPHID()))                      
          ->withColumnPHIDs($colPHIDs)
          ->needProjectPHIDs(true)    
          ->execute();

    $tasks = $this->getUndoneTasks($tasks);

    if(count($tasks)) {      
      $tasksList = id(new ModifiedManiphestTaskResultListView())
        ->setUser($this->viewer)
        ->setTasks($tasks)
        ->setSavedQuery(new PhabricatorSavedQuery());

      //$header = id(new PHUIHeaderView())
      //->setHeader('Tasks after completion date');
      //->setNoBackground(true);

      array_push($list, id(new PHUIInfoView())                
      //->setTitle($this->getItemWithTooltip('Tasks after completion date', TT_TASKS_AFTER_COMPL_DATE))
      ->setTitle(pht('Tasks after completion date'))
      ->setIcon('fa-exclamation')
      ->setFlush(false)
      ->setSeverity(PHUIInfoView::SEVERITY_ERROR)
      //->appendChild($this->getItemWithTooltipX('Tasks after completion date', TT_TASKS_AFTER_COMPL_DATE))
      //->appendChild($header)
      ->appendChild($tasksList));
    }        

    $openTasksArr = array();    
    foreach ($columns as $column) {                  
      $tasks = id(new ManiphestTaskQuery())
            ->setViewer($this->viewer)
            ->withOwners(array($this->chosenUser->getPHID()))            
            ->withStatuses([STATUS_OPEN,STATUS_WAITING])            
            ->withColumnPHIDs(array($column->getPHID()))            
            ->needProjectPHIDs(true)    
            ->execute();

      $tasks = $this->removeTasksEndingBeforeStart($tasks);
      
      if(count($tasks)) {
        $openTasksArr = array_merge($openTasksArr, $tasks);        
        $tasksList = id(new ModifiedManiphestTaskResultListView())
          ->setUser($this->viewer)
          ->setTasks($tasks)
          ->setSavedQuery(new PhabricatorSavedQuery());                             
        
        array_push($list, id(new PHUIInfoView())                
        ->setTitle($column->getName())
        ->setIcon(null)
        ->setFlush(false)
        ->setSeverity(PHUIInfoView::SEVERITY_NODATA)        
        ->appendChild($tasksList));
      }      
    }
    $this->openTasksArr = $openTasksArr;

    return $list;
  }

  private function getUndoneTestsByTester($tests) {
    $result = array();    
    foreach($tests as $test) {
      $completionDate = getCustomFieldValue($test, MANIPHEST_IBA_ESTIMATED_COMPLETION_DATE);      
      $tester = getCustomFieldValue($test, MANIPHEST_IBA_TESTER);
      $tstr = substr($tester, 2, -2);      
      if($completionDate < $this->startDate && $tstr == $this->chosenUser->getPHID()) {
        array_push($result, $test);
      }
    }    
    return $result;
  }

  private function getTestingTasksList() {    
    $columns = $this->getColumns(['In Test']);    
    $list = array();
    
    $colPHIDs = array();
    foreach ($columns as $column) {
        array_push($colPHIDs, $column->getPHID());
    }
    /* Tests after estimated end date */               
    $tasks = id(new ManiphestTaskQuery())
          ->setViewer($this->viewer)          
          ->withColumnPHIDs($colPHIDs)
          ->needProjectPHIDs(true)    
          ->execute();

    $tasks = $this->getUndoneTestsByTester($tasks);

    if(count($tasks)) {      
      $tasksList = id(new ModifiedManiphestTaskResultListView())
        ->setUser($this->viewer)
        ->setTasks($tasks)
        ->setSavedQuery(new PhabricatorSavedQuery());

      array_push($list, id(new PHUIInfoView())                
      ->setTitle(pht('Tests after completion date'))
      ->setIcon('fa-exclamation')
      ->setFlush(false)
      ->setSeverity(PHUIInfoView::SEVERITY_ERROR)
      ->appendChild($tasksList));
    } 

    $openTestsArr = array();
    foreach ($columns as $column) {            
      $tasks = id(new ManiphestTaskQuery())
            ->setViewer($this->viewer)            
            ->withStatuses([STATUS_TEST])
            ->withColumnPHIDs(array($column->getPHID()))            
            ->needProjectPHIDs(true)    
            ->execute();                        

      $userTasks = array();
      foreach($tasks as $task) {
          $tester = getCustomFieldValue($task, MANIPHEST_IBA_TESTER);
          $tstr = substr($tester, 2, -2);
          if($tstr == $this->chosenUser->getPHID()) {
            array_push($userTasks, $task);
          }        
      }

      $userTasks = $this->removeTasksEndingBeforeStart($userTasks);
      
      if(count($userTasks)) {
        $openTestsArr = array_merge($openTestsArr, $userTasks);        
        $tasksList = id(new ModifiedManiphestTaskResultListView())
          ->setUser($this->viewer)
          ->setTasks($userTasks)
          ->setSavedQuery(new PhabricatorSavedQuery());                             
        
        array_push($list, id(new PHUIInfoView())                
        ->setTitle($column->getName())
        ->setIcon(null)
        ->setFlush(false)
        ->setSeverity(PHUIInfoView::SEVERITY_NODATA)        
        ->appendChild($tasksList));
      }      
    }
    $this->openTestsArr = $openTestsArr;

    return $list;
  }

  /**
   * @param[in] tasks
   * @param[in] implTime calculate time for implementation (true) or for testing (false)
   * 
   * @return array with estimated time, actual time, actual / estimated
   */
  private function calculateWorkload($tasks, $implTime = true) {
    $estimatedHours = 0;
    $actualHours = 0;

    $estimatedHoursEP = 0;
    $actualHoursEP = 0;
        
    $estimatedTime = $implTime ? MANIPHEST_IBA_ESTIMATED_TIME: MANIPHEST_IBA_ESTIMATED_TIME_TESTING;
    $actualTime = $implTime ? MANIPHEST_IBA_ACTUAL_TIME : MANIPHEST_IBA_ACTUAL_TIME_TESTING;
        
    foreach ($tasks as $task) {
      $eh = getCustomFieldValue($task, $estimatedTime);
      $ah = getCustomFieldValue($task, $actualTime);
      $estimatedHours += $eh;
      $actualHours += $ah;

      if(getCustomFieldValue($task, MANIPHEST_IBA_ESTIMATED_COMPLETION_DATE) 
          <= $this->endDate) {
        $estimatedHoursEP += $eh;
        $actualHoursEP += $ah;                
      }

    }
    $result = array(
      'all' => $estimatedHours - $actualHours,
      'in_period' => $estimatedHoursEP - $actualHoursEP
    );    

    return $result;    
  }

  /**   
   * @return tasks chosenUser is tester
   */
  private function getTaskForTesting() {    
    $tasks = id(new ManiphestTaskQuery())
            ->setViewer($this->viewer)    
            ->withStatus(ManiphestTaskQuery::STATUS_OPEN)    
            ->withDateCreatedAfter($this->startDate)
            ->needProjectPHIDs(true)    
            ->execute();

    $result = array();        
    foreach($tasks as $task) {
        $tester = getCustomFieldValue($task, MANIPHEST_IBA_TESTER);
        $tstr = substr($tester, 2, -2);
        if($tstr == $this->chosenUser->getPHID()) {
          array_push($result, $task);
        }        
    }
    
    return $result;
  }  

  /**   
   * @return all projects in Phabricator
   */
  private function getProjects() {
    $projects = id(new PhabricatorProjectQuery())
      ->setViewer($this->viewer)
      ->withMemberPHIDs(array($this->chosenUser->getPHID())) 
      ->execute(); 

    return $projects;
  }

  /**
   * @param [in] namesArr array with column names we want to get
   * @return workboard columns
   */
  private function getColumns($namesArr) {
    $columns = id(new PhabricatorProjectColumnQuery())
       ->setViewer($this->viewer)      
       ->execute();   

    $results = array();
    foreach($namesArr as $name) {            
      foreach($columns as $column) {
        if($name == $column->getName()) {          
          array_push($results, $column);          
        }
      }
    }
    return $results;
  }
    
  /**
   * @param[in] workingDays number of workingDays
   * 
   * @return total number of working hours. Overload ratio and working tarif are
   *         taken into account
   */
  private function getWorkingHours($workingDays) {
      $result = ($workingDays * 8 * $this->tarif * (1 - $this->overloadRatio));

      $fresult = number_format((float)$result, 1, '.', '');

      return $fresult;
  }

  /**
   * Reference: https://stackoverflow.com/questions/336127/calculate-business-days
   * 
   * @param[in] startDate start date of interval
   * @param[in] endDate end date of interval
   * @param[in] holidays array with dates of holidays in string format "YYYY-MM-DD"
   * 
   * @return total number of working between startDate and endDate
   */
  private function getWorkingDays($startDate,$endDate,$holidays){
    /**
     * The total number of days between the two dates
     * We compute number of seconds and divide it to 60*60*24
     */    
    $days = ($endDate - $startDate) / 86400 + 1;

    $fullWeeks = floor($days / 7);
    $remainingDays = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $firstDay = date("N", $startDate);
    $lastDay = date("N", $endDate);

    /**
     * The two can be equal in leap years when february has 29 days, the equal sign is added here
     * In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
     */
    if ($firstDay <= $lastDay) {
        if ($firstDay <= 6 && 6 <= $lastDay) $remainingDays--;
        if ($firstDay <= 7 && 7 <= $lastDay) $remainingDays--;
    }
    else {
        // the day of the week for start is later than the day of the week for end
        if ($firstDay == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $remainingDays--;

            if ($lastDay == 6) {
                // if the end date is a Saturday, then we subtract another day
                $remainingDays--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $remainingDays -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
    //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $fullWeeks * 5;
    if ($remainingDays > 0 )
    {
      $workingDays += $remainingDays;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $timeStamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $timeStamp && $timeStamp <= $endDate && date("N",$timeStamp) != 6 && date("N",$timeStamp) != 7)
            $workingDays--;
    }

    return $workingDays;    
  }
}
