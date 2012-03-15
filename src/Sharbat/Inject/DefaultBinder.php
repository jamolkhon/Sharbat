<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;
use Sharbat\Inject\Binder\LinkedBindingBuilder;
use Sharbat\Inject\Binder\Binding;
use Sharbat\Inject\Binder\SourceAlreadyBoundException;
use Sharbat\Inject\Binder\ConstantBinding;
use Sharbat\Reflect\Method;

class DefaultBinder implements Binder, Bindings {
  private $reflectionService;
  private $linkedBindingBuilder;
  private $membersInjector;
  private $providesProvider;

  /**
   * @var \Sharbat\Inject\AbstractModule[]
   */
  private $modules = array();

  /**
   * @var \Sharbat\Inject\Binder\Binding[]
   */
  private $bindings = array();

  public function __construct(ReflectionService $reflectionService,
      MembersInjector $membersInjector, ProvidesProvider $providesProvider) {
    $this->reflectionService = $reflectionService;
    $this->linkedBindingBuilder = new LinkedBindingBuilder($reflectionService,
      $this);
    $this->membersInjector = $membersInjector;
    $this->providesProvider = $providesProvider;
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Binder\LinkedBindingBuilder
   */
  public function bind($qualifiedClassName) {
    return $this->linkedBindingBuilder->bind($qualifiedClassName);
  }

  /**
   * @param string $constant
   * @return \Sharbat\Inject\Binder\ConstantBinding
   */
  public function bindConstant($constant) {
    $binding = new ConstantBinding($constant);
    $this->addBinding($binding);
    return $binding;
  }

  public function install(AbstractModule $module) {
    $module->setBinder($this);
    $module->configure();
    $this->modules[] = $module;
  }

  public function build() {
    foreach ($this->modules as $module) {
      $moduleClass = $this->reflectionService->getClass(get_class($module));
      $providesMethods = $moduleClass->getMethodsWithAnnotation(
        Annotations::PROVIDES);

      foreach ($providesMethods as $method) {
        $this->bindAsProvider($method, $module);
      }
    }

    return $this;
  }

  private function bindAsProvider(Method $method, AbstractModule $module) {
    $providesProvider = $this->providesProvider->createProviderFor($module,
      $method);
    /** @var \Sharbat\Provides $providesAnnotation */
    $providesAnnotation = $method->getFirstAnnotation(Annotations::PROVIDES);
    $scopedBindingBuilder = $this->bind($providesAnnotation->getDependencyName())
        ->toProviderInstance($providesProvider);
    /** @var \Sharbat\Scope $scopeAnnotation */
    $scopeAnnotation = $method->getFirstAnnotation(Annotations::SCOPE);

    if ($scopeAnnotation != null) {
      $scopedBindingBuilder->in($scopeAnnotation->getScopeClassName());
    }
  }

  public function requestInjection($instance) {
    $this->membersInjector->injectTo($instance);
  }

  public function addBinding(Binding $binding) {
    $key = $binding->getKey();

    if (isset($this->bindings[$key])) {
      throw new SourceAlreadyBoundException($key . ' is already bound');
    }

    $this->bindings[$key] = $binding;
  }

  /**
   * @param string $key
   * @return \Sharbat\Inject\Binder\Binding
   */
  public function getBinding($key) {
    $key = ltrim($key, '\\');
    return isset($this->bindings[$key]) ? $this->bindings[$key] : null;
  }

  /**
   * @param string $constant
   * @return \Sharbat\Inject\Binder\ConstantBinding
   */
  public function getConstantBinding($constant) {
    return $this->getBinding(ConstantBinding::generateKey($constant));
  }

  /**
   * @param string $key
   * @return \Sharbat\Inject\Binder\Binding
   */
  public function getOrCreateBinding($key) {
    $key = ltrim($key, '\\');

    if (!isset($this->bindings[$key])) {
      // Just-in-time binding
      $this->bind($key);
    }

    return $this->bindings[$key];
  }

  /**
   * @return \Sharbat\Inject\Binder\Binding[]
   */
  public function getBindings() {
    return $this->bindings;
  }

  public function removeBinding($key) {
    unset($this->bindings[$key]);
  }
}
