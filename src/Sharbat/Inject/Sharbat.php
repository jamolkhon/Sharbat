<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\AnnotationParser;
use Sharbat\Reflect\ObjectUtils;
use Sharbat\Reflect\ReflectionService;
use \RuntimeException;
use Sharbat\Inject\Binder\BindingValidator;

final class Sharbat {

  private function __construct() {
  }

  /**
   * @return \Sharbat\Inject\Injector
   */
  public static function createInjector() {
    // Building injector (object graph)
    $annotationParser = new AnnotationParser();
    $objectUtils = new ObjectUtils();;
    $reflectionService = new ReflectionService($annotationParser, $objectUtils);
    $injectorClass = $reflectionService->getClass('\Sharbat\Inject\DefaultInjector');
    $injector = $injectorClass->newInstanceWithoutConstructor();
    $dependenciesProvider = new DefaultDependenciesProvider($reflectionService,
      $injector);
    $methodInvoker = new DefaultMethodInvoker($reflectionService,
      $dependenciesProvider);
    $membersInjector = new DefaultMembersInjector($reflectionService, $injector,
      $methodInvoker);
    $providesProvider = new ProvidesProvider($methodInvoker);
    $binder = new DefaultBinder($reflectionService, $membersInjector,
      $providesProvider);
    $defaultScope = new DefaultScope($injector);
    $bindingInstantiator = new BindingInstantiator($defaultScope, $injector);
    $circularDependencyManager = new CircularDependencyManager($reflectionService,
      $dependenciesProvider);
    $injectorClass->invokeConstructorIfExists($injector, array($binder,
      $bindingInstantiator, $circularDependencyManager, $membersInjector));

    $binder->bind('\Sharbat\Reflect\ReflectionService')->toInstance(
      $reflectionService);
    $binder->bind('\Sharbat\Inject\Injector')->toInstance($injector);
    $binder->bind('\Sharbat\Inject\Singleton')->toInstance(new Singleton(
      $defaultScope));

    foreach (func_get_args() as $module) {
      if (is_object($module) && $module instanceof AbstractModule) {
        $binder->install($module);
      } else {
        throw new RuntimeException('Received non-module argument');
      }
    }

    $binder->build();
    $bindingValidator = new BindingValidator();
    $bindingValidator->validateAll($binder->getBindings());
    return $injector;
  }

}
