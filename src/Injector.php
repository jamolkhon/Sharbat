<?php

interface Injector
{
	public function getInstance($target);
	public function getInstanceOfClass($class);
}
