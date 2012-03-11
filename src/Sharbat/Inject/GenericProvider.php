<?php

namespace Sharbat\Inject;

use \RuntimeException;

/**
 * \Sharbat\@Singleton
 */
class GenericProvider implements Provider {

  private $injector;
  private $targetType;

  public function __construct(Injector $injector) {
    $this->injector = $injector;
  }

  public function get() {
    if ($this->targetType == null) {
      throw new RuntimeException('Invalid state: targetType is null');
    }

    return $this->injector->getInstance($this->targetType);
  }

  public function createProviderFor($qualifiedClassName) {
    $genericProvider = new GenericProvider($this->injector);
    $genericProvider->targetType = $qualifiedClassName;
    return $genericProvider;
  }

}
