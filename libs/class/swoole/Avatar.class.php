<?php
class Avatar
{
	/**
	 * 裁切图片，制作头像
	 * @param $image  图片相对网站根目录的地址
	 * @param $params 参数，高度height=100，宽度width=116，精度qulitity=80，新图片的地址newfile，原图的真实宽度abs_width
	 * @param $original_size 原始的尺寸
	 * @param $crop_size 裁切的参数，高度,宽度,四点坐标
	 * @return true/false
	 */	
	static function cropImage($image,$params,$original_size,$crop_size)
	{
		$qulitity = isset($params['qulitity'])?$params['qulitity']:100;
		$dst_width = isset($params['width'])?$params['width']:90;
		$dst_height = isset($params['height'])?$params['height']:105;
		
		$image = WEBPATH.$image;
		if(!file_exists($image))
			return '错误，图片不存在！';

		$image_info = getimagesize($image);
		
		if($image_info["mime"]=="image/jpeg" || $image_info["mime"]=="image/gif" || $image_info["mime"]=="image/png")
		{
			/**
			 * 计算实际裁剪区域，图片是否被缩放，如果不是真实大小，需要计算
			 */
			if(isset($params['abs_width']))
			{
				$tmp_rate = $params['abs_width'] / $original['width'];
				$crop_size['left'] = $crop_size['left'] * $tmp_rate;
				$crop_size['top'] = $crop_size['top'] * $tmp_rate;
				$crop_size['width'] = $crop_size['width'] * $tmp_rate;
				$crop_size['height'] = $crop_size['height'] * $tmp_rate;
			}

			//裁剪
			$image_new = imagecreatetruecolor($dst_width, $dst_height);
			switch($image_info["mime"]){
				case "image/jpeg":
					$bin_ori = imagecreatefromjpeg($image);
					break;
				case "image/gif":
					$bin_ori = imagecreatefromgif($image);
					break;
				case "image/png":
					$bin_ori = imagecreatefrompng($image);
					break;
			}
			
			imagecopyresampled($image_new, $bin_ori, 0, 0, $crop_size['left'], $crop_size['top'], $dst_width, $dst_height, $crop_size['width'], $crop_size['height']);
			$file_new = WEBPATH.$params['newfile'];
			if(!file_exists(dirname($file_new))) mkdir(dirname($file_new),0777,true);
			return imagejpeg($image_new, $file_new , $qulitity);	 
		}
	}
}
?>