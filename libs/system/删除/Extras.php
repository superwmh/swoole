<?php
/**
 * 主要用于，数据库表的扩展选项，一般用于，多选框和单选框
*/

class Extras
{
	var $options;
	var $value;
	
	var $argc = 3;
	
	function __construct($options='')
	{
		$this->options = $options;
	}
	
	function value($value='')
	{
		if($value==='') return $this->value;
		else $this->value = $value;
	}
	
	static function getDec($params)
	{
		$num=0;
		if(count($params)==0) return 0;
		foreach($params as $param)
		{
			$num+=bindec(intval($param));
		}
		return $num;
	}
	
	static function getDecByOpt($params)
	{
		$this->options[$params];
	}
	
	static function getBin($dec_num)
	{
		return decbin(intval($dec_num));
	}
	
	function ext_has($opt)
	{
		return $this->ext_if($this->options[$opt]);
	}
	
	function ext_if($option_index)
	{
		$option_index = intval($option_index);
		$binnum_str = strval(self::getBin($this->value));
		if(strlen($binnum_str)<$this->argc)
			$binnum_str = str_pad($binnum_str,$this->argc,'0',STR_PAD_LEFT);
		
		if($option_index>=$this->argc) return false;
		if($binnum_str{$option_index}=='1') return true;
		else return false;
	}
	
}
?>