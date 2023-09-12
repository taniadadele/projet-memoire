<?php
session_start();

$_SESSION["CFGDIR"] = ( @$CFGDIR != "" ) ? $CFGDIR : "." ;
$_SESSION["ROOTDIR"] = ( @$ROOTDIR != "" ) ? $ROOTDIR : "." ;
$_SESSION["CfgPuce"]    = $_SESSION["ROOTDIR"]."/css/themes/puce/dash.gif";

//---------------------------------------------------------------------------
require_once $_SESSION["CFGDIR"]."/config.php";
// require_once "config.php";

//---------------------------------------------------------------------------
require_once "include/TMessage.php";
require_once "include/sqltools.php";
require_once "include/session.php";
require_once "include/config.php";
require_once "include/fonction.php";

//---------------------------------------------------------------------------
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

//---- choix de la langue ----
if ( !empty($_GET["lang"]) )
	$_SESSION["lang"] = $_GET["lang"];
else
	if ( empty($_SESSION["lang"]) )
		if ( isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ) {
			$list = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			$lang = strtolower(substr(chop($list[0]), 0, 2));

			// pays de langue française
			$fr   = Array('fr', 'be', 'bf', 'bj', 'cd', 'cf', 'cg', 'ch', 'ci', 'cm', 'dz', 'ga', 'gf', 'gq', 'lc', 'lu', 'ma', 'mg', 'mq', 'mu', 'ne', 'pf', 'pm', 're', 'sc', 'sn', 'td', 'tn', 'wf', 'yt');

			// pays de langue espagnole
			$es   = Array('es', 'ar', 'bo', 'cl', 'co', 'cr', 'cu', 'do', 'ec', 'mx', 'pr', 'gt', 'gw', 'hn', 'ni', 'pa', 'pe', 'py', 'sv', 'uy', 've');

			if ( in_array($lang, $fr) )
				$_SESSION["lang"] = ( is_dir("msg/fr") ) ? "fr" : $LANG ;
			else
				if ( in_array($lang, $es) )
					$_SESSION["lang"] = ( is_dir("msg/es") ) ? "es" : $LANG ;
				else
					$_SESSION["lang"] = ( is_dir("msg/en") ) ? "en" : $LANG ;
			}
		else
			$_SESSION["lang"] = $LANG;


			// Définit le décalage horaire par défaut de toutes les fonctions date/heure (PHP 5 >= 5.1.0)
			if ( function_exists('date_default_timezone_set') )
				date_default_timezone_set($TIMEZONE);

			// initialisation du générateur
			srand(time());


$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
header( 'Content-Type: text/csv; charset=utf-8' );
header( 'Content-Disposition: attachment;filename=export_examens_'.date("d-m-Y").'-'.date("H:i").".csv");
// ---------------------------------------------------------------------------







if ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 2) {
	$query = "SELECT * FROM `campus_examens` WHERE 1 ";
	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
	if($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4)
	{
		echo "ID;Nom;Type;Coef;Note Max\n";
	}
	else
	{
		echo "ID;Nom;Type;Coef;Note Max\n";
	}

	// données CSV
	while ( $row ) {
		$query2 = "SELECT `_text`, `_ident` FROM `notes_type` WHERE `_lang` = 'fr' AND `_IDtype` = '".$row[1]."' ";
		$result2 = mysqli_query($mysql_link, $query2);
		while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
			$type =  "(".$row2[1].") - ".$row2[0];
		}






		if($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4)
		{
			echo $row[0].";".$row[2].";".$type.";".$row[3].";".$row[4]."\n";
		}
		else
		{
			echo $row[0].";".$row[2].";".$type.";".$row[3].";".$row[4]."\n";
		}

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
	}





?>
