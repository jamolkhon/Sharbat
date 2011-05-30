<?php

class SimpleCache implements Cache
{
	protected $storage = array();
	
	public function __construct(array $storage=array())
	{
		$this->storage = $storage;
	}
	
	public function has($key)
	{
		return isset($this->storage[$key]);
	}
	
	public function get($key)
	{
		if (!isset($this->storage[$key])) {
			return null;
		}
		
		return $this->storage[$key];
	}
	
	public function set($key, $value)
	{
		$this->storage[$key] = $value;
	}
}
