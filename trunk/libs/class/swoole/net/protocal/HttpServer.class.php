<?php
class HttpServer implements Swoole_TCP_Server_Protocal
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
    function onRecive($client_id,$request)
    {
        $data = $this->config['request_call']($request,$response);

        $out  = "HTTP/1.1 200 OK\r\n";
        $out .= "Date: " . gmdate("D, d M Y H:i:s T")."\r\n";
        $out .= "Server: " . $_SERVER['software'] . "\r\n";
        $out .= "Content-type: ".$response['Content-Type']."\r\n";
        $out .= "Connection: close\r\n";
        //$out .= "Connection: Keep-Alive\r\n";
        $out .= "Cache-Control: no-store, no-cache, must-revalidate\r\n"; // HTTP/1.1
        $out .= "Cache-Control: post-check=0, pre-check=0\r\n";
        $out .= "Pragma: no-cache\r\n"; // HTTP/1.0
        $out .= "Content-Length: " .$response['Content-Length'] . "\r\n\r\n";
        $out .= $data;

        $this->server->send($client_id,$out);
        $this->server->close($client_id);
    }


    function onClose($client_id)
    {

    }
    function onShutdown()
    {
        echo "server shutdown\n";
    }

    function request()
    {

    }

    function response()
    {

    }
}
?>