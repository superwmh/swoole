<?php
function smarty_modifier_digest($string)
{
	$array = array('<font color="#000000">��ͨ����</a>','<font color="#00ff00">��Ŀ�Ƽ�</a>','<font color="#0000ff">վ���Ƽ�</a>','<font color="#ff0000">ͷ���Ƽ�</a>');
	return $array[intval($string)];
}
?>
