<?php
/**
 * 通用试图类
 * 产生一个简单的请求控制，解析的结构，一般用于后台管理系统
 * 简单模拟List  delete  modify  add 4项操作
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage MVC
 *
 */
class GeneralView
{
	protected $swoole;
	public $action = 'list';
	public $app_name;
	static public $method_prefix = 'admin';

	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}

	function run()
	{
		if(isset($_GET['action'])) $this->action = $_GET['action'];
		$method = self::$method_prefix.'_'.$this->action;
		if(method_exists($this,$method)) call_user_func(array($this,$method));
		else Error::info('GeneralView Error!',"View <b>{$this->app_name}->{$method}</b> Not Found!");
	}

	function proc_upfiles()
	{
		import_func('file');
		if(!empty($_FILES))
		{
			foreach($_FILES as $k=>$f)
			{
				if(!empty($_FILES[$k]['type'])) $_POST[$k] = file_upload($k);
			}
		}
	}
}
?>