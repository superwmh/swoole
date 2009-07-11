<?php
function smarty_function_getall($params, &$smarty)
{
	$record_name = $params['_name'];	
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
			$start=10*intval($params['page']/10);
			if($select->pages>10 and $params['page']<$start) $smarty->assign("more",1);
			$smarty->assign("start",$start);
			$smarty->assign("end",$select->pages-$start);
			$smarty->assign("pages",$select->pages);
			$smarty->assign("pagesize",$select->page_size);
			$smarty->assign("num",$select->num);
		}
		$records = $select->getall();
		$smarty->_tpl_vars[$record_name] = $records;
	endif;
}
?>
