<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Matcher;
use Sharbat\Intercept\Interceptor;

interface Binder {
  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Binder\LinkedBindingBuilder
   */
  function bind($qualifiedClassName);

  /**
   * @param string $constant
   * @return \Sharbat\Inject\Binder\ConstantBinding
   */
  function bindConstant($constant);

  function bindInterceptor(Matcher $classMatcher, Matcher $methodMatcher,
    Interceptor $interceptor);

  function install(AbstractModule $module);

  /**
   * @return \Sharbat\Inject\Binder
   */
  function build();

  function requestInjection($instance);
}
