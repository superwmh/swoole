<?php
/**
 * 导入所有controller
 * @return unknown_type
 */
function import_all_controller()
{
    global $php;
    $d = dir(APPSPATH.'/controllers');
    while($file=$d->read())
    {
        $name = basename($file,'.php');
        //不符合命名规则
        if(!preg_match('/^[a-z0-9_]+$/i',$name)) continue;
        //首字母大写的controller为基类控制器，不直接提供响应
        if(ord($name{0})>64 and ord($name{0})<91) continue;
        $path = $d->path.'/'.$file;
        require($path);
        $php->env['controllers'][$name] = $path;
    }
    $d->close();
}
/**
 * 导入所有model
 * @return unknown_type
 */
function import_all_model()
{
    global $php;
    $d = dir(APPSPATH.'/models');
    while($file=$d->read())
    {
        $name = basename($file,'.model.php');
        //不符合命名规则
        if(!preg_match('/^[a-z0-9_]+$/i',$name)) continue;
        //首字母大写的controller为基类控制器，不直接提供响应
        if(ord($name{0})>64 and ord($name{0})<91) continue;
        $path = $d->path.'/'.$file;
        require($path);
        $php->env['controllers'][$name] = $path;
    }
    $d->close();
}
/**
 * 创建控制器类的文件
 * @param $name
 * @return unknown_type
 */
function create_controllerclass($name,$hello=false)
{
    $content  = "";
    $content .= "<?php\n";
    $content .= "class {$name} extends Controller\n";
    $content .= "{\n";
    $content .= "	function __construct(\$swoole)\n";
    $content .= "	{\n";
    $content .= "	    parent::__construct(\$swoole);\n";
    $content .= "	}\n";
    //添加一个hello view
    if($hello)
    {
        $content .= "	function index(\$swoole)\n";
        $content .= "	{\n";
        $content .= "	    echo 'hello world.This page build by <a href=http://www.swoole.com/>swoole</a>!';\n";
        $content .= "	}\n";
    }
    $content .= "}";
    file_put_contents(WEBPATH.'/apps/controllers/'.$name.'.php',$content);
}
/**
 * 创建模型类的文件
 * @param $name
 * @return unknown_type
 */
function create_modelclass($name,$table='')
{
    $content  = "";
    $content .= "<?php\n";
    $content .= "class {$name} extends Model\n";
    $content .= "{\n";
    $content .= "	//Here write Database table's name\n";
    $content .= "	var \$table = '{$table}';\n";
    $content .= "}";
    file_put_contents(WEBPATH.'/apps/models/'.$name.'.model.php',$content);
}
/**
 * 创建必需的目录
 * @return unknown_type
 */
function create_require_dir()
{
    /**
     * 建立MVC目录
     */
    if(!is_dir(WEBPATH.'/apps')) mkdir(WEBPATH.'/apps',0755);
    if(!is_dir(WEBPATH.'/apps/controllers')) mkdir(WEBPATH.'/apps/controllers',0755);
    if(!is_dir(WEBPATH.'/apps/models')) mkdir(WEBPATH.'/apps/models',0755);

    /**
     * 建立缓存的目录
     */
    if(!is_dir(WEBPATH.'/cache')) mkdir(WEBPATH.'/cache',0755);
    if(!is_dir(WEBPATH.'/cache/pages_c')) mkdir(WEBPATH.'/cache/pages_c',0777);
    if(!is_dir(WEBPATH.'/cache/templates_c')) mkdir(WEBPATH.'/cache/templates_c',0777);
    if(!is_dir(WEBPATH.'/cache/filecache')) mkdir(WEBPATH.'/cache/filecache',0777);

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
}
/**
 * server模式下的请求处理函数
 * @param $request
 * @return unknown_type
 */
function http_request_process($request)
{
    global $php,$mime_types,$static_access,$static_files;
    $php->__init();
    $request->setGlobal();
    $response = new Response;

    //回收Smarty产生的assign内存占用
    sw_gc_array($php->tpl->_tpl_vars);
    $path = trim($request->meta['path'],'/');
    $_mvc =  explode('/',$path,2);

    if(isset($static_files[$_mvc[0]]))
    {
        $static_file = WEBPATH.$request->meta['path'];
        if(is_file($static_file))
        {
            $ext = Upload::file_ext($static_file);
            if(!isset($static_access[$ext]))
            {
                $response->send_http_status(403);
                $response->head['Content-Type'] = $mime_types['txt'];
                $response->body = "request deny";
            }
            else
            {
                $response->head['Content-Type'] = $mime_types[$ext];
                $response->body = file_get_contents($static_file);
            }
        }
        else
        {
            $response->send_http_status(404);
            $response->head['Content-Type'] = $mime_types['txt'];
            $response->body = "file not found!";
        }
        return $response;
    }
    else
    {
        if(empty($path)) $mvc = array('controller'=>'page','view'=>'index');
        else
        {
            $mvc['controller'] = $_mvc[0];
            $mvc['view'] = $_mvc[1];
        }
        $php->env['mvc'] = $mvc;
        $response->head['Content-Type'] = 'text/html';

        if(!isset($php->env['controllers'][$mvc['controller']]))
        {
            $response->send_http_status(404);
            $response->body = Error::info('MVC Error',"Controller Class <b>{$mvc['controller']}</b> not exist!");
            return $response;
        }
        $controller = new $mvc['controller']($php);
        $controller->request = $request;
        $controller->response = $response;
        if(!method_exists($controller,$mvc['view']))
        {
            $response->send_http_status(404);
            $response->body = Error::info('MVC Error!'.$mvc['view'],"View <b>{$mvc['controller']}->{$mvc['view']}</b> Not Found!");
            return $response;
        }
        if(empty($mvc['param'])) $param = array();
        else $param = $mvc['param'];

        if($controller->is_ajax) $response->body = json_encode(call_user_func(array($controller,$mvc['view']),$param));
        else $response->body = call_user_func(array($controller,$mvc['view']),$param);
        //保存Session
        if($controller->session_open) $controller->session->save();
        return $response;
    }
}