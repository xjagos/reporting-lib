<?php

final class ReportingMissingWorkboardColumnException extends Exception {
  public function __construct($name) {
    parent::__construct(
      pht("Column: '%s' in your project's workboard for Reporting lib regularly working does not exist.", $name)
    );
  }

}