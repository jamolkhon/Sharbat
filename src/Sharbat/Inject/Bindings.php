<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Binding;

interface Bindings {
  function addBinding(Binding $binding);

  /**
   * @param string $key
   * @return \Sharbat\Inject\Binder\Binding|null
   */
  function getBinding($key);

  /**
   * @param string $constant
   * @return \Sharbat\Inject\Binder\ConstantBinding
   */
  function getConstantBinding($constant);

  /**
   * @param string $key
   * @return \Sharbat\Inject\Binder\Binding
   */
  function getOrCreateBinding($key);

  /**
   * @return \Sharbat\Inject\Binder\Binding[]
   */
  function getBindings();

  function removeBinding($key);
}
