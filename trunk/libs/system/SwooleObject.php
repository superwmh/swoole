<?php 
class SwooleObject extends ArrayObject
{
	public function __construct($array = array())
    {
        parent::__construct($array);
    }

	function __set($keyname,$value)
	{
		$this->offsetSet($keyname,$value);
	}
	
	function __get($keyname)
	{
		return $this->offsetGet($keyname);
	}
}
?>