<?php
define("DEBUG",'on');
define("WEBPATH",str_replace("\\","/",dirname(__FILE__)));
@define("WEBROOT",'http://'.$_SERVER['SERVER_NAME']);

define('DBTYPE','MySQL');
define('DBENGINE','MyISAM');
define("DBMS","mysql");
define("DBHOST","192.168.1.101");
define("DBUSER","sanmingzhi");
define("DBPASSWORD","liyichenG199*");
define("DBNAME","sanmingzhi");
define("DBCHARSET","utf8");
define("DBSETNAME",true); #是否发送set names

//字典数据目录
define("DICTPATH",WEBPATH.'/dict');

//应用程序的位置
define("APPSPATH",WEBPATH.'/apps');
define('HTML',WEBPATH.'/html');
define('HTML_URL_BASE','/html');
define('HTML_FILE_EXT','.html');


//上传文件的位置
define('UPLOAD_DIR','/static/uploads');
define('FILECACHE_DIR',WEBPATH.'/cache/filecache');

//缓存系统
//define('CACHE_URL','memcache://192.168.11.26:11211');
define('CACHE_URL','file://localhost#site_cache');
//define('SESSION_CACHE','memcache://192.168.11.26:11211');
//define('KDB_CACHE','memcache://127.0.0.1:1978');
define('KDB_CACHE','file://localhost#item_cache');
//define('KDB_ROOT','cms,user');

define('EVENT_MODE','async');
define('EVENT_HANDLE',WEBPATH.'/apps/configs/events.php');
define('EVENT_QUEUE','file://localhost#queue');
define('EVENT_QUEUE_TYPE','CacheQueue');

require('libs/lib_config.php');
$php->autoload('db','tpl','cache');
//$php->plugin->load('kdb');
//$php->loadConfig();
mb_internal_encoding('utf-8');
?>