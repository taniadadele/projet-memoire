<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : edt.php
 *		projet   : la page des emplois du temps des salles
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 7/10/06
 *		modif    :
 */


$IDcentre = ( @$_POST["IDcentre"] )					// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;

// type d'edt
if (isset($_GET['IDedt'])) $IDedt = $_GET['IDedt'];
elseif (getUserParam($_SESSION['CnxID'], 'edt_id') !== 'null') $IDedt = getUserParam($_SESSION['CnxID'], 'edt_id');
if (isset($IDedt)) setUserParam($_SESSION['CnxID'], 'edt_id', $IDedt, 'Mode d\'affichage de l\'EDT');


$_SESSION["IDedt"] = $IDedt;

if (isset($_GET['IDitem'])) $IDitem = $_GET['IDitem'];
elseif (isset($_SESSION['IDitem'])) $IDitem = $_SESSION['IDitem'];


if (isset($_GET['IDitem'])) $IDitem = $_GET['IDitem'];
elseif (getUserParam($_SESSION['CnxID'], 'edt_id_item') !== null) $IDitem = getUserParam($_SESSION['CnxID'], 'edt_id_item');
if (isset($IDitem)) setUserParam($_SESSION['CnxID'], 'edt_id_item', $IDitem, 'Éléments affichés sur l\'EDT');

$_SESSION["IDitem"] = $IDitem;
$IDclass  = ( @$_POST["IDclass"] )					// Identifiant de la classe
	? (int) $_POST["IDclass"]
//	: (int) @$_GET["IDclass"] ;
	: (int) (@$_GET["IDclass"] ? $_GET["IDclass"] : $IDitem) ;
$IDuser   = ( @$_POST["IDuser"] )					// Identifiant de l'utilisateur
	? (int) $_POST["IDuser"]
	: (int) @$_GET["IDuser"] ;
$generique   = ( @$_POST["generique"] )					// Identifiant de l'utilisateur
	?  $_POST["generique"]
	:  @$_GET["generique"] ;
$setdate  = ( isset($_GET["setdate"]) )
	? $_GET["setdate"]
	: @$_SESSION["setdate"];

if($generique == "")
{
	$generique = "off";
}

$IDdata   = (int) @$_GET["IDdata"];					// Identifiant de l'edt
$ident    = addslashes(trim(@$_GET["ident"]));			// Identifiant de la salle

$submit   = ( @$_POST["submit"] )					// ajout salle
	? $_POST["submit"]
	: @$_GET["submit"] ;

if($_SESSION["CnxGrp"] == 1 && !isset($_GET["IDedt"]) && !isset($_GET["IDedt"]))
{
	$IDedt = 4;
	$IDitem = $_SESSION["CnxClass"];
}
//---------------------------------------------------------------------------
function getRowspan($delay, $horaire, $idx)
{
	/*
	 * fonction : détermine le nombre de cellules fusionnées pour l'edt
	 * out : nombre de cellules fusionnées
	 */

	// la durée de réservation
	list($h, $m) = explode(":", $delay);

	$rowspan = 1;
	for ($i = $idx, $j = $idx+1; @$horaire[$i]; $i++, $j++) {
		list($h1, $m1) = explode(":", $horaire[$i]);
		list($h2, $m2) = @$horaire[$j] ? explode(":", $horaire[$j]) : explode(":", "1:0") ;

		$dh = $h2 - $h1;
		$dm = $m2 - $m1;

		if ( $h AND $m < $dm ) {
			$h--;
			$m += 60;
			}
		else
			if ( $h == 0 AND $dh ) {
				$dh--;
				$dm += 60;
				}

		$h -= ( $h ) ? $dh : 0 ;
		$m -= ( $m ) ? $dm : 0 ;

		if ( $h <= 0 AND $m <= 0 )
			break;
		else
			$rowspan++;
		}

	return $rowspan;
}
//---------------------------------------------------------------------------

?>



<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-4 text-gray-800">Emploi du temps</h1>





	<div style="float: right; text-align: right;">



		<form id="formulaire" action="index.php" method="get" style="margin-bottom: 0px">

			<input type="hidden" name="item" value="<?php echo $item; ?>" />
			<input type="hidden" name="IDitem" value="<?php echo $IDitem; ?>" />
			<input type="hidden" name="cmde" value="<?php echo $cmde; ?>" />
			<input type="hidden" name="IDedt" value="<?php echo $IDedt; ?>" />

				<label for="IDcentre">
					<select id="IDcentre" name="IDcentre" class="custom-select" onchange="document.forms.formulaire.submit()">
						<?php
							// lecture des centres constitutifs
							$query  = "select _IDcentre, _ident from config_centre ";
							$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
							$query .= "order by _IDcentre";
							$result = mysqli_query($mysql_link, $query);
							if ( !$IDcentre )
								$IDcentre = $row[0];
							while ($row = mysqli_fetch_row($result)) {
								if ( $IDcentre == $row[0] )
									echo '<option selected="selected" value="'.$row[0].'">'.$row[1].'</option>';
								else
									echo '<option value="'.$row[0].'">'.$row[1].'</option>';
							}
						?>
					</select>
				</label>


				<label for="IDedt">
					<select id="IDedt" name="IDedt" class="custom-select" onchange="document.forms.formulaire.submit()">
					<?php
						// sélection des edt
						$Query  = "select _IDmod, _IDgrprd, _IDgrpwr, _titre, _IDweek, _horaire, _IDedt, _IDmod from edt ";
						$Query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
						$Query .= "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
						$Query .= "order by _IDedt";
						$result = mysqli_query($mysql_link, $Query);
						if ($IDedt == 0 && $_SESSION['CnxGrp'] == 2) $IDedt = 2;
						elseif ($IDedt == 0 && $_SESSION['CnxGrp'] == 4) $IDedt = 3;
						if ( $IDedt == 0 )
							$IDedt = $edt[6];

						while ($edt = mysqli_fetch_row($result)) {
							if ($IDedt == $edt[6]) $selected = 'selected'; else $selected = '';
							echo '<option value="'.$edt[6].'" '.$selected.'>'.$msg->getTrad($edt[3], true).'</option>';
						}
					?>
					</select>
				</label>



			<?php
				switch ($IDedt) {
					case 1 : $val_select = $msg->read($EDT_LOCATION); break;		// Salle
					case 2 : $val_select = $msg->read($EDT_CHOOSETEACHER); break;		// Prof
					case 4 : $val_select = $msg->read($EDT_CHOOSESTUDENT); break;		// Etudiant
					default : $val_select = $msg->read($EDT_CLASSROOM); break;		// Classe
				}
			?>
			<?php
				if ( $submit == "new" OR $submit == "update" )
				{
					$query = "";
					$value = ( $submit == "new" ) ? $msg->read($EDT_APPEND) : $msg->read($EDT_MODIFICATION) ;

					switch ( $IDedt )
					{
						case 1 :	// les salles
							$query  = "select _title from edt_items ";
							$query .= "where _IDcentre = '$IDcentre' ";
							$query .= "AND _lang = '".$_SESSION["lang"]."' AND _IDitem = '$IDitem' ";
							$query .= "limit 1";
							break;
						default :	// les classes
							break;
					}

					$result = ( strlen($query) ) ? mysqli_query($mysql_link, $query) : 0 ;
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					print("<label for=\"ident\"><input type=\"text\" id=\"ident\" name=\"ident\" size=\"20\" value=\"$row[0]\" /></label> <input type=\"submit\" name=\"submit\" value=\"$value\" style=\"font-size: 9px;\" />");
				}
				else
				{
					print("
						<label for=\"IDitem\">
							<select id=\"IDitem\" name=\"IDitem\" class=\"custom-select\" onchange=\"document.forms.formulaire.submit()\">
								<option value=\"0\">$val_select</option>");

						switch ( $IDedt ) {
							case 1 :	// les salles
								$query  = "select _IDitem, _title from edt_items ";
								$query .= "where _IDcentre = '$IDcentre' ";
								$query .= "AND _lang = '".$_SESSION["lang"]."' ";
								$query .= "order by _title";
								break;
							case 2 :	// les professeurs
								$query  = "select _IDgrp, _ident from user_group ";
								$query .= "where _IDcat = '2' AND _visible = 'O' ";
								$query .= "AND _lang = '".$_SESSION["lang"]."' ";
								$query .= "order by _ident";
								break;
							case 4 :	// les étudiants
								$query  = "select _IDgrp, _ident from user_group ";
								$query .= "where _IDcat = '1' AND _visible = 'O' ";
								$query .= "AND _lang = '".$_SESSION["lang"]."' ";
								$query .= "order by _ident";
								break;
							default :	// les classes
								$query  = "select _IDclass, _ident, _code from campus_classe ";
								$query .= "where _IDcentre = '$IDcentre' AND _visible = 'O' ";
								if (!getParam('edt_visible_par_tous')) $query .= ($_SESSION["CnxGrp"] == 1) ? "AND _IDclass = ".$_SESSION["CnxClass"]." " : "";
								$query .= "order by _code ASC";
								break;
							}
						$result = mysqli_query($mysql_link, $query);
						// $row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
						$found  = false;

						while ($row = mysqli_fetch_row($result))
						{
							if ($IDedt == 2 || $IDedt == 4)
							{
								$query  = "select _ID, _name, _fname from user_id ";
								$query .= "where _IDgrp = '$row[0]' ";
								if (!getParam('edt_visible_par_tous') || $IDedt == 4)
								{
									$query .= ($_SESSION["CnxGrp"] == 1) ? "AND _ID = ".$_SESSION["CnxID"]." " : "";	// Un étudiant ne peut voir que son EDT
									$query .= ($_SESSION["CnxGrp"] == 2) ? "AND _ID = ".$_SESSION["CnxID"]." " : "";	// Un enseignant ne peut voir que son EDT
								}
								if ($_SESSION['CnxGrp'] == 1) $query .= "AND _ID IN (SELECT _ID FROM user_id WHERE _adm = '1') ";
								$query .= "AND (_adm = '1' OR _adm = '255') ";
								$query .= "order by _name";
								$return = mysqli_query($mysql_link, $query);
								$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

								if ( $myrow  ) {
									echo '<optgroup label="'.$msg->getTrad($row[1]).'">';
									while ( $myrow  ) {
										if ($IDitem == $myrow[0]) $found = true;
										if ($IDitem == $myrow[0]) $select = 'selected';
										// $select = ( $IDitem == $myrow[0] ) ? "selected=\"selected\"" : "" ;
										if (!getParam('edt_visible_par_tous') || $IDedt == 4)
										{
											if($_SESSION["CnxGrp"] == 1) $select = ( $_SESSION["CnxID"] == $myrow[0] ) ? "selected=\"selected\"" : '' ;	// Un etudiant ne peut voir que son EDT
											if($_SESSION["CnxGrp"] == 2) $select = ( $_SESSION["CnxID"] == $myrow[0] ) ? "selected=\"selected\"" : '' ;	// Un enseignant ne peut voir que son EDT
										}
										// Si aucuns étudiant/prof n'est sélectionné, alors on sélectionne la personne connecté
										elseif (!isset($_GET['IDitem']))
										{
											if($_SESSION["CnxGrp"] == 1) $select = ( $_SESSION["CnxID"] == $myrow[0] ) ? "selected=\"selected\"" : '' ;	// Un etudiant ne peut voir que son EDT
											if($_SESSION["CnxGrp"] == 2) $select = ( $_SESSION["CnxID"] == $myrow[0] ) ? "selected=\"selected\"" : '' ;	// Un enseignant ne peut voir que son EDT
										}
										$value  = ($myrow[0] * 100) + $row[0];
										echo '<option value="'.$myrow[0].'" '.$select.'>'.formatUserName($myrow[1], $myrow[2]).'</option>';
										$myrow  = remove_magic_quotes(mysqli_fetch_row($return));
									}
									echo '</optgroup>';
								}
							}
							else
							{
								$found  = ( $IDitem == $row[0] ) ? true : $found ;
								$select = ( $IDitem == $row[0] ) ? "selected=\"selected\"" : "" ;

								// Si on est étudiant alors on sélectionne sa classe en priorité
								if ($_SESSION['CnxGrp'] == 1 && $_SESSION['CnxClass'] == $row[0] && $_GET['IDitem'] <= 0) $select = "selected=\"selected\"";
								elseif ($_SESSION['CnxGrp'] == 1 && $_GET['IDitem'] <= 0) $select = "";

								$codeclasse = ($IDedt == 3 && $row[2] != "") ? "[".$row[2]."] " : "";
								$codeclasse = '';
								echo '<option value="'.$row[0].'" '.$select.'>'.$codeclasse.$row[1].'</option>';
							}

							// $row = remove_magic_quotes(mysqli_fetch_row($result));
						}
						// reset sur changement dans la liste
						if (!$found)
							$IDuser = $IDitem = 0;

						echo '</select>';
					echo '</label>';
				}
			?>

		</form>
	</div>

</div>



<div class="card shadow mb-4">
  <div class="card-header py-3">
    <!-- <h6 class="m-0 font-weight-bold text-primary">Titre</h6> -->



		<div class="btn-group" role="group" aria-label="Basic example">
			<button type="button" class="btn btn-secondary" onclick="prevViewButton()"><i class="fas fa-chevron-left"></i></button>
			<button type="button" class="btn btn-secondary" onclick="nextViewButton()"><i class="fas fa-chevron-right"></i></button>
		</div>
		<button class="btn btn-primary" onclick="todayButton()">Aujourd'hui</button>


		<div style="float: right;">
			<div class="spinner-border" role="status" id="loader">
			  <span class="sr-only">Loading...</span>
			</div>

			<div class="btn-group" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-secondary time_select_button" onclick="dayViewButton()" id="day_select_button"><i class="fas fa-calendar-day"></i>&nbsp;Jour</button>
				<button type="button" class="btn btn-secondary time_select_button active" onclick="weekViewButton()" id="week_select_button"><i class="fas fa-calendar-week"></i>&nbsp;Semaine</button>
				<button type="button" class="btn btn-secondary time_select_button" onclick="monthViewButton()" id="month_select_button"><i class="fas fa-calendar"></i>&nbsp;Mois</button>
			</div>

			<a target="_blank" id="link_print" class="btn btn-secondary" href="<?php echo "edt_frame_print.php?IDcentre=$IDcentre&IDedt=$IDedt&IDitem=$IDitem&IDuser=$IDuser&IDdata=$IDdata&generique=$generique&view=week"; ?>"><i class="fa fa-print"></i></a>
			<button class="btn btn-primary" onclick="refreshViewButton()" style="display: none;"><?php echo $msg->read($EDT_REFRESH) ?></button>

			<!-- Il faut absolument garder ce bouton pour que le modal de création d'évent fonctionne -->
			<button class="btn btn-secondary" id='reloadIframeBtn' onclick="reloadIframe()" style="<?php if (!getParam('showReloadButtonEDT')) echo 'display: none;'; ?>"><i class="fas fa-sync-alt"></i></button>
		</div>
  </div>
  <div class="card-body">




		<iframe id="edt_frame" src="edt_frame.php?IDcentre=<?php echo $IDcentre; ?>&amp;IDedt=<?php echo $IDedt; ?>&amp;IDitem=<?php echo $IDitem; ?>&amp;IDclass=<?php echo $IDclass; ?>&amp;IDuser=<?php echo $IDuser; ?>&amp;IDdata=<?php echo $IDdata; ?>&amp;generique=<?php echo $generique; ?>&amp;setdate=<?php echo $setdate; ?>" class="width100" height="800" style="width: 100%; border: none; frameborder: 0; scrolling: no; margin-left: -5px"></iframe>






  </div>
</div>



	<form id="edt" action="index.php" method="get">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"$cmde\" /></p>
			");
	?>
	</form>
<div class="maincontent" style="margin-top: -20px;">

 <?php
	// initialisation
	if ( $IDitem < 0 )
	{
		$IDuser = (int) abs($IDitem / 100);
		$IDitem = abs($IDitem + $IDuser);
	}
?>




<?php
if($generique == "on")
{
	?>
	<div class="alert alert-error">
		<strong><center>Vous avez activé le mode "générique" : </strong> Les cours que vous allez créer seront reportés sur toute l'année</center>
    </div>
	<?php
}
?>

</div>






<script>
	function todayButton() {
		let iframe = $('#edt_frame');
		let btn = iframe.contents().find('#showtodaybtn');
		btn.click();
	}
	function dayViewButton() {
		let iframe = $('#edt_frame');
		let btn = iframe.contents().find('#showdaybtn');
		btn.click();
		$('.time_select_button').removeClass('active');
		$('#day_select_button').addClass('active');
	}
	function weekViewButton() {
		let iframe = $('#edt_frame');
		let btn = iframe.contents().find('#showweekbtn');
		btn.click();
		$('.time_select_button').removeClass('active');
		$('#week_select_button').addClass('active');
	}
	function monthViewButton() {
		let iframe = $('#edt_frame');
		let btn = iframe.contents().find('#showmonthbtn');
		btn.click();
		$('.time_select_button').removeClass('active');
		$('#month_select_button').addClass('active');
	}
	function refreshViewButton() {
		let iframe = $('#edt_frame');
		let btn = iframe.contents().find('#showreflashbtn');
		btn.click();
	}

	function nextViewButton() {
		let iframe = $('#edt_frame');
		let btn = iframe.contents().find('#sfnextbtn');
		btn.click();
	}
	function prevViewButton() {
		let iframe = $('#edt_frame');
		let btn = iframe.contents().find('#sfprevbtn');
		btn.click();
	}

function reloadIframe() {
	var src = $('#edt_frame').attr('src');
	$('#edt_frame').attr('src', src);
}


</script>
