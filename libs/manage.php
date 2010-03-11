<?php
error_reporting(E_ERROR);
if(empty($argv[1])) die('Swoole Command Error:what are you doing?');
$cmd = $argv[1];
if($cmd!='setup') require '../config.php';
switch($cmd)
{
	case 'setup':
		$project = $argv[2];
		$strlen = strlen($project);
		$end_char = $project{$strlen-1};
		if($end_char=="\\" or $end_char=='/') $project=substr($project,0,$strlen-1);
		
		if(!is_dir($project)) mkdir($project,0755,true);
		dir_copy(dirname(__FILE__),$project.'/libs');
		copy('../index.php',$project.'/index.php');
		copy('../config.php',$project.'/config.php');
		echo "Setup Swoole to $project!\n";
		echo $project;
		break;
		/**
		 * 初始化项目
		 */
	case 'init':
		/**
		 * 建立MVC目录
		 */
		if(!is_dir(WEBPATH.'/apps')) mkdir(WEBPATH.'/apps',0755);
		if(!is_dir(WEBPATH.'/apps/controllers')) mkdir(WEBPATH.'/apps/controllers',0755);
		if(!is_dir(WEBPATH.'/apps/models')) mkdir(WEBPATH.'/apps/models',0755);
		if(!is_dir(WEBPATH.'/apps/views')) mkdir(WEBPATH.'/apps/views',0755);

		/**
		 * 建立缓存的目录
		 */
		if(!is_dir(WEBPATH.'/cache')) mkdir(WEBPATH.'/cache',0755);
		if(!is_dir(WEBPATH.'/cache/pages_c')) mkdir(WEBPATH.'/cache/pages_c',0755);
		if(!is_dir(WEBPATH.'/cache/templates_c')) mkdir(WEBPATH.'/cache/templates_c',0755);
		if(!is_dir(WEBPATH.'/cache/virtualdata')) mkdir(WEBPATH.'/cache/virtualdata',0755);

		/**
		 * Smarty的模板目录
		 */
		if(!is_dir(WEBPATH.'/templates')) mkdir(WEBPATH.'/templates',0755);

		/**
		 * 建立静态文件的目录
		 */
		if(!is_dir(WEBPATH.'/static')) mkdir(WEBPATH.'/static',0755);
		if(!is_dir(WEBPATH.'/static/images')) mkdir(WEBPATH.'/static/images',0755);
		if(!is_dir(WEBPATH.'/static/css')) mkdir(WEBPATH.'/static/css',0755);
		if(!is_dir(WEBPATH.'/static/uploads')) mkdir(WEBPATH.'/static/uploads',0755);
		if(!is_dir(WEBPATH.'/static/js')) mkdir(WEBPATH.'/static/js',0755);

		/**
		 * 建立外部扩展类目录
		 */
		if(!is_dir(WEBPATH.'/class')) mkdir(WEBPATH.'/class',0755);
		/**
		 * 建立网站字典目录
		 */
		if(!is_dir(WEBPATH.'/dict')) mkdir(WEBPATH.'/dict',0755);
		/**
		 * 建立Swoole插件系统目录
		 */
		if(!is_dir(WEBPATH.'/swoole_plugin')) mkdir(WEBPATH.'/swoole_plugin',0755);
		break;
	case 'addc':
		$content = "<?php
class {$argv[2]} extends Controller
{
    function __construct(\$swoole)
    {
        parent::__construct(\$swoole);
    }
}";
		file_put_contents(WEBPATH.'/apps/controllers/'.$argv[2].'.php',$content);
		echo "create a new controller {$argv[2]}!\n";
		break;
	case 'addm':
		$content = "<?php
class {$argv[2]} extends Model
{
	//Here write Database table's name
	var \$table = '';
}";
		file_put_contents(WEBPATH.'/apps/models/'.$argv[2].'.model.php',$content);
		break;
	default:
		break;
}
/**
 * 复制目录
 * @param $fdir源目录名(不带/)
 * @param $tdir目标目录名(不带/)
 * @return
 */
function dir_copy($fdir,$tdir)
{
	if(is_dir($fdir))
	{
		if(!is_dir($tdir))
		{
			mkdir($tdir);
		}
		$handle =opendir($fdir);
		while(false!==($filename=readdir($handle)))
		{
			if($filename!="."&&$filename!="..") dir_copy($fdir."/".$filename,$tdir."/".$filename);
		}
		closedir($handle);		 
		return true;
	}
	else
	{
		copy($fdir,$tdir);
		return true;
	}
}