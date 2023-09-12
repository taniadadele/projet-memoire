<?php
// /*-----------------------------------------------------------------------*
//    Copyright (c) 2002-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
//    Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)
//
//    This file is part of Prométhée.
//
//    Prométhée is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Prométhée is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
//  *-----------------------------------------------------------------------*/
//
//
// /*
//  *		module   : session.php
//  *		projet   : gestion des identifiants de session
//  *
//  *		version  : 1.0
//  *		auteur   : laporte
//  *		creation : 17/03/02
//  *		modif    : 17/07/06 - Nordine Zetoutou
//  * 	                 migration des balises HTML en XHTML 1.0 strict
//  */
//
//
// // remarque : depuis PHP 4, il existe une bibliothèque de gestion de sessions intégrée...
//
// //---------------------------------------------------------------------------
// function getmicrotime()
// {
// 	list($usec, $sec) = explode(" ", microtime());
// 	return ((float) $usec + (float) $sec);
// }
// //---------------------------------------------------------------------------
// function isSessionVisible($sid)
// {
// /*
//  * fonction :	indique si l'utilisateur est loggué en fantôme
//  * in :		$sid, id de session
//  * out :		vrai si session visible, faux sinon
//  */
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	// l'identifiant de connexion a été mise à jour dans la page login
// 	$Query  = "select _visible from user_session ";
// 	$Query .= "where _IDsess = '$sid'";
//
// 	$result = mysqli_query($mysql_link, $Query);
// 	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
//
// 	return ( $row[0] == "O" ) ? true : false ;
// }
// //---------------------------------------------------------------------------
// function SessionID($length = 10, $Pool = "")
// {
// /*
//  * fonction :	création d'un identifiant de session
//  * in :		$length, longueur de l'identifiant de session
//  *			$Pool, chaîne de caractères aléatoires
//  * out :		identifiant de session
//  */
// 	// Définition d'un jeu de caractères possibles
// 	if ( !strlen($Pool) )
// 	{
// 		$Pool  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
// 		$Pool .= "abcdefghijklmnopqrstuvwxyz";
// 	}
//
// 	// tirage aléatoire
// 	$sid = "";
// 	for($index = 0; $index < $length; $index++)
//             $sid .= substr($Pool, rand() % strlen($Pool), 1);
//
// 	// renvoie de l'identifiant
// 	return $sid;
// }
// //---------------------------------------------------------------------------
// function eraseSessionID($sessID = "")
// {
// /*
//  * fonction :	efface la session
//  * in :		$sessID, identifiant de session
//  * out :		identifiant actuel si échec, nul sinon
//  */
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	// connexion à la base de données
// 	$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
//
// 	if ( !$mysql_link )
// 		return $sessID;
//
// 	// déconnexion de la session
// 	$Query  = "UPDATE user_session ";
// 	$Query .= "SET _action = 'D' ";
// 	$Query .= ( $sessID ) ? "WHERE _IDsess = '$sessID'" : "" ;
//
// 	if ( $DEBUG )
// 		print("$Query<br/>");
//
// 	if ( !mysqli_query($mysql_link, $Query) )
// 	{
// 		sql_error($mysql_link);
// 		return $sessID;
// 	}
//
// 	// Sécurité : régeneration du PHPSESSID
// 	session_regenerate_id();
//
// 	return "";
// }
// //---------------------------------------------------------------------------
// function createSessionID($visible = "O")
// {
// /*
//  * fonction :	création de session
//  * in :		$sessID, identifiant de session
//  *			$visible, utilisateur visible dans la liste des connectés
//  * out :		identifiant de session
//  */
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	// Sécurité : régeneration du PHPSESSID
// 	// session_regenerate_id();
//
// 	// on évite les doublons sur une connexion/déconnexion
// 	// sauf en mode DEMO
// 	// ou pour les utilisateurs anonymes qui peuvent se logguer à plusieurs sous le même identifiant
// 	if ( !$DEMO )
// 		if ( $_SESSION["CnxSex"] != "A" )
// 			mysqli_query($mysql_link, "delete from user_session where _ID = '".$_SESSION["CnxID"]."'");
//
// 	// création de la session
// 	$sessID  = SessionID();
//
// 	// mise à jour de la date de session
// 	$lastupd = date("Y-m-d H:i:s");
//
// 	// les anonymes
// 	$anonyme = ( $_SESSION["CnxSex"] == "A" ) ? "O" : "N" ;
//
// 	// insertion d'une session dans la base de données
// 	// l'identifiant de connexion CnxID a été mise à jour dans la page user_login
// 	$Query   = "INSERT INTO user_session ";
// 	$Query  .= "VALUES('$sessID', '$lastupd', '".$_SESSION["CnxID"]."', '$visible', '$anonyme', 'C', '".$_SESSION["CnxIP"]."')";
//
// 	if ( $DEBUG )
// 		print("New session : $Query<br/>");
//
// 	if ( !mysqli_query($mysql_link, $Query) )
// 	{
// 		sql_error($mysql_link);
//      	     	$sessID = "";
// 	}
//
// 	return $sessID;
// }
// //---------------------------------------------------------------------------
// function timeoutSession($id)
// {
// /*
//  * fonction :	indique si un compte à dépassé sa durée d'inscription
//  * in :		$id : ID de connexion de l'utilisateur
//  * out :		true si timeout, false sinon
//  */
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	// suppression des comptes inactifs
// 	if ( $AUTODEL ) {
// 		$date = date("Y-m-d H:i:s", time() - ($AUTODEL * 3600));
//
// 		// compte utilisateur
// 		$query  = "delete from user_id ";
// 		$query .= "where _lastcnx = '0000-00-00 00:00:00' OR _lastcnx = NULL ";
// 		$query .= "AND _create < '$date' ";
//
// 		mysqli_query($mysql_link, $query);
// 		}
//
// 	$date   = date("Y-m-d H:i:s");
//
// 	// compte utilisateur
// 	$query  = "select _delay, _IDgrp from user_id ";
// 	$query .= "where _ID = '$id' ";
// 	$query .= "limit 1";
//
// 	$result = mysqli_query($mysql_link, $query);
// 	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
//
// 	if ( $row[0] != "0000-00-00 00:00:00" and $row[0] != NULL)
// 		if ( $row[0] < $date )
// 			return true;
//
// 	// groupe utilisateur
// 	$query  = "select _delay from user_group ";
// 	$query .= "where _IDgrp = '$row[1]' ";
// 	$query .= "limit 1";
//
// 	$result = mysqli_query($mysql_link, $query);
// 	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
//
// 	if ( $row[0] != "0000-00-00 00:00:00" and $row[0] != NULL )
// 		if ( $row[0] < $date )
// 			return true;
//
// 	return false;
// }
// //---------------------------------------------------------------------------
// function updateSessionID($sessID, $ghost = "")
// {
// /*
//  * fonction :	mise à jour de session
//  * in :		$sessID, identifiant de session
//  *			$ghost, utilisateur visible dans la liste des connectés
//  * out :		identifiant de session
//  */
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	require $_SESSION["ROOTDIR"]."/msg/user.php";
// 	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";
//
// 	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/user.php");
//
// 	// connexion à la base de données
// 	$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
//
// 	if ( !$mysql_link )
// 		return "";
//
// 	// initialisation
// 	$visible = ( $ghost ) ? "N" : "O" ;
//
// 	// suppression de toutes les anciennes sessions ( < 30 minutes )
// 	$Query  = "DELETE FROM user_session ";
// 	$Query .= "WHERE _lastaction < '". date("Y-m-d H:i:s", (time() - $TIMELIMIT)) ."' ";
//
// 	if ( $DEBUG )
// 		print("$Query<br/>");
//
// 	if ( !mysqli_query($mysql_link, $Query) )
// 	{
// 		sql_error($mysql_link);
// 		return "";
// 	}
//
// 	// Si la session est vide, il faut la créer
// 	if ( !strlen($sessID) )
// 	{
// 		// création de la session
// 		$sessID = createSessionID($visible);
// 	}
// 	// sinon vérification de la session existante
// 	else
// 	{
// 		// on vérifie si la session est en timeout
// 		if ( timeoutSession($_SESSION["CnxID"]) )
// 			return "";
//
// 		// si la session existe, il faut l'examiner
// 		$Query  = "SELECT _action ";
// 		$Query .= "FROM user_session ";
// 		$Query .= "WHERE _IDsess = '$sessID' ";
// 		$Query .= "limit 1";
//
// 		$result = mysqli_query($mysql_link, $Query);
// 		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
//
// 		// la session a été trouvée
// 		if ( $row )
// 		{
// 			// if ( $DEBUG )
// 			// 	print($msg->read($USER_SESSION, $Query) ."<br/>");
//
// 			// si l'utilisateur a été déconnecté, on l'éjecte
// 			if ( $row[0] == "D" )
// 			{
// 				$Query  = "DELETE FROM user_session ";
// 				$Query .= "WHERE _IDsess = '$sessID' ";
// 				$Query .= "limit 1";
//
// 				print("<p style=\"color:#FF0000; text-align: center\">". $msg->read($USER_DISCONNECTED) ."</p>");
// 	     	      	$sessID = "";
// 			}
// 			// sinon on met à jour la dernière action
// 			else
// 			{
// 				$anonyme = ( $_SESSION["CnxSex"] == "A" ) ? "O" : "N" ;
//
// 				$Query   = "UPDATE user_session ";
// 				$Query  .= "SET _lastaction = '". date("Y-m-d H:i:s"). "', ";
// 				$Query  .= "_IP = '".$_SESSION["CnxIP"]."', ";
// 				$Query  .= "_ID = '".$_SESSION["CnxID"]."', ";
// 				$Query  .= "_anonyme = '$anonyme' ";
// 				$Query  .= ( isset($ghost) ) ? ", _visible = '$visible' " : "" ;
// 				$Query  .= "WHERE _IDsess = '$sessID' ";
// 				$Query  .= "limit 1";
// 			}
//
// 			if ( $DEBUG )
// 				print("$Query<br/>CnxID ".$_SESSION["CnxID"].".<br/>");
//
// 			if ( !mysqli_query($mysql_link, $Query) )
// 				sql_error($mysql_link);
// 		}
// 		//la session est en timeout
// 		else
// 		{
// 			// Si il y a un cookie
// 			if ($_COOKIE['token_key'] != "")
// 			{
// 				// On vérifie qu'il soit toujours valide
// 				$query = "SELECT _timeOut, _userID FROM user_cookie WHERE _token = '".$_COOKIE['token_key']."' LIMIT 1 ";
// 				$result = mysqli_query($mysql_link, $query);
// 				while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
// 					// Si l'utilisateur doit toujours être connecté alors on recrée la session
// 				  if ($row[0] > time())
// 					{
// 						$query_user  = "select _ID, _date, _cnx, _persistent, _sexe, _adm, _IDcentre, _name, _IDgrp, _passwd, _signature, _delay, _fname, _IDclass, _lang, _ident ";
// 						$query_user .= "from user_id ";
// 						$query_user .= "where _ID = '".$row[1]."' ";
// 						$query_user .= "limit 1";
// 						$result_user = mysqli_query($mysql_link, $query_user);
// 						while ($row_user = mysqli_fetch_array($result_user, MYSQLI_NUM)) {
// 							$_SESSION["CnxID"]     = $row_user[0];																	// ID de l'utilisateur
// 							$_SESSION["CnxPers"]   = $row_user[3];																	// connexion persistante pour l'utilisateur
// 							$_SESSION["CnxSex"]    = $row_user[4];																	// Sexe de l'utilisateur (A pour une connexion Anonyme)
// 							$_SESSION["CnxAdm"]    = $row_user[5];																	// Droits de connexion de l'utilisateur
// 							$_SESSION["CnxCentre"] = $row_user[6];																	// centre de formation
// 							$_SESSION["CnxName"]   = formatUserName($row_user[7], $row_user[12]);		// Nom de connexion de l'utilisateur
// 							$_SESSION["CnxGrp"]    = $row_user[8];																	// Groupe de connexion de l'utilisateur
// 							$_SESSION["CnxPasswd"] = $row_user[9];																	// mot de passe (vérification si vide)
// 							$_SESSION["CnxSign"]   = $row_user[10];																	// signature forum, ...
// 							$_SESSION["CnxClass"]  = $row_user[13];																	// classe de l'élève
// 							$_SESSION["CnxSSO"]	   = base64_encode($pwd);
// 							$_SESSION["idcache"]   = md5(uniqid());
//
// 							$_SESSION["CnxIdent"]  = $row_user[15];
// 							$_SESSION["lang"] = $row_user[14];
//
// 							$sessID = createSessionID($visible);
// 							$_SESSION["sessID"] = $sessID;
// 						}
// 					}
// 					else
// 					{
// 						mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '".$_SESSION["CnxID"]."', '', '".@$_SERVER["REMOTE_ADDR"]."', 'E')");
//          		// mauvaise session : on doit demander une identification car le délai a expiré
// 						print("<p style=\"color:#FF0000; text-align: center\">". $msg->read($USER_TIMEOUT) ."</p>");
// 			     	      	$sessID = "";
// 					}
// 				}
// 			}
// 			else
// 			{
// 				mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '".$_SESSION["CnxID"]."', '', '".@$_SERVER["REMOTE_ADDR"]."', 'E')");
//      		// mauvaise session : on doit demander une identification car le délai a expiré
// 				print("<p style=\"color:#FF0000; text-align: center\">". $msg->read($USER_TIMEOUT) ."</p>");
// 	     	      	$sessID = "";
// 			}
// 		}
// 	}
//
// 	return $sessID;
// }
//---------------------------------------------------------------------------
function sizeofSessionID($visible = "", $anonyme = "")
{
/*
 * fonction :	détermine le nombre de sessions ouvertes
 * in :		$visible, utilisateur fantôme
 * out :		nombre de session
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		$Query  = "SELECT _ID ";
		$Query .= "FROM user_session ";
		$Query .= "where _action = 'C' ";
		$Query .= ( $visible ) ? "AND _visible = '$visible' " : "" ;
		$Query .= ( $anonyme ) ? "AND _anonyme = '$anonyme' " : "" ;

		$result = mysqli_query($mysql_link, $Query);

		return ( $result ) ? mysqli_num_rows($result) : 0 ;
		}

	return 0;
}
//---------------------------------------------------------------------------
function admSessionAccess($adm = 0)
{
/*
 * fonction :	vérification des droits d'accès admin à un module
 * in :		$adm : niveau d'autorisation
 */

	if ( $_SESSION["CnxAdm"] == 255 )
		return true;

	if ( $adm )
		if ( $_SESSION["CnxAdm"] & $adm )
			return true;

	logSessionAccess();
}
//---------------------------------------------------------------------------
function verifySessionAccess($idmod, $grprd = 0, $grpwr = 0)
{
/*
 * fonction :	vérification des droits en lecture à un module
 * in :		$idmod : ID du modérateur, $grprd : groupe des lecteurs
 */

	if ( $_SESSION["CnxAdm"] == 255 )
		return true;

	if ( $idmod )
		if ( $_SESSION["CnxID"] == $idmod )
			return true;

	if ( $grprd )
		if ( $grprd & pow(2, $_SESSION["CnxGrp"] - 1) )
			return true;

	if ( $grpwr )
		if ( $grpwr & pow(2, $_SESSION["CnxGrp"] - 1) )
			return true;

	logSessionAccess();
}
//---------------------------------------------------------------------------
function logSessionAccess()
{
/*
 * fonction :	enregistrement des erreurs d'autorisation sur les modules
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	$item = ( @$_GET["item"] )		// item des menus
		? (int) $_GET["item"]
		: (int) @$_POST["item"] ;

	$cmde = ( @$_POST["cmde"] )		// option sur les items du menu
		? $_POST["cmde"]
		: @$_GET["cmde"] ;

	// l'identifiant de connexion a été mise à jour dans la page login
	$Query  = "insert into ip_logerr ";
	$Query .= "values('', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '".date("Y-m-d H:i:s")."', '".$_SESSION["CnxAdm"]."', '".$_SESSION["CnxName"]."', '$item', '$cmde')";

	mysqli_query($mysql_link, $Query);

	print("Access error... ".$_SESSION["CnxName"]." [".$_SESSION["CnxAdm"]."] @ ".@$_SERVER["REMOTE_ADDR"]." (".gethostbyaddr(@$_SERVER["REMOTE_ADDR"]).") - ".date("Y-m-d H:i:s"));

	exit;
}
//---------------------------------------------------------------------------
?>
