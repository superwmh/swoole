<?php
/**
 * 客户端工具
 * @author Administrator
 * @package SwooleSystem
 *
 */
class Swoole_client
{
	public static function getIP()
	{
	   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
			   $ip = getenv("HTTP_CLIENT_IP");
		   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			   $ip = getenv("HTTP_X_FORWARDED_FOR");
		   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
			   $ip = getenv("REMOTE_ADDR");
		   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
			   $ip = $_SERVER['REMOTE_ADDR'];
		   else
			   $ip = "unknown";
	   return($ip);
	}
	public static function getBrowser()
	{
		if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(myie[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Netscape[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Opera[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(NetCaptor[^;^^()]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(TencentTraveler)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Firefox[0-9/\.^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(MSN[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Lynx[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Konqueror[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(WebTV[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(msie[^;^)^(]*)|i" ) );
		else if( $Browser = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Maxthon[^;^)^(]*)|i" ) );
		else $Browser = '其它';
		return $Browser;
	}
	public static function getOS()
	{
		if( $System = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Windows NT[\ 0-9\.]*)|i" ) );
		else if( $System = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Windows[\ 0-9\.]*)|i" ) );
		else if( $System = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Mac[^;^)]*)|i" ) );
		else if( $System = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(unix)|i" ) );
		else if( $System = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(Linux[\ 0-9\.]*)|i" ) );
		else if( $System = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(SunOS[\ 0-9\.]*)|i" ) );
		else if( $System = self::matchbrowser( $_SERVER["HTTP_USER_AGENT"], "|(BSD[\ 0-9\.]*)|i" ) );
		else $System = '其它';
		$System = trim($System);
		return($System);
	}
	public static function matchbrowser( $Agent, $Patten )
	{
		if( preg_match( $Patten, $Agent, $Tmp ) )
		{
			return $Tmp[1];
		}
		else
		{
			return false;
		}
	}
}
?>