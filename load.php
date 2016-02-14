<?php
header('Content-Type: application/json');
include_once("config.php");

$tables = $SQL->consulta("SELECT TABLE_NAME, ENGINE, TABLE_ROWS, TABLE_COLLATION, TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$_GET['database']}' AND TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA <> 'information_schema'");

echo json_encode($tables);

