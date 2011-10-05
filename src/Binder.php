<?php

class Binder {

	private $annotationUtils;
	private $bindingValidator;
	private $injector;
	
	private $bindings = array();

	public function __construct(AnnotationUtils $annotationUtils,
			BindingValidator $bindingValidator) {
		$this->annotationUtils = $annotationUtils;
		$this->bindingValidator = $bindingValidator;
	}

	public function setInjector(Injector $injector) {
		$this->injector = $injector;
	}

	public function addBinding(Binding $binding) {
		$source = $binding->getSource();
		
		if(!$source) {
			throw new Exception('Invalid binding source: ' . $source);
		}
		if (isset($this->bindings[$source])) {
			throw new Exception($source . ' is already bound');
		}

		$this->bindings[$source] = $binding;
	}

	public function bind($class) {
		$annotationInfo = $this->annotationUtils
				->getAnnotationInfoForClass($class);
		return new BindingBuilder($this, $class, $annotationInfo->getScope(
				Scopes::NO_SCOPE));
	}

	public function bindConstant($constant) {
		return new ConstantBindingBuilder($this, $constant, Scopes::NO_SCOPE);
	}

	public function build() {
		$this->bindingValidator->validateAll($this->bindings);
		
		foreach ($this->bindings as $binding) {
			if ($binding instanceof LinkedBinding) {
				$binding->setInjector($this->injector);
			}
		}

		return $this;
	}

	public function isBound($source) {
		return isset($this->bindings[$source]);
	}

	public function getBinding($source) {
		if ($this->isBound($source)) {
			return $this->bindings[$source];
		}

		// Just in time binding
		$annotationInfo = $this->annotationUtils->getAnnotationInfoForClass(
				$source);
		$binding = new LinkedBinding($source, null, $annotationInfo->getScope(
				Scopes::NO_SCOPE));
		$binding->setInjector($this->injector);
		return $binding;
	}

}
