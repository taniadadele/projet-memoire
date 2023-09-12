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
 *		module   : notes_show.php
 *		projet   : la page de visualisation des bulletins élève
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 19/12/09
 *		modif    :
 */


$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;

$IDclass  = ( @$_POST["IDclass"] )			// Identifiant de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$IDeleve  = ( @$_POST["IDeleve"] )			// Identifiant de l'élève
	? (int) $_POST["IDeleve"]
	: (int) @$_GET["IDeleve"] ;
$year     = ( @$_POST["year"] )			// année
	? (int) $_POST["year"]
	: (int) (@$_GET["year"] ? $_GET["year"] : getParam('START_Y')) ;
$period   = ( @$_POST["period"] )			// trimestre
	? (int) $_POST["period"]
	: (int) (@$_GET["period"] ? $_GET["period"] : 1) ;
$text     = @$_POST["text"];				// appréciation

$setlock  = @$_POST["unlocked_x"];			// verrouillage du trimestre
$unlock   = @$_POST["locked_x"];			// déverrouillage du trimestre
$submit   = @$_GET["valid_x"];			// bouton validation


if ($_SESSION['CnxGrp'] == 1) $IDeleve = $_SESSION['CnxID'];
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($NOTES_TITLE)); ?>
	</div>
</div>

<div class="maincontent">

	<?php
		require_once "include/notes.php";
		require_once "include/postit.php";
		require_once "include/student.php";

		// lecture des droits
		$Query  = "select _IDgrpwr, _IDgrprd, _period, _IDmod, _decimal, _separator, _email, _display from notes ";
		$Query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// vérification des autorisations
		verifySessionAccess(0, $auth[1]);

		// l'utilisateur a validé la saisie
		if ( $submit) {
			$mydate = date("Y-m-d H:i:s");

			$Query  = "insert into notes_text ";
			$Query .= "values('$IDeleve', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$mydate', '$IDclass', '0', '$year', '$period', '$text', 'O')";

			if ( !mysqli_query($mysql_link, $Query) ) {
				// modification du bulletin
				$Query  = "UPDATE notes_text ";
				$Query .= "SET _text = '$text', _lock = 'O', _ID = '".$_SESSION["CnxID"]."', _IP = '".$_SESSION["CnxIP"]."', _date = '$mydate' ";
				$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
				$Query .= "limit 1";

				mysqli_query($mysql_link, $Query);
				}
			}

		// l'utilisateur a verrouillé la saisie
		if ( $setlock OR $unlock ) {
			$value  = ( $setlock ) ? "O" : "N" ;
			$mydate = date("Y-m-d H:i:s");

			// modification du bulletin
			$Query  = "UPDATE notes_text ";
			$Query .= "SET _lock = '$value', _ID = '".$_SESSION["CnxID"]."', _IP = '".$_SESSION["CnxIP"]."', _date = '$mydate' ";
			$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
			$Query .= "limit 1";

			mysqli_query($mysql_link, $Query);
			}
	?>

	<form id="formulaire" action="index.php" method="get">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"   value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"   value=\"$cmde\" /></p>
			");
	?>

	<span class="btn" style="width: 100%; cursor: auto; padding: 10px 0 0 0">
		<table width="100%" cellspacing="4" cellpadding="0">
		  <tr>
			<td style="width:50%;" class="align-center">
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
				</select> <i class="icon-home"></i>
				</label>
			</td>

	<?php
		// lecture du bulletin élèves
		$Query  = "select _lock from notes_text ";
		$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$lock   = ( $row[0] == "O" ) ? "readonly=\"readonly\"" : "" ;

		// lecture du bulletin classe
		$list   = explode (",", $msg->read($NOTES_PERIODLIST));
		$quater = substr(@$list[$auth[2]], 0, 1);

		$mymsg  = ( $lock == "" ) ? $NOTES_LOCK : $NOTES_UNLOCK ;
		$mylock = ( $lock == "" ) ? "unlocked" : "locked" ;

		print("
	        <td class=\"align-center\">
			  	<label for=\"period\">
			  	<select id=\"period\" name=\"period\" onchange=\"document.forms.formulaire.submit()\">");

				// trimestres, semestres ou années
				switch ( $auth[2] ) {
					case 0 : $j = 3; break;
					case 1 : $j = 2; break;
					default: $j = 1; break;
					}

				for ($i = 1; $i <= $j; $i++)
					printf("<option value=\"$i\" %s>$quater$i</option>", ($i == $period) ? "selected=\"selected\"" : "");

		print("
			  	</select>
			  	</label>
			</td>
			<td class=\"align-center\">
			  	<label for=\"year\">
			  	<select id=\"year\" name=\"year\" onchange=\"document.forms.formulaire.submit()\">");

				// affichage des années
				$Query  = "select distinctrow notes_data._year from notes_data, campus_classe ";
				$Query .= "where campus_classe._IDcentre = '$IDcentre' ";
				$Query .= "AND campus_classe._visible = 'O' ";
				$Query .= "AND campus_classe._IDclass = notes_data._IDclass ";
				$Query .= ( $IDclass ) ? "AND campus_classe._IDclass = '$IDclass' " : "" ;
				$Query .= "order by _year";

				$result = mysqli_query($mysql_link, $Query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				if ( mysqli_num_rows($result) == 0 )
					print("<option value=\"$year\">$year</option>");

				while ( $row ) {
					printf("<option value=\"$row[0]\" %s>$row[0]</option>", ($year == $row[0]) ? "selected=\"selected\"" : "");

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}	// endwhile années


		print("</td><input type=\"hidden\" name=\"IDclass\" value=\"$IDclass\"/>");
		print("<input type=\"hidden\" name=\"IDeleve\" value=\"$IDeleve\"/>");

		print("
	              </tr>
			</table>
		</span>");

		$nbtrim  = 1;
		$width   = 65 - ($nbtrim * 5);
		$width  -= ( $auth[7][1] ) ? 5 : 0 ;
		$width  -= ( $auth[7][2] ) ? 5 : 0 ;

		print("
			<table width=\"100%\" cellspacing=\"1\" cellpadding=\"2\">
			  <tr class=\"align-center\" style=\"background-color:#C0C0C0;\">
	                <td class=\"btn-primary\" style=\"width:35%; border-radius: 4px 0 0 4px\">". $msg->read($NOTES_MATTER) ."</td>
	                <td class=\"btn-primary\" style=\"width:5%;white-space:nowrap;\">". $msg->read($NOTES_STUDENTMEAN, "$quater$period") ."</td>");

		print("
	                <td class=\"btn-primary\" style=\"width:$width%; border-radius: 0 4px 4px 0\"></td>
			  </tr>
			");

		// affichage des matières
		$Query = "SELECT DISTINCT _IDmat FROM notes_data WHERE _IDdata IN (SELECT _IDdata FROM notes_items WHERE _IDeleve = '".$IDeleve."' AND _value != '') ";
		$Query .= "AND _year = '".$year."' ";
		$Query .= "AND _period = '".$period."' ";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		// pour statisqtiques
		$rnum   = $rtot = Array('0', '0', '0', '0', '0');

		$j = 0;
		while ( $row ) {
			$bgcolor = ( $j++ % 2 ) ? "item" : "menu" ;



			// recherche professeur principal
			$Query  = "select _IDpp from campus_classe ";
			$Query .= "where _IDclass = '$IDclass' ";
			$Query .= "limit 1";

			$return = mysqli_query($mysql_link, $Query);
			$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

			$icon   = "<img src=\"".$_SESSION["ROOTDIR"]."/images/files.gif\" title=\"*\" alt=\"*\" />";

			// Si la matière est un UV:
			if ($row[0] > 100000) $matName = 'UV - '.getUVNameByID($row[0] - 100000)." (".getMatNameByIdMat(getMatIDByPMAID(getUVPMAByUVID($uvID))).")";
			else $matName = getPoleNameByIdPole(getPoleIDByPMAID($row[0])).' - '.getMatNameByIdMat(getMatIDByPMAID($row[0]));

			//---- calcul de la note
			$note   = computeMark($IDclass, $row[0], $IDeleve, $year, $period);
			// $fmt1   = ( $note != "" ) ? number_format($note, $auth[4], $auth[5], ".") : "" ;
			$fmt1 = $note;

			// statistiques
			if ( $note != "" ) {
				$rnum[0] += 1;
				$rtot[0] += (float) $note;
				}

			//---- moyenne classe
			$note   = computeClass($IDclass, $row[0], $year, $period);
			$fmt2   = ( $note != "" ) ? number_format($note, $auth[4], $auth[5], ".") : "" ;

			// statistiques
			if ( $note != "" ) {
				$rnum[1] += 1;
				$rtot[1] += (float) $note;
				}

			//---- moyenne année
			$_tot = $_num = 0;
			for ($i = 0; $i < 4; $i++) {
				$note = computeMark($IDclass, $row[0], $IDeleve, $year, $i);

				if ( $note != "" ) {
					$_num += 1;
					$_tot += (float) $note;
					}
				}

			$fmt3   = ( $_num ) ? number_format($_tot / $_num, $auth[4], $auth[5], ".") : "" ;

			// statistiques
			if ( $fmt3 != "" ) {
				$rnum[4] += 1;
				$rtot[4] += (float) ($_tot / $_num);
				}

			//---- appréciation
			$Query  = "select _text from notes_text ";
			$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '$row[0]' AND _year = '$year' AND _period = '$period' ";
			$Query .= "limit 1";

			$return = mysqli_query($mysql_link, $Query);
			$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

 			print("
				<tr class=\"$bgcolor\">
		                <td class=\"btn\" style=\"width:29%; display: table-cell; cursor: auto; padding: 8px\">$matName</td>
		                <td class=\"btn align-center\" style=\"display: table-cell; cursor: auto; padding: 8px\">$fmt1</td>");
 			print("<td class=\"btn\" style=\"display: table-cell; cursor: auto; padding: 8px\">");

/****** NOTES *********/

			// initialisation
			$totcoef = $totpts = 0;
			$nbcols = 10;
			$IDdata = $_SESSION["CnxClass"];

			// recherche de l'entête du tableau
			$Querya  = "select _type, _total, _coef, _visible from notes_data ";
			$Querya .= "where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$row[0]' AND _period = '$period' ";
			$Querya .= "order by _IDdata";

			$resulta = mysqli_query($mysql_link, $Querya);
			$rowa    = ( $resulta ) ? mysqli_fetch_row($resulta) : 0 ;

			if ( mysqli_num_rows($resulta) ) {
				$_type    = explode(";", $rowa[0]);
				$_total   = explode(";", $rowa[1]);
				$_coef    = explode(";", $rowa[2]);
				$_visible = explode(";", $rowa[3]);
				}
			else {
				$_type    = array_fill(0, $nbcols, "0");
				$_total   = array_fill(0, $nbcols, "$auth[8]");
				$_coef    = array_fill(0, $nbcols, "1");
				$_visible = array_fill(0, $nbcols, "O");
					}

			echo "<table><tr>";

			for ($i = 0; $i < $nbcols; $i++) {
				$Query  = "select notes_items._ID, notes_items._IP, notes_items._create, notes_items._update, notes_items._value ";
				$Query .= "from notes_items notes_items, notes_data notes_data ";
				$Query .= "WHERE notes_items._IDdata = notes_data._IDdata ";
				$Query .= "AND notes_items._IDeleve = '$IDeleve' AND notes_items._index = '$i' ";
				$Query .= "AND notes_data._IDmat = $row[0] ";

				$return = mysqli_query($mysql_link, $Query);
				$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

				// $fmt    = ( $myrow[4] != "" )
				// 	? number_format($myrow[4], $auth[4], $auth[5], ".")
				// 	: "" ;
				$fmt = $myrow[4];

				$tabidx = (100 * ($i + 1)) + $j;

				if($fmt != "")
				{
					echo "<td style=\"border-right: 1px solid #CCCCCC; text-align: center; width: 40px\"><strong>$fmt</strong>/$_total[$i]</td>";
				}

				if ( $myrow[4] != "" ) {
					// statistiques
					$rnum[$i] += 1;

					// Si la note n'est pas sur 20 alors on calcule la note mise sur 20:
					$noteAjusted = ($myrow[4] / $_total[$i]) * 20;

					$rtot[$i] += (float) $noteAjusted;
					if ( $rmin[$i] > $myrow[4] )
						$rmin[$i] = $myrow[4];
					if ( $rmax[$i] < $myrow[4] )
						$rmax[$i] = $myrow[4];

					// moyenne
					$totpts  += (float) ($_coef[$i] * $myrow[4]);
					$totcoef += (float) ($_coef[$i] * $_total[$i]);

					$table[$j][$i] = (float) $myrow[4];
					}
				else
					$table[$j][$i] = -1;
				}

			echo "</tr></table>";

			$mean = ( $totcoef )
				? (float) ($auth[8] * $totpts / $totcoef)
				: "" ;
			// $fmt  = ( $mean != "" )
			// 	? number_format($mean, $auth[4], $auth[5], ".")
			// 	: "" ;
			$fmt = $mean;
			
			// stats sur la moyenne des notes
			$table[$j][$nbcols] = ( $mean != "" ) ? $mean : -1 ;

			if ( $IDeleve == 0 OR  $IDeleve == $row[2] )
				print("
						<td class=\"align-center\">$fmt</td>
					</tr>");

			$j++;

/**** FIN NOTES *******/

			print("
						</td>
				</tr>");

 			$row = remove_magic_quotes(mysqli_fetch_row($result));
 			}
	?>

	           <tr>
	              <td class="btn-inverse align-center" style="padding: 8px"><strong><?php print($msg->read($NOTES_GLOBALMEAN)); ?></strong></td>

			<?php
				// moyenne générale
				$fmt = number_format($rtot[0] / $rnum[0], 2, '.', '.');
				print("<td class=\"btn-inverse align-center\" style=\"padding: 8px\"><strong>$fmt</strong></td>");
			?>

	              <td class="btn-inverse" style="padding: 8px"></td>
	           </tr>
		</table>

		<hr style="width:80%;" />

	</form>

</div>
