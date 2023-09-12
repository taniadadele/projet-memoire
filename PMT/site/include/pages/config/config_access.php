<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : config_access.php
 *		projet   : paramétrage des droits d'accès à l'ENT
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 8/12/07
 *		modif    :
 */


	$IDcentre = ( @$_POST["IDcentre"] )				// Identifiant du centre
		? (int) $_POST["IDcentre"]
		: (int) $_SESSION["CnxCentre"] ;

	$submit   = @$_POST["valid_x"];				// bouton de validation

	//---------------------------------------------------------------------------
	function legend($IDcentre, $IDcat)
	{
		/*
		 * fonction :	affiche les groupes d'utilisateurs par catégorie
		 * in :		IDcentre : id du centre, IDcat : id de la catégorie
		*/

		require "globals.php";

		require "msg/config.php";
		require_once "include/TMessage.php";

		$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/config.php");

		// recherche de la catégorie
		$query  = "select _ident from user_category ";
		$query .= "where _IDcat = '$IDcat' AND _lang = '".$_SESSION["lang"]."' ";
		$query .= "limit 1";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		echo '<fieldset style="width:80%; border:#cccccc solid 1px;"><legend>'.$row[0].'</legend>';

			// affichage des groupes
			$query  = "select _IDgrp, _ident from user_group ";
			$query .= "where _IDcat = '$IDcat' AND _visible = 'O' ";
			$query .= "AND _lang = '".$_SESSION["lang"]."' ";
			$query .= "order by _ident";

			$result = mysqli_query($mysql_link, $query);
			$cat    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $cat ) {
				// recherche de la catégorie
				$query  = "select _dstart, _dend, _hstart, _hend from user_denied ";
				$query .= "where _IDcentre = '$IDcentre' AND _IDgrp = '$cat[0]' ";
				$query .= "limit 1";

				$return = mysqli_query($mysql_link, $query);
				$row    = ( $return ) ? mysqli_fetch_row($return) : 0 ;

				$check = ( mysqli_affected_rows($mysql_link) ) ? "checked=\"checked\"" : "" ;
				$isok  = ( $MAINTENANCE ) ? "disabled=\"disabled\"" : "" ;

				$start = ( $row ) ? $row[0] : $msg->read($CONFIG_DATE) ;
				$end   = ( $row ) ? $row[1] : $msg->read($CONFIG_DATE) ;

				print("
					<label for=\"cb_$cat[0]\"><input type=\"checkbox\" id=\"cb_$cat[0]\" name=\"cb[]\" value=\"$cat[0]\" $check $isok />$cat[1]</label>
					<span style=\"cursor: pointer;\" onclick=\"$('id_$cat[0]')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/max.gif\" title=\"\" alt=\"\" /></span><br/>

					<div id=\"id_$cat[0]\" style=\"display:none;\">
					". $msg->read($CONFIG_FROM) ." <label for=\"dstart_$cat[0]\"><input type=\"text\" id=\"dstart_$cat[0]\" name=\"dstart[]\" size=\"10\" value=\"$start\" style=\"font-size:9px;\" /></label>
					". $msg->read($CONFIG_TO) ." <label for=\"dend_$cat[0]\"><input type=\"text\" id=\"dend_$cat[0]\" name=\"dend[]\" size=\"10\" value=\"$end\" style=\"font-size:9px;\" /></label>
					</div>");

				$cat = remove_magic_quotes(mysqli_fetch_row($result));
			}	// endwhile catégorie

		echo '</fieldset>';
	}
	//---------------------------------------------------------------------------
?>


<?php
	require_once "include/config.php";

	// vérification des autorisations
	admSessionAccess();

	// on réouvre la fenêtre pour lire le nouveau fichier de configuration
	if ( strlen(@$_GET["key"]) AND strlen(@$_GET["value"]) )
		if ( setConfig($_GET["key"], $_GET["value"]) )
			print("<script type=\"text/javascript\"> window.location.replace('index.php?item=$item&cmde=$cmde', '_self'); </script>");

	if ( $submit ) {
		// on vide la table
		mysqli_query($mysql_link, "delete from user_denied where _IDcentre = '$IDcentre'");

		$start = @$_POST["dstart"];				// les dates et heures d'accès
		$end   = @$_POST["dend"];

		$cbox  = @$_POST["cb"];
		for ($i = 0; $i < count($cbox); $i++)
			mysqli_query($mysql_link, "insert into user_denied values('$IDcentre', '".@$cbox[$i]."', '".@$start[$i]."', '".@$end[$i]."', '', '')");
	}
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($CONFIG_SCREENING)); ?>
	</div>
</div>

<div class="maincontent">

	<form id="formulaire" action="index.php?item=21&amp;cmde=access" method="post">

		<table class="width100" <?php if (!getParam('showCenter')) echo 'style="display: none;"'; ?>>
			<tr>
				<td style="width:50%;"  class="align-right">
					<?php print($msg->read($CONFIG_CHOOSECENTER)); ?>
				</td>
				<td style="width:50%;">
					<label for="IDcentre">
						<select id="IDcentre" name="IDcentre" onchange="document.forms.formulaire.submit()">
							<?php
								// lecture des centres constitutifs
								$query  = "select _IDcentre, _ident from config_centre ";
								$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
								$query .= "order by _IDcentre";

								$result = mysqli_query($mysql_link, $query);
								$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

								while ( $row ) {
									printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($IDcentre == $row[0]) ? "selected=\"selected\"" : "");

									$row = remove_magic_quotes(mysqli_fetch_row($result));
								}
							?>
						</select>
					</label>
				</td>
			</tr>
		</table>

		<hr>

		<table class="width100">
			<tr>
				<td style="width:20%;" class="align-right">
					<?php print($msg->read($CONFIG_MAINTENANCE)); ?>
				</td>
				<td>
					<?php
						$check = ( $MAINTENANCE ) ? "on" : "off" ;
						$value = ( $MAINTENANCE ) ? 0 : 1 ;

						$link  = ( !$DEMO )
							? "<a href=\"".myurlencode("index.php?item=$item&cmde=$cmde&key=MAINTENANCE&value=$value")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/checkbox_".$check.".gif\" title=\"$check\" alt=\"$check\" /></a>"
							: "<img src=\"".$_SESSION["ROOTDIR"]."/images/checkbox_".$check.".gif\" title=\"$check\" alt=\"\" />" ;

						print($link);
					?>
				</td>
			</tr>

			<tr>
				<td class="align-right">
					<?php print($msg->read($CONFIG_DENIED)); ?>
				</td>
				<td>
					<?php
						// lecture des catégories d'utilisateur
						$query  = "select _IDcat from user_category ";
						$query .= "where _lang = '".$_SESSION["lang"]."' ";
						$query .= "order by _IDcat";

						$result = mysqli_query($mysql_link, $query);
						$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

						while ( $row ) {
							legend($IDcentre, $row[0]);
							$row = mysqli_fetch_row($result);
						}
					?>
				</td>
			</tr>
		</table>
		<hr>
		<?php showValidateBackButtons(); ?>

	</form>

</div>
