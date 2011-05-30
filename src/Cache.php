<?php

interface Cache
{
	public function hasKey($key);
	public function get($key);
	public function set($key, $value);
}
