<?php
interface IQueue
{
	function put($data);
	function get();
}
class Queue
{
    public $server;

	function __construct($config,$server_type)
    {
    	import('#queue.'.$server_type);
    	$this->server = new $server_type($config);
    }

    function put($data)
    {
    	return $this->server->put($data);
    }

    function get()
    {
    	return $this->server->get();
    }

    function __call($method,$param=array())
    {
    	return call_user_func_array(array($this->$server,$method),$param);
    }
}
?>