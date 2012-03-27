<?php

namespace Sharbat\Intercept;

interface MethodInterceptor extends Interceptor {
  function invoke(MethodInvocation $methodInvocation);
}
