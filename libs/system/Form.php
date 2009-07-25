<?php
class Form
{
	/**
	 * 将一个关联数组解析为列表选择表单
	 * $name  此select 的 name 标签  
	 * $array 要制作select 的数组
	 * $default 如果要设定默认选择哪个数据 就在此填入默认的数据的值
	 *	$self 如果不想要数组中的哪个数据  就在此填入这个数组的值
	 *	$attrArray html标签的熟悉  就是这个select的属性标签 例如  class="x1" 
	 *	$add_help 增加一个值为空的 请选择 项
	 */
	static function select($name,$array,$default=null,$self = array(),$attrArray=null,$add_help=true) 
	{

		$option = $array;
		if (!is_array($option) || empty($option)) 
			return("下拉选择框制作失败:需要使用的元素不是数组或没有数值");
		
		$htmlStr = "<select name=\"$name\" id=\"$name\"";
		$attrStr = " ";
		if(!empty($attrArray) && is_array($attrArray))
		{
			foreach($attrArray as $key => $value)
			{
				$attrStr .= "$key=\"$value\" ";						 
			}
		}
		$htmlStr .= $attrStr . ">\n";

		if($add_help)
		{			
			if($add_help===true)
				$htmlStr .= "<option value=\"\">请选择</option>\n";
			else $htmlStr .= "<option value=\"\">$add_help</option>\n";
		}
		if(!$self)
			$self=array();
		foreach($option as $key => $value)
		{
			if(in_array($key,$self) && $self <> null){
				continue;
			}

			elseif ($key == $default) 
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
	 *	$array 要制作radio 的数组
	 *	$default 如果要设定默认选择哪个数据 就在此填入默认的数据的值
	 *	$self 如果不想要数组中的哪个数据  就在此填入这个数组的值
	 *	$attrArray html标签的熟悉  就是这个radio的属性标签 例如  class="x1" 
	 */
	static function radio($name,$array=null, $default=null,$self = array(),$attrArray=null) 
	{
		$option = $array;
		if (!is_array($option) || empty($option)) 
			return("单选框制作失败:需要使用的元素不是数组或没有数值");
		$htmlStr = "";
		$attrStr = " ";
		if(!empty($attrArray) && is_array($attrArray))
		{
			foreach($attrArray as $key => $value)
			{
				$attrStr .= " $key=\"$value\" ";						 
			}
		}
		if(!$self)
			$self=array();
		foreach($option as $key => $value)
		{
			if($key == $self && $self <> null){
				continue;
			}

			elseif ($key == $default) 
			{
				$htmlStr .= "<input type=\"radio\" name=\"$name\" id=\"$name\" value=\"$key\" checked=\"checked\" {$attrStr} />&nbsp;".$value."&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			else
			{
				$htmlStr .= "<input type=\"radio\" name=\"$name\" id=\"$name\" value=\"$key\"  {$attrStr} />&nbsp;".$value."&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}

		return $htmlStr;
	}
	/**
	 * 将一维数组做成选择框
	 * $name  此radio 的 name 标签  
	 * $array 要制作radio 的数组
	 * $default 如果要设定默认选择哪个数据 就在此填入默认的数据的值
	 * $self 如果不想要数组中的哪个数据  就在此填入这个数组的值
	 * $attrArray html标签的熟悉  就是这个radio的属性标签 例如  class="x1"  有问题
	*/
	static function checkbox($name,$array=null, $default=null,$self = array(),$attrArray=null) 
	{
		$option = $array;
		if (!is_array($option) || empty($option))
			return("多选框制作失败:需要使用的元素不是数组或没有数值");
		$htmlStr = "";
		$attrStr = " ";
		if(!empty($attrArray) && is_array($attrArray))
		{
			foreach($attrArray as $key => $value)
			{
				$attrStr .= " $key=\"$value\" ";						 
			}
		}
		if(!$self)
			$self=array();
		foreach($option as $key => $value)
		{
			if($key == $self && $self <> null){
				continue;
			}
			elseif(strpos($default,strval($key))!==false)
			{
				$htmlStr .= "<input type=\"checkbox\" name=\"{$name}[]\" id=\"$name\" value=\"$key\" checked=\"checked\" {$attrStr} />&nbsp;".$value."&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			else
			{
				$htmlStr .= "<input type=\"checkbox\" name=\"{$name}[]\" id=\"$name\" value=\"$key\"  {$attrStr} />&nbsp;".$value."&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}
		return $htmlStr;
	}
	
	static public function input($name,$value='')
	{
		return "<input type='text' name='{$name}' id='{$name}' value='{$value}'>";
	}
}
?>