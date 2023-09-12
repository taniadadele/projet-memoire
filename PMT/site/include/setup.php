<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2010 by Dominique Laporte(C-E-D@wanadoo.fr)

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
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.ss
 /*-----------------------------------------------------------------------*/


/*
 *		module   : setup.php
 *		projet   : la page de configuration automatique de la base de données
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 30/08/10
 *		modif    : 
 */



// --------------------------------------------------------------------
function stripComment($string)
{
/*
 * fonction :	enlève les commentaires d'une requête SQL
 * in :		$string, requête SQL à traiter
 * out :		requête SQL sans commentaire
 */

	if ( $string == "" )
		return "";

	$ret = strpos($string, "#");
	$pos = ( $ret == false AND $string[0] != '#' ) ? -1 : $ret ;
	$p   = $pos - 1;

	if ( $pos > -1 ) {
		if ( $p > 0 ) {
			while ( $pos > -1 AND $string[$p] != "\t" ) {
				$ret = strpos(substr($string, $pos + 1, strlen($string)), "#");
				if ( $ret == false )
					$pos = -1;
				else
					$pos += strpos(substr($string, $pos + 1, strlen($string)), "#") + 1;
				$p   = $pos - 1;
				}

			return ( $p > 0 AND @$string[$p] == "\t" )
				? substr($string, 0, $p)
				: $string ;
			}
		else
			return "";
		}
	else
		return $string;
}
// --------------------------------------------------------------------
function request($database, $isOk, $file)
{
/*
 * fonction :	exécution de requête SQL
 * in :		$database, nom de la base de données
 *			$isOk, identifiant resource MySQL
 *			$file, nom de fichier MySQL
 * out :		-1 si fichier SQL introuvable, nombre d'erreur de syntaxe SQL sinon
 */

	// ouverture fichiers
	if ( !($in  = @fopen($file, "r")) )
		return -1;

	// initialisation requête
	$query  = "";
	$erreur = 0;

	// lecture fichier sql
	$count = 1;

	while ( !feof($in) ) {
		// suppression des blancs et retour charriots
		$line = trim(str_replace("\n", "", fgets($in, 2048)));
		$line = str_replace("##DATABASE##", $database, $line);

		// suppression des commentaires
		$line = stripComment($line);

		// construction de la requête
		if ( strlen($line) ) {
			$query .= $line;

			// validation de la requête
			if ( strrpos($line, ";") == strlen($line) - 1 ) {
				// Trace
				global	$debug;
				if ( $debug == 2 )
					print("$query<br/>");

				// lancement de la requête
				if ( !mysqli_query($mysql_link, $query, $isOk) ) {
					$errno = mysqli_errno($isOk);
					$error = mysqli_error($isOk);

					print("<span style=\"color:#FF0000\"><strong>Error $errno</strong></span> : ($file @ $count) $error.</span><br/>");
					$erreur++;
					}

				$query = "";
				}
			}

		$count++;
		}

	//fermeture fichiers
	fclose($in);

	return $erreur;
}
// --------------------------------------------------------------------
function updatedatabase($server, $user, $passwd, $database, $servport, $langlist, $text)
{
	global $VERSION;

	require $_SESSION["ROOTDIR"]."/msg/setup.php";

	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/setup.php");

	// connexion à la dba MySql
	$servname = $server . ($servport ? ":$servport" : "");
	$isOk     = mysql_connect($servname, $user, $passwd);

	if ( !$isOk )
		return -1;

	// initialisation
	$error    = 0;

	// on met à jour la bdd
	list($major, $minor) = preg_split("/[.rc]/", $VERSION);

	$M = (int) $major;
	$m = (int) $minor;

	// l'incrémentation automatique ne fonctionne qu'à partir de la version 5.0
	if ( (int) $major > 4 ) {
		$query  = "select _version, _retcode from $database.config_database ";
		$query .= "order by _IDconf desc limit 1";

		$result = mysqli_query($mysql_link, $query, $isOk);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// attention à une table vide
		if ( mysqli_affected_rows($isOk) ) {
			list($M, $m) = preg_split("/[.rc]/", $row[0]);

			// si l'installation de la dernière maj est correcte
			// on passe à la version suivante
			// sauf si mode forçage
			if ( (int) $row[1] == 0 OR $force )
				$m++;
			}
		}

	// on effectue les mises à jour incrémentales
	for ($i = $M; $i <= $major && $error == 0; $i++) {
		for ($j = $m; $j <= $minor && $error == 0; $j++) {
			// pour l'instant on ne gère pas les versions correctives
			$version = "$i.$j"."rc1";

			if ( file_exists("Mysql/update/database-$version.sql") ) {
				$error += request($database, $isOk, "Mysql/update/database-$version.sql");

				// lecture des répertoires de langues
				for ($k = 0; $k < count($langlist); $k++) {
					$path   = "Mysql/update/".$langlist[$k]."/database-$version.sql";

					if ( file_exists($path) )
						$error += request($database, $isOk, $path);
					}

				// tables dans la base de données
//				$result = mysql_list_tables($database, $isOk);	// déprécié en PHP5
				$result = mysqli_query($mysql_link, "show tables from $database");
				$tables = ( $result ) ? mysqli_num_rows($result) : 0 ;

				// log des mises à jour
				mysqli_query($mysql_link, "insert into config_database values('', '$version', '". @$_SERVER["REMOTE_ADDR"] ."', '".date("Y-m-d H:i:s")."', '$tables', '$error', '". $_SESSION["lang"] ."')", $isOk);

				$img   = ( $error ) ? "off" : "on" ;

				$text .= $msg->read($MSG_UPDATE, $version) ." ";
				$text .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/setup/check_$img.png\" title=\"\" alt=\"*\" /><br/>";
				}

			// application des patch
			if ( file_exists("Mysql/update/patch/patch-$version.php") ) {
				require "Mysql/update/patch/patch-$version.php";

				if ( function_exists('patch') )
					patch($isOk);

				$img   = ( !function_exists('patch') ) ? "off" : "on" ;

				$text .= $msg->read($MSG_PATCH, "patch-$version.php") ." ";
				$text .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/setup/check_$img.png\" title=\"\" alt=\"*\" /><br/>";
				}
			}	// endfor minor

		$m = 0; $minor = 12;
		}	// endfor major

	return $error;
}
// --------------------------------------------------------------------
function createdatabase($server, $user, $passwd, $database, $servport, $langlist, $dba = "")
{
	global $VERSION;

	// connexion à la dba MySql
	$servname = $server . ($servport ? ":$servport" : "");
	$isOk     = mysql_connect($servname, $user, $passwd);

	if ( !$isOk )
		return -1;

	// la création de la DB peut prendre du temps => on supprime le tps max d'exécution des requêtes
	// attention : safe_mode doit être désactivé
	$safe_mode  = ini_get("safe_mode");
	$time_limit = ini_get("max_execution_time");

	if ( $safe_mode != "1" )
		set_time_limit(0);

	// choix de la base de données
	$path = ( $dba )
		? "Mysql/$dba"
		: "Mysql" ;

	if ( ($error = request($database, $isOk, "$path/database.sql")) == -1 )
		return -2;

	// ouverture du répertoire des fichiers sql
	$myDir = @opendir($path);

	// lecture des fichiers sql
	while ( $entry = @readdir($myDir) )
		if ( strstr($entry, ".sql") )
			if ( $entry != "database.sql" )
				$error += request($database, $isOk, "$path/$entry");

	// fermeture du répertoire
	@closedir($myDir);

	// lecture des répertoires sql/langues
	for ($k = 0; $k < count($langlist); $k++) {
		$myDir = @opendir("$path/".$langlist[$k]);

		// lecture des fichiers sql
		while ( $entry = @readdir($myDir) )
			if ( strstr($entry, ".sql") )
				$error += request($database, $isOk, "$path/".$langlist[$k]."/$entry");

		// fermeture du répertoire
		@closedir($myDir);
		}

	// tables dans la base de données
//	$result = mysql_list_tables($database, $isOk);	// déprécié en PHP5
	$result = mysqli_query($mysql_link, "show tables from $database");
	$tables = ( $result ) ? mysqli_num_rows($result) : 0 ;

	// log des créations
	mysqli_query($mysql_link, "insert into config_database values('', '$VERSION', '". @$_SERVER["REMOTE_ADDR"] ."', '".date("Y-m-d H:i:s")."', '$tables', '$error', '". $_SESSION["lang"] ."')", $isOk);

	// réinitialisation du tps max d'exécution des requêtes
	if ( $safe_mode != "1" )
		set_time_limit($time_limit);

	return $error;
}
// --------------------------------------------------------------------
function updateconfigfile(
	$server, $user, $passwd, $database, $servport,
	$ident, $adresse, $tel, $fax, $email, $web, $zip, $city, $IDtheme = 1)
{
	// connexion au serveur MySql
	$servname   = $server . ($servport ? ":$servport" : "");
	$mysql_link = mysql_connect($servname, $user, $passwd);

	if ( !$mysql_link )
		return -1;

	require $_SESSION["ROOTDIR"]."/msg/config.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/config.php");

	// recherche de la config de l'établissement
	$query  = "select _IDconf from config ";
	$query .= "where _visible = 'O' ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);

	// si on a trouvé une valeur => mise à jour
	if ( mysqli_num_rows($result) ) {
		$query  = "update config ";
		$query .= "set _idtheme = '$IDtheme', _adresse = '$adresse', _cp = '$zip', _ville = '$city', ";
		$query .= "_tel = '$tel', _fax = '$fax', _email = '$email', _web = '$web' ";
		$query .= "where _visible = 'O' ";

		if ( !mysqli_query($mysql_link, $query) )
			sql_error_and_die();
           	}
	// sinon création
	else {
		// toutes les langues
		$query  = "update config ";
		$query .= "set _ident = '$ident', ";
		$query .= "_adresse = '$adresse', _cp = '$zip', _ville = '$city', _tel = '$tel', _fax = '$fax', _email = '$email', _web = '$web', ";
		$query .= "_visible = 'O' ";
		$query .= "where _ident = ''";

		if ( !mysqli_query($mysql_link, $query) )
			sql_error_and_die();

		// pour la langue courante
		$title  = addslashes($msg->read($CONFIG_TITLE));
		$texte  = addslashes($msg->read($CONFIG_TEXT));
		$login  = addslashes($msg->read($CONFIG_LOGIN));

		$query  = "update config ";
		$query .= "set _title = '$title', _texte = '$texte', _login = '$login' ";
		$query .= "where _ident = '$ident' AND _lang = '".$_SESSION["lang"]."' ";
		$query .= "limit 1";

		if ( !mysqli_query($mysql_link, $query) )
			sql_error_and_die();

		// création répertoire
		$dir   = "download/logos/". stripslashes($ident);
		if ( !is_dir($dir) )
			if ( !mkdir($dir) )
				die($msg->read($CONFIG_NOPERM, $dir));
			else {
				// copie des logos par défaut
				$dest = "$dir/logo01.jpg";

				// copie du fichier temporaire -> répertoire de stockage
				$file = @$_FILES["UploadedFile1"]["tmp_name"];
				if ( $file )
					move_uploaded_file($file, $dest);
				else
					copy("download/logos/default/logo01.jpg", $dest);

				$dest = "$dir/logo02.jpg";

				// copie du fichier temporaire -> répertoire de stockage
				$file = @$_FILES["UploadedFile2"]["tmp_name"];
				if ( $file )
					move_uploaded_file($file, $dest);
				else
					copy("download/logos/default/logo02.jpg", $dest);
				}
		}

	return 0;
}
//---------------------------------------------------------------------------
?>