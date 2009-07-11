<?php
class Admin
{
	var $action = 'list';
	var $php;
	var $fields_array = array();
	
	var $offset = 0;
	var $pagesize = 10;
	
	var $app;
	var $app_config;
	
	function __construct($php)
	{
//		$this->app = createModel($_GET['app']);
//		exit;
		$this->php = $php;
		$this->app = $_GET['app'];
		require(WEBPATH.'/apps/app_config.php');
		$this->app_config = $app_config;
		
		$this->table = TABLE_PREFIX.'_'.$this->table;
		$this->category_table = TABLE_PREFIX.'_'.$this->category_table;
	}
	
	function run()
	{
		
		if(isset($_GET['action'])) $this->action = $_GET['action'];
		
		$this->php->tpl->assign('app',array('name'=>$this->app));
		
		$this->fields = $this->app_config[$this->app]['fields'];
		$this->fields_array = explode(',',$this->fields);
		
		namespace('js');
		namespace('content');
		namespace('file');
		
		$this->php->tpl->assign('admin',$this);
		if($this->hasField('catid')) $this->getCategory();
		call_user_method('admin'.$this->action,$this);
	}
	
	function getCategory()
	{
		$fid = 0;
		if(isset($_GET['fid'])) $fid = $_GET['fid'];
		if($fid==0)
		{
			$res = $this->php->db->query("select * from {$this->category_table} where fid=$fid");
			$this->php->tpl->assign('clist',$res->fetchall());
			$this->php->tpl->assign('small',false);
		}
		else
		{
			$the_category = $this->php->db->query("select * from {$this->category_table} where id=$fid")->fetch();
			$this->php->tpl->assign('category',$the_category);
			
			$clist = $this->php->db->query("select * from {$this->category_table} where fid={$the_category['fid']}")->fetchall();
			$this->php->tpl->assign('clist',$clist);
			
			$sclist = $this->php->db->query("select * from {$this->category_table} where fid=$fid")->fetchall();
			if(count($sclist)>0)
			{
				$this->php->tpl->assign('small',true);
				$this->php->tpl->assign('sclist',$sclist);
			}
			else $this->php->tpl->assign('small',false);
		}
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
		elseif($this->offset!=0){$pages=$num/$this->offset;}
		else $pages = 1;
		
		$start=10*intval($page/10);
		if($pages>10 and $page<$start) $this->php->tpl->assign("more",1);
		$this->php->tpl->assign("start",$start);
		$this->php->tpl->assign("end",$pages-$start);
		$this->php->tpl->assign("pages",$pages);
		$this->php->tpl->assign("page",$page);
		$this->php->tpl->assign("pagesize",$this->offset);
		$this->php->tpl->assign("num",$num);
	}
	
	function adminlist()
	{
		$res = $this->php->db->query("select count(id) as cc from $this->table where app = '$this->app' limit 1")->fetch();
		$num = $res['cc'];
		$this->page($num);
		$res = $this->php->db->query("select $this->fields from $this->table where app = '$this->app' limit $this->offset,$this->pagesize");
		$this->php->tpl->assign('list',$res->fetchall());
		$this->php->tpl->display(ADMIN_SKIN.'/admin_list.html');
	}
	
	function adminadd()
	{
		if(isset($_POST['app']))
		{
			if($this->hasField('content'))
			{
				$_POST['content']=stripcslashes($_POST['content']);
				$_POST['content']=str_replace("'","\"",$_POST['content']);
				$_POST['content']=imageToLacal($_POST['content']);
			}

			//�������ݿ�
			if($_FILES['image']['type']!="") $_POST['image']=file_upload("image");
			if($this->php->db->insert($_POST,$this->table)) js_back("��ӳɹ�!",-2);
		}
		else
		{
			if($this->hasField('content'))
			{
				$editor = editor("content","",480);
				$this->php->tpl->assign('editor',$editor);
			}
			$this->php->tpl->display(ADMIN_SKIN."/admin_add.html");
		}
	}
	
	function adminmodify()
	{
		$id=$_GET["id"];
		if(isset($_POST['app']))
		{
			if(isset($_POST['content']))
			{
				$_POST['content']=str_replace("'","\"",$_POST['content']);
				$_POST['content']=stripcslashes($_POST['content']);
			}
			if(isset($_FILES['image'])) if($_FILES['image']['type']!="") $_POST['image']=file_upload("image");
			if(isset($_FILES['picture'])) if($_FILES['picture']['type']!="") $_POST['picture']=file_upload("picture");			
			
			//�������ݿ�
			if($this->php->db->update($id,$_POST,$this->table)) js_back('�޸ĳɹ���',-2);
		}
		else
		{
			$res=$this->php->db->query("select * from $this->table where id=$id limit 1")->fetch();
			$this->php->tpl->assign("news",$res);
			$this->php->tpl->assign("editor",editor("content",$res["content"],480));
			$this->php->tpl->display(ADMIN_SKIN."/admin_modify.html");
		}
	}
	
	function admincategory()
	{
		if(isset($_POST['name']) and $_POST['name']!='')
		{
			$this->php->db->insert($_POST,$this->category_table);
		}
		
		if(isset($_GET['del']) and $_GET['del']!="")
		{
			$this->php->db->query("delete from {$this->category_table} where id=".$_GET['del']);
		}
		
		if(isset($_GET['fid']) and $_GET['fid']!='')
		{
			$fid = $_GET['fid'];
		}
		else $fid = 0;
		
		if(isset($_POST['newname']) and $_POST['newname']!='') update($fid , array('name'=>$_POST['newname']),$this->category_table);
		$res=$this->php->db->query("select * from {$this->category_table} where id = $fid limit 1")->fetch();
		$this->php->tpl->assign("cate",$res);
		
		if($fid!=0){
			$res=$this->php->db->query("select * from {$this->category_table} where id = ".$res['fid'].' limit 1')->fetch();
			$this->php->tpl->assign("fcate",$res);
		}
		
		$res=$this->php->db->query("select * from {$this->category_table} where fid = $fid");
		$this->php->tpl->assign("list",$res->fetchall());
		$this->php->tpl->display(ADMIN_SKIN."/admin_category.html");
		
	}
	
	function adminoperate()
	{
		
	}
	
	function hasField($fieldname)
	{
		if(in_array($fieldname,$this->fields_array)) return true;
		return false;
	}
}