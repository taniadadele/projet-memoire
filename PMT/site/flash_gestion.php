<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2003-2009 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Hugues Lecocq(hugues.lecocq@laposte.net)
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
 *		module   : flash_gestion.php
 *		projet   : la page de gestion des flash infos
 *
 *		version  : 1.1
 *		auteur   : laporte
 *		creation : 15/06/03
 *		modif    : 15/06/06 - par hugues lecocq
 * 	                 migration PHP5 
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


$IDflash = ( @$_POST["IDflash"] )				// identifiant des flash infos
	? (int) $_POST["IDflash"]
	: (int) @$_GET["IDflash"] ;
$IDmod   = @$_POST["IDmod"];					// identifiant du modérateur
$cbwr    = @$_POST["cbwr"];					// droits des rédacteurs
$cbrd    = @$_POST["cbrd"];					// droits des lecteurs
$IDgrp   = @$_POST["IDgrp"];					// identifiant des groupes
$create  = @$_POST["create"];					// affichage des flashs par ordre de création/modification
$chrono  = ( @$_POST["chrono"] )				// item chrono de la table flash
	? $_POST["chrono"]
	: @$_GET["chrono"] ;
$centre  = (int) (@$_POST["centre"] ? $_POST["centre"] : $_SESSION["CnxCentre"]) ;
$PJ      = (int) @$_POST["PJ"];
$rss     = ( @$_POST["rss"] )     ? "O" : "N" ;
$visible = ( @$_POST["visible"] ) ? "N" : "O" ;
$title   = addslashes(trim(@$_POST["title"]));		// titre du flash info

$submit  = ( @$_POST["valid_x"] )				// bouton de validation
	? $_POST["valid_x"]
	: @$_GET["submit"] ;
?>


<?php
	// initialisation
	$status = "-";

	// vérification des autorisations
	admSessionAccess();

	// l'utilisateur a validé la saisie
	if ( $submit AND $_SESSION["CnxAdm"] == 255 )
		switch ( $submit ) {
			case "del" :
				// supression des articles
				$Query  = "select flash_items._IDitem ";
				$Query  = "from flash_items, flash_data ";
				$Query .= "where flash_data._IDflash = '$IDflash' ";
				$Query .= "AND flash_data._IDinfos = flash_data._IDinfos";

				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

				while ( $row ) {
					mysqli_query($mysql_link, "delete from flash_items where _IDitem = '$row[0]'");
					$row    = mysqli_fetch_row($result);
					}

				// supression des annonces
				mysqli_query($mysql_link, "delete from flash_data where _IDflash = '$IDflash'");

				// supression du flash
				mysqli_query($mysql_link, "delete from flash where _IDflash = '$IDflash'");

				// supression des PJ...
				$IDflash = 0;
				break;

			case "new" :
				$IDflash = -1;
			case "update" :
				break;

			default :
				$status = ( $IDflash ) ? $msg->read($FLASH_MODIFY) : $msg->read($FLASH_INSERT) ;

				// droits des rédacteurs et des lecteurs
				$grpwr = $grprd = 0;
				for ($i = 0; $i < count($cbrd); $i++) {
					$grpwr += ( @$cbwr[$i] )  ? @$cbwr[$i]  : 0 ;
					$grprd += ( @$cbrd[$i] )  ? @$cbrd[$i]  : 0 ;
					}

				// insertion/modification du flash info
				if ( $IDflash ) {
					$Query  = "UPDATE flash ";
					$Query .= "SET _IDmod = '$IDmod', _IDgrpwr = '$grpwr', _IDgrprd = '$grprd', _visible = '$visible', _PJ = '$PJ', _rss = '$rss', _chrono = '$chrono', _create = '$create' ";
					$Query .= ( $title ) ? ", _title = '$title' " : "" ;
					$Query .= "where _IDflash = '$IDflash' ";
					$Query .= "limit 1";
					}
				else {
					// date de création du flash
					$date   = date("Y-m-d H:i:s");
					$title  = ( $title ) ? $title : "??" ;

					$Query  = "insert into flash ";
					$Query .= "values('', '0', '$IDmod', '$grprd', '$grpwr', '$grprd', '$date', 'N', '$title', 'C', '', 'flash.htm', 'F', 'N', '$visible', 'O', 'O', '$PJ', '$chrono', '$create', '$rss', '".$_SESSION["lang"]."')";
					}

				if ( !mysqli_query($mysql_link, $Query) ) {
					$status .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />";
					sql_error($mysql_link);
					}
				else
					$status .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";

				if ( !$IDflash )
					$IDflash = mysqli_insert_id($mysql_link);

				// droits des rédacteurs et des lecteurs
				$grp = 0;
				for ($i = 0; $i < count($IDgrp); $i++)
					$grp += ( @$IDgrp[$i] ) ? @$IDgrp[$i] : 0 ;

				if ( !mysqli_query($mysql_link, "insert into flash_default values('$centre', '$IDflash', '$grp', '".$_SESSION["lang"]."')") )
					mysqli_query($mysql_link, "update flash_default set _IDgrp = '$grp' where _IDcentre = '$centre' AND _IDflash = '$IDflash' limit 1");
				break;
			}

	// recherche du flash
	$query  = "select _IDmod, _title, _IDgrpwr, _IDgrprd, _visible, _IDflash, _chrono, _PJ, _create, _rss from flash ";
	$query .= "where _type = 'F' AND _lang = '".$_SESSION["lang"]."' ";
	$query .= ( $IDflash ) ? "AND _IDflash = '$IDflash' " : "" ;
	$query .= "order by _title";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	// initialisation
	$IDmod   = $row[0];
	$title   = $row[1];
	$grpwr   = $row[2];
	$grprd   = $row[3];
	$visible = $row[4];
	$IDflash = $row[5];
	$chrono  = ( $row ) ? $row[6] : "O" ;
	$PJ      = $row[7];
	$create  = ( $row ) ? $row[8] : "O" ;
	$rss     = ( $row ) ? $row[9] : "N" ;
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($FLASH_MANAGEMENT)); ?><br/>
		<?php print($msg->read($FLASH_FORMFEED)); ?>
	</div>
</div>

<div class="maincontent">

	<p><?php print($msg->read($FLASH_STATUS) . " $status"); ?></p>

	<form id="formulaire" action="index.php" method="post" enctype="multipart/formdata">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"$cmde\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDflash\"  value=\"$IDflash\" /></p>
			");
	?>

	<div style="border:#cccccc solid 1px; padding:4px;">

		  <table class="width100">
		    <tr style="background-color:#eeeeee;">
		      <td style="width:33%;"><?php print($msg->read($FLASH_IDENT)); ?></td>
		      <td style="width:33%;"><?php print($msg->read($FLASH_CLOSE)); ?></td>
		      <td style="width:33%;"><strong><?php print($msg->read($FLASH_HOMEPAGE)); ?></strong></td>
		    </tr>

		    <tr>
		      <td>
			<?php
				if ( $submit == "new" OR $submit == "update" )
					print("<label for=\"title\"><input type=\"text\" id=\"title\" name=\"title\" size=\"15\" value=\"$title\" /></label>");
				else {
					// modification du flash info
					$add   = "<a href=\"index.php?item=$item&amp;cmde=gestion&amp;submit=new\"><img src=\"".$_SESSION["ROOTDIR"]."/images/ajouter.gif\" title=\"". $msg->read($FLASH_ADDFLASH) ."\" alt=\"". $msg->read($FLASH_ADDFLASH) ."\" /></a>";

					// suppression du flash info
					$req   = $msg->read($FLASH_DELFLASH);
					$del   = ( $IDflash != 1 )
						? "<a href=\"index.php?item=$item&amp;cmde=gestion&amp;IDflash=$IDflash&amp;submit=del\" onclick=\"return confirmLink(this, '$req');\"><img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"".$msg->read($FLASH_DELFLASH)."\" alt=\"".$msg->read($FLASH_DELFLASH)."\" /></a>"
						: "" ;

					// modification du flash info
					$maj   = ( $IDflash != 1 )
						? "<a href=\"index.php?item=$item&amp;cmde=gestion&amp;IDflash=$IDflash&amp;submit=update\"><img src=\"".$_SESSION["ROOTDIR"]."/images/stats/post.gif\" title=\"". $msg->read($FLASH_UPDATEFLASH) ."\" alt=\"". $msg->read($FLASH_UPDATEFLASH) ."\" /></a>"
						: "" ;

					// recherche des flash infos
					$query  = "select _IDflash, _title from flash ";
					$query .= "where _type = 'F' ";
					$query .= "AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "order by _title asc";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					print("<label for=\"IDflash\">");
					print("<select id=\"IDflash\" name=\"IDflash\" onchange=\"document.forms.formulaire.submit()\">");
					while ( $row ) {
						$select = ( $IDflash == $row[0] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"$row[0]\" $select>$row[1]</option>");

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
					print("</select> $add $maj $del");
					print("</label>");
					}
			?>
		      </td>
		      <td>
			<?php
				$check = ( $visible == "N" ) ? "checked=\"checked\"" : "" ;

				if ( "$FLASH" == "$title" )
					print("<img src=\"".$_SESSION["ROOTDIR"]."/images/document.gif\" title=\"\" alt=\"\" /> ". $msg->read($FLASH_HOMEPAGE));
				else
					print("<label for=\"visible\"><input type=\"checkbox\" id=\"visible\" name=\"visible\" value=\"O\" $check /></label>");
			?>
		      </td>
		      <td class="valign-top">
				<fieldset style="border:#cccccc solid 1px;">
	      		<?php
					// lecture des flash par défaut
					$query   = "select _IDgrp from flash_default ";
					$query  .= "where _IDcentre = '$centre' AND _IDflash = '$IDflash' ";
					$query  .= "limit 1";

					$result  = mysqli_query($mysql_link, $query);
					$myrow   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

					$legend  = "<label for=\"centre\">";
		  			$legend .= "<select id=\"centre\" name=\"centre\" onchange=\"document.forms.formulaire.submit()\">";

					// lecture des centres constitutifs
					$query   = "select _IDcentre, _ident from config_centre ";
					$query  .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
					$query  .= "order by _IDcentre";

					$result  = mysqli_query($mysql_link, $query);
					$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {
						$select  = ( $centre == $row[0] ) ? "selected=\"selected\"" : "" ;

						$legend .= "<option value=\"$row[0]\" $select >$row[1]</option>";

						$row     = remove_magic_quotes(mysqli_fetch_row($result));
						}				
		  			$legend .= "</select>";
		  			$legend .= "</label>";

					print("<legend>$legend</legend>");

					// recherche des groupes
					$query   = "select _IDgrp, _ident from user_group ";
					$query  .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
					$query  .= "order by _IDgrp asc";

					$result  = mysqli_query($mysql_link, $query);
					$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {
						$check = ( $myrow[0] & pow(2, $row[0] - 1) ) ? "checked=\"checked\"" : "" ;

						print("<label for=\"IDgrp_$row[0]\"><input type=\"checkbox\" id=\"IDgrp_$row[0]\" name=\"IDgrp[]\" value=\"". pow(2, $row[0] - 1) ."\" $check /> $row[1]</label><br/>");

						$row   = remove_magic_quotes(mysqli_fetch_row($result));
						}
				?>
				</fieldset>
		      </td>
		    </tr>

		    <tr>
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($FLASH_MODO)); ?> *</td>
		      <td><?php print($msg->read($FLASH_WRITER)); ?></td>
		      <td><?php print($msg->read($FLASH_READER)); ?></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
				<label for="IDmod">
				<select id="IDmod" name="IDmod">
					<option value="0">* <?php print($msg->read($FLASH_NONE)); ?></option>
					<?php
						$select = ( $IDmod == -1 ) ? "selected=\"selected\"" : "" ;
						print("<option value=\"-1\" $select>* ".$msg->read($FLASH_WRITERGROUP)."</option>");

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

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
				?>
		      </td>
		    </tr>

		    <tr>
		      <td style="height:10px;"></td>
		    </tr>

		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($FLASH_PERM)); ?></td>
		      <td><?php print($msg->read($FLASH_SHOWFLASH)); ?></td>
		      <td><?php print($msg->read($FLASH_SHOW)); ?></td>
		    </tr>

		    <tr>
		      <td class="valign-top">
	           		<label for="noPJ"><input type="checkbox" id="noPJ" name="noPJ" disabled="disabled" /><?php print($msg->read($FLASH_ATTACHMENT)); ?></label>
				<label for="PJ">
				<select id="PJ" name="PJ" style="font-size:9px;">
				<?php
					for ($i = 0; $i <= 10; $i++) {
						$select = ( $PJ == $i ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"$i\" $select>$i</option>");
						}
				?>
				</select>
				</label><br/>
				<label for="rss"><input type="checkbox" id="rss" name="rss" <?php print(($rss == "O") ? "checked=\"checked\"" : ""); ?> value="O" /><?php print($msg->read($FLASH_RSS)); ?></label>
		      </td>
		      <td class="valign-top">
		            <label for="create_O"><input type="radio" id="create_O" name="create" value="O" <?php print(($create == "O") ? "checked=\"checked\"" : ""); ?> /> <?php print($msg->read($FLASH_CREATE)); ?></label><br/>
		            <label for="create_N"><input type="radio" id="create_N" name="create" value="N" <?php print(($create == "N") ? "checked=\"checked\"" : ""); ?> /> <?php print($msg->read($FLASH_UPDATE)); ?></label>
		      </td>
		      <td class="valign-top">
		            <label for="chrono_O"><input type="radio" id="chrono_O" name="chrono" value="O" <?php print(($chrono == "O") ? "checked=\"checked\"" : ""); ?> /> <?php print($msg->read($FLASH_CHRONO)); ?></label><br/>
		            <label for="chrono_N"><input type="radio" id="chrono_N" name="chrono" value="N" <?php print(($chrono == "N") ? "checked=\"checked\"" : ""); ?> /> <?php print($msg->read($FLASH_CHRONOREV)); ?></label>
		      </td>
		    </tr>
		  </table>

	</div>

	<div class="x-small">* <?php print($msg->read($FLASH_DECLARE)); ?></div>

         <table class="width100">
           <tr>
              <td style="width:100%;" colspan="2"><hr style="width:80%;" /></td>
           </tr>
           <tr>
              <td style="width:10%;" class="valign-middle align-center">
              	<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($FLASH_INPUTOK)."\" />"); ?>
              </td>
              <td class="valign-middle">
              	<?php print($msg->read($FLASH_MODIFICATION)); ?>
              </td>
           </tr>
           <tr>
              <td class="valign-middle align-center">
              	<a href="index.php"><?php print("<img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($FLASH_INPUTCANCEL)."\" />"); ?></a>
              </td>
              <td class="valign-middle">
              	<?php print($msg->read($FLASH_QUIT)); ?>
              </td>
           </tr>
         </table>

	</form>

</div>