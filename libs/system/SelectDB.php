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
			$this->table='';
			$this->primary='id';
			$this->select='*';
			$this->sql='';
			$this->limit='';
			$this->where='';
			$this->order='';
			$this->group='';
			$this->join='';
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
		$this->join.="INNER JOIN {$table_name} ON ({$on})";
	}

	function leftjoin($table_name,$on)
	{
		$this->join.="LEFT JOIN {$table_name} ON ({$on})";
	}

	function rightjoin($table_name,$on)
	{
		$this->join.="RIGHT JOIN {$table_name} ON ({$on})";
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

	function raw_put($params)
	{
		foreach($params as $array)
		{
			if(isset($array[0]) and isset($array[1]) and count($array)==2)
			{
				$this->_call($array[0],$array[1]);
			}
			else
			{
				$this->raw_put($array);
			}
		}
	}

	function exeucte($sql='')
	{
		if($sql=='') $this->getsql(false);
		else $this->sql = $this->sql();
		$this->res = $this->db->query($this->sql);
		$this->is_execute++;
	}

	function put($params)
	{
		if(isset($params['put']))
		{
			Error::info('SelectDB Error!','Params put() cannot call put()!');
		}
		//处理where条件
		if(isset($params['where']))
		{
			$wheres = $params['where'];
			if(is_array($wheres)) foreach($wheres as $where) $this->where($where);
			else $this->where($wheres);
			unset($params['where']);
		}
		//处理orwhere条件
		if(isset($params['orwhere']))
		{
			$orwheres = $params['orwhere'];
			if(is_array($orwheres)) foreach($orwheres as $orwhere) $this->orwhere($orwhere);
			else $this->$orwheres($orwhere);
			unset($params['orwhere']);
		}

		foreach($params as $key=>$value)
		{
			$this->_call($key,$value);
		}
	}

	private function _call($method,$param)
	{
		if($method=='update' or $method=='delete' or $method=='insert') continue;
		if(strpos($method,'_')!==0)
		{
			if(method_exists($this,$method))
			{
				if(is_array($param)) call_user_func_array(array($this,$method),$param);
				else call_user_func(array($this,$method),$param);
			}
			else
			{
				if($this->call_by=='func')
				$this->where($method.'="'.$param.'"');
				elseif($this->call_by=='smarty')
				{
					if(strpos($param,'$')===false)
					$this->where($method."='".$param."'");
					else
					$this->where($method."='{".$param."}'");
				}
				else
				Error::info('Error: SelectDB 错误的参数',"<pre>参数$method=$param</pre>");
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
		$sql=trim("select count(*) as cc from {$this->table} {$this->join} {$this->where} {$this->group}");
		$res=$this->db->query($sql)->fetch();
		return $res['cc'];
	}

	function insert($data)
	{
		$field="";
		$values="";
		foreach($data as $key => $value)
		{
			$value = str_replace("'","\'",$value);
			$field=$field."$key,";
			$values=$values."'$value',";
		}
		$field=substr($field,0,-1);
		$values=substr($values,0,-1);
		return $this->db->query("insert into {$this->table} ($field) values($values)");
	}

	function update($data)
	{
		$update="";
		foreach($data as $key=>$value)
		{
			$value = str_replace("'","\'",$value);
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