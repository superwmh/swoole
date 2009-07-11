<?php
class Category extends Model
{
	var $table = 'system_category';
	var $primary = 'id';
	var $swoole;
	
	var $_cate;
	var $_fcate;
	var $_child_cate;
	var $_cid;
	
	function getChild($fid)
	{
		$db_apt = $this->all();
		$db_apt->filter('fid='.$fid);
		return $db_apt->getall();
	}
	
	function getContents($fid,$select='id,title,addtime')
	{
		$cate = $this->get($fid);
		return $this->db->query('select '.$select.' from '.TABLE_PREFIX.'_'.$cate['modelname'].' where catid='.$fid)->fetchall();
	}
	
	function getCategory($catid)
	{
		$this->_cid = $catid;
		$this->_cate = $this->get($catid);
		if($catid!=0) $this->_fcate = $this->get($this->_cate['fid']);
		$db_apt = $this->all();
		$db_apt->filter('fid='.$catid);
		$this->_child_cate = $db_apt->getall();
	}

	function Fetch($var)
	{
		$var = '_'.$var;
		return $this->$var;
	}
	
	function getFCategory($the_category)
	{
		$cates[] = $the_category;
		$fid = $the_category['fid'];
		if($fid!=0)
		{
			$fcate = $this->swoole->db->query("select * from {$this->app_config['categorytable']} where id=$fid limit 1")->fetch();
			$cates = array_merge($cates,$this->getFCategory($fcate));
		}
		
		return $cates;
	}
}
?>