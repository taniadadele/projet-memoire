<?php
// Téléchargement d'un fichier d'absence
session_start();
include_once '../../php/dbconfig.php';

$IDitem = $_GET['item'];

if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
{
	// Récupère le nom du fichier
  $filename = $db->getRow("SELECT _file as file FROM absent_items WHERE _IDitem = ?i LIMIT 1 ", $IDitem)->file;

	header('Content-Type: application/force-download');
	header("Content-Transfer-Encoding: binary");
	header('Content-Disposition: attachment; filename='.basename($filename));
	header('Pragma: no-cache');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');
	readfile('files/'.$IDitem);
}
?>
