<?php
function smarty_function_getall($params, &$smarty)
{
	return json_encode($smarty->_tpl_vars[$params['var']]);
}
?>
