<?php
class Field extends Model
{
	var $table = 'system_fields';
	
	function getAppFields($appid)
	{
		$data = $this->all();
		$data->where('appid = '.$appid);
		$data->order('orderid');
		return $data->getall();
	}
}
?>