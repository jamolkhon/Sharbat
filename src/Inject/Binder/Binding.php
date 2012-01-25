<?php

namespace Sharbat\Inject\Binder;

interface Binding {

  /**
   * @return string
   */
  function getKey();

  function accept(BindingVisitor $bindingVisitor);

}
