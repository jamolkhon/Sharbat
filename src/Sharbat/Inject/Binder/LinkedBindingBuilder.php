<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\ReflectionService;
use Sharbat\Inject\Bindings;
use Sharbat\Reflect\Clazz;
use Sharbat\Scope;
use Sharbat\Inject\Provider;

class LinkedBindingBuilder implements ScopedBindingBuilder {

  private $reflectionService;
  private $bindings;

  /** @var \Sharbat\Inject\Binder\AbstractScopedBinding */
  private $binding;

  public function __construct(ReflectionService $reflectionService,
      Bindings $bindings) {
    $this->reflectionService = $reflectionService;
    $this->bindings = $bindings;
  }

  public function bind($qualifiedClassName) {
    $class = $this->reflectionService->getClass($qualifiedClassName);
    $this->binding = new LinkedBinding($class);
    $this->bindings->addBinding($this->binding);

    $scopeAnnotation = $class->getFirstAnnotation('\Sharbat\Scope');
    /* @var \Sharbat\Scope $scopeAnnotation */

    if ($scopeAnnotation != null) {
      $scopeClass = $this->reflectionService->getClass(
        $scopeAnnotation->getScopeClassName());
      $this->binding->setScope($scopeClass);
    }

    return $this;
  }

  private function addBinding(Binding $binding) {
    $this->bindings->removeBinding($binding->getKey());
    $this->bindings->addBinding($binding);
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Binder\ScopedBindingBuilder
   */
  public function to($qualifiedClassName) {
    $targetClass = $this->reflectionService->getClass($qualifiedClassName);
    $this->binding = new LinkedBinding($this->binding->getSource(), $targetClass,
      $this->binding->getScope());
    $this->addBinding($this->binding);
    return $this;
  }

  /**
   * @param object $instance
   * @return \Sharbat\Inject\Binder\ScopedBindingBuilder
   */
  public function toInstance($instance) {
    $this->binding = new InstanceBinding($this->binding->getSource(), $instance,
      $this->binding->getScope());
    $this->addBinding($this->binding);
    return $this;
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Binder\ScopedBindingBuilder
   */
  public function toProvider($qualifiedClassName) {
    $providerClass = $this->reflectionService->getClass($qualifiedClassName);
    $this->binding = new ProviderBinding($this->binding->getSource(),
      $providerClass, $this->binding->getScope());
    $this->addBinding($this->binding);
    return $this;
  }

  /**
   * @param \Sharbat\Inject\Provider $provider
   * @return \Sharbat\Inject\Binder\ScopedBindingBuilder
   */
  public function toProviderInstance(Provider $provider) {
    $this->binding = new ProviderInstanceBinding($this->binding->getSource(),
      $provider, $this->binding->getScope());
    $this->addBinding($this->binding);
    return $this;
  }

  public function in($qualifiedClassName) {
    $scope = $this->reflectionService->getClass($qualifiedClassName);
    $this->binding->setScope($scope);
  }

  public function inSingleton() {
    $this->in('\Sharbat\Inject\Singleton');
  }

  public function inNoScope() {
    $this->binding->unsetScope();
  }

}
