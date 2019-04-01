<?php

final class ReportApplication extends PhabricatorApplication {

  public function getName() {
    return pht('Report');
  }

  public function getIcon() {
    return 'fa-line-chart';    
  }

  public function getBaseURI() {
    return '/report/';
  }


  public function getShortDescription() {
    return 'Module for reporting';
  }

  public function getApplicationGroup() {
    return self::GROUP_UTILITIES;
  }

  public function isPrototype() {
    return true;
  }

  public function getRoutes() {
    return array(
      '/report/' => array(
        '' => 'ReportController',
        'employeeworkload/' => 'EmployeeWorkloadController',
        'tasktree/' => 'TaskTreeController',
        'chart/' => 'ChartController',
      ),
    );
  }

}
