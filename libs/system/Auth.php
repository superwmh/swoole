<?php
/**
 * 用户验证类
 * @author Han Tianfeng
 */
class Auth
{
	var $table = '';
	var $db = '';
	
	function __construct($db,$table='')
	{
		if($table=='') $this->table = TABLE_PREFIX.'_user';
		$this->db = $db;
	}
	/**
	 * 登录
	 * @param $username
	 * @param $password
	 * @param $auto
	 * @return unknown_type
	 */
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
	/**
	 * 检查是否登录
	 * @return unknown_type
	 */
	function isLogin()
	{
		if(isset($_SESSION['isLogin']) and $_SESSION['isLogin']==1) return true;
		elseif(isset($_COOKIE['password']))
		{
			return $this->login($_COOKIE['username'],$_COOKIE['password'],$auto=1);
		}
		return false;
	}
	/**
	 * 自动登录，如果自动登录则在本地记住密码
	 * @param $user
	 * @return unknown_type
	 */
	function autoLogin($user)
	{
		$ip = Swoole_client::getIP();
		setcookie('username',$user['username'],time() + 2592000,'/');
		setcookie('password',$user['password'],time() + 2592000,'/');
		setcookie('ip',$ip,time() + 2592000,'/');
		setcookie('id',$user['id'],time() + 2592000,'/');
	}
	/**
	 * 注销登录
	 * @return unknown_type
	 */
	function logout()
	{
		if(isset($_SESSION['isLogin'])) unset($_SESSION['isLogin']);
		elseif(isset($_COOKIE['password']))
		{
			setcookie('password','',0,'/');
		}
	}
	/**
	 * 产生一个密码串，连接用户名和密码，并使用sha1产生散列
	 * @param $username
	 * @param $password
	 * @return $password_string 40位的散列
	 */
	public static function mkpasswd($username,$password)
	{
		return sha1($username.$password);
	}
}
?>