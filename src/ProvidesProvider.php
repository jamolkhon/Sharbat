<?php

class ProvidesProvider implements Provider {

	private $injector;
	private $module;
	private $methodName;

	public function __construct(Injector $injector, AbstractModule $module,
			$methodName) {
		$this->injector = $injector;
		$this->module = $module;
		$this->methodName = $methodName;
	}
	
	public function get() {
		return $this->injector->injectIntoMethod($this->module,
				$this->methodName);
	}

}
