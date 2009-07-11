<?php
class SwooleDIr extends Model
{
	var $table="swoole_dict";
	
	var $current_dictory=0;
	var $current_path="/";
	var $defalt_access="rwxrwxr--";
	var $root=0;
	
	var $dir;
	
	function mkdir($name)
	{
		$dict=$this->create();
		$dict->name=$name;
		$dict->path=$this->current_path;
		$dict->fid=$this->current_dictory;
		$dict->root=$this->root;
		$dict->createdate=time();
		$dict->access=$this->defalt_access;
		$dict->owner="root";
		$dict->groups="wheel";
		$dict->save();
	}
	
	function chdir($id)
	{
		$this->current_dictory=$id;
		$this->dir=$this->get($id);
		if($this->dir==false) return false;
		if($this->dir->root==0)
		{
			$this->current_path="/$id";
			$this->root=$id;
		}
		else
		{
			$this->root=$this->dir->root;
			$this->current_path=$this->dir->path."/$id";
		}
		return true;
	}
	
	function rename($name)
	{
		$dict=$this->get($this->current_dictory);
		$dict->name=$name;
		$dict->save();
	}
	
	function getChilds($fid)
	{
		$rs=$this->all();
		$rs->filter("fid=$fid");
		return $rs->getlist();
	}
	
	function __toString()
	{
		return "Class Scmp_dict";
	}
}
?>