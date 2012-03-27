<?php

namespace Sharbat\Intercept;

interface Invocation extends Joinpoint {
  /**
   * @return \Sharbat\Intercept\InvocationArguments
   */
  function getArguments();
}
