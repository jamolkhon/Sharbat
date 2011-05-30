<?php

class ApplicationModule implements Module
{
	public function configure(Binder $binder)
	{
		$binder->bind('Dispatcher')->to('RequestDispatcher');
		
		$builder = Router::builder();
		$builder->createRoute('/user(/<id>)', array('controller' => 'user'))
			->where('id')
			->matches('\d+');
		$builder->createRoute('/(<controller>)', array('controller' => 'main'));
		$router = $builder->build();
		
		$binder->bind('Router')->toInstance($router);
	}
}
