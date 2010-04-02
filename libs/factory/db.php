<?php
$db_config['host'] = DBHOST;
$db_config['engine'] = DBENGINE;
$db_config['dbms'] = DBMS;
$db_config['user'] = DBUSER;
$db_config['password'] = DBPASSWORD;
$db_config['dbname'] = DBNAME;
$db_config['charset'] = DBCHARSET;
if(defined('DBPERSISTENT')) $db_config['persistent'] = DBPERSISTENT;
if(defined('DBSETNAME')) $db_config['ifsetname'] = DBSETNAME;
else $db_config['ifsetname'] = false;
$db = new Database($db_config,DBTYPE);
$db->connect();
?>