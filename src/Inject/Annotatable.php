<?php

namespace Sharbat\Inject;

interface Annotatable {

  /**
   * @param string $qualifiedClassName
   * @return \Sharbat\Inject\Annotation
   */
  function getAnnotation($qualifiedClassName);

  /**
   * @return \Sharbat\Inject\Annotation[]
   */
  function getAnnotations();

}
