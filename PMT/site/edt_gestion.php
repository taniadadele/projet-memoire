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
 *		module   : edt_gestion.php
 *		projet   : la page de gestion des emplois du temps
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 7/10/06
 *		modif    : 
 */


$IDedt   = ( @$_POST["IDedt"] )		// ID de l'edt
	? (int) $_POST["IDedt"]
	: (int) @$_GET["IDedt"] ;

$IDmod   = (int) @$_POST["IDmod"];		// ID du modérateur
$cbwr    = @$_POST["cbwr"];
$cbrd    = @$_POST["cbrd"];
$idweek  = @$_POST["idweek"];
$ttable  = @$_POST["ttable"];
$week    = @$_POST["week"];
$visible = ( @$_POST["visible"] ) ? "N" : "O" ;

$submit  = @$_POST["valid_x"];		// bouton de validation
?>


<?php
	// vérification des autorisations
	admSessionAccess();

	// l'utilisateur a validé la saisie
	if ( $submit ) {
		$status = $msg->read($EDT_MODIFICATION) ." ";

		// les semaines
		for ($i = 0; $i < count($week); $i++)
			if ( @$week[$i] ) {
				$j = $i + 1;
				mysqli_query($mysql_link, "update edt_week set _ident = '$week[$i]' where _IDweek = '$j'");
				}

		// droits des rédacteurs et des lecteurs
		$grpwr = $grprd = 0;
		for ($i = 0; $i < count($cbwr); $i++)
			$grpwr += ( @$cbwr[$i] ) ? $cbwr[$i] : 0 ;
		for ($i = 0; $i < count($cbrd); $i++)
			$grprd += ( @$cbrd[$i] ) ? $cbrd[$i] : 0 ;

		// jours ouvrés
		$week = 0;
		for ($i = 0; $i < count($idweek); $i++)
			$week += ( @$idweek[$i] ) ? $idweek[$i] : 0 ;

		// horaires
		$horaire = "";
		for ($i = 0; $i < count($ttable); $i++)
			$horaire .= ( @$ttable[$i] ) ? "$ttable[$i];" : "" ;

		// modification de l'edt
		$Query  = "UPDATE edt ";
		$Query .= "SET _IDmod = '$IDmod', _IDgrpwr = '$grpwr', _IDgrprd = '$grprd', _IDweek = '$week', _horaire = '$horaire', _visible = '$visible' ";
		$Query .= "where _IDedt = '$IDedt' ";

		if ( !mysqli_query($mysql_link, $Query) ) {
			$status .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />";
			sql_error($mysql_link);
			}
		else
			$status .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
		}
	// initialisation
	else
		$status = "-";

	// recherche de la réservation
	$Query  = "select _IDmod, _titre, _IDgrpwr, _IDgrprd, _IDweek, _horaire, _visible from edt ";
	$Query .= "where _lang = '".$_SESSION["lang"]."' ";
	$Query .= ( $IDedt ) ? "AND _IDedt = '$IDedt' " : "order by _IDedt " ;
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	// initialisation
	$IDmod  = $row[0];
	$title  = $row[1];
	$grpwr  = $row[2];
	$grprd  = $row[3];
	$week   = $row[4];
	$ttable = explode(";", $row[5]);
	$check  = ( $row[6] == "N" ) ? "checked=\"checked\"" : "" ;
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($EDT_MANAGEMENT)); ?>
	</div>
</div>

<div class="maincontent">

	<p><?php print($msg->read($EDT_STATUS) . " $status"); ?></p>

	<form id="formulaire" action="index.php" method="post">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\" value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\" value=\"$cmde\" /></p>
			");
	?>

	<div style="border:#cccccc solid 1px; padding:4px;">

		  <table class="width100">
		    <tr style="background-color:#eeeeee;">
		      <td style="width:33%;">
				<?php print($msg->read($EDT_TIMETABLE)); ?>
			</td>
		      <td style="width:33%;">
				<?php print($msg->read($EDT_CLOSETT)); ?>
			</td>
		      <td style="width:33%;"></td>
		    </tr>

		    <tr>
		      <td>
				<label for="IDedt">
				<select id="IDedt" name="IDedt" onchange="document.forms.formulaire.submit()">
			      <?php
					// recherche des edt
					$query  = "select _IDedt, _titre from edt ";
					$query .= "where _lang = '".$_SESSION["lang"]."' ";
					$query .= "order by _IDedt asc";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {
						$select = ( $IDedt == $row[0] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"$row[0]\" $select>$row[1]</option>");

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
		      	?>
				</select>
				</label>
		      </td>
		      <td>
             		<label for="visible"><input type="checkbox" id="visible" name="visible" value="O" <?php print("$check"); ?> /></label>
		      </td>
		      <td></td>
		    </tr>

		    <tr>
		      <td style="height:15px;" colspan="3"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($EDT_MODO)); ?> *</td>
		      <td><?php print($msg->read($EDT_WRITER)); ?></td>
		      <td><?php print($msg->read($EDT_READER)); ?></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
				<label for="IDmod">
				<select id="IDmod" name="IDmod">
					<option value="0"><?php print($msg->read($EDT_NONE)); ?></option>
					<?php
						// recherche des modérateurs
						$result = mysqli_query($mysql_link, "select _ID, _name, _fname from user_id where _adm & 4 order by _name asc");
						$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

						while ( $row ) {
							$select = ( $IDmod == $row[0] ) ? "selected=\"selected\"" : "" ;

							print("<option value=\"$row[0]\" $select>".formatUserName($row[1], $row[2])."</option>");

							$row = remove_magic_quotes(mysqli_fetch_row($result));
							}
					?>
				</select>
				</label>
		      </td>
		      <td class="valign-top">
	      		<?php
					// recherche des groupes
					$query  = "select _IDgrp, _ident from user_group ";
					$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "order by _IDgrp asc";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {
						$check = ( $grpwr & pow(2, $row[0] - 1) ) ? "checked=\"checked\"" : "" ;

	             			print("<label for=\"cbwr_$row[0]\"><input type=\"checkbox\" id=\"cbwr_$row[0]\" name=\"cbwr[]\" value=\"". pow(2, $row[0] - 1) ."\" $check /> $row[1]</label><br/>");

						$row   = remove_magic_quotes(mysqli_fetch_row($result));
						}
             		?>
		      </td>
		      <td class="valign-top">
	      		<?php
					// recherche des groupes
					mysqli_data_seek($result, 0);
					$row = remove_magic_quotes(mysqli_fetch_row($result));

					while ( $row ) {
						$check = ( $grprd & pow(2, $row[0] - 1) ) ? "checked=\"checked\"" : "" ;

	             			print("<label for=\"cbrd_$row[0]\"><input type=\"checkbox\" id=\"cbrd_$row[0]\" name=\"cbrd[]\" value=\"". pow(2, $row[0] - 1) ."\" $check /> $row[1]</label><br/>");

						$row   = remove_magic_quotes(mysqli_fetch_row($result));
						}
             		?>
		      </td>
		    </tr>

		    <tr>
		      <td style="height:15px;" colspan="3"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($EDT_OPENDAYS)); ?></td>
		      <td><?php print($msg->read($EDT_HOURS)); ?></td>
		      <td><?php print($msg->read($EDT_WEEKS)); ?></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
	      		<?php
					// initialisation
					$day = explode(",", $msg->read($EDT_DAYS)); 

					for ($i = 0; $i < count($day); $i++) {
						$check = ( $week & pow(2, $i) ) ? "checked=\"checked\"" : "" ;

	             			print("<label for=\"idweek_$i\"><input type=\"checkbox\" id=\"idweek_$i\" name=\"idweek[]\" value=\"". pow(2, $i) ."\" $check /> $day[$i]</label><br/>");
						}
             		?>
		      </td>
		      <td class="valign-top">
	      		<?php
					for ($i = 0; $i < count($ttable); $i++) {
						print("<label for=\"ttable_$i\"><input type=\"text\" id=\"ttable_$i\" name=\"ttable[]\" size=\"6\" value=\"$ttable[$i]\" /></label>");

						if ( $i % 2 )
							print("<br/>");
						}
             		?>
		      </td>
		      <td class="valign-top">
	      		<?php
					// recherche de la semaine
					$Query  = "select _IDweek, _ident from edt_week ";
					$Query .= "where _lang = '".$_SESSION["lang"]."' ";
					$Query .= "order by _IDweek" ;

					$result = mysqli_query($mysql_link, $Query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {
						print("<label for=\"week_$row[0]\"><input type=\"text\" id=\"week_$row[0]\" name=\"week[]\" size=\"4\" value=\"$row[1]\" /></label><br/>");

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
             		?>
		      </td>
		    </tr>
		  </table>

	</div>

	<div class="x-small">* <?php print($msg->read($EDT_DECLARE)); ?></div>

	<hr style="width:80%;" />

         <table class="width100">
           <tr>
              <td style="width:10%;" class="valign-middle align-center">
              	<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($EDT_INPUTOK)."\" />"); ?>
              </td>
              <td class="valign-middle">
              	<?php print($msg->read($EDT_UPDATETT)); ?>
              </td>
           </tr>
           <tr>
              <td class="valign-middle align-center">
              	<a href="index.php"><?php print("<img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($EDT_INPUTCANCEL)."\" />"); ?></a>
              </td>
              <td class="valign-middle">
              	<?php print($msg->read($EDT_QUIT)); ?>
              </td>
           </tr>
         </table>

	</form>

</div>