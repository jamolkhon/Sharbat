<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;
use \RuntimeException;
use Sharbat\Reflect\Clazz;

class DefaultInjector implements Injector, MembersInjector {

  private $bindingDao;
  private $bindingInstantiator;
  private $reflectionService;
  private $dependenciesProvider;

  private $dependencies = array();
  private $incompleteInstances = array();

  public function __construct(BindingDao $bindingDao,
      BindingInstantiator $bindingInstantiator,
      ReflectionService $reflectionService,
      DependenciesProvider $dependenciesProvider) {
    $this->bindingDao = $bindingDao;
    $this->bindingInstantiator = $bindingInstantiator;
    $this->reflectionService = $reflectionService;
    $this->dependenciesProvider = $dependenciesProvider;
  }

  public function getInstance($qualifiedClassName) {
    $binding = $this->bindingDao->getOrCreateBinding($qualifiedClassName);
    return $this->bindingInstantiator->getInstance($binding);
  }

  public function getConstant($constant) {
    $constantBinding = $this->bindingDao->getConstantBinding($constant);

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
      $injector->inject($instance);
    }

    foreach ($this->incompleteInstances[$qualifiedClassName] as $incompleteInstance) {
      $constructorInjector->inject($incompleteInstance);

      foreach ($memberInjectors as $injector) {
        $injector->inject($incompleteInstance);
      }
    }

    unset($this->dependencies[$qualifiedClassName]);
    unset($this->incompleteInstances[$qualifiedClassName]);
    return $instance;
  }

  public function injectMembers($instance) {
    $class = $this->reflectionService->getClass(get_class($instance));

    foreach ($this->getMemberInjectors($class) as $injector) {
      $injector->inject($instance);
    }
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\ConstructorInjector
   */
  private function getConstructorInjector(Clazz $class) {
    $constructor = $class->getConstructor();

    if ($constructor == null) {
      return new ConstructorInjector($class, array());
    }

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
    $injectAnnotationClassName = '\Sharbat\Inject';
    $injectableFields = $class->getFieldsWithAnnotation($injectAnnotationClassName);

    foreach ($injectableFields as $field) {
      if (!$field->isStatic()) {
        $injectAnnotation = $field->getAnnotation($injectAnnotationClassName);
        /* @var \Sharbat\Inject $injectAnnotation */
        $dependency = $this->getInstance($injectAnnotation->getDependencyName());
        $injectors[] = new FieldInjector($field, $dependency);
      }
    }

    $injectableMethods = $class->getMethodsWithAnnotation($injectAnnotationClassName);

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
