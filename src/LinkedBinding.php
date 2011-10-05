<?php

class LinkedBinding extends Binding {

	private $injector;
	
	public function setInjector(Injector $injector) {
		$this->injector = $injector;
	}
	
	public function getInstance() {
		if ($this->getTarget() === null) {
			return $this->injector->createInstance($this->getSource());
		}
		
		return $this->injector->getInstance($this->getTarget());
	}

}
