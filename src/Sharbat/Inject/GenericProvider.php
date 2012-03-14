<?php

namespace Sharbat\Inject;

use \RuntimeException;

/**
 * \Sharbat\@Singleton
 */
class GenericProvider implements Provider {

  private $injector;
  private $targetClassName;

  public function __construct(Injector $injector) {
    $this->injector = $injector;
  }

  public function get() {
    if ($this->targetClassName == null) {
      throw new RuntimeException('Invalid state: targetClassName is null');
    }

    return $this->injector->getInstance($this->targetClassName);
  }

  public function createProviderFor($qualifiedClassName) {
    $genericProvider = new GenericProvider($this->injector);
    $genericProvider->targetClassName = $qualifiedClassName;
    return $genericProvider;
  }

}
