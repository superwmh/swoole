<?php
// Controller的基类
class Controller
{
	var $swoole;
	var $filter_request = true;
	
	function __construct($php)
	{
		$this->swoole = $php;
		if($this->filter_request)
		{
			Filter::filter_array($_POST);
			Filter::filter_array($_GET);
		}
	}
}
?>