<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Binding;
use Sharbat\Inject\Binder\ScopedBinding;

/**
 * \Sharbat\@Singleton
 */
class BindingInstantiator {

  private $defaultScope;
  private $injector;

  public function __construct(DefaultScope $defaultScope, Injector $injector) {
    $this->defaultScope = $defaultScope;
    $this->injector = $injector;
  }

  public function getInstance(Binding $binding) {
    if ($binding instanceof ScopedBinding) {
      /* @var \Sharbat\Inject\Binder\ScopedBinding $binding */
      return $this->getScopeInstance($binding)->getInstance($binding);
    }

    return $this->defaultScope->getInstance($binding);
  }

  /**
   * @param \Sharbat\Inject\Binder\ScopedBinding $binding
   * @return \Sharbat\Inject\Scope
   */
  private function getScopeInstance(ScopedBinding $binding) {
    if ($binding->getScope() != null) {
      $scopeClass = $binding->getScope();
      return $this->injector->getInstance($scopeClass->getQualifiedName());
    }

    return $this->defaultScope;
  }

}
