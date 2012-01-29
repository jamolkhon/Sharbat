<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Annotation;

class InScope implements Annotation {

  private $qualifiedClassName;

  public function __construct($qualifiedClassName) {
    $this->qualifiedClassName = $qualifiedClassName;
  }

  public function getScopeClassName() {
    return $this->qualifiedClassName;
  }

}
