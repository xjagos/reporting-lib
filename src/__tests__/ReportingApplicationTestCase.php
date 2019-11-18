<?php

/**
 * Tests for testing whole application working
 */
final class ReportingApplicationTestCase extends PhabricatorTestCase {
  // Tests if reporting is installed to Phabricator
  public function testReportingIsInstalled() {    
    $installedApps = PhabricatorApplication::getAllInstalledApplications();
    
    $isInstalled = false;
    foreach ($installedApps as $app) {
      $isInstalled = $app->getName() == 'Reporting';
      if($isInstalled) {
        break;
      }               
    }
    $this->assertTrue($isInstalled, pht('Reporting module is installed'));
  }

  public function testHttpResponse() {
    $future = new HTTPFuture('http://phab.example.com/report');
    list($status, $body, $headers) = $future->resolve();
    $status_code = $status->getStatusCode();
    $this->assertEqual(302, $status_code, pht('HTTP response code == 302'));
  }
}