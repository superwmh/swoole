<?php
require_once LIBPATH.'/class/swoole/net/SwooleServer.class.php';
require_once LIBPATH.'/system/Request.php';
require_once LIBPATH.'/system/Response.php';
/**
 * HTTP Server
 * @author Tianfeng.Han
 * @link http://www.swoole.com/
 * @package Swoole
 * @subpackage net.protocol
 */
class HttpServer implements Swoole_TCP_Server_Protocol
{
    public $server;
    public $config;
    public $default_port = 80;
    public $default_page = 'index.html';

    public $mime_types;
    public $dynamic_only = false;
    public $static_dir;
    public $static_ext;
    public $dynamic_ext;
    public $deny_dir;

    function __construct($ini_file)
    {
        define('SWOOLE_SERVER',true);
        import_func('compat');
        require(LIBPATH.'/data/mimes.php');
        $this->mime_types = array_flip($mimes);
        $this->load_setting($ini_file);
    }
    function log($msg)
    {
        echo $msg,NL;
    }
    function onStart()
    {
        echo "server running\n";
    }
    function onShutdown()
    {
        echo "server shutdown\n";
    }
    function onConnect($client_id)
    {

    }
    function onClose($client_id)
    {

    }
    private function load_setting($ini_file)
    {
        if(!is_file($ini_file)) exit("Swoole AppServer配置文件错误($ini_file)\n");
        $config = parse_ini_file($ini_file,true);
        /*--------------Server------------------*/
        if(empty($config['server']['driver'])) $config['server']['driver'] = 'SelectTCP'; //BlockTCP,EventTCP,SelectTCP
        if(empty($config['server']['software'])) $config['server']['software'] = $_SERVER['server_software'];
        if(empty($config['server']['host'])) $config['server']['host'] = '0.0.0.0';
        if(empty($config['server']['port'])) $config['server']['port'] = 8888;
        if(empty($config['server']['processor_num'])) $config['server']['processor_num'] = 1;   //启用的进程数目
        /*--------------Session------------------*/
        if(empty($config['session']['cookie_life'])) $config['session']['cookie_life'] = 86400; //保存SESSION_ID的cookie存活时间
        if(empty($config['session']['session_life'])) $config['session']['session_life'] = 1800;        //Session在Cache中的存活时间
        if(empty($config['session']['cache_url'])) $config['session']['cache_url'] = 'file://localhost#sess';        //Session在Cache中的存活时间
        /*--------------Apps------------------*/
        if(empty($config['apps']['url_route'])) $config['apps']['url_route'] = 'url_route_default';
        if(empty($config['apps']['auto_reload'])) $config['apps']['auto_reload'] = 0;
        if(empty($config['apps']['charset'])) $config['apps']['charset'] = 'utf-8';
        /*--------------Access------------------*/
        $this->deny_dir = array_flip(explode(',',$config['access']['deny_dir']));
        $this->static_dir = array_flip(explode(',',$config['access']['static_dir']));
        $this->static_ext = array_flip(explode(',',$config['access']['static_ext']));
        $this->dynamic_ext = array_flip(explode(',',$config['access']['dynamic_ext']));
        /*-----set----*/
        $this->config = $config;
    }
    /**
     * 接收到数据
     * @param $client_id
     * @param $data
     * @return unknown_type
     */
    function onRecive($client_id,$data)
    {
        //检测request data完整性（暂无）
        //解析请求
        $request = $this->request($data);
        //处理请求，产生response对象
        $response = $this->process_request($request);
        //发送response
        $this->response($client_id,$response);
        //回收内存
        unset($data);
        $request->unsetGlobal();
        unset($request);
        unset($response);
    }
    /**
     * 解析form_data格式文件
     * @param $part
     * @param $request
     * @param $cd
     * @return unknown_type
     */
    function parse_form_data($part,&$request,$cd)
    {
        $cd = '--'.str_replace('boundary=','',$cd);
        $form = explode($cd,$part);
        foreach($form as $f)
        {
            if($f==='') continue;
            $parts = explode("\r\n\r\n",$f);
            $head = $this->parse_head(explode("\r\n",$parts[0]));
            if(!isset($head['Content-Disposition'])) continue;
            $meta = $this->parse_cookie($head['Content-Disposition']);
            if(!isset($meta['filename']))
            {
                //checkbox
                if(substr($meta['name'],-2)==='[]') $request->post[substr($meta['name'],0,-2)][] = trim($parts[1]);
                else $request->post[$meta['name']] = trim($parts[1]);
            }
            else
            {
                $file = trim($parts[1]);
                $tmp_file = tempnam('/tmp','sw');
                file_put_contents($tmp_file,$file);
                if(!isset($meta['name'])) $meta['name']='file';
                $request->file[$meta['name']] = array('name'=>$meta['filename'],
                			'type'=>$head['Content-Type'],
                			'size'=>strlen($file),
                			'error'=>UPLOAD_ERR_OK,
                			'tmp_name'=>$tmp_file);
            }
        }
    }
    /**
     * 头部解析
     * @param $headerLines
     * @return unknown_type
     */
    function parse_head($headerLines)
    {
        $header = array();
        foreach($headerLines as $k=>$head)
        {
            $head = trim($head);
            if(empty($head)) continue;
            list($key, $value) = explode(':', $head);
            $header[trim($key)] = trim($value);
        }
        return $header;
    }
    /**
     * 解析Cookies
     * @param $cookies
     * @return unknown_type
     */
    function parse_cookie($cookies)
    {
        $_cookies = array();
        $blocks = explode (";", $cookies);
        foreach ($blocks as $cookie)
        {
            list ($key, $value) = explode("=", $cookie);
            $_cookies[trim($key)] = trim($value,"\r\n \t\"");
        }
        return $_cookies;
    }
    /**
     * 解析请求
     * @param $data
     * @return unknown_type
     */
    function request($data)
    {
        $parts = explode("\r\n\r\n", $data,2);
        // parts[0] = HTTP头;
        // parts[1] = HTTP主体，GET请求没有body
        $headerLines = explode("\r\n", $parts[0]);

        $request = new Request;
        // HTTP协议头,方法，路径，协议[RFC-2616 5.1]
        list($request->meta['method'],$request->meta['uri'],$request->meta['protocol']) = explode(' ',$headerLines[0]);
        //错误的HTTP请求
        if(empty($request->meta['method']) or empty($request->meta['uri']) or empty($request->meta['protocol']))
        {
        	return false;
        }
        unset($headerLines[0]);
        //解析Head
        $request->head = $this->parse_head($headerLines);
        $url_info = parse_url($request->meta['uri']);
        $request->meta['path'] = $url_info['path'];
        $request->meta['fragment'] = $info['fragment'];
        parse_str($url_info['query'],$request->get);
        //POST请求,有http body
        if($request->meta['method']==='POST')
        {
            $cd = strstr($request->head['Content-Type'],'boundary');
            if(isset($request->head['Content-Type']) and $cd!==false) $this->parse_form_data($parts[1],$request,$cd);
            else parse_str($parts[1], $request->post);
        }
        //解析Cookies
        if(!empty($request->head['Cookie'])) $request->cookie = $this->parse_cookie($request->head['Cookie']);
        return $request;
    }
    /**
     * 发送响应
     * @param $client_id
     * @param $response
     * @return unknown_type
     */
    function response($client_id,$response)
    {
        if(!isset($response->head['Date'])) $response->head['Date'] = gmdate("D, d M Y H:i:s T");
        if(!isset($response->head['Server'])) $response->head['Server'] = $this->config['server']['software'];
        if(!isset($response->head['KeepAlive'])) $response->head['KeepAlive'] = 'off';
        if(!isset($response->head['Connection'])) $response->head['Connection'] = 'close';
        if(!isset($response->head['Content-Length'])) $response->head['Content-Length'] = strlen($response->body);

        $out = $response->head();
        $out .= $response->body;
        $this->server->send($client_id,$out);
        //$this->server->close($client_id);
    }
    function http_error($code,$response,$content='')
    {
        $response->send_http_status($code);
        $response->head['Content-Type'] = 'text/html';
        $response->body = Error::info(Response::$HTTP_HEADERS[$code],"<p>$content</p><hr><address>{$this->config['server']['software']} Server at {$this->server->host} Port {$this->server->port}</address>");
    }
    /**
     * 处理请求
     * @param $request
     * @return unknown_type
     */
    function process_request($request)
    {
        $request->setGlobal();
        $response = new Response;
        //仅有动态请求
        if($this->dynamic_only)
        {
            $this->process_dynamic($request,$response);
            return $response;
        }
        //请求路径
        $path = explode('/',trim($request->meta['path'],'/'));
        if(empty($path)) $request->meta['path'] = $this->default_page;
        //扩展名
        $ext_name = Upload::file_ext($request->meta['path']);
        /* 检测是否拒绝访问 */
        if(isset($this->deny_dir[$path[0]]))
        {
            $this->http_error(403,$response,"服务器拒绝了您的访问({$request->meta['path']})！");
        }
        /* 是否静态目录 */
        elseif(isset($this->static_dir[$path[0]]) or isset($this->static_ext[$ext_name]))
        {
            $this->process_static($request,$response);
        }
        /* 动态脚本 */
        elseif(isset($this->dynamic_ext[$ext_name]) or empty($ext_name))
        {
            $this->process_dynamic($request,$response);
        }
        else
        {
            $this->http_error(403,$response);
        }
        return $response;
    }
    /**
     * 静态请求
     * @param $request
     * @param $response
     * @return unknown_type
     */
    function process_static(&$request,&$response)
    {
        $path = realpath(WEBPATH.'/'.$request->meta['path']);
        if(is_file($path))
        {
            $response->head['Content-Type'] = $mime_types[$ext_name];
            $response->body = file_get_contents($path);
        }
        else $this->http_error(404,$response,"文件不存在({$request->meta['path']})！");
    }
    /**
     * 动态请求
     * @param $request
     * @param $response
     * @return unknown_type
     */
    function process_dynamic(&$request,&$response)
    {
        $path = realpath(WEBPATH.'/'.$request->meta['path']);
        if(is_file($path))
        {
            $request->setGlobal();
            ob_start();
            try
            {
                include $path;
            }
            catch(Exception $e)
            {
                $response->send_http_status(404);
                $response->head['Content-Type'] = 'text/html';
                $response->body = $e->getMessage().'!<br /><h1>'.$this->config['server']['software'].'</h1>';
            }
            $response->body = ob_get_contents();
            ob_end_clean();
        }
        else $this->http_error(404,$response,"页面不存在({$request->meta['path']})！");
    }
}
