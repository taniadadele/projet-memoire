<?php
session_start();
require_once "config.php";
require_once "include/sqltools.php";
include_once("php/functions.php");
include 'include/fonction.php';
// connexion à la base de données
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

//---------------------------------------------------------------------------
function remove_magic_quotes($array)
{
	/*
	 * fonction :	nettoyage des \ dans une chaîne
	 * in :		$array : tableau de valeurs
	 */

	// On n'exécute la boucle que si nécessaire
	if ( $array AND get_magic_quotes_gpc() == 1 )
		foreach($array as $key => $val) {
			// Si c'est un array, recursion de la fonction, sinon suppression des slashes
			if ( is_array($val) )
				remove_magic_quotes($array[$key]);
			else
				if ( is_string($val) )
					$array[$key] = stripslashes($val);
			}

	return $array;
}
//---------------------------------------------------------------------------

$image = @base64_decode($_GET["image"]);
$image = substr($image, strpos($image, "_")+1);


if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 OR $_SESSION['CnxID'] != "")
{
	// Récupère le fichier
	$new_image = substr($image, 0, strrpos($image, '.'));

	$query  = "SELECT postit_pj._IDpj, postit_pj._IDpost, postit_pj._title, postit_items._IDdst, postit_items._IDexp ";
	$query .= "FROM postit_pj INNER JOIN postit_items ON postit_pj._IDpost = postit_items._IDpost ";
	$query .= "WHERE (postit_items._IDdst = '".$_SESSION['CnxID']."' OR postit_items._IDexp = '".$_SESSION['CnxID']."') ";
	$query .= "AND postit_pj._IDpj = '".addslashes($_GET['IDpj'])."' ";
	$query .= "LIMIT 1";

	$result = mysqli_query($mysql_link, $query);
	$row    = ($result) ? mysqli_fetch_row($result) : 0 ;

	$file = "download/post-it/".$row[0]."_".$row[2];

	header('Content-Type: application/force-download');
	header('Content-Transfer-Encoding: binary');
	header('Content-Disposition: attachment; filename='.basename(str_replace(' ', '_', $row[2])));
	header('Pragma: no-cache');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');
	readfile($file);
}
?>
