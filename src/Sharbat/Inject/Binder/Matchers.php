<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotation;

final class Matchers {
  private function __construct() {
  }

  public static function annotatedWithType($annotationQualifiedClassName) {
    return new AnnotationTypeMatcher($annotationQualifiedClassName);
  }

  public static function annotatedWith(Annotation $annotation) {
    return new AnnotationMatcher($annotation);
  }

  public static function any() {
    return new AnyMatcher();
  }

  public static function identicalTo($object) {
    return new IdentityMatcher($object);
  }

  public static function inNamespace($qualifiedNamespaceName) {
    return new NamespaceMatcher($qualifiedNamespaceName);
  }

  public static function inSubNamespace($qualifiedNamespaceName) {
    return new SubNamespaceMatcher($qualifiedNamespaceName);
  }

  public static function not(Matcher $matcher) {
    return new InvertedMatcher($matcher);
  }

  public static function only($object) {
    return new EqualsMatcher($object);
  }

  public static function subclassesOf($qualifiedClassName) {
    return new SubclassMatcher($qualifiedClassName);
  }
}
