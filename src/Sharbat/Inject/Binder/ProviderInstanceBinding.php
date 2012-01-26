<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;
use Sharbat\Inject\Provider;

class ProviderInstanceBinding extends AbstractScopedBinding {

  private $provider;

  public function __construct(Clazz $source, Provider $provider, Clazz $scope = null) {
    $this->source = $source;
    $this->provider = $provider;
    $this->scope = $scope;
  }

  /**
   * @return \Sharbat\Inject\Provider
   */
  public function getProviderInstance() {
    return $this->provider;
  }

  public function accept(BindingVisitor $bindingVisitor) {
    return $bindingVisitor->visitProviderInstanceBinding($this);
  }

}
