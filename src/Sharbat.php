<?php

class SharbatException extends Exception {}

abstract class Sharbat
{
	public static function autoload($class)
	{
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php';
		
		if (is_file($file)) {
			require_once $file;
			return true;
		}
		
		return false;
	}
	
	public static function createInjector()
	{
		$binder = new Binder();
		$modules = func_get_args();
		
		foreach ($modules as $module) {
			if (is_object($module) && $module instanceof Module) {
				$module->configure($binder);
			} else {
				throw new SharbatException('Received non-module argument');
			}
		}
		
		$session = new Session();
		$sessionNamespace = $session->getNamespace('Sharbat');
		
		$injector = new ReflectionInjector($binder, new SimpleCache(),
			new AnnotationParser(), $sessionNamespace);
		
		if (!$binder->lookUp('Injector')) {
			$binder->bind('Injector')->toInstance($injector);
		}
		
		$binder->build($injector, $sessionNamespace);
		return $injector;
	}
}
