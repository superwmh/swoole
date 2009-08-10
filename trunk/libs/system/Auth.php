<?php
/**
 * 用户验证类
 * @author Han Tianfeng
 */
class Auth
{
	var $table = '';
	static $login_url = '/login.php?';
	static $username = 'username';
	static $password = 'password';
	static $session_prefix = '';
	static $cookie_life = 2592000;
	var $db = '';
	
	function __construct($db,$table='')
	{
		if($table=='') $this->table = TABLE_PREFIX.'_user';
		else $this->table = $table;
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
		setcookie('username',$username,time() + self::$cookie_life,'/');
		$res = $this->db->query('select * from '.$this->table." where username='$username' and password ='$password'");
		$user = $res->fetch();
		if(empty($user)) return false;
		else
		{
			$_SESSION[self::$session_prefix.'isLogin']=true;
			$_SESSION[self::$session_prefix.'user_id']=$user['id'];
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
		if(isset($_SESSION[self::$session_prefix.'isLogin']) and $_SESSION[self::$session_prefix.'isLogin']==1) return true;
		elseif(isset($_COOKIE['autologin']) and isset($_COOKIE['username']) and isset($_COOKIE['password']))
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
		setcookie('autologin',1,time() + self::$cookie_life,'/');
		setcookie('username',$user['username'],time() + self::$cookie_life,'/');
		setcookie('password',$user['password'],time() + self::$cookie_life,'/');
		setcookie('ip',$ip,time() + self::$cookie_life,'/');
		setcookie('id',$user['id'],time() + self::$cookie_life,'/');
	}
	/**
	 * 注销登录
	 * @return unknown_type
	 */
	function logout()
	{
		if(isset($_SESSION[self::$check_login]))
		{
			session_destroy();
			if(isset($_COOKIE['password'])) setcookie('password','',0,'/');
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
	
	public static function login_require()
	{
		if(empty($_SESSION)) session();
		if(isset($_SESSION[self::$session_prefix.'isLogin']) and $_SESSION[self::$session_prefix.'isLogin']=='1') $check=true;
		if(!$check)
		{
			header('Location:'.self::$login_url.'refer='.urlencode($_SERVER["REQUEST_URI"]));
			exit;
		}
	}
}
?>