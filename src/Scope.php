<?php

class ScopeException extends Exception {}

class Scope
{
	const REQUEST = 1;
	const SINGLETON = 2;
	const SESSION = 3;
	
	protected $scope;
	
	public function __construct($scope)
	{
		$this->in($scope);
	}
	
	public function in($scope)
	{
		$scopesMapping = array(
			'Request' => self::REQUEST,
			'Singleton' => self::SINGLETON,
			'Session' => self::SESSION
		);
		
		if (!isset($scopesMapping[$scope])) {
			throw new ScopeException('Unknown scope name ' . $scope);
		}
		
		$this->scope = $scopesMapping[$scope];
	}
	
	public function isRequest()
	{
		return $this->scope === self::REQUEST;
	}
	
	public function isSingleton()
	{
		return $this->scope === self::SINGLETON;
	}
	
	public function isSession()
	{
		return $this->scope === self::SESSION;
	}
}
