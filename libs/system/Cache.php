<?php
interface ICache
{
    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @param $expire
     * @return unknown_type
     */
    function set($key,$value,$expire=0);
    /**
     * 获取缓存值
     * @param $key
     * @return unknown_type
     */
    function get($key);
    /**
     * 删除缓存值
     * @param $key
     * @return unknown_type
     */
    function delete($key);
}
/**
 * 缓存制造类，缓存基类
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage cache
 *
 */
class Cache
{
    public $cache;
    static $backends = array(
        'apc'=>'ApcCache',
        'file'=>'FileCache',
        'memcache'=>'CMemcache',
        'eac'=>'EAcceleratorCache',
        'xcache'=>'XCache',
        'dbcache'=>'DBCache',
        'wincache'=>'WinCache');

    function __construct($cache_url)
    {
        $config = Swoole_tools::uri($cache_url);
        $this->cache = self::get_cache($config);
    }
    /**
     * 获取缓存对象
     * @param $scheme
     * @return cache object
     */
    static function get_cache($config)
    {
        if(empty(self::$backends[$config['protocol']])) return Error::info('Cache Error',"cache backend:{$config['protocol']} no support");
        $backend = self::$backends[$config['protocol']];
        import('#cache.'.$backend);
        return new $backend($config);
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
        return $this->cache->set($key,$value,$expire);
    }
    /**
     * 删除键值
     * @param $key
     * @return unknown_type
     */
    function delete($key)
    {
        return $this->cache->delete($key);
    }

    function __call($method,$params)
    {
        return call_user_func_array(array($this->cache,$method),$params);
    }
}