<?php
/**
 * 用于读取配置文件
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage base
 *
 */
class SwooleConfig implements ArrayAccess
{
	private $_data;
	
	function __get($key)
	{
		return $this->offsetGet($key);
	}
	function offsetSet($key,$value){}
	function offsetUnset($key){}
	function offsetExists($key){}
	function offsetGet($key)
	{
		if(!isset($this->_data[$key]))
		{
			SiteDict::$data_dir = APPSPATH.'/configs';
			$this->_data[$key] = SiteDict::get('config_'.$key);
		}		
		return $this->_data[$key];
	}
}
?>