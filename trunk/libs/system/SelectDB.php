<?php
/**
 * 查询数据库的封装类，基于底层数据库封装类，实现SQL生成器
 * @author Tianfeng.Han
 * @package SwooleSystem
 * 
 */
class SelectDB
{
	var $table='';
	var $primary='id';
	var $select='*';
	var $sql='';
	var $limit='';
	var $where='';
	var $order='';
	var $group='';
	var $join='';
	
	var $if_join = false;
	var $if_add_tablename = false;
	
	var $page_size = 10;
	var $num = 0;
	var $pages = 0;
	var $page = 0;
	var $pager = null;
	
	var $auto_cache = false;
	var $cache_life = 600;
	var $cache_prefix = 'selectdb'; 
	
	var $RecordSet;
	
	var $is_execute = 0;
	
	var $result_filter = array();
	
	var $call_by = 'func';	
	var $db;
	
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * 初始化，select的值，参数$where可以指定初始化哪一项
	 * @param $what
	 * @return unknown_type
	 */
	function init($what='')
	{
		if($what=='')
		{
			$this->table="";
			$this->select="*";
			$this->limit="";
			$this->where="";
			$this->order="";
		}
		else
			$this->$what = '';
	}
	/**
	 * 指定表名，可以使用table1,table2
	 * @param $table_name
	 * @return None
	 */
	function from($table)
	{
		$this->table=$table;
	}
    /**
     * 指定查询的字段，select * from table
     * 可多次使用，连接多个字段
     * @param $select
     * @return unknown_type
     */
	function select($select,$force=false)
	{
		if($this->select=="*" or $force) $this->select=$select;
		else $this->select=$this->select.','.$select;
	}
	/**
	 * where参数，查询的条件
	 * @param $where
	 * @return unknown_type
	 */
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
	/**
	 * 相似查询like
	 * @param $field
	 * @param $like
	 * @return unknown_type
	 */
	function like($field,$like)
	{
		$this->where("{$field} like '{$like}'");
	}
	/**
	 * 使用or连接的条件
	 * @param $where
	 * @return unknown_type
	 */
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
	/**
	 * 查询的条数
	 * @param $limit
	 * @return unknown_type
	 */
	function limit($limit)
	{
		$this->limit="limit ".$limit;
	}
	/**
	 * 指定排序方式
	 * @param $order
	 * @return unknown_type
	 */
	function order($order)
	{
		$this->order="order by $order";
	}
	
	function group($group)
	{
		$this->group = "group by $group";
	}
	
	function in($field,$ins)
	{
		$this->where("$field in ({$ins})");
	}
	
	function notin($field,$ins)
	{
		$this->where("$field not in ({$ins})");
	}
	
	function join($table_name,$on)
	{
		$this->join="INNER JOIN {$table_name} ON {$on}";
	}
	
	function leftjoin($table_name,$on)
	{
		$this->join="LEFT JOIN {$table_name} ON {$on}";
	}
	
	function rightjoin($table_name,$on)
	{
		$this->join="RIGHT JOIN {$table_name} ON {$on}";
	}
	
	function pagesize($pagesize)
	{
		$this->page_size = $pagesize;
	}
	
	function page($page)
	{
		$this->page = $page;
	}
	
	function id($id)
	{
		$this->where("{$this->primary} = '$id'");
	}
	
	function paging()
	{
		$this->num = $this->count();
		$offset=($this->page-1)*$this->page_size;
		if($this->num%$this->page_size>0)
			$this->pages=intval($this->num/$this->page_size)+1;
		else
			$this->pages=$this->num/$this->page_size;
		$this->limit($offset.','.$this->page_size);
		$this->pager = new Pager(array('total'=>$this->num,'perpage'=>$this->page_size));
	}
	
	function filter($filter_func)
	{
		$filter_list = explode(',',$filter_func);
		$this->result_filter = array_merge($$this->result_filter,$filter_list);
	}

	function getsql($ifreturn=true)
	{
		if($this->sql=='')
			$this->sql=trim("select {$this->select} from {$this->table} {$this->join} {$this->where} {$this->group} {$this->order} {$this->limit}");
		if($ifreturn) return $this->sql;
	}
	
	function exeucte($sql='')
	{
		if($sql=='') $this->getsql(false);
		else $this->sql = $this->sql();
		$this->res = $this->db->query($this->sql);
		$this->is_execute++;
	}
	
	function put($param)
	{
		//处理where条件
		if(isset($param['where']))
		{
			$wheres = $param['where'];
			if(is_array($wheres)) foreach($wheres as $where) $this->where($where);
			else $this->where($wheres);
			unset($param['where']);
		}
		
		foreach($param as $key=>$value)
		{
			if($key=='update' or $key=='delete' or $key=='insert')
				continue;
			if(strpos($key,'_')!==0)
			{
				if(method_exists($this,$key))
				{
					if(is_array($value)) call_user_method_array($key,$this,$value);
					else call_user_method($key,$this,$value);
				}
				else
				{
					if($this->call_by=='func')
						$this->where($key.'="'.$value.'"');
					elseif($this->call_by=='smarty')
					{
						if(strpos($value,'$')===false)
							$this->where($key."='".$value."'");
						else
							$this->where($key."='{".$value."}'");
					}
					else
						Error::info('Error: SelectDB 错误的参数',"<pre>参数$key=$value</pre>");
				}
			}
		}
	}
	
	function getone($field='',$cache_id='')
	{
		$this->limit('1');
		if($this->auto_cache or !empty($cache_id))
		{
			$cache_key = empty($cache_id)?$this->cache_prefix.'_one_'.md5($this->sql):$this->cache_prefix.'_all_'.$cache_id;
			global $php;
			$record = $php->cache->get($cache_key);
			if(empty($data))
			{
				if($this->is_execute==0) $this->exeucte();
				$record = $this->res->fetch();
				$php->cache->set($cache_key,$record,$this->cache_life);
			}
		}
		else
		{
			if($this->is_execute==0) $this->exeucte();
			$record = $this->res->fetch();
		}		
		if($field==='') return $record;
		return $record[$field];	
	}
	
	function getall($cache_id='')
	{
		if($this->auto_cache or !empty($cache_id))
		{
			$cache_key = empty($cache_id)?$this->cache_prefix.'_all_'.md5($this->sql):$this->cache_prefix.'_all_'.$cache_id;
			global $php;
			$data = $php->cache->get($cache_key);
			if(empty($data))
			{
				if($this->is_execute==0) $this->exeucte();
				$data = $this->res->fetchall();
				$php->cache->set($cache_key,$data,$this->cache_life);
				return $data;
			}
			else return $data;
		}
		else
		{
			if($this->is_execute==0) $this->exeucte();
			return $this->res->fetchall();
		}
	}
	
	public function count()
	{
		$sql=trim("select count(*) as cc from {$this->table} {$this->where}");
		$res=$this->db->query($sql)->fetch();
		return $res['cc'];
	}
	
	function insert($data)
	{
		return $this->db->insert($data,$this->table);
	}
	
	function update($data)
	{
		$update="";		
		foreach($data as $key=>$value)
		{
			if($value!='' and $value{0}=='`') $update=$update."$key=$value,";
			else $update=$update."$key='$value',";
		}
		
		$update = substr($update,0,-1);
		return $this->db->query("update {$this->table} set $update {$this->where} {$this->limit}");
	}	
	function delete()
	{
		return $this->db->query("delete from {$this->table} {$this->where} {$this->limit}");
	}
}
?>