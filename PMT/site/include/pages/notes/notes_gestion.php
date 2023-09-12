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
 *		module   : notes_gestion.php
 *		projet   : la page de gestion des Bulletins de notes
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 6/12/09
 *		modif    : 
 */


$IDcentre  = ( @$_POST["IDcentre"] ) 		// ID du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;

$IDmod     = (int) @$_POST["IDmod"];
$cbrd      = @$_POST["cbrd"];
$cbwr      = @$_POST["cbwr"];
$period    = (int) @$_POST["period"];
$email     = ( @$_POST["email"] ) ? $_POST["email"] : "-" ;
$text      = @$_POST["text"];
$font      = (int) @$_POST["font"];
$max       = (int) @$_POST["max"];
$sep       = @$_POST["sep"];
$decimal   = (int) @$_POST["decimal"];
$month     = ( strlen(@$_POST["month"]) ) ? (int) @$_POST["month"] : 8 ;
$disperiod = @$_POST["disperiod"];
$disp_C    = @$_POST["disp_C"];
$disp_Y    = @$_POST["disp_Y"];
$disp_T    = @$_POST["disp_T"];
$disp_D    = @$_POST["disp_D"];

$submit    = @$_POST["valid_x"];		// bouton de validation
?>


<?php
	// initialisation
	$status  = "-";

	// vérification des autorisations
	admSessionAccess();

	// l'utilisateur a validé la saisie
	if ( $submit ) {
		$status = $msg->read($NOTES_MODIFICATION) ." ";

		// droits des rédacteurs et des lecteurs
		$grpwr  = $grprd = 0;
		for ($i = 0; $i < count($cbwr); $i++)
			$grpwr += ( @$cbwr[$i] ) ? $cbwr[$i] : 0 ;
		for ($i = 0; $i < count($cbrd); $i++)
			$grprd += ( @$cbrd[$i] ) ? $cbrd[$i] : 0 ;

		if ( !strlen($sep) )
			$sep = ".";

		$display  = $disperiod ? "1" : "0" ;
		$display .= $disp_C ? "1" : "0" ;
		$display .= $disp_Y ? "1" : "0" ;
		$display .= $disp_T ? "1" : "0" ;
		$display .= $disp_D ? "1" : "0" ;

		// nouveau bulletin
		$Query  = "insert into notes ";
		$Query .= "values('$IDcentre', '$IDmod', '$grpwr', '$grprd', '$period', '$month', '$email', '$text', '$decimal', '$sep', '$max', '$display', '$font')";

		if ( !mysqli_query($mysql_link, $Query) ) {
			// modification du bulletin
			$Query  = "UPDATE notes ";
			$Query .= "SET _IDmod = '$IDmod', _IDgrpwr = '$grpwr', _IDgrprd= '$grprd', _period = '$period', _month = '$month', _email = '$email', _text = '$text', _decimal = '$decimal', _separator = '$sep', _max = '$max', _display = '$display', _font = '$font' ";
			$Query .= "where _IDcentre = '$IDcentre' ";
			$Query .= "limit 1";

			if ( !mysqli_query($mysql_link, $Query) ) {
				$status .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />";
				sql_error($mysql_link);
				}
			else
				$status .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
			}
		else
			$status .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
		}

	// recherche du bulletin
	$Query  = "select _IDmod, _IDgrpwr, _IDgrprd, _period, _month, _email, _text, _separator, _decimal, _max, _display, _font ";
	$Query .= "from notes ";
	$Query .= "where _IDcentre = '$IDcentre' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// initialisation des champs de saisie
	$IDmod   = $row[0];
	$grpwr   = $row[1];
	$grprd   = $row[2];
	$period  = $row[3];
	$month   = $row[4];
	$email   = $row[5];
	$text    = ( $row ) ? $row[6] : 20 ;
	$sep     = $row[7];
	$decimal = $row[8];
	$max     = ( $row ) ? $row[9] : 10 ;
	$display = $row[10];
	$font    = ( $row ) ? $row[11] : 10 ;

	$disp_C  = (int) @$display[1];
	$disp_Y  = (int) @$display[2];
	$disp_T  = (int) @$display[3];
	$disp_D  = (int) @$display[4];
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($NOTES_MANAGEMENT)); ?>
	</div>
</div>

<div class="maincontent">

	<p><?php print($msg->read($NOTES_STATUS)." $status"); ?></p>

	<form id="formulaire" action="index.php" method="post">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"    value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"    value=\"$cmde\" /></p>
			");
	?>

	<div style="border:#cccccc solid 1px; padding:4px;">

		  <table class="width100">
		    <tr style="background-color:#eeeeee;">
		      <td style="width:33%;"><?php print($msg->read($NOTES_CENTER)); ?></td>
		      <td style="width:33%;"></td>
		      <td style="width:33%;"></td>
		    </tr>

		    <tr>
		      <td>
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
						$select = ( $IDcentre == $row[0] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"$row[0]\" $select>$row[1]</option>");

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}				
				?>
				</select>
				</label>
		      </td>
		      <td></td>
		      <td></td>
		    </tr>

		    <tr>
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($NOTES_MODO)); ?> *</td>
		      <td><?php print($msg->read($NOTES_WRITER)); ?></td>
		      <td><?php print($msg->read($NOTES_READER)); ?></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
				<label for="IDmod">
				<select id="IDmod" name="IDmod">
					<option value="0"><?php print($msg->read($NOTES_NONE)); ?></option>
					<?php
						// recherche des modérateurs
						$query  = "select _ID, _name, _fname from user_id where _adm & 4 order by _name asc";

						$result = mysqli_query($mysql_link, $query);
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
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($NOTES_PERIOD)); ?></td>
		      <td><?php print($msg->read($NOTES_DISPLAY)); ?></td>
		      <td><?php print($msg->read($NOTES_DISPLAY)); ?></td>
		    </tr>

		    <tr>
			<td class="valign-top">
			<?php
				$list = explode(",", $msg->read($NOTES_PERIODLIST));

				for ($i = 0; $i < count($list); $i++) {
					$check = ( $i == $period ) ? "checked=\"checked\"" : "" ;

					print("<label for=\"period_$i\"><input type=\"radio\" id=\"period_$i\" name=\"period\" value= \"$i\" $check /> $list[$i]</label><br/>");
					}
			?>

				<label for="month">
				<select id="month" name="month">
				<?php
					$list = explode(",", $msg->read($NOTES_MONTH));

					// sélection du mois
					for ($i=0; $i < count($list); $i++)
						if ( $month == $i )
							print("<option value=\"$i\" selected=\"selected\">$list[$i]</option>");
						else
							print("<option value=\"$i\">$list[$i]</option>");
				?>
	    			</select>
	    			</label> <?php print($msg->read($NOTES_BEGIN)); ?>
		      </td>
			<td class="valign-top">
			<?php
				$list = explode(",", $msg->read($NOTES_DISPERIOD));

				for ($i = 0; $i < count($list); $i++) {
					$check = ( $i == $disperiod ) ? "checked=\"checked\"" : "" ;

					print("<label for=\"disperiod_$i\"><input type=\"radio\" id=\"disperiod_$i\" name=\"disperiod\" value= \"$i\" $check /> $list[$i]</label><br/>");
					}
			?>
		      </td>
			<td class="valign-top">
	           		<label for="disp_C"><input type="checkbox" id="disp_C" name="disp_C" <?php print($disp_C ? "checked=\"checked\"" : ""); ?> value="1" /><?php print($msg->read($NOTES_DISPCLASS)); ?></label><br/>
	           		<label for="disp_Y"><input type="checkbox" id="disp_Y" name="disp_Y" <?php print($disp_Y ? "checked=\"checked\"" : ""); ?> value="1" /><?php print($msg->read($NOTES_DISPYEAR)); ?></label><br/>
	           		<label for="disp_T"><input type="checkbox" id="disp_T" name="disp_T" <?php print($disp_T ? "checked=\"checked\"" : ""); ?> value="1" /><?php print($msg->read($NOTES_THRESHOLD)); ?></label><br/>
	           		<label for="disp_D"><input type="checkbox" id="disp_D" name="disp_D" <?php print($disp_D ? "checked=\"checked\"" : ""); ?> value="1" /><?php print($msg->read($NOTES_DETAILS)); ?></label>
		      </td>
		    </tr>

		    <tr>
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($NOTES_ATTENTION)); ?></td>
		      <td><?php print($msg->read($NOTES_MARKING)); ?></td>
		      <td><?php print($msg->read($NOTES_PRINTING)); ?></td>
		    </tr>

		    <tr>
			<td class="valign-top">
	           		<label for="email_"><input type="radio" id="email_" name="email" <?php print(($email == "-") ? "checked=\"checked\"" : ""); ?> value="-" /><?php print($msg->read($NOTES_NONE)); ?></label><br/>
	           		<label for="email_P"><input type="radio" id="email_P" name="email" <?php print(($email == "P") ? "checked=\"checked\"" : ""); ?> value="P" /><?php print($msg->read($NOTES_POSTIT)); ?></label><br/>
	           		<label for="email_E"><input type="radio" id="email_E" name="email" <?php print(($email == "E") ? "checked=\"checked\"" : ""); ?> value="E" /><?php print($msg->read($NOTES_BYMAIL)); ?></label>
		      </td>
			<td class="valign-top">
	           		<label for="text"><input type="text" id="text" name="text" size="6" value="<?php print($text); ?>" /></label>
				<?php print("<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/aide.gif\" title=\"".$msg->read($NOTES_UKTYPE)."\" alt=\"".$msg->read($NOTES_UKTYPE)."\" />"); ?><br/>

	           		<label for="sep"><input type="text" id="sep" name="sep" size="1" value="<?php print($sep); ?>" /></label>
				<?php print($msg->read($NOTES_SEPARATOR)); ?><br/>

				<label for="decimal">
				<select id="decimal" name="decimal">
				<?php
					for ($i = 0; $i < 3; $i++) {		
						$select = ( $i == $decimal ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"$i\" $select>$i</option>");
						}				
				?>
				</select>
				</label> <?php print($msg->read($NOTES_DECIMAL)); ?>
				<br/>

				<label for="max">
				<select id="max" name="max">
				<?php
					for ($i = 1; $i < 11; $i++) {		
						$select = ( $i == $max ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"$i\" $select>$i</option>");
						}				
				?>
				</select>
				</label> <?php print($msg->read($NOTES_MAXINPUT)); ?>
		      </td>
			<td class="valign-top">
	           		<label for="font"><input type="text" id="font" name="font" size="1" value="<?php print($font); ?>" /></label> <?php print($msg->read($NOTES_FONTSIZE)); ?>
		      </td>
		    </tr>
		  </table>

	</div>

	<div class="x-small">* <?php print($msg->read($NOTES_DECLARE)); ?></div>

	<hr style="width:80%;" />

		<table class="width100">
	           <tr>
	              <td style="width:10%;" class="valign-middle align-center">
	              	<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($NOTES_INPUTOK)."\" />"); ?>
	              </td>
	              <td class="valign-middle">
	              	<?php print($msg->read($NOTES_MODIFY)); ?>
	              </td>
	           </tr>
	           <tr>
	              <td class="valign-middle align-center">
	              	<?php print("<a href=\"index.php?item=$item\"><img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($NOTES_INPUTCANCEL)."\" />"); ?></a>
	              </td>
	              <td class="valign-middle">
	              	<?php print($msg->read($NOTES_BACK)); ?>
	              </td>
	           </tr>
		</table>
	</form>

</div>