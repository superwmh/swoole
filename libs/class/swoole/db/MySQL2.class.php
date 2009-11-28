<?php
/**
 * MySQL数据库封装类
 * @package SwooleExtend
 * @author Tianfeng.Han
 *
 */
class MySQL2 extends mysqli implements IDatabase
{
	function __construct($db_config)
	{
		parent::connect($db_config['host'],$db_config['user'],$db_config['password'],$db_config['dbname']);
		if($db_config['ifsetname']) parent::query('set names '.$db_config['charset']);
	}
	/**
	 * 执行一个SQL语句
	 * @param $sql 执行的SQL语句
	 */
	function query($sql)
	{
		parent::real_escape_string($sql);
		$res = parent::query($sql);
		return new MySQLiRecord($res);
	}
	/**
	 * 返回上一个Insert语句的自增主键ID
	 * @return $ID
	 */
	function Insert_ID()
	{
		return $this->insert_id;
	}
	function close()
	{
		$this->close($thi->conn);
	}
}
class MySQLiRecord implements IDbRecord
{
	var $result;
	function __construct($result)
	{
		$this->result = $result;
	}
	
    function fetch()
    {
    	return $this->result->fetch_assoc();
    }
    
    function fetchall()
    {
    	$data = array();
    	while($record = $this->result->fetch_assoc())
    	{
    		$data[] = $record;
    	}
    	return $data;
    }
    function free()
    {
    	$this->result->free_result();
    }
}
?>