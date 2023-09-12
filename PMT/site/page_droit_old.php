<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : page_droit.php
 *		projet   : le menu de droite
 *
 *		version  : 1.4
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 12/12/03 - par D. Laporte
 *			     fix du bug d'affichage des questions du sondage
 *
 *			     20/03/03 - par D. Laporte
 *			     menu d'affichage des logs de connexion
 *
 *			     30/05/03 - par D. Laporte
 *			     accès au egroup Prométhée
 *
 *			     28/12/03 - par D. Laporte
 *			     calendrier
 *
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */
 ?>

<?php if (1 == 0) { ?>

<!-- chatroom -->
<?php
	// si le chat n'existe pas on le crée
	$Query  = "select _visible, _IDgrprd, _start, _end from chat ";
	$Query .= "where _title = 'Prométhée' " ;
	$Query .= "AND _lang = '".$_SESSION["lang"]."' limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if ( !$row ) {
		$query  = "insert into chat ";
		$query .= "values('', '0', '255', '255', '', '', 'Prométhée', '1', '15', '10', 'O', '".$_SESSION["lang"]."')";

		if ( mysqli_query($mysql_link, $query) ) {
			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
			}
		}

	if ( $row[0] == "O" AND ($row[1] & pow(2, $_SESSION["CnxGrp"] - 1)) ) {
		$time = date("H:i:s");

		if ( ($row[2] == "00:00:00" OR $time > $row[2]) AND ($row[3] == "00:00:00" OR $time < $row[3]) ) {
			if ( strcmp(phpversion(), "4.3") >= 0 OR ini_get("output_buffering") != "0" ) {
				require "msg/chat.php";
				require_once "include/TMessage.php";

				$dialmsg = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/chat.php");

				//require "chat_room.php";
				}

			print("<p style=\"margin-top:0px; margin-bottom:".$TBLSPACING."px;\"></p>");
			}
		}
?>

<?php } ?>
