<?php
function smarty_modifier_proc_extras($string)
{
	$extra_types = array('hot'=>'100','new'=>'010','push'=>'001');
	$num = strval(decbin(intval($string)));
	$str = '';
 	if($num{0}=='1') $str .='<img src="/site_static/images/hot.jpg" width="41" height="17" />&nbsp;';
	if($num{1}=='1') $str .='<img src="/site_static/images/new.jpg" width="41" height="17" />&nbsp;';
	if($num{2}=='1') $str .='<img src="/site_static/images/recommend.jpg" width="41" height="17" />&nbsp;';
	return $str;
}
?>
