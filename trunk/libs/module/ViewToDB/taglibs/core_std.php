<?php
function proc_records($tpl,$loopname)
{
	print_r($loopname);
	$tagname=$this->tagtrim($tag);
	
	$exp=explode(":",$tagname);
	if(count($exp)>2) die("错误的标签:".$tag);
	
	$tag_proc = 'proc_'.$exp[0];
	//	extract(process_exp($loopname));
//	$head=$this->lefttag."loop:".$loopname.$this->righttag;
//	$cstart = strpos($this->content,$head);
//	$cend = strpos($this->content,"{{/loop}}");
//	$content = substr($this->content,$cstart,$cend-$cstart);
//	$tagscontent=$content."{{/loop}}";
//	$content= str_replace($head,"",$content);
//	$res=$this->db->query("select * from $table limit $num");
//	$list=$res->fetchall();
//	$resultcontent=$this->process_loopvar($content,$list);
//	$this->content=str_replace($tagscontent,$resultcontent,$this->content);
}
?>