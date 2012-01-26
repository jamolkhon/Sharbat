<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Method;

interface DependenciesProvider {

  /**
   * @param string $qualifiedClassName
   * @param string $method
   * @return array
   */
  function getDependencies($qualifiedClassName, $method);

  /**
   * @param string $qualifiedClassName
   * @return array
   */
  function getConstructorDependencies($qualifiedClassName);

  /**
   * @param \Sharbat\Reflect\Method $method
   * @return array
   */
  function getDependenciesOfMethod(Method $method);

}
