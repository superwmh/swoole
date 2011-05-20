<?php
class ChatServer implements Swoole_TCP_Server_Protocol
{
    public $default_port = 8080;
    public $chat_users;
    public $chat_client;

    function log($msg)
    {
        echo $msg,NL;
    }

    function onRecive($client_id,$data)
    {
        $msg = explode(' ',$data,3);
        $this->log($client_id.$data);
        if($msg[0]=='/setname')
        {
            if(isset($this->chat_users[$msg[1]])) $this->server->send($client_id,'user exists');
            else
            {
                $this->chat_client[$msg[1]] = $client_id;
                $this->chat_users[$msg[1]] = $msg[2];
                $this->server->send($client_id,'setname success');
                $this->server->sendAll($client_id,$msg[2],' login!');
            }
        }
        elseif($msg[0]=='/sendto')
        {
            if(isset($this->chat_client[$msg[1]]))
            {
                $this->server->send($this->chat_client[$msg[1]],$msg[1]);
            }
        }
        elseif($msg[0]=='/sendall')
        {
            $this->log('############send all##########'.NL);
            $this->server->sendAll($client_id,$msg[1]);
        }
        elseif($msg[0]=='/getusers')
        {
            $this->server->send($client_id,json_encode($this->chat_users));
        }
    }

    function onStart()
    {

    }

    function onShutdown()
    {

    }
    function onClose($client_id)
    {

    }
    function onConnect($client_id)
    {

    }
}
