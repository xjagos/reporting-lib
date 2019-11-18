<?php

/**
 * Tests for testing Employee Workload
 */
final class ReportingEmployeeWorkloadTestCase extends PhabricatorTestCase {
  public function testHttpResponse() {
    $future = new HTTPFuture('http://phab.example.com/report/employeeworkload/');
    list($status, $body, $headers) = $future->resolve();
    $status_code = $status->getStatusCode();
    $this->assertEqual(200, $status_code, pht('HTTP response code == 200'));
  }
}