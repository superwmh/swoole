<?php
//Swooleϵͳ����
class Swoole_content
{
	public function editor($input_name, $input_value,$height="480")
	{
		 $editor = Factory::create("fckeditor",$input_name);
		 $editor->BasePath   = WEBROOT."/swoole/module/fckeditor/";
		 $editor->ToolbarSet = "Default";
		 $editor->Width      = "100%";
		 $editor->Height     = $height;
		 $editor->Value      = $input_value;
		 $editor->Config['AutoDetectLanguage'] = true ;
		 $editor->Config['DefaultLanguage']  = 'en' ;//����
		 return $editor->CreateHtml();
	}
	
	//�Զ�������ҳ����
	//����$spsize�ṩ�ĳ��ȣ���$mybody�ֳ�$sptag�ָ��һ���ı�������ʹ��explodeʵ�����ݻ���
	function SpLongBody(&$mybody,$spsize,$sptag)
	{
		if(strlen($mybody)<$spsize) return $mybody;
		$bds = explode('<',$mybody);
		$npageBody = "";
		$istable = 0;
		$mybody = "";
		foreach($bds as $i=>$k)
		{
			if($i==0){$npageBody .= $bds[$i]; continue;}
			$bds[$i] = "<".$bds[$i];
			if(strlen($bds[$i])>6)
			{
				$tname = substr($bds[$i],1,5);
				if(strtolower($tname)=='table') $istable++;
				else if(strtolower($tname)=='/tabl') $istable--;
				if($istable>0){$npageBody .= $bds[$i]; continue;}
				else $npageBody .= $bds[$i];
			}
			else
			{
				$npageBody .= $bds[$i];
			}
			if(strlen($npageBody)>$spsize)
			{
				$mybody .= $npageBody.$sptag;
				$npageBody = "";
			}
		}
		if($npageBody!="") $mybody .= $npageBody;
		return $mybody;
	}
	
	//�Զ�������������$data��Զ��ͼƬ��url��Ϊ����ͼƬ�����Զ���Զ��ͼƬ���浽����
	function imageToLacal($data)
	{
		$option="upfiles/images";
		$up_path="/$option/".date('Y')."/".date("m")."/".date("d");
		$path=WEBPATH."/".$up_path;
		
		if(!file_exists(WEBPATH."/$option/"))
		{
			mkdir(WEBPATH."/$option/",0777);
		}
		if(!file_exists($path))
		{
			//echo "����Ŀ¼".$path;
			if(!file_exists(WEBPATH."/$option/".date('Y')))
			{
				mkdir(WEBPATH."/$option/".date('Y'),0777);
			}
			if(!file_exists(WEBPATH."/$option/".date('Y')."/".date('m')))
			{
				mkdir(WEBPATH."/$option/".date('Y').'/'.date('m'),0777);
			}
			mkdir($path,0777);
		}
		$chunklist = array ();
		$chunklist = explode("\n",$data);
		$links = array ();
		$regs = array ();
		$source = array();
		$i = 0;
		while(list ($id, $chunk) = each($chunklist))
		{
			if (strstr(strtolower($chunk),"img") && strstr(strtolower($chunk), "src"))
			{
				while (eregi("(img[^>]*src[[:blank:]]*)=[[:blank:]]*[\'\"]?(([[a-z]{3,5}://(([.a-zA-Z0-9-])+(:[0-9]+)*))*([+:%/?=&;\\\(\),._a-zA-Z0-9-]*))(#[.a-zA-Z0-9-]*)?[\'\" ]?", $chunk, $regs)) 
				{
					if($regs[2]){
						$i++;
						$source[$i] = $regs[2];
						//$imglinks[$i] = $this->realUrl($regs[2]);
					}
					$chunk = str_replace($regs[0], "", $chunk);
				}
			}
		}
		$newImg = array();
		foreach($source as $uri)
		{
			if(!strstr(WEBROOT,$uri) and $uri{0}!='/')
			{
				$filename=substr(time(),6,-1).rand(100000,999999).".jpg";
				copy($uri,$path.'/'.$filename);
				//echo $uri;
				//echo WEBROOT.$up_path."/".$filename."<br />";
				$data=str_replace($uri,WEBROOT.$up_path."/".$filename,$data);
			}
		}
		return $data;
	}
	
	//����תΪƴ��
	function pinyin($str)
	{
		require("swoole/contrib/data/pinyin.php");
		$ret=""; 
		for($i=0;$i<strlen($str);$i++)
		{ 
			$p=ord(substr($str,$i,1)); 
			if($p>160){
				$q=ord(substr($str,++$i,1)); 
				$p=$p*256+$q-65536;
			}
			$ret.=self::pinyin_z($p,$pinyin);
		}
		return $ret;
	}
	
	//��Ҫ������pinyin����
	function pinyin_z($num,&$pinyin)
	{
		if($num>0&&$num<160)
		{ 
			return chr($num); 
		} 
		elseif($num<-20319||$num>-10247)
		{ 
			return ""; 
		}
		else
		{ 
			for($i=count($pinyin)-1;$i>=0;$i--){if($pinyin[$i][1]<=$num)break;} 
			return $pinyin[$i][0]; 
		} 
	}
}
?>