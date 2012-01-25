<?php

namespace Sharbat\Inject;

interface InstanceCreator {

  function createInstance($qualifiedClassName);

}
