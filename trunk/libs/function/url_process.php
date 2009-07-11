<?php
function url_process_mvc_default()
{
	$array = array('app'=>'default','controller'=>'default','view'=>'index','segs'=>'');
	if(!empty($_GET["app"])) $array['app']=$_GET["app"];
	if(!empty($_GET["controller"])) $array['controller']=$_GET["controller"];
	if(!empty($_GET["view"])) $array['view']=$_GET["view"];
	if(isset($_GET["param"])) $array['segs']=Swoole_tools::getSegs($_GET["param"]);
	return $array;
}

/**
 * 处理mvc的GET请求
 * @return array
 */
function url_process_mvc_simple()
{
	//默认配置
	$array = array('controller'=>'SiaoCMS','view'=>'index','segs'=>'');
	if(!empty($_GET["controller"])) $array['controller']=$_GET["controller"];
	if(!empty($_GET["view"])) $array['view']=$_GET["view"];
	if(isset($_GET["param"])) $array['segs']=Swoole_tools::getSegs($_GET["param"]);
	return $array;
}
?>