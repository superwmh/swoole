<?php
require 'TCPServer.php';

class MyServer extends TCPServer
{
    function onRecive($client_id,$data)
    {
        $data = trim($data);
		if($data=="quit")
		{
			$this->close($client_id);
			return true;
		}
		elseif($data=='shutdown')
		{
		    $this->shutdown();
		}
		else
		{
		    $client_socket_name =  stream_socket_get_name($this->client_sock[$client_id],true);
    		echo "Server send response data to client $client_socket_name\n";
    		$send = date('Y-m-d H:i:s')."$client_socket_name said:$data\n";
    		$this->sendAll($client_id,$send);
		}
    }
    /**
     * 发送到所有客户端
     * @param $data
     * @return unknown_type
     */
	function sendAll($client_id,$data)
	{
	    foreach($this->client_sock as $k=>$sock)
	    {
	        if($k==$client_id) continue;
	        fwrite($sock,$data);
	    }
	}
    /**
     * 发送到某个客户端
     * @param $client_id
     * @param $data
     * @return unknown_type
     */
	function sendTo($client_id,$data)
	{
	    fwrite($this->client_sock[$client_id],$data);
	}

    function onStart()
    {
        echo "Server in running!\n";
    }

    function onConnect($client_id)
    {
        $this->sendAll($client_id,"Client $client_id is connected!\n");
    }

    function onClose($client_id)
    {
        $this->sendAll($client_id,"Client $client_id is closed!\n");
    }

    function onShutdown()
    {
        echo "Server in stop!\n";
    }
}

$server = new MyServer('0.0.0.0',8005);
$server->run();
