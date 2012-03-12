<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\BindingVisitor;
use Sharbat\Inject\Binder\Binding;
use Sharbat\Inject\Binder\LinkedBinding;
use Sharbat\Inject\Binder\InstanceBinding;
use Sharbat\Inject\Binder\ProviderBinding;
use Sharbat\Inject\Binder\ProviderInstanceBinding;
use Sharbat\Inject\Binder\ConstantBinding;

/**
 * \Sharbat\@Singleton
 */
class DefaultScope implements Scope, BindingVisitor {

  private $injector;

  public function __construct(Injector $injector) {
    $this->injector = $injector;
  }

  public function getInstance(Binding $binding) {
    return $binding->accept($this);
  }

  public function visitLinkedBinding(LinkedBinding $binding) {
    if ($binding->getTarget() != null) {
      return $this->injector->getInstance(
        $binding->getTarget()->getQualifiedName());
    }

    return $this->injector->createInstance(
      $binding->getSource()->getQualifiedName());
  }

  public function visitInstanceBinding(InstanceBinding $binding) {
    return $binding->getInstance();
  }

  public function visitProviderBinding(ProviderBinding $binding) {
    $providerClass = $binding->getProvider();
    $provider = $this->injector->getInstance($providerClass->getQualifiedName());
    /* @var \Sharbat\Inject\Provider $provider */
    return $provider->get();
  }

  public function visitProviderInstanceBinding(ProviderInstanceBinding $binding) {
    return $binding->getProviderInstance()->get();
  }

  public function visitConstantBinding(ConstantBinding $binding) {
    return $binding->getValue();
  }

}
