<?php
class SwooleContent extends Model
{
	var $table="swoole_content";
	var $select="id,title";
	
	function write($data,$dict)
	{
		$data["root"]=$dict->root;
		$data["path"]=$dict->current_path;
		$data["dict"]=$dict->dir->id;
		$new=$this->create();
		$new->put($data);
		$new->save();
	}
	
	function update()
	{
		
	}
	
	function read($id)
	{
		return $this->get($id);
	}
	
	function getContents($dict)
	{
		$rs=$this->all();
		$rs->filter("dict=$dict");
		return $rs->getlist();
	}
	
	function del($id)
	{
		$obj=$this->get($id);
		$obj->delete();
	}
}
?>