<?php
define("WEBPATH",str_replace("\\","/",dirname(__FILE__)));
define("WEBROOT",'http://'.$_SERVER['SERVER_NAME']);
//Database Driver，可以选择PdoDB , MySQL, MySQL2(MySQLi) , AdoDb(需要安装adodb插件)
define('DBTYPE','PdoDB');
define('DBENGINE','MyISAM');
define("DBMS","mysql");
define("DBHOST","127.0.0.1");
define("DBUSER","root");
define("DBPASSWORD","root");
define("DBNAME","test");
define("DBCHARSET","utf8");

//应用程序的位置
define("APPSPATH",WEBPATH.'/apps');
define('HTML',WEBPATH.'/html');
define('HTML_URL_BASE','/html');
define('HTML_FILE_EXT','.html');

//上传文件的位置
define('UPLOAD_DIR','/static/uploads');

//缓存系统
#define('CACHE_URL','memcache://127.0.0.1:11211');
define('CACHE_URL','filecache://localhost#site_cache');
//define('SESSION_CACHE','memcache://192.168.11.26:11211');
//define('KDB_CACHE','memcache://192.168.11.26:11211');
//define('KDB_ROOT','cms,user');

//DES加密解密的KEY
define('DESKEY','jcxh@21xiehou.com');

require('libs/lib_config.php');
$php->autoload('db','tpl','cache');
//动态配置系统
//$php->loadConfig();
//指定国际编码的方式
mb_internal_encoding('utf-8');
?>