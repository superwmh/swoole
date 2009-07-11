<?php
require(LIBPATH.'/module/adodb/adodb.inc.php');
$adodb = ADONEWConnection(DBMS);
$adodb->connect(DBHOST,DBUSER,DBPASSWORD,DBNAME);
$adodb->execute('set names '.DBCHARSET);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

function insert($data,$table)
{
	$field='';
	$values='';
	global $db;
	
	foreach($data as $key => $value)
	{
		$field=$field."$key,";
		$value=str_replace('<?',htmlentities('<?'),$value);
		$value=str_replace('?>',htmlentities('?>'),$value);
		$values=$values."'$value',";
	}
	
	$field=substr($field,0,-1);
	$values=substr($values,0,-1);
	return $db->execute("insert into $table ($field) values($values)") or die("Error:".$db->ErrorMsg());
	
}

function delete($id,$table)
{
	global $db;
	$db->execute("delete from $table where id=$id") or die("Error:".$db->ErrorMsg());
}

function update($id,$data,$table,$where="id")
{
	$sql="";
	global $db;
	
	foreach($data as $key=>$value)
	{
		$sql=$sql."$key='$value',";
	}
	
	$sql=substr($sql,0,-1);
	$db->execute("update $table set $sql where $where='$id'") or die("Error:".$db->ErrorMsg());
	return true;
}

class selectdb
{
	var $table="";
	var $select="*";
	var $sql="";
	var $limit="";
	var $where="";
	var $order="";
	
	function from($table)
	{
		$this->table=$table;
	}
	
	function select($select)
	{
		if($this->select=="*")
		{
			$this->select=$select;
		}
		else
		{
			$this->select=$this->select.",".$select;
		}
	}
	
	function where($where)
	{
		if($this->where=="")
		{
			$this->where="where ".$where;
		}
		else
		{
			$this->where=$this->where." and ".$where;
		}
	}
	
	function orwhere($where)
	{
		if($this->where=="")
		{
			$this->where="where ".$where;
		}
		else
		{
			$this->where=$this->where." or ".$where;
		}
	}
	
	function limit($offset,$pagesize="")
	{
		if($pagesize=="")
		{
			$this->limit="limit ".$offset;
		}
		else
		{
			$this->limit="limit ".$offset.",".$pagesize;
		}
	}
		
	function order($order)
	{
		$this->order="order by $order";
	}
	
	function get()
	{
		global $db;
		$this->sql=trim("select {$this->select} from {$this->table} {$this->where} {$this->order} {$this->limit} ");
		$res=$db->execute($this->sql) or die("SQL:{$this->sql}<br>Error:".$db->ErrorMsg());
		return $res->GetArray();
	}

	function res_count()
	{
		global $db;
		$this->sql=trim("select count(id) from {$this->table} {$this->where} {$this->order} {$this->limit} ");
		$res=$db->execute($this->sql) or die("SQL:{$this->sql}<br>Error:".$db->ErrorMsg());
		return $res->fields[0];
	}
	
	function init()
	{
		$this->table="";
		$this->select="*";
		$this->limit="";
		$this->where="";
		$this->order="";
	}
}
?>