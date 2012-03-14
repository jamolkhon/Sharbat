<?php

namespace Sharbat;

use Sharbat\Inject\Annotation;

class Scope implements Annotation {
  private $qualifiedClassName;

  public function __construct($qualifiedClassName) {
    $this->qualifiedClassName = $qualifiedClassName;
  }

  public function getScopeClassName() {
    return $this->qualifiedClassName;
  }
}
