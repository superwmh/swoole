<?php
/**
 * 过滤类
 * 用于过滤过外部输入的数据，过滤数组或者变量中的不安全字符，以及HTML标签
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage request_filter
 *
 */
class Filter
{
	static $error_url;
	public $mode;

	function __construct($mode='deny',$error_url=false)
	{
        $this->mode = $mode;
        self::$error_url = $error_url;
	}
	function post($param)
	{
        $this->_check($_POST,$param);
	}
	function get($param)
	{
		$this->_check($_GET,$param);
	}
	function cookie($param)
	{
		$this->_check($_COOKIE,$param);
	}
	/**
	 * 根据提供的参数对数据进行检查
	 * @param $data
	 * @param $param
	 * @return unknown_type
	 */
	function _check(&$data,$param)
	{
		foreach($param as $k=>$p)
        {
        	if(!isset($data[$k]))
        	{
        		if(isset($p['require']) and $p['require']) self::raise('param require');
                else continue;
        	}

        	if(isset($p['type']))
        	{
        		$data[$k] = Validate::$p['type']($data[$k]);
        		if($data[$k]===false) self::raise();

        		//最小值参数
        		if(isset($p['min']) and is_numeric($data[$k]) and $data[$k]<$p['min']) self::raise('num too small');
        		//最大值参数
        		if(isset($p['max']) and is_numeric($data[$k]) and $data[$k]>$p['max']) self::raise('num too big');

        		//最小值参数
                if(isset($p['short']) and is_string($data[$k]) and mb_strlen($data[$k])<$p['short']) self::raise('string too short');
                //最大值参数
                if(isset($p['long']) and is_string($data[$k]) and mb_strlen($data[$k])>$p['long']) self::raise('string too long');

                //自定义的正则表达式
                if($p['type']=='regx' and isset($p['regx']) and preg_match($p['regx'],$data[$k])===false) self::raise();
        	}
        }
        //如果为拒绝模式，所有不在过滤参数$param中的键值都将被删除
        if($this->mode=='deny')
        {
        	 $allow = array_keys($param);
        	 $have = array_keys($data);
        	 foreach($have as $ha) if(!in_array($ha,$allow)) unset($data[$ha]);
        }
	}
	static function raise($text=false)
	{
		if(self::$error_url) Swoole_client::redirect(self::$error_url);
		if($text) exit($text);
		else exit('Web input param error!');
	}
	function _filter_int($data)
	{
		return (int)$data;
	}
	function _filter_float($data)
	{
		return (float)$data;
	}

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