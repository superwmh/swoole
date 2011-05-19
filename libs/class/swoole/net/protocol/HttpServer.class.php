<?php
require_once LIBPATH.'/system/Request.php';
require_once LIBPATH.'/system/Response.php';
class HttpServer implements Swoole_TCP_Server_Protocol
{
    public $config;
    public $server;
    public $request_process;

    function __construct($func='http_request_process')
    {
        $this->request_process = $func;
    }

    function log($msg)
    {
        //echo $msg;
    }

    function onStart()
    {
        echo "server running\n";
    }
    function onConnect($client_id)
    {

    }
    /**
     * 接收到数据
     * @param $client_id
     * @param $data
     * @return unknown_type
     */
    function onRecive($client_id,$data)
    {
        $this->log($data);
        //处理data的完整性
        $request = $this->request($data);
        $call_func = $this->request_process;
        $response = $call_func($request);
        $this->response($client_id,$response);
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
            //var_dump($parts[0]);

            $head = $this->parse_head(explode("\r\n",$parts[0]));
            //var_dump($head);
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
                ;
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
     * 处理请求
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

    function onClose($client_id)
    {

    }
    function onShutdown()
    {
        echo "server shutdown\n";
    }

    function response($client_id,$response)
    {
        $response->head['Date'] = gmdate("D, d M Y H:i:s T");
        $response->head['Server'] = $_SERVER['server_software'];
        $response->head['KeepAlive'] = 'off';
        $response->head['Connection'] = 'close';
        $response->head['Content-Length'] = strlen($response->body);

        $out = $response->head();
        $this->log($out);
        $out .= $response->body;

        $this->server->send($client_id,$out);
        $this->server->close($client_id);
    }
}
