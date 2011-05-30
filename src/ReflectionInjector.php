<?php

class ReflectionInjectorException extends Exception {}

/**
 * Reflection based injector
 */
class ReflectionInjector implements Injector
{
	protected $binder;
	protected $reflectionCache;
	protected $annotationParser;
	protected $sessionNamespace;
	protected $singletons = array();
	
	public function __construct(Binder $binder, Cache $reflectionCache,
		AnnotationParser $annotationParser, SessionNamespace $sessionNamespace)
	{
		$this->binder = $binder;
		$this->reflectionCache = $reflectionCache;
		$this->annotationParser = $annotationParser;
		$this->sessionNamespace = $sessionNamespace;
	}
	
	public function getInstance($target)
	{
		$binding = $this->binder->lookUp($target);
		
		if ($binding) {
			return $binding->getInstance();
		}
		
		return $this->getInstanceOfClass($target);
	}
	
	public function getInstanceOfClass($class)
	{
		$reflectionClass = $this->getReflectionClass($class);
		
		if (!$reflectionClass) {
			throw new ReflectionInjectorException('No such class ' . $class);
		}
		
		if (!$reflectionClass->isInstantiable()) {
			throw new ReflectionInjectorException($class .
				' cannot be instantiated');
		}
		
		return $this->getScopedInstance($reflectionClass);
	}
	
	protected function getReflectionClass($class)
	{
		if ($this->reflectionCache->has($class)) {
			return $this->reflectionCache->get($class);
		}
		
		try {
			$reflectionClass = new ReflectionClass($class);
			$this->reflectionCache->set($class, $reflectionClass);
			return $reflectionClass;
		} catch (ReflectionException $exception) {
			return null;
		}
	}
	
	protected function getScopedInstance(ReflectionClass $reflectionClass)
	{
		$key = new Key($reflectionClass->getName());
		$hash = $key->getHash();
		
		$isSingleton = $this->hasAnnotation($reflectionClass, 'Singleton');
		$isSessionScoped = $this->hasAnnotation($reflectionClass,
			'SessionScoped');
		
		if ($isSingleton && isset($this->singletons[$hash])) {
			return $this->singletons[$hash];
		} elseif ($isSessionScoped && $this->sessionNamespace->has($hash)) {
			return $this->sessionNamespace->get($hash);
		}
		
		$instance = $this->createInstance($reflectionClass);
		
		if ($isSingleton) {
			$this->singletons[$hash] = $instance;
		} elseif ($isSessionScoped) {
			$this->sessionNamespace->set($hash, $instance);
		}
		
		return $instance;
	}
	
	protected function hasAnnotation($reflection, $annotation)
	{
		return $this->annotationParser->hasAnnotation(
			$reflection->getDocComment(), $annotation);
	}
	
	protected function createInstance(ReflectionClass $reflectionClass)
	{
		$instance = $this->constructorInject($reflectionClass);
		$publicMethods =
			$reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
		
		foreach ($publicMethods as $reflectionMethod) {
			if ($this->hasAnnotation($reflectionMethod, 'Inject') &&
				!$reflectionMethod->isConstructor()) {
				$this->methodInject($instance, $reflectionMethod);
			}
		}
		
		return $instance;
	}
	
	protected function constructorInject(ReflectionClass $reflectionClass)
	{
		$constructor = $reflectionClass->getConstructor();
		
		if ($constructor) {
			if ($constructor->getNumberOfRequiredParameters() &&
				!$this->hasAnnotation($constructor, 'Inject')) {
				throw new ReflectionInjectorException('Cannot instantiate ' .
					$reflectionClass->getName() .
					' because its constructor is not injectable');
			}
			
			$dependencies = $this->getDependencies($constructor);
			return $reflectionClass->newInstanceArgs($dependencies);
		}
		
		return $reflectionClass->newInstance();
	}
	
	protected function methodInject($instance,
		ReflectionMethod $reflectionMethod)
	{
		$dependencies = $this->getDependencies($reflectionMethod);
		return $reflectionMethod->invokeArgs($instance, $dependencies);
	}
	
	protected function getDependencies(ReflectionMethod $reflectionMethod)
	{
		$dependencies = array();
		
		foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
			$dependencies[] =
				$this->getDependency($reflectionParameter);
		}
		
		return $dependencies;
	}
	
	protected function getDependency(ReflectionParameter $reflectionParameter)
	{
		if ($reflectionClass = $reflectionParameter->getClass()) {
			return $this->getInstance($reflectionClass->getName());
		}
		
		$binding = $this->binder->lookUpConstant(
			$reflectionParameter->getName());
		
		if ($binding !== null) {
			return $binding->getInstance();
		}
		
		if ($reflectionParameter->isDefaultValueAvailable()) {
			return $reflectionParameter->getDefaultValue();
		}
		
		throw new ReflectionInjectorException('No binding for param ' .
			$reflectionParameter->getName() . ' in class ' .
			$reflectionParameter->getDeclaringClass()->getName());
	}
}
