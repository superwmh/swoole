<?php
require LIBPATH.'/system/Template.php';
$tpl = new Template();
global $php;
$tpl->assign_by_ref('php_genv',$php->genv);
$tpl->assign_by_ref('php',$php->env);

if(defined('TPL_DIR'))
{
	$tpl->template_dir = TPL_DIR;
}
elseif(is_dir(Swoole::$app_path.'/templates'))
{
	$tpl->template_dir = Swoole::$app_path.'/templates';
}
else
{
	$tpl->template_dir = WEBPATH."/templates";
}

if(DEBUG=='on') $tpl->compile_check = true;
else $tpl->compile_check = false;