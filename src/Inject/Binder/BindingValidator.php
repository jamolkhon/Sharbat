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
  }

  private function validateScope(Clazz $scope = null) {
    if ($scope != null && !$scope->implementsInterface('\Sharbat\Inject\Scope')) {
      throw new InvalidBindingException($scope->getQualifiedName() . ' is not a scope');
    }
  }

  public function visitLinkedBinding(LinkedBinding $binding) {
    $source = $binding->getSource();
    $target = $binding->getTarget();

    if ($target != null) {
      $valid = false;

      if ($source->isInterface()) {
        /* if target is also an interface and extends source interface
           then both implementsInterface and isSubclassOf methods return true */
        $valid = $target->implementsInterface($source->getQualifiedName());
      } else if (!$target->isInterface()) {
        $valid = $target->isSubclassOf($source->getQualifiedName());
      }

      if (!$valid) {
        throw new InvalidBindingException($target->getQualifiedName() .
            ' must implement/extend ' . $source->getQualifiedName());
      }
    }

    $this->validateScope($binding->getScope());
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

    $this->validateScope($binding->getScope());
  }

  public function visitProviderBinding(ProviderBinding $binding) {
    if (!$binding->getProvider()->implementsInterface('\Sharbat\Inject\Provider')) {
      throw new InvalidBindingException($binding->getProvider()->getQualifiedName() .
          ' is not a provider');
    }

    $this->validateScope($binding->getScope());
  }

  public function visitProviderInstanceBinding(ProviderInstanceBinding $binding) {
    $this->validateScope($binding->getScope());
  }

  public function visitConstantBinding(ConstantBinding $binding) {
    $constant = $binding->getConstant();

    if (empty($constant)) {
      throw new InvalidBindingException('Empty constant name');
    }
  }

}
