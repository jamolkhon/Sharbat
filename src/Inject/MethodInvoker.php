<?php

namespace Sharbat\Inject;

interface MethodInvoker {

  function invokeMethod($instance, $methodName);

}
