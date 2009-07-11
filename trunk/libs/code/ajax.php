<?php
header('Cache-Control: no-cache, must-revalidate');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Content-type: application/json');

$method = $_GET['method'];
$data = call_user_func($method);

if(DBCHARSET!='utf8')
{
	namespace('array');
	$data = array_iconv(DBCHARSET , 'utf-8' , $data);
}
echo json_encode($data);
?>