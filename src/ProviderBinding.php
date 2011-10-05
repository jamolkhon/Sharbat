<?php

class ProviderBinding extends LinkedBinding {

	public function getInstance() {
		return parent::getInstance()->get();
	}

}
