<?php

class BindingValidator {

	public function validateAll(array $bindings) {
		foreach ($bindings as $binding) {
			$this->validate($binding);
		}
		
		return true;
	}
	
	public function validate(Binding $binding) {
		if ($binding instanceof LinkedBinding) {
			$this->ensureValidInterfaceOrClass($binding->getSource());
			
			if ($binding instanceof ProviderBinding) {
				$this->ensureValidImplementationOrSubclass(
						$binding->getTarget(), 'Provider');
			} else {
				$this->ensureValidImplementationOrSubclass(
						$binding->getSource(), $binding->getTarget());
			}
		}
		
		if ($binding instanceof InstanceBinding) {
			if ($binding instanceof ProviderInstanceBinding) {
				$this->ensureValidInterfaceOrClass($binding->getSource());
				$this->ensureValidInstance($binding->getTarget(), 'Provider');
			} elseif (!($binding instanceof ConstantBinding)) {
				$this->ensureValidInterfaceOrClass($binding->getSource());
				$this->ensureValidInstance($binding->getTarget(),
						$binding->getSource());
			}
		}
		
		$this->ensureValidScope($binding->getScope());
		return true;
	}
	
	private function ensureValidInterfaceOrClass($class) {
		if (!is_string($class) || (!interface_exists($class) &&
				!class_exists($class))) {
			throw new Exception('Invalid interface/class: ' . $class);
		}
		
		return true;
	}
	
	private function ensureValidImplementationOrSubclass($source, $target) {
		if ((!class_exists($source) || !is_subclass_of($target, $source)) &&
				!in_array($source, class_implements($target))) {
			throw new Exception($target . ' must extend/implement ' . $source);
		}
		
		return true;
	}
	
	private function ensureValidInstance($instance, $class) {
		if (!is_object($instance)) {
			throw new Exception('Binding target must be an object');
		}
		
		if (!($instance instanceof $class)) {
			throw new Exception('Instance must extend/implement ' . $class);
		}
		
		return true;
	}
	
	private function ensureValidScope($scope) {
		if ($scope !== Scopes::NO_SCOPE && (
				!$this->ensureValidInterfaceOrClass($scope) ||
				!$this->ensureValidImplementationOrSubclass('Scope', $scope))) {
			throw new Exception('Invalid scope: ' . $scope);
		}
		
		return true;
	}

}
