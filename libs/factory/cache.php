<?php
$cache_server = parse_url(CACHE_URL);
if(!isset($cache_server['scheme'])) Error::info('Config.php Error','Cache url error!');
if($cache_server['scheme']=='file')
{
	$cache = new FileCache(FILECACHE_DIR.'/'.$cache_server['fragment'].'.fc');
}
elseif($cache_server['scheme']=='memcache')
{
	$cache = new Memcache;
	if(!isset($cache_server['port'])) $cache_server['port']=11211;
	$cache->connect($cache_server['host'],$cache_server['port']);
}
?>