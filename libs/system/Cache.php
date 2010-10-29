<?php
/**
 * 缓存制造类，缓存基类
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage cache
 *
 */
class Cache
{
    public $uri;
    public $cache;
    static $memcache_compress = 1;

    function __construct($cache_url)
    {
        $this->uri = parse_url($cache_url);
        if(!isset($this->uri['scheme'])) Error::info('Config.php Error','Cache url error!');
        $this->cache = $this->get_cache();
    }

    /**
     * 获取缓存对象
     * @param $scheme
     * @return cache object
     */
    function get_cache()
    {
        switch($this->uri['scheme'])
        {
            case 'memcache':
                $obj = new Memcache;
                $obj->connect($this->uri['host'],isset($this->uri['port'])?$this->uri['port']:11211);
                return $obj;
            case 'file':
                $obj = new FileCache(FILECACHE_DIR.'/'.$this->uri['fragment'].'.fc');
                return $obj;
            default:
                return;
        }
    }

    /**
     * 获取键的值
     * @param $key
     * @return unknown_type
     */
    function get($key)
    {
    	return $this->cache->get($key);
    }

    /**
     * 设置键值
     * @param $key--键名
     * @param $value--值
     * @param $expire--过期时间
     * @return unknown_type
     */
    function set($key,$value,$expire=600)
    {
        if($this->uri['scheme']=='memcache')
            $this->cache->set($key,$value,self::$memcache_compress,$expire);
        else
            $this->cache->set($key,$value,$expire);
        return true;
    }

    /**
     * 删除键值
     * @param $key
     * @return unknown_type
     */
    function delete($key)
    {
        $this->cache->delete($key);
    }

    function save()
    {
    	if($this->uri['scheme']=='file') $this->cache->save();
    }

    /*function lock($lock_key)
    {
    	$lock = 'l';
    	while($lock=='l')
    	{
    		$lock = $this->get($lock_key);
    		if($lock==)
    	}
    	return false;
    }*/

   // function

    function __call($method,$params)
    {
        return call_user_func_array(array($this->cache,$method),$params);
    }
}
?>