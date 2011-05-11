<?php
/**
 * Swoole系统核心类，外部使用全局变量$php引用
 * Swoole框架系统的核心类，提供一个swoole对象引用树和基础的调用功能
 * @package SwooleSystem
 * @author Tianfeng.Han
 * @subpackage base
 */
class Swoole extends ArrayObject
{
    public $db;
    public $tpl;
    public $cache;
    public $event;
    public $config;

    static $default_cache_life=600;
    public $pagecache;
    /**
     * 发生错误时的回调函数
     * @var unknown_type
     */
    public $error_callback;

    public $load;
    public $model;
    public $plugin;
    public $genv;
    public $env;

    function __construct()
    {
        if(!defined('DEBUG')) define('DEBUG','off');
        if(DEBUG=='off') error_reporting(0);
        else error_reporting(E_ALL);
        $this->__init();
        parent::__construct();
        $this->load = new SwooleLoader($this);
        $this->model = new ModelLoader($this);
        $this->plugin = new PluginLoader($this);
    }
    function __release()
    {
        if($this->db instanceof Database) $this->db->close();
        unset($this->tpl);
        unset($this->cache);
    }
    /**
     * 获取资源消耗
     * @return unknown_type
     */
    function runtime()
    {
        // 显示运行时间
        $return['time'] = number_format((microtime(true)-$this->env['runtime']['start']),4).'s';

        $startMem =  array_sum(explode(' ',$this->env['runtime']['mem']));
        $endMem   =  array_sum(explode(' ',memory_get_usage()));
        $return['memory'] = number_format(($endMem - $startMem)/1024).'kb';
        return $return;
    }
    /**
     * 压缩内容
     * @return unknown_type
     */
    function gzip()
    {
        //不要在文件中加入UTF-8 BOM头
        //ob_end_clean();
        ob_start("ob_gzhandler");
        #是否开启压缩
        if(function_exists('ob_gzhandler')) ob_start('ob_gzhandler');
        else ob_start();
    }
    /**
     * 初始化环境
     * @return unknown_type
     */
    function __init()
    {
        #记录运行时间和内存占用情况
        $this->env['runtime']['start'] = microtime(true);
        $this->env['runtime']['mem'] = memory_get_usage();
        #捕获错误信息
        if(DEBUG=='on') set_error_handler('swoole_error_handler');
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
    function loadConfig($static = true)
    {
        if($static) $this->config = new ArrayObject;
        else $this->config = new SwooleConfig;
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
        if(!preg_match('/^[a-z0-9_]+$/i',$mvc['controller'])) exit;
        if(!preg_match('/^[a-z0-9_]+$/i',$mvc['view'])) exit;
        $this->env['mvc'] = $mvc;
        $controller_path = APPSPATH.'/controllers/'.$mvc['controller'].'.php';
        if(!is_file($controller_path))
        {
            header("HTTP/1.1 404 Not Found");
            Error::info('MVC Error',"Controller <b>{$mvc['controller']}</b> not exist!");
        }
        else require($controller_path);
        if(!class_exists($mvc['controller']))
        {
            Error::info('MVC Error',"Controller Class <b>{$mvc['controller']}</b> not exist!");
        }
        $controller = new $mvc['controller']($this);
        if(!method_exists($controller,$mvc['view']))
        {
            header("HTTP/1.1 404 Not Found");
            Error::info('MVC Error!'.$mvc['view'],"View <b>{$mvc['controller']}->{$mvc['view']}</b> Not Found!");
        }
        if(empty($mvc['param'])) $param = array();
        else $param = $mvc['param'];

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
        else echo call_user_func(array($controller,$mvc['view']),$param);
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
        header('Cache-Control: no-cache, must-revalidate');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-type: application/json');

        $method = $_GET['method'];
        $data = call_user_func($method);
        if(DBCHARSET!='utf8')
        {
            $data = Swoole_tools::array_iconv(DBCHARSET , 'utf-8' , $data);
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

    function runServer()
    {
        if(empty($_SERVER['run_mode'])) $_SERVER['run_mode'] = 'server';
        if(empty($_SERVER['server_driver'])) $_SERVER['server_driver'] = 'SelectTCP'; //BlockTCP,EventTCP,SelectTCP
        if(empty($_SERVER['server_host'])) $_SERVER['server_host'] = '0.0.0.0';
        if(empty($_SERVER['server_port'])) $_SERVER['server_port'] = 8888;
        if(empty($_SERVER['server_processor_num'])) $_SERVER['server_processor_num'] = 1;   //启用的进程数目
        if(empty($_SERVER['session_cookie_life'])) $_SERVER['session_cookie_life'] = 86400; //保存SESSION_ID的cookie存活时间
        if(empty($_SERVER['session_life'])) $_SERVER['session_life'] = 1800;        //Session在Cache中的存活时间

        import('#net.driver.'.$_SERVER['server_driver']);
        import('#net.protocal.HttpServer');

        $server = new $_SERVER['server_driver']($_SERVER['server_host'],$_SERVER['server_port'],60);
        $server->setProtocol(new HttpServer);
        $server->run($_SERVER['server_processor_num']);
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