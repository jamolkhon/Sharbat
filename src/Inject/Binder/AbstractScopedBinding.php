<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;

abstract class AbstractScopedBinding implements ScopedBinding {

  /** @var \Sharbat\Reflect\Clazz */
  protected $source;

  /** @var \Sharbat\Reflect\Clazz */
  protected $scope;

  public function getKey() {
    return $this->source->getQualifiedName();
  }

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getSource() {
    return $this->source;
  }

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getScope() {
    return $this->scope;
  }

  public function setScope(Clazz $scope) {
    $this->scope = $scope;
  }

  public function unsetScope() {
    $this->scope = null;
  }

}
