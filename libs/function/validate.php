<?php
function is_email($str)
{
	$check = preg_match("/^[\w-\.]+@[\w-]+(\.(\w)+)*(\.(\w){2,4})$/",$str);
	if($check) return true;
	else return false;
}

function is_mobile($str)
{
	$check = preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$str);
	if($check) return true;
	else return false;
}
?>