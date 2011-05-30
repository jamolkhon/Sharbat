<?php

class BindingBuilderException extends Exception {}

class BindingBuilder
{
	protected $binder;
	protected $key;
	protected $scope;
	
	public function __construct(Binder $binder, Key $key, Scope $scope)
	{
		$this->binder = $binder;
		$this->key = $key;
		$this->scope = $scope;
	}
	
	public function ensureValidClass($class)
	{
		if (!is_string($class)) {
			throw new BindingBuilderException('Expecting a string but ' .
				gettype($class) . ' was provided');
		}
		
		if (!class_exists($class)) {
			throw new BindingBuilderException('Class ' . $class . ' not found');
		}
		
		return true;
	}
	
	public function ensureValidBinding($class)
	{
		$this->ensureValidClass($class);
		
		$target = $this->key->getName();
		
		if ($this->key->isInterface()) {
			if (!in_array($target, class_implements($class))) {
				throw new BindingBuilderException('Class ' . $class .
					' must implement ' . $target);
			}
		} elseif ($this->key->isClass()) {
			if ($target !== $class &&
				!in_array($target, class_parents($class))) {
				throw new BindingBuilderException('Class ' . $class .
					' must extend or be an instance of ' . $target);
			}
		}
		
		return true;
	}
	
	public function ensureValidInstance($instance)
	{
		if (!is_object($instance)) {
			throw new BindingBuilderException('Expecting an object but ' .
				gettype($instance) . ' was provided');
		}
		
		$target = $this->key->getName();
		
		if (!($instance instanceof $target)) {
			throw new BindingBuilderException('Class ' . get_class($instance) .
				' must extend/implement ' . $target);
		}
		
		return true;
	}
	
	public function ensureValidProvider($provider)
	{
		$this->ensureValidClass($provider);
		
		if (!in_array('Provider', class_implements($class))) {
			throw new BindingBuilderException('Class ' . $class .
				'must implement Provider');
		}
		
		return true;
	}
	
	public function to($class)
	{
		$this->ensureValidBinding($class);
		
		$binding = new LinkedBinding($this->key, $this->scope, $class);
		$this->binder->addBinding($binding);
		return $binding->getScope();
	}
	
	public function toProvider($provider)
	{
		$this->ensureValidProvider($provider);
		
		$binding = new ProviderBinding($this->key, $this->scope, $provider);
		$this->binder->addBinding($binding);
		return $binding->getScope();
	}
	
	public function toInstance($instance)
	{
		$this->ensureValidInstance($instance);
		
		$binding = new InstanceBinding($this->key, $this->scope);
		$binding->setInstance($instance);
		$this->binder->addBinding($binding);
		return null;
	}
}
