<?php
include_once("dbconfig.php");
include_once("functions.php");
$db = new DBConnection();
$db->getConnection();

setClassAbs($_GET["date"], $_GET["texte"]);
	
?>