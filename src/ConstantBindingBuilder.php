<?php

class ConstantBindingBuilder extends BindingBuilder
{
	public function to($value)
	{
		$binding = new ConstantBinding($this->key);
		$binding->setInstance($value);
		$this->binder->addBinding($binding);
		return null;
	}
}
