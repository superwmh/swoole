<?php
define("DEBUG",'on');
define("WEBPATH",str_replace("\\","/",dirname(__FILE__)));
@define("WEBROOT",'http://'.$_SERVER['SERVER_NAME']);

define('DBTYPE','MySQL'); //PdoDB，MySQL，MySQL2(MySQLi)
define('DBENGINE','MyISAM');
define("DBMS","mysql");   //数据库类型
define("DBHOST","localhost"); //数据库HOST地址
define("DBUSER","root");      //用户名
define("DBPASSWORD","root");  //密码
define("DBNAME","test"); //数据库名称
define("DBCHARSET","utf8"); //编码方式
define("DBSETNAME",true); #是否发送set names

//字典数据目录
define("DICTPATH",WEBPATH.'/dict');
//应用程序的位置
define("APPSPATH",WEBPATH.'/apps');
//上传文件的位置
define('UPLOAD_DIR','/static/uploads');
//文件缓存的目录
define('FILECACHE_DIR',WEBPATH.'/cache/filecache');

//缓存系统
//define('CACHE_URL','memcache://localhost:11211');
define('CACHE_URL','file://localhost#site_cache');
//define('SESSION_CACHE','file://localhost#sess');
//define('KDB_CACHE','memcache://127.0.0.1:1978');
//define('KDB_CACHE','file://localhost#item_cache');
//define('KDB_ROOT','cms,user');

//事件配置
define('EVENT_MODE','sync'); //async异步，或者sync同步
define('EVENT_HANDLE',WEBPATH.'/apps/configs/events.php');
define('EVENT_QUEUE','http://192.168.1.104:1218');  //消息队列Queue服务器地址
define('EVENT_QUEUE_TYPE','HttpQueue');   //消息队列类型，HttpQueue或者CacheQueue

//日志系统配置
define('LOGTYPE','PHPLog');   //Log的存储方式
define('LOGPUT',WEBPATH.'/cache/site.log'); //存储的名称，如果是数据库填写表名，如果是文件请填写文件名称
define('LOGPUT_TYPE','file');

//Login登录用户配置
define('LOGIN_TABLE','user_login');
//框架
require_once WEBPATH.'/libs/lib_config.php';
//自动加载项目，请查看libs/factory目录
$php->autoload('db','tpl','cache', 'config');
//加载插件
#$php->plugin->load('kdb');
//加载动态配置
$php->loadConfig();
//设置mb字符串编码
mb_internal_encoding('utf-8');
//是否启用内容压缩
//$php->gzip();
