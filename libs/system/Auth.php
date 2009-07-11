<?php
/**
 * 用户验证类
 * @author Han Tianfeng
 */
class Auth
{
	var $table='chq_user';
	
	function __construct($db,$table='')
	{
		if($table=='') $this->table = TABLE_PREFIX.'_user';
		$this->db = $db;
	}
	
	function login($username,$password,$auto)
	{
		setcookie('username',$username,time() + 2592000,'/');
		$res = $this->db->query('select * from '.$this->table." where username='$username' and password ='$password'");
		$user = $res->fetch();
		if(empty($user)) return false;
		else
		{
			$_SESSION['isLogin']=true;	
			if($auto==1) $this->autoLogin($user);
			return true;
		}
	}
	function isLogin()
	{
		if(isset($_SESSION['isLogin']) and $_SESSION['isLogin']==1) return true;
		elseif(isset($_COOKIE['password']))
		{
			return $this->login($_COOKIE['username'],$_COOKIE['password'],$auto=1);
		}
		return false;
	}
	
	function autoLogin($user)
	{
		$ip = Swoole_client::getIP();
		setcookie('username',$user['username'],time() + 2592000,'/');
		setcookie('password',$user['password'],time() + 2592000,'/');
		setcookie('ip',$ip,time() + 2592000,'/');
		setcookie('id',$user['id'],time() + 2592000,'/');
	}
	function logout()
	{
		if(isset($_SESSION['isLogin'])) unset($_SESSION['isLogin']);
		elseif(isset($_COOKIE['password']))
		{
			setcookie('password','',0,'/');
		}
	}
	public static function mkpasswd($username,$password)
	{
		return sha1($username.$password);
	}
}
?>