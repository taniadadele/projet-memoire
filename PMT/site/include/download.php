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
 *		module   : download.php
 *		projet   : gestion des logs de téléchargements
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 8/12/02
 *		modif    :
 */


//---------------------------------------------------------------------------
require_once $_SESSION["CFGDIR"]."/config.php";

require_once $_SESSION["ROOTDIR"]."/include/sqltools.php";
require_once $_SESSION["ROOTDIR"]."/include/logintools.php";
//---------------------------------------------------------------------------
function logDownloadFile($downloaded)
{
	/*
	 * fonction :	enregistre l'historique des fichiers téléchargés
	 * in :		$downloaded : fichier à télécharger
	 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		require_once $_SESSION["ROOTDIR"]."/include/urlencode.php";

		// lecture du poste client
		$id     = $_SESSION["CnxID"];
		$cnxip  = $_SESSION["CnxIP"];

		$file   = addslashes(trim(str_replace(Array("%20", "\'"), Array(" ", "'"), myurlencode($downloaded))));
//		$file   = addslashes(trim(myurlencode($downloaded)));

		// lecture du compteur des téléchargements
		$result = mysqli_query($mysql_link, "select _IDdown from download_data where _file = '$file' limit 1");
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// date de dernier téléchargement
		$date   = date("Y-m-d H:i:s");

		// mise à jour du compteur des téléchargements
		if ( $row ) {
			if ( !mysqli_query($mysql_link, "update download_data set _count = _count + 1, _date = '$date' where _IDdown = '$row[0]'") )
				sql_error($mysql_link);
			}
		else {
			if ( !mysqli_query($mysql_link, "insert into download_data values('', '$file', '1', '$date')") )
				sql_error($mysql_link);

			$row[0] = mysqli_insert_id($mysql_link);
			}

		// log des téléchargements par utilisateur
		if ( $row )
			if ( !mysqli_query($mysql_link, "insert into download values('$row[0]', '$id', '$cnxip', '1', '$date')") )
				if ( !mysqli_query($mysql_link, "update download set _count = _count + 1, _IP = '$cnxip', _date = '$date' where _IDdown = '$row[0]' AND _ID = '$id'") )
					sql_error($mysql_link);
		}
}
//---------------------------------------------------------------------------
function logDownloadError($downloaded)
{
	/*
	 * fonction :	enregistre l'historique des fichiers téléchargés
	 * in :		$downloaded : fichier à télécharger
	 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		require_once $_SESSION["ROOTDIR"]."/include/session.php";

		logSessionAccess();
		}
}
//---------------------------------------------------------------------------
function logTmpFile($file)
{
	/*
	 * fonction :	efface les fichiers temporaires
	 * in :		$time : délais avant suppression
	 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		$date = date("Y-m-d H:i:s");

		// log des fichiers temporaires
		if ( !mysqli_query($mysql_link, "insert into download_tmp values('', '$file', '$date')") )
			mysqli_query($mysql_link, "update download_tmp set _date = '$date' where _file = '$file'");
		}
}
//---------------------------------------------------------------------------
function deleteTmpFile($time)
{
	/*
	 * fonction :	efface les fichiers temporaires
	 * in :		$time : délais avant suppression
	 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	// connexion à la base de données
	$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

	if ( $mysql_link ) {
		$date = date("Y-m-d H:i:s", time() - $time);

		// compte utilisateur
		$query  = "select _IDfile, _file from download_tmp ";
		$query .= "where _date < '$date' ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		while ( $row ) {
			if ( @unlink($row[1]) )
				mysqli_query($mysql_link, "delete from download_tmp where _IDfile = '$row[0]' limit 1");

			$row = mysqli_fetch_row($result);
			}
		}
}
//---------------------------------------------------------------------------
?>
