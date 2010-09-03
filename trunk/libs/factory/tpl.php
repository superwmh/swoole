<?php
$tpl = new Template();
global $php;
$tpl->assign_by_ref('php_genv',$php->genv);
$tpl->assign_by_ref('php',$php->env);
//$tpl->assign('$_site_',WEBROOT);
if(defined('TPL_DIR')) $tpl->template_dir = TPL_DIR;
if(DEBUG=='on') $tpl->compile_check = true;
else $tpl->compile_check = false;