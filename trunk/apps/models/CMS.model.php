<?php
class CMS
{
	var $db;
	var $swoole;
	var $smarty;	

	function __construct($db)
	{
		$this->db = $db;
	}
	
	function get(&$params)
	{
		$method = 'get'.ucfirst($params['get']);
		unset($params['get'],$params['item'],$params['key'],$params['func']);
		return $this->$method($params);
	}
	
	function getContent(&$attrs)
	{		
		$select = new SelectDB($this->db);
		$select->call_by = 'smarty';

		if(isset($attrs['typeid']))
		{
			$attrs['catid'] = $attrs['typeid'];
			unset($attrs['typeid']);
			
			if(is_numeric($attrs['typeid']))
			{
				$cate = $this->swoole->createModel('Category');
				$category = $cate->get($attrs['fid']);
				$select->from(TABLE_PREFIX.'_'.$category['modelname']);	
			}
			elseif(isset($attrs['app']))
			{
				$app = $this->swoole->createModel('App');
				$app_config = $app->getConfig($attrs['app']);
				$select->from($app_config['tablename']);
				unset($attrs['app']);
			}
			else
				Error::info('Tpl Error','Nested structure must have `app` param!');
		}
		elseif(isset($attrs['app']))
		{
			$app = $this->swoole->createModel('App');
			$app_config = $app->getConfig($attrs['app']);
			$select->from($app_config['tablename']);
			unset($attrs['app']);
		}
		else
			Error::info('Param Error','Get content must have param `typeid` or `app` !');
		
		if(array_key_exists('select',$attrs))
		{
			$select->select = $attrs['select'];
		}
		if(array_key_exists('titlelen',$attrs))
		{		
			$titlelen = $attrs['titlelen'];
			unset($attrs['titlelen']);
			$select->select = str_replace('title',"substring( title, 1, $titlelen ) AS title,title as title_full",$attrs['select']);
		}
		
		$select->limit(isset($attrs['row'])?$attrs['row']:10);
		unset($attrs['row']);
		
		$select->order(isset($attrs['order'])?$attrs['order']:'id desc');
		unset($attrs['order']);
		
		unset($attrs['get'],$attrs['name'],$attrs['key'],$attrs['func']);
		$select->put($attrs);
		
		if(array_key_exists('page',$attrs))
		{
			$select->paging();
			$php->env['start'] = 10*intval($attrs['page']/10);
			if($select->pages>10 and $attrs['page']<$start)
				$php->env['more'] = 1;
			$php->env['end'] = $select->pages-$php->env['start'];
			$php->env['end'] = $select->pages;
			$php->env['end'] = $select->page_size;
			$php->env['end'] = $select->num;
		}
		return $select->getsql();
	}
	
	function getCategory(&$attrs)
	{
		$cate = $this->swoole->createModel('Category');

		$select = new SelectDB($this->db);
		$select->call_by = 'smarty';
		$select->from($cate->table);
		
		if(array_key_exists('app',$attrs))
		{
			$app = $this->swoole->createModel('App');
			$app_config = $app->getConfig($attrs['app']);
			
			unset($attrs['app']);
			$attrs['fid'] = $app_config['category'];
		}
		unset($attrs['get'],$attrs['name'],$attrs['key'],$attrs['func']);
		$select->put($attrs);		
		return $select->getsql();
	}
	
	function getPlot(&$attrs)
	{
		$select = new SelectDB($this->db);
		$select->call_by = 'smarty';
		$select->from(TABLE_PREFIX.'_plot');
		
		unset($attrs['get'],$attrs['name'],$attrs['key'],$attrs['func']);
		$select->put($attrs);
		return $select->getsql();
	}
}
?>