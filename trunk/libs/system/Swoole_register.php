<?php
/**
 * Swoole_register，对象容器类，注册模式
 * 通过get和set来动态调用Swoole中的资源
 * @package SwooleSystem
 * @author Tianfeng.Han
 */
class Swoole_register
{
	var $swoole;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}
	
	private function set($name,$object)
	{
		$this->swoole->$name=$object;
	}
	
	function database()
	{
		$this->get('db');
	}
	
	function auth()
	{
		if(isset($_SESSION) or empty($_SESSION)) session_start();
		$auth = new Auth($this->swoole->db);
		$this->swoole->auth = $auth;
	}
	
	function model($model_name)
	{
		$model = createModel($model_name,$this->swoole->db);
		$this->set($model_name,$model);
	}
	
	function get($name)
	{
		if(!is_object($this->swoole->$name))
		{
			$this->swoole->$name=$this->load($name);
		}
	}
	
	function load($lib)
	{
		$object = create($lib);			
		$this->set($lib,$object);
		return $object;
	}
}
?>