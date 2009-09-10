<?php
/**
 * PDO数据库封装类
 * @package SwooleSystem
 * @author Administrator
 *
 */
class Database extends PDO
{
	var $debug = false;
	var $read_times = 0;
	var $write_times = 0;
	function __construct($host='',$user='',$password='',$dbname='',$charset='utf8')
	{
        try
		{
			if($host=='')
			{
				$host = DBHOST;
				$user = DBUSER;
				$password = DBPASSWORD;
				$dbname = DBNAME;
				$charset = DBCHARSET;
			}
			if(defined('DEBUG') and DEBUG=='on') $this->debug = true;
			$dsn=DBMS.":host=".$host.";dbname=".$dbname; 
			echo parent::__construct($dsn, $user, $password);
			parent::query('set names '.$charset);			
			$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
			
        }
		catch (PDOException $e)
		{
        	die("Error: " . $e->__toString() . "<br/>");
        }
    }
    /**
	* 执行一个SQL语句
     */
    public final function query($sql)
	{
		if($this->debug) echo "$sql<br />\n<hr />";
		$res=parent::query($sql) or die("SQL Error: ".implode(", ",$this->errorInfo())."<br />\n");
		$this->read_times +=1;
		return $res;
    }
    /**
     * 插入$data数据库的表$table，$data必须是键值对应的，$key是数据库的字段，$value是对应的值
     * @param $data
     * @param $table
     * @return unknown_type
     */
	public function insert($data,$table)
	{
		$field="";
		$values="";
		foreach($data as $key => $value)
		{
			$field=$field."$key,";
			$value=str_replace("<?",htmlentities("<?"),$value);
			$value=str_replace("?>",htmlentities("?>"),$value);
			$value=str_replace("'","\"",$value);
			$values=$values."'$value',";
		}
		
		$field=substr($field,0,-1);
		$values=substr($values,0,-1);
		$this->write_times +=1;
		return $this->query("insert into $table ($field) values($values)") or die("Error:".$this->errorInfo());
	}
	
	public function delete($id,$table,$where='id')
	{
		$this->write_times +=1;
		return $this->query("delete from $table where $where='$id'");
	}
	
	public function update($id,$data,$table,$where='id')
	{
		$sql="";
		
		foreach($data as $key=>$value)
		{
			if($value!='' and $value{0}=='`') $sql=$sql."$key=$value,";
			else $sql=$sql."$key='$value',";
		}
		
		$sql=substr($sql,0,-1);
		$this->write_times +=1;
		return $this->query("update $table set $sql where $where='$id'");
	}
	
	function Insert_ID()
	{
		return $this->lastInsertId();
	}
	/**
	* 根据主键获取单条数据
	*/
	function get($id,$table,$primary='id')
	{
		return $this->query("select * from $table where $primary=$id")->fetch();
	}
}
?>