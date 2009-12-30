<?php
function file_upload($name,$up_dir=null,$access='',$filename=null)
{
	if(empty($up_dir)) $up_dir = UPLOAD_DIR."/".date('Y').date("m")."/".date("d");
	
	$path=WEBPATH.$up_dir;
	if(!file_exists($path))
	{
		mkdir($path,0777,true);
	}

	$mime=$_FILES[$name]['type'];
		
	$filetype= file_gettype($mime);
	if($filetype==false)
	{
		echo "File Type Error!";
		return false;
	}
	elseif(!empty($access))
	{
		$access_type = explode(',',$access);
		if($filetype=='bin') $filetype = $access_type[0];
		if(!in_array($filetype,$access_type))
		{	
			echo "File Type '$filetype' not allow upload!";
			return false;
		}
	}
	if($filename==null) $filename=substr(time(),6,-1).rand(100000,999999);
	$filename.=".".$filetype;
	if (move_uploaded_file($_FILES[$name]['tmp_name'],$path."/".$filename))
	{
		return "$up_dir/$filename";
	}
	else
	{
		echo "Error! debug:\n";
		print_r($_FILES[$name]);
		return false;
	}
}

function file_gettype($mime)
{
	$file_types  = array(
		'image/pjpeg' => 'jpg', 
		'image/jpeg'  => 'jpg',
		'image/jpeg'  => 'jpg',
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
		'application/octet-stream' => 'bin',//flash上传的格式，二进制
		'application/zip' => 'zip',
		'application/x-zip-compressed' => 'zip',
		'application/msword' => 'doc');
	if(!array_key_exists($mime,$file_types)) return false;
	else return $file_types[$mime];
}
function file_ext($file)
{ 
	return strtolower(trim(substr(strrchr($file, '.'), 1))); 
} 
?>