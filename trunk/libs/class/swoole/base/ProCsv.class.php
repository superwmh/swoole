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
		$result = array();
		$datas = explode(self::$col_sep,$line);
		foreach($datas as $data)
		{
			$d = self::parse_data($data);
			$result[$d[0]] = $d[1];
		}
		return $result;
	}
	
	static function parse_data($data)
	{
		return explode(self::$data_sep,$data);
	}
	
	/**
	 * 分割一段文字
	 * @return unknown_type
	 */
	static function parse_text($text)
	{
		$result = array();
		$lines = explode(self::$row_sep,$text);
		foreach($lines as $line)
		{
			$result[] = self::parse_line($line);
		}
		return $result;
	}
}
?>