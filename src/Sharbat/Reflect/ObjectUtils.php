<?php

namespace Sharbat\Reflect;

/**
 * \Sharbat\@Singleton
 */
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
    $fieldValueMap = array();
    $fields = $this->getFields(get_class($object));

    foreach ($fields as $field) {
      $getterMethodName = 'get' . ucfirst($field);;

      if (method_exists($object, $getterMethodName)) {
        $fieldValueMap[$field] = $object->$getterMethodName();
      }
    }

    return $fieldValueMap;
  }

  public function getFields($qualifiedClassName) {
    $fields = array();

    foreach (get_class_methods($qualifiedClassName) as $methodName) {
      if (strlen($methodName) > 3 && substr($methodName, 0, 3) === 'get') {
        $fields[] = lcfirst(substr($methodName, 3));
      }
    }

    return $fields;
  }

}
