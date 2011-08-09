<?php
/**
 * 基本函数，全局对象$php的构造
 * @package SwooleSystem
 * @author 韩天峰
 */
define("LIBPATH",str_replace("\\","/",dirname(__FILE__)));
//加载核心的文件
require LIBPATH.'/system/Swoole.php';
require LIBPATH.'/system/SwooleLoader.php';
require LIBPATH.'/system/ModelLoader.php';
require LIBPATH.'/system/PluginLoader.php';

if(PHP_OS=='WINNT') define("NL","\r\n");
else define("NL","\n");
define("BL","<br />".NL);
/**
 * 产生类库的全局变量
 */
$php = new Swoole;
/**
 *函数的命名空间
 */
function import_func($space_name)
{
    if($space_name{0}=='@') $func_file = WEBPATH.'/class/'.substr($space_name,1).'.func.php';
    else $func_file = LIBPATH.'/function/'.$space_name.'.php';
    require_once($func_file);
}

/**
 *生产一个model接口
 */
function createModel($model_name,$import=false)
{
    global $php;
    if($import===false)
    {
        return $php->model->$model_name;
    }
    else
    {
        $model = explode('.',$model_name);
        import($model_name);
        return new $model[-1]($php);
    }
}

/**
 * 导入类库
 */
function import($lib_name)
{
    $file = str_replace('.','/',$lib_name);
    if($file{0}=='@') $lib_file = WEBPATH.'/class/'.substr($file,1).'.class.php';
    elseif($file{0}=='#') $lib_file = LIBPATH.'/class/swoole/'.substr($file,1).'.class.php';
    else $lib_file = LIBPATH.'/class/'.$file.".class.php";

    if(file_exists($lib_file))
    {
        require_once($lib_file);
        return true;
    }
    else
    {
        Error::info("Import Error!","Class <b>$lib_file</b> not fountd!<br />\n $lib_name load fail!<br />\n");
        return false;
    }
}
/**
 * 工厂方法，产生一个类的对象
 * @param $name
 * @return unknown_type
 */
function create($name)
{
    import($name);
    $classinfo = explode('.',$name);
    $classname = $classinfo[-1];
    if(func_num_args()!=1)
    {
        $args=func_get_args();
        for($i=1;$i<count($args);$i++) $el[]='$args['.$i.']';
        $object=eval("return new $classname(".implode(",",$el).");");
        return $object;
    }
    else return new $classname;
}
/**
 * 开启会话
 * @return None
 */
function session()
{
    if(!defined('SESSION_CACHE'))
    {
        session_start();
        return true;
    }
    $session_cache = new Cache(SESSION_CACHE);
    $mSess = new Session($session_cache);
    $mSess->initSess();
}
/**
 * 导入插件
 * @param $plugin_name
 * @return None
 */
function loadPlugin($plugin_name)
{
    global $php;
    $php->plugin->load($plugin_name);
}
/**
 * 调试数据，终止程序的运行
 * @param $var
 * @return unknown_type
 */
function debug()
{
    echo '<pre>';
    $vars = func_get_args();
    foreach($vars as $var) var_dump($var);
    echo '</pre>';
    exit;
}
/**
 * 引发一个错误
 * @param $error_id
 * @param $stop
 * @return unknown_type
 */
function error($error_id,$stop=true)
{
    global $php;
    $error = new Error($error_id);
    if(isset($php->error_call[$error_id]))
    {
        call_user_func($php->error_call[$error_id],$error);
    }
    elseif($stop) exit($error);
    else echo $error;
}
/**
 * 错误信息输出处理
 */
function swoole_error_handler($errno, $errstr, $errfile, $errline)
{
    $level = 'Error';
    $info = '';

    switch ($errno)
    {
        case E_USER_ERROR:
            $level = 'User Error';
            break;
        case E_USER_WARNING:
            $level = 'Warnning';
            break;
        case E_USER_NOTICE:
            $level = 'Notice';
            break;
        default:
            return;
    }

    $title = 'Swoole '.$level;
    $info .= '<b>File:</b> '.$errfile."<br />\n";
    $info .= '<b>Line:</b> '.$errline."<br />\n";
    $info .= '<b>Info:</b> '.$errstr."<br />\n";
    $info .= '<b>Code:</b> '.$errno."<br />\n";
    Error::info($title,$info);
}
/**
 *自动导入类
 */
function __autoload($class_name)
{
    if(is_file(LIBPATH.'/system/'.$class_name.'.php'))
    require(LIBPATH.'/system/'.$class_name.'.php');
    elseif(is_file(WEBPATH.'/class/'.$class_name.'.class.php'))
    require(WEBPATH.'/class/'.$class_name.'.class.php');
}