<?php
/**
 * Controller的基类，控制器基类
 * @package SwooleSystem
 * @subpackage MVC
 */
class Controller
{
	public $swoole;
	public $view;
	public $is_ajax = false;
	public $if_filter = true;
	
	protected $model;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
		$this->model = $swoole->model;
		$this->view = new View($swoole);
		
		if($this->if_filter) Filter::request();
	}
}
?>