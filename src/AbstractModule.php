<?php

abstract class AbstractModule {

	private $binder;
	private $installedModules = array();

	public function setBinder(Binder $binder) {
		$this->binder = $binder;
	}

	public function bind($class) {
		return $this->binder->bind($class);
	}

	public function install(AbstractModule $module) {
		$this->installedModules[] = $module;
		$module->setBinder($this->binder);
		$module->configure();
	}
	
	public function getInstalledModules() {
		return $this->installedModules;
	}

	abstract public function configure();

}
