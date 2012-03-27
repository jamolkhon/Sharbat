<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Binding;

class Singleton implements Scope {
  private $bindingInstantiator;
  private $instances = array();

  public function __construct(BindingInstantiator $bindingInstantiator) {
    $this->bindingInstantiator = $bindingInstantiator;
  }

  public function getInstance(Binding $binding) {
    $key = $binding->getKey();

    if (!isset($this->instances[$key])) {
      $this->instances[$key] = $this->bindingInstantiator->getInstance($binding);
    }

    return $this->instances[$key];
  }
}
