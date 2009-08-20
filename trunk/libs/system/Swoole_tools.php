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
	public function getSegs($segs)
	{
		return explode(" ",trim(str_replace("/"," ",$segs)));
	}
	
	public function js_unescape($str)
	{
		$ret = '';
		$len = strlen($str);
		
		for ($i = 0; $i < $len; $i++)
		{
			if ($str[$i] == '%' && $str[$i+1] == 'u')
			{
			$val = hexdec(substr($str, $i+2, 4));
			
			if ($val < 0x7f) $ret .= chr($val);
			else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
			else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
			
			$i += 5;
			}
			else if ($str[$i] == '%')
			{
				$ret .= urldecode(substr($str, $i, 3));
				$i += 2;
			}
			else $ret .= $str[$i];
		}
		return $ret;
	}
	
	public function utf_substr($str,$len)
	{
		for($i=0;$i<$len;$i++)
		{
			$temp_str=substr($str,0,1);
			if(ord($temp_str) > 127)
			{
				$i++;
				if($i<$len)
				{
					$new_str[]=substr($str,0,3);
					$str=substr($str,3);
				}
			}
			else 
			{
				$new_str[]=substr($str,0,1);
				$str=substr($str,1);
			}
		}
		return join($new_str);
	}
	
	public function upload($name,$option="upfiles/images",$allow="jpg,gif,png")
	{
		if(empty($file_types)) global $file_types;
		$up_path="/$option/".date('Y')."/".date("m")."/".date("d");
		$path=WEBPATH."/".$up_path;
		$file_types  = array('image/pjpeg' => 'jpg', 
							'image/jpeg'  => 'jpg',
							'image/jpeg'  => 'jpeg',
							'image/gif'   => 'gif',
							'image/X-PNG' => 'png',
							'image/PNG'   => 'png', 
							'image/png'   => 'png', 
							'image/x-png' => 'png', 
							'image/JPG'   => 'jpg',
							'image/GIF'   => 'gif',
							'image/bmp'   => 'bmp',
							'image/bmp'   => 'BMP',
							'application/x-rar-compressed' => 'rar',
							'application/octet-stream' => 'rar',//
							'application/zip' => 'zip',
							'application/x-zip-compressed' => 'zip',//
							'application/msword' => 'doc' 
		);
		
		if(!file_exists(WEBPATH."/$option/"))
		{
			mkdir(WEBPATH."/$option/",0777);
		}
		if(!file_exists($path))
		{
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
			
		$mime=$_FILES[$name]['type'];
		if(!array_key_exists($mime,$file_types))
		{
			echo "Access deny,not found file type!";
			echo $mime;
			return false;
		}
				
		$filetype=$file_types[$mime];
		$filename=rand(1,999999).".".$filetype;
		if (move_uploaded_file($_FILES[$name]['tmp_name'],$path."/".$filename))
		{
				return "$up_path/$filename";
		}
		else
		{
			print "Fails! Debug:\n";
			print_r($_FILES[$name]);
			return false;
		}
	}
}
?>