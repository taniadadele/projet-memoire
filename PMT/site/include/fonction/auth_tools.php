<?php
/*-----------------------------------------------------------------------*
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
 *		module   : auth_tools.php
 *		projet   : Fonctions nécessaires pour la gestion de l'identification
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 21/10/2019
 *		modif    :
 */


 /*

    Ne pas faire d'echo sur cette page!


 */


// ---------------------------------------------------------------------------
// Fonction: Crée un identifiant unique utilisé comme identifiant de session
// IN:		    -
// OUT: 		  Identifiant de session (TEXT)
// ---------------------------------------------------------------------------
function createUniqueSessID()
{
  $sessID = time().$_SERVER['REMOTE_ADDR'];
  $sessID = md5($sessID);
  return $sessID;
}
// ---------------------------------------------------------------------------




// ---------------------------------------------------------------------------
// Fonction:  Déconnecte l'utilisateur et supprime sa session et cookies
// IN:		    $type = Type de déconnexion, D = déco et E = expiration (TEXT)
//            $userID est utilisé si expiration pour les logs (id de l'utilisateur)
// OUT: 		  -
// ---------------------------------------------------------------------------
function disconnectUser($type, $userID = 0)
{
  global $mysql_link;
	// Suppression du cookie:
	$currentDay = date('d');
	$currentMonth = date('m');
	$currentYear = date('Y');
	$timeOutCookie = strtotime($currentYear."-".$currentMonth."-".($currentDay + 1)." 03:00:00");
	setcookie('token_key', 'none', $timeOutCookie, '/');
	$_COOKIE['token_key'] = 'none';

  if ($userID == 0) $userID = $_SESSION['CnxID'];

  // Écriture dans les logs
  mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '".$userID."', '', '".@$_SERVER["REMOTE_ADDR"]."', '".$type."')");

	$_SESSION["CnxID"]      = "";		// ID utilisateur
	$_SESSION["CnxIP"]      = "";		// @IP de l'utilisateur
	$_SESSION["CnxAdm"]     = "";		// Droits de connexion de l'utilisateur
	$_SESSION["CnxName"]    = "";		// Nom de connexion de l'utilisateur
	$_SESSION["CnxGrp"]     = "";		// Groupe de connexion de l'utilisateur
	$_SESSION["CnxSex"]     = "";		// Sexe de l'utilisateur (A pour une connexion Anonyme)
	$_SESSION["CnxSign"]    = "";		// Signature des mails
	$_SESSION["CnxPers"]    = "";		// connexion persistante pour l'utilisateur
	$_SESSION["CnxCentre"]  = "";		// centre de formation
	$_SESSION["CnxPasswd"]  = "";		// mot de passe (vérification si vide)
	$_SESSION["CampusName"] = "";		// le e-campus
	$_SESSION["CnxClass"]   = "";		// classe de l'élève
	$_SESSION["egroup"]     = "";

	$_SESSION['CnxIdent']		= "";
	$_SESSION["sessID"]			= "";
}
//---------------------------------------------------------------------------



// ---------------------------------------------------------------------------
// Fonction:  Vérifie si l'utilisateur est connecté
// IN:		    -
// OUT: 		  True si connecté et False sinon (BOOL)
// ---------------------------------------------------------------------------
function isUserConnected()
{
  global $mysql_link;
	if (isset($_COOKIE['token_key']) && $_COOKIE['token_key'] != "" && $_COOKIE['token_key'] != "none")
	{
		$token_key = $_COOKIE['token_key'];
		$query = "SELECT _timeOut, _userID FROM user_cookie WHERE _token = '".$token_key."' ";
		$result = mysqli_query($mysql_link, $query);
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

			if ($row[0] > time()) return true;
      else
      {
        disconnectUser('E', $row[1]);
        return false;
      }
		}
	}
	else return false;

}
// ---------------------------------------------------------------------------





// ---------------------------------------------------------------------------
// Fonction:  Récupère toutes les infos de l'utilisateur depuis la session
// IN:		    -
// OUT: 		  True si connecté et False sinon (BOOL)
// ---------------------------------------------------------------------------
function getUserDataFromCookie()
{
  global $mysql_link;
	// On vérifie que l'utilisateur soit bien connecté
	if (isUserConnected())
	{
		$token_key = $_COOKIE['token_key'];
		$query = "SELECT _userID FROM user_cookie WHERE _token = '".$token_key."' ";
		$result = mysqli_query($mysql_link, $query);
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$query_2  = "SELECT _ID, _adm, _name, _fname, _ident, _IDclass, _sexe, _IDcentre, _IDgrp, _passwd, _signature, _lang FROM user_id ";
			$query_2 .= "WHERE _ID = '".$row[0]."' ";
			$query_2 .= "AND _adm > '0' ";
			$query_2 .= "LIMIT 1";
			$result_2 = mysqli_query($mysql_link, $query_2);
			while ($row_2 = mysqli_fetch_array($result_2, MYSQLI_NUM)) {
				$_SESSION["CnxID"]     = $row_2[0];						// ID de l'utilisateur
				$_SESSION["CnxAdm"]    = $row_2[1];						// Droits de connexion de l'utilisateur
				$_SESSION["CnxName"]   = formatUserName($row_2[3], $row_2[2]);		// Nom de connexion de l'utilisateur
				$_SESSION["CnxIdent"]  = $row_2[4];
				$_SESSION["CnxClass"]  = $row_2[5];						// classe de l'élève
				$_SESSION["CnxSex"]    = $row_2[6];						// Sexe de l'utilisateur
				$_SESSION["CnxCentre"] = $row_2[7];						// centre de formation
				$_SESSION["CnxGrp"]    = $row_2[8];						// Groupe de connexion de l'utilisateur
				$_SESSION["CnxPasswd"] = $row_2[9];						// mot de passe (vérification si vide)
				$_SESSION["CnxSign"]   = $row_2[10];						// signature forum, ...
				if (isset($pwd)) $_SESSION["CnxSSO"]	   = base64_encode($pwd);
				$_SESSION["idcache"]   = md5(uniqid());
        // Bug si le champ _lang de la BDD n'est pas remplis...
        $_SESSION["lang"] = $row_2[11];
        if ($_SESSION['lang'] == '') $_SESSION['lang'] = getCenterLanguageByIDCenter($_SESSION['CnxCentre']);

				$_SESSION["sessID"] = createUniqueSessID();
				$_SESSION['CnxPers'] = 'N';
				$_SESSION['CnxIP'] = $_SERVER['REMOTE_ADDR'];
        if ($_SESSION['CnxIP'] == '::1') $_SESSION['CnxIP'] = '192.168.1.1';
			}
		}
	}
	else return false;

}
// ---------------------------------------------------------------------------


// ---------------------------------------------------------------------------
// Fonction:  Récupère la langue du centre en fonction de son ID
// IN:		    ID du centre (INT)
// OUT: 		  La langue du centre (TEXT)
// ---------------------------------------------------------------------------
function getCenterLanguageByIDCenter($idCenter)
{
  global $mysql_link;
  $query = "SELECT _lang FROM config_centre WHERE _IDcentre = '".$idCenter."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}
// ---------------------------------------------------------------------------


// ---------------------------------------------------------------------------
// Fonction: Récupère l'adresse IP de l'utilisateur
// IN:       nan
// OUT:      L'adresse IP (TEXT)
// ---------------------------------------------------------------------------
function getUserIP() {
  $ip = $_SESSION["CnxIP"];
  if ($ip == '::1') $ip = '127.0.0.1';
  return $ip;
}





?>
