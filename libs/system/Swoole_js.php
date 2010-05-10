<?php
class Swoole_js
{
	static $head="<script language=\"javascript\">\n";
	static $foot="</script>\n";

	public function echojs($js)
	{
		echo self::$head;
		echo $js;
		echo self::$foot;
	}

	public function alert($str)
	{
		self::echojs("alert(\"$str\");");
	}

	public function location($url)
	{
		self::echojs("location.href='$url';");
	}

	static function js_back($msg,$go=-1)
	{
		if(!is_numeric($go)) $go=-1;
		self::echojs("alert('$msg');\nhistory.go($go);\n");
	}
	static function js_alert($msg)
	{
		echo "<script language='javascript'>alert('$msg');</script>";
	}
    static function js_alert_close($msg)
    {
        echo "<script language='javascript'>alert('$msg');</script>";
    }
    static function js_parent_reload($msg)
    {
        echo "<script language='javascript'>alert(\"$msg\");";
        echo "window.parent.location.reload();</script>";
    }
	static function js_goto($msg,$url)
	{
		echo "<script language='javascript'>alert(\"$msg\");";
		echo "window.location.href=\"$url\";</script>";
	}

	static function js_confirm($msg,$true,$false)
	{
		echo "<script language=\"JavaScript\">
		if(confirm('$msg'))
			location.href=\"".$true."\";
		else
			location.href=\"".$false."\";
		</script>";
	}

	static function js_confirmback($msg,$true)
	{
		echo "<script language=\"JavaScript\">
		if(confirm('$msg'))
			location.href=\"".$true."\";
		else
			history.go(-1);
		</script>";
	}
}
?>