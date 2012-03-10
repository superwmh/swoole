<?php
/**
 * MySQL数据库封装类
 * @package SwooleExtend
 * @author Tianfeng.Han
 *
 */
class MySQL implements IDatabase
{
	public $debug = false;
	public $conn = null;
	public $config;

	function __construct($db_config)
	{
        $this->config = $db_config;
	}
	function connect()
	{
		$db_config = &$this->config;

		if(isset($db_config['persistent']) and $db_config['persistent'])
            $this->conn = mysql_pconnect($db_config['host'],$db_config['user'],$db_config['password']) or die(mysql_error());
        else

            $this->conn = mysql_connect($db_config['host'],$db_config['user'],$db_config['password']) or die(mysql_error());

        mysql_select_db($db_config['dbname'],$this->conn) or die(mysql_error($this->conn));
        if($db_config['ifsetname']) mysql_query('set names '.$db_config['charset'],$this->conn) or die(mysql_error($this->conn));
	}
	/**
	 * 执行一个SQL语句
	 * @param $sql 执行的SQL语句
	 */
	function query($sql)
	{
		mysql_real_escape_string($sql,$this->conn);
		$res = mysql_query($sql,$this->conn);
		if(!$res) echo Error::info("SQL Error",mysql_error($this->conn)."<hr />$sql");
		return new MySQLRecord($res);
	}
	/**
	 * 返回上一个Insert语句的自增主键ID
	 * @return $ID
	 */
	function Insert_ID()
	{
		return mysql_insert_id($this->conn);
	}
	function ping()
	{
	    if(!mysql_ping($this->conn)) return false;
	    else return true;
	}
	/**
	 * 获取上一次操作影响的行数
	 * @return int
	 */
	function affected_rows()
	{
		return mysql_affected_rows($this->conn);
	}
	/**
	 * 关闭连接
	 * @see libs/system/IDatabase#close()
	 */
	function close()
	{
		mysql_close($this->conn);
	}
}
class MySQLRecord implements IDbRecord
{
	public $result;
	function __construct($result)
	{
		$this->result = $result;
	}

    function fetch()
    {
    	return mysql_fetch_assoc($this->result);
    }

    function fetchall()
    {
    	$data = array();
    	while($record = mysql_fetch_assoc($this->result))
    	{
    		$data[] = $record;
    	}
    	return $data;
    }
    function free()
    {
    	mysql_free_result($this->result);
    }
}
?>
