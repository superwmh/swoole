<?php
class ProCsv
{
	static $row_sep = "\n";
	static $col_sep = ",";
	static $data_sep = ':';
	
	var $data;
	var $text;
	
	function __construct($text)
	{
		$this->text = $text;
	}
	
	static function parse_line($line)
	{
		$line = trim($line);
		$result = array();
		$datas = explode(self::$col_sep,$line);
		foreach($datas as $data)
		{
			$d = self::parse_data($data);
			if(empty($d[0])) continue;
			$result[trim($d[0])] = trim($d[1]);
		}
		return $result;
	}
	
	static function parse_data($data)
	{
		$data = trim($data);
		return explode(self::$data_sep,$data);
	}
	
	/**
	 * 分割一段文字
	 * @return unknown_type
	 */
	static function parse_text($text)
	{
		$text = trim($text);
		$result = array();
		$lines = explode(self::$row_sep,$text);
		foreach($lines as $line)
		{
			$result[] = self::parse_line($line);
		}
		return $result;
	}
	/**
	 * 解析函数格式，类似于 Max(a,b)
	 * @return unknown_type
	 */
	static function parse_func($str,$param)
	{
		$str = trim($str);
		$_func = explode('(',$str,2);		
		
		//不是函数形式的，返回false
		if(empty($_func)) return false;
		
		//实际要调用的函数名称
		$func = $_func[0];
		$func_arg = explode(';',substr($_func[1],0,strlen($_func[1])-1));
				
		//实际要传的参数
		$arg = array();
		foreach($func_arg as $a)
		{
			if(isset($param[$a])) $arg[] = $param[$a];
			else $arg[] = null;
		}
		return call_user_func($func,$arg);
	}
	
	static function build_line($array)
	{
		$datas = array();
		foreach($array as $k=>$v)
		{
			$datas[]=$k.self::$data_sep.$v;
		}
		return implode(self::$col_sep,$datas);
	}
}
?>