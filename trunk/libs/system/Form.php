<?php
/**
 * 表单处理器
 * 用于生成HTML表单项
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage HTML
 *
 */
class Form
{
	static function autoform($params,$option = null,$data = null ,$selfs = null)
	{
		$forms = array();
		foreach($params as $name=>$p)
		{
			$func = $p['type'];
			$value = '';
			if(!empty($data[$name])) $value = $data[$name];
			$attr = array();
			if(!empty($p['attr']) and is_array($p['attr'])) $attr = $p['attr'];
			$self = array();
			if(!empty($p['self']) and is_array($p['self'])) $attr = $p['self'];

			if($func=='select' or $func=='checkbox' or $func=='radio') $forms[$name] = self::$func($name,$option[$name],$value,$self,$attr);
			else $forms[$name] = self::$func($name,$value,$attr);
		}
		return $forms;
	}
	static function input_attr($attr)
	{
	    $str = " ";
        if(!empty($attr) && is_array($attr))
        {
            foreach($attr as $key=>$value)
            {
                $str .= "$key=\"$value\" ";
            }
        }
        return $str;
	}
	/**
     * 将一个关联数组解析为列表选择表单
     * $name  此select 的 name 标签
     * $array 要制作select 的数
     * $default 如果要设定默认选择哪个数据 就在此填入默认的数据的值
     * $self 设置为ture，option的值等于$value
     * $attrArray html标签的熟悉  就是这个select的属性标签 例如  class="x1"
     * $add_help 增加一个值为空的 请选择 项
     */
	static function select($name,&$option,$default=null,$self=null,$attrArray=null,$add_help=true)
	{
		if (!is_array($option) || empty($option)) return("下拉选择框制作失败:需要使用的元素不是数组或没有数值");

		$htmlStr = "<select name=\"$name\" id=\"$name\"";
		$htmlStr .= self::input_attr($attrArray) . ">\n";

		if($add_help)
		{
			if($add_help===true)
			$htmlStr .= "<option value=\"\">请选择</option>\n";
			else $htmlStr .= "<option value=\"\">$add_help</option>\n";
		}
		foreach($option as $key => $value)
		{
			if($self) $key=$value;
			if ($key == $default)
			{
				$htmlStr .= "<option value=\"{$key}\" selected=\"selected\">{$value}</option>\n";
			}
			else
			{
				$htmlStr .= "<option value=\"{$key}\">{$value}</option>\n";
			}
		}
		$htmlStr .= "</select>\n";

		return $htmlStr;
	}
	/**
	 * 将一维数组做成选择框
	 *	$name  此radio 的 name 标签
	 *	$array 要制作radio 的数
	 *	$default 如果要设定默认选择哪个数据 就在此填入默认的数据的值
	 *	$self 设置为ture，option的值等于$value
	 *	$attrArray html标签的熟悉  就是这个radio的属性标签 例如  class="x1"
	 */
	static function radio($name,&$array, $default=null,$self = false,$attrArray=null)
	{
		if (!is_array($array) || empty($array)) return("单选框制作失败:需要使用的元素不是数组或没有数值");
		$htmlStr = "";
		$attrStr = self::input_attr($attrArray);
		foreach($array as $key => $value)
		{
			if($self) $key=$value;
			if ($key == $default)
			{
				$htmlStr .= "<input type=\"radio\" name=\"$name\" id=\"{$name}_{$key}\" value=\"$key\" checked=\"checked\" {$attrStr} />&nbsp;".$value."&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			else
			{
				$htmlStr .= "<input type=\"radio\" name=\"$name\" id=\"{$name}_{$key}\" value=\"$key\"  {$attrStr} />&nbsp;".$value."&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}
		return $htmlStr;
	}
	/**
	 * 将一维数组做成选择框
	 * $name  此radio 的 name 标签
	 * $array 要制作radio 的数
	 * $default 如果要设定默认选择哪个数据 就在此填入默认的数据的值
	 * $self 设置为ture，option的值等于$value
	 * $attrArray html标签的熟悉  就是这个radio的属性标签 例如  class="x1"  有问
	 */
	static function checkbox($name,&$option, $default=null,$self = false,$attrArray=null)
	{
		if (!is_array($option) || empty($option)) return("多选框制作失败:需要使用的元素不是数组或没有数值");
		$htmlStr = "";
		$attrStr = self::input_attr($attrArray);

		if(!$self)
		$self=array();
		foreach($option as $key => $value)
		{
			if($self) $key=$value;
			if(strpos($default,strval($key))!==false)
			{
				$htmlStr .= "<li><input type=\"checkbox\" name=\"{$name}[]\" id=\"$name\" value=\"$key\" checked=\"checked\" {$attrStr} />&nbsp;".$value.'</li>';
			}
			else
			{
				$htmlStr .= "<li><input type=\"checkbox\" name=\"{$name}[]\" id=\"$name\" value=\"$key\"  {$attrStr} />&nbsp;".$value.'</li>';
			}
		}
		return $htmlStr;
	}

    static function upload($name,$value='',$attrArray=null,$size=50)
    {
    	$attrStr = self::input_attr($attrArray);
    	$form = '';
        if(!empty($value))
            $form = " <a href='$value' target='_blank'>查看文件</a><br />\n重新上传";
        return $form."<input type='file' name='$name' id='{$name}' size='{$size}' {$attrStr} />";
    }

	static public function input($name,$value='',$attrArray=null)
	{
		$attrStr = self::input_attr($attrArray);
		return "<input type='text' name='{$name}' id='{$name}' value='{$value}' {$attrStr} />";
	}

	static public function string($name,$value='',$attrArray=null)
	{
		return self::input($name,$value,$attrArray);
	}

	static function int($name,$value='',$attrArray=null)
	{
		return self::input($name,$value,$attrArray);
	}

	static public function htmltext($name,$value='',$attrArray=null)
	{
		if(!isset($attrArray['height'])) $attrArray['height'] = 480;
		global $php;
		$php->plugin->load('fckeditor');
		return editor($name,$value,$attrArray['height']);
	}

	static public function text($name,$value='',$attrArray=null)
	{
		if(!isset($attrArray['cols'])) $attrArray['cols'] = 60;
		if(!isset($attrArray['rows'])) $attrArray['rows'] = 3;
		$attrStr = self::input_attr($attrArray);
		$forms = "<textarea name='{$name}' id='{$name}' $attrStr>$value</textarea>";
		return $forms;
	}

	static public function areaProvince($nameprovince,$namecity,$value='')
	{
		if(intval($value)<=-1 or empty($value)) $value = 10100000;
		$forms="<script>getProvinceSelect43rds('','{$nameprovince}','{$nameprovince}','{$namecity}','','{$value}');</script>";
		return $forms;
	}

	static public function areaCity($name,$value='')
	{
		if($value==='0') $value='';
		$forms= "<script>getCitySelect43rds('','$name','$name','','{$value}');</script>";
		return $forms;
	}
}
?>