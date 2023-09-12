<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : edt.php
 *		projet   : page de saisie des emplois du temps
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 7/10/06
 *		modif    : 
 */


$sid      = @$_GET["sid"];			// session de l'utilisateur
$IDedt    = (int) @$_GET["IDedt"];		// ID du type d'edt
$IDcentre = (int) @$_GET["IDcentre"];	// ID du centre
$IDdata   = (int) @$_GET["IDdata"];		// ID de l'emploi du temps
$j        = @$_GET["j"];			// jour
$h        = @$_GET["h"];			// heure

$IDitem   = ( @$_POST["IDitem"] )		// ID de la salle
	? (int) $_POST["IDitem"]
	: (int) @$_GET["IDitem"] ;
$IDclass  = ( @$_POST["IDclass"] )		// ID de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$IDuser   = ( @$_POST["IDuser"] )		// ID de l'utilisateur
	? (int) $_POST["IDuser"]
	: (int) @$_GET["IDuser"] ;
$IDmat    = (int) @$_POST["IDmat"];		// ID de la matière
$idweek   = (int) @$_POST["idweek"];	// type de semaine
$idgroup  = (int) @$_POST["idgroup"];	// groupe classe
$delay    = @$_POST["delay"];			// durée du cours

$submit   = @$_POST["valid_x"];		// bouton de validation
//$url   = $_SERVER['SERVER_NAME'] .$_SERVER ['REQUEST_URI'];		// bouton de validation

$_SESSION["CnxCentre"] = $IDcentre;
?>


<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<?php require "page_banner.php"; ?>

<body style="background-color:#FFFFFF; margin:5px;">

<?php
	require "msg/edt.php";
	require_once "include/TMessage.php";

	// qui suis-je ?
	$query  = "select distinctrow _adm, _IDgrp from user_id, user_session ";
	$query .= "where _IDsess = '$sid' " ;
	$query .= "AND user_id._ID = user_session._ID ";
	$query .= "order by _lastaction ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// vérification des droits
	$query   = "select _IDmod, _IDgrpwr from edt ";
	$query  .= "where _IDedt = '$IDedt' " ;
	$query  .= "limit 1";

	$result  = mysqli_query($mysql_link, $query);
	$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if ( $auth[0] != 255 AND $auth[0] != $row[0] AND !($row[1] & pow(2, $auth[1] - 1)) )
		exit;

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/edt.php");
	$msg->msg_search  = $keywords_search;
	$msg->msg_replace = $keywords_replace;

	// traitement commande
	if ( $submit ) {
		$query = ( $IDdata )
			? "update edt_data set _IDmat = '$IDmat', _IDclass = '$IDclass', _IDitem = '$IDitem', _ID = '$IDuser', _semaine = '$idweek', _group = '$idgroup', _delais = '$delay' where _IDdata = '$IDdata'"
			: "insert into edt_data values('', '$IDedt', '$IDcentre', '$IDmat', '$IDclass', '$IDitem', '$IDuser', '$idweek', '$idgroup', '$j', '$h', '$delay', 'O')" ;

		mysqli_query($mysql_link, $query);

		// retour à la fénêtre appelante
		print("
			<script type=\"text/javascript\">
			window.opener.location=\"index.php?item=".@$_GET["item"]."&IDedt=$IDedt&IDcentre=$IDcentre&IDitem=$IDitem&IDuser=$IDuser&IDclass=$IDclass\";
			self.close();
			</script>");
		}

	if ( $IDdata ) {
		$query   = "select _IDmat, _IDclass, _delais, _semaine, _IDitem, _group, _ID from edt_data ";
		$query  .= "where _IDdata = '$IDdata' " ;
		$query  .= "limit 1";

		$result  = mysqli_query($mysql_link, $query);
		$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$IDmat   = (int) $row[0];
		$IDclass = (int) $row[1];
		$delay   = $row[2];
		$idweek  = (int) $row[3];
		$IDitem  = (int) $row[4];
		$idgroup = (int) $row[5];
		$IDuser  = (int) $row[6];
		}
?>

<table class="width100" style="border: 1px solid black">
  <tr>
     <td class="align-center" style="width:20%;background-color:<?php print($_SESSION["CfgColor"]); ?>">
		<span style="color:#FFFFFF;"><?php print($msg->read($EDT_UPDATE)); ?></span>
     </td>
  </tr>

  <tr>
     <td>

	<form id="formulaire" action="" method="get">

	<table class="width100">
        <tr>
          <td style="width:30%;" class="align-right valign-middle">
		<?php print($msg->read($EDT_MATTER)); ?>
	    </td>
	    <td class="valign-middle">
		<label for="IDmat">
		<select id="IDmat" name="IDmat">
		<?php
			// recherche des matières enseignées
			$query  = "select _IDmat from user_id ";
			$query .= "where _ID = '$IDuser' ";
			$query .= "limit 1";

			$result = mysqli_query($mysql_link, $query);
			$myrow  = ( $result ) ? mysqli_fetch_row($result) : 0 ;

			$list   = explode(" ", trim($myrow[0]));

			// affichage des matières
			$query  = "select _IDmat, _titre from campus_data ";
			$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
			$query .= "order by _titre";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row ) {
				$select = ( $IDmat == $row[0] ) ? "selected=\"selected\"" : "" ;

				if ( !strlen(trim($myrow[0])) OR in_array($row[0], $list) )
					print("<option value=\"$row[0]\" $select>$row[1]</option>");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}				
		?>
		</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/donneravis.gif" title="" alt="" />
		</label>
	    </td>
        </tr>

<?php
	if ( $IDedt != 3 ) {
		print("
	        <tr>
	          <td class=\"align-right valign-middle\">
			". $msg->read($EDT_CLASS) ."
		    </td>
		    <td class= \"valign-middle\">
			<label for=\"IDclass\">
			<select id=\"IDclass\" name=\"IDclass\">
			");

			$query  = "select _IDclass, _ident from campus_classe ";
			$query .= "where _visible = 'O' ";
			$query .= "order by _text";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row ) {			
				if ( $IDclass == $row[0] )
					print("<option selected=\"selected\" value=\"$row[0]\">$row[1]</option>");
				else
					print("<option value=\"$row[0]\">$row[1]</option>");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}				

		print("
			</select> <img src=\"".$_SESSION["ROOTDIR"]."/images/campus.png\" title=\"\" alt=\"\" />
			</label>
		    </td>
	        </tr>
			");
		}

	if ( $IDedt != 2 ) {
		print("
	        <tr>
	          <td class=\"align-right valign-middle\">
			". $msg->read($EDT_TEACHER) ."
		    </td>
		    <td class= \"valign-middle\">
			<label for=\"IDuser\">
			<select id=\"IDuser\" name=\"IDuser\">
				<option value=\"0\">&nbsp;</option>
			");

			$query  = "select distinctrow _ID, _name, _fname ";
			$query .= "from user_id, user_group ";
			$query .= "where user_id._adm AND user_id._IDcentre = '$IDcentre' ";
			$query .= "AND user_group._IDcat = '2' ";
			$query .= "AND user_id._IDgrp = user_group._IDgrp ";
			$query .= "order by _name";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row ) {			
				$select = ( $IDuser == $row[0] ) ? "selected=\"selected\"" : "" ;

				print("<option value=\"$row[0]\" $select>$row[1] $row[2]</option>");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
			
		print("
			</select> <img src=\"".$_SESSION["ROOTDIR"]."/images/whosonline.gif\" title=\"\" alt=\"\" />
			</label>
		    </td>
	        </tr>
			");
		 }

	if ( $IDedt != 1 ) {
		print("
	        <tr>
	          <td class=\"align-right valign-middle\">
			". $msg->read($EDT_ROOM) ."
		    </td>
		    <td class= \"valign-middle\">
			<label for=\"IDitem\">
			<select id=\"IDitem\" name=\"IDitem\">
				<option value=\"0\">&nbsp;</option>
			");

			$query  = "select _IDitem, _title from edt_items ";
			$query .= "where _IDcentre = '$IDcentre' ";
			$query .= "AND _lang = '".$_SESSION["lang"]."' ";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row ) {			
				$select = ( $IDitem == $row[0] ) ? "selected=\"selected\"" : "" ;

				print("<option value=\"$row[0]\" $select>$row[1]</option>");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
			
		print("
			</select> <img src=\"".$_SESSION["ROOTDIR"]."/images/home.gif\" title=\"\" alt=\"\" />
			</label>
		    </td>
	        </tr>
			");
		}
?>

        <tr>
          <td class="align-right valign-middle">
		<?php print($msg->read($EDT_DELAY)); ?>
	    </td>
	    <td class="valign-middle">
		<label for="delay">
		<select id="delay" name="delay">
		<?php
			for ($i = 1; $i < 5; $i++) {
				$duree = "0$i:00";		

				$select = ( $delay == $duree.":00" ) ? "selected=\"selected\"" : "" ;

				print("<option value=\"$duree\" $select>$duree</option>");

				$duree = "0$i:30";		

				$select = ( $delay == $duree.":30" ) ? "selected=\"selected\"" : "" ;

				print("<option value=\"$duree\" $select>$duree</option>");
				}				
		?>
		</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/horloge.png" title="" alt="" />
		</label>
	    </td>
        </tr>

        <tr>
          <td class="align-right valign-middle">
		<?php print($msg->read($EDT_WEEK)); ?>
	    </td>
	    <td class="valign-middle">
		<label for="idweek">
		<select id="idweek" name="idweek">
			<option value="0">&nbsp;</option>
		<?php
			// recherche de la semaine
			$Query  = "select _IDweek, _ident from edt_week ";
			$Query .= "where _lang = '".$_SESSION["lang"]."' ";
			$Query .= "order by _IDweek" ;

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row ) {
				if ( $idweek == $row[0] )
					print("<option selected=\"selected\" value=\"$row[0]\">$row[1]</option>");
				else
					print("<option value=\"$row[0]\">$row[1]</option>");

				$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
		?>
		</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/agenda.gif" title="" alt="" />
		</label>
	    </td>
        </tr>

        <tr>
          <td class="align-right valign-middle">
		<?php print($msg->read($EDT_GROUP)); ?>
	    </td>
	    <td class="valign-middle">
		<label for="idgroup">
		<select id="idgroup" name="idgroup">
			<option value="0">&nbsp;</option>
		<?php
			// recherche du groupe classe
			for ($i = 1; $i < 3; $i++)
				if ( $idgroup == $i )
					print("<option selected=\"selected\" value=\"$i\">$i</option>");
				else
					print("<option value=\"$i\">$i</option>");
		?>
		</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/group.gif" title="" alt="" />
		</label>
	    </td>
        </tr>

        <tr>
          <td colspan="2"><hr style="width:80%; text-align:center;" /></td>
        </tr>

        <tr>
          <td style="width:10%;" class="valign-middle align-right">
           	<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($EDT_INPUTOK)."\" />"); ?>
          </td>
          <td class="valign-middle"><?php print($msg->read($EDT_VALIDATE)); ?></td>
        </tr>
	</table>

	</form>

     </td>
  </tr>
</table>

<p style="text-align:center;">
[<a href="#" onclick="window.close(); return false;"><?php print($msg->read($EDT_CLOSEWINDOW)); ?></a>]
</p>

</body>
</html>