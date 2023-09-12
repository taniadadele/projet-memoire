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
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/


/*
 *		module   : log_csv.php
 *		projet   : la page d'exportation des log de connexion
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 6/11/10
 *		modif    : 
 */
?>


<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<?php require "page_banner.php"; ?>

<body style="background-color:#FFFFFF; margin:5px;">

<?php
	// qui suis-je ?
	$query  = "select distinctrow user_id._adm  ";
	$query .= "from user_id, user_session ";
	$query .= "where user_session._IDsess = '".@$_GET["sid"]."' ";
	$query .= "AND user_id._ID = user_session._ID ";
	$query .= "order by user_session._lastaction ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$auth   = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	// il faut les droits admin
	if ( $auth[0] == 255 ) {
		$query  = "select _date, _ID, _IPv6, _action, _ident from stat_log ";
		$query .= ( @$_GET["id"] ) ? "where _ID = '".$_GET["id"]."' " : "" ;
		$query .= "order by _date desc";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// données CSV
		while ( $row ) {
			switch ( $row[3] ) {
				case 'C' : $action = "Cnx"; break;
				case 'D' : $action = "Dnx"; break;
				case 'X' : $action = "Err"; break;
				default  : $action = "Exp"; break;
				}

			print("$row[0];".getUserNameByID($row[1], false).";$row[2];$action<br/>");

			$row = mysqli_fetch_row($result);
			}
		}
?>

</body>
</html>
