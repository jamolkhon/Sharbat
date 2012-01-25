<?php

namespace Sharbat\Inject;

use Sharbat\Inject\Binder\Binding;

interface Scope {

  function getInstance(Binding $binding);

}
