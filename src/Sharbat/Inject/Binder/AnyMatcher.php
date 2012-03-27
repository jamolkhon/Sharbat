<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotatable;

class AnyMatcher extends AbstractMatcher {
  public function matches($T, Annotatable $annotatable) {
    return true;
  }
}
