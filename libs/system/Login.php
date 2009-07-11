<?php
/*
 * ���ƣ�CnkknD PHP Login Class   
 * ������PHP���ڵ�¼���࣬����MySQL   
 * ���ߣ�Daniel King��cnkknd@msn.com   
 * ���ڣ�Start@2003/8/25��Update@2004/4/16   
 */
class Login
{
	var $appname="Apolov"; //��վ����    
	var $username; //�û���   
	var $userpass; //����
	 
	var $authtable="account"; //��֤�����ݱ�    
	var $col_username="username"; //�û����ֶ�    
	var $col_password="password"; //�û������ֶ�    
	var $col_banned="banned"; //�Ƿ񱻽�ֹ�ֶ�    
	 
	var $use_cookie=true; //ʹ��cookie����sessionid    
	var $cookiepath='/'; //cookie·��  
	var $cookietime=108000; //cookie��Чʱ��    
	 
	var $err_mysql="<script>alert('���ݿ����');</script>"; //mysql������ʾ    
	var $err_auth="<script>alert('�û������������');history.back();</script>"; //�û�����Ч��ʾ    
	var $err_user="user invalid"; //�û���Ч��ʾ(�����)
	var $fields="*";
	 
	var $err; //������ʾ    
	var $error_report=false; //��ʾ����    
	 
	function Login($appname="")
	{
		$this->appname=$appname; //��ʼ����վ����    
	}
	 
	function isLoggedin() //�ж��Ƿ��¼    
	{
		if(isset($_COOKIE['sid'])) //���cookie�б�����sid    
		{
			session_id($_COOKIE['sid']);
			if(!isset($_SESSION)) session_start();
			if(!isset($_SESSION['appname']) or $_SESSION['appname']!=$this->appname) return false;
			return true;
		}
		else //���cookie��δ����sid,��ֱ�Ӽ��session    
		{
			if(!isset($_SESSION)) session_start();
			if(isset($_SESSION['appname']))
			return true;
		}
		return false;
	}
	 
	function userAuth($username,$userpass) //�û���֤    
	{
		global $php;
		$this->username=$username;
		$this->userpass=$userpass;
		$query="select ".$this->fields." from `".$this->authtable."` where `".$this->col_username."`='$username';";
		$result=$php->db->query($query);
		if($result->rowCount()==1) //�ҵ����û�    
		{
			$row=$result->fetch();
			/*
			 if($row['banned']==1) //���û������    
			 {
			 $this->errReport($this->err_user);
			 $this->err=$this->err_user;
			 return false;
			 }
			 */
			if(md5($userpass)==$row[$this->col_password]) //����ƥ�� 
			{
				$this->userinfo=$row;
				return true;
			}
			else //���벻ƥ��    
			{
				$this->errReport($this->err_auth);
				$this->err=$this->err_auth;
				return false;
			}
		}
		else //û���ҵ����û�    
		{
			$this->errReport($this->err_auth);
			$this->err=$this->err_auth;
			return false;
		}
	}
	 
	function setSession() //��session 
	 
	{
		$sid=uniqid('sid'); //����sid  
		 
		session_id($sid);
		if(!isset($_SESSION)) session_start();
		$_SESSION['appname']=$this->appname; //���������    
		 
		$_SESSION['userinfo']=$this->userinfo; //�����û���Ϣ�����������ֶΣ�    

		if($this->use_cookie) //���ʹ��cookie����sid    
		{
			if(!setcookie('sid',$sid,time()+$this->cookietime,$this->cookiepath))
			{
				$this->errReport("set cookie failed");
				$this->err="set cookie failed";
			}
		}
		else
		setcookie('sid','',time()-3600); //���cookie�е�sid    
		 
	}
	 
	function userLogout() //�û�ע��    
	{
		if(!isset($_SESSION)) session_start();
		unset($_SESSION['userinfo']); //���session���û���Ϣ    
		unset($_SESSION['appname']); //���session�г�����    
		 
		if(setcookie('sid','',time()-3600)) //���cookie�е�sid    
		return true;
		else return false;
	}
	 
	function errReport($str) //����  
	{
		if($this->error_report)
		echo "ERROR: $str";
	}
}
?>