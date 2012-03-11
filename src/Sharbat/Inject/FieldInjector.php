<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\Field;

class FieldInjector implements MemberInjector {

  private $field;
  private $dependency;

  public function __construct(Field $field, $dependency) {
    $this->field = $field;
    $this->dependency = $dependency;
  }

  public function injectTo($instance) {
    $this->field->setValue($instance, $this->dependency);
  }

}
