<?php
$smarty = new Template;
global $php;
$smarty->assign_by_ref('php_genv',$swoole->genv);
$smarty->assign_by_ref('php_env',$swoole->env);
?>