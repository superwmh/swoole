<?php
/**
 * MySQL数据库封装类
 * @package SwooleExtend
 * @author Tianfeng.Han
 *
 */
class MySQL2 extends mysqli implements IDatabase
{
	public $debug = false;
    public $conn = null;
    public $config;

    function __construct($db_config)
    {
        $this->config = $db_config;
    }

	function connect($db_config)
	{
		$db_config = &$this->config;
		if(isset($db_config['persistent']) and $db_config['persistent'])
		    parent::pconnect($db_config['host'],$db_config['user'],$db_config['password'],$db_config['dbname']);
		else
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
		$res = parent::query($sql) or die($this->error);
		if(!$res) echo $sql,"<hr />\n",$this->error,"<br />\n";
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
	public $result;
	function __construct($result)
	{
		$this->result = $result;
	}

    function fetch()
    {
    	if(empty($this->result))
    	{
    		Error::warn('Select Result Empty','Select Result Empty');
  			return false;
    	}
    	return $this->result->fetch_assoc();
    }

    function fetchall()
    {
   		if(empty($this->result))
    	{
    		Error::warn('Select Result Empty','Select Result Empty');
  			return false;
    	}
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