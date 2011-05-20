<?php
class Comet extends HttpServer implements Swoole_TCP_Server_Protocol
{
    public $config;
    public $server;

    function log($msg)
    {
        //echo $msg;
    }
    function onStart()
    {
        $this->log("server running\n");
    }
    function onConnect($client_id)
    {
        $this->log("connect me\n");
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
        $request = $this->request($data);
        $response = new Response;

        $response->head['Date'] = gmdate("D, d M Y H:i:s T");
        $response->head['Server'] = 'Swoole';
//        $response->head['KeepAlive'] = 'off';
//        $response->head['Connection'] = 'close';
       // $response->head['Content-type'] = 'application/json';
        //$response->body = $request->get['callback'].'('.json_encode(array('successful'=>true)).')';
        //$response->head['Content-Length'] = strlen($response->body);

        $out = $response->head();

        //$out .= $response->body;
        $this->server->send($client_id,$out);
        for($i=0;$i<10;$i++)
        {
            $this->server->send($client_id,'<script language="javascript">alert("msg on");</script>');
            sleep(1);
        }

        //处理data的完整性
        $this->server->close($client_id);
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


}
