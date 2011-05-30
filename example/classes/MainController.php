<?php

class MainController extends Controller
{
	public function handle(Request $request, Response $response)
	{
		$response->setBody('Hello dependency injection!');
		$response->send();
	}
}
