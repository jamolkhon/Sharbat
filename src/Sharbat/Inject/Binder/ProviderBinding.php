<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;

class ProviderBinding extends AbstractScopedBinding {
  private $provider;

  public function __construct(Clazz $source, Clazz $provider, Clazz $scope = null) {
    $this->source = $source;
    $this->provider = $provider;
    $this->scope = $scope;
  }

  public function getProvider() {
    return $this->provider;
  }

  public function accept(BindingVisitor $bindingVisitor) {
    return $bindingVisitor->visitProviderBinding($this);
  }
}
