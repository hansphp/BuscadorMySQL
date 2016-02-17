<?php
set_time_limit(1200);
header('Content-Type: application/json');
include_once("config.php");
$result = array('error' => 1);

if(isset($_GET['type'])){
	if($_GET['type'] == 'table'){
		$result = $SQL->consulta("SELECT TABLE_NAME, ENGINE, TABLE_ROWS, TABLE_COLLATION, TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$_GET['database']}' AND TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA <> 'information_schema'");
	}elseif($_GET['type'] == 'column'){
		$result = $SQL->consulta("SELECT TABLE_NAME, COLUMN_NAME, ORDINAL_POSITION, DATA_TYPE, IFNULL(CHARACTER_MAXIMUM_LENGTH, IFNULL(NUMERIC_PRECISION, 0)) MAX_LEN, COLLATION_NAME, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '{$_GET['database']}' AND TABLE_SCHEMA <> 'information_schema' AND TABLE_NAME = '{$_GET['table']}' ORDER BY ORDINAL_POSITION");
	}elseif($_GET['type'] == 'row'){
		$result = $SQL->fila("SELECT COUNT(*) coincidencias FROM `{$_GET['database']}`.`{$_GET['table']}` WHERE `{$_GET['row']}` LIKE '%{$_GET['val']}%'");
	}
}

echo json_encode($result);

