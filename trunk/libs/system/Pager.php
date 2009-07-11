<?php
/**
 * filename: ext_page.class.php
 * @package:phpbean
 * @author :feifengxlq<feifengxlq#gmail.com>
 * @copyright :Copyright 2006 feifengxlq
 * @license:version 2.0
 * @create:2006-5-31
 * @modify:2006-6-1
 * @modify:feifengxlq 2006-11-4
 * description:��ǿ��ҳ�࣬���ַ�ҳģʽ��Ĭ�ϲ�������baidu,google�ķ�ҳ���
 * 2.0���ӹ��ܣ�֧���Զ������Զ�����ʽ��ͬʱ֧��PHP4��PHP5,
 * to see detail,please visit http://www.phpobject.net/blog/read.php
 * example:
 * ģʽ���ַ�ҳģʽ��
   require_once('../libs/classes/page.class.php');
   $page=new page(array('total'=>1000,'perpage'=>20));
   echo 'mode:1<br>'.$page->show();
   echo '<hr>mode:2<br>'.$page->show(2);
   echo '<hr>mode:3<br>'.$page->show(3);
   echo '<hr>mode:4<br>'.$page->show(4);
   ����AJAX��
   $ajaxpage=new page(array('total'=>1000,'perpage'=>20,'ajax'=>'ajax_page','page_name'=>'test'));
   echo 'mode:1<br>'.$ajaxpage->show();
   ���ü̳��Զ����ҳ��ʾģʽ��
   demo:[url=http://www.phpobject.net/blog]http://www.phpobject.net/blog[/url]
 */
class Pager 
{
	/**
	* config ,public
	*/
	var $page_name="page";//page��ǩ����������urlҳ������˵xxx.php?PB_page=2�е�PB_page
	var $next_page='>';//��һҳ
	var $pre_page='<';//��һҳ
	var $first_page='First';//��ҳ
	var $last_page='Last';//βҳ
	var $pre_bar='<<';//��һ��ҳ��
	var $next_bar='>>';//��һ��ҳ��
	var $format_left='';
	var $format_right='';
	var $is_ajax=false;//�Ƿ�֧��AJAX��ҳģʽ 

	/**
	* private
	*
	*/ 
	var $pagebarnum=10;//���Ƽ�¼���ĸ�����
	var $totalpage=0;//��ҳ��
	var $ajax_action_name='';//AJAX������
	var $nowindex=1;//��ǰҳ
	var $url="";//url��ַͷ
	var $offset=0;
	var $style;

	/**
	* constructor���캯��
	*
	* @param array $array['total'],$array['perpage'],$array['nowindex'],$array['url'],$array['ajax']...
	*/
function __construct($array)
{
if(is_array($array)){
if(!array_key_exists('total',$array))$this->error(__FUNCTION__,'need a param of total');
$total=intval($array['total']);
$perpage=(array_key_exists('perpage',$array))?intval($array['perpage']):10;
$nowindex=(array_key_exists('nowindex',$array))?intval($array['nowindex']):'';
$url=(array_key_exists('url',$array))?$array['url']:'';
}else{
$total=$array;
$perpage=10;
$nowindex='';
$url='';
}
if((!is_int($total))||($total<0))$this->error(__FUNCTION__,$total.' is not a positive integer!');
if((!is_int($perpage))||($perpage<=0))$this->error(__FUNCTION__,$perpage.' is not a positive integer!');
if(!empty($array['page_name']))$this->set('page_name',$array['page_name']);//����pagename
$this->_set_nowindex($nowindex);//���õ�ǰҳ
$this->_set_url($url);//�������ӵ�ַ
$this->totalpage=ceil($total/$perpage);
$this->offset=($this->nowindex-1)*$perpage;
if(!empty($array['ajax']))$this->open_ajax($array['ajax']);//��AJAXģʽ
}
/**
* �趨����ָ����������ֵ������ı�������������࣬��throwһ��exception
*
* @param string $var
* @param string $value
*/
function set($var,$value)
{
if(in_array($var,get_object_vars($this)))
$this->$var=$value;
else {
$this->error(__FUNCTION__,$var." does not belong to PB_Page!");
}

}
/**
* �򿪵�AJAXģʽ
*
* @param string $action Ĭ��ajax�����Ķ�����
*/
function open_ajax($action)
{
$this->is_ajax=true;
$this->ajax_action_name=$action;
}
/**
* ��ȡ��ʾ"��һҳ"�Ĵ���
* 
* @param string $style
* @return string
*/
function next_page()
{
	$style = $this->style;
	if($this->nowindex<$this->totalpage)
	{
		return $this->_get_link($this->_get_url($this->nowindex+1),$this->next_page,$style);
	}
	return '<span class="'.$style.'">'.$this->next_page.'</span>';
}

	/**
	* ��ȡ��ʾ����һҳ���Ĵ���
	*
	* @param string $style
	* @return string
	*/
	function pre_page()
	{
		$style = $this->style;
	if($this->nowindex>1){
	return $this->_get_link($this->_get_url($this->nowindex-1),$this->pre_page,$style);
	}
	return '<span class="'.$style.'">'.$this->pre_page.'</span>';
	}

/**
* ��ȡ��ʾ����ҳ���Ĵ���
*
* @return string
*/
function first_page()
{
	$style = $this->style;
	if($this->nowindex==1){
	return '<span class="'.$style.'">'.$this->first_page.'</span>';
	}
	return $this->_get_link($this->_get_url(1),$this->first_page,$style);
}

	/**
	* ��ȡ��ʾ��βҳ���Ĵ���
	*
	* @return string
	*/
	function last_page()
	{
		$style = $this->style;
	if($this->nowindex==$this->totalpage){
	return '<span class="'.$style.'">'.$this->last_page.'</span>';
	}
		return $this->_get_link($this->_get_url($this->totalpage),$this->last_page,$style);
	}

	function nowbar()
	{
		$style = $this->style;
		
		$plus=ceil($this->pagebarnum/2);
		if($this->pagebarnum-$plus+$this->nowindex>$this->totalpage)$plus=($this->pagebarnum-$this->totalpage+$this->nowindex);
		$begin=$this->nowindex-$plus+1;
		$begin=($begin>=1)?$begin:1;
		$return='';
		for($i=$begin;$i<$begin+$this->pagebarnum;$i++)
		{
			if($i<=$this->totalpage){
			if($i!=$this->nowindex)
			$return.=$this->_get_text($this->_get_link($this->_get_url($i),$i,$style));
			else 
			$return.=$this->_get_text('<span id="pager_now">'.$i.'</span>');
			}else{
			break;
			}
			$return.="\n";
		}
		unset($begin);
		return $return;
	}
/**
* ��ȡ��ʾ��ת��ť�Ĵ���
*
* @return string
*/
function select()
{
$return='<select name="PB_Page_Select">';
for($i=1;$i<=$this->totalpage;$i++)
{
if($i==$this->nowindex){
$return.='<option value="'.$i.'" selected>'.$i.'</option>';
}else{
$return.='<option value="'.$i.'">'.$i.'</option>';
}
}
unset($i);
$return.='</select>';
return $return;
}

/**
* ��ȡmysql �����limit��Ҫ��ֵ
*
* @return string
*/
function offset()
{
return $this->offset;
}

/**
* ���Ʒ�ҳ��ʾ��������������Ӧ�ķ��
*
* @param int $mode
* @return string
*/
	function render($mode=2)
	{
		$pager_html = "<div id='pager'>";
		switch ($mode)
		{
			case '1':
				$this->next_page='��һҳ';
				$this->pre_page='��һҳ';
				$pager_html.=$this->pre_page().$this->nowbar().$this->next_page().'��'.$this->select().'ҳ';
				break;
			case '2':
				$this->next_page='��һҳ';
				$this->pre_page='��һҳ';
				$this->first_page='��ҳ';
				$this->last_page='βҳ';
				$pager_html.=$this->first_page().$this->pre_page().'[��'.$this->nowindex.'ҳ]'.$this->nowbar().$this->next_page().$this->last_page();
				break;
			case '3':
				$this->next_page='��һҳ';
				$this->pre_page='��һҳ';
				$this->first_page='��ҳ';
				$this->last_page='βҳ';
				$pager_html.=$this->first_page().$this->pre_page().$this->next_page().$this->last_page();
				break;
			case '4':
				$this->next_page='��һҳ';
				$this->pre_page='��һҳ';
				$pager_html.=$this->pre_page().$this->nowbar().$this->next_page();
				break;
			case '5':
				$pager_html.=$this->pre_bar().$this->pre_page().$this->nowbar().$this->next_page().$this->next_bar();
				break;
		}
		$pager_html.='</div>';
		return $pager_html;
	}
/*----------------private function (˽�з���)-----------------------------------------------------------*/
/**
* ����urlͷ��ַ
* @param: String $url
* @return boolean
*/
function _set_url($url="")
{
if(!empty($url)){
//�ֶ�����
$this->url=$url.((stristr($url,'?'))?'&':'?').$this->page_name."=";
}else{
//�Զ���ȡ
if(empty($_SERVER['QUERY_STRING'])){
//������QUERY_STRINGʱ
$this->url=$_SERVER['REQUEST_URI']."?".$this->page_name."=";
}else{
//
if(stristr($_SERVER['QUERY_STRING'],$this->page_name.'=')){
//��ַ����ҳ�����
$this->url=str_replace($this->page_name.'='.$this->nowindex,'',$_SERVER['REQUEST_URI']);
$last=$this->url[strlen($this->url)-1];
if($last=='?'||$last=='&'){
$this->url.=$this->page_name."=";
}else{
$this->url.='&'.$this->page_name."=";
}
}else{
//
$this->url=$_SERVER['REQUEST_URI'].'&'.$this->page_name.'=';
}//end if    
}//end if
}//end if
}

/**
* ���õ�ǰҳ��
*
*/
function _set_nowindex($nowindex)
{
if(empty($nowindex)){
//ϵͳ��ȡ

if(isset($_GET[$this->page_name])){
$this->nowindex=intval($_GET[$this->page_name]);
}
}else{
//�ֶ�����
$this->nowindex=intval($nowindex);
}
}

	/**
	* Ϊָ����ҳ�淵�ص�ֵַ
	*
	* @param int $pageno
	* @return string $url
	*/
	function _get_url($pageno=1)
	{
		return $this->url.$pageno;
	}

	/**
	* ��ȡ��ҳ��ʾ���֣�����˵Ĭ�������_get_text('<a href="">1</a>')������[<a href="">1</a>]
	*
	* @param String $str
	* @return string $url
	*/ 
	function _get_text($str)
	{
		return $this->format_left.$str.$this->format_right;
	}

/**
* ��ȡ���ӵ�ַ
*/
function _get_link($url,$text,$style=''){
$style=(empty($style))?'':'class="'.$style.'"';
if($this->is_ajax){
//�����ʹ��AJAXģʽ
return '<a '.$style.'href="javascript:'.$this->ajax_action_name.'(\''.$url.'\')">'.$text.'</a>';
}else{
return '<a '.$style.'href="'.$url.'">'.$text.'</a>';
}
}
/**
* ������ʽ
*/
function error($function,$errormsg)
{
die('Error in file <b>'.__FILE__.'</b> ,Function <b>'.$function.'()</b> :'.$errormsg);
}
}
?>