<?php
require(LIBPATH."/module/fckeditor/fckeditor.php");
function editor($input_name, $input_value,$height="480")
{
	$editor = new FCKeditor($input_name) ;
	$editor->BasePath   = WEBROOT."/libs/module/fckeditor/"; //指定编辑器路径
	$editor->ToolbarSet = "Default"; //编辑器工具栏有Basic（基本工具）,Default（所有工具）选择
	$editor->Width      = "100%";
	$editor->Height     = $height;
	$editor->Value      = $input_value;
	$editor->Config['AutoDetectLanguage'] = true ;
	$editor->Config['DefaultLanguage']  = 'en';
	$FCKeditor = $editor->CreateHtml();
	$ext = <<<HTML
<script language="javascript">
function upfile_success(filepath)
{
	var fck = FCKeditorAPI.GetInstance("content");
	fck.InsertHtml("<img src='"+ filepath +"' />");
}
</script>
<iframe src="/admin/upload_image.php" height="40" width="100%" frameborder="0" scrolling="no"></iframe>
HTML;
	return $FCKeditor.$ext;
	
}
function resource($app,$aid='',$catid='')
{
	$resource = <<<HTML
	<script language="javascript">
	function fck_insert(html)
	{
		var fck = FCKeditorAPI.GetInstance("content");
		fck.InsertHtml(html); 
	}
	</script>
	<iframe src="/admin/upfile.php?app=$app&aid=$aid&catid=$catid" height="100" width="100%" frameborder="0" scrolling="no" id='upload_resource'></iframe>
HTML;
	return $resource;	
}

function imageToLacal($data)
{
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
			$filename=substr(time(),6,-1).rand(100000,999999).".jpg";
			copy($uri,$path.'/'.$filename);
			//echo $uri;
			//echo WEBROOT.$up_path."/".$filename."<br />";
			$data=str_replace($uri,WEBROOT.$up_path."/".$filename,$data);
		}
	}
	return $data;
}

?>