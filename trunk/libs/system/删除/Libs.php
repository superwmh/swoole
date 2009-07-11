<?php
class Libs
{
	var $resource = array();
	function __construct()
	{

	}

	public static function load($name)
	{
		require(WEBPATH.'/models/'.$name.'.php');
	}
}
?>