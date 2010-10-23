<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_magic($params, &$smarty)
{
	if(empty($params['func'])) exit(new Error(509));
	return call_user_func("cms_".$params['func'],$params,$smarty);
}
?>
