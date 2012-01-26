<?php

namespace Sharbat\Inject\Binder;

use Sharbat\Reflect\ReflectionService;

interface ScopedBindingBuilder {

  function in($qualifiedClassName);

  function inSingleton();

  function inNoScope();

}
