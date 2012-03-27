<?php

namespace Sharbat\Reflect;

use Sharbat\Inject\Annotatable;
use \ReflectionMethod;

class Method implements Annotatable {
  const ALL = 1799;
  const IS_STATIC = 1;
  const IS_PUBLIC = 256;
  const IS_PROTECTED = 512;
  const IS_PRIVATE = 1024;
  const IS_ABSTRACT = 2;
  const IS_FINAL = 4;
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

  public function createDefinition($methodBody) {
    return implode("\n", array($this->reflection->getDocComment(),
      $this->getHeaderDefinition() . ' {', $methodBody, '}'));
  }

  public function getHeaderDefinition() {
    $headerDeclaration = '';

    if ($this->reflection->isPublic()) {
      $headerDeclaration .= 'public';
    } else if ($this->reflection->isProtected()) {
      $headerDeclaration .= 'protected';
    } else if ($this->reflection->isPrivate()) {
      $headerDeclaration .= 'private';
    }

    if ($this->reflection->isStatic()) {
      $headerDeclaration .= ' static';
    }

    $headerDeclaration .= ' function';

    if ($this->reflection->returnsReference()) {
      $headerDeclaration .= ' &' . $this->reflection->getShortName();
    } else {
      $headerDeclaration .= ' ' . $this->reflection->getShortName();
    }

    $headerDeclaration .= $this->getParameterListDefinition(true);
    return $headerDeclaration;
  }

  public function getParameterListDefinition($withParentheses = false) {
    $parameterDeclarations = array();

    foreach ($this->parameters as $parameter) {
      $parameterDeclarations[] = $parameter->getDefinition();
    }

    $parameterListDeclaration = implode(', ', $parameterDeclarations);

    if ($withParentheses) {
      $parameterListDeclaration = '(' . $parameterListDeclaration . ')';
    }

    return $parameterListDeclaration;
  }

  /**
   * @param string $qualifiedClassName
   * @return bool
   */
  public function hasAnnotation($qualifiedClassName) {
    return $this->getFirstAnnotation($qualifiedClassName) != null;
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
    return $this->parameters;
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

  public function isNamespaced() {
    return $this->reflection->inNamespace();
  }

  public function invoke($instance) {
    return $this->invokeArgs($instance, array_slice(func_get_args(), 1));
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
