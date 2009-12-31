<?php
/**
 * 字典操作模型
 * @author Administrator
 *
 */
class SwooleDict extends Model
{
	var $table = 'swoole_dict';
	
	function igets($fid=0)
	{
		$gets['fid'] = $fid;
		return $this->gets($gets);
	}
	
	function pgets($kpath)
	{
		$gets['kpath'] = $kpath;
		return $this->gets($gets);
	}
	
	function pget($kpath,$kname)
	{
		$get['kpath'] = $kpath;
		$get['limit'] = 1;
		$get['ckname'] = $kname;
		$res = 	$this->gets($get);
		if(empty($res))
		{
			Error::pecho("Not found $kpath/$kname");
			return false;
		}
		return $res[0];
	}
	
	/**
	 * KEY查询方式，找出一个项
	 * @param $keyid
	 * @return unknown_type
	 */
	function kget($keyid)
	{
		$get['keyid'] = $keyid;
		$get['limit'] = 1;
		$data = $this->gets($get);
		return $data[0];
	}
	/**
	 * KEY查询方式，找出多个子项
	 * @param $keyid
	 * @return unknown_type
	 */
	function kgets($fkey)
	{
		$get['fkey'] = $fkey;
		$data = $this->gets($get);
		return $data;
	}
}
?>