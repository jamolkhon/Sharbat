<?php

class BinderException extends Exception {}

class Binder
{
	protected $bindings = array();
	
	public function addBinding(Binding $binding)
	{
		$key = $binding->getKey();
		$hash = $key->getHash();
		
		if (isset($this->bindings[$hash])) {
			throw new BinderException($key->getName() . ' is already bound');
		}
		
		$this->bindings[$hash] = $binding;
	}
	
	public function bind($target)
	{
		$key = new Key($target);
		
		if ($key->isConstant()) {
			throw new BinderException('No such class/interface ' . $target);
		}
		
		return new BindingBuilder($this, $key, new Scope('Request'));
	}
	
	public function bindConstant($constant)
	{
		$key = Key::fromConstant($constant);
		return new ConstantBindingBuilder($this, $key, new Scope('Request'));
	}
	
	public function build(Injector $injector,
		SessionNamespace $sessionNamespace)
	{
		foreach ($this->bindings as $binding) {
			if ($binding instanceof LinkedBinding) {
				$hash = $binding->getKey()->getHash();
				
				if ($binding->getScope()->isSession() &&
					$sessionNamespace->hasKey($hash)) {
					$binding->setInstance($sessionNamespace->get($hash));
				} else {
					$binding->setInjector($injector);
				}
			}
		}
		
		return $this;
	}
	
	public function lookUp($target)
	{
		$key = new Key($target);
		$hash = $key->getHash();
		
		if ($key->isConstant() || !isset($this->bindings[$hash])) {
			return null;
		}
		
		return $this->bindings[$hash];
	}
	
	public function lookUpConstant($constant)
	{
		$key = Key::fromConstant($constant);
		$hash = $key->getHash();
		
		if (!isset($this->bindings[$hash])) {
			return null;
		}
		
		return $this->bindings[$hash];
	}
}
