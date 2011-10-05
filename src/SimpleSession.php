<?php

class SimpleSession implements Session {

	public function __construct() {
		session_start();
	}

	public function get($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		
		return null;
	}
	
	public function set($key, $value) {
		$_SESSION[$key] = $value;
	}

}
