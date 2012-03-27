<?php

namespace Sharbat;

use Sharbat\Reflect\AnnotationParser;
use Sharbat\Reflect\ObjectUtils;
use Sharbat\Reflect\ReflectionService;
use Sharbat\Inject\GenericProvider;
use Sharbat\Inject\DefaultDependenciesProvider;
use Sharbat\Inject\ProvidesProvider;
use Sharbat\Inject\DefaultBinder;
use Sharbat\Intercept\InterceptorProxyBuilder;
use Sharbat\Inject\BindingInstantiator;
use Sharbat\Inject\ScopedBindingInstantiator;
use Sharbat\Inject\InjectorProvider;
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
    $providesProvider = new ProvidesProvider($dependenciesProvider);
    $binder = new DefaultBinder($reflectionService, $injector, $providesProvider);
    $bindingInstantiator = new BindingInstantiator($injector);
    $scopedBindingInstantiator = new ScopedBindingInstantiator($injector,
      $bindingInstantiator);
    $injectorProvider = new InjectorProvider($dependenciesProvider);
    $interceptorProxyBuilder = new InterceptorProxyBuilder($reflectionService,
      $binder);
    $injectorClass->invokeConstructorIfExists($injector, array($binder,
      $scopedBindingInstantiator, $reflectionService, $dependenciesProvider,
      $injectorProvider, $interceptorProxyBuilder));

    $binder->bind('\Sharbat\Inject\DependenciesProvider')->to(
      '\Sharbat\Inject\DefaultDependenciesProvider')->inSingleton();
    $binder->bind('\Sharbat\Inject\Binder')->toInstance($binder);
    $binder->bind('\Sharbat\Inject\Bindings')->toInstance($binder);
    $binder->bind('\Sharbat\Reflect\ReflectionService')->toInstance(
      $reflectionService);
    $binder->bind('\Sharbat\Inject\Injector')->toInstance($injector);
    $binder->bind('\Sharbat\Inject\MembersInjector')->toInstance($injector);
    $binder->bind('\Sharbat\Inject\Singleton')->toInstance(new Singleton(
      $bindingInstantiator));

    foreach (func_get_args() as $module) {
      if ($module instanceof AbstractModule) {
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
