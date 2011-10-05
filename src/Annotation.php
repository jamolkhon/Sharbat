<?php

class Annotation {

	private $name;
	private $arguments = array();
	
	public function __construct($name, array $arguments) {
		$this->name = $name;
		$this->arguments = $arguments;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getArgument($index) {
		if (isset($this->arguments[$index])) {
			return $this->arguments[$index];
		}
		
		return null;
	}
	
	public function getArguments() {
		return $this->arguments;
	}
	
	public function hasArgument($arg) {
		return in_array($arg, $this->arguments);
	}

}
