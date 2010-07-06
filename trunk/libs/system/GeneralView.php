<?php
/**
 * 通用试图类
 * 产生一个简单的请求控制，解析的结构，一般用于后台管理系统
 * 简单模拟List  delete  modify  add 4项操作
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage MVC
 *
 */
class GeneralView
{
	protected $swoole;
	public $action = 'list';
	public $app_name;
	static public $method_prefix = 'admin';

	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}

	function run()
	{
		if(isset($_GET['action'])) $this->action = $_GET['action'];
		$method = self::$method_prefix.'_'.$this->action;
		if(method_exists($this,$method)) call_user_func(array($this,$method));
		else Error::info('GeneralView Error!',"View <b>{$this->app_name}->{$method}</b> Not Found!");
	}

	function proc_upfiles()
	{
		import_func('file');
		if(!empty($_FILES))
		{
			foreach($_FILES as $k=>$f)
			{
				if(!empty($_FILES[$k]['type'])) $_POST[$k] = file_upload($k);
			}
		}
	}
	
	function handle_entity_center($config)
	{
		if(!isset($config['model']) or !isset($config['name'])) die('参数错误！');
		$_model = createModel($config['model']);
		$this->swoole->tpl->assign('act_name',$config['name']);
		if(empty($config['tpl.add'])) $config['tpl.add'] = LIBPATH.'/data/tpl/admin_entity_center_add.html';
		if(empty($config['tpl.list'])) $config['tpl.list'] = LIBPATH.'/data/tpl/admin_entity_center_list.html';
		if(isset($config['limit']) and $config['limit']===true) $this->swoole->tpl->assign('limit',true);
		else $this->swoole->tpl->assign('limit',false);
		
		if(isset($_GET['add']))
		{
			if(!empty($_POST['name']))
			{
				$data['name'] = trim($_POST['name']);
				$data['fid'] = intval($_POST['fid']);
				$data['intro'] = trim($_POST['intro']);
				
				#增加
				if(empty($_POST['id']))
				{
					$_model->put($data);
					Swoole_js::js_back('增加成功！');
				}
				#修改
				else
				{
					$_model->set((int)$_POST['id'],$data);
					Swoole_js::js_back('增加成功！');	
				}
			}
			else
			{
				if(!empty($_GET['id']))
				{
					$data = $_model->get((int)$_GET['id'])->get();
					$this->swoole->tpl->assign('data',$data);
				}
				$this->swoole->tpl->display($config['tpl.add']);
			}			
		}
		else
		{
			if(!empty($_GET['del']))
			{
				$del_id = intval($_GET['del']);
				$_model->del($del_id);
				Swoole_js::js_back('删除成功！');						
			}
			//Error::dbd();
			$get['fid']  = empty($_GET['fid'])?0:(int)$_GET['fid'];						
			$get['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
			$get['pagesize'] = 15;
			$pager = null;
			$list = $_model->gets($get,$pager);
			$this->swoole->tpl->assign('list',$list);
			$this->swoole->tpl->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
			$this->swoole->tpl->display($config['tpl.list']);
		}
	}
	
	function handle_catelog_center($config)
	{
		if(!isset($config['model']) or !isset($config['name'])) die('参数错误！');
		$_model = createModel($config['model']);
		$this->swoole->tpl->assign('act_name',$config['name']);
		if(empty($config['tpl.add'])) $config['tpl.add'] = LIBPATH.'/data/tpl/admin_catelog_center_add.html';
		if(empty($config['tpl.list'])) $config['tpl.list'] = LIBPATH.'/data/tpl/admin_catelog_center_list.html';
		if(isset($config['limit']) and $config['limit']===true) $this->swoole->tpl->assign('limit',true);
		else $this->swoole->tpl->assign('limit',false);
		
		if(isset($_GET['add']))
		{
			if(!empty($_POST['name']))
			{
				$data['name'] = trim($_POST['name']);
				$data['fid'] = intval($_POST['fid']);
				$data['intro'] = trim($_POST['intro']);
				
				#增加
				if(empty($_POST['id']))
				{
					$_model->put($data);
					Swoole_js::js_back('增加成功！');
				}
				#修改
				else
				{
					$_model->set((int)$_POST['id'],$data);
					Swoole_js::js_back('增加成功！');	
				}
			}
			else
			{
				if(!empty($_GET['id']))
				{
					$data = $_model->get((int)$_GET['id'])->get();
					$this->swoole->tpl->assign('data',$data);
				}
				$this->swoole->tpl->display($config['tpl.add']);
			}			
		}
		else
		{
			if(!empty($_GET['del']))
			{
				$del_id = intval($_GET['del']);
				$_model->del($del_id);
				Swoole_js::js_back('删除成功！');						
			}
			//Error::dbd();
			$get['fid']  = empty($_GET['fid'])?0:(int)$_GET['fid'];						
			$get['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
			$get['pagesize'] = 15;
			$pager = null;
			$list = $_model->gets($get,$pager);
			$this->swoole->tpl->assign('list',$list);
			$this->swoole->tpl->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
			$this->swoole->tpl->display($config['tpl.list']);
		}
	}
}
?>