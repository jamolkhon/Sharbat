<?php

class ReflectionInjector implements Injector {

	private $binder;
	private $reflectionDao;
	private $annotationUtils;

	public function __construct(Binder $binder, ReflectionDao $reflectionDao,
			AnnotationUtils $annotationUtils) {
		$this->binder = $binder;
		$this->reflectionDao = $reflectionDao;
		$this->annotationUtils = $annotationUtils;
	}

	public function getInstance($dependency) {
		$binding = $this->binder->getBinding($dependency);

		if ($binding->getScope() === Scopes::NO_SCOPE) {
			return $binding->getInstance();
		}

		$scope = $this->getInstance($binding->getScope());
		return $scope->getInstance($binding);
	}
	
	public function createInstance($class) {
		$instance = $this->instantiate($class);
		$this->requestInjection($instance);
		return $instance;
	}

	public function requestInjection($instance) {
		$this->requestFieldsInjection($instance);
		$this->requestMethodsInjection($instance);
	}
	
	private function instantiate($class) {
		$reflection = $this->reflectionDao->get($class);
		$constructor = $reflection->getConstructor();
		
		if ($constructor) {
			return $reflection->newInstanceArgs($this->getDependencies(
					$constructor));
		}

		return $reflection->newInstance();
	}
	
	public function requestFieldsInjection($instance) {
		$class = $this->reflectionDao->get($instance);
		$fields = $class->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($fields as $field) {
			$annotationInfo = $this->annotationUtils->getAnnotationInfo(
					$field->getDocComment());

			if ($annotationInfo->isInjectable()) {
				$field->setValue($instance, $this->getInstance(
						$annotationInfo->getInjectClass()));
			}
		}
	}

	public function requestMethodsInjection($instance) {
		$class = $this->reflectionDao->get($instance);
		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			$annotationInfo = $this->annotationUtils->getAnnotationInfo(
					$method->getDocComment());

			if (!$method->isConstructor() && $annotationInfo->isInjectable()) {
				$method->invokeArgs($instance, $this->getDependencies($method));
			}
		}
	}

	public function injectIntoField($instance, $fieldName) {
		$class = $this->reflectionDao->get($instance);
		$field = $class->getProperty($fieldName);
		$annotationInfo = $this->annotationUtils->getAnnotationInfo(
				$field->getDocComment());
		$field->setValue($instance, $this->getInstance(
				$annotationInfo->getInjectClass()));
	}

	public function injectIntoMethod($instance, $methodName) {
		$reflection = $this->reflectionDao->get($instance);
		$method = $reflection->getMethod($methodName);
		return $method->invokeArgs($instance, $this->getDependencies($method));
	}

	private function getDependencies(ReflectionMethod $method) {
		$dependencies = array();
		
		foreach ($method->getParameters() as $parameter) {
			$dependencies[] = $this->getDependency($parameter);
		}
		
		return $dependencies;
	}
	
	private function getDependency(ReflectionParameter $parameter) {
		$class = $parameter->getClass();
		
		if ($class) {
			return $this->getInstance($class->getName());
		}
		
		return $this->getInstance($parameter->getName());
	}

}
