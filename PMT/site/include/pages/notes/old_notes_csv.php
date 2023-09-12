<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2009 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : notes_csv.php
 *		projet   : la page d'exportation des bulletins de notes
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 13/12/09
 *		modif    : 
 */


$IDcentre = (int) @$_GET["IDcentre"];		// Identifiant du centre
$IDclass  = (int) @$_GET["IDclass"];		// Identifiant de la classe
$IDmat    = (int) @$_GET["IDmat"];			// Identifiant de la matière
$year     = (int) @$_GET["year"];			// année
$period   = (int) @$_GET["period"];			// trimestre
?>


<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<?php require "page_banner.php"; ?>

<body style="background-color:#FFFFFF; margin:5px;">

<?php
	require_once "include/notes.php";

	require "msg/notes.php";
	require_once "include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/notes.php");

	// qui suis-je ?
	$query  = "select distinctrow user_id._IDgrp  ";
	$query .= "from user_id, user_session ";
	$query .= "where user_session._IDsess = '".@$_GET["sid"]."' ";
	$query .= "AND user_id._ID = user_session._ID ";
	$query .= "order by user_session._lastaction ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$auth   = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	// il faut appartenir au personnel
	if ( getAccess($auth[0]) == 2 ) {
		$nbcols = ( $IDmat ) ? 10 : 14 ;

		// lecture des droits
		$Query  = "select _IDgrpwr, _IDgrprd, _period, _IDmod, _decimal, _separator, _email from notes ";
		$Query .= "where _IDcentre = '$IDcentre' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$list   = explode (",", $msg->read($NOTES_PERIODLIST));
		$quater = substr($list[$auth[2]], 0, 1);

		if ( $IDmat ) {
			// recherche de l'entête du tableau
			$Query  = "select _type, _total, _coef, _IDdata from notes_data ";
			$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _period = '$period' ";
			$Query .= "limit 1";

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

			$IDdata = $row[3];

			if ( mysqli_num_rows($result) ) {
				$_type    = explode(";", $row[0]);
				$_total   = explode(";", $row[1]);
				$_coef    = explode(";", $row[2]);
				}

			// affichage des matières
			$query  = "select _titre from campus_data ";
			$query .= "where _IDmat = '$IDmat' AND _lang = '".$_SESSION["lang"]."' ";
			$query .= "limit 1";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			print("$row[0]");

			for ($i = 0; $i < $nbcols; $i++)
				print(";$_type[$i]");

			print("<br/>");
			}

		print("$quater$period - $year");

		if ( $IDmat )
			for ($i = 0; $i < $nbcols; $i++)
				print(";$_total[$i]");
		else {
			// affichage des matières
			$Query  = "select distinctrow campus_data._IDmat, campus_data._titre, notes_data._IDdata ";
			$Query .= "from campus_data, notes_data ";
			$Query .= "where campus_data._lang = '".$_SESSION["lang"]."' ";
			$Query .= "AND notes_data._year = '$year' AND notes_data._IDclass = '$IDclass' AND notes_data._period = '$period' ";
			$Query .= "AND notes_data._IDmat = campus_data._IDmat ";
			$Query .= "order by campus_data._titre asc";

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			$IDdata = $row[2];
			$_idmat = Array();

			$i = 0;
			while ( $row ) {			
				$_idmat[$i++] = $row[0];

				print(";$row[1]");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}	// endwhile matières

			for (; $i < $nbcols; $i++)
				print(";");
			}

		print("<br/>");

		// affichage des élèves
		$query  = "select _name, _fname, _ID, _IDclass ";
		$query .= "from user_id ";
		$query .= "where _visible = 'O' ";
		$query .= "AND _IDclass = '$IDclass' AND _IDgrp = '1' ";
		$query .= "order by _name, _fname";

		$result = mysqli_query($mysql_link, $query);
		$row    = remove_magic_quotes(mysqli_fetch_row($result));

		while ( $row ) {
			print("$row[0] $row[1]");

			for ($i = 0; $i < $nbcols; $i++) {
				if ( $IDmat ) {
					$Query  = "select _ID, _IP, _create, _update, _value from notes_items ";
					$Query .= "where _IDdata = '$IDdata' AND _IDeleve = '$row[2]' AND _index = '$i' ";

					$return = mysqli_query($mysql_link, $Query);
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

					$fmt  = ( $myrow[4] != "" )
						? number_format($myrow[4], $auth[4], $auth[5], ".")
						: "" ;
					}
				else {
					if ( @$_idmat[$i] ) {
						//---- calcul de la note
						$note = computeMark($IDclass, $_idmat[$i], $row[2], $year, $period);
						$fmt  = ( $note != "" ) ? number_format($note, $auth[4], $auth[5], ".") : "" ;
						}
					else
						$fmt  = "";
					}

				print(";$fmt");
				}

			if ( $IDmat ) {
				//---- appréciation
				$Query  = "select _text from notes_text ";
				$Query .= "where _IDeleve = '$row[2]' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _year = '$year' AND _period = '$period' ";
				$Query .= "limit 1";

				$return = mysqli_query($mysql_link, $Query);
				$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

				print(";$myrow[0]");
				}

			print("<br/>");

			$row    = remove_magic_quotes(mysqli_fetch_row($result));
			}
		}
?>

</body>
</html>
