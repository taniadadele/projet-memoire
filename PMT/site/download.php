<?php
session_start();
require_once "config.php";
require_once "include/sqltools.php";
// connexion à la base de données
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

$image = @base64_decode($_GET["image"]);
$image = substr($image, strpos($image, "_")+1);

if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
{
	// Récupère le fichier
	$query  = "SELECT _IDimage, _type, _title, _ID ";
	$query .= "FROM images ";
	$query .= "WHERE _IDimage = ".$image." ";
	$query .= "LIMIT 1 ";

	$result = mysqli_query($mysql_link, $query);
	$row    = ($result) ? mysqli_fetch_row($result) : 0 ;

	$file = "download/".$row[1]."/files/".$row[3]."/".$row[2];

	header('Content-Type: application/force-download');
	header("Content-Transfer-Encoding: binary");
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Pragma: no-cache');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');
	readfile("$file");
}
?>
