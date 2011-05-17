<?php
require '../../config.php';
import('#web.RestClient');

$server_url = "http://top.com/test/web/rpc.php";
$user = 'test';
$pass = '123456';
$rest = new RestClient($server_url,$user,$pass);
//$rest->debug = true;

$result1 = $rest->func('testme');

$obj = $rest->create('world');
$obj->index = 'page';
$result2 = $obj->getinfo('delete');

debug($result1,$result2);