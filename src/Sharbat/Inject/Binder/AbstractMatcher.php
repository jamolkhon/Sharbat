<?php

namespace Sharbat\Inject\Binder;

abstract class AbstractMatcher implements Matcher {
  public function andMatch(Matcher $matcher) {
    return new AndMatcher($this, $matcher);
  }

  public function orMatch(Matcher $matcher) {
    return new OrMatcher($this, $matcher);
  }
}
