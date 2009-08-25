<?php
function smarty_function_json($params, &$smarty)
{
	return json_encode($smarty->_tpl_vars[$params['var']]);
}
?>
