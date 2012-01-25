<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Annotation;

class InScope implements Annotation {

  private $qualifiedClassName;

  public function __construct($qualifiedClassName) {
    $this->qualifiedClassName = $qualifiedClassName;
  }

  public static function getName() {
    return __CLASS__;
  }

  public function getScopeClassName() {
    return $this->qualifiedClassName;
  }

}
