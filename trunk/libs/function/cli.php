<?php
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