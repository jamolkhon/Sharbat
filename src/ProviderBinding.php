<?php

class ProviderBinding extends LinkedBinding
{
	protected function getInstanceInternal()
	{
		$provider = $this->injector->getInstance($this->class);
		return $provider->get();
	}
}
