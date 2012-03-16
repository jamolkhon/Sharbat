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
    return $this->class->newInstanceArgs($this->constructorDependencies);
  }

  public function injectTo($instance) {
    $this->class->invokeConstructorIfExists($instance,
      $this->constructorDependencies);
  }
}
