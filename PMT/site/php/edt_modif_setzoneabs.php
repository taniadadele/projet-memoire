<?php
include_once("dbconfig.php");
include_once("functions.php");
$db = new DBConnection();
$db->getConnection();

setZoneAbs($_GET["date"], $_GET["texte"], $_GET["IDclass"]);
	
?>