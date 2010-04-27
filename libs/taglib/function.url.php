<?php
function smarty_function_url($params)
{
	if(isset($params['key']) and isset($params['value']))
	{
        $url = array();
		$urls = $_GET;
        $urls[$params['key']] = $params['value'];
        foreach($urls as $k=>$v)
        {
        	if($v=='' or $v=='') continue;
        	$url[] = $k.'='.urlencode($v);
        }
        return $_SERVER['PHP_SELF'].'?'.implode('&',$url);
	}
	else echo "url merge error!";
}
?>
