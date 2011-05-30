<?php

class LinkedBinding extends ScopedBinding
{
	protected $class;
	protected $injector;
	
	public function __construct(Key $key, Scope $scope, $class)
	{
		parent::__construct($key, $scope);
		$this->class = $class;
	}
	
	public function setInjector(Injector $injector)
	{
		$this->injector = $injector;
	}
	
	protected function getInstanceInternal()
	{
		if ($this->class === $this->key->getName()) {
			return $this->injector->getInstanceOfClass($this->class);
		}
		
		return $this->injector->getInstance($this->class);
	}
}
