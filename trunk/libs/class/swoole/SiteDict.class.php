<?php
global $php;
SiteDict::$swoole = $php;

class SiteDict
{
	static $swoole;
	static $cache_life = 600;
	
	function __construct($swoole)
	{
		self::$swoole = $swoole;
	}
	static function get($dictname)
	{
		$cache_key = 'sitedict_'.$dictname;
		$$dictname = self::$swoole->cache->get($cache_key);
		if(empty($$dictname))
		{
			require(WEBPATH.'/dict/'.$dictname.'.php');
			self::$swoole->cache->set($cache_key,$$dictname,self::$cache_life);
		}
		return $$dictname;
	}
	static function set($dictname,$dict)
	{
		$filename = WEBPATH.'/dict/'.$dictname.'.php';
		file_put_contents($filename,"<?php\n\${$dictname}=".var_export($dict,true).';');
		$cache_key = 'sitedict_'.$dictname;
		self::$swoole->cache->delete($cache_key);
	}
	static function delete($dictname)
	{
		$cache_key = 'sitedict_'.$dictname;
		self::$swoole->cache->delete($cache_key);
	}
}
?>