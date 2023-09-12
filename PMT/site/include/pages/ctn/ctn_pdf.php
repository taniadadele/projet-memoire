<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2010 by Jérémy CORNILLEAU (jeremy.cornilleau@gmail.com)
   Copyright (c) 2010 by Alexandre MAHE ()

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
 *		module   : ctn_pdf.php
 *		projet   : la page d'impression des cahier de texte
 *
 *		version  : 0.1
 *		auteurs  : Alexandre MAHE - Jérémy CORNILLEAU
 *		creation : 12/05/10
 *		modif    : 
 */


 
$IDgroup  = (int) @$_GET["IDgroup"];			// Identifiant du e-groupe
$IDcentre = (int) @$_GET["IDcentre"];		// Identifiant du centre
$IDclass  = (int) @$_GET["IDclass"];			// Identifiant de la classe
$IDmat    = (int) @$_GET["IDmat"];				// Identifiant de la matière
$sid      = @$_GET["sid"];						// sid

?>


<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<?php require "page_banner.php"; ?>

<body style="background-color:#FFFFFF; margin:5px;">

<?php
	require_once "include/ctn.php";

	require_once "lib/fpdf.php";

	require "msg/ctn.php";
	require_once "include/TMessage.php";
	require_once "include/session.php";
	require_once "include/calendar_tools.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/ctn.php");
	
		// qui suis-je ?
	$query  = "select distinctrow user_id._ID, user_id._IDgrp ";
	$query .= "from user_id, user_session ";
	$query .= "where user_session._IDsess = '".$sid."' ";
	$query .= "AND user_id._ID = user_session._ID ";
	$query .= "order by user_session._lastaction ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$auth   = ( $return ) ? mysqli_fetch_row($return) : 0 ;
	
	if(empty($_GET["date"])){
		// il faut que la date soit renseigné avant l'export
		if(!empty($_GET["date_e"]) && !empty($_GET["date_b"])){

			$date_begin = @$_GET["date_e"]." 00:00:00";			// Date de début
			$date_end = @$_GET["date_b"]." 00:00:00";			// Date de fin
			
			// il faut avoir les droits de lecture
			if ( getAccess($auth[1]) == 2 )
				exportPDF($IDgroup, $IDcentre, $IDclass, $IDmat, $auth[0], $date_begin, $date_end);
			else
				die($msg->read($CTN_LIMITED)); 
		}else{
		
			
			if(empty($_GET["date_b"])){
				$date_begin =  NULL;
			}else{
				$date_begin =  @$_GET["date_e"];
			}
			
			if(empty($_GET["date_e"])){
				$date_end =  NULL;
			}else{
				$date_end =  @$_GET["date_e"];
			}

			// formulaire pour choisir la date
			print("
			<div style=\"text-align:center;\">
			<form id=\"formulaire\" name=\"formulaire\" action=\"ctn_pdf.php\" method=\"get\" enctype=\"multipart/form-data\">
				<p class=\"hidden\"><input type=\"hidden\" name=\"IDgroup\"  value=\"$IDgroup\" /></p>
				<p class=\"hidden\"><input type=\"hidden\" name=\"IDcentre\"  value=\"$IDcentre\" /></p>
				<p class=\"hidden\"><input type=\"hidden\" name=\"IDclass\"  value=\"$IDclass\" /></p>
				<p class=\"hidden\"><input type=\"hidden\" name=\"IDmat\"  value=\"$IDmat\" /></p>
				<p class=\"hidden\"><input type=\"hidden\" name=\"sid\" value=\"$sid\"></p>");

			print(
				$msg->read($CTN_MYDATE) ." ". $msg->read($CTN_FROM) ."
				<label for=\"date_b\"><input type=\"text\" id=\"date_b\" name=\"date_b\" size=\"10\" value=\"$date_begin\" /></label>");

			// calendrier surgissant
			CalendarPopup("id1", "document.formulaire.date_b");

			print(
				$msg->read($CTN_TO) ."
				<label for=\"date_e\"><input type=\"text\" id=\"date_e\" name=\"date_e\" size=\"10\" value=\"$date_end\" /></label>");

			CalendarPopup("id1", "document.formulaire.date_e");

			print("
				<hr style=\"width:80%;\" />
					<input type=\"submit\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($CTN_INPUTOK)."\" />
				</form>
				
			</div>");
		}
	}else{
		// il faut avoir les droits de lecture
		if( getAccess($auth[1]) == 2 )
			exportPDF($IDgroup, $IDcentre, $IDclass, $IDmat, $auth[0], $_GET["date"], $_GET["date"]);
		else
			die($msg->read($CTN_LIMITED)); 
	}
?>

</body>
</html>
