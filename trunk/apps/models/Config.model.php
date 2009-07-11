<?php
class Config extends Model implements ArrayAccess
{
	var $table = 'system_config';
	var $primary = 'keyname';
	var $_config_data;
	var $swoole;
	
	const CACHE_TIME=3600;
	const CACHE_NAME='site_config';
	
	function __construct($db)
	{
		parent::__construct($db);
		global $php;
		$this->swoole = $php;
		$this->_config_data = $php->cache->get(self::CACHE_NAME);
	}
	
	function getGroup($category)
	{
		$data = $this->all();
		$data->where("category='$category'");
		return $data->getall();
	}
	
	function __get($key)
	{
		if(isset($this->_config_data[$key])) $value=$this->_config_data[$key];
		else
		{
			$data = $this->all();
			$data->where("keyname='$key'");
			$value = $data->getone('value');
			$this->_config_data[$key] = $value;
		}
		return $value;
	}
	
	function __set($key,$value)
	{
		$this->_config_data[$key]=$value;
		if($this->offsetExists($key))
			$this->db->update($key,array('value'=>$value),$this->table,'keyname');
		else
			$this->db->insert(array('keyname'=>$key,'value'=>$value),$this->table,'keyname');
	}
	
	function offsetExists($key)
	{
		$data = $this->all();
		$data->where("keyname='$key'");
		if($data->count()==0) return false;
		return true;
	}
	
	function offsetGet($key)
	{
		return $this->$key;
	}
	
	function offsetSet($key,$value)
	{
		$this->$key = $value;
	}
	
	function offsetUnset($key)
	{
		if(isset($this->_config_data[$key])) unest($this->_config_data[$key]);
		$this->db->delete($key,$this->table,'keyname');
	}
	
	function __destruct()
	{
		$this->swoole->cache->set(self::CACHE_NAME,$this->_config_data,self::CACHE_TIME);
		$this->swoole->cache->save();
	}
}
?>