<?php
/**
 * 用于读取配置文件
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage base
 *
 */
class SwooleConfig
{
	private $_data;
	private $_file;

	function __construct($config_file)
	{
	  // $this->_file = $config_file;
	   //require $config_file;
	   //$this->_data = $config;
	}

	function __get($key)
	{
		return $this->offsetGet($key);
	}

	function __set($key,$value)
	{
		$this->_data[$key] = $value;
	}

	function save()
	{

	}
}
?>