<?php

class RequestDispatcher implements Dispatcher {

	protected $router;
	protected $injector;

	public function __construct(Router $router, Injector $injector) {
		$this->router = $router;
		$this->injector = $injector;
	}

	public function dispatch(Request $request) {
		$uri = $request->getPathInfo();
		$route = $this->router->match($uri);

		if (!$route) {
			throw new Exception('No matching route for uri ' . $uri);
		}

		$controller = $route->getParam('controller');

		if (!$controller) {
			throw new Exception('Empty controller parameter for uri ' . $uri);
		}

		$controller = ucfirst($controller) . 'Controller';
		$controllerInstance = $this->injector->getInstance($controller);
		$controllerInstance->handle($request, new Response(array(), null));
	}

}
