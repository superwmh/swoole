<?php
/**
 * Swoole系统核心类，外部使用全局变量$php引用
 * @package SwooleSystem
 * @author Tianfeng.Han
 *
 */
class Swoole extends ArrayObject
{
	var $db;
	var $tpl;
	var $cache;
	var $config;
	
	static $default_cache_life=600;
	var $pagecache;
	
	var $load;
	var $model;
	var $plugin;
	var $genv;
	var $env;
	
	function __construct()
	{
		$this->__init();
		parent::__construct();
		$this->load = new SwooleLoader($this);
		$this->model = new ModelLoader($this);
		$this->plugin = new PluginLoader($this);
		$this->genv = new SwooleEnv($this);		
	}
	/**
	 * 初始化环境
	 * @return unknown_type
	 */
	private function __init()
	{
		$this->env['runtime']['start'] = microtime(true);
		$this->env['runtime']['mem'] = memory_get_usage();
	}
	/**
	 * 自动导入模块
	 * @return None
	 */
	function autoload()
	{
		$autoload = func_get_args();
		foreach($autoload as $lib) $this->$lib = $this->load->loadLib($lib);
	}
	/**
	 * 加载config数据
	 * @return unknown_type
	 */
	function loadConfig()
	{
		$this->config = new SwooleConfig;
	}
	/**
	 * 运行MVC处理模型
	 * @param $url_processor
	 * @return None
	 */
	function runMVC($url_processor)
	{
		$url_func = 'url_process_'.$url_processor;
		if(!function_exists($url_func))
			Error::info('MVC Error!',"Url Process function not found!<p>\nFunction:$url_func");
		$mvc = call_user_func($url_func);
		$this->env['mvc'] = $mvc;
		$controller_path = APPSPATH.'/controllers/'.$mvc['controller'].'.php';
		if(!file_exists($controller_path))
		{
			header("HTTP/1.1 404 Not Found");
			Error::info('MVC Error',"Controller <b>{$mvc['controller']}</b> not exist!");
		}
		else require($controller_path);
		$controller = new $mvc['controller']($this);		
		if(!method_exists($controller,$mvc['view']))
		{
			header("HTTP/1.1 404 Not Found");
			Error::info('MVC Error!'.$this->view,"View <b>{$mvc['controller']}->{$mvc['view']}</b> Not Found!");
		}
		if($controller->is_ajax)
		{
			header('Cache-Control: no-cache, must-revalidate');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header('Content-type: application/json');
			$data = call_user_func(array($controller,$mvc['view']));
			if(DBCHARSET!='utf8')
			{
				import_func('array');
				$data = array_iconv(DBCHARSET , 'utf-8' , $data);
			}
			echo json_encode($data);
		}
		else echo call_user_func(array($controller,$mvc['view']));
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
		if(DBCHARSET!='utf8')
		{
			import_func('array');
			$data = array_iconv(DBCHARSET , 'utf-8' , $data);
		}
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
		call_user_func('admin_'.$action,$admin);
	}
}
?>