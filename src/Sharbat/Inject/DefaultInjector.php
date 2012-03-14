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

  private $dependencies = array();
  private $incompleteInstances = array();

  public function __construct(Bindings $bindings,
      BindingInstantiator $bindingInstantiator,
      ReflectionService $reflectionService,
      DependenciesProvider $dependenciesProvider) {
    $this->bindings = $bindings;
    $this->bindingInstantiator = $bindingInstantiator;
    $this->reflectionService = $reflectionService;
    $this->dependenciesProvider = $dependenciesProvider;
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
    $constructorInjector = $this->getConstructorInjector($class);
    $memberInjectors = $this->getMemberInjectors($class);
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

    foreach ($this->getMemberInjectors($class) as $injector) {
      $injector->injectTo($instance);
    }
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\ConstructorInjector
   */
  private function getConstructorInjector(Clazz $class) {
    $dependencies = $this->dependenciesProvider->getConstructorDependencies(
      $class->getQualifiedName());
    return new ConstructorInjector($class, $dependencies);
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\MemberInjector[]
   */
  private function getMemberInjectors(Clazz $class) {
    $injectors = array();
    $injectableFields = $class->getFieldsWithAnnotation(Annotations::INJECT);

    foreach ($injectableFields as $field) {
      if (!$field->isStatic()) {
        $dependency = $this->dependenciesProvider->getDependencyOfField($field);
        $injectors[] = new FieldInjector($field, $dependency);
      }
    }

    $injectableMethods = $class->getMethodsWithAnnotation(Annotations::INJECT);

    foreach ($injectableMethods as $method) {
      if (!$method->isStatic()) {
        $dependencies = $this->dependenciesProvider->getDependenciesOfMethod(
          $method);
        $injectors[] = new MethodInjector($method, $dependencies);
      }
    }

    return $injectors;
  }

}
