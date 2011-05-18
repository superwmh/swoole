<?php
require('config.php');

$php->runMVC('mvc_get');  //��URLRewrite��ͨ��GET��ֵ��ʽ
//$php->runMVC('mvc_rewrite'); //��URLRewrite

/**
 * ����URL GET������ӳ�䷽ʽ
 * ��index.php?c=page&v=index ��ӳ�䵽 apps/controllers/page.php ��class page��index����
*/
function url_process_mvc_get()
{  
    $array = array('controller'=>'page','view'=>'index');
    //$_GET['c'] ������controller����
    if(empty($_GET['c'])) return $array;
    else $array['controller'] = trim($_GET['c']);
    
    //$_GET['v'] ����ҳ��view����
    if(empty($_GET['v'])) return $array;
    else $array['view'] = trim($_GET['v']);

    return $array;
}
/**
 * ��/page/index/ ��ӳ�䵽 apps/controllers/page.php ��class page��index����
 * ����URL rewrite���URLӳ�䷽ʽ
 * Apache URLWrite���ã���staticĿ¼�⣬���������ӳ�䵽 index.php
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