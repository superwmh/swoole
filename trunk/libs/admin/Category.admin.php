<?php
class CategoryAdmin
{
	public $app;
	public $app_config;
	public $app_model;
	public $swoole;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
		$this->model = $swoole->createModel('Category');
	}
	
	function admin_list()
	{
		if(!isset($_GET['fid'])) js_back('参数错误',-2);
		$fid = $_GET['fid'];
		
		$this->model->GetCategory($fid);
		$this->swoole->tpl->assign("cate",$this->model->Fetch('cate'));
		$this->swoole->tpl->assign("fcate",$this->model->Fetch('fcate'));
		$this->swoole->tpl->assign("list",$this->model->Fetch('child_cate'));
		
		$this->swoole->tpl->display(ADMIN_SKIN."/adminmulti_category.html");
	}
	
	function admin_modify()
	{
		if(isset($_POST['newname']) and $_POST['newname']!='')
		{
			$data['name']=$_POST['newname'];
			$data['dirname']=$_POST['dirname'];
			$data['url']=$_POST['url'];
			$data['extras'] = $_POST['extras'];
			$data['orderid'] = $_POST['orderid'];	
			
			$cate = $this->model->get($_POST['fid']);
			$cate->put($data);
			$cate->save();
			js_goto('修改成功！',"category_do.php?fid=".$_POST['fid']);
		}
	}
	
	function admin_add()
	{
		if(isset($_POST['name']) and $_POST['name']!='')
		{
			$_POST['name'] = trim($_POST['name']);
			$this->model->put($_POST);
			if(empty($_POST['dirname']))
			{
				$id = $this->swoole->db->Insert_ID();
				$the_cate = $this->model->get($id);
				$the_cate->dirname = $id;
				$the_cate->save();
				
				$f_cate = $this->model->get($_POST['fid']);
				$f_cate->childnum = $f_cate->childnum + 1;
				$f_cate->save();
			}
			js_goto('增加成功！',"category_do.php?fid=".$_POST['fid']);
		}
	}
	
	function admin_delete()
	{
		if(isset($_GET['del']) and $_GET['del']!="")
		{
			$cate = $this->model->get($_GET['del']);
			$cate->delete();
		}
		js_back('删除成功！');
	}
}
?>