<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : sqltools.php
 *		projet   : utilitaires pour les appels sql
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 17/03/02
 *		modif    :
 */

//---------------------------------------------------------------------------
function sql_error_value($link)
{
	/*
	 * fonction :	retourne les erreurs survenues lors d'une requête SQL
	 * in :		lien de connexion au serveur mysql
	 * out :		rien
	 */

	$errno = mysqli_errno($link);
	$error = mysqli_error($link);

	return "<span class=\"small\"><span style=\"color:#FF0000\"><strong>Error $errno</strong></span> : $error.</span><br/>";
}
//---------------------------------------------------------------------------
function sql_error($link)
{
	/*
	 * fonction :	affiche les erreurs survenues lors d'une requête SQL
	 * in :		lien de connexion au serveur mysql
	 * out :		rien
	 */

	print( sql_error_value($link) );
}
//---------------------------------------------------------------------------
function sql_error_and_die($link)
{
	/*
	 * fonction :	affiche les erreurs survenues lors d'une requête SQL et termine le programme
	 * in :		lien de connexion au serveur mysql
	 * out :		rien
	 */

	exit( sql_error($link) );
}
//---------------------------------------------------------------------------
function sql_getunique($table)
{
	/*
	 * fonction :	retourne le n° d'index du 1er élément
	 * in :		nom de la table
	 * out :		n° index si trouvé, -1 si erreur
	 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	$result = mysqli_query($mysql_link, "select * from $table");
	$field  = ( $result ) ? mysql_field_name($result, 0) : "" ;

	$Query  = "select $field from $table ";
	$Query .= "where _lang = '".$_SESSION["lang"]."' ";
	$Query .= "order by $field desc ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	return ( $result ) ? (int) ($row[0] + 1) : -1 ;
}
//---------------------------------------------------------------------------
function connectDatabase($server, $user, $passwd, $database, $servport = 0, $persistent = 0)
{
	/*
	 * fonction :	connexion à la base de données
	 * in :		$server, nom du serveur MySQL
	 * 			$user, nom utilisateur
	 * 			$passwd, mot de passe de l'utilisateur
	 * 			$database, nom de la base de données
	 * 			$servport, n° du port
	 * 			$persistent, connexion persistente
	 * out :		lien de connexion au serveur mysql
	 */

	global	$mysql_link;

	if ( $mysql_link )
		return $mysql_link;

	// connexion au serveur MySql
	$servname   = $server . ($servport ? ":$servport" : "");

	// $mysql_link = ( $persistent )
	// 	? @mysql_connect($servname, $user, $passwd)
	// 	: @mysql_connect($servname, $user, $passwd) ;

	$mysql_link = mysqli_connect($servname, $user, $passwd);

	if ( !$mysql_link )
		sql_error($mysql_link);
	else
		mysqli_set_charset($mysql_link, 'utf8');
		mysqli_query($mysql_link, "SET NAMES UTF8");
		// sélection de la base de données
		if ( !mysqli_select_db($mysql_link, $database) )
			sql_error($mysql_link);
// print("$servname, $user, $passwd, $database");





	return $mysql_link;
}
//---------------------------------------------------------------------------
?>
