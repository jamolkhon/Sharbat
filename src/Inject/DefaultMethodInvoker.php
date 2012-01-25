<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;

class DefaultMethodInvoker implements MethodInvoker {

  private $reflectionService;
  private $dependenciesProvider;

  public function __construct(ReflectionService $reflectionService,
      DependenciesProvider $dependenciesProvider) {
    $this->reflectionService = $reflectionService;
    $this->dependenciesProvider = $dependenciesProvider;
  }

  public function invokeMethod($instance, $methodName) {
    $class = $this->reflectionService->getClass(get_class($instance));
    $method = $class->getMethod($methodName);
    $dependencies = $this->dependenciesProvider->getDependenciesOfMethod($method);
    return $method->invokeArgs($instance, $dependencies);
  }

}
