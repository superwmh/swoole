<?php
function smarty_function_askey($params)
{
	$data = $params['data'];
	$key = $params['key'];
	if(isset($params['key2'])) echo $data[$key][$params['key2']];
	else echo $data[$key];
}
?>
