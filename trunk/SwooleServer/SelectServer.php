<?php
$port = 9050;
$timeout = 60;

function broadcast($from_sock,$msg)
{
    global $clients;
    foreach($clients as $c)
    {
        if($c!==$from_sock) socket_write($c, $msg."\n");
    }
}

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_nonblock($sock);
socket_bind($sock, 0, $port);
socket_listen($sock);
echo "server running on $port\n";

$clients = array();

while (true)
{
    $r_fds = array_merge($clients,array($sock));
    if(socket_select($r_fds, $write = NULL, $except = NULL, $timeout))
    {
        foreach($r_fds as $read_sock)
        {
            if($read_sock==$sock)
            {
                $clients[] = $newsock = socket_accept($sock);
                broadcast($sock,"There are ".count($clients)." client(s) connected to the server");
                socket_getpeername($newsock, $ip);
                echo "New client connected: {$ip}\n";
                break;
            }
            else
            {
                $data = @socket_read($read_sock, 1024, PHP_NORMAL_READ);
                if(!$data)
                {
                    socket_shutdown($read_sock);
                    socket_close($read_sock);
                    $key = array_search($read_sock,$clients);
                    unset($clients[$key]);
                    echo "$key logout!\n";
                    broadcast($sock,"$key logout!");
                    continue 2;
                }
                if(substr($data,-1)!=="\r" and substr($data,-1)!=="\n")
                {
                    $data.=$data;
                    continue 2;
                }
                else
                {
                    broadcast($read_sock,$data);
                }
            }
        }
    }

}
socket_close($sock);
