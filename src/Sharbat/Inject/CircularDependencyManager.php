<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;

class CircularDependencyManager implements InstanceCreator {

  private $reflectionService;
  private $dependenciesProvider;

  private $dependencies = array();
  private $mockInstances = array();

  public function __construct(ReflectionService $reflectionService,
      DependenciesProvider $dependenciesProvider) {
    $this->reflectionService = $reflectionService;
    $this->dependenciesProvider = $dependenciesProvider;
  }

  private function circularDependencyDetected($qualifiedClassName) {
    return isset($this->dependencies[$qualifiedClassName]);
  }

  private function getMockInstance($qualifiedClassName) {
    if (is_object($this->dependencies[$qualifiedClassName])) {
      $instance = $this->dependencies[$qualifiedClassName];
      unset($this->dependencies[$qualifiedClassName]);
      return $instance;
    }

    if (isset($this->mockInstances[$qualifiedClassName])) {
      return $this->mockInstances[$qualifiedClassName];
    }

    $class = $this->reflectionService->getClass($qualifiedClassName);
    return $this->mockInstances[$qualifiedClassName] =
        $class->newInstanceWithoutConstructor();
  }

  private function getRealInstance($qualifiedClassName) {
    $this->dependencies[$qualifiedClassName] = true;
    $dependencies = $this->dependenciesProvider->getConstructorDependencies(
      $qualifiedClassName);
    $class = $this->reflectionService->getClass($qualifiedClassName);

    if (isset($this->mockInstances[$qualifiedClassName])) {
      $mockInstance = $this->mockInstances[$qualifiedClassName];
      $class->invokeConstructorIfExists($mockInstance, $dependencies);
      unset($this->dependencies[$qualifiedClassName]);
      unset($this->mockInstances[$qualifiedClassName]);
      return $mockInstance;
    }

    if ($class->getConstructor() == null) {
      return $this->dependencies[$qualifiedClassName] = $class->newInstance();
    }

    return $this->dependencies[$qualifiedClassName] = $class->newInstanceArgs(
      $dependencies);
  }

  public function createInstance($qualifiedClassName) {
    if ($this->circularDependencyDetected($qualifiedClassName)) {
      return $this->getMockInstance($qualifiedClassName);
    }

    return $this->getRealInstance($qualifiedClassName);
  }

}
