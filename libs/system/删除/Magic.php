<?php
class Magic
{
	var $swoole;

	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}
	
	function render($type,$name,$params)
	{
		
	}
	
	function pager(&$params)
	{
		$pager = new Pager(array('total'=>$params['num'],'perpage'=>intval($params['pagesize'])));
		return $pager->render(@$params['mode']);
	}
	
	function comment(&$params)
	{
		return "<iframe frameborder=\"0\" src=\"/index.php?controller=SiaoCMS&view=comment&aid={$params['aid']}&app={$params['app']}\" scrolling=\"no\" width=\"746\" id=\"comment\" name=\"comment\"></iframe>";
	}
}
?>