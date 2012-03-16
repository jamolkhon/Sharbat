<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Clazz;

class InjectorProvider {
  private $dependenciesProvider;

  public function __construct(DependenciesProvider $dependenciesProvider) {
    $this->dependenciesProvider = $dependenciesProvider;
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\ConstructorInjector
   */
  public function getConstructorInjector(Clazz $class) {
    $dependencies = $this->dependenciesProvider->getConstructorDependencies(
      $class->getQualifiedName());
    return new ConstructorInjector($class, $dependencies);
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\FieldInjector[]
   */
  public function getFieldInjectors(Clazz $class) {
    $fieldInjectors = array();
    $injectableFields = $class->getFieldsWithAnnotation(Annotations::INJECT);

    foreach ($injectableFields as $field) {
      if (!$field->isStatic()) {
        $dependency = $this->dependenciesProvider->getDependencyOfField($field);
        $fieldInjectors[] = new FieldInjector($field, $dependency);
      }
    }

    return $fieldInjectors;
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\MethodInjector[]
   */
  public function getMethodInjectors(Clazz $class) {
    $methodInjectors = array();
    $injectableMethods = $class->getMethodsWithAnnotation(Annotations::INJECT);

    foreach ($injectableMethods as $method) {
      if (!$method->isStatic()) {
        $dependencies = $this->dependenciesProvider->getDependenciesOfMethod(
          $method);
        $methodInjectors[] = new MethodInjector($method, $dependencies);
      }
    }

    return $methodInjectors;
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\MemberInjector[]
   */
  public function getMemberInjectors(Clazz $class) {
    $fieldInjectors = $this->getFieldInjectors($class);
    $methodInjectors = $this->getMethodInjectors($class);
    return array_merge($fieldInjectors, $methodInjectors);
  }
}
