<?php 
class BuildHTML
{
	public $swoole;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}
	
	function out_detail($id,$app)
	{
		$controller = $this->swoole->createController('SiaoCMS');
		$segs = array('app'=>$app,'id'=>$id);
		$html = $controller->detail($segs);
		
		$content = $this->swoole->createModel('Content');
	}
	
	function out_category($cid)
	{
		$cate_ins = $this->swoole->createModel('Category');
		$category = $cate_ins->get($cid);
		
		$app_ins = $this->swoole->createModel('App');
		$app_config = $app_ins->getConfig($category->modelname);
		
		$pagesize = 10;
	$res = $php->db->query("select id,dirname,extras from $cate_table")->fetchall();
	$cate_list = array();
	
	foreach($res as $ct) $cate_list[$ct['id']]=$ct;
	
	$fields = $app_config[$modulename]['fields'];
	
	$res = $php->db->query("select $fields from $table");
	$list = $res->fetchall();
	
	foreach($list as $rs)
	{
		//$dir = "/$modulename/";
		$dir = "/";
		if(empty($cate_list[$rs['tid']]['dirname']))
			$cate_list[$rs['tid']]['dirname'] = $cate_list[$rs['tid']]['id'];
		
		if(empty($cate_list[$rs['tid2']]['dirname']))
			$cate_list[$rs['tid2']]['dirname'] = $cate_list[$rs['tid2']]['id'];

		$dir .= $cate_list[$rs['tid']]['dirname'].'/';
		if($rs['tid2']!=0) $dir.=$cate_list[$rs['tid2']]['dirname'].'/';
		$php->tpl->clear_all_assign();
		$php->tpl->clear_all_cache();
		$php->tpl->assign('title',$rs['title']);
		$php->tpl->assign('id',$rs['id']);
		
		$php->tpl->outhtml("show_$modulename.html",$rs['id'].'.html',HTML.$dir);
		$php->db->update($rs['id'],array('url'=>HTML_URL_BASE.$dir.$rs['id'].'.html'),$table);
	}
	
	$root_cate = $php->db->query("select * from $cate_table where fid=0")->fetchall();
	foreach($root_cate as $cate)
	{
		if($cate['extras']==3) continue;
		if(empty($cate['dirname']))
			$cate['dirname'] = $cate['id'];
			
		//$dir = "/$modulename/".$cate['dirname'];
		$dir= '/'.$cate['dirname'];
		$num = $php->db->query("select count(id) as cc from $table where tid={$cate['id']}")->fetch();
		$pages = getPages($num['cc'],$pagesize);

		for($i=1;$i<=$pages;$i++)
		{
			$php->tpl->clear_all_assign();
			$php->tpl->clear_all_cache();
			$php->tpl->assign('title',$cate['name']);
			$php->tpl->assign('catid',$cate['id']);
			$php->tpl->assign('lv',1);
			$php->tpl->assign('page',$i);
			if($i==1) $php->tpl->outhtml($modulename.'.html',"index.html",HTML.$dir);
			$php->tpl->outhtml($modulename.'.html',"index_page$i.html",HTML.$dir);
		}
		$php->db->update($cate['id'],array('url'=>HTML_URL_BASE.$dir.'/'),$cate_table);
		
		$childs = $php->db->query("select * from $cate_table where fid={$cate['id']}")->fetchall();

		if(count($childs)>0)
		{
			foreach($childs as $child_cate)
			{
				if($child_cate['extras']==3) continue;
				if(empty($child_cate['dirname']))
					$child_cate['dirname'] = $child_cate['id'];
				$child_dir = $dir.'/'.$child_cate['dirname'];		
				$num = $php->db->query("select count(id) as cc from $table where tid2={$child_cate['id']}")->fetch();
				$child_pages = getPages($num['cc'],$pagesize);

				for($j=1;$j<=$child_pages;$j++)
				{
					$php->tpl->clear_all_assign();
					$php->tpl->clear_all_cache();
					$php->tpl->assign('title',$child_cate['name']);
					$php->tpl->assign('catid',$child_cate['id']);
					$php->tpl->assign('lv',2);
					$php->tpl->assign('page',$j);
					if($j==1) $php->tpl->outhtml($modulename.'.html',"index.html",HTML.$child_dir);
					$php->tpl->outhtml($modulename.'.html',"index_page$j.html",HTML.$child_dir);
				}
				$php->db->update($child_cate['id'],array('url'=>HTML_URL_BASE.$child_dir.'/'),$cate_table);
			}
		}
	}
	}
	
	function out_app($app)
	{
		
	}
	
	function out_content($id,$appid)
	{
		
	}
	
	static function outhtml($html,$filename,$path='')
	{
		if($path==='')
		{
			$path = dirname($filename);
			$filename = basename($filename);
		}
		if(!is_dir($path)) mkdir($path,0777,true);
		return file_put_contents($path.'/'.$filename,$html);
	}
}
?>