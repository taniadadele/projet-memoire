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
 *		module   : notes.php
 *		projet   : la page de visualisation des bulletins
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 6/12/09
 *		modif    :
 */


// $IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
// 	? (int) $_POST["IDcentre"]
// 	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
//
// $name     = ( @$_POST["name"] )			// nom alpha
// 	? $_POST["name"]
// 	: @$_GET["name"] ;
// $IDclass  = ( strlen(@$_POST["IDclass"]) )	// Identifiant de la classe
// 	? (int) $_POST["IDclass"]
// 	: (int) @$_GET["IDclass"] ;
// $year     = ( strlen(@$_POST["year"]) )		// année
// 	? (int) $_POST["year"]
// 	: (int) @$_GET["year"] ;
// $period   = ( strlen(@$_POST["period"]) )		// trimestre
// 	? (int) $_POST["period"]
// 	: (int) (@$_GET["period"] ? $_GET["period"] : getParam('periode_courante')) ;

$post_get = array('IDcentre', 'name', 'IDclass', 'year', 'period');
foreach ($post_get as $key => $value) {
	if (isset($_POST[$value]) && $_POST[$value] != '') $$value = $_POST[$value];
	elseif (isset($_GET[$value]) && $_GET[$value] != '') $$value = $_GET[$value];
}

$get = array('skpage', 'skshow');
foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

if (!isset($skpage)) $skpage = 1;
if (!isset($skshow)) $skshow = 1;
if (!isset($IDcentre)) $IDcentre = $_SESSION['CnxCentre'];
if (!isset($period)) $period = getParam('periode_courante');

// $submit   = @$_POST["valid_x"];			// bouton validation

// Si on a pas choisi l'année  alors on prend la plus récente dans la liste des notes
if (!isset($year) || $year == '') {
	$query  = "select distinctrow notes_data._year from notes_data, campus_classe where campus_classe._visible = 'O' AND campus_classe._IDclass = notes_data._IDclass  ";
	if (isset($IDclass) && $IDclass) $query .= "AND campus_classe._IDclass = '$IDclass' ";
	$query .= "order by _year ";
	$result = mysqli_query($mysql_link, $query);
	$years_possible = array();
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  $years_possible[] = $row[0];
		$last_possible_year = $row[0];
	}
	// if (!in_array($year, $years_possible)) $year = $last_possible_year;
	if (isset($last_possible_year)) $year = $last_possible_year;
	else $year = date('Y');
}


if (getParam('afficherQueLaPeriodeTotaleBulletin')) $period = 0;
?>




<?php
// intialisation
if (!$year) $year = getParam("START_Y");
// $href = 'IDcentre='.$IDcentre.'&name='.$name.'&IDclass='.$IDclass.'&year='.$year.'&period='.$period;
?>


<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($NOTES_TITLE); ?></h1>

  <div style="float: right; text-align: right;">
    <form id="formulaire" action="index.php" method="post" style="display: inline-block;">
			<input type="hidden" name="item" value="<?php echo $item; ?>">
			<input type="hidden" name="cmde" value="<?php echo $cmde; ?>">
			<input type="hidden" name="IDcentre" value="">
    </form>

		<form id="selection" action="index.php?item=<?php echo $item; ?>" method="post" style="display: inline-block;">
			<div class="form-row">
				<!-- Centre -->
				<div class="col">
					<?php echo centerSelect($IDcentre, 'formulaire', 'IDcentre', 'IDcentre_2', false, 'btn d-none d-sm-inline-block btn-sm shadow-sm', false); ?>
				</div>
				<!-- Ordre alpha -->
				<div class="col">
					<select id="name" name="name" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm" onchange="document.forms.selection.submit()">
						<?php
							$alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
							if ($name == '') $selected = 'selected'; else $selected = '';
							echo '<option value="" '.$selected.'>'.$msg->read($NOTES_ALL).'</option>';
							for ($i = 0; $i < 26; $i++) {
								if ($alpha[$i] == $name) $selected = 'selected'; else $selected = '';
								echo '<option value="'.$alpha[$i].'" '.$selected.'>'.$alpha[$i].'</option>';
							}
						?>
					</select>
				</div>
				<!-- Classe -->
				<div class="col">
					<select id="IDclass" name="IDclass" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm" onchange="document.forms.selection.submit()">
						<?php
						$query  = "select _IDclass, _ident from campus_classe where _IDcentre = '$IDcentre' AND _visible = 'O' ";
						if ($_SESSION['CnxGrp'] == 1) $query .= "AND _IDclass = '".$_SESSION['CnxClass']."' ";
						$query .= "order by _IDclass";
						$result = mysqli_query($mysql_link, $query);
						if ($_SESSION['CnxGrp'] != 1) echo '<option value="0">Toutes les classes</option>';
						while ($row = mysqli_fetch_row($result)) {
							if ($IDclass == $row[0]) $selected = 'selected'; else $selected = '';
							echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
						}
						?>
					</select>
				</div>
				<!-- Années -->
				<div class="col">
					<select id="year" name="year" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm" onchange="document.forms.selection.submit()">
						<?php
							$query  = "select distinctrow notes_data._year from notes_data, campus_classe where campus_classe._IDcentre = '$IDcentre' AND campus_classe._visible = 'O' AND campus_classe._IDclass = notes_data._IDclass ";
							if ($IDclass) $query .= "AND campus_classe._IDclass = '$IDclass' ";
							$query .= "order by _year";
							$result = mysqli_query($mysql_link, $query);
							if (mysqli_num_rows($result) == 0) echo '<option value="'.$year.'">'.$year.'</option>';
							while ($row = mysqli_fetch_row($result)) {
								if ($year == $row[0]) $selected = 'selected'; else $selected = '';
								echo '<option value="'.$row[0].'" '.$selected.'>'.$row[0].'</option>';
							}
						?>
			  	</select>
				</div>
				<!-- Périodes -->
				<div class="col">
					<select id="period" name="period" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm" onchange="document.forms.selection.submit()">
						<?php
							$periodList = json_decode(getParam('periodeList'), true);

							echo '<option value="0">Toute les périodes</option>';
							foreach ($periodList as $key => $value) {
								if ($key == $period) $selected = 'selected'; else $selected = '';
								echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
							}
						?>
					</select>
				</div>
			</div>
		</form>
  </div>
</div>


<?php
// Récupération des classes
$listeClasses = array();
$query = 'SELECT _IDclass, _ident FROM campus_classe WHERE _visible = "O" ';
$result = mysqli_query($mysql_link, $query);
while ($row = mysqli_fetch_row($result)) $listeClasses[$row[0]] = $row[1];
// affichage des élèves
$query  = "select _name, _fname, _ID, _IDclass from user_id where _visible = 'O' AND _IDgrp = '1' AND _IDcentre = '$IDcentre' AND _ID IN(SELECT _IDeleve FROM notes_items where 1) AND _IDclass IN(SELECT _IDclass from campus_classe where _visible = 'O') ";
if (isset($name) && $name) $query .= "AND _name like '$name%' ";
if (isset($IDclass) && $IDclass) $query .= "AND _IDclass = '$IDclass' ";
if ($_SESSION['CnxGrp'] == 1) $query .= "AND `_ID` = '".$_SESSION['CnxID']."' ";
$query .= "AND (`_adm` = 1 OR `_adm` = '255') order by _name, _fname ";

// détermination du nombre d'éléments
$result = mysqli_query($mysql_link, $query);
$nbelem = mysqli_affected_rows($mysql_link);
// On filtre en fonction de la page choisie
$query .= "LIMIT ".$MAXSHOW." OFFSET ".(($skpage * $MAXSHOW) - $MAXSHOW);
$result = mysqli_query($mysql_link, $query);

$page  = $nbelem;
$show  = 1;

if (isset($IDclass) && $IDclass) $page = $show = 1;
else {
	$page  = ( $page % $MAXPAGE )
		? (int) ($page / $MAXPAGE) + 1
		: (int) ($page / $MAXPAGE) ;
	$show  = ( $page % $MAXSHOW )
		? (int) ($page / $MAXSHOW) + 1
		: (int) ($page / $MAXSHOW) ;
}
// initialisation
$i     = 1;
$first = 1 + (($skpage - 1) * $MAXPAGE);
// mysqli_data_seek($result, $first - 1);

$j   = 0;
$max = (isset($IDclass) && $IDclass) ? $nbelem : $MAXPAGE;
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Résultats : <?php echo $nbelem; ?></h6>
  </div>
  <div class="card-body">
		<table class="table table-striped">
			<tr>
				<th style="width: 1%;"></th>
				<th>Nom</th>
				<th>Classe</th>
				<th>Nb notes</th>
				<th style="width: 1%;"></th>
			</tr>
			<?php
			while ($row = mysqli_fetch_row($result)) {
				// Si on affiche pas la promo
				if (!isset($listeClasses[$row[3]])) continue;
				// recherche du nbr de matières renseignées
				$query   = "select notes_data._IDmat, notes_items._value from notes_items, notes_data where notes_items._IDeleve = '$row[2]' AND notes_items._value != '' AND notes_data._IDdata = notes_items._IDdata AND notes_data._IDclass = '$row[3]' AND notes_data._year = '$year' ";
				if ($period != 0) $query .= "AND notes_data._period = '$period' ";
				$nbitem = 0;
				$result_2 = mysqli_query($mysql_link, $query);
				while ($row_2 = mysqli_fetch_array($result_2, MYSQLI_NUM)) {
					if ($row_2[0] > 100000)
					{
						if (isUVCertificat($row_2[0] - 100000)) $nbitem++;
					}
					else $nbitem = $nbitem + 1;
				}
				// Lien pour accéder au bulletin
				if ($_SESSION['CnxGrp'] > 1 || $_SESSION['CnxID'] == $row[2]) $link_bulletin = '<a href="'.myurlencode('index.php?item='.$item.'&cmde=show&IDcentre='.$IDcentre.'&IDclass='.$row[3].'&IDeleve='.$row[2].'&year='.$year.'&period='.$period).'"><i class="fas fa-list" title="'.$msg->read($NOTES_POST).'"></i></a>';
				else $link_bulletin = '';
				$download_bulletin_link = RESSOURCE_PATH['PAGES_FOLDER'].'notes/notes_pdf.php?IDcentre=1&IDclass='.$row[3].'&IDeleve='.$row[2].'&year='.$year.'&period='.$period;
				echo '<tr>';
					echo '<td>'.$link_bulletin.'</td>';						// Le lien pour accéder au bulletin
					echo '<td>'.$row[0].' '.$row[1].'</td>';			// Le nom + prénom de l'étudiant
					echo '<td>'.$listeClasses[$row[3]].'</td>';		// Le nom de la classe
					echo '<td>'.$nbitem.'</td>';									// Le nombre de notes
					echo '<td><a href="'.$download_bulletin_link.'" target="_blank"><i class="fas fa-download"></i></a></td>';	// Le lien pour télécharger directement le bulletin
				echo '</tr>';
				echo '<tr></tr>';
			}
			?>
		</table>
  </div>
  <div class="card-footer text-muted">
		<?php $link_infos = 'index.php?item='.(@$item).'&cmde='.(@$cmde).'&IDcentre='.(@$IDcentre).'&name='.(@$name).'&IDclass='.(@$IDclass).'&year='.(@$year).'&period='.(@$period); ?>
		<?php echo getPagination($skpage, $nbelem, $link_infos, false); ?>
  </div>
</div>




<script>
	// Gère les formulaires de filtres
	$('#IDcentre_2').on('change', function(){
		var value = $(this).val();
		$('input[name="IDcentre"]').val(value);
		$('#formulaire').submit();
	});
</script>
