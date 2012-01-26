<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;
use Sharbat\Reflect\Method;
use Sharbat\Reflect\Parameter;

class DefaultDependenciesProvider implements DependenciesProvider {

  private $reflectionService;
  private $injector;

  public function __construct(ReflectionService $reflectionService,
      Injector $injector) {
    $this->reflectionService = $reflectionService;
    $this->injector = $injector;
  }

  public function getDependencies($qualifiedClassName, $method) {
    $class = $this->reflectionService->getClass($qualifiedClassName);
    return $this->getDependenciesOfMethod($class->getMethod($method));
  }

  public function getConstructorDependencies($qualifiedClassName) {
    $class = $this->reflectionService->getClass($qualifiedClassName);
    $constructor = $class->getConstructor();

    if ($constructor == null) {
      return array();
    }

    return $this->getDependenciesOfMethod($constructor);
  }

  public function getDependenciesOfMethod(Method $method) {
    $parameters = $method->getParameters();
    $dependencies = array();

    foreach ($parameters as $parameter) {
      $dependencies[] = $this->getDependency($parameter);
    }

    return $dependencies;
  }

  private function getDependency(Parameter $parameter) {
    $class = $parameter->getClass();

    if ($class == null) {
      return $this->injector->getConstant($parameter->getName());
    }

    return $this->injector->getInstance($class->getName());
  }

}
