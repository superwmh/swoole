<?php
/**
 * 附加工具集合
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage tools
 */
class Swoole_tools
{
	static function howLongAgo($datetime)
	{
		$timestamp = strtotime($datetime);
		$seconds = time();

		$time = date('Y',$seconds)-date('Y',$timestamp);
		if($time>0) return $time.'年前';

		$time = date('m',$seconds)-date('m',$timestamp);
		if($time>0) return $time.'个月前';
		$time = date('d',$seconds)-date('d',$timestamp);
		if($time>0)
		{
			if($time==1) return '昨天';
			elseif($time==2) return '前天';
			else return $time.'天前';
		}

		$time = date('H',$seconds)-date('H',$timestamp);
		if($time>=1) return $time.'小时前';

		$time = date('i',$seconds)-date('i',$timestamp);
		if($time>=1) return $time.'分钟前';

		$time = date('s',$seconds)-date('s',$timestamp);
		return $time.'秒前';
	}
	static function array_fullness($array)
	{
		$nulls = 0;
		foreach($array as $v) if(empty($v) or intval($v)<0) $nulls++;
		return 100-intval($nulls/count($array)*100);
	}
}
?>