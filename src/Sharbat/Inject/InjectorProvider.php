<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Clazz;
use Sharbat\Reflect\Field;
use Sharbat\Reflect\Method;

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
    $nonStaticFieldsFilter = Field::ALL & ~Field::IS_STATIC;
    $injectableFields = $class->getFieldsWithAnnotation(Annotations::INJECT,
      $nonStaticFieldsFilter);

    foreach ($injectableFields as $field) {
      $dependency = $this->dependenciesProvider->getDependencyOfField($field);
      $fieldInjectors[] = new FieldInjector($field, $dependency);
    }

    return $fieldInjectors;
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\MethodInjector[]
   */
  public function getMethodInjectors(Clazz $class) {
    $methodInjectors = array();
    $nonStaticMethodsFilter = Method::ALL & ~Method::IS_STATIC;
    $injectableMethods = $class->getMethodsWithAnnotation(Annotations::INJECT,
      $nonStaticMethodsFilter);

    foreach ($injectableMethods as $method) {
      $dependencies = $this->dependenciesProvider->getDependenciesOfMethod(
        $method);
      $methodInjectors[] = new MethodInjector($method, $dependencies);
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
