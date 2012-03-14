<?php

namespace Sharbat;

class InjectProvider extends Inject {
  private $targetClassName;

  public function setT($qualifiedClassName) {
    $this->targetClassName = $qualifiedClassName;
  }

  public function getTargetClassName() {
    return $this->targetClassName;
  }
}
