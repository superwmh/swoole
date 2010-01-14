<?php
global $php;
SiteDict::$swoole = $php;

class SiteDict
{
	static $swoole;
	static $cache_life = 600;
	static $data_dir = DICTPATH;
	var $table = 'site_dict';
	
	function __construct()
	{
		#import('app.SwooleDict');
	}
	static function get($dictname)
	{
		if(!self::$swoole->cache) Error::info('SiteDict Cache Error','Please load Cache!');
		$cache_key = 'sitedict_'.$dictname;
		$$dictname = self::$swoole->cache->get($cache_key);
		if(empty($$dictname))
		{
			require(self::$data_dir.'/'.$dictname.'.php');
			self::$swoole->cache->set($cache_key,$$dictname,self::$cache_life);
		}
		return $$dictname;
	}
	static function set($dictname,$dict)
	{
		if(!self::$swoole->cache) Error::info('SiteDict Cache Error','Please load Cache!');
		$filename = self::$data_dir.'/'.$dictname.'.php';
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