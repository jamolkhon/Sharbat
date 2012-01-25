<?php

namespace Sharbat\Inject;

interface Injector {

  function getInstance($qualifiedClassName);

  function getConstant($constant);

  function createInstance($qualifiedClassName);

}
