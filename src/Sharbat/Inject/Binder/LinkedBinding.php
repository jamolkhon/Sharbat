<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;

class LinkedBinding extends AbstractScopedBinding {
  private $target;

  public function __construct(Clazz $source, Clazz $target = null, Clazz $scope = null) {
    $this->source = $source;
    $this->target = $target;
    $this->scope = $scope;
  }

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getTarget() {
    return $this->target;
  }

  public function accept(BindingVisitor $bindingVisitor) {
    return $bindingVisitor->visitLinkedBinding($this);
  }
}
