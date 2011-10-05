<?php

class Application {

	protected $requestProvider;
	protected $dispatcher;

	public function __construct(RequestProvider $requestProvider,
		Dispatcher $dispatcher) {
		$this->requestProvider = $requestProvider;
		$this->dispatcher = $dispatcher;
	}

	public function execute() {
		$request = $this->requestProvider->get();
		$response = $this->dispatcher->dispatch($request);
	}

}
