<?php

namespace Sharbat\Inject;

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

  function install(AbstractModule $module);

  /**
   * @return \Sharbat\Inject\Binder
   */
  function build();

  function requestInjection($instance);
}
