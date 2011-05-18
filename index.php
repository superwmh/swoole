<?php
require('config.php');

$php->runMVC('mvc_get');  //无URLRewrite，通过GET传值方式
//$php->runMVC('mvc_rewrite'); //有URLRewrite

/**
 * 采用URL GET参数的映射方式
 * 如index.php?c=page&v=index 即映射到 apps/controllers/page.php 中class page的index方法
*/
function url_process_mvc_get()
{  
    $array = array('controller'=>'page','view'=>'index');
    //$_GET['c'] 控制器controller名称
    if(empty($_GET['c'])) return $array;
    else $array['controller'] = trim($_GET['c']);
    
    //$_GET['v'] 请求页面view名称
    if(empty($_GET['v'])) return $array;
    else $array['view'] = trim($_GET['v']);

    return $array;
}
/**
 * 如/page/index/ 即映射到 apps/controllers/page.php 中class page的index方法
 * 采用URL rewrite后的URL映射方式
 * Apache URLWrite配置，除static目录外，所有请求均映射到 index.php
 <VirtualHost *>
    ServerName localhost
    Alias /phpmyadmin F:/WebServer/www/phpMyAdmin
    AddDefaultCharset utf-8
    DocumentRoot e:/php/wwwroot
    <Directory "e:/php/wwwroot">
        Order allow,deny
        Allow from all
    </Directory>
    <IfModule mod_rewrite.c>
         RewriteEngine on
         RewriteRule ^/(index\.htm|index\.html|)$ /index.php                           [L]
         RewriteCond %{REQUEST_URI} !=/favicon.ico
         RewriteCond %{REQUEST_URI} !=/admin/
         RewriteCond %{REQUEST_URI} !=/static/
         RewriteRule ^(.*)$ /index.php?mvc=$1 [L,QSA]
    </IfModule>
</VirtualHost>
*/
function url_process_mvc_rewrite()
{
    $array = array('controller'=>'page','view'=>'index');
    if(empty($_GET['mvc'])) return $array;

    $request = explode('/',$_GET['mvc'],3);
    if(count($request)!==3)
    {
    	header("HTTP/1.1 404 Not Found");
    	Error::info('URL Error',"HTTP 404!Page Not Found!<p>Error request:<b>{$_SERVER['REQUEST_URI']}</b>");
    }
    $array['controller']=$request[1];
    $array['view']=$request[2];
    return $array;
}