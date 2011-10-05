<?php

class ScopeAssigner {

	private $binding;
	
	public function __construct(Binding $binding) {
		$this->binding = $binding;
	}
	
	public function in($scope) {
		$this->binding->setScope($scope);
	}

}
