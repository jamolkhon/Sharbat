<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Method;
use \RuntimeException;

class ProvidesProvider implements Provider {

  private $methodInvoker;

  /** @var \Sharbat\Inject\AbstractModule */
  private $module;

  /** @var \Sharbat\Reflect\Method */
  private $method;

  public function __construct(MethodInvoker $methodInvoker) {
    $this->methodInvoker = $methodInvoker;
  }

  public function forModuleMethod(AbstractModule $module, Method $method) {
    $providesProvider = new ProvidesProvider($this->methodInvoker);
    $providesProvider->module = $module;
    $providesProvider->method = $method;
    return $providesProvider;
  }

  public function get() {
    if ($this->module == null || $this->method == null) {
      throw new RuntimeException('Invalid State: Module and/or method is null');
    }

    return $this->methodInvoker->invokeMethod($this->module,
      $this->method->getUnqualifiedName());
  }

}
