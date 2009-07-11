<?php
class ModelAdmin extends AdminBase
{
	function __construct($swoole)
	{
		parent::$model_name = 'DModel';
		parent::__construct($swoole);
	}
}
?>