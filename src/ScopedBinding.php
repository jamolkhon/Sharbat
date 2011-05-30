<?php

abstract class ScopedBinding extends Binding
{
	protected $scope;
	
	public function __construct(Key $key, Scope $scope)
	{
		parent::__construct($key);
		$this->scope = $scope;
	}
	
	public function getScope()
	{
		return $this->scope;
	}
	
	abstract protected function getInstanceInternal();
	
	public function getInstance()
	{
		if ($this->instance) {
			return $this->instance;
		} elseif ($this->scope->isSingleton()) {
			return $this->instance = $this->getInstanceInternal();
		}
		
		return $this->getInstanceInternal();
	}
}
