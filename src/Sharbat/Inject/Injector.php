<?php

namespace Sharbat\Inject;

interface Injector {

  function getInstance($qualifiedClassName);

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Provider
   */
  function getProviderFor($qualifiedClassName);

  function getConstant($constant);

  function createInstance($qualifiedClassName);

}
