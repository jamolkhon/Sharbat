<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;
use Sharbat\Intercept\InterceptorProxyBuilder;
use \RuntimeException;
use Sharbat\Reflect\Clazz;

class DefaultInjector implements Injector, MembersInjector {
  private $bindings;
  private $scopedBindingInstantiator;
  private $reflectionService;
  private $dependenciesProvider;
  private $injectorProvider;
  private $interceptorProxyBuilder;

  private $dependencies = array();
  private $incompleteInstances = array();

  public function __construct(Bindings $bindings,
      ScopedBindingInstantiator $scopedBindingInstantiator,
      ReflectionService $reflectionService,
      DependenciesProvider $dependenciesProvider,
      InjectorProvider $injectorProvider,
      InterceptorProxyBuilder $interceptorProxyBuilder) {
    $this->bindings = $bindings;
    $this->scopedBindingInstantiator = $scopedBindingInstantiator;
    $this->reflectionService = $reflectionService;
    $this->dependenciesProvider = $dependenciesProvider;
    $this->injectorProvider = $injectorProvider;
    $this->interceptorProxyBuilder = $interceptorProxyBuilder;
  }

  public function getInstance($qualifiedClassName) {
    $class = $this->reflectionService->getClass($qualifiedClassName);
    $binding = $this->bindings->getOrCreateBinding($class);
    return $this->scopedBindingInstantiator->getInstance($binding);
  }

  public function getProviderFor($qualifiedClassName) {
    return $this->dependenciesProvider->getProviderFor($qualifiedClassName);
  }

  public function getConstant($constant) {
    $constantBinding = $this->bindings->getConstantBinding($constant);

    if ($constantBinding == null) {
      throw new RuntimeException('No binding found for constant: ' . $constant);
    }

    return $constantBinding->getValue();
  }

  public function createInstance($qualifiedClassName) {
    $class = $this->reflectionService->getClass($qualifiedClassName);
    $this->incompleteInstances += array($qualifiedClassName => array());

    if (isset($this->dependencies[$qualifiedClassName])) {
      $incompleteInstance = $class->newInstanceWithoutConstructor();
      $incompleteInstance = $this->interceptorProxyBuilder->createProxyOrReturn(
        $incompleteInstance);
      $this->incompleteInstances[$qualifiedClassName][] = $incompleteInstance;
      return $incompleteInstance;
    }

    $this->dependencies[$qualifiedClassName] = true;
    $constructorInjector = $this->injectorProvider->getConstructorInjector(
      $class);
    $memberInjectors = $this->injectorProvider->getMemberInjectors($class);
    $instance = $constructorInjector->createNew();

    foreach ($memberInjectors as $injector) {
      $injector->injectTo($instance);
    }

    foreach ($this->incompleteInstances[$qualifiedClassName] as $incompleteInstance) {
      $constructorInjector->injectTo($incompleteInstance);

      foreach ($memberInjectors as $injector) {
        $injector->injectTo($incompleteInstance);
      }
    }

    unset($this->dependencies[$qualifiedClassName]);
    unset($this->incompleteInstances[$qualifiedClassName]);
    return $this->interceptorProxyBuilder->createProxyOrReturn($instance);
  }

  public function injectTo($instance) {
    $class = $this->reflectionService->getClass(get_class($instance));
    $memberInjectors = $this->injectorProvider->getMemberInjectors($class);

    foreach ($memberInjectors as $injector) {
      $injector->injectTo($instance);
    }
  }
}
