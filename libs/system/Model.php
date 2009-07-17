<?php
/**
 * Model类，ORM基础类，提供对某个数据库表的接口
 * @author Administrator
 * @package SwooleSystem
 *
 */
class Model
{
	var $_data=array(); //数据库字段的具体值
	var $db;
	var $primary="id";
	
	var $foreignkey='catid';
	
	var $table="";
	var $fields;
	var $select='*';
	
	var $create_sql='';
	
	function __construct($db)
	{
		$this->db = $db;
		//$this->fields = $db->query('describe '.$this->table)->fetchall();
	}
	
	/**
	 * 获取主键$primary_key为$object_id的一条记录对象(Record Object)
	 * 如果参数为空的话，则返回一条空白的Record，可以赋值，产生一条新的记录
	 * @param $object_id
	 * @return Record Object
	 */
	public function get($object_id=0)
	{
		return new Record($object_id,$this->db,$this->table,$this->primary);
	}
	/**
	 * 获取表的一段数据，查询的参数由$params指定
	 * @param $params
	 * @return Array
	 */
	public function gets($params)
	{
	    $selectdb = new SelectDB($this->db);
		$selectdb->from($this->table);
		$selectdb->primary = $this->primary;
		$selectdb->select($this->select);
		$selectdb->order($this->primary." desc");
		$selectdb->put($params);
		return $selectdb->getall();
	}
	/**
	 * 插入一条新的记录到表
	 * @param $data Array 必须是键值（表的字段对应值）对应
	 * @return None
	 */
	public function put($data)
	{
		$this->db->insert($data,$this->table);
		return $this->db->Insert_ID();
	}
	/**
	 * 更新记录
	 * @param $id
	 * @param $data
	 * @param $where
	 * @return true/false
	 */
	function sets($id,$data,$where='')
	{
		if(empty($where)) $where=$this->primary;
		return $this->db->update($id,$data,$this->table,$where);
	}
	/**
	 * 获取到所有表记录的接口，通过这个接口可以访问到数据库的记录
	 * @return RecordSet Object (这是一个接口，不包含实际的数据)
	 */
	public function all()
	{
		return new RecordSet($this->db,$this->table,$this->primary,$this->select);
	}
	/**
	 * 建立表，必须在Model类中，指定table_sql
	 * @return unknown_type
	 */
	function createTable()
	{
		$this->db->query($this->table_sql);
	}
	/**
	 * 获取表状态
	 * @return array 表的status，包含了自增ID，计数器等状态数据
	 */
	function getStatus()
	{
		return $this->db->query("show table status from ".DBNAME." where name='{$this->table}'")->fetch();
	}
	/**
	 * 获取一个数据列表，功能类似于gets，此方法仅用于SiaoCMS，不作为同样类库的方法
	 * @param $params
	 * @param $get
	 * @return unknown_type
	 */
	function getList(&$params,$get='data')
	{
		$selectdb = new SelectDB($this->db);
		$selectdb->from($this->table);
		$selectdb->select($this->select);
		$selectdb->limit(isset($params['row'])?$params['row']:10);
		unset($params['row']);
		$selectdb->order(isset($params['order'])?$params['order']:$this->primary.' desc');
		unset($params['order']);

		if(isset($params['typeid']))
		{
			$selectdb->where($this->foreignkey.'='.$params['typeid']);
			unset($params['typeid']);
		}
		$selectdb->put($params);
		if(array_key_exists('page',$params))
		{
			$selectdb->paging();
			global $php;
			$php->env['page'] = $params['page'];
			$php->env['start'] = 10*intval($params['page']/10);
			if($selectdb->pages>10 and $params['page']<$start)
				$php->env['more'] = 1;
			$php->env['end'] = $selectdb->pages-$php->env['start'];
			$php->env['pages'] = $selectdb->pages;
			$php->env['pagesize'] = $selectdb->page_size;
			$php->env['num'] = $selectdb->num;
		}
		if($get==='data') return $selectdb->getall();
		elseif($get==='sql') return $selectdb->getsql();
	}
}
/**
 * Record类，表中的一条记录，通过对象的操作，映射到数据库表
 * 可以使用属性访问，也可以通过关联数组方式访问
 * @author Administrator
 * @package SwooleSystem
 */
class Record implements ArrayAccess
{
	var $_data = array();
	var $_change;
	var $db;

	var $primary="id";
	var $table="";

	var $change=0;
	var $_current_id=0;

	function __construct($id,$db,$table,$primary)
	{
		$this->db=$db;
		$this->_current_id=$id;
		$this->table=$table;
		$this->primary=$primary;
		if(!empty($this->_current_id))
		{
			$res=$this->db->query('select * from '.$this->table.' where '.$this->primary."='$id' limit 1");
			$this->_data=$res->fetch();
			$this->change=1;
		}
	}
	/**
	 * 将关联数组压入object中，赋值给各个字段
	 * @param $data
	 * @return unknown_type
	 */
	function put($data)
	{
		if($this->change == 1)
		{
			$this->change = 2;
			$this->_change = $data;
		}
		elseif($this->change==0)
		{
			$this->change = 1;
			$this->_data=$data;
		}
	}
	/**
	 * 获取数据数组
	 * @return unknown_type
	 */
	function get()
	{
		return $this->_data;
	}

	function __get($property)
	{
		if(array_key_exists($property,$this->_data)) return $this->_data[$property];
		else die("Class Model no property: $property");
	}

	function __set($property,$value)
	{
		if($this->change==1 or $this->change==2)
		{
			$this->change=2;
			$this->_change[$property]=$value;
		}
		else
		{
			$this->_data[$property]=$value;
		}
		return true;
	}
	/**
	 * 保存对象数据到数据库
	 * 如果是空白的记录，保存则会Insert到数据库
	 * 如果是已存在的记录，保持则会update，修改过的值，如果没有任何值被修改，则不执行SQL
	 * @return unknown_type
	 */
	function save()
	{
		if($this->change==0)
		{
			$this->db->insert($this->_data,$this->table);
			$this->_current_id=$this->db->lastInsertId();
		}
		elseif($this->change==2)
		{
			unset($this->_data[$this->primary]);
			$this->db->update($this->_current_id,$this->_change,$this->table,$this->primary);
		}
		return true;
	}
	/**
	 * 删除数据库中的此条记录
	 * @return unknown_type
	 */
	function delete()
	{
		$this->db->delete($this->_current_id,$this->table,$this->primary);
	}

	function offsetExists($keyname)
	{
		return array_key_exists($keyname,$this->_data);
	}

	function offsetGet($keyname)
	{
		return $this->_data[$keyname];
	}

	function offsetSet($keyname,$value)
	{
		$this->_data[$keyname] = $value;
	}

	function offsetUnset($keyname)
	{
		unset($this->_data[$keyname]);
	}
}

class RecordSet implements Iterator
{
	var $_list=array();
	
	var $table='';
	var $db;
	var $db_select;
	
	var $primary="";

	var $_current_id=0;

	function __construct($db,$table,$primary,$select)
	{
		$this->table=$table;
		$this->primary=$primary;
		$this->db = $db;
		$this->db_select = new SelectDB($db);
		$this->db_select->from($table);
		$this->db_select->primary = $primary;
		$this->db_select->select($select);
		$this->db_select->order($this->primary." desc");
		if(!empty($limit)) $this->db_select->limit($limit);
	}
	
	function filter($where)
	{
		$this->db_select->where($where);
	}
	
	function orfilter($where)
	{
		$this->db_select->orwhere($where);
	}
	
	function fetch($field='')
	{
		return $this->db_select->getone($field);
	}
	
	function __call($method,$argv)
	{
		return call_user_method_array($method,$this->db_select,$argv);
	}

	public function rewind()
	{
		if(empty($this->_list)) $this->_list = $this->db_select->getall();
		$this->_current_id=0;
	}

	public function key()
	{
		return $this->_current_id;
	}

	public function current()
	{
		$record = new Record(0,$this->db,$this->table,$this->primary);
		$record->put($this->_list[$this->_current_id]);
		$record->_current_id = $this->_list[$this->_current_id][$this->primary];
		return $record;
	}

	public function next()
	{
		$this->_current_id++;
	}

	public function valid()
	{
		if(isset($this->_list[$this->_current_id])) return true;
		else return false;
	}
}
?>