<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;

class UntargettedBinding extends AbstractScopedBinding {
  public function __construct(Clazz $source, Clazz $scope = null) {
    $this->source = $source;
    $this->scope = $scope;
  }

  public function accept(BindingVisitor $bindingVisitor) {
    return $bindingVisitor->visitUntargettedBinding($this);
  }
}
