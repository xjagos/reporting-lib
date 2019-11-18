<?php

final class ReportingTaskTreeTestCase extends PhabricatorTestCase {
  public function testHttpResponse() {
    $future = new HTTPFuture('http://phab.example.com/report/tasktree/');
    list($status, $body, $headers) = $future->resolve();
    $status_code = $status->getStatusCode();
    $this->assertEqual(200, $status_code, pht('HTTP response code == 200'));
  }

}