<?php

class HttpResponse
{
	protected $headers = array();
	protected $body = '';
	
	public function __construct(array $headers, $body)
	{
		$this->headers = $headers;
		$this->body = $body;
	}
	
	public function hasHeader($name)
	{
		return isset($this->headers[$name]);
	}
	
	public function getHeader($name)
	{
		if ($this->hasHeader($name)) {
			return $this->headers[$name];
		}
		
		return null;
	}
	
	public function getHeaders()
	{
		return $this->headers;
	}
	
	public function setHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}
	
	public function getBody()
	{
		return $this->body;
	}
	
	public function setBody($body)
	{
		$this->body = $body;
	}
	
	public function send()
	{
		foreach ($this->headers as $key => $value) {
			header($key . ': ' . $value);
		}
		
		echo $this->body;
	}
}
