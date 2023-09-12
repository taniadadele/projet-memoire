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


if (isset($_GET['file']))
{
	if (file_exists("download/copies/output/".$_GET['file']))
	{
		$file = "download/copies/output/".$_GET['file']."";
	}
}
elseif (isset($_GET['user_file']))
{
	$query = "SELECT _ID FROM images WHERE _IDimage = '".addslashes($_GET['user_file'])."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		if ($_SESSION['CnxAdm'] == 255) $userID = $row[0];
		else $userID = $_SESSION['CnxID'];
	}
	if (file_exists("download/copies/eleves/".$userID."/".$_GET['user_file'].".pdf"))
	{
		$file = "download/copies/eleves/".$userID."/".$_GET['user_file'].".pdf";
	}

}

if (isset($_GET['file_name'])) $fileName = $_GET['file_name'].".pdf";
elseif (isset($_GET['user_file'])) $fileName = $_GET['user_file'].".pdf";
else $fileName = $_GET['file'];


header('Content-Type: application/force-download');
header("Content-Transfer-Encoding: binary");
header('Content-Disposition: attachment; filename='.basename(str_replace(' ', '_', $fileName)));
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
readfile("$file");



echo $file." - ".$fileName;

?>
