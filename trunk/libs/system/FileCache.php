<?php
/**
 * 文件缓存类，提供类似memcache的接口
 * 警告：此类仅用于测试，不作为生产环境的代码，请使用Key-Value缓存系列！
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage cache
 *
 */
class FileCache
{
	public $_vd=array();
	public $onchange=0;
	public $res;
	public $autosave = false;

	function __construct($file)
	{
		$this->res=$file;
		if(file_exists($this->res)) $this->_vd=unserialize(file_get_contents($this->res));
    }

    function set($name,$value,$timeout=0)
	{
		$this->_vd[$name]["value"]=$value;
		$this->_vd[$name]["timeout"]=$timeout;
		$this->_vd[$name]["mktime"]=time();
		$this->onchange=1;
		if($this->autosave) $this->save();
		return true;
    }

	function get($name)
	{
		if($this->exist($name)) return $this->_vd[$name]["value"];
		else return false;
	}

	function exist($name)
	{
		if(!array_key_exists($name,$this->_vd)) return false;
		elseif($this->_vd[$name]["timeout"]==0) return true;
		elseif(($this->_vd[$name]["mktime"]+$this->_vd[$name]["timeout"])<time())
		{
			$this->onchange=1;
			$this->delete($name);
			return false;
		}
		else return true;
	}

	function delete($name)
	{
		if(array_key_exists($name,$this->_vd)) unset($this->_vd[$name]);
	}

	function save()
	{
		if($this->onchange==1) file_put_contents($this->res,serialize($this->_vd));
	}

	function __destruct()
	{
		$this->save();
	}
}
?>