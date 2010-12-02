<?php
class SwooleServer
{
    var $base_event;
    var $server_event;
    var $client_event = array();
    var $proc = null;

    var $host = '0.0.0.0';
    var $port;
    var $timeout;

    public $buffer_size = 1024;

    function __construct($host,$port,$timeout=30)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    function onError($errno,$errstr)
    {
        exit("$errstr ($errno)");
    }

    function init()
    {
        $this->base_event = event_base_new();
        $this->server_event = event_new();
    }
    /**
     * 创建一个Server Socket
     * @param $uri
     * @return unknown_type
     */
    function create($uri,$block=0)
    {
        //UDP
        if($uri{0}=='u') $socket = stream_socket_server($uri,$errno,$errstr,STREAM_SERVER_BIND);
        //TCP
        else $socket = stream_socket_server($uri,$errno,$errstr);

        if(!$socket) $this->onError($errno,$errstr);
        //设置socket为非堵塞或者阻塞
        stream_set_blocking($socket,$block);
        return $socket;
    }
}
/**
 * 关闭socket
 * @param $socket
 * @param $event
 * @return unknown_type
 */
function sw_socket_close($socket,$event)
{
    event_del($event);
    event_free($event);
    stream_socket_shutdown($socket,STREAM_SHUT_RDWR);
    fclose($socket);
}
?>
