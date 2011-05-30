<?php

class InstanceBinding extends ScopedBinding
{
	protected function getInstanceInternal()
	{
		return $this->instance;
	}
}
