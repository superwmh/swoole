<?php
class Logs
{
	var $file;
	var $_date_format;
	
	function __contruct($filename)
	{
		$this->file = fopen($filename,'a');
	}
	function __destruct()
	{
		fclose($this->file);
	}
	function info()
	{
		
	}
	function error()
	{
		
	}
	function warn()
	{
		
	}
	function notice()
	{
		
	}
	function output($line)
	{
		fwrite($this->file,$line,strlen($line));
	}
	function format($format)
	{
		$this->_date_format = $format;
	}
}
?>