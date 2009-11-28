<?php
class GeneralView
{
	protected $swoole;
	var $action = 'list';
	var $app_name;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}
	
	function run()
	{
		if(isset($_GET['action'])) $this->action = $_GET['action'];
		$method = 'admin_'.$this->action;
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
				$_POST[$k] = file_upload($k);
			}
		}
	}
}
?>