<?php
class Object
{
	static $php;
	
	function __construct($php='')
	{
		if($php=='') global $php;
		self::$php = $php;
	}

	function __call($method,$params)
	{
		return call_user_method_array($method,$this->php,$params);
	}
	
	function __get($keyname)
	{
		return $this->php->$keyname;
	}
	
}
?>