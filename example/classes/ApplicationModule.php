<?php

class ApplicationModule extends AbstractModule {

	public function configure() {
		$this->bind('Dispatcher')->to('RequestDispatcher')->in(Scopes::SINGLETON);
	}

	/**
	 * @Provides(Router)
	 * @Scope(SingletonScope)
	 */
	public function provideRouter() {
		$builder = Router::builder();
		$builder->createRoute('/user(/<id>)', array('controller' => 'user'))
			->where('id')
			->matches('\d+');
		$builder->createRoute('/(<controller>)', array('controller' => 'main'));
		return $builder->build();
	}

}
