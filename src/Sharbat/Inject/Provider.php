<?php

namespace Sharbat\Inject;

interface Provider {
  /**
   * @return object
   */
  function get();
}
