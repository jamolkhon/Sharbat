<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;
use \RuntimeException;
use Sharbat\Reflect\Clazz;

class DefaultInjector implements Injector, MembersInjector {
  private $bindings;
  private $bindingInstantiator;
  private $reflectionService;
  private $dependenciesProvider;
  private $injectorProvider;

  private $dependencies = array();
  private $incompleteInstances = array();

  public function __construct(Bindings $bindings,
      BindingInstantiator $bindingInstantiator,
      ReflectionService $reflectionService,
      DependenciesProvider $dependenciesProvider,
      InjectorProvider $injectorProvider) {
    $this->bindings = $bindings;
    $this->bindingInstantiator = $bindingInstantiator;
    $this->reflectionService = $reflectionService;
    $this->dependenciesProvider = $dependenciesProvider;
    $this->injectorProvider = $injectorProvider;
  }

  public function getInstance($qualifiedClassName) {
    $binding = $this->bindings->getOrCreateBinding($qualifiedClassName);
    return $this->bindingInstantiator->getInstance($binding);
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
    return $instance;
  }

  public function injectTo($instance) {
    $class = $this->reflectionService->getClass(get_class($instance));
    $memberInjectors = $this->injectorProvider->getMemberInjectors($class);

    foreach ($memberInjectors as $injector) {
      $injector->injectTo($instance);
    }
  }
}
