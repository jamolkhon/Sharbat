<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Binding;
use Sharbat\Inject\Binder\ScopedBinding;

/**
 * \Sharbat\@Singleton
 */
class ScopedBindingInstantiator {
  private $injector;
  private $bindingInstantiator;

  public function __construct(Injector $injector,
      BindingInstantiator $bindingInstantiator) {
    $this->injector = $injector;
    $this->bindingInstantiator = $bindingInstantiator;
  }

  public function getInstance(Binding $binding) {
    if (!($binding instanceof ScopedBinding) || $binding->getScope() === null) {
      /** @var ScopedBinding $binding */
      return $this->bindingInstantiator->getInstance($binding);
    }

    /** @var Scope $scopeInstance */
    $scopeInstance = $this->injector->getInstance(
      $binding->getScope()->getQualifiedName());
    return $scopeInstance->getInstance($binding);
  }
}
