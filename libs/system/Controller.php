<?php
// Controller的基类
class Controller
{
	var $swoole;
	
	function __construct($php)
	{
		$this->swoole = $php;
		
		$php->tpl->left_delimiter = '{{'.$php->config->tpl_left_tag;
		$php->tpl->template_dir = WEBPATH.'/templates/'.$php->config->site_theme;
		
		$this->swoole->tpl->clear_all_assign();
		$this->swoole->tpl->clear_all_cache();
		$this->swoole->tpl->assign_by_ref('env',$this->swoole->env);
	}
}
?>