<?php

interface Injector {

	public function getInstance($dependency);
	
	public function createInstance($class);

	public function requestInjection($instance);

	public function requestFieldsInjection($instance);

	public function requestMethodsInjection($instance);

	public function injectIntoField($instance, $fieldName);

	public function injectIntoMethod($instance, $methodName);

}
