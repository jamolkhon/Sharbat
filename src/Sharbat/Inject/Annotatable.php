<?php

namespace Sharbat\Inject;

interface Annotatable {

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Annotation
   */
  function getFirstAnnotation($qualifiedClassName);

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Annotation[]
   */
  function getAnnotations($qualifiedClassName);

  /**
   * @return \Sharbat\Inject\Annotation[]
   */
  function getAllAnnotations();

}
