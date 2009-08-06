<?php
// Controller的基类
class Controller
{
	var $swoole;
	var $filter_request = true;
	var $is_ajax = false;
	protected $model;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
		$this->model = $swoole->model;
		$this->swoole->tpl->assign_by_ref('php_genv',$swoole->genv);
		$this->swoole->tpl->assign_by_ref('php_env',$swoole->env);
		if($this->filter_request)
		{
			Filter::filter_array($_POST);
			Filter::filter_array($_GET);
		}
	}
}
?>