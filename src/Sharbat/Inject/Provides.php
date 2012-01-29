<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Annotation;

class Provides implements Annotation {

  private $dependency;

  public function __construct($dependency) {
    $this->dependency = $dependency;
  }

  public function getDependencyName() {
    return $this->dependency;
  }

}
