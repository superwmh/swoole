<?php
/**
 * 数值验证类，类中的方法都是静态的，用于检测一个变量是否符合某种规则，不符合返回false，符合返回原值
 * @author tianfeng.han
 *
 */
class Validate
{
	/**
	 * 验证是否为INT
	 * @param $num 数字
	 * @return false or $num
	 */
	static function int($num)
	{
		return filter_var($num, FILTER_VALIDATE_INT);
	}
	/**
	 * 验证是否为无符号整数UINT
	 * @param $num
	 * @return false or $num
	 */
	static function uint($num)
	{
		$num = filter_var($num, FILTER_VALIDATE_INT);;
		if($num===false or $num<0) return false;
		else return $num;
	}
	/**
	 * 验证是否为浮点型
	 * @param $num 数字
	 * @return false or $num
	 */
	static function float($num)
	{
		return filter_var($num, FILTER_VALIDATE_FLOAT);
	}
	/**
	 * 验证是否为无符号浮点型UFloat
	 * @param $num 数字
	 * @return false or $num
	 */
	static function ufloat($num)
	{
		$num = filter_var($num, FILTER_VALIDATE_FLOAT);;
        if($num===false or $num<0) return false;
        else return $num;
	}
	/**
	 * 验证是否为EMAIL
	 * @param $str
	 * @return false or $str
	 */
	static function email($str)
	{
		return filter_var($str, FILTER_VALIDATE_EMAIL);
	}
	/**
	 * 验证字符串格式
	 * @param $str
	 * @return false or $str
	 */
	static function string($str)
	{
		return filter_var($str, FILTER_DEFAULT);
	}
	/**
	 * 验证是否为IP地址
	 * @param $str
	 * @return false or $str
	 */
	static function ip($str)
	{
		return filter_var($str, FILTER_VALIDATE_IP);
	}
	/**
	 * 验证是否为URL
	 * @param $str
	 * @return false or $str
	 */
	static function url($str)
	{
		return filter_var($str, FILTER_VALIDATE_URL);
	}
	/**
	 * 过滤HTML，使参数为纯文本
	 * @param $str
	 * @return false or $str
	 */
	static function text($str)
	{
		return filter_var($str, FILTER_SANITIZE_STRING);
	}
	/**
	 * 检测是否为utf-8中文字符串
	 * @param $str
	 * @return false or $str
	 */
	static function chinese($str)
	{
		$n = preg_match("/^([\\xE4-\\xE9][\\x80-\\xBF][\\x80-\\xBF])+$/",$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
	/**
	 * 检测是否为utf-8中文字符串
	 * @param $str
	 * @return false or $str
	 */
    static function chinese_utf8($str)
	{
		$n = preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
    /**
	 * 检测是否为gb2312中文字符串
	 * @param $str
	 * @return false or $str
	 */
    static function chinese_gb($str)
	{
		$n =  preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
	/**
	 * 检测是否为自然字符串（可是中文，字符串，下划线，数字），不包含特殊字符串，只支持utf-8或者gb2312
	 * @param $str
	 * @return false or $str
	 */
	static function realstring($str,$encode='utf8')
	{
	    if($encode=='utf8') $n = preg_match('/^[\x{4e00}-\x{9fa5}|a-z|0-9|A-Z]+$/u',$str,$match);
	    else $n = preg_match("/^[".chr(0xa1)."-".chr(0xff)."|a-z|0-9|A-Z]+$/",$str,$match);
		if($n===0) return false;
		else return $match[0];
    }
    /**
     * 检测是否为固定电话格式（可包含分机号）
     * @param $str
     * @return false or $str
     */
	static function tel($str)
	{
        $n = preg_match('/(\d{3})-(\d{8})-{0,1}([0-9]{0,4})|(\d{4})-(\d{7})-{0,1}([0-9]{0,4})$/',$str,$match);
        if($n===0) return false;
		else return $match[0];
	}
	/**
     * 检测是否一个英文单词，不含空格和其他特殊字符
     * @param $str
     * @return false or $str
     */
	static function word($str)
	{
		$n = preg_match('/^([a-zA-Z_]*)$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
	/**
     * 检测是否一个日期格式
     * @param $str
     * @return false or $str
     */
	static function date($str)
	{
	    $n = preg_match('/[1-9][0-9][0-9][0-9]-[0-9]{1,2}-[0-9]{1,2}$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
	/**
     * 检测是否一个时间格式
     * @param $str
     * @return false or $str
     */
	static function time($str)
	{
		$n = preg_match('/[0-9]{1,2}(:[0-9]{1,2}){1,2}$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
	/**
     * 检测是否一个日期时间格式
     * @param $str
     * @return false or $str
     */
	static function datetime($value)
	{
	    $n = preg_match('/[1-9][0-9][0-9][0-9]-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}(:[0-9]{1,2}){1,2}$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
	/**
	 * 检查是否ASSIC码
	 * @param $value
	 * @return true or false
	 */
	static function assic($value)
	{
		$len = strlen($value);
		for ($i = 0; $i < $len; $i++) {
			$ord = ord(substr($value, $i, 1));
			if ($ord > 127) {
				return false;
			}
		}
		return $value;
	}
	/**
	 * 检查是否为IP
	 * @param $value
	 * @return true or false
	 */
	static function ipv4($str)
	{
	    $n = preg_match('/[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
	/**
	 * 检测是否八进制数
	 * @param $value
	 * @return unknown_type
	 */
	static function octal($str)
	{
	    $n = preg_match('/0[1-7]*[0-7]+$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
    /**
	 * 检测是否二进制数
	 * @param $value
	 * @return unknown_type
	 */
	static function binary($str)
	{
	    $n = preg_match('/[01]+$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
    /**
	 * 检测是否十六进制数
	 * @param $value
	 * @return unknown_type
	 */
	static function hex($str)
	{
	    $n = preg_match('/0x[0-9a-f]+$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
    /**
	 * 检测是否为域名
	 * @param $value
	 * @return unknown_type
	 */
	static function domain($str)
	{
	    $n = preg_match('/@([0-9a-z-_]+.)+[0-9a-z-_]+$/',$str,$match);
		if($n===0) return false;
		else return $match[0];
	}
}
?>