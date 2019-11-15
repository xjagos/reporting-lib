<?php

  const MANIPHEST_IBA_ESTIMATED_TIME = 'std:maniphest:iba:estimated-time';
  const MANIPHEST_IBA_ESTIMATED_TIME_TESTING = 'std:maniphest:iba:estimated-time-testing';
  const MANIPHEST_IBA_ACTUAL_TIME = 'std:maniphest:iba:actual-time';
  const MANIPHEST_IBA_ACTUAL_TIME_TESTING = 'std:maniphest:iba:actual-time-testing';


  /**
   * @param[in] object Phabricator object which has the custom field
   * @param[in] keyField Key of custom field
   * 
   * @return custom field value
   */
  function getCustomFieldValue($object, $keyField) {
    try {
      $field = PhabricatorCustomField::getObjectField(
        $object,
        PhabricatorCustomField::ROLE_DEFAULT,
        $keyField
      );
    
      id(new PhabricatorCustomFieldStorageQuery())
      ->addField($field)
      ->execute();
    
      $value = $field->getValueForStorage();
      return $value;
    } catch (TypeError $e) {      
      throw new ReportingMissingCustomFieldException($keyField);      
    }

  }