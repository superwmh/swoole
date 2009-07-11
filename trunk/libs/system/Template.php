<?php
require(LIBPATH."/module/smarty/Smarty.class.php");
class Template extends Smarty
{
	function __construct()
	{
		//$this->templates_dir = WEBPATH."/templates";
		$this->compile_dir = WEBPATH."/cache/templates_c";
		$this->config_dir = WEBPATH."/configs";
		$this->cache_dir = WEBPATH."/cache/tmp";
		$this->left_delimiter = "{{";
		$this->right_delimiter = "}}";
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
		foreach($data as $key=>$value) $this->assign_by_ref($key,$value);
	}
}
?>