<?php
/**
 * Swoole错误类
 * @package SwooleSystem
 * @author Tianfeng.Han
 *
 */
class Error
{
	var $error_info = array('101'=>'',
							'102'=>'');
	function except($error_id)
	{
		
	}
	/**
	 * 输出一条错误信息，并结束程序的运行
	 * @param $msg
	 * @param $content
	 * @return unknown_type
	 */
	function info($msg,$content)
	{
		$html = <<<HTMLS
<html>
<head>
<title>$msg</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">

body {
background-color:	#fff;
margin:				40px;
font-family:		Lucida Grande, Verdana, Sans-serif;
font-size:			12px;
color:				#000;
}

#content  {
border:				#999 1px solid;
background-color:	#fff;
padding:			20px 20px 12px 20px;
}

h1 {
font-weight:		normal;
font-size:			14px;
color:				#990000;
margin: 			0 0 4px 0;
}
</style>
</head>
<body>
	<div id="content">
		<h1>$msg</h1>
		<p>$content</p>	</div>
</body>
</html>
HTMLS;
		echo $html;
		exit;
	}
	
	static function warn($title,$content)
	{
		echo '<b>Warning </b>:'.$title."<br/> \n";
		echo $content;
	}
	
	static function debug($var)
	{
		debug($var);
	}
	/**
	 * 以表格的形式显示一个2维数组
	 * @param $var
	 * @return unknown_type
	 */
	static function output($var)
	{
		if(!is_array($var)) self::warn('Error Debug!','Not is a array!');
		import('#html.HTML_table');
		$attr['border'] = 1;
		$attr['style'] = 'font-size:14px';
		
		$table = new HTML_table($var,$attr);
		echo $table->html();
	}
	static function parray($array)
	{
		if(!is_array($array)) self::warn('Error Debug!','Not is a array!');
		foreach($array as $k=>$v)
		{
			echo $k,': ',$v,"<br />";	
		}
	}
	static function pecho($str)
	{
		echo $str,"<br />\n";
	}
	/**
	 * 调试数据库
	 * @return unknown_type
	 */
	static function dbd()
	{
		global $php;
		$php->db->debug = true;
	}
}
?>