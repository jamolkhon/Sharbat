<?php

namespace Sharbat\Inject\Binder;

interface BindingVisitor {

  function visitLinkedBinding(LinkedBinding $binding);

  function visitInstanceBinding(InstanceBinding $binding);

  function visitProviderBinding(ProviderBinding $binding);

  function visitProviderInstanceBinding(ProviderInstanceBinding $binding);

  function visitConstantBinding(ConstantBinding $binding);

}
