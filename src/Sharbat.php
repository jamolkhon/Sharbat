<?php

class Sharbat {

	private $binder;
	private $reflectionDao;
	private $annotationUtils;
	
	public function __construct(Binder $binder, ReflectionDao $reflectionDao,
			AnnotationUtils $annotationUtils) {
		$this->binder = $binder;
		$this->reflectionDao = $reflectionDao;
		$this->annotationUtils = $annotationUtils;
	}

	public static function createInjector() {
		// Building object graph of Sharbat
		$reflectionDao = new ReflectionDao();
		$annotationUtils = new AnnotationUtils(new AnnotationParser(),
				$reflectionDao);
		$binder = new Binder($annotationUtils, new BindingValidator());
		$sharbat = new Sharbat($binder, $reflectionDao, $annotationUtils);
		return $sharbat->getInjector(func_get_args());
	}
	
	private function getInjector(array $modules) {
		$injector = new ReflectionInjector($this->binder, $this->reflectionDao,
			$this->annotationUtils);
		$this->binder->setInjector($injector);
		
		// Built-in scopes
		$this->binder->bind('SingletonScope')->toInstance(
				$injector->getInstance('SingletonScope'));
		$this->binder->bind('Session')->to('SimpleSession')
				->in(Scopes::SINGLETON);
		
		// Built-in bindings
		$this->binder->bind('Injector')->toInstance($injector);
		$this->binder->bind('Binder')->toInstance($this->binder);
		$this->binder->bind('ReflectionDao')->toInstance($this->reflectionDao);
		$this->binder->bind('AnnotationUtils')->toInstance($this->annotationUtils);

		$this->configure($modules);
		$this->bindProvidesProviders($modules, $injector);
		$this->binder->build();
		return $injector;
	}
	
	private function configure(array $modules) {
		foreach ($modules as $module) {
			if (is_object($module) && $module instanceof AbstractModule) {
				$module->setBinder($this->binder);
				$module->configure();
			} else {
				throw new Exception('Received a non-module argument.');
			}
		}
	}
	
	private function bindProvidesProviders(array $modules, Injector $injector) {
		foreach ($modules as $module) {
			$this->bindProvidesProviders($module->getInstalledModules(),
					$injector);
			$reflection = $this->reflectionDao->get($module);
			$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
			
			foreach ($methods as $method) {
				$this->bindMethodAsProvider($module, $method, $injector);
			}
		}
	}

	private function bindMethodAsProvider($module, ReflectionMethod $method,
			Injector $injector) {
		$methodAnnotationInfo = $this->annotationUtils->getAnnotationInfo(
				$method->getDocComment());
		$providesClass = $methodAnnotationInfo->getProvidesClass();

		if ($providesClass && !$this->binder->isBound($providesClass)) {
			$providesProvider = new ProvidesProvider($injector, $module,
					$method->getName());
			$classAnnotationInfo = $this->annotationUtils
					->getAnnotationInfoForClass($providesClass);
			$classScope = $classAnnotationInfo->getScope(Scopes::NO_SCOPE);
			$this->binder->bind($providesClass)
					->toProviderInstance($providesProvider)
					->in($methodAnnotationInfo->getScope($classScope));
		}
	}

}
