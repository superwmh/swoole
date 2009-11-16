<?php
global $php;
SiteForm::$swoole = $php;

class SiteForm
{
	static $swoole;
	const RADIO = 5;
	
	function __construct($swoole)
	{
		self::$swoole = $swoole;
	}
	
	static function userForm(&$user=null)
	{
		$userinfo_dict = SiteDict::get('user_info');
		if(!empty($user))
		{
			$userinfo_dict['shape'] = $userinfo_dict['shape'][$user['sex']];
			$userinfo_dict['selfdom'] = $userinfo_dict['selfdom'][$user['sex']];
		}
		$forms = array();
		require(WEBPATH.'/dict/userforms.php');
		//单选
		$fields = explode(',',$userforms_type['radio']);
		foreach($fields as $f)
		{
			$options = $userinfo_dict[$f];
			$forms[$f] = Form::radio($f,$options,$user[$f]);
		}
		
		//复选框
		$fields = explode(',',$userforms_type['checkbox']);
		foreach($fields as $f)
		{
			$options = $userinfo_dict[$f];
			$forms[$f] = Form::checkbox($f,$options,$user[$f]);
		}
		
		//下拉菜单		
		$fields = explode(',',$userforms_type['select']);
		foreach($fields as $f)
		{
			$options = $userinfo_dict[$f];
			$forms[$f] = Form::select($f,$options,$user[$f]);
		}
		
		//输入框
		$fields = explode(',',$userforms_type['input']);
		foreach($fields as $f)
		{
			$forms[$f] = Form::input($f,$user[$f]);
		}
		$forms['work_province'] = Form::areaProvince('work_province','work_city',$user['work_province']);
		$forms['work_city'] = Form::areaCity('work_city',$user['work_city']);		
		$forms['birth_province'] = Form::areaProvince('birth_province','birth_city',$user['birth_province']);
		$forms['birth_city'] = Form::areaCity('birth_city',$user['birth_city']);
		$forms['birthday'] = "<script>getYearsSelect('','birthday_year','birthday_year','','{$user['birthday_year']}');</script>\n
						<script>getMonthsSelect('','birthday_month','birthday_month','','{$user['birthday_month']}');</script>\n
                        <script>getDaysSelect('','birthday_day','birthday_day','','{$user['birthday_day']}');</script>";
		return $forms;
	}
	
	static function searchForm($data)
	{
		$condition_dict = SiteDict::get('user_condition');
		$userinfo_dict = SiteDict::get('user_info');
		unset($userinfo_dict['shape'][0]);
		unset($userinfo_dict['car'][0]);
		unset($userinfo_dict['house'][0]);
		
		$forms['work_province'] = Form::areaProvince('work_province','work_city',$data['work_province']);
		$forms['work_city'] = Form::areaCity('work_city',$data['work_city']);		
		$forms['birth_province'] = Form::areaProvince('birth_province','birth_city',$data['birth_province']);
		$forms['birth_city'] = Form::areaCity('birth_city',$data['birth_city']);
		
		$forms['sex'] = Form::radio('sex',$userinfo_dict['sex'],$data['sex']);
		$forms['min_age'] = Form::select('min_age',$condition_dict['age'],$data['min_age'],null,null,'不限');
		$forms['max_age'] = Form::select('max_age',$condition_dict['age'],$data['max_age'],null,null,'不限');		
		$forms['min_height'] = Form::select('min_height',$userinfo_dict['height'],$data['min_height'],null,null,'不限');
		$forms['max_height'] = Form::select('max_height',$userinfo_dict['height'],$data['max_height'],null,null,'不限');
		$forms['shape'] = Form::select('shape',$userinfo_dict['shape'],$data['shape'],null,null,'不限');
		$forms['salary'] = Form::select('salary',$userinfo_dict['salary'],$data['salary'],null,null,'不限');
		
		$forms['children'] = Form::select('children',$userinfo_dict['children'],$data['children'],null,null,'不限');
		$forms['marriage'] = Form::select('marriage',$userinfo_dict['marriage'],$data['marriage'],null,null,'不限');
		
		$forms['degree'] = Form::select('degree',$userinfo_dict['degree'],$data['degree'],null,null,'不限');
		$forms['car'] = Form::select('car',$userinfo_dict['car'],$data['car'],null,null,'不限');
		$forms['house'] = Form::select('house',$userinfo_dict['house'],$data['house'],null,null,'不限');
		$forms['job'] = Form::select('job',$userinfo_dict['job'],$data['job'],null,null,'不限');
		unset($userinfo_dict['credit'][0]);
		$forms['credit'] = Form::select('credit',$userinfo_dict['credit'],$data['credit'],null,null,'不限');	
		$forms['belief'] = Form::select('belief',$userinfo_dict['belief'],$data['belief'],null,null,'不限');	
		$forms['min_jon'] = Form::select('min_jon',$userinfo_dict['min_jon'],$data['min_jon'],null,null,'不限');			
		$forms['constellation'] = Form::select('constellation',$userinfo_dict['constellation'],$data['constellation'],null,null,'不限');	
		$forms['companytype'] = Form::select('companytype',$userinfo_dict['companytype'],$data['companytype'],null,null,'不限');	
		$forms['job'] = Form::select('job',$userinfo_dict['job'],$data['job'],null,null,'不限');	
		$forms['bloodtype'] = Form::select('bloodtype',$userinfo_dict['bloodtype'],$data['bloodtype'],null,null,'不限');	
		$forms['animal'] = Form::select('animal',$userinfo_dict['animal'],$data['animal'],null,null,'不限');	
		$forms['smoking'] = Form::select('smoking',$userinfo_dict['smoking'],$data['smoking'],null,null,'不限');	
		$forms['drinking'] = Form::select('drinking',$userinfo_dict['drinking'],$data['drinking'],null,null,'不限');	
		
		return $forms;
	}
	
	static function conditionForm($data=null)
	{
		$condition_dict = SiteDict::get('user_condition');
		$userinfo_dict = SiteDict::get('user_info');
		unset($userinfo_dict['shape'][0]);
		unset($userinfo_dict['car'][0]);
		unset($userinfo_dict['house'][0]);
		
		$forms['min_age'] = Form::select('min_age',$condition_dict['age'],$data['min_age'],null,null,'不限');
		$forms['max_age'] = Form::select('max_age',$condition_dict['age'],$data['max_age'],null,null,'不限');		
		$forms['min_height'] = Form::select('min_height',$userinfo_dict['height'],$data['min_height'],null,null,'不限');
		$forms['max_height'] = Form::select('max_height',$userinfo_dict['height'],$data['max_height'],null,null,'不限');
		$forms['shape'] = Form::select('shape',$userinfo_dict['shape'],$data['shape'],null,null,'不限');
		$forms['salary'] = Form::select('salary',$userinfo_dict['salary'],$data['salary'],null,null,'不限');
		$forms['marriage'] = Form::select('marriage',$userinfo_dict['marriage'],$data['marriage'],null,null,'不限');
		$forms['children'] = Form::select('children',$userinfo_dict['children'],$data['children'],null,null,'不限');
		$forms['degree'] = Form::select('degree',$userinfo_dict['degree'],$data['degree'],null,null,'不限');
		$forms['car'] = Form::select('car',$userinfo_dict['car'],$data['car'],null,null,'不限');
		$forms['house'] = Form::select('house',$userinfo_dict['house'],$data['house'],null,null,'不限');
		$forms['credit'] = Form::select('credit',$userinfo_dict['credit'],$data['credit'],null,null,'不限');	
		//$forms['has_avatar'] = Form::radio('has_avatar',$condition_dict['has_avatar'],$data['hasimage'],null,null,'不限');
		$forms['work_province'] = Form::areaProvince('work_province','work_city',$data['work_province']);
		$forms['work_city'] = Form::areaCity('work_city',$data['work_city']);		
		return $forms;
	}
}
?>