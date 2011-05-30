<?php

class HttpRequest
{
	const GET = 1;
	const POST = 2;
	
	protected $host = '';
	protected $uri = '/';
	protected $pathInfo = '/';
	protected $method = self::GET;
	protected $headers = array();
	protected $getParams = array();
	protected $postParams = array();
	
	public function __construct($host, $uri='/', $pathInfo='/',
		$method=self::GET, array $headers=array(), array $getParams=array(),
			array $postParams=array())
	{
		$this->host = $host;
		$this->uri = $uri;
		$this->pathInfo = $pathInfo;
		$this->method = $method;
		$this->headers = $headers;
		$this->getParams = $getParams;
		
		if ($method === self::POST) {
			$this->postParams = $postParams;
		}
	}
	
	public function getHost()
	{
		return $this->host;
	}
	
	public function getUri()
	{
		return $this->uri;
	}
	
	public function getPathInfo()
	{
		return $this->pathInfo;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function isGet()
	{
		return $this->method === self::GET;
	}
	
	public function isPost()
	{
		return $this->method === self::POST;
	}
	
	public function hasHeader($key)
	{
		return isset($this->headers[$key]);
	}
	
	public function getHeader($key)
	{
		if (!isset($this->headers[$key])) {
			return null;
		}
		
		return $this->headers[$key];
	}
	
	public function getHeaders()
	{
		return $this->headers;
	}
	
	public function setHeader($key, $value)
	{
		$this->headers[$key] = $value;
	}
	
	public function getParamGet($key)
	{
		if (!isset($this->getParams[$key])) {
			return null;
		}
		
		return $this->getParams[$key];
	}
	
	public function getParams()
	{
		return $this->getParams;
	}
	
	public function setParamGet($key)
	{
		$this->getParams[$key] = $value;
	}
	
	public function getParamPost($key)
	{
		if (!isset($this->postParams[$key])) {
			return null;
		}
		
		return $this->postParams[$key];
	}
	
	public function postParams()
	{
		return $this->postParams;
	}
	
	public function setParamPost($key)
	{
		$this->postParams[$key] = $value;
	}
}
