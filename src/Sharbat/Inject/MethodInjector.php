<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Method;

class MethodInjector implements MemberInjector {
  private $method;
  private $dependencies = array();

  public function __construct(Method $method, array $dependencies) {
    $this->method = $method;
    $this->dependencies = $dependencies;
  }

  public function injectTo($instance) {
    $this->method->invokeArgs($instance, $this->dependencies);
  }
}
