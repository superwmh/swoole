<?php
require 'SwooleServer.php';
abstract class TCPServer extends SwooleServer
{
    /**
     * Server Socket
     * @var unknown_type
     */
	var $server_sock;
	//最大连接数
	var $max_connect=100;

    //客户端socket列表
	var $client_sock = array();
	//客户端数量
	var $client_num = 0;

	function __construct($host,$port,$timeout=30)
	{
		parent::__construct($host,$port,$timeout=30);
	}

    /**
     * 运行服务器程序
     * @return unknown_type
     */
	function run()
	{
	    //初始化事件系统
		$this->init();
		//建立服务器端Socket
		$this->server_sock = $this->create("tcp://{$this->host}:{$this->port}");

		//设置事件监听，监听到服务器端socket可读，则有连接请求
		event_set($this->server_event,$this->server_sock, EV_READ | EV_PERSIST, "sw_server_handle_connect",$this);
		event_base_set($this->server_event,$this->base_event);
		event_add($this->server_event);
		$this->onStart();
		event_base_loop($this->base_event);
	}
	/**
	 * 关闭服务器程序
	 * @return unknown_type
	 */
	function shutdown()
	{
	    //关闭所有客户端
	    foreach($this->client_sock as $k=>$sock)
	    {
	        sw_socket_close($sock,$this->client_event[$k]);
	    }
	    //关闭服务器端
	    sw_socket_close($this->server_sock,$this->server_event);
	    //关闭事件循环
        event_base_loopexit($this->base_event);
        $this->onShutdown();
	}
	/**
	 * 关闭某个客户端
	 * @return unknown_type
	 */
	function close($client_id)
	{
        sw_socket_close($this->client_sock[$client_id],$this->client_event[$client_id]);
        unset($this->client_sock[$client_id],$this->client_event[$client_id]);
        $this->onClose($client_id);
	}
	/**
	 * 接收到数据后回调函数
	 * @param $client_sock
	 * @param $data
	 * @return unknown_type
	 */
	abstract protected function onRecive($client_id,$data);
	protected function onConnect($client_id){}
	protected function onShutdown(){}
	protected function onClose($client_id){}
    protected function onStart(){}
}
/**
 * 处理客户端连接请求
 * @param $server_socket
 * @param $events
 * @param $server
 * @return unknown_type
 */
function sw_server_handle_connect($server_socket,$events,$server)
{
    //如果超过最大连接数，则拒绝请求
	if(count($server->client_sock)>$server->max_connect) return false;
	//接受连接
	$client_socket = stream_socket_accept($server_socket);
	stream_set_blocking($client_socket,0);
	echo stream_socket_get_name($client_socket,true)," is connected!\n";

	//加入到客户端socket列表
	$server->client_sock[$server->client_num] = $client_socket;
	stream_set_blocking($client_socket , 0);
	//新的事件监听，监听客户端发生的事件
	$client_event = event_new();
	event_set($client_event, $client_socket, EV_READ | EV_PERSIST, "sw_server_handle_receive", array($server,$server->client_num));
	//设置基本时间系统
	event_base_set($client_event,$server->base_event);
	//加入事件监听组
	event_add($client_event);
	$server->client_event[$server->client_num] = $client_event;
	$server->onConnect($server->client_num);
	$server->client_num++;
}
/**
 * 接收到数据后进行处理
 * @param $client_socket
 * @param $events
 * @param $arg
 * @return unknown_type
 */
function sw_server_handle_receive($client_socket,$events,$arg)
{
	$server = $arg[0];
	$client_id = $arg[1];
	$data = fread($client_socket,$server->buffer_size);

	if($data !== false && $data !='')
	{
		$server->onRecive($client_id,$data);
	}
	else
	{
	    $server->close($client_id);
	}
}
