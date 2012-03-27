<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Inject\Annotation;
use Sharbat\Inject\Annotatable;

class AnnotationMatcher extends AbstractMatcher {
  private $annotation;

  public function __construct(Annotation $annotation) {
    $this->annotation = $annotation;
  }

  public function matches($T, Annotatable $annotatable) {
    $sameClassAnnotations = $annotatable->getAnnotations(get_class(
      $this->annotation));

    foreach ($sameClassAnnotations as $annotation) {
      if ($annotation == $this->annotation) {
        return true;
      }
    }

    return false;
  }
}
