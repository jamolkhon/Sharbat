<?php

abstract class Controller
{
	protected $request;
	
	abstract public function handle(Request $request, Response $response);
}
