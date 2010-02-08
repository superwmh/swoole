<?php
/**
 * 过滤类
 * 用于过滤数组或者变量中的不安全字符，以及HTML标签
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage request_filter
 *
 */
class Filter
{
	static function request()
	{
		//过滤$_GET $_POST $_REQUEST请求
		Filter::filter_array($_POST);
		Filter::filter_array($_GET);
		Filter::filter_array($_REQUEST);
	}
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