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
	static function input($source,$name,$check_type=null,$options=null)
	{
		$source = strtolower($source);
		switch($source)
		{
			case 'get':
				$input_type = INPUT_GET;
				break;
			case 'post':
				$input_type = INPUT_POST;
				break;
			case 'cookie':
				$input_type = INPUT_COOKIE;
				break;
			case 'env':
				$input_type = INPUT_ENV;
				break;
			case 'SERVER':
				$input_type = INPUT_SERVER;
				break;
			case 'SESSION':
				$input_type = INPUT_SESSION;
				break;
			case 'REQUEST':
				$input_type = INPUT_REQUEST;
				break;
			default:
				$input_type = INPUT_REQUEST;
				break;
		}

		$check_type = strtolower($check_type);
		switch($check_type)
		{
			case 'int':
				$check = FILTER_VALIDATE_INT;
				break;
			case 'int':
				$check = FILTER_VALIDATE_BOOLEAN;
				break;
			case 'int':
				$check = FILTER_VALIDATE_FLOAT;
				break;
			case 'int':
				$check = FILTER_VALIDATE_REGEXP;
				break;
			case 'int':
				$check = FILTER_VALIDATE_URL;
				break;
			case 'int':
				$check = FILTER_VALIDATE_EMAIL;
				break;
			case 'int':
				$check = FILTER_VALIDATE_IP;
				break;
			default:
				$check = FILTER_DEFAULT;
				break;
		}
		return filter_input($input_type,$name,$check,$options);
	}
	static function request()
	{
		//过滤$_GET $_POST $_REQUEST请求
		Filter::filter_array($_POST);
		Filter::filter_array($_GET);
		Filter::filter_array($_REQUEST);
	}
	static function safe(&$content)
	{
		Filter::deslash($content);
		$content = html_entity_decode($content);
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