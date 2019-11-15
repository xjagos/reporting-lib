<?php

final class ReportApplication extends PhabricatorApplication {

  public function getName() {
    return pht('Reporting');
  }

  public function getIcon() {
    return 'fa-line-chart';    
  }

  public function getBaseURI() {
    return '/report/';
  }


  public function getShortDescription() {
    return pht('Module for reporting');
  }

  public function getApplicationGroup() {
    return self::GROUP_UTILITIES;
  }

  public function isPrototype() {
    return false;
  }

  public function getRoutes() {
    return array(
      '/report/' => array(
        '' => 'ReportController',
        'employeeworkload/' => 'EmployeeWorkloadController',
        'tasktree/' => 'TaskTreeController',
        //'chart/' => 'ChartController',
      ),
    );
  }

}
