<?php
/**
 * XCache缓存，安装XCache加速器后可以使用
 * 警告：XCache缓存不适用于分布式环境
 * @author Tianfeng.Han
 * @package Swoole
 * @subpackage cache
 */
class WinCache implements ICache
{
	function __construct($config)
	{
		if(!function_exists('xcache_isset'))
		{
			throw new Error('没有安装wincache扩展');
		}
	}
	/**
	 * 设置缓存
	 * @see libs/system/ICache#set($key, $value, $expire)
	 */
	function set($keyf,$value,$timeout=0)
	{
		return xcache_set($key,$value,$timeout);
	}
	/**
	 * 检测是否存在
	 * @param $key
	 * @return unknown_type
	 */
	function exists($key)
	{
	    return xcache_isset($key);
	}
	/**
	 * 读取缓存
	 * @see libs/system/ICache#get($key)
	 */
	public function get($key)
	{
		return xcache_get($key);
	}
	/**
	 * 清空缓存
	 */
	public function clear()
	{
		wincache_ucache_clear();
	}
	/**
	 * 删除缓存
	 * @return true/false
	 */
	public function delete($key)
	{
		return xcache_unset($key);
	}
}