<?php
/*
 * 名称：CnkknD PHP Login Class   
 * 描述：PHP用于登录的类，基于MySQL   
 * 作者：Daniel King，cnkknd@msn.com   
 * 日期：Start@2003/8/25，Update@2004/4/16   
 */
class Login
{
	var $appname="Apolov"; //网站名称    
	var $username; //用户名   
	var $userpass; //密码
	 
	var $authtable="account"; //验证用数据表    
	var $col_username="username"; //用户名字段    
	var $col_password="password"; //用户密码字段    
	var $col_banned="banned"; //是否被禁止字段    
	 
	var $use_cookie=true; //使用cookie保存sessionid    
	var $cookiepath='/'; //cookie路径  
	var $cookietime=108000; //cookie有效时间    
	 
	var $err_mysql="<script>alert('数据库错误');</script>"; //mysql出错提示    
	var $err_auth="<script>alert('用户名或密码错误');history.back();</script>"; //用户名无效提示    
	var $err_user="user invalid"; //用户无效提示(被封禁)
	var $fields="*";
	 
	var $err; //出错提示    
	var $error_report=false; //显示错误    
	 
	function Login($appname="")
	{
		$this->appname=$appname; //初始化网站名称    
	}
	 
	function isLoggedin() //判断是否登录    
	{
		if(isset($_COOKIE['sid'])) //如果cookie中保存有sid    
		{
			session_id($_COOKIE['sid']);
			if(!isset($_SESSION)) session_start();
			if(!isset($_SESSION['appname']) or $_SESSION['appname']!=$this->appname) return false;
			return true;
		}
		else //如果cookie中未保存sid,则直接检查session    
		{
			if(!isset($_SESSION)) session_start();
			if(isset($_SESSION['appname']))
			return true;
		}
		return false;
	}
	 
	function userAuth($username,$userpass) //用户认证    
	{
		global $php;
		$this->username=$username;
		$this->userpass=$userpass;
		$query="select ".$this->fields." from `".$this->authtable."` where `".$this->col_username."`='$username';";
		$result=$php->db->query($query);
		if($result->rowCount()==1) //找到此用户    
		{
			$row=$result->fetch();
			/*
			 if($row['banned']==1) //此用户被封禁    
			 {
			 $this->errReport($this->err_user);
			 $this->err=$this->err_user;
			 return false;
			 }
			 */
			if(md5($userpass)==$row[$this->col_password]) //密码匹配 
			{
				$this->userinfo=$row;
				return true;
			}
			else //密码不匹配    
			{
				$this->errReport($this->err_auth);
				$this->err=$this->err_auth;
				return false;
			}
		}
		else //没有找到此用户    
		{
			$this->errReport($this->err_auth);
			$this->err=$this->err_auth;
			return false;
		}
	}
	 
	function setSession() //置session 
	 
	{
		$sid=uniqid('sid'); //生成sid  
		 
		session_id($sid);
		if(!isset($_SESSION)) session_start();
		$_SESSION['appname']=$this->appname; //保存程序名    
		 
		$_SESSION['userinfo']=$this->userinfo; //保存用户信息（表中所有字段）    

		if($this->use_cookie) //如果使用cookie保存sid    
		{
			if(!setcookie('sid',$sid,time()+$this->cookietime,$this->cookiepath))
			{
				$this->errReport("set cookie failed");
				$this->err="set cookie failed";
			}
		}
		else
		setcookie('sid','',time()-3600); //清除cookie中的sid    
		 
	}
	 
	function userLogout() //用户注销    
	{
		if(!isset($_SESSION)) session_start();
		unset($_SESSION['userinfo']); //清除session中用户信息    
		unset($_SESSION['appname']); //清除session中程序名    
		 
		if(setcookie('sid','',time()-3600)) //清除cookie中的sid    
		return true;
		else return false;
	}
	 
	function errReport($str) //报错  
	{
		if($this->error_report)
		echo "ERROR: $str";
	}
}
?>