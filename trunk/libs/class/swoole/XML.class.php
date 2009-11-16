<?php
class XMLElement implements ArrayAccess
{
	var $xml_object;
	var $xml_property;
	
	function __construct($element=null)
	{
		if(!empty($element)) $this->xml_object = $element;
	}
	function offsetGet($key)
	{
		return $this->xml_property[$key];
	}
	function offsetSet($key,$value)
	{
		$this->xml_property[$key]=$value;
	}
	function offsetUnset($key)
	{
		unset($this->xml_property[$key]);
	}
	function offsetExists($key)
	{
		return isset($this->xml_property[$key]);
	}
	function __get($key)
	{
		return $this->xml_object[$key];
	}
	function __set($key,$value)
	{
		$this->xml_object[$key]=$value;
	}
}

class XML extends XMLElement
{
	var $xml;
	
	function __construct($xml_file)
	{
		//$this->xml = simplexml_load_file($xml_file);
		//$this->root = new XMLElement;
	}
}
?>