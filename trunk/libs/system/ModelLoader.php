<?php
class ModelLoader
{
	private $swoole = null;
	private $_models = array();
	
	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}
	
	function __get($model_name)
	{
		if(isset($this->_models[$model_name]))
			return $this->_models[$model_name];
		else
		{
			$model_file = APPSPATH.'/models/'.$model_name.'.model.php';
			if(!file_exists($model_file)) Error::info('MVC错误',"不存在的模型, <b>$model_name</b>");
			require($model_file);
			$this->_models[$model_name] = new $model_name($this->swoole);
			return $this->_models[$model_name];
		}
	}
}
?>