<?php
class AdminBase
{
	public $swoole;
	public $model;
	static $model_name;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
		$this->model = $swoole->createModel(self::$model_name);
	}
	
	function admin_add()
	{
		if(isset($_POST['name']))
		{
			$new = $this->model->get();
			$new->put($_POST);
			$new->save();
			Swoole_js::js_back('添加成功！',-2);
		}
		else
		{
			$this->swoole->tpl->display(ADMIN_SKIN.'/add_'.strtolower(self::$model_name).'.html');
		}
	}
	
	function admin_list()
	{
		$list = $this->model->all()->getall();
		$this->swoole->tpl->assign('num',count($list));
		$this->swoole->tpl->assign('list',$list);
		$this->swoole->tpl->display(ADMIN_SKIN.'/app_list.html');
	}
}
?>