<?php

namespace Sharbat\Inject;

use Sharbat\Reflect\ReflectionService;
use Sharbat\Inject\Binder\LinkedBindingBuilder;
use Sharbat\Inject\Binder\Binding;
use Sharbat\Inject\Binder\SourceAlreadyBoundException;
use Sharbat\Inject\Binder\ConstantBinding;
use Sharbat\Inject\Binder\Matcher;
use Sharbat\Intercept\Interceptor;
use Sharbat\Inject\Binder\InterceptorBinding;
use Sharbat\Inject\Binder\InvalidBindingException;
use Sharbat\Reflect\Method;
use Sharbat\Reflect\Clazz;

class DefaultBinder implements Binder, Bindings {
  private $reflectionService;
  private $linkedBindingBuilder;
  private $membersInjector;
  private $providesProvider;

  /**
   * @var \Sharbat\Inject\AbstractModule[]
   */
  private $modules = array();

  /**
   * @var \Sharbat\Inject\Binder\Binding[]
   */
  private $bindings = array();

  /**
   * @var \Sharbat\Inject\Binder\InterceptorBinding[]
   */
  private $interceptorBindings = array();
  private $instancesToBeMemberInjected = array();

  public function __construct(ReflectionService $reflectionService,
      MembersInjector $membersInjector, ProvidesProvider $providesProvider) {
    $this->reflectionService = $reflectionService;
    $this->linkedBindingBuilder = new LinkedBindingBuilder($reflectionService,
      $this);
    $this->membersInjector = $membersInjector;
    $this->providesProvider = $providesProvider;
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Binder\LinkedBindingBuilder
   */
  public function bind($qualifiedClassName) {
    return $this->linkedBindingBuilder->bind($qualifiedClassName);
  }

  /**
   * @param string $constant
   * @return \Sharbat\Inject\Binder\ConstantBinding
   */
  public function bindConstant($constant) {
    $binding = new ConstantBinding($constant);
    $this->addBinding($binding);
    return $binding;
  }

  public function bindInterceptor(Matcher $classMatcher, Matcher $methodMatcher,
      Interceptor $interceptor) {
    $interceptors = array($interceptor);

    if (func_num_args() > 3) {
      $interceptors = array_slice(func_get_args(), 2);
    }

    foreach ($interceptors as $interceptor) {
      if (!($interceptor instanceof Interceptor)) {
        throw new InvalidBindingException('Received non-interceptor argument');
      }
    }

    $this->interceptorBindings[] = new InterceptorBinding($classMatcher,
      $methodMatcher, $interceptors);
  }

  public function install(AbstractModule $module) {
    $module->setBinder($this);
    $module->configure();
    $this->modules[] = $module;
  }

  public function build() {
    foreach ($this->modules as $module) {
      $moduleClass = $this->reflectionService->getClass(get_class($module));
      $nonStaticMethodsFilter = Method::ALL & ~Method::IS_STATIC;
      $providesMethods = $moduleClass->getMethodsWithAnnotation(
        Annotations::PROVIDES, $nonStaticMethodsFilter);

      foreach ($providesMethods as $method) {
        $this->bindAsProvider($method, $module);
      }
    }

    foreach ($this->instancesToBeMemberInjected as $instance) {
      $this->membersInjector->injectTo($instance);
    }

    unset($this->instancesToBeMemberInjected);
    return $this;
  }

  private function bindAsProvider(Method $method, AbstractModule $module) {
    /** @var \Sharbat\Provides $providesAnnotation */
    $providesAnnotation = $method->getFirstAnnotation(Annotations::PROVIDES);

    if (isset($this->bindings[$providesAnnotation->getDependencyName()])) {
      return;
    }

    $providesProvider = $this->providesProvider->createProviderFor($module,
      $method);
    $scopedBindingBuilder = $this->bind($providesAnnotation->getDependencyName())
        ->toProviderInstance($providesProvider);
    /** @var \Sharbat\Scope $scopeAnnotation */
    $scopeAnnotation = $method->getFirstAnnotation(Annotations::SCOPE);

    if ($scopeAnnotation != null) {
      $scopedBindingBuilder->in($scopeAnnotation->getScopeClassName());
    }
  }

  public function requestInjection($instance) {
    $this->instancesToBeMemberInjected[] = $instance;
  }

  public function addBinding(Binding $binding) {
    $key = $binding->getKey();

    if (isset($this->bindings[$key])) {
      throw new SourceAlreadyBoundException($key . ' is already bound');
    }

    $this->bindings[$key] = $binding;
  }

  public function getInterceptors($object) {
    $interceptors = array();
    $class = $this->reflectionService->getClass(get_class($object));
    $nonFinalNonPrivateNonStaticMethodsFilter = Method::ALL & ~Method::IS_FINAL &
        ~Method::IS_PRIVATE & ~Method::IS_STATIC;

    foreach ($this->interceptorBindings as $binding) {
      if ($binding->getClassMatcher()->matches($object, $class)) {
        foreach ($class->getMethods($nonFinalNonPrivateNonStaticMethodsFilter) as $method) {
          if ($binding->getMethodMatcher()->matches($object, $method)) {
            $interceptors += array($method->getUnqualifiedName() => array());
            $interceptors[$method->getUnqualifiedName()] =
                array_merge($interceptors[$method->getUnqualifiedName()],
                  $binding->getInterceptors());
          }
        }
      }
    }

    return $interceptors;
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\Binder\Binding
   */
  public function getBinding(Clazz $class) {
    $key = $class->getQualifiedName();
    return isset($this->bindings[$key]) ? $this->bindings[$key] : null;
  }

  /**
   * @param string $constant
   * @return \Sharbat\Inject\Binder\ConstantBinding
   */
  public function getConstantBinding($constant) {
    return isset($this->bindings[$constant]) ? $this->bindings[$constant] : null;
  }

  /**
   * @param \Sharbat\Reflect\Clazz $class
   * @return \Sharbat\Inject\Binder\Binding
   */
  public function getOrCreateBinding(Clazz $class) {
    $key = $class->getQualifiedName();

    if (!isset($this->bindings[$key])) {
      // Just-in-time binding
      $this->bind($key);
    }

    return $this->bindings[$key];
  }

  /**
   * @return \Sharbat\Inject\Binder\Binding[]
   */
  public function getBindings() {
    return $this->bindings;
  }

  public function removeBinding($key) {
    unset($this->bindings[$key]);
  }
}
