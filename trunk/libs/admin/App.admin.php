<?php
class AppAdmin extends AdminBase
{
	function __construct($swoole)
	{
		parent::$model_name = 'App';
		parent::__construct($swoole);
	}
	
	function admin_newapp()
	{
		if($_POST)
		{
			$this->swoole->db->insert($_POST,'system_apps');
			$appid = $this->swoole->db->Insert_ID();
			
			$new_cate = array('name'=>$_POST['displayname'],'modelname'=>$_POST['name']);
			$this->swoole->db->insert($new_cate,'system_category');
			$catid = $this->swoole->db->Insert_ID();
			
			$this->swoole->db->update($appid,array('category'=>$catid),'system_apps');
			$this->model->createTable($appid);
			Swoole_js::js_goto('增加成功！','app_do.php');
		}
		else
		{
			$this->swoole->tpl->assign('field_types',load_data('field_types'));
			$this->swoole->tpl->display(ADMIN_SKIN.'/app_add.html');
		}
	}
	
	function admin_modify()
	{
		$id = $_GET['id'];
		$app = $this->model->get($id);
		if($_POST)
		{
			$app->put($_POST);
			$app->save();
			Swoole_js::js_goto('修改成功！','app_do.php');
		}
		else
		{
			$this->swoole->tpl->assign('app',$app);
			$this->swoole->tpl->display(ADMIN_SKIN.'/app_modify.html');
		}
	}
	
	function admin_delete()
	{
		if(isset($_GET['appid']))
		{
			$app = $this->model->get($_GET['appid']);
		}
		else exit;
		if(isset($_POST['confirm']) and $_POST['confirm']=='yes')
		{
			$this->swoole->db->query("DROP TABLE `{$app['tablename']}`");
			$app->delete();
			Swoole_js::js_goto('删除成功！','app_do.php');
		}
		else
		{
			$this->swoole->tpl->assign('name','删除应用');
			$this->swoole->tpl->assign('app',$app);
			$this->swoole->tpl->display(ADMIN_SKIN.'/admin_confirm.html');
		}
	}
}
?>