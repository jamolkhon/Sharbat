<?php

namespace Sharbat\Reflect;

use Sharbat\Inject\Annotatable;
use \ReflectionProperty;

class Field implements Annotatable {
  const ALL = 1793;
  const IS_STATIC = 1;
  const IS_PUBLIC = 256;
  const IS_PROTECTED = 512;
  const IS_PRIVATE = 1024;
  /**
   * @var \ReflectionProperty
   */
  private $reflection;

  /**
   * @var \Sharbat\Inject\Annotation[]
   */
  private $annotations = array();

  /**
   * @var \Sharbat\Reflect\Clazz
   */
  private $declaringClass;

  public function __construct(ReflectionProperty $reflection, array $annotations,
      Clazz $declaringClass) {
    $this->reflection = $reflection;
    $this->annotations = $annotations;
    $this->declaringClass = $declaringClass;
  }

  /**
   * @return \ReflectionProperty
   */
  public function getInternalReflection() {
    return $this->reflection;
  }

  public function getDefinition() {
    $declaration = '';

    if ($this->reflection->isPublic()) {
      $declaration .= 'public';
    } else if ($this->reflection->isProtected()) {
      $declaration .= 'protected';
    } else if ($this->reflection->isPrivate()) {
      $declaration .= 'private';
    }

    if ($this->reflection->isStatic()) {
      $declaration .= ' static';
    }

    $declaration .= ' $' . $this->reflection->getName();
    return $declaration;
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

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getDeclaringClass() {
    return $this->declaringClass;
  }

  public function getDocComment() {
    return $this->reflection->getDocComment();
  }

  public function getModifiers() {
    return $this->reflection->getModifiers();
  }

  public function getName() {
    return $this->reflection->getName();
  }

  public function getValue($instance) {
    if (!$this->reflection->isPublic()) {
      $this->reflection->setAccessible(true);
    }

    return $this->reflection->getValue($instance);
  }

  public function isPrivate() {
    return $this->reflection->isPrivate();
  }

  public function isProtected() {
    return $this->reflection->isProtected();
  }

  public function isPublic() {
    return $this->reflection->isPublic();
  }

  public function isStatic() {
    return $this->reflection->isStatic();
  }

  public function setValue($instance, $value) {
    if (!$this->reflection->isPublic()) {
      $this->reflection->setAccessible(true);
    }

    $this->reflection->setValue($instance, $value);
  }
}
