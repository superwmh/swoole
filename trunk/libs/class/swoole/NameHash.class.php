<?php
class NameHash
{
	static function idhash($uid,$base=1000)
	{
		return intval($uid/$base);
	}
	
	static function filename($type,$seed=null,$length=8)
	{
		switch($type)
		{
			case 'time':
				$filename = rand(1111,9999).substr(time(),-6,$length);
			case 'md5':
				$filename = substr(md5($seed),0,$length);
		}
		return $filename;
	}
	
	static function randmd5($seed,$length=8)
	{
		return substr(md5($seed.rand(111111,999999)),0,$length);
	}
	
}
?>