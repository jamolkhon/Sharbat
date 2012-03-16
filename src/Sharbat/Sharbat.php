<?php

namespace Sharbat;

use Sharbat\Reflect\AnnotationParser;
use Sharbat\Reflect\ObjectUtils;
use Sharbat\Reflect\ReflectionService;
use Sharbat\Inject\GenericProvider;
use Sharbat\Inject\DefaultDependenciesProvider;
use Sharbat\Inject\InjectorProvider;
use Sharbat\Inject\ProvidesProvider;
use Sharbat\Inject\DefaultBinder;
use Sharbat\Inject\DefaultScope;
use Sharbat\Inject\BindingInstantiator;
use Sharbat\Inject\Singleton;
use Sharbat\Inject\AbstractModule;
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
    $objectUtils = new ObjectUtils();
    $reflectionService = new ReflectionService($annotationParser, $objectUtils);
    $injectorClass = $reflectionService->getClass('\Sharbat\Inject\DefaultInjector');
    $injector = $injectorClass->newInstanceWithoutConstructor();
    $genericProvider = new GenericProvider($injector);
    $dependenciesProvider = new DefaultDependenciesProvider($reflectionService,
      $injector, $genericProvider);
    $injectorProvider = new InjectorProvider($dependenciesProvider);
    $providesProvider = new ProvidesProvider($dependenciesProvider);
    $binder = new DefaultBinder($reflectionService, $injector, $providesProvider);
    $defaultScope = new DefaultScope($injector);
    $bindingInstantiator = new BindingInstantiator($defaultScope, $injector);
    $injectorClass->invokeConstructorIfExists($injector, array($binder,
      $bindingInstantiator, $reflectionService, $dependenciesProvider,
      $injectorProvider));

    $binder->bind('\Sharbat\Inject\DependenciesProvider')->to(
      '\Sharbat\Inject\DefaultDependenciesProvider')->inSingleton();
    $binder->bind('\Sharbat\Inject\Binder')->toInstance($binder);
    $binder->bind('\Sharbat\Inject\Bindings')->toInstance($binder);
    $binder->bind('\Sharbat\Reflect\ReflectionService')->toInstance(
      $reflectionService);
    $binder->bind('\Sharbat\Inject\Injector')->toInstance($injector);
    $binder->bind('\Sharbat\Inject\MembersInjector')->toInstance($injector);
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
