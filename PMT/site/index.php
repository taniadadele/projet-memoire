<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)
   Copyright (c) 2019 by Thomas Dazy (contact@thomasdazy.fr)

   This file is part of Prométhée.

   Prométhée is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Prométhée is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/

/*
 *		module   : index.php
 *		projet   : affichage des pages en fonction du menu
 *
 *		version  : 2.0
 *		auteur   : laporte
 *		creation : 4/10/02
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *		modif    : 18/10/2019 - Thomas Dazy (contact@thomasdazy.fr)
 * 	                 Refonte complête du système de connexion
 */

/*
  Lors de la connexion:
  - On récupère juste l'ID de l'utilisateur si les identifiants sont bon
  - On crée et enregistre le cookie
  - On récupère le reste des infos (fonction isUserConnected())

  Sur toutes les pages:
  - On récupère les infos de l'utilisateur (si connecté)
  - On redirige vers la page de connexion (si déconnecté)
*/


   session_start();
   // error_reporting(0);
   // ini_set("display_errors", 0);
   error_reporting(E_ALL);
   ini_set("display_errors", 1);


   ini_set('arg_separator.output', '&amp;');
  require("page_session.php");

  require("php/dbconfig.php");

  // On initialises les différentes classes
  $alert = new Alert;

  // On inclus les différentes fonctions de l'identification
  require('include/fonction/auth_tools.php');

  // On inclus les fichiers pour logger
  require_once $_SESSION['ROOTDIR'].'/php/log4php/main/php/Logger.php';
  Logger::configure($_SESSION['ROOTDIR'].'/php/log4php/config_daily_file.properties');

  $logger = Logger::getRootLogger();

  // On définis des éléments globaux:
  define('RESSOURCE_PATH', array(
    'NO_PROFILE_PICTURE' => $_SESSION["ROOTDIR"].'/download/photo/eleves/no_picture_user.png',
    'PAGES_FOLDER' => 'include/pages/'        // Chemin du dossier contenant les pages du site    RESSOURCE_PATH['PAGES_FOLDER']
  ));

  define('MLS_ROOT', __DIR__);

  $showCenter = getParam('showCenter');



  if (isset($_GET['idmenu'])) $idmenu_global = $_GET['idmenu'];
  elseif (isset($_POST['idmenu'])) $idmenu_global = $_POST['idmenu'];
  else {
    if ((isset($_GET['item']) && $_GET['item'] != '' && $_GET['item'] != 0) || isset($_POST['item']) && $_POST['item'] != '' && $_POST['item'] != 0) {
      if (isset($_GET['item'])) $item = $_GET['item'];
      else $item = $_POST['item'];
      $elem_to_search = 'item='.$item;
      if (isset($_GET['cmde']) && $_GET['cmde'] != '') $elem_to_search .= '&cmde='.$_GET['cmde'];

      $query = "SELECT _IDsubmenu FROM config_submenu WHERE _link = '".$elem_to_search."' LIMIT 1";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result)) $idmenu_global = $row[0];
      if (!isset($idmenu_global) || $idmenu_global == '') {
        $query = "SELECT _IDsubmenu FROM config_submenu WHERE _link = 'item=".$item."' LIMIT 1";
        $result = mysqli_query($mysql_link, $query);
        while ($row = mysqli_fetch_array($result)) $idmenu_global = $row[0];
      }
    }
  }
  if (!isset($idmenu_global) || $idmenu_global == '') $idmenu_global = '';


// ---------------------------------------------------------------------------
// Connexion
// ---------------------------------------------------------------------------
if (isset($_POST['id'])) $ident = addslashes(trim($_POST['id']));
if (isset($_POST['pwd'])) $passwd = addslashes(trim($_POST['pwd']));;

if ( @$_POST["submitAuth"] == "Valider" && strlen($ident) && strlen($passwd) && (!isset($_SESSION['CnxID']) || $_SESSION['CnxID'] == "") && (!isset($_GET['item']) || $_GET['item'] != -1) )
{
  // On récupère l'ID de l'utilisateur
	$query  = "SELECT _ID, _IDcentre, _lang FROM user_id ";
	$query .= "WHERE (_ident = '".$ident."' OR _email = '".$ident."') ";
	$query .= "AND _passwd = '".md5($passwd)."' AND _adm > 0 LIMIT 1 ";
	$result = mysqli_query($mysql_link, $query);
  if (mysqli_num_rows($result) == 0)
  {
    // Écriture dans les logs si identifiants mauvais
    mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '0', '".$ident."', '".@$_SERVER["REMOTE_ADDR"]."', 'X')");
    $loginMessage = "bad_ident";
  }
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

    // On stoque les différentes variable POST pour les écrire plus tard dans les logs
    foreach ($_POST as $key => $value) {
      if ($key != 'pwd') $_SESSION['POST_'.$key] = $value;
    }
    $_SESSION['LOGS_ID'] = $row[0];




		$_SESSION["CnxID"]     = $row[0];						// ID de l'utilisateur
    $_SESSION["CnxCentre"] = $row[1];						// ID du centre (pour les traductions)
    $_SESSION["lang"]      = $row[2];						// langue (pour les traductions)

    if ($_SESSION['lang'] == '') $_SESSION['lang'] = getCenterLanguageByIDCenter($_SESSION['CnxCentre']);
    $loginMessage = "";
	}

  if ($_SESSION["CnxID"] != "" && $_SESSION["CnxID"] != 0)
  {
    // Écriture dans les logs
    mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '".$_SESSION["CnxID"]."', '', '".@$_SERVER["REMOTE_ADDR"]."', 'C')");

    // Gestion des cookies
  	$currentDay = date('d');
  	$currentMonth = date('m');
  	$currentYear = date('Y');
  	$timeOutCookie = strtotime($currentYear."-".$currentMonth."-".$currentDay." 03:00:00 + 1 day");
  	// identifiant (réel, pas celui utilisé lors de la co) + année + mois + jour + H + I + s en crypt
  	$key = md5(rand().$ident.date('YmdHis'));
  	setcookie('token_key', $key, $timeOutCookie, '/');

  	$_COOKIE['token_key'] = $key;

  	// On stoque le cookie dans la BDD pour pouvoir faire la correspondance après
  	$query = "SELECT _userID FROM user_cookie WHERE _userID = '".$_SESSION['CnxID']."' ";
  	$result = mysqli_query($mysql_link, $query);
  	if (mysqli_num_rows($result) == 1)
  	{
  		// Requête d'UPDATE
  		$query = "UPDATE user_cookie SET _token = '".$key."', _timeOut = '".$timeOutCookie."' WHERE _userID = '".$_SESSION['CnxID']."' ";
  	}
  	else
  	{
  		// Requête d'INSERT
  		$query = "INSERT INTO user_cookie SET _token = '".$key."', _timeOut = '".$timeOutCookie."', _userID = '".$_SESSION['CnxID']."' ";
  	}
  	mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }
}
// ---------------------------------------------------------------------------


// ---------------------------------------------------------------------------
// DECONNEXION
// ---------------------------------------------------------------------------
if (isset($_GET['item']) && $_GET['item'] == -1)
{
	disconnectUser('D');
}
// ---------------------------------------------------------------------------


// ---------------------------------------------------------------------------
// Récupération des données utilisateur
// ---------------------------------------------------------------------------
getUserDataFromCookie();
// ---------------------------------------------------------------------------


// ---------------------------------------------------------------------------
// Si on est en maintenance et que l'on est pas admin alors on déonnecte
// ---------------------------------------------------------------------------
if ($MAINTENANCE && $_SESSION['CnxAdm'] != 255) {
  disconnectUser('D');
}
// ---------------------------------------------------------------------------


// ---------------------------------------------------------------------------
// Redirection vers la version HTTPS
// ---------------------------------------------------------------------------
if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false && strpos($_SERVER['HTTP_HOST'], 'xip') === false && strpos($_SERVER['HTTP_HOST'], 'demo-promethee:8888') === false && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false)
{

	if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
			$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $location);
			exit;
	// echo "REDIRECT";
	}
}
else
{
	// DO nothing
}
// ---------------------------------------------------------------------------


// ---------------------------------------------------------------------------
// Protection contre les injections SQL: on retire tous les '\' pour en
// rajouter là où c'est nécessaire après...
// ---------------------------------------------------------------------------
foreach ($_POST as $key => $value) {
	// Si la valeur est un tableau, alors on ajoute des '\' au valeurs dudit tableau
	if (is_array($value))
	{
		foreach ($value as $key2 => $value2) $_POST[$key][$key2] = addslashes(stripslashes($value2));
	}
	else
	{
		if (strpos($key, '_ckeditor') !== false) $_POST[$key] = str_replace("<style", "", str_replace("<script", "", addslashes(stripslashes($value))));
		else $_POST[$key] = htmlspecialchars(addslashes(stripslashes($value)));
	}

}

foreach ($_GET as $key => $value) {
	// Si la valeur est un tableau, alors on ajoute des '\' au valeurs dudit tableau
	if (is_array($value))
	{
		foreach ($value as $key2 => $value2) $_GET[$key][$key2] = addslashes(stripslashes($value2));
	}
	else
	{
		if (strpos($key, '_ckeditor') !== false) $_GET[$key] = str_replace("<style", "", str_replace("<script", "", addslashes(stripslashes($value))));
		else $_GET[$key] = htmlspecialchars(addslashes(stripslashes($value)));
	}


}

foreach ($_REQUEST as $key => $value) {
	// Si la valeur est un tableau, alors on ajoute des '\' au valeurs dudit tableau
	if (is_array($value))
	{
		foreach ($value as $key2 => $value2) $_REQUEST[$key][$key2] = addslashes(stripslashes($value2));
	}
	else
	{
		if (strpos($key, '_ckeditor') !== false) $_REQUEST[$key] = str_replace("<style", "", str_replace("<script", "", addslashes(stripslashes($value))));
		else $_REQUEST[$key] = htmlspecialchars(addslashes(stripslashes($value)));
	}
}
// ---------------------------------------------------------------------------














// Vérification mobile
$user_agent = $_SERVER["HTTP_USER_AGENT"];
if(!strpos($user_agent, "iPad") && (strpos($user_agent, "Mobile") || strpos($user_agent, "BlackBerry") || strpos($user_agent, "SymbianOS") || strpos($user_agent, "iPhone") || strpos($user_agent, "Windows Phone")))
{
	$location = "index_mobile.php";
	$getWith = "";
	foreach ($_GET as $key => $value) {
		if ($getWith != "") $getWith .= "&";
		$getWith .= $key."=".$value;
	}
	if ($getWith != "") $location .= "?".$getWith;

	header("Location: ".$location);
}



//---------------------------------------------------------------------------
// Si on a cliqué sur le lien dans le mail:
//---------------------------------------------------------------------------
if (isset($_SESSION['pathToGo']) && $_SESSION['pathToGo'] != "")
{
	$pathExploded_1 = explode("||1", $_SESSION['pathToGo']);
	foreach ($pathExploded_1 as $key => $value) {
		$pathExploded_2 = explode("||2", $value);
		if ($key == 0) $finalPath = "?";
		else $finalPath .= "&";

		$finalPath .= $pathExploded_2[0]."=".$pathExploded_2[1];
	}

	$finalPath = "index.php".$finalPath;
	$_SESSION['pathToGo'] = "";

	header("Location: ".$finalPath);
}
//---------------------------------------------------------------------------

// les pages par défaut
	$page        = "forbidden.php";
	$page_left   = "page_gauche.php";
	$page_right  = "page_droit.php";

// Reload
$reload    = ( @$_POST["reload"] )
	? $_POST["reload"]
	: @$_GET["reload"] ;



// On reload le cache si la demande à été effectuée (dans le GET) ou si on est administrateur (cache du menu pour les comptes activés)
// if($reload == "on" or $_SESSION["CnxAdm"] == 255)
if($reload == "on" || (isset($_SESSION['reload_forced']) && $_SESSION['reload_forced'] == "on"))
{
	$_SESSION['reload_forced'] = "";
	if (file_exists('cache/'.$_SESSION["idcache"].'_top.html')) unlink('cache/'.$_SESSION["idcache"].'_top.html');
	if (file_exists('cache/'.$_SESSION["idcache"].'_'.$page_left)) unlink('cache/'.$_SESSION["idcache"].'_'.$page_left);
}

// config
if(isset($_GET["config"]) && $_GET["config"] == "on")
{
	$_SESSION["config"] = "on";
	unlink('cache/'.$_SESSION["idcache"].'_top.html');
	unlink('cache/'.$_SESSION["idcache"].'_'.$page_left);
}
else if(isset($_GET["config"]) && $_GET["config"] == "off")
{
	$_SESSION["config"] = "off";
	unlink('cache/'.$_SESSION["idcache"].'_top.html');
	unlink('cache/'.$_SESSION["idcache"].'_'.$page_left);
	}
else if(!isset($_SESSION["config"]))
{
	$_SESSION["config"] = "";
}

// variables d'environnement pour l'hébergement de sites
$_SESSION["CFGDIR"] = ( @$CFGDIR != "" ) ? $CFGDIR : "." ;
$_SESSION["ROOTDIR"] = ( @$ROOTDIR != "" ) ? $ROOTDIR : "." ;

//---- compteur des téléchargements ----//
if ( @$_SESSION["CnxID"] AND @$_SESSION["CnxIP"] AND @$_GET["file"] ) {
	// petite protection
	if ( strstr($_GET["file"], "../") OR strstr($_GET["file"], "config.php") ) {
		require_once "include/download.php";

		logDownloadError($_GET["file"]);
		}
	else {
		// lancement du téléchargement
		Header("Location: ".$_GET["file"]);

		require_once "include/download.php";

		// mise à jour des fichiers de log
		logDownloadFile(@$_GET["fname"] ? $_GET["fname"] : $_GET["file"]);
		if ( @$_GET["tmp"] )
			logTmpFile($_GET["file"]);

		exit;
		}
	}
else {
	// suppression des fichiers temporaires
	require_once "include/download.php";

	deleteTmpFile($TIMETMP);
	}

$item = ( @$_GET["item"] )		// item des menus
	? (int) $_GET["item"]
	: (int) @$_POST["item"] ;

$cmde = ( @$_POST["cmde"] )		// option sur les items du menu
	? $_POST["cmde"]
	: @$_GET["cmde"] ;







//---------------------------------------------------------------------------
$MAXPAGE = getParam('MAXPAGE');
//---------------------------------------------------------------------------

?>


<!DOCTYPE html>
<?php
	// lecture de la configuration
	require "page_banner.php";

	$width_right = "0px";

	$force_left  = false;
	$force_right = true;

	$errconnect  = "";


	// Si l'utilisateur est connecté
	if ( isUserConnected() )
	{
		if ( $_SESSION["CnxAdm"] )
		{
			require_once "modulesitem.php";
		} // endif utilisateur autorisé
		else {
			// On ne devrai pas arriver ici
			// compte fermé
			$Query  = "select _email from user_id ";
			$Query .= "where _email != '' AND _adm = '255' ";
			$Query .= "order by _ID asc limit 1";

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

			$email  = ( $row ) ? "<a href=\"mailto:$row[0]\">". $message->read($PAGE_ADMIN) ."</a>" : $message->read($PAGE_ADMIN) ;

			$errconnect = $message->read($PAGE_ACCOUNTCLOSE, $email);
		}
	}

	// Utilisateur pas identifié
	else
	{
		// RAZ si timeout
		$_SESSION["CnxID"]  = "";		// ID utilisateur
		$_SESSION["CnxAdm"] = "";		// Droits de connexion de l'utilisateur
		$_SESSION["egroup"] = "";

		$page_left = $page_right = "";

		switch ( $item ) {
			case '1000' :	// création de compte
				list($iscreat, $nil) = explode(":", $AUTHUSER);

				if ( $iscreat )
					$page = "user_new.php";
				break;
			case '1' :		// mot de passe perdu
				$page = "user_lost.php";
				break;
			case '1100' :
				$page = "mail_validate.php";
				break;
			default :		// demande d'identification
				$page = "user_login.php";
				break;
			}
		}



?>

<!-- haut de page -->
<?php

// ---- l'utilisateur est identifié
// if ( isset($_SESSION["sessID"]) AND $_SESSION["sessID"] != "" AND !empty($_SESSION["CnxAdm"]) && isset($_SESSION['CnxID']) && $_SESSION['CnxID'] != 0 )
if (isUserConnected())

{
	if((file_exists('cache/'.$_SESSION["idcache"].'_top.html')) AND ($reload == ""))
	{
		require 'cache/'.$_SESSION["idcache"].'_top.html';
	}
	else {
	//On reconstruit le cache
	ob_start();
	require "page_top_connected.php";
	$tampon = ob_get_contents(); // stockage du tampon dans une chaîne de caractères
	file_put_contents('cache/'.$_SESSION["idcache"].'_top.html', $tampon) ;
	ob_end_clean(); // fermeture de la tamporisation de sortie et effacement du tampon
	echo $tampon ;
	}
}
else
{
	// require "page_top.php";
}








		// MENUSKIN : 0 = 2 colonnes, 1 = colonne gauche, 2 = colonne droite
		if ($item != "111" && $item != "120" && $item != "64" && $item != "63") {
			if ( $MENUSKIN != 2 AND $page_left )
			{


					require_once "include/postit.php";

					// a-t-on reçu un post-it ?
					newMail();
			}
			// if ( $MENUSKIN == 1 AND !$force_right AND $page_right )
			// 	require $page_right;
		}
	?>





	<?php
    if ($page == 'maintenance') require RESSOURCE_PATH['PAGES_FOLDER'].'page_under_maintenance.php';
		elseif (file_exists($_SESSION["ROOTDIR"]."/".$page)) {
      // Les fichiers de trads sont tous à la racine du dossier de langue mais pas les fichiers de pages
      $page_trad = getPageWithoutPath($page);
			if ($errconnect != '') $alert->error($message->read($PAGE_ERRCONNECT), $errconnect, 'danger'); // On affiche une alerte en cas d'erreure

			// sélection du fichier langue
			$path = $_SESSION["ROOTDIR"] . "/";
			$file = explode(" ", str_replace(Array($path, '/', '_', '.'), Array('', ' ', ' ', ' '), $page_trad));

			if (is_file($_SESSION["ROOTDIR"]."/msg/$file[0].php")) require "msg/$file[0].php";

			$msg  = new TMessage("msg/".$_SESSION["lang"]."/$file[0].php", $_SESSION["ROOTDIR"]);

			// log des stats
			logSession($page);

			// Dérivation de pages
  		if(file_exists("derivation/page/".$page)) $page = "derivation/page/".$page;

			// affichage page
      // echo '<strong>Page: </strong>'.$page.' | <strong>Page traductions: </strong>'.$page_trad;

      // // On log la page de l'utilisateur
      // setLog('info', 'page', array('page' => "$page", 'item' => $item, 'cmde' => $cmde));

      // On stoque la page dans l'historique
      if ($item == 0) unset($_SESSION['page_path']);
      if (isset($_GET['back']) && $_GET['back'] == 'true') $back_link = true; else $back_link = false;
      storePagePath($item, $cmde, $idmenu_global, $back_link);

			require $page;
		}
		else {
			require "forbidden.php";     // accès refusé
			logSession("forbidden.php"); // log des stats
		}







require "page_bottom.php";
?>
