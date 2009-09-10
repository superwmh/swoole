<?php
/**
 * 缓存制造类
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage cache
 *
 */
class Cache
{
    var $uri;
    var $cache;
    
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
                $obj = new FileCache($cache_server['fragment']);
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
    	return $default;
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
            $this->cache->set($key,$value,0,$expire);
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
    
    function __call($method,$params)
    {
        return call_user_method_array($method,$this->cache,$params);
    }
}
?>