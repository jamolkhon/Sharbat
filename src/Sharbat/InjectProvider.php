<?php

namespace Sharbat;

class InjectProvider extends Inject {

  private $targetType;

  public function setT($qualifiedClassName) {
    $this->targetType = $qualifiedClassName;
  }

  public function getTargetType() {
    return $this->targetType;
  }

}
