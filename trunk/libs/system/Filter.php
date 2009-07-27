<?php
class Filter
{
	public static function filter_var($var,$type)
	{
		switch($type)
		{
			case 'int':
				return intval($var);
			case 'string':
				return htmlspecialchars(strval($var),ENT_QUOTES);
			case 'float':
				return floatval($var);
			default:
				return false;
		}
	}
	
	public static function filter_array(&$array,$allow_html=false)
	{
		foreach($array as &$string)
		{
			if($allow_html===false)
			{
				if(is_array($string))
				{
					self::filter_array($string);
				}
				else $string = htmlspecialchars($string,ENT_QUOTES);
			}
			if(is_array($string))
			{
				self::filter_array($string);
			}
			else
				self::addslash($string);
		}
	}
	
	public static function addslash(&$string)
	{
		if(!get_magic_quotes_gpc())
			$string = addslashes($string);
	}
	
	public static function deslash(&$string)
	{
		$string = stripslashes($string);
	}
}
?>