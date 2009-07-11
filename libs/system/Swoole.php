<?php
/**
 * Swoole系统核心类，外部使用全局变量$php引用
 * @package SwooleSystem
 * @author Tianfeng.Han
 *
 */
class Swoole extends SwooleObject
{
	var $load;
	var $db;
	var $tpl;
	var $auth;
	var $config;
	var $cache;
	var $pagecache;
	
	var $model;
	
	var $env;
	
	function __construct()
	{
		$this->load = new Swoole_register($this);
		$this->model = new SwooleObject;
	}
	/**
	 * 运行MVC处理模型
	 * @param $url_processor
	 * @return unknown_type
	 */
	function runMVC($url_processor='mvc_default')
	{
		namespace('url_process');
		$url_func = 'url_process_'.$url_processor;
		if(!function_exists($url_func))
			Error::info('MVC Error!','Url Process function not found!<br/>Please check <b>"./libs/function/url_process.php"</b>,'."<br/>\nFunction:$url_func");
		$resources = call_user_func($url_func);
		foreach($resources as $property=>$ressource)
		{
			$this->$property = $ressource;
		}

		$controller = $this->createController($this->controller);
		if(!method_exists($controller,$this->view)) Error::info('没有视图'.$this->view,'不存在的视图方法，请检查您的应用程序！');
		echo call_user_method($this->view,$controller,$_GET);
	}
	
	function runAjax()
	{
		if(empty($_GET['method'])) return;
		$method = $_GET['method'];
		if(!function_exists($method))
		{
			echo 'Error: Function not found!';
			exit;
		}
		$data = call_user_func($method);
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Content-type: application/json');
		
		$method = $_GET['method'];
		$data = call_user_func($method);
		
//		if(DBCHARSET!='utf8')
//		{
//			namespace('array');
//			$data = array_iconv(DBCHARSET , 'utf-8' , $data);
//		}
		echo json_encode($data);
	}
	
	function runView($pagecache=false)
	{
		if($pagecache)
		{
			//echo '启用缓存';
			$cache = new Swoole_pageCache(3600);
			if($cache->isCached())
			{
				//echo '调用缓存';
				$cache->load();
			}
			else
			{
				//echo '没有缓存，正在建立缓存';
				$view = isset($_GET['view'])?$_GET['view']:'index';
				foreach($_GET as $key=>$param)
					$this->tpl->assign($key,$param);
				$cache->create($this->tpl->fetch($view.'.html'));
				$this->tpl->display($view.'.html');
			}			
		}
		else
		{
			//echo '不启用缓存';
			$view = isset($_GET['view'])?$_GET['view']:'index';
			foreach($_GET as $key=>$param)
				$this->tpl->assign($key,$param);
			$this->tpl->display($view.'.html');
		}
	}
	
	function runAdmin($admin_do)
	{
		require(LIBPATH."/admin/$admin_do.admin.php");
		$classname = $admin_do.'Admin';
		$admin = new $classname($this);
		$action = isset($_GET['action'])?$_GET['action']:'list';
		call_user_method('admin_'.$action,$admin);
	}

	public function loadlibs($libs)
	{
		$_libs=explode(",",$libs);
		foreach($_libs as $lib) $this->load->get($lib);
	}
	
	public function loadConfig()
	{
		$this->config = $this->createModel('Config');
	}
	
	public function create($name)
	{
		$name=str_replace(".","/",$name);
		$classinfo=explode("/",$name);
		$classname=$classinfo[count($classinfo)-1];
		if(!self::loadfile($name)) throw new Exception('Class not found!');
		if(func_num_args()!=1)
		{
			$args=func_get_args();
			for($i=1;$i<count($args);$i++) $el[]='$args['.$i.']';
			$object=eval("return new $classname(".implode(",",$el).");");
			return $object;
		}
		else return new $classname;
	}
	/**
	 * 产生一个模型对象
	 * @param $model_name 模型名称
	 * @return $model_object 模型对象
	 */
	function createModel($model)
	{
		if(!class_exists($model)) require(APPSPATH.'/models/'.$model.'.model.php');
		if(!is_object($this->db)) $this->load->get('db');
		if(!is_object($this->model->$model))
			$this->model->$model = new $model($this->db);
		return $this->model->$model;
	}
	
	/**
	 * 产生一个控制器对象
	 * @param $controller_name 控制器名称
	 * @return $controller_object 控制器对象
	 */
	function createController($controller)
	{
		$controller_path = APPSPATH.'/controllers/'.$controller.'.php';
		if(!file_exists($controller_path)) Error::info('MVC Error',"Controller <b>$controller</b> not exist!");
		else require_once($controller_path);
		return new $controller($this);
	}
}
?>