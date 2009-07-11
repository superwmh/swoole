<?php
class AutoForm
{
	var $fields;
	var $field_types;
	var $data;
	var $forms;
	const MAX_TEXT_SIZE = 50;
	const TEXT_FIELD_ROW = 20;
	const CHAR_FIELD_ROW = 3;
	
	function __construct($fields)
	{
		$this->fields = $fields;
	}
	
	function getForm($data='')
	{
		foreach($this->fields as $field)
		{
			$fieldname = $field['name'];
			$method = 'parse_'.$field['dtype'];
			if(empty($data))
				$this->forms[$fieldname]['value'] = $this->$method($field);
			else
				$this->forms[$fieldname]['value'] = $this->$method($field,$data[$fieldname]);
			$this->forms[$fieldname]['name'] = $field['cnname'];
		}
		return $this->forms;
	}
	
	function parse_upload($params,$value='')
	{
		$form = '';
		if(!empty($value))
			$form = "当前文件： <span title=\"header=[图片] body=[<img src='$value' class='div_image'>]\"><a href=#>$value</a></span><br />\n重新上传：";			
		return $form."<input type='file' class='input' name='{$params['name']}' id='{$params['name']}' size='50' />";
	}
	
	function parse_text($params,$value='')
	{
		return "<textarea rows=5 cols=80 name='{$params['name']}' id='{$params['name']}'>$value</textarea>";
	}
	
	function parse_varchar($params,$value='')
	{
		$form = "<input type='text' class='input' size='{$params['length']}' name='{$params['name']}' id='{$params['name']}' value='$value'>";
		return $form;
	}
	
	function parse_htmltext($params,$value='')
	{
		namespace('content');
		return editor($params['name'],$value,480);
	}
	
	function parse_radio($params,$value='')
	{
		$types = explode('(',$type,2);
		$fieldType['type'] = $types[0];
		if(count($types)>1)	$fieldType['length'] = substr($types[1],0,-1);
		else $fieldType['length'] = '';
		return $fieldType;
	}
	
	function parse_checkbox($params,$value='')
	{
		$types = explode('(',$type,2);
		$fieldType['type'] = $types[0];
		if(count($types)>1)	$fieldType['length'] = substr($types[1],0,-1);
		else $fieldType['length'] = '';
		return $fieldType;
	}
	
	function parse_int($params,$value='')
	{
		$form = "<input type='text' class='input' size='{$params['length']}' name='{$params['name']}' id='{$params['name']}' value='$value'>";
		return $form;
	}
}
?>