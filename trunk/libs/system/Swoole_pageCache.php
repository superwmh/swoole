<?php
//Swoole_pageCache��ҳ�滺����
class Swoole_pageCache
{
	var $cache_dir;
	var $expire;
	
	function __construct($expire=3600,$cache_dir='')
	{
		$this->expire = $expire;
		if($cache_dir==='') $this->cache_dir = WEBPATH.'/cache/pages_c';
		else $this->cache_dir = $cache_dir;		
	}
		
	//��������
	function create($content)
	{
		file_put_contents($this->cache_dir.'/'.base64_encode($_SERVER['REQUEST_URI']).'.html',$content);
	}
	
	//���ػ���
	function load()
	{
		require($this->cache_dir.'/'.base64_encode($_SERVER['REQUEST_URI']).'.html');
		exit;
	}
	
	//����Ƿ������Ч����
	function isCached()
	{
		$file=$this->cache_dir.'/'.base64_encode($_SERVER['REQUEST_URI']).'.html';
		if(!file_exists($file)) return false;
		elseif(filemtime ($file)+$this->expire<time()) return false;
		else return true;
	}
}
?>