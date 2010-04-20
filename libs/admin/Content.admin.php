<?php
class ContentAdmin
{
	public $app;
	public $app_config;
	public $app_model;
	public $swoole;
	
	public $base_url;
	public $category;
	
	public $fields_array;
	
	public $offset = 0;
	public $pagesize = 8;
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
		if(isset($_GET['app']))
		{
			$this->app = $_GET['app'];		
			$this->app_model = $swoole->createModel('App');		
			$this->app_config = $this->app_model->getConfig($this->app);
			
			$this->fields_array = explode(',',$this->app_config['addfields']);
			$this->base_url = 'content_do.php?app='.$this->app.'&catid='.@$_GET['catid'];
			$this->swoole->tpl->assign('app',$this->app_config);
			$this->swoole->tpl->assign('admin',$this);
		}
	}
	
	function admin_view()
	{
		$fid = 0;
		$category_instance = $this->swoole->createModel('Category');
		
		if(isset($_GET['fid']))
		{
			$fid = $_GET['fid'];
			$the_cate = $category_instance->get($fid);
			if($fid!=0)
			{
				$this->swoole->tpl->assign('list',$category_instance->getContents($fid,'id,title,addtime'));
			}
			$this->swoole->tpl->assign('the_cate',$the_cate);
		}
		
		$this->swoole->tpl->assign('fid',$fid);
		$this->swoole->tpl->assign('category',$category_instance->getChild($fid));
		$this->swoole->tpl->display(ADMIN_SKIN.'/content_root.html');
	}

	function admin_list()
	{
		$where = 1;
		if(!isset($_GET['catid'])) echo '参数错误！';
		$where= 'catid='.$_GET['catid'];
		$this->getCategory($_GET['catid']);
			
		if(isset($_GET['del']) and $_GET['del']!="")
		{
			$this->swoole->db->query("delete from {$this->app_config['tablename']} where id=".$_GET['del'].' limit 1');
		}
		
		$res = $this->swoole->db->query("select count(id) as cc from {$this->app_config['tablename']} where $where limit 1")->fetch();
		$num = $res['cc'];
		
		$pager = new Pager(array('total'=>$num,'perpage'=>$this->pagesize));
		$this->offset = $pager->offset();
		$this->swoole->tpl->assign('pager',$pager->render());

		$res = $this->swoole->db->query("select {$this->app_config['adminfields']} from {$this->app_config['tablename']} where $where order by id desc limit $this->offset,$this->pagesize");
		$this->swoole->tpl->assign('list',$res->fetchall());
		$this->swoole->tpl->display(ADMIN_SKIN.'/adminmulti_list.html');
	}
	
	function admin_add()
	{
		if($_POST)
		{
			if(isset($_POST['content']))
			{
				$_POST['content']=stripcslashes($_POST['content']);
				$_POST['content']=str_replace("'","\"",$_POST['content']);
				$_POST['content']=imageToLacal($_POST['content']);
			}

			//插入数据库
			$this->proc_upload();
			$this->proc_category();
			
			//处理URL
			$this->proc_url();

			$this->swoole->db->insert($_POST,$this->app_config['tablename']);
			
			$id = $this->swoole->db->Insert_ID();
			
			$controller = $this->swoole->createController('SiaoCMS');
			$segs = array('app'=>$_GET['app'],'id'=>$id);
			
			$html = $controller->detail($segs);
			BuildHTML::outhtml($html,WEBPATH.$_POST['url']);
			
			//搜索引擎建立索引系统
			$this->proc_search($id);

			//js_goto("添加成功!","content_do.php?catid={$_GET['catid']}&app={$_GET['app']}");
		}
		else
		{
			if($this->app_config['ifresource']=='1')
			{
				$resource = resource($this->app_config['name']);			
				$this->swoole->tpl->assign('resource',$resource);
			}
			$fields = $this->app_model->getFields();
			$forms = new AutoForm($fields);
			$this->swoole->tpl->assign('forms',$forms->getForm());
			$this->swoole->tpl->display(ADMIN_SKIN."/adminmulti_add.html");
		}
	}
	
	function admin_modify()
	{		
		$id = $_GET['id'];
		$app_instance = $this->app_model->getInstance();
		$content = $app_instance->get($id);
			
		if($_POST)
		{
			if(isset($_POST['content']))
			{
				$_POST['content']=str_replace("'","\"",$_POST['content']);
				$_POST['content']=stripcslashes($_POST['content']);
			}
			
			//更新数据库
			$this->proc_upload();
			$content->put($_POST);
			$content->save();
			Swoole_js::js_back('修改成功！',-2);
		}
		else
		{
			$fields = $this->app_model->getFields();
			$forms = new AutoForm($fields);
			$this->swoole->tpl->assign('content',$content);
			$this->swoole->tpl->assign('forms',$forms->getForm($content));
			$this->swoole->tpl->display(ADMIN_SKIN."/adminmulti_modify.html");
		}
	}
	
	function admin_delete()
	{
		if(isset($_GET['del']) and $_GET['del']!="")
		{
			$table = $this->app_config['tablename'];
			$this->swoole->db->query("delete from $table where id=".$_GET['del'].' limit 1');
			js_goto('删除成功！',$this->base_url);
		}
	}
	
	function admincategory()
	{
		if(isset($_POST['name']) and $_POST['name']!='')
		{
			$this->swoole->db->insert($_POST,$this->category_table);
		}
		
		if(isset($_GET['del']) and $_GET['del']!="")
		{
			$this->swoole->db->query("delete from {$this->category_table} where id=".$_GET['del']);
		}
		
		if(isset($_GET['fid']) and $_GET['fid']!='')
		{
			$fid = $_GET['fid'];
		}
		else $fid = 0;
		
		if(isset($_POST['newname']) and $_POST['newname']!='')
			$this->swoole->db->update($fid , array('name'=>$_POST['newname']),$this->category_table);
		$res=$this->swoole->db->query("select * from {$this->category_table} where id = $fid limit 1")->fetch();
		$this->swoole->tpl->assign("cate",$res);
		
		if($fid!=0){
			$res=$this->swoole->db->query("select * from {$this->category_table} where id = ".$res['fid'].' limit 1')->fetch();
			$this->swoole->tpl->assign("fcate",$res);
		}
		
		$res=$this->swoole->db->query("select * from {$this->category_table} where fid = $fid");
		$this->swoole->tpl->assign("list",$res->fetchall());
		$this->swoole->tpl->display(ADMIN_SKIN."/adminmulti_category.html");
		
	}
	
	function admin_operate()
	{
		if(isset($_POST['job']) and $_POST['job']!='')
		{
			if(!isset($_POST['ids'])) js_alert('没有选中任何操作的对象');
			extract($_POST);
			if($job=='delete')
			{
				foreach($ids as $id)
					$this->swoole->db->delete($id,$this->app_config['tablename']);
			}
			elseif($job=='digest')
			{
				foreach($ids as $id)
					$this->swoole->db->update($id,array('digest'=>$digest),$this->app_config['tablename']);
			}
			elseif($job=='move' and $tocid!='0')
			{
				$cate = $this->swoole->db->query("select name from system_category where id=".$tocid)->fetch();
				foreach($ids as $id) $this->swoole->db->update($id,array('catid'=>$tocid,'catname'=>$cate['name']) ,$this->app_config['tablename']);
			}
			echo "<script language='javascript'>parent.window.location.reload();</script>";
		}
	}
	/**
	 * 递归获取父类
	 * @param $catid
	 * @return unknown_type
	 */
	function getFCategory($the_category)
	{
		$cates[] = $the_category;
		$fid = $the_category['fid'];
		if($fid!=0)
		{
			$fcate = $this->swoole->db->query("select * from system_category where id=$fid limit 1")->fetch();
			$cates = array_merge($cates,$this->getFCategory($fcate));
		}
		return $cates;
	}
	
	function proc_url()
	{
		$dir = $this->category['dirname'];
		if($this->swoole->config->content_filename=='id')
		{
			$ins = $this->app_model->getInstance();
			$status = $ins->getStatus();
			
			$id = $status['Auto_increment'];
			$filename = $id.HTML_FILE_EXT;
		}
		elseif($this->swoole->config->content_filename=='md5')
		{
			$filename = substr(md5(time()),0,8).HTML_FILE_EXT;
		}
		else
		{
			$filename = rand(10000,99999).HTML_FILE_EXT;
		}
		$_POST['url'] = HTML_URL_BASE.'/'.$dir.'/'.$filename;
	}
	
	function proc_category()
	{
		if(isset($_POST['catid']))
		{
			$catid = $_POST['catid'];
			$this->category = $this->swoole->db->query("select * from system_category where id=$catid limit 1")->fetch();
			$_POST['catname'] = $this->category['name'];
			$this->swoole->db->query("update system_category set recordnum=recordnum+1 where id=$catid limit 1");
		}
	}
	
	function proc_search($id)
	{
		$search = new SwooleSearch($this->swoole->db);
		$search->addIndex($_POST,$id,$_GET['app']);
	}
	
	function proc_upload()
	{
		if(isset($_FILES['image'])) if($_FILES['image']['type']!="") $_POST['image']=file_upload("image");
		if(isset($_FILES['picture'])) if($_FILES['picture']['type']!="") $_POST['picture']=file_upload("picture");			
		if(isset($_FILES['m_image'])) if($_FILES['m_image']['type']!="") $_POST['m_image']=file_upload("m_image");
	}
	
	function getCategory($fid)
	{
		$the_category = $this->swoole->db->query("select * from system_category where id=$fid limit 1")->fetch();
		$this->swoole->tpl->assign('category',$the_category);
		
		$sclist = $this->swoole->db->query("select * from system_category where fid=$fid")->fetchall();
		if(count($sclist)>0)
		{
			$this->swoole->tpl->assign('small',true);
			$this->swoole->tpl->assign('sclist',$sclist);
		}
		else $this->swoole->tpl->assign('small',false);
		
		$fcates = $this->getFCategory($the_category);
		$this->swoole->tpl->assign('fcates',array_reverse($fcates));
		
		if($the_category['fid']==0) return;
		$clist = $this->swoole->db->query("select * from system_category where fid={$the_category['fid']}")->fetchall();
		$this->swoole->tpl->assign('clist',$clist);		
	}
	
	function page($num)
	{
		if(!isset($_GET['page']))
		{
			$page=1;
		}
		else
		{
			$page=$_GET['page'];
		}
		
		$this->offset=($page-1)*$this->pagesize;

		if($num%$this->pagesize>0) $pages=intval($num/$this->pagesize)+1;
		else{$pages=$num/$this->pagesize;}
		
		$start=10*intval($page/10);
		if($pages>10 and $page<$start) $this->php->tpl->assign("more",1);
		$this->swoole->tpl->assign("start",$start);
		$this->swoole->tpl->assign("end",$pages-$start);
		$this->swoole->tpl->assign("pages",$pages);
		$this->swoole->tpl->assign("page",$page);
		$this->swoole->tpl->assign("pagesize",$this->offset);
		$this->swoole->tpl->assign("num",$num);
	}
	
	function hasField($fieldname)
	{
		if(in_array($fieldname,$this->fields_array)) return true;
		return false;
	}
}
?>