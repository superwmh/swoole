<?php
require(LIBPATH."/module/smarty/Smarty.class.php");
class Template extends Smarty
{
	var $if_pagecache = false;
	var $cache_life = 3600;
	
	function __construct()
	{
		//$this->templates_dir = WEBPATH."/templates";
		$this->compile_dir = WEBPATH."/cache/templates_c";
		$this->config_dir = WEBPATH."/configs";
		$this->cache_dir = WEBPATH."/cache/tmp";
		$this->left_delimiter = "{{";
		$this->right_delimiter = "}}";
	}
	function pagecache()
	{
		$pagecache = new Swoole_pageCache($this->cache_life);		
		if($pagecache->isCached()) $pagecache->load();
		else return false;
		return true;
	}
	function display($template= null,$cache_id= null,$complile_id= null)
	{
		if($template==null)
		{
			global $php;
			$template = $php->env['mvc']['controller'].'_'.$php->env['mvc']['view'].'.html';
		}
		if($this->if_pagecache)
		{
			$pagecache = new Swoole_pageCache($this->cache_life);
			if(!$pagecache->isCached()) $pagecache->create(parent::fetch($template,$cache_id,$complile_id));
			$pagecache->load();
		}
		else parent::display($template,$cache_id,$complile_id);
	}
	
	/**
	 * 生成静态页面
	 * @param $template
	 * @param $filename
	 * @return unknown_type
	 */
	function outhtml($template,$filename,$path='')
	{
		if($path=='')
		{
			$path = dirname($filename);
			$filename = basename($filename);
		}
		if(!is_dir($path)) mkdir($path,0777,true);
		$content = $this->fetch($template);
		file_put_contents($path.'/'.$filename,$content);
		return true;
	}
	
	function push($data)
	{
		foreach($data as $key=>$value) $this->assign($key,$value);
	}
}
?>