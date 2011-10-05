<?php

abstract class Binding {

	private $source;
	private $target;
	private $scope;
	
	public function __construct($source, $target, $scope)
	{
		$this->source = $source;
		$this->target = $target;
		$this->scope = $scope;
	}
	
	public function getSource() {
		return $this->source;
	}
	
	public function getTarget() {
		return $this->target;
	}
	
	public function setTarget($target) {
		$this->target = $target;
	}
	
	public function getScope()
	{
		return $this->scope;
	}
	
	public function setScope($scope)
	{
		$this->scope = $scope;
	}
	
	abstract public function getInstance();

}
