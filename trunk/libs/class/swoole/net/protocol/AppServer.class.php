<?php
require_once LIBPATH.'/class/swoole/net/protocol/HttpServer.class.php';
class AppServer extends HttpServer
{
    function __construct($ini_file)
    {
        parent::__construct($ini_file);
    }
    function process_dynamic(&$request,&$response)
    {
        global $php;
        $php->__init();
        $request->setGlobal();

        $path = trim($request->meta['path'],'/');
        $url_route_func = $this->config['apps']['url_route'];
        $mvc = $url_route_func($path);
        $php->env['mvc'] = $mvc;

        /*---------------------加载MVC程序----------------------*/
        $controller_file = APPSPATH.'/controllers/'.$mvc['controller'].'.php';
        if(!isset($php->env['controllers'][$mvc['controller']]))
        {
            if(is_file($controller_file))
            {
                import_controller($controller_file);
            }
            else
            {
                $this->http_error(404,$response,"控制器 <b>{$mvc['controller']}</b> 不存在!");
                return $response;
            }
        }
        $controller = new $mvc['controller']($php);
        /*---------------------检测代码是否更新----------------------*/
        if(extension_loaded('runkit') and $this->config['apps']['auto_reload'])
        {
            clearstatcache();
            $fstat = stat($controller_file);
            //修改时间大于加载时的时间
            if($fstat['mtime']>$php->env['controllers'][$mvc['controller']]['time'])
            {
                runkit_import($controller_file);
                $php->env['controllers'][$mvc['controller']]['time'] = time();
                $this->log("reload controller ".$mvc['controller']);
            }
        }
        /*---------------------处理MVC----------------------*/
        $php->request = $request;
        $php->response = $response;
        if(!method_exists($controller,$mvc['view']))
        {
            $this->http_error(404,$response,"视图 <b>{$mvc['controller']}->{$mvc['view']}</b> 不存在!");
            return $response;
        }
        if(empty($mvc['param'])) $param = array();
        else $param = $mvc['param'];

        $response->head['Content-Type'] = 'text/html';
        if($controller->is_ajax) $response->body = json_encode(call_user_func(array($controller,$mvc['view']),$param));
        else $response->body = call_user_func(array($controller,$mvc['view']),$param);
        //保存Session
        if($php->session_open) $php->session->save();
        return $response;
    }
}