<?php
/**
 *  ����ͼƬ�����������ͼƬ���룬ˮӡ���
 *  ��ˮӡͼ����Ŀ��ͼƬ�ߴ�ʱ��ˮӡͼ���Զ���ӦĿ��ͼƬ����С
 *  ˮӡͼ�������ø������ĺϲ���
 *
 *  Copyright(c) 2005 by ustb99. All rights reserved
 *
 *  To contact the author write to {@link mailto:ustb99@hotmail.com}
 *
 * @author żȻ
 * @version $Id: thumb.class.php,v 1.0 2005/6/21 19:00:04 $
 * @package system
 *
 * last modified by linln 2006-07 
 */

/**
 * ThumbHandler
 * @access public
 */
class ThumbHandler
{
    public $src_img;// Դ�ļ�
    public $dst_img;// Ŀ���ļ�
    public $h_src; // ͼƬ��Դ���
    public $h_dst;// ��ͼ���
    public $h_mask;// ˮӡ���
    public $img_create_quality = 100;// ͼƬ��������
    public $img_display_quality = 100;// ͼƬ��ʾ����,Ĭ��Ϊ75
    public $img_scale = 0;// ͼƬ���ű���
    public $src_w = 0;// ԭͼ���
    public $src_h = 0;// ԭͼ�߶�
    public $dst_w = 0;// ��ͼ�ܿ��
    public $dst_h = 0;// ��ͼ�ܸ߶�
    public $fill_w;// ���ͼ�ο�
    public $fill_h;// ���ͼ�θ�
    public $start_x;// ��ͼ������ʼ������
    public $start_y;// ��ͼ������ʼ������
    public $end_x;// ��ͼ���ƽ���������
    public $end_y;// ��ͼ���ƽ���������
    public $mask_word;// ˮӡ����
    public $mask_img;// ˮӡͼƬ
    public $mask_pos_x = 0;// ˮӡ������
    public $mask_pos_y = 0;// ˮӡ������
    public $mask_offset_x = 5;// ˮӡ����ƫ��
    public $mask_offset_y = 5;// ˮӡ����ƫ��
    public $font_w;// ˮӡ�����
    public $font_h;// ˮӡ�����
    public $mask_w;// ˮӡ��
    public $mask_h;// ˮӡ��
    public $mask_font_color = "#ffffff";// ˮӡ������ɫ
    public $mask_font = 2;// ˮӡ����
    public $font_size;// �ߴ�
    public $mask_position = 0;// ˮӡλ��
    public $mask_img_pct = 50;// ͼƬ�ϲ��̶�,ֵԽ�󣬺ϲ�����Խ��
    public $mask_txt_pct = 50;// ���ֺϲ��̶�,ֵԽС���ϲ�����Խ��
    public $img_border_size = 0;// ͼƬ�߿�ߴ�
    public $img_border_color;// ͼƬ�߿���ɫ
	public $_flip_x=0;// ˮƽ��ת����
	public $_flip_y=0;// ��ֱ��ת����
	
    public $img_type;// �ļ�����
	
	public $error = '';

    // �ļ����Ͷ���,��ָ�����������ɺ����ͼƬ�ĺ���
    public $all_type = array(
        "jpg"  => array("create"=>"ImageCreateFromjpeg","output"=>"imagejpeg"),
        "gif"  => array("create"=>"ImageCreateFromGIF" ,"output"=>"imagegif"),
        "png"  => array("create"=>"imagecreatefrompng" ,"output"=>"imagepng"),
        "wbmp" => array("create"=>"imagecreatefromwbmp","output"=>"image2wbmp"),
        "jpeg" => array("create"=>"ImageCreateFromjpeg","output"=>"imagejpeg"));

    /**
     * ���캯��
     */
    function ThumbHandler()
    {
        $this->mask_font_color = "#ffffff";
        $this->font = 2;
        $this->font_size = 12;
    }

    /**
     * ȡ��ͼƬ�Ŀ�
     */
    function getImgWidth($src)
    {
        return imagesx($src);
    }

    /**
     * ȡ��ͼƬ�ĸ�
     */
    function getImgHeight($src)
    {
        return imagesy($src);
    }

    /**
     * ����ͼƬ����·��
     *
     * @param    string    $src_img   ͼƬ����·��
     */
    function setSrcImg($src_img)
    {
        file_exists($src_img) ? ($this->src_img = $src_img) : $this->error = "ͼƬ������";
		$this->img_type = $this->_getPostfix($this->src_img);
        $this->_checkValid($this->img_type);

        $img_type  = $this->img_type;
        $func_name = $this->all_type[$img_type]['create'];
        if(function_exists($func_name))
        {
            $this->h_src = $func_name($this->src_img);
            $this->src_w = $this->getImgWidth($this->h_src);
            $this->src_h = $this->getImgHeight($this->h_src);
        }
        else
        {
            $this->error = $func_name."��������֧��";
			return false;
        }
    }

    /**
     * ����ͼƬ����·��
     *
     * @param    string    $dst_img   ͼƬ����·��
     */
    function setDstImg($dst_img)
    {
        $arr  = explode('/',$dst_img);
        $last = array_pop($arr);
        $path = implode('/',$arr);
        $this->_mkdirs($path);
        $this->dst_img = $dst_img;
    }

    /**
     * ����ͼƬ����ʾ����
     *
     * @param    string      $n    ����
     */
    function setImgDisplayQuality($n)
    {
        $this->img_display_quality = (int)$n;
    }

    /**
     * ����ͼƬ����������
     *
     * @param    string      $n    ����
     */
    function setImgCreateQuality($n)
    {
        $this->img_create_quality = (int)$n;
    }

    /**
     * ��������ˮӡ
     *
     * @param    string     $word    ˮӡ����
     * @param    integer    $font    ˮӡ����
     * @param    string     $color   ˮӡ������ɫ
     */
    function setMaskWord($word)
    {
        $this->mask_word .= $word;
    }

    /**
     * ����������ɫ
     *
     * @param    string     $color    ������ɫ
     */
    function setMaskFontColor($color="#ffffff")
    {
        $this->mask_font_color = $color;
    }

    /**
     * ����ˮӡ����
     *
     * @param    string|integer    $font    ����
     */
    function setMaskFont($font=2)
    {
        if(!is_numeric($font) && !file_exists($font))
        {
            die("�����ļ�������");
        }
        $this->font = $font;
    }

    /**
     * �������������С,����truetype������Ч
     */
    function setMaskFontSize($size = "12")
    {
        $this->font_size = $size;
    }

    /**
     * ����ͼƬˮӡ
     *
     * @param    string    $img     ˮӡͼƬԴ
     */
    function setMaskImg($img)
    {
        $this->mask_img = $img;
    }

    /**
     * ����ˮӡ����ƫ��
     *
     * @param    integer     $x    ����ƫ����
     */
    function setMaskOffsetX($x)
    {
        $this->mask_offset_x = (int)$x;
    }

    /**
     * ����ˮӡ����ƫ��
     *
     * @param    integer     $y    ����ƫ����
     */
    function setMaskOffsetY($y)
    {
        $this->mask_offset_y = (int)$y;
    }

    /**
     * ָ��ˮӡλ��
     *
     * @param    integer     $position    λ��,1:����,2:����,3:����,0/4:����
     */
    function setMaskPosition($position=0)
    {
        $this->mask_position = (int)$position;
    }

    /**
     * ����ͼƬ�ϲ��̶�
     *
     * @param    integer     $n    �ϲ��̶�
     */
    function setMaskImgPct($n)
    {
        $this->mask_img_pct = (int)$n;
    }

    /**
     * �������ֺϲ��̶�
     *
     * @param    integer     $n    �ϲ��̶�
     */
    function setMaskTxtPct($n)
    {
        $this->mask_txt_pct = (int)$n;
    }

    /**
     * ��������ͼ�߿�
     *
     * @param    (����)     (������)    (����)
     */
    function setDstImgBorder($size=1, $color="#000000")
    {
        $this->img_border_size  = (int)$size;
        $this->img_border_color = $color;
    }

	/**
	 * ˮƽ��ת
	 */
	function flipH()
	{
		$this->_flip_x++;
	}

	/**
	 * ��ֱ��ת
	 */
	function flipV()
	{
		$this->_flip_y++;
	}

    /**
     * ����ͼƬ,������
     * @param    integer    $a     ��ȱ�ٵڶ�������ʱ���˲����������ٷֱȣ�
     *                             ������Ϊ���ֵ
     * @param    integer    $b     ͼƬ���ź�ĸ߶�
     */
    function createImg($a, $b=null)
    {
        $num = func_num_args();
        if(1 == $num)
        {
            $r = (int)$a;
            if($r < 1)
            {
                die("ͼƬ���ű�������С��1");
            }
            $this->img_scale = $r;
			$this->_setNewImgSize($r);
        }

        if(2 == $num)
        {
            $w = (int)$a;
            $h = (int)$b;
            if(0 == $w)
            {
                die("Ŀ���Ȳ���Ϊ0");
            }
            if(0 == $h)
            {
                die("Ŀ��߶Ȳ���Ϊ0");
            }
            $this->_setNewImgSize($w, $h);
        }

		if($this->_flip_x%2!=0)
		{
			$this->_flipH($this->h_src);
		}

		if($this->_flip_y%2!=0)
		{
			$this->_flipV($this->h_src);
		}
        $this->_createMask();
        $this->_output();

        // �ͷ�
        imagedestroy($this->h_src);
        imagedestroy($this->h_dst);
    }

    /**
     * ����ˮӡ,����������ˮӡ���ֺ�ˮӡͼƬ��������
     */
    function _createMask()
    {
        if($this->mask_word)
        {
            // ��ȡ������Ϣ
            $this->_setFontInfo();

            if($this->_isFull())
            {
                die("ˮӡ���ֹ���");
            }
            else
            {
                $this->h_dst = imagecreatetruecolor($this->dst_w, $this->dst_h);
                $this->_drawBorder();
                imagecopyresampled( $this->h_dst, $this->h_src,
                                    $this->start_x, $this->start_y,
                                    0, 0,
                                    $this->end_x, $this->end_y,
                                    $this->src_w, $this->src_h);
                $this->_createMaskWord($this->h_dst);
            }
        }

        if($this->mask_img)
        {
            $this->_loadMaskImg();//����ʱ��ȡ�ÿ��

            if($this->_isFull())
            {
                // ��ˮӡ������ԭͼ���ٿ�
                $this->_createMaskImg($this->h_src);
                $this->h_dst = imagecreatetruecolor($this->dst_w, $this->dst_h);
                $this->_drawBorder();
                imagecopyresampled( $this->h_dst, $this->h_src,
                                    $this->start_x, $this->start_y,
                                    0, 0,
                                    $this->end_x, $this->end_y,
                                    $this->src_w, $this->src_h);
            }
            else
            {
                // ������ͼ������
                $this->h_dst = imagecreatetruecolor($this->dst_w, $this->dst_h);
                $this->_drawBorder();
                imagecopyresampled( $this->h_dst, $this->h_src,
                                    $this->start_x, $this->start_y,
                                    0, 0,
                                    $this->end_x, $this->end_y,
                                    $this->src_w, $this->src_h);
                $this->_createMaskImg($this->h_dst);
            }
        }

        if(empty($this->mask_word) && empty($this->mask_img))
        {
            $this->h_dst = imagecreatetruecolor($this->dst_w, $this->dst_h);
            $this->_drawBorder();
            imagecopyresampled( $this->h_dst, $this->h_src,
                                $this->start_x, $this->start_y,
                                0, 0,
                                $this->end_x, $this->end_y,
                                $this->src_w, $this->src_h);
        }
    }

    /**
     * ���߿�
     */
    function _drawBorder()
    {
        if(!empty($this->img_border_size))
        {
            $c = $this->_parseColor($this->img_border_color);
            $color = ImageColorAllocate($this->h_src,$c[0], $c[1], $c[2]);
            imagefilledrectangle($this->h_dst,0,0,$this->dst_w,$this->dst_h,$color);// ��䱳��ɫ
        }
    }

    /**
     * ����ˮӡ����
     */
    function _createMaskWord($src)
    {
        $this->_countMaskPos();
        $this->_checkMaskValid();

        $c = $this->_parseColor($this->mask_font_color);
        $color = imagecolorallocatealpha($src, $c[0], $c[1], $c[2], $this->mask_txt_pct);

        if(is_numeric($this->font))
        {
            imagestring($src,
                        $this->font,
                        $this->mask_pos_x, $this->mask_pos_y,
                        $this->mask_word,
                        $color);
        }
        else
        {
            imagettftext($src,
                        $this->font_size, 0,
                        $this->mask_pos_x, $this->mask_pos_y,
                        $color,
                        $this->font,
                        $this->mask_word);
        }
    }

    /**
     * ����ˮӡͼ
     */
    function _createMaskImg($src)
    {
        $this->_countMaskPos();
        $this->_checkMaskValid();
        imagecopymerge($src,
                        $this->h_mask,
                        $this->mask_pos_x ,$this->mask_pos_y,
                        0, 0,
                        $this->mask_w, $this->mask_h,
                        $this->mask_img_pct);

        imagedestroy($this->h_mask);
    }

    /**
     * ����ˮӡͼ
     */
    function _loadMaskImg()
    {
        $mask_type = $this->_getPostfix($this->mask_img);
        $this->_checkValid($mask_type);

        $func_name = $this->all_type[$mask_type]['create'];
        if(function_exists($func_name))
        {
            $this->h_mask = $func_name($this->mask_img);
            $this->mask_w = $this->getImgWidth($this->h_mask);
            $this->mask_h = $this->getImgHeight($this->h_mask);
        }
        else
        {
            $this->error = $func_name."��������֧��";
			return false;
        }
    }

    /**
     * ͼƬ���
     */
    function _output()
    {
        $img_type  = $this->img_type;
        $func_name = $this->all_type[$img_type]['output'];
        if(function_exists($func_name))
        {
			// �ж������,����IE�Ͳ�����ͷ
			if(isset($_SERVER['HTTP_USER_AGENT']))
			{
				$ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
				if(!preg_match('/^.*MSIE.*\)$/i',$ua))
				{
					header("HTTP/1.1 202 Accepted");
					header("Content-type:$img_type");
				}
			}
            $func_name($this->h_dst, $this->dst_img, $this->img_display_quality);
        }
        else
        {
            return false;
        }
    }

    /**
     * ������ɫ
     *
     * @param    string     $color    ʮ��������ɫ
     */
    function _parseColor($color)
    {
        $arr = array();
        for($ii=1; $ii<strlen($color); $ii++)
        {
            $arr[] = hexdec(substr($color,$ii,2));
            $ii++;
        }

        return $arr;
    }

    /**
     * �����λ������
     */
    function _countMaskPos()
    {
		if($this->_isFull())
		{
			switch($this->mask_position)
			{
				case 1:
					// ����
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;

				case 2:
					// ����
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
					break;

				case 3:
					// ����
					$this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;

				case 4:
					// ����
					$this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
					$this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
					break;

				default:
					// Ĭ�Ͻ�ˮӡ�ŵ�����,ƫ��ָ������
					$this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
					$this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
					break;
			}
		}
		else
		{
				switch($this->mask_position)
			{
				case 1:
					// ����
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;

				case 2:
					// ����
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
					break;

				case 3:
					// ����
					$this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;

				case 4:
					// ����
					$this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
					$this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
					break;

				default:
					// Ĭ�Ͻ�ˮӡ�ŵ�����,ƫ��ָ������
					$this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
					$this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
					break;
			}	    
		}
    }

    /**
     * ����������Ϣ
     */
    function _setFontInfo()
    {
        if(is_numeric($this->font))
        {
            $this->font_w  = imagefontwidth($this->font);
            $this->font_h  = imagefontheight($this->font);

            // ����ˮӡ������ռ���
            $word_length   = strlen($this->mask_word);
            $this->mask_w  = $this->font_w*$word_length;
            $this->mask_h  = $this->font_h;
        }
        else
        {
            $arr = imagettfbbox ($this->font_size,0, $this->font,$this->mask_word);
            $this->mask_w  = abs($arr[0] - $arr[2]);
            $this->mask_h  = abs($arr[7] - $arr[1]);
        }
    }

    /**
     * ������ͼ�ߴ�
     *
     * @param    integer     $img_w   Ŀ����
     * @param    integer     $img_h   Ŀ��߶�
     */
    function _setNewImgSize($img_w, $img_h=null)
    {
		$num = func_num_args();
		if(1 == $num)
		{
			$this->img_scale = $img_w;// �����Ϊ����
			$this->fill_w = round($this->src_w * $this->img_scale / 100) - $this->img_border_size*2;
		    $this->fill_h = round($this->src_h * $this->img_scale / 100) - $this->img_border_size*2;
			$this->dst_w   = $this->fill_w + $this->img_border_size*2;
			$this->dst_h   = $this->fill_h + $this->img_border_size*2;
		}

		if(2 == $num)
		{
			$fill_w   = (int)$img_w - $this->img_border_size*2;
			$fill_h   = (int)$img_h - $this->img_border_size*2;
			if($fill_w < 0 || $fill_h < 0)
			{
				die("ͼƬ�߿�����ѳ�����ͼƬ�Ŀ��");
			}
			$rate_w = $this->src_w/$fill_w;
			$rate_h = $this->src_h/$fill_h;
			if($rate_w > $rate_h)
			{
				$this->fill_w = (int)$fill_w;
				$this->fill_h = round($this->src_h/$rate_w);
			}
			else
			{
				$this->fill_w = round($this->src_w/$rate_h);
				$this->fill_h = (int)$fill_h;
			}
			$this->dst_w   = $this->fill_w + $this->img_border_size*2;
			$this->dst_h   = $this->fill_h + $this->img_border_size*2;
		}
		
		$this->start_x = $this->img_border_size;
		$this->start_y = $this->img_border_size;
		$this->end_x   = $this->fill_w;
		$this->end_y   = $this->fill_h;
    }

    /**
     * ���ˮӡͼ�Ƿ�������ɺ��ͼƬ���
     */
    function _isFull()
    {
        Return (   $this->mask_w + $this->mask_offset_x > $this->fill_w
                || $this->mask_h + $this->mask_offset_y > $this->fill_h)
                   ?true:false;
    }

    /**
     * ���ˮӡͼ�Ƿ񳬹�ԭͼ
     */
    function _checkMaskValid()
    {
        if(    $this->mask_w + $this->mask_offset_x > $this->src_w
            || $this->mask_h + $this->mask_offset_y > $this->src_h)
        {
            die("ˮӡͼƬ�ߴ����ԭͼ������Сˮӡͼ");
        }
    }

    /**
     * ȡ���ļ���׺����Ϊ���Ա
     */
    function _getPostfix($filename)
    {
        return substr(strrchr(trim(strtolower($filename)),"."),1);
    }

    /**
     * ���ͼƬ�����Ƿ�Ϸ�,������array_key_exists�������˺���Ҫ��
     * php�汾����4.1.0
     *
     * @param    string     $img_type    �ļ�����
     */
    function _checkValid($img_type)
    {
        if(!array_key_exists($img_type, $this->all_type))
        {
            Return false;
        }
    }

    /**
     * ��ָ��·������Ŀ¼
     *
     * @param    string     $path    ·��
     */
    function _mkdirs($path)
    {
        $adir = explode('/',$path);
        $dirlist = '';
        $rootdir = array_shift($adir);
        if(($rootdir!='.'||$rootdir!='..')&&!file_exists($rootdir))
        {
            mkdir($rootdir);
        }
        foreach($adir as $key=>$val)
        {
            if($val!='.'&&$val!='..')
            {
                $dirlist .= "/".$val;
                $dirpath = $rootdir.$dirlist;
                if(!file_exists($dirpath))
                {
                    mkdir($dirpath);
                    chmod($dirpath,0777);
                }
            }
        }
    }
	
	/**
     * ˮƽ��ת
     *
     * @param    string     $path    ·��
     */
	function _flipV($src)
	{
		$src_x = $this->getImgWidth($src);
		$src_y = $this->getImgHeight($src);

		$new_im = imagecreatetruecolor($src_x, $src_y);
		for ($x = 0; $x < $src_x; $x++)
		{
			for ($y = 0; $y < $src_y; $y++)
			{
				imagecopy($new_im, $src, $x, $src_y - $y - 1, $x, $y, 1, 1);
			}
		}
		$this->h_src = $new_im;
	}

	function _flipH($src)
	{
		$src_x = $this->getImgWidth($src);
		$src_y = $this->getImgHeight($src);

		$new_im = imagecreatetruecolor($src_x, $src_y);
		for ($x = 0; $x < $src_x; $x++)
		{
			for ($y = 0; $y < $src_y; $y++)
			{
				imagecopy($new_im, $src, $src_x - $x - 1, $y, $x, $y, 1, 1);
			}
		}
		$this->h_src = $new_im;
	}

	/**
	 * ȡ��ĳһ�����ɫֵ
	 *
	 * @param    (����)     (������)    (����)
	 */
	function _getPixColor($src, $x, $y)
	{
		$rgb   = @ImageColorAt($src, $x, $y);
		$color = imagecolorsforindex($src, $rgb);
		
		Return $color;
	}
}
?>