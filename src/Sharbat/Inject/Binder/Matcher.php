<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotatable;

interface Matcher {
  /**
   * @param Matcher $matcher
   * @return Matcher
   */
  function andMatch(Matcher $matcher);

  /**
   * @param mixed $T
   * @param \Sharbat\Inject\Annotatable $annotatable
   * @return bool
   */
  function matches($T, Annotatable $annotatable);

  /**
   * @param Matcher $matcher
   * @return Matcher
   */
  function orMatch(Matcher $matcher);
}
