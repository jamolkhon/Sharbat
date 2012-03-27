<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotatable;

class IdentityMatcher extends AbstractMatcher {
  private $object;

  public function __construct($object) {
    $this->object = $object;
  }

  public function matches($T, Annotatable $annotatable) {
    return $T === $this->object;
  }
}
