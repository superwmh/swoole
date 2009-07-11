<?php
class FieldAdmin extends AdminBase
{
	function __construct($swoole)
	{
		parent::$model_name = 'Field';
		parent::__construct($swoole);
	}
	
	function admin_del()
	{
		$this->swoole->db->delete($_GET['id'],'system_fields');
		header('Location:field_do.php');
	}
	
	function admin_list()
	{
		$app = $this->swoole->createModel('App');
		$isNewApp = false;
		
		if(empty($_GET['appid']))
		{
			$status = $app->getStatus();
			$appid = $status['Auto_increment'];
			$isNewApp = true;
		}
		else
		{
			$appid = $_GET['appid'];
			$this->swoole->tpl->assign('table_fields',$this->model->getAppFields($appid));
		}
		$this->swoole->tpl->assign('isNew',$isNewApp);
		$app->appid = $appid;
		
		if($_POST)
		{		
			if(!$isNewApp) //已存在的表
			{
				$app->addField($_POST);
			}
			unset($_POST['after']);
			$this->swoole->db->insert($_POST,'system_fields');
		}
		
		$this->swoole->tpl->assign('appid',$appid);
		$f_list = $this->model->getAppFields($appid);
		$this->swoole->tpl->assign('list',$f_list);
		$this->swoole->tpl->display(ADMIN_SKIN.'/field_list.html');
	}
	
	function admin_op()
	{
		if($_GET['job']=='order')
		{
			foreach($_POST as $key=>$order)
			{
				$this->swoole->db->query("update system_fields set orderid = $order where appid={$_GET['appid']} and name='$key'");
			}
		}
		echo "<script language='javascript'>parent.window.location.reload();</script>";
	}
}
?>