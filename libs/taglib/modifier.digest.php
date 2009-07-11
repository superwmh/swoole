<?php
function smarty_modifier_digest($string)
{
	$array = array('<font color="#000000">普通主题</a>','<font color="#00ff00">栏目推荐</a>','<font color="#0000ff">站点推荐</a>','<font color="#ff0000">头条推荐</a>');
	return $array[intval($string)];
}
?>
