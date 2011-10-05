<?php

class ReflectionDao {

	private $reflections = array();
	
	public function get($target) {
		$className = is_object($target) ? get_class($target) : $target;
		
		if (isset($reflections[$className])) {
			return $reflections[$className];
		}
		
		return $reflections[$className] = new ReflectionClass($target);
	}

}
