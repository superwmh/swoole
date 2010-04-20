<?php
class Swoole_config
{
    public $string;
    public $configs = array();
    const ROW_SG = "\n";
    const COL_SG = ':';
    
	function __construct($string)
	{
	    $this->string = trim($string);
	    $this->split();
	}
	
	function get($key='')
	{
	    if(empty($key)) return $this->configs;
	    elseif(array_key_exists($key)) return $this->configs[$key];
	    return false;
	}
	
	function split()
	{
	    $options = explode(self::ROW_SG,$this->string);
	    foreach($options as $value)
	    {
	        $option = explode(self::COL_SG,$value);
	        if(count($option)!==2) Error::info('Config Error','Config String format error!'."\n{$this->string}");
	        $this->configs[trim($option[0])] = trim($option[1]);
	    }
	}
	
}
?>