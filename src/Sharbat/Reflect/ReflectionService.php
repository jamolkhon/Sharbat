<?php

namespace Sharbat\Reflect;

use \Serializable;
use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionMethod;
use \RuntimeException;

class ReflectionService implements Serializable {

  private $annotationParser;
  private $objectUtils;

  /**
   * @var \Sharbat\Reflect\Clazz[]
   */
  private $classes = array();

  /**
   * @var \ReflectionClass[]
   */
  private $reflections = array();

  private $annotations = array();

  public function __construct(AnnotationParser $annotationParser,
      ObjectUtils $objectUtils) {
    $this->annotationParser = $annotationParser;
    $this->objectUtils = $objectUtils;
  }

  /**
   * @param string
   * @return \ReflectionClass
   */
  public function getReflectionClass($qualifiedClassName) {
    if (!isset($this->reflections[$qualifiedClassName])) {
      $this->reflections[$qualifiedClassName] = new ReflectionClass(
        $qualifiedClassName);
    }

    return $this->reflections[$qualifiedClassName];
  }

  /**
   * @param string
   * @return \Sharbat\Reflect\Clazz
   */
  public function getClass($qualifiedClassName) {
    if (isset($this->classes[$qualifiedClassName])) {
      return $this->classes[$qualifiedClassName];
    }

    $reflection = $this->getReflectionClass($qualifiedClassName);
    $annotations = $this->getAnnotations($qualifiedClassName,
      $reflection->getDocComment());
    $parentReflectionClass = $reflection->getParentClass();
    $parentClass = null;

    if ($parentReflectionClass != null) {
      $parentClass = $this->getClass($parentReflectionClass->getName());
    }

    $interfaces = array();

    foreach ($reflection->getInterfaceNames() as $qualifiedInterfaceName) {
      $interfaces[] = $this->getClass($qualifiedInterfaceName);
    }

    $class = new Clazz($reflection, $annotations, $parentClass, $interfaces);
    $this->setClassFields($class);
    return $this->classes[$qualifiedClassName] = $class;
  }

  private function setClassFields(Clazz $class) {
    $reflection = $class->getInternalReflection();
    $fields = array();

    foreach ($reflection->getProperties() as $property) {
      $propertyName = $property->getName();
      $fields[$propertyName] = $this->createField($property, $class);
    }

    $this->setField($class, 'fields', $fields);
    $methods = array();

    foreach ($reflection->getMethods() as $method) {
      /* @var \ReflectionMethod $method */
      $methods[$method->getName()] = $this->createMethod($method, $class);
    }

    $this->setField($class, 'methods', $methods);
  }

  private function setField($instance, $property, $value) {
    $reflection = $this->getReflectionClass(get_class($instance));
    $propertyReflection = $reflection->getProperty($property);
    $propertyReflection->setAccessible(true);
    $propertyReflection->setValue($instance, $value);
  }

  private function createField(ReflectionProperty $property, Clazz $parent) {
    $uniqueName = $parent->getQualifiedName() . ':' . $property->getName();
    $annotations = $this->getAnnotations($uniqueName, $property->getDocComment());
    return new Field($property, $annotations, $parent);
  }

  private function createMethod(ReflectionMethod $method, Clazz $parent) {
    $uniqueName = $parent->getQualifiedName() . ':' . $method->getShortName();
    $annotations = $this->getAnnotations($uniqueName, $method->getDocComment());
    return new Method($method, $annotations, $parent);
  }

  private function getAnnotations($uniqueName, $docString) {
    if (!isset($this->annotations[$uniqueName])) {
      $this->annotations[$uniqueName] = $this->parseAnnotations($docString);
    }

    return $this->annotations[$uniqueName];
  }

  public function parseAnnotations($docString) {
    $annotations = $this->annotationParser->parseAnnotations($docString);
    $annotationObjects = array();

    foreach ($annotations as $qualifiedClassName => $arguments) {
      $annotation = $this->getAnnotation($qualifiedClassName, $arguments);
      $annotationObjects[] = $annotation;
    }

    return $annotationObjects;
  }

  public function getAnnotation($qualifiedClassName, array $arguments) {
    $reflection = $this->getReflectionClass($qualifiedClassName);

    if (!$reflection->implementsInterface('\Sharbat\Inject\Annotation')) {
      throw new RuntimeException($qualifiedClassName . ' is not an annotation');
    }

    $constructor = $reflection->getConstructor();
    /* @var \ReflectionMethod $constructor */

    $numberOfArguments = count($arguments);
    $numberOfStringKeys = count(array_filter(array_keys($arguments), 'is_string'));

    if ($numberOfStringKeys === 0) {
      if ($numberOfArguments < $constructor->getNumberOfRequiredParameters()) {
        throw new RuntimeException('Not enough arguments to create annotation ' .
            $qualifiedClassName);
      }

      return $reflection->newInstanceArgs($arguments);
    } else if ($numberOfStringKeys === $numberOfArguments) {
      return $this->objectUtils->createValueObject($qualifiedClassName,
        $arguments);
    }

    throw new RuntimeException(
      'Cannot determine annotation building method: constructor or setter');
  }

  public function serialize() {
    return serialize(array($this->annotationParser, $this->objectUtils,
      $this->annotations));
  }

  public function unserialize($data) {
    list($this->annotationParser, $this->objectUtils, $this->annotations) =
        unserialize($data);
  }

}
