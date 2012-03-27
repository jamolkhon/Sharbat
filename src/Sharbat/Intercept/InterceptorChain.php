<?php

namespace Sharbat\Intercept;

use Sharbat\Reflect\Method;

class InterceptorChain {
  private $method;
  private $interceptors = array();

  public function __construct(Method $method, array $interceptors) {
    $this->method = $method;
    $this->interceptors = $interceptors;
  }

  public function apply($object, array $arguments) {
    $methodInvocation = new MethodInvocation($object, $this->method,
      new InvocationArguments($arguments), $this->interceptors);
    return $methodInvocation->proceed();
  }
}
