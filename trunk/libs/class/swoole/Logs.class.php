<?php
/**
 * Swoole日志类
 * @author Tianfeng.Han
 *
 */
class Logs
{
	public $file;
	public $_date_format='Y-m-d H:i:s';
	public $newline;
	
	function __construct($filename)
	{
		$this->file = fopen($filename,'a');
		if(PATH_SEPARATOR==':') $this->newline = "\n";
		else $this->newline = "\r\n";	
	}
	function __destruct()
	{
		fclose($this->file);
	}
	function info($info)
	{
		$this->output($info,'INFO');
	}
	function error($info)
	{
		$this->output($info,'ERROR');
	}
	function warn($info)
	{
		$this->output($info,'WARNNING');
	}
	function notice($info)
	{
		$this->output($info,'NOTICE');
	}
	function output($content,$level)
	{
		$line = date($this->_date_format)." $level ".$content.$this->newline;
		fwrite($this->file,$line);
	}
	function format($format)
	{
		$this->_date_format = $format;
	}
}
?>