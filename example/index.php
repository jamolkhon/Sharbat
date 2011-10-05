<?php

function __autoload($class) {
	$searchDirs = array('/../src/', '/framework/', '/classes/');
	
	foreach ($searchDirs as $dir) {
		$file = dirname(__FILE__) . $dir . $class . '.php';
		
		if (is_file($file)) {
			require $file;
			return;
		}
	}
}

$injector = Sharbat::createInjector(new ApplicationModule());
$application = $injector->getInstance('Application');
$application->execute();
