<?php
function smarty_function_getall($params, &$smarty)
{
	$record_name = $params['_name'];
	if(!isset($params['from']))
	{
		echo 'No table name!';
		return false;
	}
	if(!array_key_exists($record_name,$smarty->_tpl_vars)):
		global $php;
		$select = new SelectDB($php->db);
		$select->call_by = 'func';
		$select->put($params);

		if(!array_key_exists('order',$params))
			$select->order('id desc');
		
		if(array_key_exists('page',$params))
		{
			$select->paging();
            $pager = $select->pager;            
			$smarty->assign("pager",array('total'=>$pager->total,'render'=>$pager->render()));
		}
		$records = $select->getall();
		$smarty->_tpl_vars[$record_name] = $records;
	endif;
}
?>
