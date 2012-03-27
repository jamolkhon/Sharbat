<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotatable;

class OrMatcher extends AbstractMatcher {
  private $firstMatcher;
  private $secondMatcher;

  public function __construct(Matcher $firstMatcher, Matcher $secondMatcher) {
    $this->firstMatcher = $firstMatcher;
    $this->secondMatcher = $secondMatcher;
  }

  public function matches($T, Annotatable $annotatable) {
    return $this->firstMatcher->matches($T, $annotatable) ||
        $this->secondMatcher->matches($T, $annotatable);
  }
}
