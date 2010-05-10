<?php
function smarty_function_url($params)
{
	if(isset($params['key']) and isset($params['value']))
	{
        if(isset($params['ignore'])) return Swoole_tools::url_merge($params['key'],$params['value'],$params['ignore']);
        else return Swoole_tools::url_merge($params['key'],$params['value']);
	}
	else echo "url merge error!";
}
?>
