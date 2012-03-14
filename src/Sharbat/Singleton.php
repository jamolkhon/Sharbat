<?php

namespace Sharbat;

class Singleton extends Scope {
  public function __construct() {
    parent::__construct('\Sharbat\Inject\Singleton');
  }
}
