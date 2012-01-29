<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;

class DefaultMembersInjector implements MembersInjector {

  private $reflectionService;
  private $injector;
  private $methodInvoker;

  public function __construct(ReflectionService $reflectionService,
      Injector $injector, MethodInvoker $methodInvoker) {
    $this->reflectionService = $reflectionService;
    $this->injector = $injector;
    $this->methodInvoker = $methodInvoker;
  }

  public function injectMembers($instance) {
    $this->injectFields($instance);
    $this->injectMethods($instance);
  }

  public function injectFields($instance) {
    $class = $this->reflectionService->getClass(get_class($instance));

    foreach ($class->getFields() as $field) {
      $injectAnnotation = $field->getAnnotation('\Sharbat\Inject\Inject');
      /* @var \Sharbat\Inject\Inject $injectAnnotation */

      if ($injectAnnotation != null && !$field->isStatic()) {
        $field->setValue($instance, $this->injector->getInstance(
          $injectAnnotation->getDependencyName()));
      }
    }
  }

  public function injectMethods($instance) {
    $class = $this->reflectionService->getClass(get_class($instance));

    foreach ($class->getMethods() as $method) {
      $injectAnnotation = $method->getAnnotation('\Sharbat\Inject\Inject');
      /* @var \Sharbat\Inject\Inject $injectAnnotation */

      if ($injectAnnotation != null && !$method->isStatic()) {
        $this->methodInvoker->invokeMethod($instance,
          $method->getUnqualifiedName());
      }
    }
  }

}
