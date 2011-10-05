<?php

class Router {

	protected $routes = array();

	public function __construct(array $routes) {
		$this->routes = $routes;
	}

	public function match($uri) {
		foreach ($this->routes as $route) {
			if ($route->match($uri)) {
				return $route;
			}
		}

		return null;
	}

	public static function builder() {
		return new RouterBuilder();
	}

}

class RouterBuilder {

	protected $routes = array();

	public function createRoute($pattern, array $params=array()) {
		$pattern = '#^' . preg_quote($pattern) . '$#';
		$pattern = str_replace('\(', '(?:', $pattern);
		$pattern = str_replace('\)', ')?', $pattern);
		return $this->routes[] = new Route($pattern, $params);
	}

	public function build() {
		foreach ($this->routes as $route) {
			$route->where()->matches('\w+');
		}

		return new Router($this->routes);
	}

}

class Route {

	protected $pattern = '';
	protected $defaultParams = array();
	protected $params = array();

	public function __construct($pattern, array $params) {
		$this->pattern = $pattern;
		$this->defaultParams = $params;
	}

	public function where($paramKey='') {
		return new PatternBuilder($this, $paramKey);
	}

	public function getPattern() {
		return $this->pattern;
	}

	public function setPattern($pattern) {
		$this->pattern = $pattern;
	}

	public function getParam($key) {
		if (!isset($this->params[$key])) {
			return null;
		}

		return $this->params[$key];
	}

	public function getParams() {
		return $this->params;
	}

	public function match($uri) {
		$count = preg_match($this->pattern, $uri, $matches);
		$this->params = $matches + $this->defaultParams;
		return !!$count;
	}

}

class PatternBuilder {

	protected $route;
	protected $paramKey = '';

	public function __construct(Route $route, $paramKey) {
		$this->route = $route;
		$this->paramKey = $paramKey;
	}

	protected function buildPattern($pattern, $paramKey, $paramPattern) {
		$search = preg_quote("<{$paramKey}>");
		$replaceKey = preg_quote($paramKey);
		$parts = explode($search, $pattern, 2);

		if (count($parts) === 2) {
			$parts[1] = str_replace($search, "(?P={$replaceKey})", $parts[1]);
			$pattern = implode("(?P<{$replaceKey}>{$paramPattern})", $parts);
		}

		return $pattern;
	}

	public function matches($paramPattern) {
		$pattern = $this->route->getPattern();

		if ($this->paramKey) {
			$pattern = $this->buildPattern($pattern, $this->paramKey,
				$paramPattern);
		} else {
			preg_match_all('#\\\<(.+?)\\\>#', $pattern, $matches);
			$paramKeys = array_unique($matches[1]);

			foreach ($paramKeys as $paramKey) {
				$pattern = $this->buildPattern($pattern, $paramKey,
					$paramPattern);
			}
		}

		$this->route->setPattern($pattern);
		return $this->route;
	}

}
