<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2009 by Dominique Laporte(C-E-D@wanadoo.fr)
	 Copyright (c) 2019 by Thomas Dazy (contact@thomasdazy.fr)

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
 *		module   : notes_post.php
 *		projet   : la page de saisie des bulletins
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 6/12/09
 *		modif    :
 *					 29/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7 et maj du thème
 */

 if ($_SESSION['CnxGrp'] < 2) exit();

$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;

$IDclass  = ( @$_POST["IDclass"] )			// Identifiant de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$IDmat    = ( @$_POST["IDmat"] )			// Identifiant de la matière
	? (int) $_POST["IDmat"]
	: (int) @$_GET["IDmat"] ;
$year     = ( @$_POST["year"] )			// année
	? (int) $_POST["year"]
	: (int) (@$_GET["year"] ? $_GET["year"] : getParam('START_Y')) ;
$period   = ( @$_POST["period"] )			// trimestre
	? (int) $_POST["period"]
	: (int) (@$_GET["period"] ? $_GET["period"] : getParam('periode_courante')) ;

if (isset($_POST['IDuv'])) $IDuv = $_POST['IDuv'];
if (isset($_POST['IDmat'])) $IDpma = $_POST['IDmat'];


if (isset($IDuv) && $IDuv != '') {
	$IDmat = "";
	$IDpma = getPMAIDByUVID($IDuv);
}
else
{
	if (isset($_POST['IDmat'])) $IDmat = getMatIDByPMAID($_POST['IDmat']);

	// Si on veux modifier une note des années précédentes, on récupère le bon ID de classe
	if ($year != getParam('START_Y')) {
		$query  = "SELECT _IDclass FROM campus_classe WHERE _IDclass IN (SELECT _IDclass FROM user_id WHERE _ID IN (SELECT _IDuser FROM user_log WHERE _year = '".$year."' AND _niveau = '".getNiveauNumberByPMA($IDpma)."' )) order by _code DESC limit 1 ";
		$result = mysqli_query($mysql_link, $query);
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $IDclass = $row[0];
	}
	elseif (isset($IDpma)) $IDclass = getClassIDByPMAID($IDpma);
}



$setlock  = @$_POST["unlocked_x"];			// verrouillage du trimestre
$unlock   = @$_POST["locked_x"];			// déverrouillage du trimestre
$submit   = @$_POST["valid_x"];				// bouton validation
?>




<?php
	// require_once $_SESSION["ROOTDIR"]."/include/notes.php";
	// require_once $_SESSION["ROOTDIR"]."/include/postit.php";

	// lecture des droits
	$Query  = "select _IDgrpwr, _IDgrprd, _period, _IDmod, _decimal, _separator, _email, _max, _text from notes ";
	$Query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// vérification des autorisations
	verifySessionAccess(0, $auth[1]);

	// initialisation
	$status  = "-";
	$nbcols  = (int) $auth[7];


	if (isset($IDuv) && $IDuv != "") $nbcols = 1;
	if (isset($IDuv) && $IDuv != "")
	{
		$query = "SELECT `_coef`, `_note_max`, `_ID_pma` FROM `campus_examens` WHERE `_ID_exam` = '".$IDuv."' ";
		$result = mysqli_query($mysql_link, $query);
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$UVcoef 					= $row[0];
			$UVnoteMax 				= $row[1];
			$UVpoleName 			= getPoleNameByIdPole($row[2]);
			$UVClass 					= getClassYearByPMAID($row[2]);
			$graduationYear 	= getGraduationYearByClassNumber(substr($UVClass, 0, 1));
			if ($year != getParam('START_Y')) {
				$query  = "SELECT _IDclass FROM campus_classe WHERE _IDclass IN (SELECT _IDclass FROM user_id WHERE _ID IN (SELECT _IDuser FROM user_log WHERE _year = '".$year."' AND _niveau = '".getNiveauNumberByPMA($row[2])."' )) ";
				$result = mysqli_query($mysql_link, $query);
				while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $IDclass = $row[0];
			}
			else $IDclass = getClassIDByPMAID($row[2]);
		}
	}

	// l'utilisateur a validé la saisie
	if ( $submit AND $IDclass AND ($auth[0] & pow(2, $_SESSION["CnxGrp"] - 1)) ) {
		$status = $msg->read($NOTES_MODIFICATION) ." ";

		$_type = $_total = $_coef = $_visible = "";
		for ($i = 0; $i < $nbcols; $i++) {
			$_type    .= @$_POST["type_$i"].";";
			$_total   .= trim(@$_POST["total_$i"]).";";
			$_coef    .= str_replace(",", ".", trim(@$_POST["coef_$i"])).";";
			$_visible .= ( @$_POST["visible_$i"] ) ? $_POST["visible_$i"].";" : "O;" ;
		}

			if ($IDuv) $IDmatToInsert = $IDuv + 100000;
			else $IDmatToInsert = $IDpma;

		//---- nouveau bulletin élèves
		$Query  = "insert into notes_data ";
		// $Query .= "values('', '$year', '$IDclass', '$IDmat', '$period', '$_type', '$_total', '$_coef', '$_visible', 'N', '0', '0', '')";
		$Query .= "values(NULL, '$year', '$IDclass', '$IDmatToInsert', '$period', '$_type', '$_total', '$_coef', '$_visible', 'N', '0', '0', NULL)";

		if ( !mysqli_query($mysql_link, $Query) ) {
			// modification du bulletin
			$Query  = "UPDATE notes_data ";
			$Query .= "SET _type = '$_type', _total = '$_total', _coef = '$_coef', _visible = '$_visible' ";
			$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$IDmatToInsert' AND _period = '$period' ";
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

	// l'utilisateur a supprimé une note
	if ( @$_GET["del"] AND ($auth[0] & pow(2, $_SESSION["CnxGrp"] - 1)) ) {
		$IDeleve = (int) @$_GET["IDeleve"];

		$Query   = "delete from notes_items ";
		$Query  .= "where _IDdata = '".$_GET["del"]."' AND _IDeleve = '$IDeleve' ";

		mysqli_query($mysql_link, $Query);
		}

	// l'utilisateur a verrouillé la saisie
	if ( ($setlock OR $unlock) AND ($auth[0] & pow(2, $_SESSION["CnxGrp"] - 1)) ) {
		$value  = ( $setlock ) ? "O" : "N" ;

		if ($IDuv) $IDmatToInsert = $IDuv + 100000;
		else $IDmatToInsert = $IDpma;


		$Query  = "UPDATE notes_data ";
		$Query .= "SET _lock = '$value', _ID = '".$_SESSION["CnxID"]."', _IP = '".$_SESSION["CnxIP"]."', _date = '".date("Y-m-d H:i:s")."' ";
		$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$IDmatToInsert' AND _period = '$period' ";
		$Query .= "limit 1";

		mysqli_query($mysql_link, $Query);
		}

	// l'utilisateur a importé des notes
	if ( @$_POST["import"] == $msg->read($NOTES_IMPORT) )
		import_notes($IDcentre, $IDclass, $IDmat, $year, @$_FILES["UploadFile"]);
?>



<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($NOTES_TITLE); ?></h1>
	<div style="float: right; text-align: right;">
		<div>
			<a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="#" onclick="window.print();return false;">
				<i class="fas fa-print fa-sm text-white-50" title="Imprimer"></i>&nbsp;Imprimer
			</a>
		</div>
	</div>
</div>

<form id="formulaire" action="" method="post" enctype="multipart/form-data">




	<div class="card shadow mb-4">
	  <div class="card-body">





		<?php
			print("
				<p class=\"hidden\"><input type=\"hidden\" name=\"item\"   value=\"$item\" /></p>
				<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"   value=\"$cmde\" /></p>
				");
		?>

			<table class="width100">
				<?php if (!getParam('showCenter')) $showCenter = 'display: none;'; else $showCenter = ''; ?>
			  <tr style="<?php echo $showCenter; ?>">
					<td style="width:50%;" class="align-right">
						<?php print($msg->read($NOTES_CHOOSECENTER)); ?>
					</td>
					<td style="width:50%;">
						<?php echo centerSelect($IDcentre, 'formulaire', 'IDcentre', 'IDcentre', false); ?>
					</td>
			  </tr>

				<?php if (!isset($IDuv) || $IDuv == '') $show = ''; else $show = 'display: none;'; ?>

			  <tr style="<?php echo $show; ?>">
					<td class="align-right">
						<?php print($msg->read($NOTES_CHOOSEMATTER)); ?>
					</td>
					<td>
						<?php if (!isset($IDuv) || $IDuv == "") echo showPMAList("IDmat", @$IDpma); ?>
					</td>
			  </tr>

				<?php if ($IDmat != 0) $show = 'display: none;'; else $show = ''; ?>
				<tr style="<?php echo $show; ?>">
					<td class="align-right">
						<span <?php if (isset($IDuv) && $IDuv != "") echo "style=\"display: none;\""; ?>><strong>ou </strong></span>choisissez un examen/certificat :
					</td>
					<td style="width:50%;">
						<label for="IDuv">
							<?php echo getUVSelect('IDuv', 'IDuv', @$IDuv, 1, 'formulaire'); ?>
						</label>
					</td>
				</tr>


			</table>

		<hr/>

<?php
// recherche de l'élève
$query  = "select _IDclass from user_id where _ID = '".$_SESSION["CnxID"]."' limit 1 ";
$result = mysqli_query($mysql_link, $query);
$row    = ($result) ? mysqli_fetch_row($result) : 0;

if ($row[0]) {
	$IDeleve = $_SESSION["CnxID"];
	$IDclass = $row[0];
}
else $IDeleve = 0;

if (isset($IDuv) && $IDuv) $IDmatToInsert = $IDuv + 100000;
elseif (isset($IDpma)) $IDmatToInsert = $IDpma;
else $IDmatToInsert = 0;

// lecture du bulletin élèves
$query  = "select _IDdata, _lock from notes_data where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$IDmatToInsert' ";
if (!getParam('showOnlyTotalPeriodGeneral')) $query .= "AND _period = '$period' ";
$query .= "limit 1";

$result = mysqli_query($mysql_link, $query);
$row    = ($result) ? mysqli_fetch_row($result) : 0;

$IDdata = $row[0];
if ($row[1] == 'O') $lock = 'readonly'; else $lock = '';

$width  = 100 - ($nbcols * 5) - 10;
$colspan = 2;
if((isset($IDmat) && $IDmat != 0) || (isset($IDuv) && $IDuv != '')) {
	?>


	<table class="table table-striped">
		<?php
		// recherche de l'entête du tableau
		$query  = "select _type, _total, _coef, _visible from notes_data ";
		$query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$IDmatToInsert' AND _period = '$period' ";
		$query .= "order by _IDdata";
		$result = mysqli_query($mysql_link, $query);
		$row    = ($result) ? mysqli_fetch_row($result) : 0;

		if ( mysqli_num_rows($result) ) {
			$_type    = explode(";", $row[0]);
			$_total   = explode(";", $row[1]);
			$_coef    = explode(";", $row[2]);
			$_visible = explode(";", $row[3]);
		}
		else {
			$_type    = array_fill(0, $nbcols, '0');
			$_total   = array_fill(0, $nbcols, $auth[8]);
			$_coef    = array_fill(0, $nbcols, '1');
			$_visible = array_fill(0, $nbcols, 'O');
		}

		if ($lock == '' AND ($auth[0] & pow(2, $_SESSION['CnxGrp'] - 1))) $disabled = ''; else $disabled = 'disabled';

		// ---------------------------------
		// Type d'examen
		// ---------------------------------
		if (!isset($IDuv) || $IDuv == "") {
			echo '<tr>';
				echo '<td style="width: '.$width.'%;" colspan="2"></td>';
					for ($i = 0; $i < $nbcols; $i++) {
						?>
						<td class="text-center" style="width: 5%;">
							<select id="type_<?php echo $i; ?>" name="type_<?php echo $i; ?>" style="font-size: 9px;" class="custom-select align-middle" <?php echo $disabled; ?>>");
								<?php
									// affichage des types de controle
									$query  = "select _IDtype, _ident, _text from notes_type where _lang = '".$_SESSION["lang"]."' order by _IDtype ";
									$result = mysqli_query($mysql_link, $query);
									$over = '';
									while ($row = mysqli_fetch_row($result)) {
										if ($_type[$i] == $row[0]) $selected = 'selected'; else $selected = '';
										echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
										$over .= '<strong>'.$row[1].'</strong> - '.$row[2].'<br/>';
									}
								?>
							</select>
						</td>
						<?php
					}
				echo '</td>';
				// Bouton info
				echo '<td class="align-middle px-0"><a tabindex="0" class="align-middle fas fa-question link-unstyled" role="button" data-placement="left" data-toggle="popover" data-trigger="focus" data-html="true" title="'.$msg->read($NOTES_TYPE).'" data-content="'.$over.'"></a></td>';
				echo '<td></td>';
			echo '</tr>';
			echo '<tr></tr>';
		}

		$list = json_decode(getParam('periodeList'), TRUE);

		if ($IDdata) {
			if (!isset($lock) || $lock == '') {
				$mymsg = $NOTES_LOCK;
				$mylock = 'unlocked';
			}
			else {
				$mymsg = $NOTES_UNLOCK;
				$mylock = 'locked';
			}
		}


		// ---------------------------------
		// NOTES MAX
		// ---------------------------------
		echo '<tr>';
			echo '<td class="align-right" style="white-space:nowrap;" colspan="'.$colspan.'">';
				// SELECT DE PERIODE
				if (!getParam('showOnlyTotalPeriodGeneral')) {
					echo '<label class="my-1 mr-2" for="period">Periode</label>';
				  echo '<select class="custom-select my-1 mr-sm-2" name="period" id="period" onchange="document.forms.formulaire.submit()" style="width: 100px;">';
						foreach ($list as $key => $value) {
							if ($key == $period) $selected = 'selected';
							else $selected = '';
							echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
						}
					echo '</select>';
				}


				// SELECT DE L'ANNEE
				echo '<select class="custom-select my-1 mr-sm-2" name="year" id="year" onchange="document.forms.formulaire.submit()" style="width: 140px;">';
					$annees = array();
					$query = "SELECT DISTINCT _year FROM notes_data WHERE 1 ORDER BY _year ASC ";
					$result = mysqli_query($mysql_link, $query);
					while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $annees[] = $row[0];
					if (!in_array(getParam('START_Y'), $annees)) $annees[] = getParam('START_Y');

					foreach ($annees as $annee) {
						if ($year == $annee) $selected = 'selected'; else $selected = '';
						echo '<option value="'.$annee.'" '.$selected.'>'.$annee.' - '.($annee + 1).'</option>';
					}
				echo '</select>';

			echo '</td>';

			for ($i = 0; $i < $nbcols; $i++)
			{
				if (isset($IDuv) && $IDuv != "") $totalValue = $UVnoteMax; else $totalValue = $_total[$i];
				if (isset($IDuv) && $IDuv != "") $disabledNoteMax = 'readonly'; else $disabledNoteMax = '';
				if ($i > 0 && isset($IDuv) && $IDuv != "") $disabledNote = 'display: none;'; else $disabledNote = '';

				echo '<td class="text-center align-middle"><input type="text" class="form-control align-middle" id="total_'.$i.'" name="total_'.$i.'" size="1" value="'.$totalValue.'" style="'.$disabled.'" '.$disabledNoteMax.'></td>';
			}
			// Bouton info
			echo '<td class="align-middle px-0"><a tabindex="0" class="align-middle fas fa-question link-unstyled" role="button" data-placement="left" data-toggle="popover" data-trigger="focus" data-html="true" title="" data-content="'.$msg->read($NOTES_MARK).'"></a></td>';
			echo '<td></td>';
		echo '</tr>';
		echo '<tr></tr>';


		// ---------------------------------
		// COEFS
		// ---------------------------------
		echo '<tr>';
			echo '<td colspan="2" class="align-middle">';
				echo $msg->read($NOTES_CLASS).' : ';
				// affichage de la classe
				if ($year == getParam('START_Y')) $query  = "SELECT _ident FROM campus_classe WHERE _code = '".getNiveauNumberByPMA($IDpma)."' LIMIT 1 ";
				else $query  = "SELECT _ident FROM campus_classe WHERE _IDclass IN (SELECT _IDclass FROM user_id WHERE _ID IN (SELECT _IDuser FROM user_log WHERE _year = '".$year."' AND _niveau = '".getNiveauNumberByPMA($IDpma)."' )) ";
				$result = mysqli_query($mysql_link, $query);
				echo mysqli_fetch_row($result)[0];
			echo '</td>';

			for ($i = 0; $i < $nbcols; $i++)
			{
				if (isset($IDuv) && $IDuv != '') {
					$coef = $UVcoef;
					$disabledCoef = 'readonly';
				}
				else {
					$coef = $_coef[$i];
					$disabledCoef = '';
				}
				if ($i > 0 && isset($IDuv) && $IDuv != "") $disabledNote = 'display: none;'; else $disabledNote = '';
				echo '<td class="align-middle text-center"><input type="text" class="form-control" id="coef_'.$i.'" name="coef_'.$i.'" size="1" value="'.$coef.'" style="'.$disabled.'" '.$disabledCoef.'></td>';
			}


			// if (!isset($IDuv) || $IDuv == '')
			// {
				// Bouton info
				echo '<td class="align-middle px-0"><a tabindex="0" class="align-middle fas fa-question link-unstyled" style="float: left;" role="button" data-placement="left" data-toggle="popover" data-trigger="focus" data-html="true" title="" data-content="'.$msg->read($NOTES_COEF).'"></a></td>';
				echo '<th class="align-middle text-center">'.$msg->read($NOTES_MEAN).'</th>';
			// }
			// else echo '<td></td>';
		echo '</tr>';




		// affichage des élèves
		$query  = "select _name, _fname, _ID, _IDclass ";
		$query .= "from user_id ";
		$query .= "where _visible = 'O' AND `_adm` = 1 ";
		$query .= "AND _IDclass = '$IDclass' AND _IDgrp = '1' ";
		//		$query .= ( $IDeleve ) ? "AND _ID = '$IDeleve' " : "" ;
		$query .= "order by _name, _fname";

		if ($year != getParam('START_Y')) {
			$query  = "SELECT _name, _fname, _ID, _IDclass FROM user_id WHERE _visible = 'O' AND _adm = 1 ";
			$query .= "AND _ID IN (SELECT _IDuser FROM user_log WHERE _year = '".$year."' AND _niveau = '".getNiveauNumberByPMA($IDpma)."' ) ";
			$query .= "ORDER BY _name, _fname ";
		}

		$result = mysqli_query($mysql_link, $query);
		// $row    = mysqli_fetch_row($result);

		// pour statistiques
		$rnum   = $rmin   = $rmax = $rtot = $rmoy = $table = Array();

		// initialisation
		for ($i = 0; $i < $nbcols; $i++) {
			$rmin[$i] = (float) $_total[$i];
			$rnum[$i] = $rmax[$i] = $rtot[$i] = (float) 0;
		}

		$j = 0;
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			if ($submit && $IDdata) {
				$date = date('Y-m-d H:i:s');
				$IDeleve = $row[2];
				for ($i = 0; $i < $nbcols; $i++) {
					$value  = str_replace(',', '.', trim(@$_POST['value_'.$IDeleve.'_'.$i]));
					if ($value < 0 OR $value > $_total[$i] OR !is_numeric($value)) $value = '';

					$query  = "insert into notes_items values(NULL, '$IDdata', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '$date', '$IDeleve', '$i', '$value') ";
					if (!mysqli_query($mysql_link, $query)) {
						// modification du bulletin
						$query  = "UPDATE notes_items SET _ID = '".$_SESSION["CnxID"]."', _IP = '".$_SESSION["CnxIP"]."', _update = '$date', _value = '$value' where _IDdata = '$IDdata' AND _IDeleve = '$IDeleve' AND _index = '$i' limit 1 ";
						mysqli_query($mysql_link, $query);
					}
				}
			}

			// appréciation
			$href   = "item=$item&cmde=$cmde&IDcentre=$IDcentre&IDeleve=$row[2]&IDclass=$IDclass&IDmat=$IDmat&year=$year&period=$period&lang=".$_SESSION["lang"];
			$icon = '<a href="#" class="align-middle" onclick="popWin(\''.$_SESSION["ROOTDIR"].'/'.RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_comment.php?sid='.myurlencode($_SESSION['sessID'].'&'.$href).'\', \'450\', \'350\');" title="'.$msg->read($NOTES_COMMENT).'"><i class="fas fa-comment"></i></a>';
			$link = '<img class="align-middle" src="'.getUserPictureLink($row[2]).'" style="height: 23px; border-radius: 100%;"><a class="align-middle" href="'.myurlencode('index.php?item='.$item.'&cmde=show&IDcentre='.$IDcentre.'&IDclass='.$IDclass.'&IDeleve='.$row[2].'&year='.$year.'&period='.$period).'">'.$row[0].' '.$row[1].'</a>';


			$query  = "select _text from notes_text where _IDeleve = '$row[2]' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _year = '$year' AND _period = '$period' limit 1 ";
			$return = mysqli_query($mysql_link, $query);
			$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			$over   = ( strlen($myrow[0]) )
				? "<span>". str_replace(Array("\r", "\n"), Array("", "<br/>"), $myrow[0]) ."</span>"
				: "" ;

			$note   = ( $over != "" )
				? "<a href=\"#\" class=\"\"><img src=\"".$_SESSION["ROOTDIR"]."/images/document.gif\" title=\"\" alt=\"". $msg->read($NOTES_COMMENT) ."\" />$over</a>"
				: "" ;

      // On cache les boutons d'ajout de commentaire
      $note = $icon = '';

			if ($IDeleve == 0 OR $IDeleve == $row[2]) {
				echo '<tr>';
					echo '<td class="align-middle text-center" colspan="2">'.$icon.'&nbsp;'.$link.'&nbsp;'.$note.'</td>';
			}

			// initialisation
			$totcoef = $totpts = 0;

			for ($i = 0; $i < $nbcols; $i++) {
				$query  = "select _ID, _IP, _create, _update, _value from notes_items where _IDdata = '$IDdata' AND _IDeleve = '$row[2]' AND _index = '$i' ";
				$return = mysqli_query($mysql_link, $query);
				$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

				if ($myrow[4] != '') $fmt = number_format($myrow[4], 2, ".", "."); else $fmt = '';

				// Partie 1 gestion des notes manquantes
				// Vérification si manque une note à l'élève (on vérifie si les autres élèves ont une note pour le même exam et si oui alors l'élève a bien une note manquante)
				$query_check  = "select _ID, _IP, _create, _update, _value from notes_items where _IDdata = '$IDdata' AND _IDeleve != '$row[2]' AND _index = '$i' AND _value != '' ";
				$result_check = mysqli_query($mysql_link, $query_check);
				$warning = '';
				while ($row_check = mysqli_fetch_array($result_check, MYSQLI_NUM)) if ($fmt == '') $warning = 'is-invalid';

				$tabidx = (100 * ($i + 1)) + $j;

				if ($i > 0 and $IDuv != '') $disabledNote = 'display: none;'; else $disabledNote = '';

				if ($IDeleve == 0 OR  $IDeleve == $row[2]) {
          echo '<td class="align-center">';
            echo '<input type="text" tabindex="'.$tabidx.'" style="min-width: 70px;" id="value_'.$row[2].'_'.$i.'" name="value_'.$row[2].'_'.$i.'" size="1" value="'.$fmt.'" class="form-control" style="'.$disabled.' '.$disabledNote.'">';
          echo '</td>';
        }

				if ( $myrow[4] != '' ) {
					// statistiques
					$rnum[$i] += 1;
					$rtot[$i] += (float) $myrow[4];
					if ($rmin[$i] > $myrow[4]) $rmin[$i] = $myrow[4];
					if ($rmax[$i] < $myrow[4]) $rmax[$i] = $myrow[4];
					// moyenne
					$totpts += ((($myrow[4] / $_total[$i]) * 20) * $_coef[$i]);
					$totcoef += $_coef[$i];
					$table[$j][$i] = (float) $myrow[4];
				}
				else $table[$j][$i] = -1;
			}

			if ($totcoef != 0) $mean = (float) $totpts / $totcoef;
			else $mean = 0;

			$fmt = number_format($mean, 2, ".", ".");

			// stats sur la moyenne des notes
			$table[$j][$nbcols] = ( $mean != "" ) ? $mean : -1 ;

			if ($IDeleve == 0 OR  $IDeleve == $row[2]) {
          echo '<td class="px-0"></td><td class="align-middle text-center">'.$fmt.'</td>';
        echo '</tr>';
        echo '<tr></tr>';
      }
			$j++;
		}
		?>

    <tr></tr>

    <!-- ********* NOTES MINIMUM ********* -->
		<tr style="display: table-row">
  		<td class="align-middle text-right" colspan="<?php echo $colspan; ?>"><?php print($msg->read($NOTES_MIN)); ?></td>
  		<?php
  		for ($i = 0; $i < $nbcols; $i++) {
    		if ($rnum[$i]) $fmt = $rmin[$i]; else $fmt = '';
        echo '<td class="align-center">';
          echo '<input type="text" id="min_'.$i.'" name="min_'.$i.'" size="1" readonly value="'.$fmt.'" class="form-control">';
        echo '</td>';
  		}

  		$_min = 20; $_num = 0;
  		for ($j = 0; $j < count($table); $j++)
    		if ($table[$j][$nbcols] != -1) {
      		if ($_min > $table[$j][$nbcols])
      		  $_min = $table[$j][$nbcols];
      		$_num++;
    		}
  		if ($_num) $fmt = $_min; else $fmt = '';

      echo '<td class="px-0"></td>';
      echo '<td class="align-middle text-center">'.$fmt.'</td>';
  		?>
		</tr>
    <tr></tr>

    <!-- ********* NOTES MAXIMUM ********* -->
    <tr style="display: table-row">
  		<td class="align-middle text-right" colspan="<?php echo $colspan; ?>"><?php print($msg->read($NOTES_MAX)); ?></td>
  		<?php
  		for ($i = 0; $i < $nbcols; $i++) {
    		if ($rnum[$i]) $fmt = $rmax[$i]; else $fmt = '';
        echo '<td class="align-center">';
          echo '<input type="text" id="max_'.$i.'" name="max_'.$i.'" size="1" readonly value="'.$fmt.'" class="form-control">';
        echo '</td>';
  		}

  		$_max = $_num = 0;
  		for ($j = 0; $j < count($table); $j++)
    		if ($table[$j][$nbcols] != -1)
      		if ($_max < $table[$j][$nbcols]) {
        		$_max = $table[$j][$nbcols];
        		$_num++;
      		}

  		if ($_num) $fmt = $_max; else $fmt = '';
      echo '<td class="px-0"></td>';
      echo '<td class="align-middle text-center">'.$fmt.'</td>';
  		?>
		</tr>
    <tr></tr>

    <!-- ********* NOTES MOYENNES ********* -->
    <tr style="display: table-row">
  		<td class="align-middle text-right" colspan="<?php echo $colspan; ?>"><?php print($msg->read($NOTES_MEAN)); ?></td>
  		<?php
  		for ($i = 0; $i < $nbcols; $i++) {
    		if ($rnum[$i]) $fmt = number_format($rtot[$i] / $rnum[$i], 2, ".", "."); else $fmt = '';
        echo '<td class="align-center">';
          echo '<input type="text" id="mean_'.$i.'" name="mean_'.$i.'" size="1" readonly value="'.$fmt.'" class="form-control">';
        echo '</td>';
  		}

  		$_tot = $_num = (float) 0;
  		for ($j = 0; $j < count($table); $j++)
    		if ($table[$j][$nbcols] != -1) {
      		$_tot += (float) $table[$j][$nbcols];
      		$_num++;
    		}

  		if ($_num) $fmt = number_format($_tot/ $_num, 2, '.', '.'); else $fmt = '';
      echo '<td class="px-0"></td>';
      echo '<td class="align-middle text-center">'.$fmt.'</td>';
  		?>
		</tr>

		<tr></tr>

    <!-- ********* ECART TYPE ********* -->
		<tr style="display: table-row">
  		<td class="align-middle text-right" colspan="<?php echo $colspan; ?>"><?php print($msg->read($NOTES_ECARTYPE)); ?></td>
  		<?php
  		for ($i = 0; $i < $nbcols; $i++) {
    		$var = 0;
    		if ($rnum[$i]) {
      		$moy =  (float) ($rtot[$i] / $rnum[$i]);
      		for ($j = 0; $j < count($table); $j++)
        		if ( $table[$j][$i] != -1 )
        		  $var += pow($table[$j][$i] - $moy, 2);
      		$var /= $rnum[$i];
    		}
    		$fmt = ($rnum[$i])
      		? number_format(sqrt($var), $auth[4], $auth[5], '.')
      		: '';

        echo '<td>';
          echo '<input type="text" id="ectype_'.$i.'" name="ectype_'.$i.'" size="1" readonly value="'.$fmt.'" class="form-control">';
        echo '</td>';
  		}

  		$var  = 0;

  		if ($_num) {
    		$_moy = (float) ($_tot/ $_num);
    		for ($j = 0; $j < count($table); $j++)
      		if ( $table[$j][$nbcols] != -1 )
      		  $var += pow($table[$j][$nbcols] - $_moy, 2);
    		$var /= $_num;
  		}

  		$fmt = ($_num)
    		? number_format(sqrt($var), $auth[4], $auth[5], '.')
    		: '' ;

      echo '<td class="px-0"></td>';
      echo '<td class="align-middle text-center">'.$fmt.'</td>';
  		?>
		</tr>
	</table>
<?php } ?>



	  </div>
		<div class="card-footer text-muted">
			<?php if ($lock == '' && ($auth[0] & pow(2, $_SESSION['CnxGrp'] - 1)) && ($IDmat != 0 or (isset($IDuv) && $IDuv != 0))) { ?>
				<input type="submit" class="btn btn-success" name="valid_x" value="<?php echo $msg->read($NOTES_INPUTOK); ?>">
			<?php } ?>
	    <a href="<?php echo myurlencode('index.php?item='.$item.'&IDcentre='.$IDcentre.'&IDclass='.$IDclass.'&year='.$year.'&period='.$period); ?>" class="btn btn-danger"><i class="fas fa-chevron-left"></i>&nbsp;<?php echo $msg->read($NOTES_INPUTCANCEL); ?></a>
	  </div>
	</div>
</form>





<script>
	// ---------------------------------------------------------------------------
	// Fonction: Met en forme l'input si quand on en perd le focus, celui-ci est vide
	// IN:		   -
	// OUT: 		 -
	// ---------------------------------------------------------------------------
	$('input').focusout(function(){
		// Si le champ est vide on le met en rouge sinon on retire le rouge
		if ($(this).val() == '') $(this).addClass('is-invalid');
		else $(this).removeClass('is-invalid');
	})
</script>

<style>
	.is-invalid {
		background-image: none !important;
		padding: 0px !important;
	}
</style>
