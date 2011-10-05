<?php

class InstanceBinding extends Binding {
	
	public function getInstance() {
		return $this->getTarget();
	}

}
