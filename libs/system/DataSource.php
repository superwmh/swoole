<?php
class DataSource
{
	var $swoole;
	var $ds_uri;
	
	function __construct($swoole,$dsn)
	{
		$this->swoole = $swoole;
		$this->ds_uri = $dsn;
	}
	
	function fetch()
	{
		$dsn = self::parseDsn($this->ds_uri);
		$method = 'fetch_'.$dsn['dtype'];
		return $this->$method($dsn['dname'],$dsn['params']);
	}
	
	function fetch_model($dname,$params)
	{
		$model = $this->swoole->createModel($dname);
		if(array_key_exists('func',$params))
		{
			$func=$params['func'];
			unset($params['func']);
		}
		else $func = 'getList';
		return call_user_method_array($func,$model,$params);
	}
	
	function fetch_select($dname,$params)
	{
		$select = new SelectDB($this->swoole->db);
		$select->from($dname);
		$select->put($params);
		return $select->getall();		
	}
	
	static function parseDsn($dns)
	{
		$result = array();
		$dns_array = explode(':',trim($dns));
		
		$result['dtype'] = $dns_array[0];
		
		$params_array = explode(' ',trim($dns_array[1]));
		$result['dname'] = $params_array[0];
		
		$count = count($params_array);
		for($i=1;$i<$count;$i++)
		{
			$p = explode('=',trim($params_array[$i]));
			$result['params'][$p[0]] = $p[1];
		}
		return $result;
	}
}

function smarty_block_app($params, $body, &$smarty)
{
	if (empty($body)) return;
	
	if(array_key_exists('model',$params))
	{
		$model=$params['model'];
		unset($params['model']);
	}
	else $model = 'news';
	
	if(array_key_exists('func',$params))
	{
		$func=$params['func'];
		unset($params['func']);
	}
	else $func = 'getList';
	
	$fields = implode(',',SwooleTemplate::get_fields($body));
	global $php;
	$php->createModel($model);
	
	if(array_key_exists('titlelen',$params))
	{		
		$titlelen = $params['titlelen'];
		unset($params['titlelen']);
		$php->model->$model->select = str_replace('title',"substring( title, 1, $titlelen ) AS title",$fields);
	}
	else
	{
		$php->model->$model->select = $fields;
	}
	$data = call_user_method($func,$php->model->$model,$params);
	//var_dump($data);
	return SwooleTemplate::parse_loop($data,$body,$fields);
}
?>