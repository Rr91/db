<?php

require_once "mysql.php";	// подключение файла для работы с бд

$db = new Database();
$db->select("table_name", "fields", "where");

 
?>