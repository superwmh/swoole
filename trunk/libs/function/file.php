<?php
function file_upload($name,$option="/upfiles",$access='')
{
	$up_path = "$option/".date('Y').date("m")."/".date("d");
	$path=WEBPATH."/".$up_path;
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
		if(!in_array($filetype,$access_type))
		{	
			echo "File Type '$filetype' not allow upload!";
			return false;
		}
	}
	
	$filename=substr(time(),6,-1).rand(100000,999999).".".$filetype;
	if (move_uploaded_file($_FILES[$name]['tmp_name'],$path."/".$filename))
	{
		return "$up_path/$filename";
	}
	else
	{
		print "Error! debug:\n";
		print_r($_FILES[$name]);
		return false;
	}
}

function file_gettype($mime)
{
	$file_types  = array(
		'image/pjpeg' => 'jpg', 
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
		'application/octet-stream' => 'rar',
		'application/zip' => 'zip',
		'application/x-zip-compressed' => 'zip',
		'application/msword' => 'doc');
	if(!array_key_exists($mime,$file_types)) return false;
	else return $file_types[$mime];
}
?>