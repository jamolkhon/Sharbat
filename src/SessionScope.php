<?php

class SessionScope implements Scope {

	private $session;

	public function __construct(Session $session) {
		$this->session = $session;
	}

	public function getInstance(Binding $binding) {
		$key = $binding->getSource();
		$instance = $this->session->get($key);

		if ($instance) {
			return $instance;
		}

		$instance = $binding->getInstance();
		$this->session->set($key, $instance);
		return $instance;
	}

}
