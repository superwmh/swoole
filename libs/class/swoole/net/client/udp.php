<?php
require 'UDPServer.php';

class MyServer extends UDPServer
{
    function onData($peer,$data)
    {
         echo $data;
         if(trim($data)=='shutdown') $this->shutdown();
         echo $peer;
         echo "\n";
    }

    function onStart()
    {
        echo "Server in running!\n";
    }

    function onShutdown()
    {
        echo "Server in stop!\n";
    }
}

$server = new MyServer('0.0.0.0',8006);
$server->run();
