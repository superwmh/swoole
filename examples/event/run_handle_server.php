<?php
require 'settings.php';

function test($id,$op)
{
    echo $id,':',$op,NL;
}
$php->autoload('event');
$php->event->run_server(1000,WEBPATH.'/cache/event.log');