<?php

class RequestProvider implements Provider {

	public function get() {
		$host = $_SERVER['SERVER_NAME'];
		list($uri) = explode('?', $_SERVER['REQUEST_URI'], 2);
		$pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
		$method = $_SERVER['REQUEST_METHOD'] === 'POST'
			? Request::POST : Request::GET;
		$headers = getallheaders();
		$get = $_GET;
		$post = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : array();
		return new Request($host, $uri, $pathInfo, $method, $headers, $get, $post);
	}

}
