<?php

abstract class Binding
{
	protected $key;
	protected $instance;
	
	public function __construct(Key $key)
	{
		$this->key = $key;
	}
	
	public function getKey()
	{
		return $this->key;
	}
	
	public function getInstance()
	{
		return $this->instance;
	}
	
	public function setInstance($instance)
	{
		$this->instance = $instance;
	}
}
