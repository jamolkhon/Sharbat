<?php

namespace Sharbat\Inject\Binder;

class InterceptorBinding {
  private $classMatcher;
  private $methodMatcher;
  private $interceptors = array();

  public function __construct(Matcher $classMatcher, Matcher $methodMatcher,
      array $interceptors) {
    $this->classMatcher = $classMatcher;
    $this->methodMatcher = $methodMatcher;
    $this->interceptors = $interceptors;
  }

  /**
   * @return Matcher
   */
  public function getClassMatcher() {
    return $this->classMatcher;
  }

  /**
   * @return Matcher
   */
  public function getMethodMatcher() {
    return $this->methodMatcher;
  }

  /**
   * @return \Sharbat\Intercept\Interceptor[]
   */
  public function getInterceptors() {
    return $this->interceptors;
  }
}
