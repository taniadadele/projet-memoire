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
 *		module   : notes_comment.php
 *		projet   : page de saisie des appréciations des bulletins scolaires
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 12/12/09
 *		modif    :
 */
session_start();
if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
{

  $sid      = @$_GET["sid"];					// session de l'utilisateur

  $IDcentre = (int) @$_GET["IDcentre"];			// Identifiant du centre
  $IDeleve  = (int) @$_GET["IDeleve"];			// ID élève
  $IDclass  = (int) @$_GET["IDclass"];			// ID de la classe
  $IDmat    = (int) $_GET["IDmat"];				// ID de la matière
  $year     = (int) $_GET["year"];				// année période
  $period   = (int) $_GET["period"];				//

  $note     = addslashes(trim(@$_POST["note"]));		// appréciation

  $submit   = @$_POST["valid_x"];				// bouton de validation
  $action   = @$_GET["action"];

  $_SESSION["CnxCentre"] = $IDcentre;
  ?>


  <!DOCTYPE html
       PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php
error_reporting(E_ALL);
   ini_set("display_errors", 1);
   ?>
  <?php require "../../fonction/auth_tools.php"; ?>
  <?php require "../../../page_banner.php"; ?>

  <body style="background-color:#FFFFFF; margin:5px;">

  <?php
  	require "../../../msg/notes.php";
  	require_once "../../../include/TMessage.php";

  	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/notes.php");
  	$msg->msg_search  = $keywords_search;
  	$msg->msg_replace = $keywords_replace;

  	$statut = $msg->read($NOTES_MODIFICATION) ;

  	// Suppression
  	if($action == "delete")
  	{
  		$mydate = date("Y-m-d H:i:s");

  		$query  = "insert into notes_text ";
  		$query .= "values('$IDeleve', '$auth[2]', '$auth[3]', '$mydate', '$IDclass', '$IDmat', '$year', '$period', '', 'N')";

  		if ( !mysqli_query($mysql_link, $query) )
  		{
  			// modification du bulletin
  			$query  = "UPDATE notes_text ";
  			$query .= "SET _ID = '$auth[2]', _IP = '$auth[3]', _date = '$mydate', _text = '' ";
  			$query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _year = '$year' AND _period = '$period' ";
  			$query .= "limit 1";

  			if ( mysqli_query($mysql_link, $query) ) $statut .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
  			else $statut .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />";
  		}
  		else
  		{
  			$statut .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
  		}

  		// maj de la fénêtre appelante
  		print("
  			<script type=\"text/javascript\">
  			window.opener.location=\"index.php?item=".@$_GET["item"]."&cmde=".@$_GET["cmde"]."&IDcentre=$IDcentre&IDclass=$IDclass&IDmat=$IDmat&year=$year&period=$period\";
  			self.close();
  			</script>");
  	}

  	if ( $submit ) {
  		// recherche gestion des bulletins
  		$query  = "select _IDmod, _IDgrpwr from notes ";
  		$query .= "where _IDcentre = '$IDcentre' ";
  		$query .= "limit 1";

  		$result = mysqli_query($mysql_link, $query);
  		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

  		// qui suis-je ?
  		$query  = "select distinctrow _adm, _IDgrp, user_session._ID, user_session._IP ";
  		$query .= "from user_id, user_session ";
  		$query .= "where _IDsess = '$sid' ";
  		$query .= "AND user_id._ID = user_session._ID ";
  		$query .= "order by _lastaction ";
  		$query .= "limit 1";

  		$result = mysqli_query($mysql_link, $query);
  		$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;


  		if ( $auth[0] == 255 OR $auth[0] == $row[0] OR ($row[1] & pow(2, $auth[1] - 1)) ) {
  			$mydate = date("Y-m-d H:i:s");

  			$Query  = "insert into notes_text ";
  			$Query .= "values('$IDeleve', '$auth[2]', '$auth[3]', '$mydate', '$IDclass', '$IDmat', '$year', '$period', '$note', 'N')";

  			if ( !mysqli_query($mysql_link, $Query) ) {
  				// modification du bulletin
  				$Query  = "UPDATE notes_text ";
  				$Query .= "SET _ID = '$auth[2]', _IP = '$auth[3]', _date = '$mydate', _text = '$note' ";
  				$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _year = '$year' AND _period = '$period' ";
  				$Query .= "limit 1";

  				if ( mysqli_query($mysql_link, $Query) )
  					$statut .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
  				else
  					$statut .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />";
  				}
  			else
  				$statut .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
  			}

  		// maj de la fénêtre appelante
  		print("
  			<script type=\"text/javascript\">
  			window.opener.location=\"index.php?item=".@$_GET["item"]."&cmde=".@$_GET["cmde"]."&IDcentre=$IDcentre&IDclass=$IDclass&IDmat=$IDmat&year=$year&period=$period\";
  			self.close();
  			</script>");
  		}

  	// initialisation
  	$Query  = "select _text from notes_text ";
  	$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _year = '$year' AND _period = '$period' ";
  	$Query .= "limit 1";

  	$result = mysqli_query($mysql_link, $Query);
  	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

  	$note = ( mysqli_num_rows($result) )
  		? $row[0]
  		: "" ;
  ?>

  <table class="width100" style="border: 1px solid black">
    <tr>
       <td class="align-center" style="width:20%;background-color:<?php print($_SESSION["CfgColor"]); ?>">
  		<span style="color:#FFFFFF;"><?php print($msg->read($NOTES_COMMENT)); ?></span>
       </td>
    </tr>

    <tr>
       <td>

  	<form id="formulaire" action="" method="post">

  	<table class="width100">
          <tr>
            <td style="width:25%;" class="align-right valign-middle">
  		<?php print($msg->read($NOTES_STATUS)); ?>
  	    </td>
  	    <td class="valign-middle">
  		<?php print($statut); ?>
  	    </td>
          </tr>

          <tr>
            <td colspan="2" class="align-center"><hr style="width:80%;" /></td>
          </tr>

          <tr>
            <td class="align-right valign-middle">
  		<?php print($msg->read($NOTES_TEXTAREA)); ?>
  	    </td>
  	    <td class="valign-middle">
  		<label for="note"><textarea id="note" name="note" rows="6" cols="30"><?php print($note); ?></textarea></label>
  	    </td>
          </tr>

          <tr>
            <td colspan="2" class="align-center"><hr style="width:80%;" /></td>
          </tr>

          <tr>
            <td class="valign-middle align-right">
             	<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($NOTES_INPUTOK)."\" />"); ?>
            </td>
            <td class="valign-middle"><?php print($msg->read($NOTES_SENDPOST)); ?></td>
          </tr>
  	</table>

  	</form>

       </td>
    </tr>
  </table>

  <p style="text-align:center;">
  [<a href="#" onclick="window.close(); return false;"><?php print($msg->read($NOTES_CLOSEWINDOW)); ?></a>]
  </p>

  </body>
  </html>

<?php
}
?>
