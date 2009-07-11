<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_magic($params, &$smarty)
{
	global $php;
	$magic = new Magic($php);
	$func = $params['func'];
	return $magic->$func($params);
}
?>