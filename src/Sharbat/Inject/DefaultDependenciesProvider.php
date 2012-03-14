<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;
use Sharbat\Reflect\Method;
use Sharbat\Reflect\Field;
use Sharbat\InjectProvider;
use Sharbat\Reflect\Parameter;
use \RuntimeException;

class DefaultDependenciesProvider implements DependenciesProvider {

  private $reflectionService;
  private $injector;
  private $genericProvider;

  public function __construct(ReflectionService $reflectionService,
      Injector $injector, GenericProvider $genericProvider) {
    $this->reflectionService = $reflectionService;
    $this->injector = $injector;
    $this->genericProvider = $genericProvider;
  }

  public function getDependencies($qualifiedClassName, $method) {
    $class = $this->reflectionService->getClass($qualifiedClassName);
    return $this->getDependenciesOfMethod($class->getMethod($method));
  }

  public function getProviderFor($qualifiedClassName) {
    return $this->genericProvider->createProviderFor($qualifiedClassName);
  }

  public function getConstructorDependencies($qualifiedClassName) {
    $class = $this->reflectionService->getClass($qualifiedClassName);
    $constructor = $class->getConstructor();

    if ($constructor == null) {
      return array();
    }

    return $this->getDependenciesOfMethod($constructor);
  }

  public function getDependencyOfField(Field $field) {
    $injectAnnotation = $field->getFirstAnnotation(Annotations::INJECT);
    /* @var \Sharbat\Inject $injectAnnotation */

    if ($injectAnnotation instanceof InjectProvider) {
      /* @var \Sharbat\InjectProvider $injectAnnotation */
      return $this->getProviderFor($injectAnnotation->getTargetClassName());
    }

    return $this->injector->getInstance($injectAnnotation->getDependencyName());
  }

  public function getDependenciesOfMethod(Method $method) {
    $dependencies = array();

    foreach ($method->getParameters() as $parameter) {
      $dependencies[] = $this->getDependencyOfParameter($parameter);
    }

    return $dependencies;
  }

  public function getDependencyOfParameter(Parameter $parameter) {
    $class = $parameter->getClass();

    if ($class == null) {
      return $this->injector->getConstant($parameter->getName());
    }

    $class = $this->reflectionService->getClass($class->getName());

    if (!$class->nameEquals('\Sharbat\Inject\Provider')) {
      return $this->injector->getInstance($class->getQualifiedName());
    }

    $providerAnnotations = $parameter->getDeclaringMethod()->getAnnotations(
      Annotations::PROVIDER);
    /* @var \Sharbat\Provider[] $providerAnnotations */

    foreach ($providerAnnotations as $annotation) {
      if ($annotation->getParameterName() == $parameter->getName()) {
        return $this->getProviderFor($annotation->getTargetClassName());
      }
    }

    throw new RuntimeException(
      'Cannot satisfy Provider dependency. No target type specified');
  }

}
