<?php
/**
 * 数组编码转换
 * @param $in_charset
 * @param $out_charset
 * @param $data
 * @return $data
 */
function array_iconv($in_charset,$out_charset,$data)
{
	if(is_array($data))
	{
		foreach($data as $key=>$value)
		{
			if(is_array($value)) $value = array_iconv($value);
			else $value = iconv($in_charset,$out_charset,$value);
			$data[$key]=$value;
		}
	}
	return $data;
}
?>