<?php 
class SwooleObject
{
	var $_property=array();
	var $_method;
	
	function __set($keyname,$value)
	{
		$this->_property[$keyname] = $value;
	}
	
	function __get($keyname)
	{
		if(array_key_exists($keyname,$this->_property))
			return $this->_property[$keyname];
		return false;
	}
}
?>