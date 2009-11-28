<?php
function js_back($msg,$go=-1)
{
	echo "<script language='javascript'>alert('$msg');history.go($go);</script>";
}
function js_alert($msg)
{
	echo "<script language='javascript'>alert('$msg');</script>";
}
function js_goto($msg,$url)
{
	echo "<script language='javascript'>alert(\"$msg\");";
	echo "window.location.href=\"$url\";</script>";
}

function js_confirm($msg,$true,$false)
{
	echo "<script language=\"JavaScript\">
	if(confirm('$msg'))
		location.href=\"".$true."\";
	else
		location.href=\"".$false."\";
	</script>";
}

function js_location($url)
{
	echo "<script language='javascript'>";
	echo "window.location.href=\"$url\";</script>";
}

function js_unescape($str)
{
	$ret = '';
	$len = strlen($str);

	for ($i = 0; $i < $len; $i++)
	{
		if ($str[$i] == '%' && $str[$i+1] == 'u')
		{
			$val = hexdec(substr($str, $i+2, 4));

			if ($val < 0x7f) $ret .= chr($val);
			else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
			else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));

			$i += 5;
		}
		else if ($str[$i] == '%')
		{
			$ret .= urldecode(substr($str, $i, 3));
			$i += 2;
		}
		else $ret .= $str[$i];
	}
	return $ret;
}

function js_encode($php_data)
{
	//$json = new JSON;
	if(DBCHARSET!='utf8')
	{
		if(!function_exists('array_iconv'))
			import_func('array');
		$php_data = array_iconv(DBCHARSET,'utf-8',$php_data);
	}
	return json_encode($php_data);
}

function js_decode($js_data)
{
	if(DBCHARSET!='utf8')
	{
		$js_data = iconv(DBCHARSET,'utf-8',$js_data);
	}
	return json_decode($js_data);
}
?>