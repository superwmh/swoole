<?php
require('../../config.php');
$fields = load_data('field_model');
$db = create('pdo');
$db->debug = true;

if(isset($_POST['tablename']) and $_POST['tablename']!='')
{
	$sql = 'CREATE TABLE `'.$_POST['tablename'].'` (';
	$sql .= implode(",\n",$_POST['fields']);
	$sql .= ",\nPRIMARY KEY  (`id`)\n) ENGINE=".$_POST['engine'].'  DEFAULT CHARSET='.DBCHARSET;
	$db->query($sql);
	echo 'create table success';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
<!--
body,td,th {
	font-size: 14px;
}
-->
</style></head>

<body>
<p>建表向导</p>
<hr />
<p>1. 字段建表</p>
<form id="form1" name="form1" method="post" action="">
  <table width="600" border="1">
    <tr>
      <td>表名</td>
      <td><input type="text" name="tablename" id="tablename" /> <label>
        <select name="engine" id="engine">
          <option value="MyISAM">MyISAM</option>
          <option value="InnoDB">InnoDB</option>
        </select>
      </label></td>
    </tr>
    <tr>
      <td>模型</td>
      <td>
      <table><tr>
      <?php
      foreach($fields as $key=>$field)
      {
     	echo '<td><input type="checkbox" name="fields[]" value="',$field['value'],'" />',$field['name'],'&nbsp;&nbsp;</td>';
		if($key%4==3) echo '</tr><tr>';
      }
      ?>
      </tr></table>      </td>
    </tr>
    <tr>
      <td colspan="2"> <input type="submit" value="提交" />      </td>
    </tr>
  </table>
</form>
</body>
</html>
