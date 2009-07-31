<?php
class SwooleLoader
{
	static $swoole;
	static $_objects;
	
	function __construct($swoole)
	{
		self::$swoole = $swoole;
		self::$_objects = array('model'=>new SwooleObject,'lib'=>new SwooleObject,'object'=>new SwooleObject);
	}
	
	/**
	 * 加载一个模型对象
	 * @param $model_name 模型名称
	 * @return $model_object 模型对象
	 */
	static function loadModel($model_name)
	{
		if(isset(self::$_objects['model'][$model_name]))
			return self::$_objects['model'][$model_name];
		else
		{
			$model_file = APPSPATH.'/models/'.$model_name.'.model.php';
			if(!file_exists($model_file)) Error::info('MVC错误',"不存在的模型, <b>$model_name</b>");
			require($model_file);
			self::$_objects['model'][$model_name] = new $model_name(self::$swoole);
			return self::$_objects['model'][$model_name];
		}
	}
	
	/**
	 * 加载一个控制器对象
	 * @param $controller_name 控制器名称
	 * @return $controller_object 控制器对象
	 */
	static function loadController($controller_name)
	{
		$controller_path = APPSPATH.'/controllers/'.$controller_name.'.php';
		if(!file_exists($controller_path)) Error::info('MVC Error',"Controller <b>$controller_name</b> not exist!");
		else require_once($controller_path);
		return new $controller_name(self::$swoole);
	}
	
	static function loadLib($lib_name)
	{
		if(isset(self::$_objects['lib'][$lib_name]))
			return self::$_objects['lib'][$lib_name];
		else
		{
			require(LIBPATH.'/factory/'.$lib_name.'.php');
			$lib_object = $$lib_name;
			self::$_objects['lib'][$lib_name] = $lib_object;
			return $lib_object;
		}
	}
}
?>