<?php

namespace Sharbat\Inject\Binder;

interface ScopedBindingBuilder {

  function in($qualifiedClassName);

  function inSingleton();

  function inNoScope();

}
