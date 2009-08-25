<?php
/**
 * 附加工具集合
 * @author Tianfeng.Han
 * @package SwooleSystem
 */
class Swoole_tools
{
	static function howLongAgo($datetime)
	{
		$seconds = time() - strtotime($datetime);
		$time = intval($seconds/31104000);
		if($time>=1) return $time.'年前';
		$time = intval($seconds/2592000);
		if($time>=1) return $time.'个月前';
		$time = intval($seconds/86400);
		if($time>=1) return $time.'天前';
		$time = intval($seconds/3600);
		if($time>=1) return $time.'小时前';
		$time = intval($seconds/60);
		if($time>=1) return $time.'分钟前';
		return $seconds.'秒前';
	}
	static function array_fullness($array)
	{
		$nulls = 0;
		foreach($array as $v) if(empty($v) or intval($v)<0) $nulls++;
		return 100-intval($nulls/count($array)*100);
	}
}
?>