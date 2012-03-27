<?php

namespace Sharbat\Intercept;

use \Countable;
use \IteratorAggregate;
use \ArrayIterator;

class InvocationArguments implements Countable, IteratorAggregate {
  private $arguments = array();

  public function __construct(array $arguments) {
    $this->arguments = $arguments;
  }

  public function asArray() {
    return $this->arguments;
  }

  public function replaceArguments(array $arguments){
    $this->arguments = $arguments;
  }

  public function getArgument($offset) {
    return isset($this->arguments[$offset]) ? $this->arguments[$offset] : null;
  }

  public function setArgument($offset, $value) {
    $this->arguments[$offset] = $value;
  }

  public function size() {
    return $this->count();
  }

  public function count() {
    return count($this->arguments);
  }

  public function getIterator() {
    return new ArrayIterator($this->arguments);
  }
}
