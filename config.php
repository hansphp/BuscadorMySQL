<?php
define('__HVE', "<tt>Error crítico de seguridad</tt>");
include_once('class/MySQL/mysqli.class.php');

define('H_MYSQL_HOST', 'localhost');
define('H_MYSQL_USUARIO', 'root');
define('H_MYSQL_CLAVE', '');
define('H_MYSQL_BD', '');

function clear($s){
	return trim(addslashes($s));
}

if(!empty($_POST)){
	foreach($_POST as &$r){
		$r = clear($r);
	}
}

$SQL = new H_MYSQLi;