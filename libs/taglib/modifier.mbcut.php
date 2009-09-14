<?php
function smarty_modifier_mbcut($string,$length=12,$more='')
{
	if(mb_strlen($string)>$length)
	{
		return mb_substr($string,0,$length).$more;
	}
	else
	{
		return $string;
	}
}