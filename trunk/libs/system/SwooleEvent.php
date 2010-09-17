<?php
class SwooleEvent
{
	private $_queue;
	private $_handles = array();
	public $mode;

	function __construct($mode,$queue_url='',$queue_type='')
	{
		$this->mode = $mode;
		if($queue_url and $mode=='async')
		{
			$this->_queue = new Queue(array('server_url'=>$queue_url),$queue_type);
		}
	}
	/**
	 * 引发一个事件
	 * @param $event_type 事件类型
	 * @return NULL
	 */
	function raise()
	{
		$params = func_get_args();
		/**
		 * 同步，直接在引发事件时处理
		 */
        if($this->mode=='sync')
        {
        	if(!isset($this->_handles[$params[0]]) or !function_exists($params[0]))
        	{
        		if(empty($handle)) Error::info('SwooleEvent Error','Event handle not found!');
        	}
        	return call_user_func_array($params[0],array_slice($params,1));
        }
        /**
         * 异步，将事件压入队列
         */
        else
        {
            $this->_queue->put($params);
        }
	}
    /**
     * 增加对一种事件的监听
     * @param $event_type 事件类型
     * @param $call_back  发生时间后的回调程序
     * @return NULL
     */
	function addListener($event_type,$call_back)
	{
		$this->_handles[$event_type] = $call_back;
	}

	function run_server($time=1,$log_file=null)
	{
		if($log_file) $filelog = new FileLog($log_file);
		while(true)
		{
		    $event = $this->_queue->get();
			if($event)
			{
				if(!isset($this->_handles[$params[0]]) or !function_exists($params[0]))
	            {
	                if(empty($handle)) Error::info('SwooleEvent Error','Event handle not found!');
	            }
	            else
	            {
	            	call_user_func_array($event[0],array_slice($event,1));
                    if($log_file) $filelog->info('Raise a event,type '.$event[0]);
	            }
			}
		    else
		    {
		    	usleep($time*1000);
		    	echo 'sleep',NL;
		    }
		}
	}
    /**
     * 设置监听列表
     * @param $listens
     * @return unknown_type
     */
	function set_listens($listens)
	{
		$this->_handles = array_merge($this->_handles,$listens);
	}
}
?>