<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotatable;

class InvertedMatcher extends AbstractMatcher {
  private $matcher;

  public function __construct(Matcher $matcher) {
    $this->matcher = $matcher;
  }

  public function matches($T, Annotatable $annotatable) {
    return !$this->matcher->matches($T, $annotatable);
  }
}
