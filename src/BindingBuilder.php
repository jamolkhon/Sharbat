<?php

class BindingBuilder {

	private $binder;
	private $class;
	private $scope;

	public function __construct(Binder $binder, $class, $scope) {
		$this->binder = $binder;
		$this->class = $class;
		$this->scope = $scope;
	}

	public function to($class) {
		$binding = new LinkedBinding($this->class, $class, $this->scope);
		$this->binder->addBinding($binding);
		return new ScopeAssigner($binding);
	}

	public function toProvider($provider) {
		$binding = new ProviderBinding($this->class, $provider, $this->scope);
		$this->binder->addBinding($binding);
		return new ScopeAssigner($binding);
	}
	
	public function toProviderInstance($provider) {
		$binding = new ProviderInstanceBinding($this->class, $provider,
				$this->scope);
		$this->binder->addBinding($binding);
		return new ScopeAssigner($binding);
	}

	public function toInstance($instance) {
		$binding = new InstanceBinding($this->class, $instance, $this->scope);
		$this->binder->addBinding($binding);
		return new ScopeAssigner($binding);
	}

	public function in($scope) {
		$binding = new LinkedBinding($this->class, null, $scope);
		$this->binder->addBinding($binding);
	}

}
