<?php

namespace Sharbat\Inject\Binder;

class ConstantBinding implements Binding {
  private $constant;
  private $value;

  public function __construct($constant, $value = null) {
    $this->constant = $constant;
    $this->value = $value;
  }

  public static function generateKey($constant) {
    return 'constant:' . $constant;
  }

  public function getKey() {
    return self::generateKey($this->constant);
  }

  public function getConstant() {
    return $this->constant;
  }

  public function getValue() {
    return $this->value;
  }

  public function to($value) {
    $this->value = $value;
  }

  public function accept(BindingVisitor $bindingVisitor) {
    return $bindingVisitor->visitConstantBinding($this);
  }
}
