<?php
define("WEBPATH",str_replace("\\","/",dirname(__FILE__)));
define("WEBROOT",'http://'.$_SERVER['SERVER_NAME']);

define('DBTYPE','pdo');
define('DBENGINE','MyISAM');
define("DBMS","mysql");
define("DBHOST","localhost");
define("DBUSER","root");
define("DBPASSWORD","5524001");
define("DBNAME","website");
define("DBCHARSET","gb2312");

define("TABLE_PREFIX",'chq');
define('ADMIN_SKIN','very');

//Ӧ�ó����λ��
define("APPSPATH",WEBPATH.'/apps');
define('HTML',WEBPATH.'/html');
define('HTML_URL_BASE','/html');
define('HTML_FILE_EXT','.html');

//����ϵͳ
define('CACHE_URL','file://localhost/#site_cache');
define('KDB_CACHE','memcache://192.168.11.26:11211');
define('KDB_ROOT','cms,user');

/**
 * Session�Ựϵͳ����
 */
define('SESSION_CACHE','memcache://192.168.11.26:11211');
define('SESSION_LIFETIME',3600);

require('libs/lib_config.php');
$php->loadlibs('db,cache,tpl');
$php->loadConfig();
?>