<?php
// Controller的基类
class Controller
{
	var $swoole;
	var $view;
	var $filter_request = true;
	var $is_ajax = false;
	protected $model;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
		$this->model = $swoole->model;
		$this->view = new View($swoole);
		
		if($this->filter_request)
		{
			Filter::filter_array($_POST);
			Filter::filter_array($_GET);
		}
	}
}
?>