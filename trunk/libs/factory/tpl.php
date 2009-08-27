<?php
$tpl = new Template();
global $php;
$tpl->assign_by_ref('php_genv',$swoole->genv);
$tpl->assign_by_ref('php_env',$swoole->env);
?>