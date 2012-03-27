<?php

namespace Sharbat\Intercept;

use Sharbat\Reflect\Method;

class MethodInvocation implements Invocation {
  private $object;
  private $method;
  private $invocationArguments = array();
  private $interceptors = array();

  public function __construct($object, Method $method,
      InvocationArguments $invocationArguments, array $interceptors) {
    $this->object = $object;
    $this->method = $method;
    $this->invocationArguments = $invocationArguments;
    $this->interceptors = $interceptors;
  }

  /**
   * @return \Sharbat\Reflect\Method
   */
  public function getMethod() {
    return $this->method;
  }

  public function getArguments() {
    return $this->invocationArguments;
  }

  /**
   * @return \Sharbat\Reflect\Method
   */
  public function getStaticPart() {
    return $this->method;
  }

  public function getThis() {
    return $this->object;
  }

  public function proceed() {
    if (empty($this->interceptors)) {
      $arguments = $this->invocationArguments->asArray();
      return $this->method->invokeArgs($this->object, $arguments);
    }

    /** @var MethodInterceptor $nextInterceptor */
    $nextInterceptor = array_shift($this->interceptors);
    return $nextInterceptor->invoke($this);
  }
}
