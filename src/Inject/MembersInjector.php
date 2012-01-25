<?php

namespace Sharbat\Inject;

interface MembersInjector {

  function injectMembers($instance);

  function injectFields($instance);

  function injectMethods($instance);

}
