<?php

namespace Sharbat\Reflect;

use Sharbat\Inject\Annotatable;
use \ReflectionMethod;

class Method implements Annotatable {
  /**
   * @var \ReflectionMethod
   */
  private $reflection;

  /**
   * @var \Sharbat\Reflect\Parameter[]
   */
  private $parameters = array();

  /**
   * @var \Sharbat\Inject\Annotation[]
   */
  private $annotations = array();

  /**
   * @var \Sharbat\Reflect\Clazz
   */
  private $declaringClass;

  public function __construct(ReflectionMethod $reflection, array $annotations,
      Clazz $declaringClass) {
    $this->reflection = $reflection;
    $this->annotations = $annotations;
    $this->declaringClass = $declaringClass;
  }

  /**
   * @return \ReflectionMethod
   */
  public function getInternalReflection() {
    return $this->reflection;
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Annotation
   */
  public function getFirstAnnotation($qualifiedClassName) {
    foreach ($this->annotations as $annotation) {
      if ($annotation instanceof $qualifiedClassName) {
        return $annotation;
      }
    }

    return null;
  }

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Annotation[]
   */
  public function getAnnotations($qualifiedClassName) {
    $annotations = array();

    foreach ($this->annotations as $annotation) {
      if ($annotation instanceof $qualifiedClassName) {
        $annotations[] = $annotation;
      }
    }

    return $annotations;
  }

  /**
   * @return \Sharbat\Inject\Annotation[]
   */
  public function getAllAnnotations() {
    return $this->annotations;
  }

  /**
   * @return \Sharbat\Reflect\Clazz
   */
  public function getDeclaringClass() {
    return $this->declaringClass;
  }

  public function getDocComment() {
    return $this->reflection->getDocComment();
  }

  public function getEndLine() {
    return $this->reflection->getEndLine();
  }

  /**
   * @return \ReflectionExtension
   */
  public function getExtension() {
    return $this->reflection->getExtension();
  }

  public function getExtensionName() {
    return $this->reflection->getExtensionName();
  }

  public function getFileName() {
    return $this->reflection->getFileName();
  }

  public function getModifiers() {
    return $this->reflection->getModifiers();
  }

  public function getQualifiedName() {
    return $this->reflection->getName();
  }

  public function getNamespaceName() {
    return $this->reflection->getNamespaceName();
  }

  public function getNumberOfParameters() {
    return $this->reflection->getNumberOfParameters();
  }

  public function getNumberOfRequiredParameters() {
    return $this->reflection->getNumberOfRequiredParameters();
  }

  /**
   * @return \Sharbat\Reflect\Parameter[]
   */
  public function getParameters() {
    $parameters = array();

    foreach ($this->reflection->getParameters() as $parameter) {
      $parameters[] = new Parameter($parameter, $this);
    }

    return $parameters;
  }

  public function getUnqualifiedName() {
    return $this->reflection->getShortName();
  }

  public function getPrototype() {
    return $this->reflection->getPrototype();
  }

  public function getStartLine() {
    return $this->reflection->getStartLine();
  }

  public function getStaticVariables() {
    return $this->reflection->getStaticVariables();
  }

  public function inNamespace() {
    return $this->reflection->inNamespace();
  }

  public function invoke($instance) {
    if (!$this->reflection->isPublic()) {
      $this->reflection->setAccessible(true);
    }

    return call_user_func_array(array($this->reflection, 'invoke'),
      func_get_args());
  }

  public function invokeArgs($instance, array $arguments) {
    if (!$this->reflection->isPublic()) {
      $this->reflection->setAccessible(true);
    }

    return $this->reflection->invokeArgs($instance, $arguments);
  }

  public function isAbstract() {
    return $this->reflection->isAbstract();
  }

  public function isClosure() {
    return $this->reflection->isClosure();
  }

  public function isConstructor() {
    return $this->reflection->isConstructor();
  }

  public function isDeprecated() {
    return $this->reflection->isDeprecated();
  }

  public function isDestructor() {
    return $this->reflection->isDestructor();
  }

  public function isFinal() {
    return $this->reflection->isFinal();
  }

  public function isInternal() {
    return $this->reflection->isInternal();
  }

  public function isPrivate() {
    return $this->reflection->isPrivate();
  }

  public function isProtected() {
    return $this->reflection->isProtected();
  }

  public function isPublic() {
    return $this->reflection->isPublic();
  }

  public function isStatic() {
    return $this->reflection->isStatic();
  }

  public function isUserDefined() {
    return $this->reflection->isUserDefined();
  }

  public function returnsReference() {
    return $this->reflection->returnsReference();
  }
}
