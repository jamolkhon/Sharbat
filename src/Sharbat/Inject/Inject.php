<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Annotation;

class Inject implements Annotation {

  private $qualifiedClassName;

  public function __construct($qualifiedClassName = null) {
    $this->qualifiedClassName = $qualifiedClassName;
  }

  public function getDependencyName() {
    return $this->qualifiedClassName;
  }

}
