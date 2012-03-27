<?php

namespace Sharbat\Reflect;

use Sharbat\Inject\Annotatable;
use \ReflectionClass;

/**
 * \ReflectionClass wrapper with annotations
 */
class Clazz implements Annotatable {
  /**
   * @var \ReflectionClass
   */
  private $reflection;

  /**
   * @var \Sharbat\Inject\Annotation[]
   */
  private $annotations = array();

  /**
   * @var \Sharbat\Reflect\Clazz
   */
  private $parent;

  /**
   * @var \Sharbat\Reflect\Clazz[]
   */
  private $interfaces = array();

  /**
   * @var \Sharbat\Reflect\Field[]
   */
  private $fields = array();

  /**
   * @var \Sharbat\Reflect\Method[]
   */
  private $methods = array();

  public function __construct(ReflectionClass $reflection, array $annotations,
      Clazz $parent = null, array $interfaces = array()) {
    $this->reflection = $reflection;
    $this->annotations = $annotations;
    $this->parent = $parent;
    $this->interfaces = $interfaces;
  }

  /**
   * @return \ReflectionClass
   */
  public function getInternalReflection() {
    return $this->reflection;
  }

  public static function getCanonicalName($qualifiedName) {
    return ltrim($qualifiedName, '\\');
  }

  public function nameEquals($qualifiedClassName) {
    return $this->reflection->getName() === self::getCanonicalName(
      $qualifiedClassName);
  }

  /**
   * @param string $qualifiedClassName
   * @return bool
   */
  public function hasAnnotation($qualifiedClassName) {
    return $this->getFirstAnnotation($qualifiedClassName) != null;
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Annotation
   */
  public function getFirstAnnotation($qualifiedClassName) {
    foreach ($this->annotations as $annotation) {
      if ($annotation instanceof $qualifiedClassName) {
        return $annotation;
      }
    }

    return null;
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Annotation[]
   */
  public function getAnnotations($qualifiedClassName) {
    $annotations = array();

    foreach ($this->annotations as $annotation) {
      if ($annotation instanceof $qualifiedClassName) {
        $annotations[] = $annotation;
      }
    }

    return $annotations;
  }

  /**
   * @return \Sharbat\Inject\Annotation[]
   */
  public function getAllAnnotations() {
    return $this->annotations;
  }

  public function getConstant($name) {
    return $this->reflection->getConstant($name);
  }

  public function getConstants() {
    return $this->reflection->getConstants();
  }

  /**
   * @return \Sharbat\Reflect\Method
   */
  public function getConstructor() {
    /** @var \ReflectionMethod $constructor */
    $constructor = $this->reflection->getConstructor();
    return $constructor == null ? null :
        $this->methods[$constructor->getShortName()];
  }

  public function getDocComment() {
    return $this->reflection->getDocComment();
  }

  public function getEndLine() {
    return $this->reflection->getEndLine();
  }

  /**
   * @return \ReflectionExtension
   */
  public function getExtension() {
    return $this->reflection->getExtension();
  }

  public function getExtensionName() {
    return $this->reflection->getExtensionName();
  }

  public function getFileName() {
    return $this->reflection->getFileName();
  }

  /**
   * @return string[]
   */
  public function getInterfaceNames() {
    return $this->reflection->getInterfaceNames();
  }

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getInterfaces() {
    return $this->interfaces;
  }

  /**
   * @param string $name
   * @return \Sharbat\Reflect\Method
   */
  public function getMethod($name) {
    return isset($this->methods[$name]) ? $this->methods[$name] : null;
  }

  /**
   * @param int $filter
   * @return \Sharbat\Reflect\Method[]
   */
  public function getMethods($filter = null) {
    if ($filter === null) {
      return array_values($this->methods);
    }

    $methods = array();
    /** @var \ReflectionMethod[] $reflectionMethods */
    $reflectionMethods = $this->reflection->getMethods($filter);

    foreach ($reflectionMethods as $method) {
      $methods[] = $this->methods[$method->getShortName()];
    }

    return $methods;
  }

  /**
   * @param string $qualifiedAnnotationClassName
   * @param int $filter
   * @return \Sharbat\Reflect\Method[]
   */
  public function getMethodsWithAnnotation($qualifiedAnnotationClassName,
      $filter = null) {
    $methods = $filter === null ? $this->methods : $this->getMethods($filter);
    $methodsWithAnnotation = array();

    foreach ($methods as $method) {
      if ($method->getFirstAnnotation($qualifiedAnnotationClassName)) {
        $methodsWithAnnotation[] = $method;
      }
    }

    return $methodsWithAnnotation;
  }

  public function getModifiers() {
    return $this->reflection->getModifiers();
  }

  /**
   * @return string
   */
  public function getQualifiedName() {
    return $this->reflection->getName();
  }

  public function getNamespaceName() {
    return $this->reflection->getNamespaceName();
  }

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getParentClass() {
    return $this->parent;
  }

  /**
   * @param int $filter
   * @return \Sharbat\Reflect\Field[]
   */
  public function getFields($filter = null) {
    if ($filter === null) {
      return array_values($this->fields);
    }

    $fields = array();
    $reflectionProperties = $this->reflection->getProperties($filter);

    foreach ($reflectionProperties as $property) {
      $fields[] = $this->fields[$property->getName()];
    }

    return $fields;
  }

  /**
   * @param string $qualifiedAnnotationClassName
   * @param int $filter
   * @return \Sharbat\Reflect\Field[]
   */
  public function getFieldsWithAnnotation($qualifiedAnnotationClassName,
      $filter = null) {
    $fields = $filter === null ? $this->fields : $this->getFields($filter);
    $fieldsWithAnnotation = array();

    foreach ($fields as $field) {
      if ($field->getFirstAnnotation($qualifiedAnnotationClassName)) {
        $fieldsWithAnnotation[] = $field;
      }
    }

    return $fieldsWithAnnotation;
  }

  /**
   * @param string $name
   * @return \Sharbat\Reflect\Field
   */
  public function getField($name) {
    return isset($this->fields[$name]) ? $this->fields[$name] : null;
  }

  /**
   * @return string
   */
  public function getUnqualifiedName() {
    return $this->reflection->getShortName();
  }

  public function getStartLine() {
    return $this->reflection->getStartLine();
  }

  public function getStaticFieldValue($name, $default = null) {
    return $this->reflection->getStaticPropertyValue($name, $default);
  }

  public function hasConstant($name) {
    return $this->reflection->hasConstant($name);
  }

  public function hasMethod($name) {
    return $this->reflection->hasMethod($name);
  }

  public function hasField($name) {
    return $this->reflection->hasProperty($name);
  }

  public function implementsInterface($interface) {
    return $this->reflection->implementsInterface($interface);
  }

  public function isNamespaced() {
    return $this->reflection->inNamespace();
  }

  public function inNamespace($qualifiedNamespaceName) {
    return self::getCanonicalName($qualifiedNamespaceName) . '\\' .
        $this->reflection->getShortName() === $this->reflection->getName();
  }

  public function inSubNamespace($qualifiedNamespaceName) {
    return $this->inNamespaceOrSub($qualifiedNamespaceName) &&
        strlen(self::getCanonicalName($qualifiedNamespaceName)) < strlen(
          $this->reflection->getNamespaceName());
  }

  public function inNamespaceOrSub($qualifiedNamespaceName) {
    $canonicalNamespaceName = self::getCanonicalName($qualifiedNamespaceName);
    return strpos($this->reflection->getNamespaceName(),
      $canonicalNamespaceName) === 0;
  }

  public function invokeConstructorIfExists($instance, array $arguments) {
    /** @var \ReflectionMethod $constructor */
    $constructor = $this->reflection->getConstructor();

    if ($constructor != null) {
      $constructor->invokeArgs($instance, $arguments);
    }
  }

  public function isAbstract() {
    return $this->reflection->isAbstract();
  }

  public function isFinal() {
    return $this->reflection->isFinal();
  }

  public function isInstance($instance) {
    return $this->reflection->isInstance($instance);
  }

  public function isInstantiable() {
    return $this->reflection->isInstantiable();
  }

  public function isInterface() {
    return $this->reflection->isInterface();
  }

  public function isInternal() {
    return $this->reflection->isInternal();
  }

  public function isIterateable() {
    return $this->reflection->isIterateable();
  }

  public function isSubclassOf($class) {
    return $this->reflection->isSubclassOf($class);
  }

  public function isUserDefined() {
    return $this->reflection->isUserDefined();
  }

  public function newInstance() {
    return $this->newInstanceArgs(func_get_args());
  }

  public function newInstanceArgs(array $arguments) {
    if (empty($arguments) || $this->reflection->getConstructor() == null) {
      return $this->reflection->newInstance();
    }

    return $this->reflection->newInstanceArgs($arguments);
  }

  public function newInstanceWithoutConstructor() {
    $qualifiedClassName = $this->reflection->getName();
    return unserialize(sprintf('O:%d:"%s":0:{}', strlen($qualifiedClassName),
      $qualifiedClassName));
  }

  public function setStaticPropertyValue($name, $value) {
    $this->reflection->setStaticPropertyValue($name, $value);
  }
}
