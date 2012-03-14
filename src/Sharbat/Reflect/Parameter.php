<?php

namespace Sharbat\Reflect;

use \ReflectionParameter;

class Parameter {
  /**
   * @var \ReflectionParameter
   */
  private $reflection;

  /**
   * @var \Sharbat\Reflect\Method
   */
  private $declaringMethod;

  public function __construct(ReflectionParameter $reflection,
      Method $declaringMethod) {
    $this->reflection = $reflection;
    $this->declaringMethod = $declaringMethod;
  }

  /**
   * @return \ReflectionParameter
   */
  public function getInternalReflection() {
    return $this->reflection;
  }

  public function getDeclaration() {
    $declaration = '';

    if ($this->reflection->getClass() != null) {
      $declaration .= $this->reflection->getName();
    } else if ($this->reflection->isArray()) {
      $declaration .= 'array';
    }

    if ($this->reflection->isPassedByReference()) {
      $declaration .= ' &$' . $this->reflection->getName();
    } else {
      $declaration .= ' $' . $this->reflection->getName();
    }

    if ($this->reflection->isDefaultValueAvailable()) {
      $declaration .= ' = ' . var_export($this->reflection->getDefaultValue(),
        true);
    }

    return trim($declaration);
  }

  /**
   * @return \ReflectionClass
   */
  public function getClass() {
    return $this->reflection->getClass();
  }

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getDeclaringClass() {
    return $this->declaringMethod->getDeclaringClass();
  }

  /**
   * @return \Sharbat\Reflect\Method
   */
  public function getDeclaringMethod() {
    return $this->declaringMethod;
  }

  public function getDefaultValue() {
    return $this->reflection->getDefaultValue();
  }

  public function getName() {
    return $this->reflection->getName();
  }

  public function getPosition() {
    return $this->reflection->getPosition();
  }

  public function isArray() {
    return $this->reflection->isArray();
  }

  public function isDefaultValueAvailable() {
    return $this->reflection->isDefaultValueAvailable();
  }

  public function isOptional() {
    return $this->reflection->isOptional();
  }

  public function isPassedByReference() {
    return $this->reflection->isPassedByReference();
  }
}
