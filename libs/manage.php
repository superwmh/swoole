<?php
require '../config.php';
$cmd = $argv[1];
switch($cmd)
{
	case 'init':
		mkdir(WEBPATH.'/apps',0755);
		mkdir(WEBPATH.'/apps/controllers',0755);
		mkdir(WEBPATH.'/apps/models',0755);
	
		mkdir(WEBPATH.'/cache',0755);
		mkdir(WEBPATH.'/cache/pages_c',0755);
		mkdir(WEBPATH.'/cache/templates_c',0755);
		mkdir(WEBPATH.'/cache/virtualdata',0755);
	
		mkdir(WEBPATH.'/static',0755);
		mkdir(WEBPATH.'/static/images',0755);
		mkdir(WEBPATH.'/static/css',0755);
		mkdir(WEBPATH.'/static/js',0755);
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
?>