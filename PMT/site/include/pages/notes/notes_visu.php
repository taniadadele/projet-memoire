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
 *		module   : notes_visu.php
 *		projet   : la page de visualisation des bulletins par classe
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 12/12/09
 *		modif    : 
 */


$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;

$IDclass  = ( @$_POST["IDclass"] )			// Identifiant de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$year     = ( @$_POST["year"] )			// année
	? (int) $_POST["year"]
	: (int) (@$_GET["year"] ? $_GET["year"] : date("Y")) ;
$period   = ( @$_POST["period"] )			// trimestre
	? (int) $_POST["period"]
	: (int) (@$_GET["period"] ? $_GET["period"] : 1) ;

$setlock  = @$_POST["unlocked_x"];			// verrouillage du trimestre
$unlock   = @$_POST["locked_x"];			// déverrouillage du trimestre
$submit   = @$_POST["valid_x"];			// bouton validation


//---------------------------------------------------------------------------
function getindex($array, $value)
{
/*
 * fonction :	recherche l'index d'une valeur dans un tableau
 * in :		$array : tableau, $value : valeur
 * out :		index [1..n] si trouvé, vide sinon
 */

	for ($i = 0; $i < count($array); $i++)
		if ( $array[$i] == $value )
			return $i+1;

	return "";
}
//---------------------------------------------------------------------------
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

		// lecture des droits
		$Query  = "select _IDgrpwr, _IDgrprd, _period, _IDmod, _decimal, _separator, _email from notes ";
		$Query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		// vérification des autorisations
		verifySessionAccess(0, $auth[0]);

		// initialisation
		$nbcols  = 14;

		// l'utilisateur a validé la saisie
		if ( $submit ) {
			$_coef = $_visible = "";
			for ($i = 0; $i < $nbcols; $i++) {
				$_coef    .= str_replace(",", ".", trim(@$_POST["coef_$i"])).";";
				$_visible .= ( @$_POST["visible_$i"] ) ? $_POST["visible_$i"].";" : "O;" ;
				}

			//---- nouveau bulletin classe
			$Query  = "insert into notes_lock ";
			$Query .= "values('', '$year', '$IDclass', '$period', '$_coef', '$_visible', 'N')";

			if ( !mysqli_query($mysql_link, $Query) ) {
				// modification du bulletin
				$Query  = "UPDATE notes_lock ";
				$Query .= "SET _coef = '$_coef', _visible = '$_visible' ";
				$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _period = '$period' ";
				$Query .= "limit 1";

				mysqli_query($mysql_link, $Query);
				}
			}

		// l'utilisateur a verrouillé le bulletin
		if ( $setlock OR $unlock ) {
			$value  = ( $setlock ) ? "O" : "N" ;
			$_coef  = $_visible = "";
			for ($i = 0; $i < $nbcols; $i++) {
				$_coef    .= str_replace(",", ".", trim(@$_POST["coef_$i"])).";";
				$_visible .= ( @$_POST["visible_$i"] ) ? $_POST["visible_$i"].";" : "O;" ;
				}

			$Query  = "UPDATE notes_data ";
			$Query .= "SET _lock = '$value', _ID = '".$_SESSION["CnxID"]."', _IP = '".$_SESSION["CnxIP"]."', _date = '".date("Y-m-d H:i:s")."' ";
			$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _period = '$period' ";
			$Query .= "limit 1";

			if ( mysqli_query($mysql_link, $Query) ) {
				$Query  = "insert into notes_lock ";
				$Query .= "values('', '$year', '$IDclass', '$period', '$_coef', '$_visible', '$value')";

				if ( !mysqli_query($mysql_link, $Query) ) {
					$Query  = "update notes_lock ";
					$Query .= "SET _lock = '$value' ";
					$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _period = '$period' ";
					$Query .= "limit 1";

					mysqli_query($mysql_link, $Query);
					}
				}
			}
	?>

	<form id="formulaire" action="index.php" method="post">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"   value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"   value=\"$cmde\" /></p>
			");
	?>

		<table class="width100">
		  <tr>
			<td style="width:50%;" class="align-right">
				<?php print($msg->read($NOTES_CHOOSECENTER)); ?>
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
				</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/home.gif" title="" alt="" />
				</label>
			</td>
		  </tr>
		</table>

	<hr/>

	<?php
		// lecture du bulletin classe
		$Query  = "select _lock, _coef, _visible from notes_lock ";
		$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _period = '$period' ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		if ( mysqli_num_rows($result) ) {
			$_coef    = explode(";", $row[1]);
			$_visible = explode(";", $row[2]);
			}
		else {
			$_coef    = explode(";", "1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1");
			$_visible = explode(";", "O;O;O;O;O;O;O;O;O;O;O;O;O;O;O;O");
			}

		$lock     = ( $row[0] == "O" ) ? "readonly=\"readonly\"" : "" ;
		$disabled = ( $lock != "" ) ? "disabled=\"disabled\"" : "" ;

		$list   = explode (",", $msg->read($NOTES_PERIODLIST));
		$quater = substr(@$list[$auth[2]], 0, 1);
		$islock = "";

		$mymsg  = ( $lock == "" ) ? $NOTES_LOCK : $NOTES_UNLOCK ;
		$mylock = ( $lock == "" ) ? "unlocked" : "locked" ;
		$islock = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $auth[3] )
			? "<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/$mylock.gif\" name=\"$mylock\" title=\"".$msg->read($mymsg)."\" alt=\"".$msg->read($mymsg)."\" />"
			: "<img src=\"".$_SESSION["ROOTDIR"]."/images/$mylock\" title=\"".$msg->read($mymsg)."\" alt=\"".$msg->read($mymsg)."\" />" ;

		$width  = 100 - ($nbcols * 5) - 10;

		print("
			<table class=\"width100\">
			  <tr>
	                <td class=\"align-right\" style=\"width:$width%;white-space:nowrap;\" colspan=\"2\">
				$islock ".@$list[$auth[2]]."
			  	<label for=\"period\">
			  	<select id=\"period\" name=\"period\" onchange=\"document.forms.formulaire.submit()\">");

				// les trimestres
				for ($i = 1; $i < 4; $i++)
					printf("<option value=\"$i\" %s>$quater$i</option>", ($i == $period) ? "selected=\"selected\"" : "");

		print("
			  	</select>
			  	</label>
				-
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

		print("
			  	</select>
			  	</label>
	                </td>");

		// affichage des matières
		$Query  = "select distinctrow campus_data._IDmat, campus_data._titre, notes_data._IDdata ";
		$Query .= "from campus_data, notes_data ";
		$Query .= "where campus_data._lang = '".$_SESSION["lang"]."' ";
		$Query .= "AND notes_data._year = '$year' AND notes_data._IDclass = '$IDclass' AND notes_data._period = '$period' ";
		$Query .= "AND notes_data._IDmat = campus_data._IDmat ";
		$Query .= "order by campus_data._titre asc";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		$i      = 0;
		$_idmat = Array();
		while ( $row ) {			
			$_idmat[$i++] = $row[0];

			// recherche enseignant de la matière
			$Query  = "select _ID from notes_items ";
			$Query .= "where _IDdata = '$row[2]' ";
			$Query .= "order by _IDitems ";
			$Query .= "limit 1";

			$return = mysqli_query($mysql_link, $Query);
			$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

			$over   = "<span><strong>$row[1]</strong><br/>". getUserNameByID($myrow[0], false) ."</span>";
			$href   = "#";

			switch ( $auth[6] ) {
				case "P" :
					if ( canPost($_SESSION["CnxID"]) AND canRead($myrow[0]) )
						$href = ( $_SESSION["CnxID"] != $myrow[0] AND $_SESSION["CnxSex"] != "A" )
							? myurlencode("index.php?item=4&IDpost=".$_SESSION["CnxID"]."&IDdst=$myrow[0]&cmde=post")
							: "#" ;
					break;

				case "E" :
					// envoie d'un email
					$href = ( getUserEmailByID($myrow[0]) != "" )
						? "mailto:".getUserEmailByID($myrow[0])
						: "#" ;
					break;

				default :
					break;
				}

			print("
				<td class=\"align-center\" style=\"width:5%;\">
					<a href=\"$href\" class=\"overlib\">".substr($row[1], 0, 4)."$over</a>
				</td>");

			$row = remove_magic_quotes(mysqli_fetch_row($result));
			}	// endwhile matières

		for (; $i < $nbcols; $i++)
			print("<td class=\"align-center\" style=\"width:5%;\"></td>");

		if ( $IDclass ) {
			// recherche professeur principal
			$Query  = "select _IDpp from campus_classe ";
			$Query .= "where _IDclass = '$IDclass' ";
			$Query .= "limit 1";

			$return = mysqli_query($mysql_link, $Query);
			$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

			switch ( $auth[6] ) {
				case "P" :
					if ( canPost($_SESSION["CnxID"]) AND canRead($myrow[0]) )
						$email = ( $_SESSION["CnxID"] != $myrow[0] AND $_SESSION["CnxSex"] != "A" )
							? "<a href=\"".myurlencode("index.php?item=4&IDpost=".$_SESSION["CnxID"]."&IDdst=$myrow[0]&cmde=post")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/email.gif\" title=\"". $msg->read($NOTES_SENDMAIL, getUserNameByID($myrow[0], false)) ."\" alt=\"". $msg->read($NOTES_SENDMAIL, getUserNameByID($myrow[0], false)) ."\" /></a>"
							: "" ;
					else
						$email = "";
					break;

				case "E" :
					// envoie d'un email
					$email = ( getUserEmailByID($myrow[0]) != "" )
						? "<a href=\"mailto:".getUserEmailByID($myrow[0])."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/email.gif\" title=\"". $msg->read($NOTES_SENDMAIL, getUserNameByID($myrow[0], false)) ."\" alt=\"". $msg->read($NOTES_SENDMAIL, getUserNameByID($myrow[0], false)) ."\" /></a>"
						: "" ;
					break;

				default :
					$email = "";
					break;
				}

			$export = 
				"<a href=\"".myurlencode($_SESSION["ROOTDIR"]."/notes_csv.php?sid=".$_SESSION["sessID"]."&IDcentre=$IDcentre&IDmat=0&IDclass=$IDclass&year=$year&period=$period")."\" onclick=\"window.open(this.href, '_blank'); return false;\">".
				"<img src=\"".$_SESSION["ROOTDIR"]."/images/post-in.gif\" title=\"". $msg->read($NOTES_EXPORT) ."\" alt=\"". $msg->read($NOTES_EXPORT) ."\" />".
				"</a>";
			$print  =
				"<a href=\"".myurlencode($_SESSION["ROOTDIR"]."/notes_pdf.php?sid=".$_SESSION["sessID"]."&IDcentre=$IDcentre&IDclass=$IDclass&year=$year&period=$period")."\" onclick=\"window.open(this.href, '_blank'); return false;\">
				<img src=\"".$_SESSION["ROOTDIR"]."/images/print.gif\" title=\"". $msg->read($NOTES_PRINT) ."\" alt=\"". $msg->read($NOTES_PRINT) ."\" />
				</a>";
			}
		else
			$print = $export = $email = "";

		print("
	                <td class=\"align-center\" style=\"width:10%;white-space:nowrap;\" colspan=\"2\">$print $export $email</td>
			</tr>
			");

		print("
			  <tr style=\"background-color:#C0C0C0;\">
	                <td colspan=\"2\">
			  	<label for=\"IDclass\">
			  	<select id=\"IDclass\" name=\"IDclass\" onchange=\"document.forms.formulaire.submit()\">");

				// affichage des classes
				$Query  = "select _IDclass, _ident from campus_classe ";
				$Query .= "where _IDcentre = '$IDcentre' ";
				$Query .= "AND _visible = 'O' ";
				$Query .= "order by _IDclass";

				$result = mysqli_query($mysql_link, $Query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				print("<option value=\"0\">&nbsp;</option>");

				while ( $row ) {			
					printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($IDclass == $row[0]) ? "selected=\"selected\"" : "");

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}	// endwhile classe

		print("
			  	</select>
			  	</label> | ". $msg->read($NOTES_COEFABBR) ."
	                </td>");

		for ($i = 0; $i < $nbcols; $i++)
			print("
	                <td class=\"align-center\">
	           		<label for=\"coef_$i\"><input type=\"text\" id=\"coef_$i\" name=\"coef_$i\" size=\"1\" value=\"$_coef[$i]\" $lock /></label>
	                </td>");

		print("
	                <td class=\"align-center\">
				". $msg->read($NOTES_MEANABBR) ."
	                </td>
	                <td class=\"align-center\">
				". $msg->read($NOTES_RANK) ."
	                </td>
			</tr>
			");

		// affichage des élèves
		$query  = "select _name, _fname, _ID, _IDclass ";
		$query .= "from user_id ";
		$query .= "where _visible = 'O' ";
		$query .= "AND _IDclass = '$IDclass' AND _IDgrp = '1' ";
		$query .= "order by _name, _fname";

		$result = mysqli_query($mysql_link, $query);
		$row    = remove_magic_quotes(mysqli_fetch_row($result));

		// pour statisqtiques
		$rnum   = $rmin   = $rmax = $rtot = $rmoy = $table = Array();
		$rang   = computeRank($IDclass, $_idmat, $year, $period);

		// initialisation
		for ($i = 0; $i < $nbcols; $i++) {
			$rmin[$i] = (float) 20;
			$rnum[$i] = $rmax[$i] = $rtot[$i] = (float) 0;
			}

		$j = 0;
		while ( $row ) {
			$bgcolor = ( $j % 2 ) ? "item" : "menu" ;

			$icon   = "<img src=\"".$_SESSION["ROOTDIR"]."/images/files.gif\" title=\"*\" alt=\"*\" />";
			$link   = "<a href=\"".myurlencode("index.php?item=$item&cmde=show&IDcentre=$IDcentre&IDclass=$IDclass&IDeleve=$row[2]&year=$year&period=$period")."\">$row[0] $row[1]</a>";

 			print("
				<tr class=\"$bgcolor\">
  		                <td style=\"width:1%;\">$icon</td>
		                <td style=\"width:39%;\">$link</td>");

			// initialisation
			$totcoef = $totpts = 0;

			for ($i = 0; $i < $nbcols; $i++) {
				if ( @$_idmat[$i] ) {
					//---- appréciation
					$Query  = "select _text from notes_text ";
					$Query .= "where _IDeleve = '$row[2]' AND _IDclass = '$IDclass' AND _IDmat = '$_idmat[$i]' AND _year = '$year' AND _period = '$period' ";
					$Query .= "limit 1";

					$return = mysqli_query($mysql_link, $Query);
					$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

					$over   = ( strlen($myrow[0]) )
						? "<span>". str_replace(Array("\r", "\n"), Array("", "<br/>"), $myrow[0]) ."</span>"
						: "" ;

					//---- calcul de la note
					$note   = computeMark($IDclass, $_idmat[$i], $row[2], $year, $period);
					$fmt    = ( $note != "" ) ? number_format($note, $auth[4], $auth[5], ".") : "" ;
					}
				else
					$note   = $fmt = $over = "";

				$fmt    = ( $over != "" )
					? "<a href=\"#\" class=\"overlib\">$fmt $over</a>"
					: $fmt ;

				print("<td class=\"align-center\">$fmt</td>");

				if ( $note != "" ) {
					// statistiques
					$rnum[$i] += 1;
					$rtot[$i] += (float) $note;
					if ( $rmin[$i] > $note )
						$rmin[$i] = $note;
					if ( $rmax[$i] < $note )
						$rmax[$i] = $note;

					// moyenne
					$totpts  += (float) $note;
					$totcoef += $_coef[$i] * 20;

					$table[$j][$i] = (float) $note;
					}
				else
					$table[$j][$i] = -1;
				}

			$mean = ( $totcoef )
				? (float) (20 * $totpts / $totcoef)
				: "" ;
			$fmt  = ( $mean != "" )
				? number_format($mean, $auth[4], $auth[5], ".")
				: "" ;

			// stats sur la moyenne des notes
			$table[$j][$nbcols] = ( $mean != "" ) ? $mean : -1 ;

 			print("
 		                <td class=\"align-center\">$fmt</td>
 		                <td class=\"align-center\">". getindex($rang, $mean) ."</td>
				  </tr>
 				");

			$j++;
 			$row = remove_magic_quotes(mysqli_fetch_row($result));
 			}
	?>

	           <tr>
			<?php
				$colspan = $nbcols + 4;

				print("<td colspan=\"$colspan\"><hr/></td>");
			?>
	           </tr>

	           <tr>
	              <td class="align-right" colspan="2"><?php print($msg->read($NOTES_MIN)); ?></td>

			<?php
				// notes minimum
				for ($i = 0; $i < $nbcols; $i++) {
					$fmt  = ( $rnum[$i] )
						? number_format($rmin[$i], $auth[4], $auth[5], ".")
						: "" ;

					print("
			                <td class=\"align-center\">
			           		<label for=\"min_$i\"><input type=\"text\" id=\"min_$i\" name=\"min_$i\" size=\"1\" readonly=\"readonly\" value=\"$fmt\" /></label>
		      	          </td>");
					}

				$_min = 20; $_num = 0;
				for ($j = 0; $j < count($table); $j++)
					if ( $table[$j][$nbcols] != -1 ) {
						if ( $_min > $table[$j][$nbcols] )
							$_min = $table[$j][$nbcols];
							$_num++;
							}

				$fmt = ( $_num )
					? number_format($_min, $auth[4], $auth[5], ".")
					: "" ;

				print("<td class=\"align-left\" colspan=\"2\">$fmt</td>");
			?>
	           </tr>

	           <tr>
	              <td class="align-right" colspan="2"><?php print($msg->read($NOTES_MAX)); ?></td>

			<?php
				// notes maximum
				for ($i = 0; $i < $nbcols; $i++) {
					$fmt  = ( $rnum[$i] )
						? number_format($rmax[$i], $auth[4], $auth[5], ".")
						: "" ;

					print("
			                <td class=\"align-center\">
			           		<label for=\"max_$i\"><input type=\"text\" id=\"max_$i\" name=\"max_$i\" size=\"1\" readonly=\"readonly\" value=\"$fmt\" /></label>
		      	          </td>");
					}

				$_max = $_num = 0;
				for ($j = 0; $j < count($table); $j++)
					if ( $table[$j][$nbcols] != -1 )
						if ( $_max < $table[$j][$nbcols] ) {
							$_max = $table[$j][$nbcols];
							$_num++;
							}

				$fmt = ( $_num )
					? number_format($_max, $auth[4], $auth[5], ".")
					: "" ;

				print("<td class=\"align-left\" colspan=\"2\">$fmt</td>");
			?>
	           </tr>

	           <tr>
	              <td class="align-right" colspan="2"><?php print($msg->read($NOTES_MEAN)); ?></td>

			<?php
				// notes moyennes
				for ($i = 0; $i < $nbcols; $i++) {
					$fmt  = ( $rnum[$i] )
						? number_format($rtot[$i] / $rnum[$i], $auth[4], $auth[5], ".")
						: "" ;

					print("
			                <td class=\"align-center\">
			           		<label for=\"mean_$i\"><input type=\"text\" id=\"mean_$i\" name=\"mean_$i\" size=\"1\" readonly=\"readonly\" value=\"$fmt\" /></label>
		      	          </td>");
					}

				$_tot = $_num = (float) 0;
				for ($j = 0; $j < count($table); $j++)
					if ( $table[$j][$nbcols] != -1 ) {
						$_tot += (float) $table[$j][$nbcols];
						$_num++;
						}

				$fmt = ( $_num )
					? number_format($_tot/ $_num, $auth[4], $auth[5], ".")
					: "" ;

				print("<td class=\"align-left\" colspan=\"2\">$fmt</td>");
			?>
	           </tr>

	           <tr>
	              <td class="align-right" colspan="2"><?php print($msg->read($NOTES_ECARTYPE)); ?></td>

			<?php
				// écart type
				for ($i = 0; $i < $nbcols; $i++) {
					$var = 0;

					if ( $rnum[$i] ) {
						$moy =  (float) ($rtot[$i] / $rnum[$i]);

						for ($j = 0; $j < count($table); $j++)
							if ( $table[$j][$i] != -1 )
								$var += pow($table[$j][$i] - $moy, 2);

						$var /= $rnum[$i];
						}

					$fmt = ( $rnum[$i] )
						? number_format(sqrt($var), $auth[4], $auth[5], ".")
						: "" ;

					print("
			                <td class=\"align-center\">
			           		<label for=\"ectype_$i\"><input type=\"text\" id=\"ectype_$i\" name=\"ectype_$i\" size=\"1\" readonly=\"readonly\" value=\"$fmt\" /></label>
		      	          </td>");
					}

				$var  = 0;

				if ( $_num ) {
					$_moy = (float) ($_tot/ $_num);
					for ($j = 0; $j < count($table); $j++)
						if ( $table[$j][$nbcols] != -1 )
							$var += pow($table[$j][$nbcols] - $_moy, 2);

					$var /= $_num;
					}

				$fmt = ( $_num )
					? number_format(sqrt($var), $auth[4], $auth[5], ".")
					: "" ;

				print("<td class=\"align-left\" colspan=\"2\">$fmt</td>");
			?>
	           </tr>
		</table>

		<hr style="width:80%;" />

		<table class="width100">
		<?php
			if ( $lock == "" )
				print("
			           <tr>
			              <td style=\"width:10%;\" class=\"valign-middle align-center\">
			              	<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($NOTES_INPUTOK)."\" />
			              </td>
			              <td class= \"valign-middle\">
			              	".$msg->read($NOTES_MODIFY)."
			              </td>
			           </tr>");
		?>
	           <tr>
	              <td style="width:10%;" class="valign-middle align-center">
	              	<?php print("<a href=\"".myurlencode("index.php?item=$item&IDcentre=$IDcentre&IDclass=$IDclass&year=$year&period=$period")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($NOTES_INPUTCANCEL)."\" />"); ?></a>
	              </td>
	              <td class="valign-middle">
	              	<?php print($msg->read($NOTES_BACK)); ?>
	              </td>
	           </tr>
		</table>

	</form>

</div>