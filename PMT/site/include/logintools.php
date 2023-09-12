<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)

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
 *		module   : logintools.php
 *		projet   : utilitaires de connexion
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 24/06/06
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


//---------------------------------------------------------------------------
function whoami($sid)
{
/*
 * fonction :	retourne le niveau d'accès à une ressource
 * in :		$sid : session de l'utilisateur
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		$query  = "select distinctrow user_id._adm, user_id._ID, user_id._IDgrp,  user_id._name,  user_id._fname ";
		$query .= "from user_id, user_session ";
		$query .= "where _IDsess = '$sid' ";
		$query .= "AND user_id._ID = user_session._ID ";
		$query .= "order by _lastaction limit 1";

		$result = mysqli_query($mysql_link, $query);
		$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$_SESSION["CnxAdm"]  = (int) @$auth[0];
		$_SESSION["CnxID"]   = (int) @$auth[1];
		$_SESSION["CnxGrp"]  = (int) @$auth[2];
		$_SESSION["CnxName"] = formatUserName($auth[3], $auth[4]);
		$_SESSION["CnxIP"]   = SessionIP();
		}
}
//---------------------------------------------------------------------------
function SessionIP()
{
/*
 * fonction :	détermine l'ID de l'adresse IP de connexion
 * out :		index dans la table ip
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		// on cherche dans la table des postes clients
		$Query  = "select _IP, _visible from ip ";
		$Query .= "where _IPv6 = '".$_SERVER["REMOTE_ADDR"] ."'";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// si l'adresse n'est pas autorisée => on éjecte
		if ( mysqli_affected_rows($mysql_link) )
			return ( $row[1] == "N" AND $IPFILTER ) ? 0 : $row[0] ;

		// puis dans celle des postes distants
		$Query  = "select _IP, _visible from ip_remote ";
		$Query .= "where _IPv6 = '".$_SERVER["REMOTE_ADDR"] ."'";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// si l'adresse n'est pas autorisée => on éjecte
		if ( mysqli_affected_rows($mysql_link) )
			return ( $row[1] == "O" ) ? - $row[0] : 0 ;

		// si aucune adresse n'a été trouvée, on la rajoute
		$Query  = "insert into ip_remote ";
		$Query .= "values('', '".$_SERVER["REMOTE_ADDR"]."', '".gethostbyaddr(@$_SERVER["REMOTE_ADDR"])."', 'O')";

		return mysqli_query($mysql_link, $Query)
			? - mysqli_insert_id($mysql_link)
			: 0 ;
		}

	return 0;
}
//---------------------------------------------------------------------------
function getAccess($idgrp = 0)
{
/*
 * fonction :	retourne le niveau d'accès à une ressource
 * in :		ID du groupe
 * out :		niveau d'accès
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ($mysql_link) {
		$idgrp  = ($idgrp) ? $idgrp : @$_SESSION["CnxGrp"] ;

		$query  = "select _IDcat from user_group ";
		$query .= "where _IDgrp = '$idgrp' ";
		$query .= "AND _lang = '".@$_SESSION["lang"]."' ";
		$query .= "limit 1";

		$result = mysqli_query($mysql_link, $query);
		$row    = ($result) ? mysqli_fetch_row($result) : 0 ;

		return ( mysqli_affected_rows($mysql_link) ) ? (int) $row[0] : 0 ;
		}

	return 0;
}
//---------------------------------------------------------------------------
function formatUserName($name, $fname)
{
/*
 * fonction :	retourne le nom formaté de l'utilisateur
 * in :		$name : nom de l'utilisateur, $fname : prénom de l'utilisateur
 * out :		nom formaté de l'utilisateur
 */

	return ($fname != '')
		? ucwords(strtolower($name.', '.$fname))
		: ucwords(strtolower($name)) ;
}
//---------------------------------------------------------------------------
function _getHostName($my_ip, $mouseover = true)
{
/*
 * fonction :	retourne le nom du poste distant
 * in :		$my_ip, index de la table
 * out :		nom du poste
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		// on cherche dans la table des utilisateurs
		$Query  = "select _adm from user_id ";
		$Query .= "where _ID = '".@$_SESSION["CnxID"]."' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// si l'utilisateur est un membre
		// on l'autorise à voir les IP
		if ( $row AND ($row[0] & 2) ) {
			// on cherche dans la table des postes clients
			$Query  = "select _host, _IPv6 ";
			$Query .= ( $my_ip < 0 )
				? "from ip_remote where _IP = '". -$my_ip ."' "
				: "from ip where _IP = '$my_ip' ";
			$Query .= "limit 1";

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

			if ( mysqli_affected_rows($mysql_link) )
				return ( $my_ip < 0 )
					? ( $mouseover
						? "<a href=\"#\" class=\"overlib\">@ $row[1]<span>$row[0]</span></a>"
						: "@ $row[0]" )
					: "@ $row[0]" ;
			}
		}

	return "@ ??";
}
//---------------------------------------------------------------------------
function resolveHostName($my_ip)
{
/*
 * fonction :	retourne le nom du poste distant
 * in :		$my_ip, index de la table
 * out :		nom du poste
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		// on cherche dans la table des postes clients
		$Query  = "select _IP from ip_remote ";
		$Query .= "where _IPv6 = '$my_ip' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		if ( mysqli_affected_rows($mysql_link) )
			return _getHostName(-$row[0]);
		else {
			$Query  = "select _IP from ip ";
			$Query .= "where _IPv6 = '$my_ip' ";
			$Query .= "limit 1";

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

			if ( mysqli_affected_rows($mysql_link) )
				return _getHostName($row[0]);
			}
		}

	return "@ ??";
}
//---------------------------------------------------------------------------
function getIP($my_ip)
{
/*
 * fonction :	retourne le nom du poste distant
 * in :		$my_ip, index de la table
 * out :		nom du poste
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		// on cherche dans la table des postes clients
		$Query  = "select _IPv6 ";
		$Query .= ( $my_ip < 0 )
			? "from ip_remote where _IP = '". -$my_ip ."' "
			: "from ip where _IP = '$my_ip' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		return ( $row ) ? $row[0] : "" ;
		}

	return "";
}
//---------------------------------------------------------------------------
function setvisibleIP($IP, $visible = "O")
{
/*
 * fonction :	mise en liste brûlée d'une IP pour empêcher l'ouverture d'une session
 * in :		$IP, adresse IP
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link AND $IP ) {
		$date   = date("Y-m-d H:i:s");

		$Query  = "INSERT INTO ip_denied ";
		$Query .= "values('', '$IP', '$date', '$visible')";

		if ( !mysqli_query($mysql_link, $Query) ) {
			$Query  = "UPDATE ip_denied ";
			$Query .= "SET _visible = '$visible', _date = '$date' ";
			$Query .= "WHERE _IPv6 = '$IP' ";
			$Query .= "LIMIT 1";

			mysqli_query($mysql_link, $Query);
			}
		}
}
//---------------------------------------------------------------------------
function isvisibleIP($IP)
{
/*
 * fonction :	test si une IP a été placée en liste brûlée
 * in :		$IP, adresse IP
 * out :		N si IP brûlée, O sinon
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link AND $IP ) {
		$Query  = "select _visible from ip_denied ";
		$Query .= "where _IPv6 = '$IP' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		return ( $row ) ? $row[0] : "O" ;
		}

	return "O";
}
//---------------------------------------------------------------------------
function getPageWithoutPath($page) {
	if (strpos($page, '/') !== false) return trim(substr($page, strrpos($page, '/') + 1));
	else return $page;
}
//---------------------------------------------------------------------------
function logSession($page)
{
	// global $mysql_link;
/*
 * fonction :	log des stats de connexion
 * in :		$page, nom de la page
 * out :		0 si erreur
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		require $_SESSION["ROOTDIR"]."/msg/stats.php";
		require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

		$msg = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/stats.php");

		// effaçage des stats
		if ($TIMESTAT) {
			// on efface les stats trops anciennes
			$query  = "DELETE FROM stat_page ";
			$query .= "WHERE _date < '". date("Y-m-d H:i:s", (time() - $TIMESTAT)) ."'";

			if (!mysqli_query($mysql_link, $query))
				sql_error($mysql_link);
			}

		// date du log
		// $lastaction = date('Y-m-d H:i:s');
		$attr = array(
			'full_path' => $page
		);

		$page_without_path = getPageWithoutPath($page);

		$query    = "insert into stat_page ";
		$query   .= "values(NOW(), '$page_without_path', '".@$_SESSION["CnxGrp"]."', '".@$_SESSION["CnxID"]."', '".@$_SESSION["CnxIP"]."', '".json_encode($attr)."')";

		// on enregistre les logs pour les stats répertoriées
		// return (strstr($msg->read($STATS_STATLABEL), $page)) ? mysqli_query($mysql_link, $query) : 0 ;

		return mysqli_query($mysql_link, $query);
		}

	return 0;
}
//---------------------------------------------------------------------------
function getConfigString($config_string, $id)
{
/*
 * fonction :	détermine un ID utilisateur pour des login automatique
 * in :		$config_string : configuration ([record:length,start]), $id : id utilisateur
 * out :		ID utilisateur
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// initialisation
	$string = "";
	$field  = Array();

	if ( $config_string != "" ) {
		$param = preg_split("/]|\[/", $config_string);

		// on récupère les champs de la table sélectionnée
		$j = 0;
		for ($i = 0; $i < count($param); $i++)
			if ( strpos($param[$i], ":") )
				$field[$j++] = explode(":", $param[$i]);

		if ( count($field[0]) ) {
			$Query  = "select ". $field[0][0];
			for ($i = 1; $i < $j; $i++)
				$Query .= ", " .$field[$i][0];
			$Query .= " from user_id ";
			$Query .= "where _ID = '$id' ";
			$Query .= "limit 1";

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
			}

		// on construit la chaîne
		$j = $k = 0;
		for ($i = 0; $i < count($param); $i++)
			if ( strpos($param[$i], ":") ) {
				$delimiter = explode(",", $field[$k][1]);

				if ( $delimiter[0] )
					$string .= substr($row[$j++], @$delimiter[1], @$delimiter[0]);
				else
					// équivalent de substr($row[$j++], 0, @$delimiter[0]);
					$string .= $row[$j++];

				$k++;
				}
			else
				$string .= $param[$i];
		}

	return $string;
}
//---------------------------------------------------------------------------
function getUserID($id)
{
/*
 * fonction :	détermine un ID utilisateur pour des login automatiques
 * in :		$id, id de l'élève
 * out :		ID utilisateur
 */

	global	$IDENT;
	global	$keywords_search, $keywords_replace;

	// par défaut
	$user_id = str_replace($keywords_search, $keywords_replace, "_STUDENT$id");
	$string  = getConfigString($IDENT, $id);

	return ( $string == "" ) ? $user_id : $string ;
}
//---------------------------------------------------------------------------
function getUserPassword($id)
{
/*
 * fonction :	détermine un mdp utilisateur pour des login automatiques
 * in :		$id, id de l'élève
 * out :		mot de passe de l'utilisateur
 */

	global	$AUTOPASSWD;

	return getConfigString($AUTOPASSWD, $id);
}
//---------------------------------------------------------------------------
?>
