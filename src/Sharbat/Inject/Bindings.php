<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Binding;
use Sharbat\Reflect\Clazz;

interface Bindings {
  function addBinding(Binding $binding);

  /**
   * @param object $object
   * @return array
   */
  function getInterceptors($object);

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\Binder\Binding|null
   */
  function getBinding(Clazz $class);

  /**
   * @param string $constant
   * @return \Sharbat\Inject\Binder\ConstantBinding
   */
  function getConstantBinding($constant);

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\Binder\Binding
   */
  function getOrCreateBinding(Clazz $class);

  /**
   * @return \Sharbat\Inject\Binder\Binding[]
   */
  function getBindings();

  function removeBinding($key);
}
