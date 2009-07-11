<?php
class App extends Model
{
	var $table = 'system_apps';
	var $app;
	var $appid;
	
	var $app_config;	
	var $default_fields;

	function createTable($appid)
	{
		$app = $this->get($appid);
		$fields = $this->getFields($appid);
		$sql = "CREATE TABLE `{$app['tablename']}` (\n";
		$sql.= load_data('default_fields');
		foreach($fields as $field)
		{
			$sql.=self::fieldSQL($field);
		}
		$sql.="PRIMARY KEY  (`id`)\n";
		$sql.=") ENGINE=MyISAM  DEFAULT CHARSET=".DBCHARSET.";";
		$this->db->query($sql);
	}
	
	public static function fieldType($field)
	{
		$field_name = "`{$field['name']}`";
		
		$field_types = load_data('field_types');
		$db_type = $field['dtype'];
		
		$ftype = $field_name.' ';
		
		switch($db_type)
		{
			case 'int':
				if($field['length']<=8) $db_type='tinyint';
				$ftype .= "{$db_type}({$field['length']})";
				break;	
			case 'varchar':
				if(empty($field['length'])) $field['length']=128;
				$ftype .= "{$db_type}({$field['length']})";
				break;
			case 'upload':
				if(empty($field['length'])) $field['length']=64;
				$ftype .= "{$db_type}({$field['length']})";
				break;
			case 'resource':
				return '';
			case 'url':
				if(empty($field['length'])) $field['length']=64;
				$ftype .= "{$db_type}({$field['length']})";
				break;
			case 'htmltext':
				$ftype .= "mediumtext";
				break;	
			default:
				$ftype .= "{$db_type}";
				break;
		}
		
		if($field['ifnull']) $ftype .=" NULL";
		else $ftype .=" NOT NULL";
		
		if(!empty($field['defaultvalue'])) $ftype .=" DEFAULT '{$field['defaultvalue']}'";
		
		return $ftype;
	}
	
	public static function fieldSQL($field)
	{
		$sql = '  ';		
		$sql .= self::fieldType($field);
		
		if($field['ifindex']) $sql.="  INDEX($field_name),\n";
		if($field['ifunique']) $sql.="  UNIQUE($field_name),\n";
		return $sql;
	}
	
	function getConfig($appname)
	{
		$res = $this->all();
		$res->filter("name='$appname'");
		$res->limit(1);
		$this->app_config = $res->fetch();
		$this->appid = $this->app_config ['id'];
		return $this->app_config;
	}
	
	function getConfigById($appid)
	{
		$this->appid = $appid;
		$this->app_config = $this->get($appid);
		return $this->app_config;
	}
	
	function getInstance($app_config='')
	{
		$this->app = new Model($this->db);
		if(empty($app_config))
			$this->app->table = $this->app_config['tablename'];
		else
			$this->app->table = $app_config['tablename'];
		return $this->app;
	}
	
	function getFields($appid='')
	{
		if(empty($appid)) $appid=$this->appid;
		return $this->db->query('select * from system_fields where appid='.$appid.' order by orderid')->fetchall();
	}
	
	function getTableFields($tablename)
	{
		return $this->db->query("describe $tablename")->fetchall();
	}
	
	function addField($field)
	{
		$fieldtype = self::fieldType($field);
		$app =  $this->get($this->appid);
		$sql = "ALTER TABLE `{$app['tablename']}` ADD $fieldtype after `{$field['after']}`";
		$this->db->query($sql);
		
		if($field['ifindex'])
			$this->db->query("ALTER TABLE `{$app['tablename']}` ADD INDEX(`{$field['name']}`)");
		if($field['ifunique'])
			$this->db->query("ALTER TABLE `{$app['tablename']}` ADD UNIQUE(`{$field['name']}`)");
		
		return true;
	}
}
?>