<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotatable;

class SubclassMatcher extends AbstractMatcher {
  private $qualifiedClassName;

  public function __construct($qualifiedClassName) {
    $this->qualifiedClassName = $qualifiedClassName;
  }

  public function matches($T, Annotatable $annotatable) {
    return $T instanceof $this->qualifiedClassName;
  }
}
