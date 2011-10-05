<?php

class ProviderInstanceBinding extends InstanceBinding {

	public function getInstance() {
		return parent::getTarget()->get();
	}

}
