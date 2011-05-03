<?php
require_once LIBPATH.'/system/Request.php';
require_once LIBPATH.'/system/Response.php';
class HttpServer implements Swoole_TCP_Server_Protocol
{
    public $config;
    public $server;

    function __construct($config)
    {
        $this->config = $config;
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
        $request = $this->request($data);
        $response = $this->config['request_call']($request);
        $this->response($client_id,$response);
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
            $_cookies[$key] = $value;
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
        $parts = explode("\r\n\r\n", $data);
        // parts[0] = HTTP头;
        // parts[1] = HTTP主体，GET请求没有body
        $headerLines = explode("\r\n", $parts[0]);
        $request = new Request;

        $_headers = array();
        foreach ($headerLines as $k=>$head)
        {
            $head = trim($head);
            //请求第一行，方法，路径，协议[RFC-2616 5.1]
            if($k==0)
            {
                list ($request->meta['method'],$request->meta['uri'],$request->meta['protocol']) = explode(" ",$head);
                continue;
            }
            if($head!=='')
            {
                list($key, $value) = explode (":", $header);
                $request->head[$key] = trim($value);
            }
        }
        $url_info = parse_url($request->meta['uri']);
        $request->meta['path'] = $url_info['path'];
        $request->meta['fragment'] = $info['fragment'];
        parse_str ($url_info['query'],$request->get);

        //POST请求带有http bod
        if($request->meta['method']==="POST") parse_str($parts[1], $request->post);
        //解析Cookies
        if(!empty($request->head['Cookie'])) $request->cookie = $this->parse_cookie($request['Cookie']);
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
        $out  = "HTTP/1.1 200 OK\r\n";
        $out .= "Date: " . gmdate("D, d M Y H:i:s T")."\r\n";
        $out .= "Server: " . $_SERVER['software'] . "\r\n";
        $out .= "Content-type: ".$response->head['Content-Type']."\r\n";
        $out .= "Connection: close\r\n";
        //$out .= "Connection: Keep-Alive\r\n";
        $out .= "Cache-Control: no-store, no-cache, must-revalidate\r\n"; // HTTP/1.1
        $out .= "Cache-Control: post-check=0, pre-check=0\r\n";
        $out .= "Pragma: no-cache\r\n"; // HTTP/1.0
        $out .= "Content-Length: " .$response->head['Content-Length'] . "\r\n\r\n";
        $out .= $response->body;

        $this->server->send($client_id,$out);
        $this->server->close($client_id);
    }
}
?>