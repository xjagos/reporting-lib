<?php

final class ReportingMissingCustomFieldException extends Exception {
  public function __construct($name) {
    parent::__construct(
      pht("Custom field: '%s' is probably not created", $name)
    );
  }

}