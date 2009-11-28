<?php
/**
 * 内容处理工具类
 * @author Tianfeng.Han
 * @package SwooleSystem
 *
 */
class Swoole_content
{
	/**
	 * 自动产生分页代码
	 * 根据$size提供的长度
	 * 将$content分成$sptag分割开的一个文本，可以使用explode实现内容划分
	 *
	 * @param $content 内容
	 * @param $size   每页内容长度
	 * @param $sptag  分隔符
	 * @return bool   是否成功
	 */
	function paging(&$content,$size,$sptag)
	{
		if(strlen($content)<$spsize) return false;
		$bds = explode('<',$content);
		$npageBody = "";
		$istable = 0;
		$content = "";
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
				$content .= $npageBody.$sptag;
				$npageBody = "";
			}
		}
		if($npageBody!="") $content .= $npageBody;
		return true;
	}

	/**
	 * 自动将给定的内容$data中远程图片的url改为本地图片，并自动将远程图片保存到本地
	 * @param $data
	 * @return unknown_type
	 */
	function image_local($data)
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
			//echo "建立目录".$path;
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
					if($regs[2])
					{
						$i++;
						$source[$i] = $regs[2];
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
				$data=str_replace($uri,WEBROOT.$up_path."/".$filename,$data);
			}
		}
		return $data;
	}

	/**
	 * 汉字转为拼音
	 * @param $str 中文
	 * @return $res 拼音
	 */
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
			$ret.=self::_pinyin($p,$pinyin);
		}
		return $ret;
	}

	//主要是用于pinyin方法
	private function _pinyin($num,&$pinyin)
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