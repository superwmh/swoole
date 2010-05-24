<?php
class Validate
{
	/**
	 * 验证是否为INT
	 * @param $num
	 * @return unknown_type
	 */
	static function int($num)
	{
		return filter_var($num, FILTER_VALIDATE_INT);
	}
	/**
	 * 验证是否为无符号整数UINT
	 * @param $num
	 * @return unknown_type
	 */
	static function uint($num)
	{
		$num = filter_var($num, FILTER_VALIDATE_INT);;
		if($num!==false and $num<0) return false;
		else return $num;
	}

	static function float()
	{
		return filter_var($num, FILTER_VALIDATE_FLOAT);
	}
	static function ufloat($num)
	{
		$num = filter_var($num, FILTER_VALIDATE_FLOAT);;
        if($num!==false and $num<0) return false;
        else return $num;
	}
	/**
	 * 验证是否为EMAIL
	 * @param $str
	 * @return unknown_type
	 */
	static function email($str)
	{
		return filter_var($var, FILTER_VALIDATE_EMAIL);
	}
	static function string($str)
	{
		return filter_var($var, FILTER_DEFAULT);
	}
	/**
	 * 验证是否为IP地址
	 * @param $var
	 * @return unknown_type
	 */
	static function ip($var)
	{
		return filter_var($var, FILTER_VALIDATE_IP);
	}
	/**
	 * 验证是否为URL
	 * @param $var
	 * @return unknown_type
	 */
	static function url($var)
	{
		return filter_var($var, FILTER_VALIDATE_URL);
	}
	/**
	 * 过滤HTML，使参数为纯文本
	 * @param $var
	 * @return unknown_type
	 */
	static function text($var)
	{
		filter_var($var, FILTER_SANITIZE_STRING);
	}
	static function chinese()
	{
		return preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$str);
	}
	static function realstring()
	{
		return preg_match('/^[".chr(0xa1)."-".chr(0xff)." | a-z | 0-9 | A-Z| \@\.\_\]\[\!]+$/',$str);
	}
	static function chinese_gb()
	{
		return preg_match('/^[".chr(0xa1)."-".chr(0xff)." ]+$/',$str);
	}
	static function tel()
	{

	}
	static function date($value)
	{
		if (!eregi("^[1-9][0-9][0-9][0-9]-[0-9]+-[0-9]+$", $value))
		{
			return false;
		}
		$time = strtotime($value);
		if ($time === -1) {
			return false;
		}
		$time_e = explode('-', $value);
		$time_ex = explode('-', Date('Y-m-d', $time));
		for ($i = 0; $i < count($time_e); $i++)
		{
			if ((int)$time_e[$i] != (int)$time_ex[$i])
			{
				return false;
			}
		}
		return true;
	}
	static function time($value)
	{
		if(!eregi("^[0-9]{1,2}(:[0-9]{1,2}){1,2}$", $value))
		{
			return false;
		}
		$time = strtotime($value);
		if ($time === -1)
		{
			return false;
		}
		$time_e = explode(':', $value);
		$time_ex = explode(':', Date('H:i:s', $time));
		for ($i = 0; $i < count($time_e); $i++)
		{
			if ((int)$time_e[$i] != (int)$time_ex[$i])
			{
				return false;
			}
		}
		return true;
	}
	static function datetime($value)
	{
        return eregi("^[1-9][0-9][0-9][0-9]-[0-9]+-[0-9]+ [0-9]{1,2}(:[0-9]{1,2}){1,2}$", $value);
	}
	static function assic($value) {
		$len = strlen($value);
		for ($i = 0; $i < $len; $i++) {
			$ord = ord(substr($value, $i, 1));
			if ($ord > 127) {
				return false;
			}
		}
		return true;
	}
	static function ipv4($value)
	{
		return ereg("^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$", $value);
	}
	static function octal($value)
	{
		return ereg("^0[1-7]*[0-7]+$", $value);
	}

	static function binary($value)
	{
		return ereg("^[01]+$", $value);
	}

	static function hex($value)
	{
		return eregi("^0x[0-9a-f]+$", $value);
	}

	static function domain($value)
	{
		return eregi("^@([0-9a-z-_]+.)+[0-9a-z-_]+$", $value);
	}
}
?>