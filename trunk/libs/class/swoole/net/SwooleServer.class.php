<?php
class SwooleServer
{
    public $proc = null;
    public $host = '0.0.0.0';
    public $port;
    public $timeout;

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
     * 创建一个Stream Server Socket
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
    function create_socket($uri,$block=false)
    {
        $set = parse_url($uri);
        if($uri{0}=='u') $sock = socket_create(AF_INET, SOCK_DGRAM , SOL_UDP);
        else $sock = socket_create(AF_INET, SOCK_STREAM , SOL_TCP);

        if($block) socket_set_block($sock);
        else socket_set_nonblock($sock);
        socket_bind($sock,$set['host'],$set['port']);
        socket_listen($sock);
        return $socket;
    }
    function sendData($sock,$data)
    {
        sw_fwrite_stream($sock,$data);
    }
    function log($log)
    {
        //echo $log;
    }
}

function sw_fwrite_stream($fp, $string)
{
    $length = strlen($string);
    for($written = 0; $written < $length; $written += $fwrite)
    {
        $fwrite = fwrite($fp, substr($string, $written));
        if ($fwrite === false) {
            return $written;
        }
    }
    return $written;
}

function sw_spawn($num)
{
    $pids = array();
    for($i=0;$i<$num;$i++)
    {
        $pid = pcntl_fork();
        if($pid) $pids[] = $pid;
        else break;
    }
    return $pids;
}
interface Swoole_TCP_Server_Driver
{
    function run();
    function send($client_id,$data);
    function close($client_id);
    function shutdown();
    function setProtocal($protocal);
}
interface Swoole_UDP_Server_Driver
{

}
interface Swoole_TCP_Server_Protocal
{
    function onStart();
    function onConnect($client_id);
    function onRecive($client_id,$data);
    function onClose($client_id);
    function onShutdown();
}

interface Swoole_UDP_Server_Protocal
{
    function onStart();
    function onData();
    function onShutdown();
}
