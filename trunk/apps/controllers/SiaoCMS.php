<?php
class SiaoCMS extends Controller
{
	function index($segs)
	{
		$this->swoole->tpl->display('index.html');
	}
	
	function detail(&$segs)
	{
		$this->swoole->env['app'] = $segs['app'];
		$this->swoole->env['content_id'] = $segs['id'];
		$app = $this->swoole->createModel('App');
		$app->getConfig($segs['app']);
		$ins = $app->getInstance();
		
		$content = $ins->get($segs['id']);
		$this->swoole->tpl->assign_by_ref('content',$content);
		
		$cate = $this->swoole->createModel('Category');	
		$category = $cate->get($content['catid']);
		$this->swoole->env['category_id'] = $content['catid'];
		$this->swoole->tpl->assign_by_ref('category',$category);
		
		$tpl_name = $category->modelname.'_detail.html';
		if(!$this->swoole->tpl->template_exists($tpl_name))
			$tpl_name = 'detail.html';
		return $this->swoole->tpl->fetch($tpl_name);
	}
	
	function catalog(&$segs)
	{
		$this->swoole->env['category_id'] = $segs['catid'];
		$this->swoole->env['app'] = $segs['app'];
		
		$this->swoole->tpl->assign('page',isset($segs['page'])?$segs['page']:1);
		
		$cate = $this->swoole->createModel('Category');
		$category = $cate->get($segs['catid']);
		$this->swoole->tpl->assign_by_ref('category',$category);
		$tpl_name = empty($category->template)?$category->modelname.'_catalog.html':$category->template;
		if(!$this->swoole->tpl->template_exists($tpl_name))
			$tpl_name = 'catalog.html';
		return $this->swoole->tpl->fetch($tpl_name);
	}
	
	function comment(&$segs)
	{
		if(!isset($_GET['aid']) or !isset($_GET['app'])) return;
		$aid = $_GET['aid'];
		$table = TABLE_PREFIX.'_comment';
		$app = $_GET['app'];
		
		if($_POST)
		{
			unset($_POST['image_x'],$_POST['image_y']);
			$this->swoole->db->insert($_POST,$table);
		}
		$res = $this->swoole->db->query("select * from $table where aid={$aid} and app='{$app}'");
		$this->swoole->tpl->assign('list',$res->fetchall());
		return $this->swoole->tpl->fetch('comment.html');
	}
	
	function resource(&$segs)
	{
		if(!isset($_GET['aid']) or !isset($_GET['app'])) return;
		$aid = $_GET['aid'];
		$table = TABLE_PREFIX.'_resource';
		$app = $_GET['app'];
		
		$res = $this->swoole->db->query("select * from $table where aid={$aid} and app='{$app}'");
		$this->swoole->tpl->assign('list',$res->fetchall());
		return $this->swoole->tpl->fetch('resource.html');
	}
	
	function search(&$segs)
	{
		
	}
}
?>