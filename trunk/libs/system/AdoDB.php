<?php
require(LIBPATH.'/libs/module/adodb/adodb.inc');
class AdoDB
{
	var $db;
	var $debug = false;
	function __construct($host='',$user='',$password='',$dbname='',$charset='utf8')
	{
		if($host=='')
		{
			$host = DBHOST;
			$user = DBUSER;
			$password = DBPASSWORD;
			$dbname = DBNAME;
			$charset = DBCHARSET;
		}
		
		$this->db = ADONewConnection(DBMS);
		$this->db->connect($host,$user,$password,$dbname);
		$this->db->debug = $this->debug;
		if(defined('DEBUG') and DEBUG=='on') $this->db->debug = true;
		$this->query('set names '.$charset);
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	}
	
    public final function query($sql)
	{
		return new AdoResult($this->db->execute($sql));
    }
    
    public final function nsquery($sql)
    {
        return new $this->db->execute($sql);
    }
       
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
		return $this->query("insert into $table ($field) values($values)") or die("Error:".$this->errorInfo());
	}
	
	public function delete($id,$table,$where='id')
	{
		return $this->query("delete from $table where $where=$id") or die("Error:".$db->errorInfo());
	}
	
	public function update($id,$data,$table,$where='id')
	{
		$sql="";
		
		foreach($data as $key=>$value)
		{
			//if(!$value) continue;
			$sql=$sql."$key='$value',";
		}
		
		$sql=substr($sql,0,-1);
		return $this->query("update $table set $sql where $where='$id'") or die("Error:".$db->ErrorMsg());
	}
}

class AdoResult
{
	var $result;
	function __construct($result)
	{
		$this->result = $result;
	}
	
    function fetch()
    {
    	return $this->result->fields;
    }
    
    function fetchall()
    {
    	return $this->result->getall();
    }
}
?>