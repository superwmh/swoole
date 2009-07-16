<?php
// Controller的基类
class Controller
{
	var $swoole;
	
	function __construct($php)
	{
		$this->swoole = $php;
	}
}
?>