<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Clazz;

class ConstructorInjector {

  private $class;
  private $constructorDependencies = array();

  public function __construct(Clazz $class, array $constructorDependencies) {
    $this->class = $class;
    $this->constructorDependencies = $constructorDependencies;
  }

  public function createNew() {
    if ($this->class->getConstructor() == null) {
      return $this->class->newInstance();
    }

    return $this->class->newInstanceArgs($this->constructorDependencies);
  }

  public function injectTo($instance) {
    $constructor = $this->class->getConstructor();

    if ($constructor != null) {
      $constructor->invokeArgs($instance, $this->constructorDependencies);
    }
  }

}
