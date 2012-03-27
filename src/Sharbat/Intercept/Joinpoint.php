<?php

namespace Sharbat\Intercept;

interface Joinpoint {
  /**
   * @return object
   */
  function getStaticPart();

  /**
   * @return object|null
   */
  function getThis();

  /**
   * @return mixed
   */
  function proceed();
}
