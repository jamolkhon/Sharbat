<?php

class Session
{
	public function __construct()
	{
		session_start();
	}
	
	public function getNamespace($name)
	{
		$hash = hash('crc32', $name);
		
		if (!isset($_SESSION[$hash])) {
			$_SESSION[$hash] = new SessionNamespace();
		}
		
		return $_SESSION[$hash];
	}
}
