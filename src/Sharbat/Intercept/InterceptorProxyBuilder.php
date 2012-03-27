<?php

namespace Sharbat\Intercept;

use Sharbat\Reflect\ReflectionService;
use Sharbat\Inject\Bindings;
use Sharbat\Reflect\Clazz;
use Sharbat\Reflect\Field;
use Sharbat\Reflect\Method;

class InterceptorProxyBuilder {
  const PROXY_SUFFIX = 'ProxyBySharbat';
  const INTERCEPTOR_CHAINS_FIELD_NAME = '___interceptorChainsBySharbat___';

  private $reflectionService;
  private $bindings;

  public function __construct(ReflectionService $reflectionService,
      Bindings $bindings) {
    $this->reflectionService = $reflectionService;
    $this->bindings = $bindings;
  }

  public function createProxyOrReturn($object) {
    return $this->createProxy($object) ?: $object;
  }

  public function createProxy($object) {
    $methodNameToInterceptorsMapping = $this->bindings->getInterceptors($object);

    if (empty($methodNameToInterceptorsMapping)) {
      return null;
    }

    $class = $this->reflectionService->getClass(get_class($object));
    $interceptableMethodNames = array_keys($methodNameToInterceptorsMapping);
    $proxyClass = $this->getProxyClass($class, $interceptableMethodNames);

    if ($proxyClass === null) {
      return null;
    }

    $methodNameToInterceptorChainMapping = array();

    foreach ($methodNameToInterceptorsMapping as $methodName => $interceptors) {
      $methodNameToInterceptorChainMapping[$methodName] = new InterceptorChain(
        $class->getMethod($methodName), $interceptors);
    }

    $proxyObject = $this->reflectionService->castObject($object,
      $proxyClass->getQualifiedName());
    $interceptorChainsField = $proxyClass->getField(
      self::INTERCEPTOR_CHAINS_FIELD_NAME);
    $interceptorChainsField->setValue($proxyObject,
      $methodNameToInterceptorChainMapping);
    return $proxyObject;
  }

  public function getProxyClass(Clazz $class, array $interceptableMethodNames) {
    $proxyClassName = $class->getUnqualifiedName() . self::PROXY_SUFFIX;

    if (class_exists($proxyClassName, false)) {
      return $this->reflectionService->getClass($proxyClassName);
    }

    $methodDefinitions = array();

    foreach ($class->getMethods() as $method) {
      if ($method->isConstructor() || $method->isDestructor()) {
        continue;
      }

      if (in_array($method->getUnqualifiedName(), $interceptableMethodNames)) {
        $methodDefinitions[] = $this->createInterceptedMethodDefinition($method);
      }
    }

    $classDefinitionFormat = $class->getDocComment() . "\n" . '
      class %1$s extends %2$s {
        private $%3$s = array();
        %4$s
      }
    ';

    $classDefinition = sprintf($classDefinitionFormat, $proxyClassName,
      $class->getQualifiedName(), self::INTERCEPTOR_CHAINS_FIELD_NAME,
      implode("\n", $methodDefinitions));
    eval($classDefinition);
    return $this->reflectionService->getClass($proxyClassName);
  }

  private function createInterceptedMethodDefinition(Method $method) {
    $methodBody = sprintf(
      'return $this->%s[__FUNCTION__]->apply($this, func_get_args());',
      self::INTERCEPTOR_CHAINS_FIELD_NAME);
    return $method->createDefinition($methodBody);
  }
}
