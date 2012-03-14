<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;

class InstanceBinding extends AbstractScopedBinding {
  private $instance;

  public function __construct(Clazz $source, $instance, Clazz $scope = null) {
    $this->source = $source;
    $this->instance = $instance;
    $this->scope = $scope;
  }

  public function getInstance() {
    return $this->instance;
  }

  public function accept(BindingVisitor $bindingVisitor) {
    return $bindingVisitor->visitInstanceBinding($this);
  }
}
