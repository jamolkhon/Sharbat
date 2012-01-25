<?php

namespace Sharbat\Reflect;

class ObjectUtils {

  public function createValueObject($qualifiedClassName, $fields) {
    $object = new $qualifiedClassName();

    foreach ($fields as $field => $value) {
      $setterMethodName = 'set' . ucfirst($field);

      if (method_exists($qualifiedClassName, $setterMethodName)) {
        $object->$setterMethodName($value);
      }
    }

    return $object;
  }

  public function getFieldValueMap($object) {
    $fields = array();

    foreach (get_class_methods($object) as $methodName) {
      if (substr($methodName, 0, 3) === 'get') {
        $fields[lcfirst(substr($methodName, 3))] = $object->$methodName();
      }
    }

    return $fields;
  }

}
