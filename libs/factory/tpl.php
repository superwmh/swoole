<?php
$tpl = new Template();
global $php;
$tpl->assign_by_ref('php_genv',$php->genv);
$tpl->assign_by_ref('php',$php->env);
?>