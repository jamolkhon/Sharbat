<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\BindingVisitor;
use Sharbat\Inject\Binder\Binding;
use Sharbat\Inject\Binder\LinkedBinding;
use Sharbat\Inject\Binder\ConstantBinding;
use Sharbat\Inject\Binder\UntargettedBinding;
use Sharbat\Inject\Binder\InstanceBinding;
use Sharbat\Inject\Binder\ProviderBinding;
use Sharbat\Inject\Binder\ProviderInstanceBinding;

/**
 * \Sharbat\@Singleton
 */
class BindingInstantiator implements Scope, BindingVisitor {
  private $injector;

  public function __construct(Injector $injector) {
    $this->injector = $injector;
  }

  public function getInstance(Binding $binding) {
    return $binding->accept($this);
  }

  public function visitLinkedBinding(LinkedBinding $binding) {
    return $this->injector->getInstance($binding->getTarget()->getQualifiedName());
  }

  public function visitUntargettedBinding(UntargettedBinding $binding) {
    return $this->injector->createInstance(
      $binding->getSource()->getQualifiedName());
  }

  public function visitInstanceBinding(InstanceBinding $binding) {
    return $binding->getInstance();
  }

  public function visitProviderBinding(ProviderBinding $binding) {
    $providerClass = $binding->getProvider();
    /** @var Provider $provider */
    $provider = $this->injector->getInstance($providerClass->getQualifiedName());
    return $provider->get();
  }

  public function visitProviderInstanceBinding(ProviderInstanceBinding $binding) {
    return $binding->getProviderInstance()->get();
  }

  public function visitConstantBinding(ConstantBinding $binding) {
    return $binding->getValue();
  }
}
