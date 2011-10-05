<?php

class ConstantBindingBuilder {

	private $binder;
	private $constant;
	private $scope;
	
	public function __construct(Binder $binder, $constant, $scope) {
		$this->binder = $binder;
		$this->constant = $constant;
		$this->scope = $scope;
	}
	
	public function to($value) {
		$binding = new ConstantBinding($this->constant, $value, $this->scope);
		$this->binder->addBinding($binding);
		return new ScopeAssigner($binding);
	}
	
}
