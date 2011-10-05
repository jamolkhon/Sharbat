<?php

class AnnotationUtils {

	private $annotationParser;
	private $reflectionDao;
	
	public function __construct(AnnotationParser $annotationParser,
			ReflectionDao $reflectionDao) {
		$this->annotationParser = $annotationParser;
		$this->reflectionDao = $reflectionDao;
	}
	
	public function getAnnotationInfo($for) {
		return new AnnotationInfo($this->annotationParser->getAnnotationsAssoc(
				$for));
	}
	
	public function getAnnotationInfoForClass($class) {
		$reflection = $this->reflectionDao->get($class);
		return $this->getAnnotationInfo($reflection->getDocComment());
	}

}
