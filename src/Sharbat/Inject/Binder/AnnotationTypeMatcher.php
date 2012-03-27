<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotatable;

class AnnotationTypeMatcher extends AbstractMatcher {
  private $annotationQualifiedClassName;

  public function __construct($anntationQualifiedClassName) {
    $this->annotationQualifiedClassName = $anntationQualifiedClassName;
  }

  public function matches($T, Annotatable $annotatable) {
    return $annotatable->hasAnnotation($this->annotationQualifiedClassName);
  }
}
