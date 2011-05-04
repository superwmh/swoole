<?php
require LIBPATH.'/class/swoole/net/SwooleServer.class.php';
class SelectTCP extends SwooleServer implements Swoole_TCP_Server_Driver
{
    public $server_sock;
    public $server_socket_id;

    //最大连接数
    public $max_connect=1000;
    /**
     * 文件描述符
     * @var unknown_type
     */
    public $fds;
    //客户端socket列表
    public $client_sock = array();
    //客户端数量
    public $client_num = 0;

    function __construct($host,$port,$timeout=30)
    {
        parent::__construct($host,$port,$timeout);
    }
    /**
     * 向client发送数据
     * @param $client_id
     * @param $data
     * @return unknown_type
     */
    function send($client_id,$data)
    {
        $this->sendData($this->client_sock[$client_id],$data);
    }
    /**
     * 向所有client发送数据
     * @return unknown_type
     */
    function sendAll($client_id=null,$data)
    {
        foreach($this->client_sock as $k=>$sock)
        {
            if($client_id and $k==$client_id) continue;
            $this->sendData($sock,$data);
        }
    }

    function shutdown()
    {
        //关闭所有客户端
        foreach($this->client_sock as $k=>$sock)
        {
            sw_socket_close($sock,$this->client_event[$k]);
        }
        //关闭服务器端
        sw_socket_close($this->server_sock,$this->server_event);
        $this->protocol->onShutdown();
    }

    function close($client_id)
    {
        sw_socket_close($this->client_sock[$client_id]);
        $this->client_sock[$id] = null;
        $this->fds[$client_id] = null;
        unset($this->client_sock[$client_id],$this->fds[$client_id]);
        $this->protocol->onClose($client_id);
        $this->client_num--;
    }

    function server_loop()
    {
        while(true)
        {
            $read_fds = $this->fds;
            if(stream_select($read_fds , $write = null , $exp = null , null))
            {
                foreach($read_fds as $socket)
                {
                    $socket_id = (int)$socket;
                    if($socket_id == $this->server_socket_id)
                    {
                        $client_socket = stream_socket_accept($this->server_sock);
                        $client_socket_id = (int)$client_socket;
                        stream_set_blocking($client_socket,0);
                        $this->client_sock[$client_socket_id] = $client_socket;
                        $this->fds[$client_socket_id] = $client_socket;
                        $this->client_num++;

                        if($this->client_num > $this->max_connect) $this->close($socket_id);
                        else $this->protocol->onConnect();
                    }
                    else
                    {
                        $data = fread($socket,$this->buffer_size);
                        if($data !== false && $data !='')
                        {
                            $this->protocol->onRecive($socket_id,$data);
                        }
                        else
                        {
                            $this->close($socket_id);
                            $this->protocol->onClose($socket_id);
                        }
                    }
                }                
            }      
        }
    }

    function run($num=1)
    {
        //初始化事件系统
        if(!($this->protocol instanceof Swoole_TCP_Server_Protocol))
        {
            return error(902);
        }
        //建立服务器端Socket
        $this->server_sock = $this->create("tcp://{$this->host}:{$this->port}");
        $this->server_socket_id = (int)$this->server_sock;
        $this->fds[$this->server_socket_id] = $this->server_sock;
        stream_set_blocking($this->server_sock , 0);
	    if(($num-1)>0) sw_spawn($num-1);
        $this->protocol->onStart();
        $this->server_loop();
    }
}
