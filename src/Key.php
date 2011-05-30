<?php

class Key
{
	const TYPE_INTERFACE = 1;
	const TYPE_CLASS = 2;
	const TYPE_CONSTANT = 3;
	
	protected $name;
	protected $type;
	protected $hash;
	
	public function __construct($name, $type=null)
	{
		if ($type === null) {
			$type = $this->detectType($name);
		}
		
		$this->name = $name;
		$this->type = $type;
		$this->hash = hash('crc32', $type . $name);
	}
	
	public static function detectType($name)
	{
		if (interface_exists($name)) {
			return self::TYPE_INTERFACE;
		} elseif(class_exists($name)) {
			return self::TYPE_CLASS;
		} else {
			return self::TYPE_CONSTANT;
		}
	}
	
	public static function fromConstant($name)
	{
		return new self($name, self::TYPE_CONSTANT);
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getHash()
	{
		return $this->hash;
	}
	
	public function isInterface()
	{
		return $this->type === self::TYPE_INTERFACE;
	}
	
	public function isClass()
	{
		return $this->type === self::TYPE_CLASS;
	}
	
	public function isConstant()
	{
		return $this->type === self::TYPE_CONSTANT;
	}
}
