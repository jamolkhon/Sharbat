<?php

namespace Sharbat\Inject;

use \RuntimeException;

class DefaultInjector implements Injector, MembersInjector {

  private $bindingDao;
  private $bindingInstantiator;
  private $instanceCreator;
  private $membersInjector;

  public function __construct(BindingDao $bindingDao,
      BindingInstantiator $bindingInstantiator, InstanceCreator $instanceCreator,
      MembersInjector $membersInjector) {
    $this->bindingDao = $bindingDao;
    $this->bindingInstantiator = $bindingInstantiator;
    $this->instanceCreator = $instanceCreator;
    $this->membersInjector = $membersInjector;
  }

  public function getInstance($qualifiedClassName) {
    $binding = $this->bindingDao->getOrCreateBinding($qualifiedClassName);
    return $this->bindingInstantiator->getInstance($binding);
  }

  public function getConstant($constant) {
    $constantBinding = $this->bindingDao->getConstantBinding($constant);

    if ($constantBinding == null) {
      throw new RuntimeException('No binding found for constant: ' . $constant);
    }

    return $constantBinding->getValue();
  }

  public function createInstance($qualifiedClassName) {
    $instance = $this->instanceCreator->createInstance($qualifiedClassName);
    $this->injectMembers($instance);
    return $instance;
  }

  public function injectMembers($instance) {
    $this->membersInjector->injectMembers($instance);
  }

  public function injectFields($instance) {
    $this->membersInjector->injectFields($instance);
  }

  public function injectMethods($instance) {
    $this->membersInjector->injectMethods($instance);
  }

}
