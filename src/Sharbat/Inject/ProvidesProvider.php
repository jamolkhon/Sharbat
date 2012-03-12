<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Method;
use \RuntimeException;

class ProvidesProvider implements Provider {

  private $dependenciesProvider;

  /**
   * @var \Sharbat\Inject\AbstractModule
   */
  private $module;

  /**
   * @var \Sharbat\Reflect\Method
   */
  private $method;

  public function __construct(DependenciesProvider $dependenciesProvider) {
    $this->dependenciesProvider = $dependenciesProvider;
  }

  public function createProviderFor(AbstractModule $module, Method $method) {
    $providesProvider = new ProvidesProvider($this->dependenciesProvider);
    $providesProvider->module = $module;
    $providesProvider->method = $method;
    return $providesProvider;
  }

  public function get() {
    if ($this->module == null || $this->method == null) {
      throw new RuntimeException('Invalid state: module and/or method is null');
    }

    $dependencies = $this->dependenciesProvider->getDependenciesOfMethod(
      $this->method);
    return $this->method->invokeArgs($this->module, $dependencies);
  }

}
