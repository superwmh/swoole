<?php
if(DBTYPE=='adodb') $db = new AdoDB;
elseif(DBTYPE=='pdo') $db = new Database;
?>