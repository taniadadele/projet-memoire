<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2004-2009 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : student_gestion.php
 *		projet   : la page de gestion des élèves
 *
 *		version  : 1.2
 *		auteur   : laporte
 *		creation : 27/10/04
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *		           7/01/07 - D. Laporte
 * 	                 durée d'inscription limitée
 */


$IDcentre = ( @$_GET["IDcentre"] )			// identification du centre
	? (int) $_GET["IDcentre"]
	: (int) (@$_POST["IDcentre"] ? $_POST["IDcentre"] : $_SESSION["CnxCentre"]) ;
$visu     = ( @$_POST["visu"] )			// type de visualisation
	? $_POST["visu"]
	: (@$_GET["visu"] ? $_GET["visu"] : "O");
$IDsel    = ( strlen(@$_POST["IDsel"]) )		// choix de la classe
	? (int) $_POST["IDsel"]
	: (strlen(@$_GET["IDsel"])
		? (int) $_GET["IDsel"]
		: (int) ($visu ? $_SESSION["CnxClass"] : 0) );
$IDalpha  = ( $IDsel == 0 )
	? (@$_GET["IDalpha"]
		? $_GET["IDalpha"]
		: "A")
	: "" ;
$sort     = ( strlen(@$_POST["sort"]) )		// filtre affichage
	? $_POST["sort"]
	: @$_GET["sort"] ;

$IDn      = (int) @$_POST["IDn"];			// changement de classe (tous)
$IDc      = (int) @$_POST["IDc"];			// changement de centre (tous)
$alldel   = (int) @$_POST["alldel"];		// supprimer un élève (tous)
$allold   = (int) @$_POST["allold"];		// basculement ancien élève (tous)
$allcnx   = (int) @$_POST["allcnx"];		// login de connexion (tous)

$del      = @$_POST["del"];				// élève supprimé
$id       = @$_POST["id"];				// changement de classe
$ic       = @$_POST["ic"];				// changement de centre
$regime   = @$_POST["regime"];			// changement de régime
$dlg      = @$_POST["dlg"];				// élèvé délégué
$brs      = @$_POST["brs"];				// élèvé boursier
$cnx      = @$_POST["cnx"];				// login de connexion
$old      = @$_POST["old"];				// basculement ancien élève

$valid    = @$_POST["valid_x"];			// bouton validation
?>


<?php
	// vérification des autorisations
	admSessionAccess();

	// suppression / modification des utilisateurs
	if ( $valid ) {
		// lecture des élèves
		$list = @$_POST["iduser"];

		for ($i = 0; $i < count($list); $i++)
			// on supprime les connexions
			if ( @$del[$i] ) {
            		$Query  = "delete from user_id ";
     	     			$Query .= "where _ID = '$list[$i]' ";
				$Query .= "limit 1";

				mysqli_query($mysql_link, $Query);
				}
			else {
				// on affecte la nouvelle classe, les régimes, les délégués et les bourses
				// on bascule ancien/nouveau élève
				// attribution de connexion

      	      	$Query  = "update user_id ";
     	      		$Query .= "set _IDclass = '$id[$i]', _regime = '$regime[$i]', ";
     	      		$Query .= ( @$dlg[$i] == "O" ) ? "_delegue = 'O', " : "_delegue = 'N', " ;
     	      		$Query .= ( @$brs[$i] == "O" ) ? "_bourse = 'O', " : "_bourse = 'N', " ;
     	      		$Query .= ( @$old[$i] )
					? ($visu == "O" ? "_visible = 'N', " : "_visible = 'O', ")
					: "" ;
				$Query .= ( @$cnx[$i] ) ? "_adm = '1' " : "_adm = '0' " ;
     	      		$Query .= "where _ID = '$list[$i]' ";
           			$Query .= "limit 1";

				mysqli_query($mysql_link, $Query);

				// historique des promotions
				if ( $id[$i] != $row[2] OR (@$old[$i] AND $visu == "O") ) {
					$date   = date("Y-00-00");

     		      		$Query  = "insert into user_promos ";
	     				$Query .= "values('$row[0]', '$row[2]', '$date', '$row[3]')";

					@mysqli_query($mysql_link, $Query);
					}
				}
           	}
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
	<?php
		// ajout compte élève
		$add = "<a href=\"".myurlencode("index.php?item=$item&cmde=account")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/addrecord.gif\" title=\"". $msg->read($STUDENT_ADDRECORD) ."\" alt=\"". $msg->read($STUDENT_ADDRECORD) ."\" /></a>";

		switch ( $visu ) {
	           	case "N" :
	           		echo $msg->read($STUDENT_LIST1) . " $add");
	           		break;
	           	default :
	           		echo $msg->read($STUDENT_LIST2) . " $add");
	           		break;
	           	}
	?>
	</div>
</div>

<div class="maincontent">

	<form id="formulaire" action=""  method="post">

		<table class="width100">
		  <tr>
			<td style="width:50%;" class="align-right">
			    	<?php print($msg->read($STUDENT_CHOOSECENTER)); ?>
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

					$i = 0;
					while ( $row ) {
						if ( $IDcentre == $row[0] )
							print("<option selected=\"selected\" value=\"$row[0]\">$row[1]</option>");
						else
							print("<option value=\"$row[0]\">$row[1]</option>");

						$mycentre[0][$i] = $row[0];
						$mycentre[1][$i] = $row[1];

						// intialisation des classes
						$Query  = "select _IDclass, _ident from campus_classe ";
						$Query .= "where _IDcentre = '$row[0]' " ;

						// pour les anciens élèves, certaines filières ont peut être disparues
						$Query .= ( $visu == "O" ) ? "AND _visible = 'O' " : "" ;

						$Query .= "order by _IDclass";

						// lecture des classes
						$return = mysqli_query($mysql_link, $Query);
						$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

						$j = 0;
						$k = $row[0];
						while ( $myrow ) {
							$myclass[0][$k][$j] = $myrow[0];
							$myclass[1][$k][$j] = $myrow[1];

							$myrow = remove_magic_quotes(mysqli_fetch_row($return));
							$j++;
							}	// endwhile classe

						$i++;
						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
				?>
				</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/home.gif" title="" alt="" />
				</label>
			</td>
			<td style="vertical-align: top;">
			<?php
			// il faut les droits admin
			if ( $_SESSION["CnxAdm"] == 255 )
				print("
					<a href=\"index.php?item=$item\" class=\"icon-list-alt\">
					</a>");
			?>
			</td>
		  </tr>

		    <tr>
			<td style="width:50%;" class="align-right">
			    	<?php print($msg->read($STUDENT_CHOOSECLASS)); ?>
			</td>
			<td style="width:50%;">
				<label for="IDsel">
				<select id="IDsel" name="IDsel" onchange="document.forms.formulaire.submit()">
				<?php
					print("<option value=\"0\">". $msg->read($STUDENT_ALLCLASSES) ."</option>");

					$k = $IDcentre;
					for ($i = 0; $i < count($myclass[0][$k]); $i++) {
						$select = ( $IDsel == $myclass[0][$k][$i] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"".$myclass[0][$k][$i]."\" $select>".$myclass[1][$k][$i]."</option>");
						}	// endwhile classe
				?>
				</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/group.gif" title="" alt="" />
				</label>
			</td>
		    </tr>

		    <tr>
			<td style="width:100%;" colspan="2" class="align-center">
			  <hr style="width:80%;" />

			  <?php
				// accès par ordre alphabétique
		            $alpha = "BCDEFGHIJKLMNOPQRSTUVWXYZ";

		            print("<a href=\"".myurlencode("index.php?item=$item&visu=$visu&cmde=$cmde&IDsel=$IDsel&IDalpha=A&sort=$sort")."\">A</a>");
		            for ($i = 0; $i < 25; $i++)
		            	print("<strong>.</strong><a href=\"".myurlencode("index.php?item=$item&visu=$visu&cmde=$cmde&IDsel=$IDsel&IDalpha=$alpha[$i]&sort=$sort")."\">$alpha[$i]</a>");

				// affichage filtrer
				$list    = explode("|", $msg->read($STUDENT_FILTER));

				$select  = "<label for=\"sort\">";
				$select .= "<select id=\"sort\" name=\"sort\" onchange=\"document.forms.formulaire.submit()\" style=\"font-size:9px;\">";
				$select .= "<option value=\"0\">$list[0]</option>" ;

				$select1 = ( $sort == 1 ) ? "selected=\"selected\"" : "" ;
				$select2 = ( $sort == 2 ) ? "selected=\"selected\"" : "" ;

				$select .= "<optgroup label=\"". $msg->read($STUDENT_REPRESENTATIVE) ."\">";
				$select .= "<option value=\"1\" $select1 >". $msg->read($STUDENT_YES) ."</option>" ;
				$select .= "<option value=\"2\" $select2 >". $msg->read($STUDENT_NO) ."</option>" ;
				$select .= "</optgroup>";

				$select3 = ( $sort == 3 ) ? "selected=\"selected\"" : "" ;
				$select4 = ( $sort == 4 ) ? "selected=\"selected\"" : "" ;

				$select .= "<optgroup label=\"". $msg->read($STUDENT_GRANTHOLDER) ."\">";
				$select .= "<option value=\"3\" $select3 >". $msg->read($STUDENT_YES) ."</option>" ;
				$select .= "<option value=\"4\" $select4 >". $msg->read($STUDENT_NO) ."</option>" ;
				$select .= "</optgroup>";

				$select5 = ( $sort == 5 ) ? "selected=\"selected\"" : "" ;
				$select6 = ( $sort == 6 ) ? "selected=\"selected\"" : "" ;

				$select .= "<optgroup label=\"". $msg->read($STUDENT_LOGIN) ."\">";
				$select .= "<option value=\"5\" $select5 >". $msg->read($STUDENT_YES) ."</option>" ;
				$select .= "<option value=\"6\" $select6 >". $msg->read($STUDENT_NO) ."</option>" ;
				$select .= "</optgroup>";

				$select .= "</select>";
				$select .= "</label>";

				print(" $select");
			  ?>

			  <hr style="width:80%;" />
			</td>
		    </tr>
		  </table>

	</form>

	<?php
		// on classe par ordre alphabétique
		$Query  = "select distinctrow ";
		$Query .= "user_id._name, user_id._fname, user_id._IDclass, user_id._ID, ";
		$Query .= "user_id._delegue, user_id._bourse, user_id._regime, user_id._adm, ";
		$Query .= "config_centre._ident, ";
		$Query .= "campus_classe._ident, campus_classe._IDcentre ";
		$Query .= "from user_id, config_centre, campus_classe ";
		$Query .= ( $sort == 5 OR $sort == 6 ) ? ", user_id " : "" ;
		$Query .= "where campus_classe._IDcentre = '$IDcentre' " ;
		$Query .= "AND config_centre._IDcentre = '$IDcentre' " ;
		$Query .= "AND config_centre._lang = '".$_SESSION["lang"]."' " ;
		$Query .= "AND user_id._IDclass = campus_classe._IDclass " ;
		$Query .= ( $IDsel ) ? "AND user_id._IDclass = '$IDsel' " : "" ;
		$Query .= "AND user_id._visible = '$visu' " ;
		$Query .= ( isset($IDalpha) ) ? "AND user_id._name >= '$IDalpha' " : "" ;

		switch ( $sort ) {
			case 1 :
			case 2 :
				$Query .= ( $sort == 1 ) ? "AND user_id._delegue = 'O' " : "AND user_id._delegue = 'N' " ;
				break;
			case 3 :
			case 4 :
				$Query .= ( $sort == 3 ) ? "AND user_id._bourse = 'O' " : "AND user_id._bourse = 'N' " ;
				break;
			case 5 :
			case 6 :
				$Query .= ( $sort == 5 ) ? "AND user_id._adm != '0' " : "AND user_id._adm = '0' " ;
				break;
			default :
				break;
			}

		$Query .= "order by user_id._name, user_id._fname";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		$chkdel = ( $alldel ) ? "checked=\"checked\"" : "" ;
		$chkold = ( $allold ) ? "checked=\"checked\"" : "" ;
		$chkcnx = ( $allcnx ) ? "checked=\"checked\"" : "" ;

		print("
		   <form id=\"selection\" action=\"\" method=\"post\">

			<table class=\"width100\">
			  <tr class=\"align-center\" style=\"background-color:#c0c0c0;\">
	                <td class=\"align-center\" style=\"width:6%; white-space:nowrap;\">
				<img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"". $msg->read($STUDENT_DELSTUDENT) ."\" alt=\"". $msg->read($STUDENT_DELSTUDENT) ."\" />
             		<label for=\"alldel\"><input type=\"checkbox\" id=\"alldel\" name=\"alldel\" value=\"1\" onclick=\"document.forms.selection.submit()\" $chkdel /></label>
	                </td>
	                <td style=\"width:25%;\">
			  	<label for=\"visu\">
			  	<select id=\"visu\" name=\"visu\" onchange=\"document.forms.selection.submit()\">");

					$statlist = explode(",",  $msg->read($STUDENT_STATLIST));

					for ($j = 0; $j < count($statlist); $j++) {
						list($value, $ident) = explode(":", $statlist[$j]);

						if ( $visu == $value )
							print("<option value=\"$value\" selected=\"selected\">$ident</option>");
						else
							print("<option value=\"$value\">$ident</option>");
						}

		print("
				</select>
				</label>
	                </td>
	                <td style=\"width:20%;\">
			  	<label for=\"IDc\">
			  	<select id=\"IDc\" name=\"IDc\" onchange=\"document.forms.selection.submit()\">
					<option value=\"0\">".$msg->read($STUDENT_CHANGECENTER)."</option>");

					for ($i = 0; $i < count($mycentre[0]); $i++)
						if ( $IDc == $mycentre[0][$i] )
							print("<option value=\"".$mycentre[0][$i]."\" selected=\"selected\">".$mycentre[1][$i]."</option>");
						else
							print("<option value=\"".$mycentre[0][$i]."\">".$mycentre[1][$i]."</option>");

					$alt = ( $visu == "O" )
						? $msg->read($STUDENT_SWAP)
						: $msg->read($STUDENT_SWAPAGAIN) ;

		print("
			  	</select>
			  	</label>
	                </td>
	                <td style=\"width:20%;\">
			  	<label for=\"IDn\">
			  	<select id=\"IDn\" name=\"IDn\" onchange=\"document.forms.selection.submit()\">");

					// affichage des classes
					print("<option value=\"0\">".$msg->read($STUDENT_CHANGECLASS)."</option>");

					$k = ( $IDc ) ? $IDc : $IDcentre ;
					for ($i = 0; $i < count($myclass[0][$k]); $i++) {
						$select = ( $IDn == $myclass[0][$k][$i] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"".$myclass[0][$k][$i]."\" $select>".$myclass[1][$k][$i]."</option>");
						}	// endwhile classe

		print("
			  	</select>
			  	</label>
	                </td>
	                <td style=\"width:11%;\">".$msg->read($STUDENT_REGIME)."</td>
	                <td style=\"width:3%;\">
				<img src=\"".$_SESSION["ROOTDIR"]."/images/stats/mod.gif\" title=\"".$msg->read($STUDENT_REPRESENTATIVE)."\" alt=\"".$msg->read($STUDENT_REPRESENTATIVE)."\" />
	                </td>
	                <td style=\"width:3%;\">
				<img src=\"".$_SESSION["ROOTDIR"]."/images/euro.gif\" title=\"".$msg->read($STUDENT_GRANT)."\" alt=\"".$msg->read($STUDENT_GRANT)."\" />
	                </td>
	                <td class=\"align-center\" style=\"width:6%; white-space:nowrap;\">
				<img src=\"".$_SESSION["ROOTDIR"]."/images/ip.gif\" title=\"".$msg->read($STUDENT_GIVELOGIN)."\" alt=\"".$msg->read($STUDENT_GIVELOGIN)."\" />
             		<label for=\"allcnx\"><input type=\"checkbox\" id=\"allcnx\" name=\"allcnx\" value=\"1\" onclick=\"document.forms.selection.submit()\" $chkcnx /></label>
	                </td>
	                <td class=\"align-center\" style=\"width:6%; white-space:nowrap;\">
				<img src=\"".$_SESSION["ROOTDIR"]."/images/move.gif\" title=\"$alt\" alt=\"$alt\" />
             		<label for=\"allold\"><input type=\"checkbox\" id=\"allold\" name=\"allold\" value=\"1\" onclick=\"document.forms.selection.submit()\" $chkold /></label>
	                </td>
			  </tr>
			");

		$i = 0;
		while ( $row ) {
			$nbr    = $i + 1;
			$bgcol  = ( $i % 2 ) ? "item" : "menu" ;

			$chkdel = ( $alldel ) ? "checked=\"checked\"" : "" ;
			$chkold = ( $allold ) ? "checked=\"checked\"" : "" ;
			$check1 = ( $row[4] == "O" ) ? "checked=\"checked\"" : "" ;
			$check2 = ( $row[5] == "O" ) ? "checked=\"checked\"" : "" ;
			$check3 = ( $allcnx == 0 AND $row[7] ) ? "checked=\"checked\"" : $chkcnx  ;

			print("
				<tr class=\"$bgcol\">
		                <td class=\"align-center\">
					<label for=\"del_$i\"><input type=\"checkbox\" id=\"del_$i\" name=\"del[$i]\" value=\"$row[3]\" $chkdel /></label>
					<p class=\"hidden\"><input type=\"hidden\" name=\"iduser[$i]\" value=\"$row[3]\" /></p>
					<br/>$nbr
		                </td>
		                <td>
					<a href=\"".myurlencode("index.php?item=38&cmde=account&ID=$row[3]")."\">". formatUserName($row[0], $row[1]) ."</a><br/>
					<span class=\"x-small\">$row[8], $row[9]</span>
		                </td>
		                <td class=\"align-center\">
				  	<label for=\"ic_$i\">
				  	<select id=\"ic_$i\" name=\"ic[$i]\" onchange=\"document.forms.selection.submit()\">");

					// changement de centre
					$ic_sel = ( $IDc && $IDc != @$_POST["idcentre"] )
						? $IDc
						: (@$ic[$i] ? $ic[$i] : $IDcentre) ;

					for ($j = 0; $j < count($mycentre[0]); $j++) {
						$selected = ( $ic_sel == $mycentre[0][$j] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"".$mycentre[0][$j]."\" $selected>".$mycentre[1][$j]."</option>");
						}

			print("
					</select>
					</label>
		                </td>
		                <td class=\"align-center\">
				  	<label for=\"id_$i\">
				  	<select id=\"id_$i\" name=\"id[$i]\">");

					// changement de classe
					$id_sel = ( $IDn )
						? $IDn
						: (@$id[$i] ? $id[$i] : $row[2]) ;

					$k = $ic_sel;
					for ($j = 0; $j < count($myclass[0][$k]); $j++) {
						$selected = ( $id_sel == $myclass[0][$k][$j] ) ? "selected=\"selected\"" : "" ;

						print("<option value=\"".$myclass[0][$k][$j]."\" $selected>".$myclass[1][$k][$j]."</option>");
						}

			print("
					</select>
					</label>
		                </td>
		                <td class=\"align-center\">
				  	<label for=\"regime_$i\">
				  	<select id=\"regime_$i\" name=\"regime[$i]\">");

					// changement de régime
					$reg[0] = Array('E', 'I', 'D', 'C');
					$reg[1] = explode(",",  $msg->read($STUDENT_STUDENTSTATUS));

					for ($j = 0; $j < count($reg[0]); $j++)
						if ( $row[6] == $reg[0][$j] )
							print("<option selected=\"selected\" value=\"".$reg[0][$j]."\">".$reg[1][$j]."</option>");
						else
							print("<option value=\"".$reg[0][$j]."\">".$reg[1][$j]."</option>");

			print("
					</select>
					</label>
		                </td>
		                <td class=\"align-center\"><label for=\"dlg_$i\"><input type=\"checkbox\" id=\"dlg_$i\" name=\"dlg[$i]\" value=\"O\" $check1 /></label></td>
		                <td class=\"align-center\"><label for=\"brs_$i\"><input type=\"checkbox\" id=\"brs_$i\" name=\"brs[$i]\" value=\"O\" $check2 /></label></td>
		                <td class=\"align-center\"><label for=\"cnx_$i\"><input type=\"checkbox\" id=\"cnx_$i\" name=\"cnx[$i]\" value=\"$row[3]\" $check3 /></label></td>
		                <td class=\"align-center\"><label for=\"old_$i\"><input type=\"checkbox\" id=\"old_$i\" name=\"old[$i]\" value=\"$row[3]\" $chkold /></label></td>
				  </tr>
				");

			$i++;
			$row = remove_magic_quotes(mysqli_fetch_row($result));
			}

		print("
	            </table>

			<hr />

			<table class=\"width100\">
			  <tr>
	                <td style=\"width:10%;\" class=\"valign-middle align-center\">
				<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($STUDENT_INPUTOK)."\" />
	                </td>
	                <td class= \"valign-middle\">
				". $msg->read($STUDENT_VALIDATE) ."
	                </td>
			  </tr>
	            </table>

			<p class=\"hidden\"><input type=\"hidden\" name=\"IDsel\"    value=\"$IDsel\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDalpha\"  value=\"$IDalpha\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"IDcentre\" value=\"$IDcentre\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"idcentre\" value=\"$IDc\" /></p>
		   </form>
			");
	?>

</div>
