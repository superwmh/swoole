<?php
/**
 * 基本函数，全局对象$php的构造
 * @package lib_config
 * @author 韩天峰
 */

define("LIBPATH",str_replace("\\","/",dirname(__FILE__)));

#全局变量
$php = new Swoole;

/**

*函数的命名空间

*/
function namespace($space_name)
{
	require_once(LIBPATH.'/function/'.$space_name.'.php');
}

/**

*工程函数，制造一个公用对象

*/
function create($object_name)
{
	require(LIBPATH.'/factory/'.$object_name.'.php');
	$object = $$object_name;
	return $object;
}

/**

*生产一个model接口

*/
function createModel($model_name,$db='')
{
	if(!is_object($db))
	{
		global $php;
		$php->load->get('db');
		$db = $php->db;
	}
	require(APPSPATH.'/models/'.$model_name.'.model.php');
	return new $model_name($db);
}

/**
 * 导入类库
 */
function import($lib_name)
{
	$file = str_replace('.','/',$lib_name);
	$lib_file = LIBPATH.'/class/'.$file.".class.php";
	if(file_exists($lib_file))
	{
		require_once($lib_file);
		return true;
	}
	else
	{
		Error::info("Import Error!","<b>$lib_file</b> not fountd!<br />\n $lib_name load fail!<br />\n");
		return false;
	}
}
function session()
{
    if(!defined('SESSION_CACHE'))
    {
        session_start();
        return true;
    }
    $session_cache = new Cache(SESSION_CACHE);
    $mSess    = new MSession($session_cache);
    $mSess->initSess();
}

/**
 * 导入一个数组格式的数据
 */
function load_data($data_name)
{
	require(LIBPATH.'/data/'.$data_name.'.php');
	return $$data_name;
}

/**
 * 重建类文件缓存
 * @param $path
 * @param $class_cache
 * @return none
 */
function CreateClassCache($path,$class_cache)
{
    $files = scandir($path);
    unset($files[0],$files[1]);
    foreach($files as $file)
    {
       $file_path = $path.'/'.$file;
       if( is_dir($file_path)) CreateClassCache($file_path,$class_cache);
       elseif(strpos($file,'.class.php')!==false)
       {
           $class_name = str_replace('.class.php','',$file);
           $class_cache->set($class_name,$file_path);
        }
    }
}

function debug($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    exit;
}

/**
*自动导入类
*/
function __autoload($class_name)
{
	if(file_exists(LIBPATH.'/system/'.$class_name.'.php'))
		require(LIBPATH.'/system/'.$class_name.'.php');
}
?>