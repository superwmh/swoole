<?php
/**
 * PDO数据库封装类
 * @package SwooleExtend
 * @author Tianfeng.Han
 *
 */
class PdoDB extends PDO
{
	public $debug = false;
	function __construct($db_config)
	{
		$dsn=$db_config['dbms'].":host=".$db_config['host'].";dbname=".$db_config['dbname'];
		try
		{

			if(isset($db_config['persistent']) and $db_config['persistent'])
				parent::__construct($dsn, $db_config['user'],$db_config['password'],array(ATTR_PERSISTENT=>true));
			else
			    parent::__construct($dsn, $db_config['user'],$db_config['password']);
			if($db_config['ifsetname']) parent::query('set names '.$db_config['charset']);
			$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die("Error: " . $e->__toString() . "<br/>");
		}
	}
	function connect()
	{

	}
	/**
	 * 执行一个SQL语句
	 * @param $sql 执行的SQL语句
	 */
	public final function query($sql)
	{
		if($this->debug) echo "$sql<br />\n<hr />";
		$res = parent::query($sql) or die("$sql<br />\n<hr />SQL Error: ".implode(", ",$this->errorInfo())."<br />\n");
		return $res;
	}
	/**
	 * 返回上一个Insert语句的自增主键ID
	 * @return $ID
	 */
	function Insert_ID()
	{
		return $this->lastInsertId();
	}
	/**
	 * 关闭连接，释放资源
	 * @return unknown_type
	 */
	function close()
	{
		unset($this);
	}
}
?>