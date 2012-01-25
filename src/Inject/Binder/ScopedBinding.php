<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;

interface ScopedBinding extends Binding {

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  function getScope();

  function setScope(Clazz $scope);

  function unsetScope();

}
