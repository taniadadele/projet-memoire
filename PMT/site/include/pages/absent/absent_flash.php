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
?>

<?php
/*
 *		module   : absent_flash.php
 *		projet   : affichage bandeau déroulant pour les absences
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 8/01/06
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */

//---------------------------------------------------------------------------
function showAbsent()
{
	/*
	 * fonction :	affichage des absence dans un bandeau
	 */

	require "globals.php";

	require_once "include/calendar_tools.php";

	// chargement de la langue
	require "msg/absent.php";
	require_once "include/TMessage.php";

	$msg   = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/absent.php");

	// lecture des absences de ce jour
	$year  = date("Y");
	$month = date("m");
	$day   = date("d");
	$hour  = date("H");
	$min   = date("i");
	$sec   = date("s");

	$query  = "select _IDitem, _IDabs, _start, _end, _IDgrp from absent_items ";
	$query .= "where _visible = 'O' ";
	$query .= "AND (";
	$query .= "('$year-$month-$day $hour:$min:$sec' between _start AND _end) OR ";
	$query .= "('$year-$month-$day $hour:$min:$sec' < _end AND _delay = '1') ";
	$query .= ") ";
	$query .= "order by _IDitem desc";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if ( $row ) {
		$title  = "";
		$text   = $msg->read($ABSENT_ABSENT);

		// autorisations
		$query  = "select _IDmod, _IDgrprd from absent ";
		$query .= "where _IDgrp = '$row[4]'";

		$res    = mysqli_query($mysql_link, $query);
		$auth   = ( $res ) ? mysqli_fetch_row($res) : 0 ;

		while ( $row AND ($_SESSION["CnxID"] == $auth[0] OR ($auth[1] & pow(2, $_SESSION["CnxGrp"] - 1))) ) {
			// lecture du groupe ou de la personne absente
			$return = ( getAccess($row[4]) == 1 )
				? mysqli_query($mysql_link, "select _ident from campus_classe where _IDclass = '$row[1]' limit 1")
				: mysqli_query($mysql_link, "select _name, _fname, _sexe from user_id where _ID = '$row[1]' limit 1") ;
			$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			switch ( @$myrow[2] ) {
				case "H" : $title = $msg->read($ABSENT_SEXM); break;
				case "F" : $title = $msg->read($ABSENT_SEXF); break;
				default  : $title = "";                       break;
				}

			$name  = ( getAccess($row[4]) == 1 ) ? $myrow[0] : formatUserName($myrow[0], $myrow[1]) ;

			$text .= " <a href=\"index.php?item=63&amp;cmde=visu#$row[0]\">$title $name</a> ";
			$text .= $msg->read($ABSENT_FROMTO, Array(date2longfmt($row[2]), date2longfmt($row[3])));
			$text .= "<strong> :: </strong>";

			$row   = mysqli_fetch_row($result);
			}

		if ( $text != $msg->read($ABSENT_ABSENT) ) {
			// lecture des paramètres de configuration
			$query  = "select config_theme._bgcolor from config_theme, config ";
			$query .= "where config._ident = '".$_SESSION["CfgIdent"]."' ";
			$query .= "AND config._lang = '".$_SESSION["lang"]."' ";
			$query .= "AND config_theme._IDtheme = config._IDtheme";

			$result = mysqli_query($mysql_link, $query);
			$bgcol  = ( $result ) ? mysqli_fetch_row($result) : 0 ;

			global	$TBLSPACING;

			print("
			      <table class=\"width100\">
			        <tr>
			          <td class=\"align-center\" style=\"background-color:$bgcol[0];\">
					<marquee behavior=\"scroll\" direction=\"left\" scrollamount=\"3\" scrolldelay=\"10\" onmouseover=\"this.stop()\" onmouseout=\"this.start()\" class=\"align-center\">
					$text
					</marquee>
			          </td>
			        </tr>
			      </table>

				<p style=\"margin-top: 0px; margin-bottom: $TBLSPACING px;\"></p>
				");
			}
		}
}
//---------------------------------------------------------------------------

//showAbsent();
?>