<?php
class Content extends Model
{
	var $app;
	
	function __construct($db)
	{
		parent::__construct($db);
	}
	
	function set($var,$value)
	{
		$this->$var = $value;
	}
	
	function get($id,$app='')
	{
		if(empty($app)) $app = $this->app;
		$this->db->query('select * from ')
	}
?>