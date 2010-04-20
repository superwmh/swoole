<?php
/**
 * 用户验证类
 * @author Han Tianfeng
 * @package SwooleSystem
 * @subpackage Login
 */
class Auth
{
	public $table = '';
	static $login_url = '/login.php?';
	static $username = 'username';
	static $password = 'password';
	static $session_prefix = '';
	//static $password_field = 'username,password';
	static $password_hash = 'sha1';
	static $cookie_life = 2592000;

	public $db = '';
	public $user;

	function __construct($db,$table='')
	{
		if($table=='') $this->table = TABLE_PREFIX.'_user';
		else $this->table = $table;
		$this->db = $db;
	}
	function saveUserinfo($key='userinfo')
	{
		$_SESSION[self::$session_prefix.$key] = $this->user;
	}
	function setSession($key)
	{
		$_SESSION[$key] = $this->user[$key];
	}
	/**
	 * 登录
	 * @param $username
	 * @param $password
	 * @param $auto
	 * @param $save 保存用户登录信息
	 * @return unknown_type
	 */
	function login($username,$password,$auto,$save=false)
	{
		setcookie(self::$session_prefix.'username',$username,time() + self::$cookie_life,'/');
		$this->user = $this->db->query('select * from '.$this->table." where username='$username'")->fetch();
		if(empty($this->user)) return false;
		elseif($this->user['password']==$password)
		{
			$_SESSION[self::$session_prefix.'isLogin']=true;
			$_SESSION[self::$session_prefix.'user_id']=$this->user['id'];
			if($auto==1) $this->autoLogin();
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
		elseif(isset($_COOKIE[self::$session_prefix.'autologin']) and isset($_COOKIE[self::$session_prefix.'username']) and isset($_COOKIE[self::$session_prefix.'password']))
		{
			return $this->login($_COOKIE[self::$session_prefix.'username'],$_COOKIE[self::$session_prefix.'password'],$auto=1);
		}
		return false;
	}
	/**
	 * 自动登录，如果自动登录则在本地记住密码
	 * @param $user
	 * @return unknown_type
	 */
	function autoLogin()
	{
		$ip = Swoole_client::getIP();
		setcookie(self::$session_prefix.'autologin',1,time() + self::$cookie_life,'/');
		setcookie(self::$session_prefix.'username',$this->user['username'],time() + self::$cookie_life,'/');
		setcookie(self::$session_prefix.'password',$this->user['password'],time() + self::$cookie_life,'/');
	}
	/**
	 * 注销登录
	 * @return unknown_type
	 */
	static function logout()
	{
		if(isset($_SESSION[self::$session_prefix.'isLogin']))
		{
			session_destroy();
			if(isset($_COOKIE[self::$session_prefix.'password'])) setcookie(self::$session_prefix.'password','',0,'/');
		}
	}
	/**
	 * 产生一个密码串，连接用户名和密码，并使用sha1产生散列
	 * @param $username
	 * @param $password
	 * @return $password_string 密码的散列
	 */
	public static function mkpasswd($username,$password)
	{
		//sha1 用户名+密码
		if(self::$password_hash=='sha1') return sha1($username.$password);
		//md5 用户名+密码
		elseif(self::$password_hash=='md5') return md5($username.$password);
		elseif(self::$password_hash=='sha1_single') return sha1($password);
		elseif(self::$password_hash=='md5_single') return md5($password);
	}

	public static function login_require()
	{
		$check = false;
		if(!isset($_SESSION)) session();
		if(isset($_SESSION[self::$session_prefix.'isLogin']) and $_SESSION[self::$session_prefix.'isLogin']=='1') $check=true;
		if(!$check)
		{
			header('Location:'.self::$login_url.'refer='.urlencode($_SERVER["REQUEST_URI"]));
			exit;
		}
	}
}
?>