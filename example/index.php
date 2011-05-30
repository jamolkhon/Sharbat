<?php

$startTime = microtime(true);

require_once '../src/Sharbat.php';

function __autoload($class)
{
	if (Sharbat::autoload($class)) {
		return;
	}
	
	$basedir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	$framework = $basedir . 'framework' . DIRECTORY_SEPARATOR;
	$classes = $basedir . 'classes' . DIRECTORY_SEPARATOR;
	$file = $class . '.php';
	
	if (is_file($framework . $file)) {
		require_once $framework . $file;
	} elseif (is_file($classes . $file)) {
		require_once $classes . $file;
	}
}

$injector = Sharbat::createInjector(new ApplicationModule());
$application = $injector->getInstance('Application');
$application->execute();

echo '<pre>', microtime(true) - $startTime, '</pre>';
