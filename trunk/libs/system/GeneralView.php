<?php
/**
 * 通用试图类
 * 产生一个简单的请求控制，解析的结构，一般用于后台管理系统
 * 简单模拟List  delete  modify  add 4项操
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

	function handle_entity_list($config)
	{
		if(!isset($config['model'])) die('参数错误！');
		$_model = createModel($config['model']);

		if(isset($_GET['del']))
		{
			$del = (int)$_GET['del'];
			$_model->del($del);
			Swoole_js::js_back('删除成功！');
		}
		else
		{
			if(empty($config['tpl'])) $config['tpl'] = LIBPATH.'/data/tpl/admin_entity_list.html';
			$gets['page'] = empty($_GET['page'])?1:$_GET['page'];
			$gets['pagesize'] = 10;
			$pager=null;
			$list = $_model->gets($gets,$pager);

			$pager = array('total'=>$pager->total,'render'=>$pager->render());
			$this->swoole->tpl->assign('pager',$pager);
			$this->swoole->tpl->assign('list',$list);
			$this->swoole->tpl->display($config['tpl']);
		}
	}
	function handle_entity_op($config)
	{
		if(!isset($config['model'])) die('参数错误！');
		$_model = createModel($config['model']);

		if($_POST['job']=='push')
		{
			$digg = (int)$_POST['push'];
			$set['digest'] = $digg;
			$get['in'] = array('id',implode(',',$_POST['ids']));
			$_model->sets($set,$get);
			Swoole_js::js_parent_reload('推荐成功');
		}
	}
	function handle_entity_add($config)
	{
		if(!isset($config['model'])) die('参数错误！');
		if(empty($config['tpl.add'])) $config['tpl.add'] = LIBPATH.'/data/tpl/admin_entity_add.html';
		if(empty($config['tpl.modify'])) $config['tpl.modify'] = LIBPATH.'/data/tpl/admin_entity_modify.html';

		$_model = createModel($config['model']);

		if($_POST)
		{
			$this->proc_upfiles();
			if(!empty($_POST['id']))
			{
				//如果得到id，说明提交的是修改的操作
				$id = $_POST['id'];
				if($_model->set($_POST['id'],$_POST))
				{
					Swoole_js::js_back('修改成功',-2);
					exit;
				}
				else
				{
					Swoole_js::js_back('修改失败',-1);
					exit;
				}
			}
			else
			{
				//如果没得到id，说明提交的是添加操作
				if(empty($_POST['title']))
				{
					Swoole_js::js_back('标题不能为空！');
					exit;
				}
				$id = $_model->put($_POST);
				Swoole_js::js_back('添加成功');
				exit;
			}
		}
		else
		{
			$this->swoole->plugin->load('fckeditor');
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$news = $_model->get($id)->get();
				$editor = editor("content",$news['content'],480);
				$this->swoole->tpl->assign('editor',$editor);
				$this->swoole->tpl->assign('news',$news);
				$this->swoole->tpl->display($config['tpl.modify']);
			}
			else
			{
				$editor = editor("content","",480);
				$this->swoole->tpl->assign('editor',$editor);
				$this->swoole->tpl->display($config['tpl.add']);
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
				$data['pagename'] = trim($_POST['pagename']);
				$data['keywords'] = trim($_POST['keywords']);
				$data['fid'] = intval($_POST['fid']);
				$data['intro'] = trim($_POST['intro']);

				#增加
				if(empty($_POST['id']))
				{
					unset($_POST['id']);
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
				$data['pagename'] = trim($_POST['pagename']);
				$data['fid'] = intval($_POST['fid']);
				$data['intro'] = trim($_POST['intro']);
                $data['keywords'] = trim($_POST['keywords']);
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
					Swoole_js::js_back('修改成功！');
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
	/**
	 * 列表删除
	 * @return unknown_type
	 */
	function list_del()
	{

	}
	/**
	 * 详细与更新
	 * @return unknown_type
	 */
	function detail_update()
	{

	}
	/**
	 * 增加
	 * @return unknown_type
	 */
	function add()
	{

	}
	/**
	 * 批量操作
	 * @return unknown_type
	 */
	function lot()
	{

	}

	function handle_attachment($config)
	{
		if(!isset($config['entity']) or !isset($config['attach']) or !isset($config['entity_id'])) die('参数错误！');
		$_mm = createModel($config['entity']);
		$_ma = createModel($config['attach']);

		$this->swoole->tpl->assign('config',$config);
		if($_POST)
		{
			$_ma->put($_POST);
		}
		if(isset($_GET['del']))
		{
			$dels['id'] = (int) $_GET['del'];
			$dels['aid'] = $config['entity_id'];
			$dels['limit'] = 1;
			$_ma->dels($dels);
		}
		$get['aid'] = $config['entity_id'];
		$get['pagesize'] = 16;
		$get['page'] = empty($get['page'])?1:(int)$get['page'];
		$list = $_ma->gets($get,$pager);
		$this->swoole->tpl->assign('list',$list);
		$this->swoole->tpl->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
		if(empty($config['tpl.list'])) $config['tpl.list'] = LIBPATH.'/data/tpl/admin_attachment.html';
		$this->swoole->tpl->display($config['tpl.list']);
	}
}
?>