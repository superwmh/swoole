<?php
/**
 * Controller的基类，控制器基类
 * @package SwooleSystem
 * @subpackage MVC
 */
class Controller
{
    public $swoole;
    public $view;
    public $is_ajax = false;
    public $if_filter = true;
    public $session;
    public $session_open = false;

    protected $trace = array();
    protected $model;

    function __construct($swoole)
    {
        $this->swoole = $swoole;
        $this->model = $swoole->model;
        if($this->if_filter) Filter::request();
    }
    /**
     * 开启SESSION
     * @return unknown_type
     */
    function session_start()
    {
        //运行在传统环境下
        if(!isset($_SERVER['run_mode']))
        {
            session_start();
            return true;
        }
        if(empty($_COOKIE[Session::$sess_name]))
        {
            $sess_id = uniqid(RandomKey::string(Session::$sess_size-13));
            $this->response->setcookie(Session::$sess_name,$sess_id,time()+$_SERVER['session_cookie_life']);
        }
        else $sess_id = trim($_COOKIE[Session::$sess_name]);

        $session_cache = new Cache(SESSION_CACHE);
        Session::$cache_life = $_SERVER['session_life'];
        Session::$cache_prefix = Session::$sess_name;
        $sess = new Session($session_cache);
        $_SESSION = $this->request->session = $sess->load($sess_id);
        $this->session_open = true;
        $this->session = $sess;
    }
    /**
     * 跟踪信息
     * @param $title
     * @param $value
     * @return unknown_type
     */
    protected function trace($title,$value='')
    {
        if(is_array($title))
        {
            $this->trace = array_merge($this->trace,$title);
        }
        else
        {
            $this->trace[$title] = $value;
        }
    }
    /**
     * 显示运行时间和内存占用
     * @return unknown_type
     */
    protected function showTime()
    {
        $runtime = $this->swoole->runtime();
        // 显示运行时间
        $showTime = '执行时间: '.$runtime['time'];
        // 显示内存占用
        $showTime.= ' | 内存占用:'.$runtime['memory'];
        return $showTime;
    }
    /**
     * 显示跟踪信息
     * @param $detail
     * @return unknown_type
     */
    public function showTrace($detail=false)
    {
        $_trace =   array();
        $included_files = get_included_files();

        // 系统默认显示信息
        $_trace['请求脚本'] = $_SERVER['SCRIPT_NAME'];
        $_trace['请求方法'] = $_SERVER['REQUEST_METHOD'];
        $_trace['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $_trace['HTTP版本'] = $_SERVER['SERVER_PROTOCOL'];
        $_trace['请求时间'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);

        if(isset($_SESSION)) $_trace['SESSION_ID'] = session_id();
        $_trace['读取数据库'] = $this->swoole->db->read_times.'次';
        $_trace['写入数据库'] = $this->swoole->db->write_times.'次';

        $_trace['加载文件数目'] = count($included_files);
        $_trace['PHP执行占用'] = $this->showTime();
        $_trace = array_merge($this->trace,$_trace);

        // 调用Trace页面模板
        echo <<<HTMLS
<style type="text/css">
#swoole_trace_content  {
font-family:		Consolas, Courier New, Courier, monospace;
font-size:			14px;
background-color:	#fff;
margin:				40px;
color:				#000;
border:				#999 1px solid;
padding:			20px 20px 12px 20px;
}
</style>
	<div id="content">
		<fieldset id="querybox" style="margin:5px;">
		<div style="overflow:auto;height:300px;text-align:left;">
HTMLS;
        foreach ($_trace as $key=>$info)
        {
            echo $key.' : '.$info.BL;
        }
        if($detail)
        {
            //输出包含的文件
            echo '加载的文件',BL;
            foreach ($included_files as $file)
            {
                echo 'require '.$file,BL;
            }
        }
        echo "</div></fieldset></div>";
    }
}
?>