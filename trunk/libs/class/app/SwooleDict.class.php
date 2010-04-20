<?php
/**
 * 字典操作模型
 * @author Administrator
 *
 */
class SwooleDict extends Model
{
	public $table = 'swoole_dict';
	public $cache;
	public $_data;
	public $if_cache = false;
	public $expire = 0;

	function setCache($cache)
	{
		$this->cache = $cache;
		$this->if_cache = true;
	}

	function iget($id)
	{
		return $this->get($id)->get();
	}

	function igets($fid=0,$order = 'id')
	{
		$gets['fid'] = $fid;
		$gets['order'] = $order;
		return $this->gets($gets);
	}

	function pgets($kpath,$order='id')
	{
		$gets['kpath'] = $kpath;
		$gets['order'] = $order;
		return $this->gets($gets);
	}

	function pget($kpath,$kname)
	{
		$path = "$kpath/$kname";
		if($this->if_cache) $cache_data = $this->cache->get($path);
		else $cache_data = false;
		if($cache_data) return $cache_data;
		else
		{
			$get['kpath'] = $kpath;
			$get['limit'] = 1;
			$get['ckname'] = $kname;
			$res = 	$this->gets($get);
			if(empty($res))
			{
				Error::pecho("Not found $kpath/$kname");
				$de = debug_backtrace();
				foreach($de as $d)
				{
					echo $d['file'],':',$d['line'],"\n<br />";
				}
				return false;
			}
			if($this->if_cache) $this->cache->set($path,$res[0],$this->expire);
			return $res[0];
		}
	}

	/**
	 * KEY查询方式，找出一个项
	 * @param $keyid
	 * @return unknown_type
	 */
	function kget($keyid)
	{
		if($this->if_cache) $cache_data = $this->cache->get($keyid);
		else $cache_data = false;
		if($cache_data) return $cache_data;
		else
		{
			$get['keyid'] = $keyid;
			$get['limit'] = 1;
			$data = $this->gets($get);
			if($this->if_cache) $this->cache->set($keyid,$data[0],$this->expire);
			return $data[0];
		}
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