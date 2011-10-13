<?php
require_once LIBPATH.'/class/swoole/net/SwooleServer.class.php';
class EventTCP extends SwooleServer implements Swoole_TCP_Server_Driver
{
	/**
	 * Server Socket
	 * @var unknown_type
	 */
	public $base_event;
	public $server_event;
	public $server_sock;

	//最大连接数
	public $max_connect=1000;

	//客户端socket列表
	public $client_sock = array();
	//客户端数量
	public $client_num = 0;

	function __construct($host,$port,$timeout=30)
	{
		parent::__construct($host,$port,$timeout=30);
	}
	function init()
	{
		$this->base_event = event_base_new();
		$this->server_event = event_new();
	}
	/**
	 * 运行服务器程序
	 * @return unknown_type
	 */
	function run($num=1)
	{
		//初始化事件系统
		if(!($this->protocol instanceof Swoole_TCP_Server_Protocol))
		{
			return error(902);
		}
		$this->init();
		//建立服务器端Socket
		$this->server_sock = $this->create("tcp://{$this->host}:{$this->port}");

		//设置事件监听，监听到服务器端socket可读，则有连接请求
		event_set($this->server_event,$this->server_sock, EV_READ | EV_PERSIST, "sw_server_handle_connect",$this);
		event_base_set($this->server_event,$this->base_event);
		event_add($this->server_event);
		if(($num-1)>0) sw_spawn($num-1);
		$this->protocol->onStart();
		event_base_loop($this->base_event);
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
	function sendAll($client_id,$data)
	{
	    foreach($this->client_sock as $k=>$sock)
        {
            if($client_id and $k==$client_id) continue;
            $this->sendData($sock,$data);
        }
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
		$this->protocol->onShutdown();
	}
	/**
	 * 关闭某个客户端
	 * @return unknown_type
	 */
	function close($client_id)
	{
		sw_socket_close($this->client_sock[$client_id],$this->client_event[$client_id]);
		unset($this->client_sock[$client_id],$this->client_event[$client_id]);
		$this->protocol->onClose($client_id);
		$this->client_num--;
	}
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
	//接受连接
	$client_socket = stream_socket_accept($server_socket);
	//如果超过最大连接数，则拒绝请求
	if(count($server->client_sock)>$server->max_connect)
	{
		$server->sendData($client_socket,'Server is full!');
		sw_socket_close($client_socket);
		return false;
	}

	$client_id = (int)$client_socket;
	//加入到客户端socket列表
	$server->client_sock[$client_id] = $client_socket;
	stream_set_blocking($client_socket , 0);
	//新的事件监听，监听客户端发生的事件
	$client_event = event_new();
	event_set($client_event, $client_socket, EV_READ | EV_PERSIST, "sw_server_handle_receive", array($server,$client_id));
	//设置基本时间系统
	event_base_set($client_event,$server->base_event);
	//加入事件监听组
	event_add($client_event);
	$server->client_sock[$client_id] = $client_socket;
	$server->client_event[$client_id] = $client_event;
	$server->protocol->onConnect($client_id);
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
	$data = sw_fread_stream($client_socket,$server->buffer_size);

	if($data !== false && $data !='')
	{
		$server->protocol->onRecive($client_id,$data);
	}
	else
	{
		$server->close($client_id);
	}
}
