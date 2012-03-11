<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Field;
use Sharbat\Reflect\Method;
use Sharbat\Reflect\Parameter;

interface DependenciesProvider {

  /**
   * @param string $qualifiedClassName
   * @param string $method
   * @return array
   */
  function getDependencies($qualifiedClassName, $method);

  /**
   * @param \Sharbat\Reflect\Field $field
   * @return mixed
   */
  function getDependencyOfField(Field $field);

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

  /**
   * @param \Sharbat\Reflect\Parameter $parameter
   * @return mixed
   */
  function getDependencyOfParameter(Parameter $parameter);

}
