<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 Thomas Dazy (contact@thomasdazy.fr)

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
 *		module   : notes_show.htm
 *		projet   : la page de visualisation des bulletins élève
 *
 *		version  : 2.0
 *		auteur   : Thomas Dazy
 *		creation : 09/2019
 *		modif    :
 */

// echo '<pre>';
//  foreach ($_POST as $key => $value) {
//  	echo $key." - ".$value."<br>";
//  }
// echo '</pre>';

$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
// $IDcentre = 1;
$IDclass  = ( @$_POST["IDclass"] )			// Identifiant de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
// $IDeleve  = ( @$_POST["IDeleve"] )			// Identifiant de l'élève
// 	? (int) $_POST["IDeleve"]
// 	: (int) @$_GET["IDeleve"] ;


if (isset($_POST['IDeleve'])) $IDeleve = $_POST['IDeleve'];
elseif (isset($_GET['IDeleve'])) $IDeleve = $_GET['IDeleve'];
else $IDeleve = "";

if (isset($_POST['IDclass'])) $IDclass = $_POST['IDclass'];
elseif (isset($_GET['IDclass'])) $IDclass = $_GET['IDclass'];
else $IDclass = "";


$year     = ( @$_POST["year"] )			// année
	? (int) $_POST["year"]
	: (int) (@$_GET["year"] ? $_GET["year"] : getParam('START_Y')) ;
if ($year > getParam('START_Y')) $year = getParam('START_Y');
// if (isset($_POST['year'])) $year = $_POST['year'];
// elseif (isset($_GET['year'])) $year = $_GET['year'];
// else $year = date('Y');

// $period   = ( @$_POST["period"] )			// trimestre
// 	? (int) $_POST["period"]
// 	: (int) (@$_GET["period"] ? $_GET["period"] : 1) ;
$period   = ( @$_POST["period"] )			// trimestre
	? (int) $_POST["period"]
	: (int) (@$_GET["period"] ? $_GET["period"] : 0) ;

if ($period == "") $period = 0;

$text               = @$_POST["text"];				// appréciation
$stage_hours        = @$_POST['stage_hours'];		// Nombre d'heures de stage
$open_doors_hours        = @$_POST['open_doors_hours'];		// Nombre d'heures de portes ouvertes/salons
$redoublement_bulletin_pass = @$_POST['redoublement_bulletin_pass'];
$redoublement_bulletin_nopass = @$_POST['redoublement_bulletin_nopass'];
$bulletin_validated = $_POST['bulletin_validated']; // Validation du bulletin
if ($bulletin_validated == 'on') $bulletin_validated = '1';
else $bulletin_validated = '0';

$setlock  = @$_POST["unlocked_x"];			// verrouillage du trimestre
$unlock   = @$_POST["locked_x"];			// déverrouillage du trimestre
// $submit   = @$_POST["valid"];			// bouton validation
$submit   = @$_POST["valid_value"];			// bouton validation
// echo $year."<br>";
// echo $IDclass."<br>";
// echo $period;


if (getUserClassIDByUserID($IDeleve) != $IDclass && $IDclass != '') {
	$query = "SELECT _ID FROM user_id WHERE _IDclass = '".$IDclass."' ORDER BY `_name` ASC, _fname ASC LIMIT 1";
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$IDeleve = $row[0];
	}
}

if ($period == '' || !isset($period) || is_null($period)) $period = 0;
if (getParam('afficherQueLaPeriodeTotaleBulletin')) $period = 0;
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

		$result = mysql_query($Query, $mysql_link);
		$auth   = ( $result ) ? mysql_fetch_row($result) : 0 ;

		// vérification des autorisations
		verifySessionAccess(0, $auth[1]);

		// l'utilisateur a validé la saisie
		if ( $submit) {
			$mydate = date("Y-m-d H:i:s");
			$attr = array();

			if ($redoublement_bulletin_pass && $redoublement_bulletin_nopass) $attr['redoublement_bulletin'] = 'both';
			elseif ($redoublement_bulletin_nopass) $attr['redoublement_bulletin'] = 'redoublement';
			elseif ($redoublement_bulletin_pass) $attr['redoublement_bulletin'] = 'passage_validated';
			else $attr['redoublement_bulletin'] = 'undetermined';

			$attr = json_encode($attr);

			$Query  = "insert into notes_text ";
			$Query .= "values('$IDeleve', '".$_SESSION["CnxID"]."', '0', '$mydate', '$IDclass', '0', '$year', '$period', '$text', 'O', '$stage_hours', '$bulletin_validated', '$open_doors_hours', '$attr')";

			if ( !mysql_query($Query, $mysql_link) ) {
				// modification du bulletin
				$Query  = "UPDATE notes_text ";
				$Query .= "SET _text = '$text', _lock = 'O', _ID = '".$_SESSION["CnxID"]."', _IP = '0', _date = '$mydate' ";
				if ($stage_hours) $Query .= ", _stage_hours = '$stage_hours' ";
				if ($bulletin_validated) $Query .= ", _validated = '$bulletin_validated' ";
				if ($open_doors_hours) $Query .= ", _open_doors_hours = '$open_doors_hours' ";
				if ($attr) $Query .= ", _attr = '$attr' ";
				$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
				$Query .= "limit 1";

				mysql_query($Query, $mysql_link);
				}
			}

		// l'utilisateur a verrouillé la saisie
		if ( $setlock OR $unlock ) {
			$value  = ( $setlock ) ? "O" : "N" ;
			$mydate = date("Y-m-d H:i:s");

			// modification du bulletin
			$Query  = "UPDATE notes_text ";
			$Query .= "SET _lock = '$value', _ID = '".$_SESSION["CnxID"]."', _IP = '0', _date = '$mydate' ";
			$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
			$Query .= "limit 1";

			mysql_query($Query, $mysql_link) or die ('error');
			}

	?>

	<form id="formulaire" action="index.php" method="post">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"   value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"   value=\"$cmde\" /></p>
			");
	?>

	<?php
		if (getParam('showCenter') == 0) $showCenter = "style=\"display: none;\"";
		else $showCenter = "";


	?>
		<table width="100%" cellspacing="4" cellpadding="0" <?php echo $showCenter; ?>>
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

					$result = mysql_query($query, $mysql_link);
					$row    = ( $result ) ? remove_magic_quotes(mysql_fetch_row($result)) : 0 ;

					while ( $row ) {
						printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($IDcentre == $row[0]) ? "selected=\"selected\"" : "");

						$row = remove_magic_quotes(mysql_fetch_row($result));
						}
				?>
				</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/home.gif" title="" alt="" />
				</label>
			</td>
		  </tr>
		</table>

	<hr/>

	<?php
		// lecture du bulletin élèves
		$Query  = "select _lock from notes_text ";
		// $Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
		$Query .= "where _IDeleve = '$IDeleve' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
		$Query .= "limit 1";

		$result = mysql_query($Query, $mysql_link);
		$row    = ( $result ) ? mysql_fetch_row($result) : 0 ;

		$lock   = ( $row[0] == "O" ) ? "readonly=\"readonly\"" : "" ;

		// lecture du bulletin classe
		// $list   = explode (",", $msg->read($NOTES_PERIODLIST));
		$list = json_decode(getParam('periodeList'), TRUE);


		$quater = substr(@$list[$auth[2]], 0, 1);
		$print  =
			"<a href=\"".myurlencode($_SESSION["ROOTDIR"]."/notes_pdf.php?sid=".$_SESSION["sessID"]."&IDcentre=$IDcentre&IDclass=$IDclass&IDeleve=$IDeleve&year=$year&period=$period")."\" onclick=\"window.open(this.href, '_blank'); return false;\">
        <i class=\"fa fa-print\" style=\"font-size: 20px; color: black; padding-bottom: 10px; vertical-align: middle;\"></i>
			</a>";

		$mymsg  = ( $lock == "" ) ? $NOTES_LOCK : $NOTES_UNLOCK ;
		$mylock = ( $lock == "" ) ? "unlocked" : "locked" ;

    // if ($period != 0)
    // {
      $islock = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $auth[3] )
  			? "<input style=\"height: 20px; vertical-align: middle; padding-bottom: 10px;\" type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/$mylock.gif\" name=\"$mylock\" title=\"".$msg->read($mymsg)."\" alt=\"".$msg->read($mymsg)."\" />"
  			: "<img style=\"height: 20px; vertical-align: middle; padding-bottom: 10px;\" src=\"".$_SESSION["ROOTDIR"]."/images/$mylock.gif\" title=\"".$msg->read($mymsg)."\" alt=\"".$msg->read($mymsg)."\" />" ;
    // }
    // else $islock = "";

if (getParam('afficherQueLaPeriodeTotaleBulletin')) $display = 'display: none;';
else $display = '';
		echo "
			<table  width=\"100%\" cellspacing=\"1\" cellpadding=\"2\">
			  <tr>
        	<td style=\"text-align: right;\">
						<div style=\"display: inline-block; vertical-align: middle; padding-bottom: 10px; ".$display."\">Periode :</div>
				  	<label for=\"period\" style='".$display."'>
				  		<select id=\"period\" name=\"period\" onchange=\"document.forms.formulaire.submit()\">";

								// Liste des périodes
                if ($period == 0) $selected = "selected";
                else $selected = "";
                echo "<option value=\"0\" $selected>Tout</option>";
								foreach ($list as $key => $value) {
									if ($key == $period) $selected = "selected";
									else $selected = "";
									echo "<option value=\"$key\" $selected>$value</option>";
								}

		// Affichage de l'année
		echo "
				  		</select>
			  		</label>
            $islock
				  		-
			  		<label for=\"year\">
			  			<select id=\"year\" name=\"year\" onchange=\"document.forms.formulaire.submit()\">";

								// affichage des années
								$Query  = "select distinctrow notes_data._year from notes_data, campus_classe ";
								$Query .= "where campus_classe._IDcentre = '$IDcentre' ";
								$Query .= "AND campus_classe._visible = 'O' ";
								// $Query .= "AND campus_classe._IDclass = notes_data._IDclass ";
								// $Query .= ( $IDclass ) ? "AND campus_classe._IDclass = '$IDclass' " : "" ;

								$Query .= 'AND _IDdata IN (SELECT _IDdata FROM notes_items WHERE _IDeleve = '.$IDeleve.')';

								$Query .= "order by _year";

								$result = mysql_query($Query, $mysql_link);
								$row    = ( $result ) ? remove_magic_quotes(mysql_fetch_row($result)) : 0 ;

								if ( mysql_numrows($result) == 0 )
									print("<option value=\"$year\">$year</option>");

								while ( $row )
								{
									printf("<option value=\"$row[0]\" %s>$row[0]</option>", ($year == $row[0]) ? "selected=\"selected\"" : "");

									$row = remove_magic_quotes(mysql_fetch_row($result));
								}	// endwhile années

		echo "
		  				</select>
			  		</label>
			  			|
			  		<label for=\"IDclass\">
			  			<select id=\"IDclass\" name=\"IDclass\" onchange=\"document.forms.formulaire.submit()\">";

				// affichage des classes
				$Query  = "select _IDclass, _ident from campus_classe ";
				$Query .= "where _IDcentre = '$IDcentre' ";
				$Query .= "AND _visible = 'O' ";
				$Query .= "order by _IDclass";

				$result = mysql_query($Query, $mysql_link);
				$row    = ( $result ) ? remove_magic_quotes(mysql_fetch_row($result)) : 0 ;

				while ( $row ) {
					printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($IDclass == $row[0]) ? "selected=\"selected\"" : "");

					$row = remove_magic_quotes(mysql_fetch_row($result));
				}	// endwhile classe

		print("
			  	</select>
			  	</label>
				-
			  	<label for=\"IDeleve\">
			  	<select id=\"IDeleve\" name=\"IDeleve\" onchange=\"document.forms.formulaire.submit()\">");

				// affichage des élèves
				$query  = "select _ID, _name, _fname ";
				$query .= "from user_id ";
				$query .= "where _visible = 'O' ";
				$query .= "AND _IDclass = '$IDclass' AND _IDgrp = '1' ";
				$query .= "order by _name, _fname";

				$result = mysql_query($query, $mysql_link);
				$row    = remove_magic_quotes(mysql_fetch_row($result));

				while ( $row ) {
					printf("<option value=\"$row[0]\" %s>$row[1] $row[2]</option>", ($IDeleve == $row[0]) ? "selected=\"selected\"" : "");

					$row = remove_magic_quotes(mysql_fetch_row($result));
					}	// endwhile élèves

		print("
			  	</select>
			  	</label> $print
	                </td>
	              </tr>
			</table>");







	$current_page_print = false;

	// On affiche le tableau des notes (bulletin de notes)
	$notes_table = getStudentNotesTable($year, $IDeleve, $period);
	echo $notes_table['table'];


                              ###    ##     ## ######## ########  ########
                             ## ##   ##     ##    ##    ##     ## ##
                            ##   ##  ##     ##    ##    ##     ## ##
####### ####### #######    ##     ## ##     ##    ##    ########  ######      ####### ####### #######
                           ######### ##     ##    ##    ##   ##   ##
                           ##     ## ##     ##    ##    ##    ##  ##
                           ##     ##  #######     ##    ##     ## ########

?>
<?php if (getParam('centreCity') != 1) { ?>

	<!-- COMMENTAIRES ET HEURES DE STAGES -->
	<?php
		// On récupère le texte du commentaire et le nombre d'heures de stage
		$query_text = "SELECT _text, _stage_hours, _validated, _open_doors_hours, _attr FROM notes_text WHERE _IDeleve = '".$IDeleve."' AND _year = '".$year."' AND _period = '".$period."' ";
		$text = '';
		$stage_hours = $open_doors_hours = 0;
		$attr = array();
		$result_text = mysql_query($query_text);
		while ($row_text = mysql_fetch_array($result_text, MYSQL_NUM)) {
			$text               = $row_text[0];
			$stage_hours        = $row_text[1];
			$bulletin_validated = $row_text[2];
			$open_doors_hours   = $row_text[3];
			$attr               = json_decode($row_text[4], true);
		}
	?>
	<hr>
	<table style="width: 100%;">
		<tr>
			<?php if (getParam('afficherHeureStageQue1AnneeBulletin') && getNiveauNumberByUserID($IDeleve) != 1) $display = 'display: none !important;'; else $display = ''; ?>
			<td style="vertical-align: top; width: 50%; <?php echo $display; ?>">
				<strong>Stage</strong>
				<br>
				<div style="vertical-align: middle;">
					Heures de stage effectuées : <input type="number" max="999" min="0" name="stage_hours" value="<?php echo ($stage_hours != "") ? $stage_hours : '0'; ?>" <?php echo $lock; ?>> heures
				</div>


				<?php if (getParam('afficherHeuresSalonBulletin')) { ?>
					<div style="vertical-align: middle;">
						Heures portes ouvertes/salons : <input type="number" max="999" min="0" name="open_doors_hours" value="<?php echo ($open_doors_hours != "") ? $open_doors_hours : '0'; ?>" <?php echo $lock; ?>> heures
					</div>
				<?php } ?>


				<?php if (getParam('afficherRadioPassageRedoublementBulletin') && $period == 0) { ?>
					<div>
						<input type="checkbox" id="passage_validated" name="redoublement_bulletin_pass" value="passage_validated" <?php echo $lock; if ($lock) echo ' disabled'; ?> <?php if ($attr['redoublement_bulletin'] == 'passage_validated' || $attr['redoublement_bulletin'] == 'both') echo 'checked'; ?>>
						<label for="passage_validated">Passage en année supérieure</label>
					</div>
					<div>
						<input type="checkbox" id="redoublement" name="redoublement_bulletin_nopass" value="redoublement" <?php echo $lock; if ($lock) echo ' disabled'; ?> <?php if ($attr['redoublement_bulletin'] == 'redoublement' || $attr['redoublement_bulletin'] == 'both') echo 'checked'; ?>>
						<label for="redoublement">Redoublement</label>
					</div>
				<?php } ?>



				<?php if (getParam('canValidateSignatureBulletin')) { ?>
					<br>
					<strong>Validation</strong>
					<div style="vertical-align: middle;">
						Est-ce que le bulletin est validé : <input style="margin-bottom: 6px;" type="checkbox" name="bulletin_validated"  <?php echo $lock; if ($lock) echo ' disabled'; ?> <?php if ($bulletin_validated) echo 'checked'; ?>>
					</div>
				<?php } ?>
			</td>
			<?php if (!getParam('afficherAppreciationBulletin')) $display = 'display: none !important;'; else $display = ''; ?>
			<td style="vertical-align: top; <?php echo $display; ?>">
				<label for="text" style="width: 100%;">
					<?php // echo $msg->read($NOTES_CLASSCOUNCIL); ?>
					<?php echo '<strong>'.getParam('intituleRemarqueBulletin').'</strong>'; ?>
					<br>
					<?php echo "<textarea id=\"text\" name=\"text\" style=\"width: 100%;\" rows=\"6\" cols=\"40\" $lock>$text</textarea>"; ?>
				</label>
			</td>

		</tr>
	</table>

	<hr />

	<!-- CERTIFICATS -->
	<table style="width: 100%;">
		<tr>
			<td style="padding: 0px !important; /* border-right: 1px solid grey; */ padding-right: 5px !important; width: 30%; vertical-align: top;">
				<?php echo getCertificatTable($year, $IDeleve, getParam('modeCertificatTableBulletin')); ?>
			</td>

			<?php
			// Historique par pôle
			?>
			<td style="vertical-align: top; padding: 0px !important;">
				<?php echo getPoleAndMoyHistoryTable($year, $IDeleve, getParam('modePoleAndHistoryTableBulletin'), $notes_table['data']); ?>
			</td>
		</tr>
	</table>

	<?php echo getAbsenceTable($year, $IDeleve, getParam('modeAbsentTableBulletin')); ?>
<?php } ?>










<?php
                           ##       ##    ##  #######  ##    ##
                           ##        ##  ##  ##     ## ###   ##
                           ##         ####   ##     ## ####  ##
####### ####### #######    ##          ##    ##     ## ## ## ##    ####### ####### #######
                           ##          ##    ##     ## ##  ####
                           ##          ##    ##     ## ##   ###
                           ########    ##     #######  ##    ##
?>

<?php if (getParam('centreCity') == 1) { ?>
	<br>
	<?php echo getCertificatTable($year, $IDeleve, getParam('modeCertificatTableBulletin')); ?>
	<br>
	<?php echo getPoleAndMoyHistoryTable($year, $IDeleve, getParam('modePoleAndHistoryTableBulletin'), $notes_table['data']); ?>
	<br>

<!-- *************** ABSENCES *************** -->
<?php
	if ($current_page_print) $mode = 2;
	else $mode = getParam('modeAbsentTableBulletin');
	// else $mode = 1;
	echo getAbsenceTable($year, $IDeleve, $mode);
?>





	<!-- *************** ZONES DE SAISIE *************** -->
	<?php
		// On récupère le texte du commentaire et le nombre d'heures de stage
		$query_text = "SELECT _text, _stage_hours, _validated, _open_doors_hours, _attr FROM notes_text WHERE _IDeleve = '".$IDeleve."' AND _year = '".$year."' AND _period = '".$period."' ";
		$text = '';
		$stage_hours = 0;
		$result_text = mysql_query($query_text);
		while ($row_text = mysql_fetch_array($result_text, MYSQL_NUM)) {
			$text               = $row_text[0];
			$stage_hours        = $row_text[1];
			$bulletin_validated = $row_text[2];
			$open_doors_hours   = $row_text[3];
			$attr               = json_decode($row_text[4], true);
		}
	?>
	<hr>
	<table style="width: 100%;">
		<tr>
			<?php if (getParam('afficherHeureStageQue1AnneeBulletin') && getNiveauNumberByUserID($IDeleve) != 1) $display = 'display: none !important;'; else $display = ''; ?>
			<td style="vertical-align: top; width: 50%; <?php echo $display; ?>">
				<strong>Stage</strong>
				<br>
				<div style="vertical-align: middle;">
					Heures de stage effectuées : <input type="number" max="999" min="0" name="stage_hours" value="<?php echo ($stage_hours != "") ? $stage_hours : '0'; ?>" <?php echo $lock; ?>> heures
				</div>

				<?php if (getParam('afficherHeuresSalonBulletin')) { ?>
					<div style="vertical-align: middle;">
						Heures portes ouvertes/salons : <input type="number" max="999" min="0" name="open_doors_hours" value="<?php echo ($open_doors_hours != "") ? $open_doors_hours : '0'; ?>" <?php echo $lock; ?>> heures
					</div>
				<?php } ?>


				<?php if (getParam('afficherRadioPassageRedoublementBulletin') && $period == 0) { ?>
					<div>
						<input type="checkbox" id="passage_validated" name="redoublement_bulletin_pass" value="passage_validated" <?php echo $lock; if ($lock) echo ' disabled'; ?> <?php if ($attr['redoublement_bulletin'] == 'passage_validated') echo 'checked'; ?>>
						<label for="passage_validated">Passage en année supérieure</label>
					</div>
					<div>
						<input type="checkbox" id="redoublement" name="redoublement_bulletin_nopass" value="redoublement" <?php echo $lock; if ($lock) echo ' disabled'; ?> <?php if ($attr['redoublement_bulletin'] == 'redoublement') echo 'checked'; ?>>
						<label for="redoublement">Redoublement</label>
					</div>
				<?php } ?>



				<?php if (getParam('canValidateSignatureBulletin')) { ?>
					<br>
					<strong>Validation</strong>
					<div style="vertical-align: middle;">
						Est-ce que le bulletin est validé : <input style="margin-bottom: 6px;" type="checkbox" name="bulletin_validated"  <?php echo $lock; if ($lock) echo ' disabled'; ?> <?php if ($bulletin_validated) echo 'checked'; ?>>
					</div>
				<?php } ?>
			</td>
			<?php if (!getParam('afficherAppreciationBulletin')) $display = 'display: none !important;'; else $display = ''; ?>
			<td style="vertical-align: top; <?php echo $display; ?>">
				<label for="text" style="width: 100%;">
					<?php // echo $msg->read($NOTES_CLASSCOUNCIL); ?>
					<?php echo '<strong>'.getParam('intituleRemarqueBulletin').'</strong>'; ?>
					<br>
					<?php echo "<textarea id=\"text\" name=\"text\" style=\"width: 100%;\" rows=\"6\" cols=\"40\" $lock>$text</textarea>"; ?>
				</label>
			</td>

		</tr>
	</table>




<?php } ?>






















<hr>


<?php
// Boutons suivant/précédent
$query = "SELECT _ID FROM user_id WHERE _IDclass = '".getUserClassIDByUserID($IDeleve)."' ORDER BY _name ASC ";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	if ($backID != "" && isset($backID) && $nextID == "") $nextID = $row[0];

	if ($row[0] == $IDeleve && isset($oldID)) $backID = $oldID;
	elseif ($row[0] == $IDeleve) $backID = $IDeleve;
	$oldID = $row[0];
}

if ($lock != "")
{
	echo "<div style=\"width: 100%; min-height: 30px;\">";
		echo "<div style=\"float: left;\">";
			if ($backID != $IDeleve) echo "<a class=\"btn btn-default\" href=\"?item=60&cmde=show&IDcentre=".$IDcentre."&IDclass=".getUserClassIDByUserID($backID)."&IDeleve=".$backID."&year=".$year."&period=".$period."\"><i class=\"fa fa-chevron-left\"></i>&nbsp;".getUserNameByID($backID)."</a>";
		echo "</div>";

		echo "<div style=\"float: right;\">";
			if ($nextID != $IDeleve) echo "<a class=\"btn btn-default\" href=\"?item=60&cmde=show&IDcentre=".$IDcentre."&IDclass=".getUserClassIDByUserID($nextID)."&IDeleve=".$nextID."&year=".$year."&period=".$period."\">".getUserNameByID($nextID)."&nbsp;<i class=\"fa fa-chevron-right\"></i></a>";
		echo "</div>";
	echo "</div>";
}



?>








        <?php


					// On stoque la moyenne des pôles et la moyenne générale en session parce que j'arrive pas à les passer en post avec les vérifs de sécuriré
					$_SESSION['moyenne_pole'] = json_encode($moyennePole);
					$_SESSION['moyenne_gene'] = $moyenneGene;
					// echo "<input type=\"hidden\" name=\"moyenne_pole\" value=\"".json_encode($moyennePole)."\">";



          ?>
      <!-- </tr> -->
    <!-- </table> -->

		<hr />

		<input type="hidden" id="valid_value" name="valid_value" value="0">
		<input type="submit" id="validateButton" style="display: none;" />
		<table width="100%" cellspacing="0" cellpadding="2">
		<?php
			if ( $lock == "" )
				print("
			           <tr>
			              <td style=\"width:10%;\" class=\"valign-middle align-center\">
			              	<input type=\"button\" name=\"valid\" onclick=\"validateFully()\" class=\"btn btn-success\" value=\"Valider\" />
			              </td>
			              <td class= \"valign-middle\">
			              	".$msg->read($NOTES_MODIFY)."
			              </td>
			           </tr>");
		?>
	           <tr>
	              <td style="width:10%;" class="valign-middle align-center">
									<!-- <?php print("<a href=\"".myurlencode("index.php?item=$item&IDcentre=$IDcentre&IDclass=$IDclass&year=$year&period=$period")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($NOTES_INPUTCANCEL)."\" />"); ?></a> -->
									<a href="<?php echo myurlencode("index.php?item=$item&IDcentre=$IDcentre&IDclass=$IDclass&year=$year&period=$period"); ?>" class="btn btn-danger">Fermer</a>
	              </td>
	              <td class="valign-middle">
	              	<?php print($msg->read($NOTES_BACK)); ?>
	              </td>
	           </tr>
		</table>

	</form>

</div>


<style>
.data_td {
  text-align: center;
}
</style>

<script>

function validateFully() {
	jQuery('#valid_value').val('1');
	jQuery('#validateButton').click();
}
// On affiche la moyenne des pôles au bon endroit dans le tableau
jQuery(document).ready(function() {
	jQuery('.pole_moyenne_move').each(function() {
		var mat_key = jQuery(this).attr('mat_key');
		var moyenne = jQuery(this).attr('moyenne');
		var valide = jQuery(this).attr('valide');
		if (valide == 'check') valide = '<i class="fa fa-check"></i>';
		jQuery('#validate_' + mat_key).html(valide);
		jQuery('#moyenne_' + mat_key).html(moyenne + '/20');

		if (moyenne >= <?php echo getParam('note_max_rattrapage'); ?> || moyenne < <?php echo getParam('note_min_rattrapage'); ?>) {
			jQuery('#validate_' + mat_key).removeClass('ratt_force_pass');
		}

		<?php if (getParam('afficherNotesMoinsDeDixEnRougeBulletin')) { ?>
			if (moyenne < 10) {
				jQuery('#moyenne_' + mat_key).html('<span style="color: red;">' + moyenne + '/20</span>');
			}
		<?php } ?>
		<?php if (getParam('afficherMoyennePolesBleuBulletin')) { ?>
			jQuery('#moyenne_' + mat_key).html('<span style="color: blue;">' + moyenne + '/20</span>');

		<?php } ?>

	});
});





<?php if (getParam('activateForceValidateMatPoleFunction')) { ?>
	// Quand on clique sur un intitulé de rattrapage on propose de forcer le passage de la matière
	jQuery(document).ready(function(){
		jQuery('.ratt_force_pass').click(function(){
			var mat = jQuery(this).attr('mat');


			jQuery.ajax({
				url : 'include/fonction/ajax/notes/force_student_no_rattrapage.php',
				type : 'POST', // Le type de la requête HTTP, ici devenu POST
				data : 'mat=' + mat + '&IDeleve=<?php echo $IDeleve; ?>&year=<?php echo $year; ?>&period=<?php echo $period; ?>&action=checkIfAlreadyForced',
				dataType : 'html', // On désire recevoir du HTML
				success : function(code_html, statut){ // code_html contient le HTML renvoyé
					// Si la matière/pole à déjà été validée (forcée)
					if (code_html == 'true') {
						if (confirm('Êtes vous sûr de vouloir rétablir le rattrapage pour cette matière ?')) {
							jQuery.ajax({
								url : 'include/fonction/ajax/notes/force_student_no_rattrapage.php',
								type : 'POST', // Le type de la requête HTTP, ici devenu POST
								data : 'mat=' + mat + '&IDeleve=<?php echo $IDeleve; ?>&year=<?php echo $year; ?>&period=<?php echo $period; ?>&action=removeForcedRattrapage',
								dataType : 'html', // On désire recevoir du HTML
								success : function(code_html, statut){ // code_html contient le HTML renvoyé
									jQuery('#formulaire').submit(); // On reload la page avec les filtres
								}
							});
						}
					}
					else {
						if (confirm('Êtes vous sûr de vouloir valider cette matière malgré la moyenne ?')) {

							jQuery.ajax({
								url : 'include/fonction/ajax/notes/force_student_no_rattrapage.php',
								type : 'POST', // Le type de la requête HTTP, ici devenu POST
								data : 'mat=' + mat + '&IDeleve=<?php echo $IDeleve; ?>&year=<?php echo $year; ?>&period=<?php echo $period; ?>&action=setForcedRattrapage',
								dataType : 'html', // On désire recevoir du HTML
								success : function(code_html, statut){ // code_html contient le HTML renvoyé
									jQuery('#formulaire').submit(); // On reload la page avec les filtres
								}
							});
						}
					}
				}
			});
		});
	});
<?php } ?>

</script>

<style>
	/* On retire les bordures des lignes de nom de pôles */
	.pole_name_row > td {
		border: none;
	}


	<?php if (getParam('afficherGrisColonnesEleveBulletin')) { ?>
		.student_datas {
			background-color: #C0C0C0;
		}
	<?php } ?>



	/* En-tête de tableau */
	.table_header {
		background-color: #C0C0C0;
		font-weight: bold;
	}

	.table_header_2 {
		background-color: #E0E0E0;
		font-weight: bold;
		text-align: center;
		padding: 0 3px;
	}

	.note_certificat {
		padding: 5px;
	}
</style>
