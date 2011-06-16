<?php
/**
 * 使用函数方式调用系统功能
 */
/**
 * 执行一条SQL语句并返回结果
 * @param $sql
 * @return unknown_type
 */
function sql($sql,$num=0)
{
	if(empty($sql)) return false;
	global $php;

	//数据库是否连接，对象是否可用，如果不可用则返回false
	if(empty($php->db)) return false;
	else return $php->db->query($sql)->fetchall();
}
function update()
{
	global $php;
	return $php->db->update($id,$data,$table,$where='id');
}
function insert($data,$table)
{
	global $php;
	return $php->db->insert($data,$table);
}
/**
 * 赋值给模板
 * @param $name
 * @param $value
 * @return unknown_type
 */
function assign($name,$value)
{
	global $php;
	return $php->tpl->assign($name,$value);
}
/**
 * 渲染模板
 * @param $tplname
 * @return unknown_type
 */
function display($tplname=null)
{
	global $php;
	return $php->tpl->display($tplname);
}
/**
 * 缓存设置
 * @param $name
 * @param $value
 * @param $time
 * @return unknown_type
 */
function cache_set($name,$value,$time)
{
	global $php;
	return $php->cache->set($name,$value,$time);
}
/**
 * 缓存删除
 * @param $name
 * @return unknown_type
 */
function cache_delete($name)
{
	global $php;
	return $php->cache->delete($name);
}
/**
 * 缓存获取
 * @param $name
 * @return unknown_type
 */
function cache_get($name)
{
	global $php;
	return $php->cache->get($name);
}