<?php

class SingletonScope implements Scope {

	private $instances = array();

	public function getInstance(Binding $binding) {
		$source = $binding->getSource();
		
		if (isset($this->instances[$source])) {
			return $this->instances[$source];
		}
		
		return $this->instances[$source] = $binding->getInstance();
	}

}
