<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Binding;

class Singleton implements Scope {
  private $defaultScope;
  private $instances = array();

  public function __construct(DefaultScope $defaultScope) {
    $this->defaultScope = $defaultScope;
  }

  public function getInstance(Binding $binding) {
    $key = $binding->getKey();

    if (!isset($this->instances[$key])) {
      $this->instances[$key] = $this->defaultScope->getInstance($binding);
    }

    return $this->instances[$key];
  }
}
