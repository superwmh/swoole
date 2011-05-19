<?php
require('config.php');
define('SESSION_CACHE','file://localhost#sess');
require(LIBPATH.'/function/cli.php');
//Mime格式
require(LIBPATH.'/data/mimes.php');
$mime_types = array_flip($mimes);
//静态文件许可
$static_files = array_flip(array('static','templates','swoole_plugin','favicon.ico','robot.txt'));
$static_access = array_flip(array('html','htm','jpg','gif','png','js','css'));
//加载全部controller
import_all_controller();

$_SERVER['run_mode'] = 'server';
$_SERVER['server_driver'] = 'SelectTCP'; //BlockTCP,EventTCP,SelectTCP
$_SERVER['server_host'] = '0.0.0.0';
$_SERVER['server_port'] = 8888;
$_SERVER['server_processor_num'] = 4;   //启用的进程数目
$_SERVER['session_cookie_life'] = 86400; //保存SESSION_ID的cookie存活时间
$_SERVER['session_life'] = 1800;

$php->runServer();
