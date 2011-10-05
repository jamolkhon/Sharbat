<?php

class AnnotationInfo {

	private $annotations = array();
	
	public function __construct(array $annotations) {
		$this->annotations = $annotations;
	}
	
	public function hasAnnotation($name) {
		return isset($this->annotations[$name]);
	}
	
	public function isInjectable() {
		return $this->hasAnnotation('Inject');
	}
	
	public function getAnnotation($annotation) {
		if (!isset($this->annotations[$annotation])) {
			return null;
		}
		
		return $this->annotations[$annotation];
	}
	
	public function getAnnotationArgument($annotation, $argument=0) {
		if (!isset($this->annotations[$annotation])) {
			return null;
		}
		
		return $this->annotations[$annotation]->getArgument($argument);
	}

	public function getProvidesClass() {
		return $this->getAnnotationArgument('Provides');
	}

	public function getInjectClass() {
		return $this->getAnnotationArgument('Inject');
	}

	public function getScope($defaultScope=Scopes::NO_SCOPE) {
		$scope = $this->getAnnotationArgument('Scope');
		return $scope === null ? $defaultScope : $scope;
	}

}
