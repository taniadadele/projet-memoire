<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2005-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : ctn_gestion.php
 *		projet   : la page de gestion du cahier de texte numérique
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 22/10/05
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


$IDcentre = ( @$_POST["IDcentre"] )		// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
$IDmat   = ( @$_POST["IDmat"] )		// ID de la matière
	? (int) $_POST["IDmat"]
	: (int) @$_GET["IDmat"] ;
$IDclass = ( @$_POST["IDclass"] )		// sélection de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$IDgroup = ( @$_POST["IDgroup"] )		// sélection du e-groupe
	? (int) $_POST["IDgroup"]
	: (int) @$_GET["IDgroup"] ;
$IDdata  = ( @$_POST["IDdata"] )		// ??
	? (int) $_POST["IDdata"]
	: (int) @$_GET["IDdata"] ;

$IDmod   = (int) @$_POST["IDmod"];		// ID du modérateur
$cbwr    = @$_POST["cbwr"];
$cbrd    = @$_POST["cbrd"];
$display = ( @$_POST["display"] ) ? $_POST["display"] : "D" ;
$month   = ( strlen(@$_POST["month"]) ) ? (int) @$_POST["month"] : 8 ;
$visible = ( @$_POST["visible"] ) ? "N" : "O" ;
$PJ      = ( @$_POST["PJ"] ) ? "O" : "N" ;
$diary   = ( @$_POST["diary"] ) ? "O" : "N" ;
$cdate   = ( @$_POST["cdate"] ) ? "O" : "N" ;
$limited = ( @$_POST["limited"] ) ? "O" : "N" ;
$common  = ( @$_POST["common"] ) ? "O" : "N" ;
$horaire = str_replace(" ", "", @$_POST["horaire"]);
$font    = (int) @$_POST["font"];
$rss     = ( @$_POST["rss"] ) ? "O" : "N" ;
$sndmail = ( @$_POST["sndmail"] ) ? "O" : "N" ;

$submit  = @$_POST["valid_x"];		// bouton de validation
?>


<?php
	// vérification des autorisations
	admSessionAccess();

	// l'utilisateur a validé la saisie
	if ( $submit ) {
		$status = $msg->read($CTN_MODIFICATION) ." ";

		// droits des rédacteurs et des lecteurs
		$grpwr  = $grprd = 0;
		for ($i = 0; $i < count($cbwr); $i++)
			$grpwr += ( @$cbwr[$i] ) ? $cbwr[$i] : 0 ;
		for ($i = 0; $i < count($cbrd); $i++)
			$grprd += ( @$cbrd[$i] ) ? $cbrd[$i] : 0 ;

		// ajout/modification du cahier de texte
		$Query  = "insert into ctn ";
		$Query .= "values('', '$IDclass', '$IDgroup', '$IDcentre', '$IDmod', '$grpwr', '$grprd', '$month', '$visible', '$PJ', '$diary', '$cdate', '$display', '$limited', '$common', '$horaire', '$font', '$rss', '$sndmail')";

		if ( !mysqli_query($mysql_link, $Query) ) {
			$Query  = "UPDATE ctn ";
			$Query .= "SET _IDmod = '$IDmod', _IDgrpwr = '$grpwr', _IDgrprd= '$grprd', _month = '$month', _visible = '$visible', _PJ = '$PJ', _diary = '$diary', _currdate = '$cdate', _display = '$display', _limited = '$limited', _common = '$common', _horaire = '$horaire', _font = '$font', _rss = '$rss', _sndmail = '$sndmail' ";
			$Query .= "where _IDclass = '$IDclass' AND _IDgroup = '$IDgroup' ";
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
	// initialisation
	else
		$status = "-";

	// raz sur changement de centre
	if ( @$_POST["centre"] AND @$_POST["centre"] != $IDcentre )
		$IDclass = 0;

	// recherche du cahier de texte
	$Query  = "select _IDmod, _IDgrpwr, _IDgrprd, _PJ, _visible, _month, _diary, _display, _currdate, _limited, _common, _horaire, _font, _rss, _sndmail from ctn ";
	$Query .= "where _IDcentre = '$IDcentre' ";
	$Query .= "AND _IDgroup = '$IDgroup' ";
	$Query .= ( $IDclass )
		? "AND _IDclass = '$IDclass'"
		: "AND _IDclass > '0' order by _IDclass limit 1" ;

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// initialisation
	$IDmod   = $row[0];
	$grpwr   = (int) @$row[1];
	$grprd   = ( $row ) ? $row[2] : 255 ;
	$PJ      = ( $row ) ? $row[3] : "O" ;
	$visible = ( $row ) ? $row[4] : "O" ;
	$month   = ( $row ) ? $row[5] : $month ;
	$diary   = ( $row ) ? $row[6] : "O" ;
	$display = ( $row ) ? $row[7] : "D" ;
	$cdate   = ( $row ) ? $row[8] : "O" ;
	$limited = ( $row ) ? $row[9] : "N" ;
	$common  = ( $row ) ? $row[10] : "N" ;
	$horaire = ( $row ) ? $row[11] : "1:00,2:00,3:00,4:00" ;
	$font    = ( $row ) ? $row[12] : 10 ;
	$rss     = ( $row ) ? $row[13] : "N" ;
	$sndmail = ( $row ) ? $row[14] : "N" ;
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($CTN_MANAGEMENT)); ?>
	</div>
</div>

<div class="maincontent">

	<p><?php print($msg->read($CTN_STATUS) . " $status"); ?></p>

	<form id="formulaire" action="index.php" method="post">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDmat\"    value=\"$IDmat\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDgroup\"  value=\"$IDgroup\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"$cmde\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"centre\"   value=\"$IDcentre\" /></p>
			");
	?>

	<div style="border:#cccccc solid 1px; padding:4px;">

		  <table class="width100">
		    <tr style="background-color:#eeeeee;">
		      <td style="width:33%;"><?php print($msg->read($CTN_CENTER)); ?></td>
		      <td style="width:33%;"></td>
		      <td style="width:33%;"></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
				<label for="IDcentre">
				<select id="IDcentre" name="IDcentre" onchange="document.forms.formulaire.submit()">
				<?php
					// lecture des centres constitutifs
					$query  = "select _IDcentre, _ident from config_centre ";
					$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "order by _IDcentre";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					$i = 0;
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
		      <td><?php print($msg->read($CTN_CLASS)); ?></td>
		      <td><?php print($msg->read($CTN_CLOSE)); ?></td>
		      <td></td>
		    </tr>

		    <tr>
		      <td>
				<label for="IDclass">
				<select id="IDclass" name="IDclass" onchange="document.forms.formulaire.submit()">
				<?php
					// recherche des cahiers de texte
					$query  = "select _IDclass, _ident from campus_classe ";
					$query .= "where _IDcentre = '$IDcentre' AND _visible = 'O' ";
					$query .= "order by _ident";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {
						$select = ( $IDclass == $row[0] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"$row[0]\" $select>$row[1]</option>");

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
				?>
				</select>
				</label>
		      </td>
		      <td>
             		<label for="visible"><input type="checkbox" id="visible" name="visible" value="O" <?php print(($visible == "N") ? "checked=\"checked\"" : ""); ?> /></label>
		      </td>
		      <td></td>
		    </tr>

		    <tr>
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($CTN_MODO)); ?>*</td>
		      <td><?php print($msg->read($CTN_WRITER)); ?></td>
		      <td><?php print($msg->read($CTN_READER)); ?></td>
		    </tr>
	    
		    <tr>
		      <td class="valign-top">
				<label for="IDmod">
				<select id="IDmod" name="IDmod">
					<option <?php if ( !$IDdata ) print("selected=\"selected\""); ?> value="0"><?php print($msg->read($CTN_NONE)); ?></option>
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

	             		print("<label for=\"cbwr_$row[0]\"><input type=\"checkbox\" id=\"cbwr_$row[0]\" name=\"cbwr[]\" value=\"". pow(2, $row[0] - 1) ."\" $check /></label> $row[1]<br/>");

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

	             		print("<label for=\"cbrd_$row[0]\"><input type=\"checkbox\" id=\"cbrd_$row[0]\" name=\"cbrd[]\" value=\"". pow(2, $row[0] - 1) ."\" $check /></label> $row[1]<br/>");

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
             	?>
		      </td>
		    </tr>

		    <tr>
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($CTN_PERMS)); ?></td>
		      <td><strong><?php print($msg->read($CTN_DISPLAY)); ?></strong></td>
		      <td><?php print($msg->read($CTN_PRINTING)); ?></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
           			<label for="PJ"><input type="checkbox" id="PJ" name="PJ" <?php print(($PJ == "O") ? "checked=\"checked\"" : ""); ?> value="O" /></label><?php print($msg->read($CTN_ATTACHMENT)); ?><br/>
           			<label for="diary"><input type="checkbox" id="diary" name="diary" <?php print(($diary == "O") ? "checked=\"checked\"" : ""); ?> value="O" /></label><?php print($msg->read($CTN_DIARY)); ?><br/>
           			<label for="cdate"><input type="checkbox" id="cdate" name="cdate" <?php print(($cdate == "O") ? "checked=\"checked\"" : ""); ?> value="O" /></label><?php print($msg->read($CTN_CURRDATE)); ?><br/>
           			<label for="limited"><input type="checkbox" id="limited" name="limited" <?php print(($limited == "O") ? "checked=\"checked\"" : ""); ?> value="O" /></label><?php print($msg->read($CTN_LIMITED)); ?><br/>
           			<label for="common"><input type="checkbox" id="common" name="common" <?php print(($common == "O") ? "checked=\"checked\"" : ""); ?> value="O" /></label><?php print($msg->read($CTN_COMMON)); ?><br/>
           			<label for="rss"><input type="checkbox" id="rss" name="rss" <?php print(($rss == "O") ? "checked=\"checked\"" : ""); ?> value="O" /></label><?php print($msg->read($CTN_RSS)); ?><br/>
           			<label for="sndmail"><input type="checkbox" id="sndmail" name="sndmail" <?php print(($sndmail == "O") ? "checked=\"checked\"" : ""); ?> value="O" /></label><?php print($msg->read($CTN_SNDMAIL)); ?>
		      </td>
			<td class="valign-top">
				<?php $list = explode(",", $msg->read($CTN_DISPLAYLIST)); ?>
	           		<label for="display_d"><input type="radio" id="display_d" name="display" <?php print(($display == "D") ? "checked=\"checked\"" : ""); ?> value="D" /><?php print($list[0]); ?></label><br/>
	           		<label for="display_w"><input type="radio" id="display_w" name="display" <?php print(($display == "W") ? "checked=\"checked\"" : ""); ?> value="W" /><?php print($list[1]); ?></label><br/>
	           		<label for="display_m"><input type="radio" id="display_m" name="display" <?php print(($display == "M") ? "checked=\"checked\"" : ""); ?> value="M" /><?php print($list[2]); ?></label>
		      </td>
		      <td class="valign-top">
	           		<label for="font"><input type="text" id="font" name="font" size="1" value="<?php print($font); ?>" /></label> <?php print($msg->read($CTN_FONTSIZE)); ?>
			</td>
		    </tr>

		    <tr>
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($CTN_SCHOOLYEAR)); ?></td>
		      <td><?php print($msg->read($CTN_TIMETABLE)); ?></td>
		      <td></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
				<label for="month">
				<select id="month" name="month">
				<?php
					$list = explode(",", $msg->read($CTN_MONTHFULL));

					// sélection du mois
					for ($i=0; $i < count($list); $i++)
						if ( $month == $i )
							print("<option value=\"$i\" selected=\"selected\">$list[$i]</option>");
						else
							print("<option value=\"$i\">$list[$i]</option>");
				?>
	    			</select>
	    			</label>
			</td>
			<td class="valign-top">
		      	<label for="horaire"><textarea id="horaire" name="horaire" rows="1" cols="30"><?php print($horaire); ?></textarea></label>
		      </td>
		      <td></td>
		    </tr>
		  </table>

	</div>

	<div class="x-small">* <?php print($msg->read($CTN_DECLARE)); ?></div>

	<hr style="width:80%;" />

         <table class="width100">
           <tr>
              <td style="width:10%;" class="valign-middle align-center">
              	<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($CTN_INPUTOK)."\" />"); ?>
              </td>
              <td class="valign-middle">
              	<?php print($msg->read($CTN_UPDATECTN)); ?>
              </td>
           </tr>
           <tr>
              <td class="valign-middle align-center">
              	<?php print("<a href=\"".myurlencode("index.php?item=$item&IDmat=$IDmat&IDgroup=$IDgroup&IDcentre=$IDcentre&salon=".$_SESSION["CampusName"])."\">"); ?>
			<?php print("<img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($CTN_INPUTCANCEL)."\" />"); ?></a>
              </td>
              <td class="valign-middle">
              	<?php print($msg->read($CTN_GOBACK)); ?>
              </td>
           </tr>
         </table>

	</form>

</div>