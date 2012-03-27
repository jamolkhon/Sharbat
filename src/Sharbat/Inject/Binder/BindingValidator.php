<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\Clazz;

class BindingValidator implements BindingVisitor {
  public function validateAll(array $bindings) {
    foreach ($bindings as $binding) {
      $this->validate($binding);
    }
  }

  public function validate(Binding $binding) {
    $binding->accept($this);

    if ($binding instanceof ScopedBinding) {
      /** @var ScopedBinding $binding */
      $this->validateScope($binding->getScope());
    }
  }

  private function validateScope(Clazz $scope = null) {
    if ($scope != null && !$scope->implementsInterface('\Sharbat\Inject\Scope')) {
      throw new InvalidBindingException(
        $scope->getQualifiedName() . ' is not a scope');
    }
  }

  public function visitLinkedBinding(LinkedBinding $binding) {
    $source = $binding->getSource();
    $target = $binding->getTarget();

    $valid = false;

    if ($source->isInterface()) {
      $valid = $target->implementsInterface($source->getQualifiedName());
    } else if (!$target->isInterface()) {
      $valid = $target->isSubclassOf($source->getQualifiedName());
    }

    if (!$valid) {
      throw new InvalidBindingException($target->getQualifiedName() .
          ' must implement/extend ' . $source->getQualifiedName());
    }
  }

  public function visitUntargettedBinding(UntargettedBinding $binding) {
    if (!$binding->getSource()->isInstantiable()) {
      throw new InvalidBindingException(
        $binding->getSource()->getQualifiedName() . ' cannot be instantiated');
    }
  }

  public function visitInstanceBinding(InstanceBinding $binding) {
    $source = $binding->getSource();
    $instance = $binding->getInstance();

    if (!is_object($instance)) {
      throw new InvalidBindingException('Non-object target provided for source ' .
          $source->getQualifiedName());
    }

    if (!$source->isInstance($binding->getInstance())) {
      throw new InvalidBindingException(get_class($instance) .
          ' must be an instance of ' . $source->getQualifiedName());
    }
  }

  public function visitProviderBinding(ProviderBinding $binding) {
    if (!$binding->getProvider()->implementsInterface('\Sharbat\Inject\Provider')) {
      throw new InvalidBindingException($binding->getProvider()->getQualifiedName() .
          ' is not a provider');
    }
  }

  public function visitProviderInstanceBinding(ProviderInstanceBinding $binding) {
  }

  public function visitConstantBinding(ConstantBinding $binding) {
    $constant = $binding->getConstant();

    if (empty($constant)) {
      throw new InvalidBindingException('Empty constant name');
    }
  }
}
