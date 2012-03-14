<?php

namespace Sharbat;

use Sharbat\Inject\Annotation;

class Provider implements Annotation {
  private $parameterName;
  private $targetClassName;

  public function setParam($parameterName) {
    $this->parameterName = $parameterName;
  }

  public function setT($qualifiedClassName) {
    $this->targetClassName = $qualifiedClassName;
  }

  public function getParameterName() {
    return $this->parameterName;
  }

  public function getTargetClassName() {
    return $this->targetClassName;
  }
}
