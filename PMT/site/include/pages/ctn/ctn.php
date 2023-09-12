<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2005-2010 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)
   Copyright (c) 2010 by Jérémy CORNILLEAU (jeremy.cornilleau@gmail.com)
   Copyright (c) 2010 by Alexandre MAHE (alexandre.mahe@oxydia.com)

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
 *		module   : ctn.php
 *		projet   : la page des cahiers de texte numériques
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 23/10/05
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */




	// On récupère les éléments dans le post et si il n'y sont pas alors dans le get
	$post_get = array('IDcentre', 'IDclass', 'IDgroup', 'IDmat', 'year', 'submit');
	foreach ($post_get as $value) {
		if (isset($_POST[$value])) $$value = $_POST[$value];
		elseif (isset($_GET[$value])) $$value = $_GET[$value];
	}

	// On récupère les éléments dans le post
	$post = array();
	foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

	// On récupère les éléments dans le post
	$get = array('month', 'day', 'skpage', 'skshow', 'IDitem');
	foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

	// Valeurs par défaut (si variable non définie)
	$default = array(
		'IDcentre' => $_SESSION['CnxCentre'],
		'IDclass' => $_SESSION['CnxClass'],
		'year' => getParam('START_Y'),
		'month' => date('m'),
		'day' => date('d'),
		'skpage' => 1,
		'skshow' => 1
	);
	foreach ($default as $key => $value) if (!isset($$key)) $$key = $value;


	if(strlen($month) == 1 && $month != 0) $month = (int) "0".$month;
	if(strlen($day) == 1 && $day != 0) $day = (int) "0".$day;


	if (@$_GET["salon"] == "") $_SESSION["CampusName"] = "";

	if (!isset($_GET['week'])) $week = 'on';
	elseif (isset($_GET['week']) && $_GET['week'] == 'on') $week = 'on';
?>


<?php
	require_once $_SESSION["ROOTDIR"]."/include/ctn.php";

	// vérification des droits
	$query  = "select _IDmod, _IDgrpwr, _IDgrprd, _month, _limited, _IDctn, _common, _rss, _sndmail from ctn WHERE 1 ";
	if (isset($IDgroup)) $query .= "AND _IDgroup = '$IDgroup' ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);
	$auth   = mysqli_fetch_row($result);

	// l'utilisateur a cliqué sur un lien
	if (isset($submit)) {
		switch ($submit) {
			case "del" :
				$Query  = "DELETE from ctn_items ";
				$Query .= "where _IDitem = '$IDitem' ";
				$Query .= ( $_SESSION["CnxAdm"] == 255 OR ($auth[6] == "O" AND ($auth[1] AND pow(2, $_SESSION["CnxGrp"] - 1))) )
					? ""
					: "AND _ID = '".$_SESSION["CnxID"]."' " ;
				$Query .= "limit 1";

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				else {
					$Query  = "DELETE from ctn_data ";
					$Query .= "where _IDitem = '$IDitem' ";

					mysqli_query($mysql_link, $Query);

					$Query  = "SELECT _IDpj, _ext from ctn_pj ";
					$Query .= "where _IDitem = '$IDitem' ";

					$result = mysqli_query($mysql_link, $Query);
					$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

					while ( $row ) {
						$Query  = "DELETE from ctn_pj ";
						$Query .= "where _IDpj = '$row[0]' ";
						$Query .= "limit 1";

						if ( mysqli_query($mysql_link, $Query) )
							@unlink("$DOWNLOAD/ctn/$row[0].$row[1]");

						$row = mysqli_fetch_row($result);
						}
					}
				break;

			case "visible" :
			case "invisible" :
				$Query  = "update ctn_items ";
				$Query .= ( $submit == "visible" ) ? "set _visible = 'O' " : "set _visible = 'N' " ;
				$Query .= "where _IDitem = '$IDitem' ";
				$Query .= ( $_SESSION["CnxAdm"] == 255 OR ($auth[6] == "O" AND ($auth[1] AND pow(2, $_SESSION["CnxGrp"] - 1))) )
					? ""
					: "AND _ID = '".$_SESSION["CnxID"]."' " ;

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				break;

			default :
				if ( @$_POST["import"] == $msg->read($CTN_IMPORT) )
					import_ctn($IDcentre, @$_FILES["UploadFile"]);

				// affichage automatique
				$Query  = "update ctn_items ";
				$Query .= "set _visible = 'O' ";
				$Query .= "where _date <= '".date("Y-m-d H:i:s")."' ";

				@mysqli_query($mysql_link, $Query);

				// lecture des droits
				$Query  = "select _IDmod, _IDgrpwr from campus_data ";
				$Query .= "where _titre = '".$_SESSION["CampusName"]."' ";

				$result = mysqli_query($mysql_link, $Query);
				$who    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
				break;
		}
	}



function get_lundi_dimanche_from_week($week, $year)
{
	if(strftime("%W",mktime(0,0,0,01,01,$year))==1)
	  $mon_mktime = mktime(0,0,0,01,(01+(($week-1)*7)), $year);
	else
	  $mon_mktime = mktime(0,0,0,01,(01+(($week)*7)), $year);

	if(date("w",$mon_mktime)>1)
	  $decalage = ((date("w", $mon_mktime)-1)*60*60*24);

	$lundi = $mon_mktime - $decalage;
		$dimanche = $lundi + (6*60*60*24);

		return array(date("Y-m-d", $lundi), date("Y-m-d", $dimanche));
}
?>





<?php
$link_infos = 'index.php?item='.(@$item).'&month='.(@$month).'&year='.(@$year).'&day='.(@$day).'&IDcentre='.(@$IDcentre).'&IDmat='.(@$IDmat).'&IDclass='.(@$IDclass).'&IDgroup='.(@$IDgroup);


$rss_flux = ( $auth[7] == "O" )
	? "<a href=\"".$_SESSION["ROOTDIR"]."/ctn_rss.php\" onclick=\"window.open(this.href, '_blank'); return false;\">
	<img src=\"".$_SESSION["ROOTDIR"]."/images/rss.png\" title=\"". $msg->read($CTN_RSS) ."\" alt=\"". $msg->read($CTN_RSS) ."\" />
	</a>"
	: "" ;

?>


<form id="formulaire" action="" method="get" enctype="multipart/form-data">
	<input type="hidden" name="item" value="13" />


	<div id="import" style="display: none;">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $FILESIZE; ?>" />
		<input type="file" name="UploadFile" size="40" style="font-size:9px; margin-bottom:5px\" />
		<input type="submit" name="import" value="<?php echo $msg->read($CTN_IMPORT); ?>" style="font-size:9px; margin-bottom:5px;" />
	</div>


	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
	  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($CTN_CTN); ?></h1>

	  <div style="float: right; text-align: right;">

			<!-- Boutons d'action -->
			<div class="mb-3">
				<?php if (($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 OR $_SESSION["CnxGrp"] == 2) && getParam('feuille_suivi_pedagogique_show')) { ?>
					<button type="button" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm noprint" onclick="generateEmargementDoc()"><i class="fas fa-file"></i>&nbsp;Feuille de suivi pédagogique</button>
				<?php } ?>
	      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="<?php echo $_SESSION["ROOTDIR"]; ?>/exports.php?item=13&cmde=&IDcentre=<?php echo @$IDcentre; ?>&IDclass=<?php echo @$IDclass; ?>&IDgroup=<?php echo @$IDgroup; ?>&IDmat=<?php echo @$IDmat; ?>&IDitem=<?php echo @$IDitem; ?>&year=<?php echo @$year; ?>&month=<?php echo @$month; ?>&day=<?php echo @$day; ?>&skpage=<?php echo @$skpage; ?>&skshow=<?php echo @$skshow; ?>&week=<?php echo @$week; ?>" onclick="window.open(this.href, '_blank'); return false;">
	        <i class="fas fa-upload fa-sm text-white-50" title="Export"></i>&nbsp;Exporter
	      </a>
	      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="#" onclick="window.print();return false;">
	        <i class="fas fa-print fa-sm text-white-50" title="Imprimer"></i>&nbsp;Imprimer
	      </a>
	    </div>

			<!-- Filtres -->
	    <div class="mb-3">
				<div class="form-row">
					<div class="col" style="<?php if (!getParam("showCenter")) echo 'display: none;'; ?>">
						<select id="IDcentre" name="IDcentre" class="custom-select" onchange="document.forms.formulaire.submit()">
							<?php
							// lecture des centres constitutifs
							$query  = "select _IDcentre, _ident from config_centre where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' order by _IDcentre ";
							$result = mysqli_query($mysql_link, $query);
							while ($row = mysqli_fetch_row($result)) {
								if ($IDcentre == $row[0]) $selected = 'selected'; else $selected = '';
								echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
							}
							?>
						</select>
					</div>

					<div class="col">
						<select id="IDmat" name="IDmat" class="custom-select" onchange="document.forms.formulaire.submit()">
							<option value="0">Sélectionnez une matière</option>
								<?php
									$query  = "select _IDmat, _titre from campus_data where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' order by _titre ";
									$return = mysqli_query($mysql_link, $query);
									while ($row = mysqli_fetch_row($return))
									{
										if ($IDmat == $row[0]) $selected = 'selected'; else $selected = "";
										echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
									}
								?>
						</select>
					</div>

					<div class="col">
						<?php
							if ($_SESSION['CnxGrp'] == 1) $disabled_select = true; else $disabled_select = false;
							echo getClassSelect('IDclass', 'IDclass', $IDclass, true, 'formulaire', true, $disabled_select);
						?>
					</div>
				</div>
	    </div>
	  </div>
	</div>











	<div class="card shadow mb-4">
	  <div class="card-header py-3">
			<!-- Filtre année -->
			<div class="form-row">
				<div class="mr-2">
					<select id="year" name="year" class="custom-select" style="width: 80px;" onchange="document.forms.formulaire.submit()">
						<?php
							// les années scolaires
							$query  = "select min(_date), max(_date) from ctn_items where 1 ";
							if (isset($IDclass) && $IDclass != 0) $query .= "AND _IDctn LIKE '%;".$IDclass.";%' ";
							if (isset($IDmat) && $IDmat != 0) $query .= "AND _IDmat = '".$IDmat."' ";
							$return = mysqli_query($mysql_link, $query);
							$myrow  = @mysqli_fetch_row($return);
							if ($myrow) {
								$min = strval(substr($myrow[0], 0, 4));
								$max = strval(substr($myrow[1], 0, 4));
								if (!$year) $year = $max;
								if (!$min && !$max) $min = $max = $year;
								for ($k = $max; $k >= $min; $k--) {
									if ($year == $k) $selected = 'selected'; else $selected = '';
									echo '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
								}
							}
							else echo '<option value="'.$year.'">'.$year.'</option>';
						?>
					</select>
				</div>

				<!-- Filtre mois -->
				<div class="mr-2" style="width: 140px;">
					<select id="month" name="month" class="custom-select" onchange="document.forms.formulaire.submit()">
						<option value="0">Tous les mois</option>
							<?php
							$mois = array_merge(Array(''), explode(",", $msg->read($CTN_MONTH)));
							for ($i = 1; $i < 13; $i++) {
								if ($month == $i) $selected = 'selected'; else $selected = '';
								echo '<option value="'.$i.'" '.$selected.'>'.$mois[$i].'</option>';
							}
							?>
					</select>
				</div>

				<!-- Filtre jours -->
				<div class="mr-2" style="width: 140px;">
					<select id="day" name="day" class="custom-select" onchange="document.forms.formulaire.submit()">
						<option value="0">Tous les jours</option>
						<?php
							for ($i = 1; $i <= getmaxdays($year, $month); $i++) {
								if ($day == $i) $selected = 'selected'; else $selected = '';
								echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
							}
						?>
					</select>
				</div>

				<!-- Filtre semaine complète  -->
					<?php
					if (isset($week) && $week == 'on') $checked = 'checked'; else $checked = '';
					if($month != 0 && $day != 0) {
					?>
					<input type="hidden" name="week" value="no">	<!-- Permet par défaut de cocher la case semaine -->
					<div class="form-group form-check" style="margin-bottom: 0px; padding-top: 6px;">
						<input type="checkbox" class="form-check-input" id="week" name="week" value="on" <?php echo $checked; ?> onchange="document.forms.formulaire.submit()">
						<label class="form-check-label" for="week"><?php echo $msg->read($CTN_WEEKOF).date("W", mktime(0, 0, 0, (int)$month, (int)$day, (int)$year)); ?></label>
					</div>
				<?php } ?>
			</div>
	  </div>
	  <div class="card-body">

			<table class="table table-stripped">
				<tr>
					<th>Options</th>
					<th>Date</th>
					<th><?php echo $msg->read($CTN_MATIERE); ?></th>
					<th><?php echo $msg->read($CTN_CONTENU); ?></th>
					<th><?php echo $msg->read($CTN_DEVOIRS); ?></th>
					<th><?php echo $msg->read($CTN_CLASS); ?></th>
					<th><?php echo $msg->read($CTN_ABS); ?></th>
				</tr>

				<?php
					// Stockage des classes
					$classe_liste = array();
					$query = "SELECT _IDclass, _ident FROM campus_classe WHERE 1 ";
					$result = mysqli_query($mysql_link, $query);
					while ($row = mysqli_fetch_row($result)) {
						$classe_liste[$row[0]] = $row[1];
					}

					// lecture de la base de données
					$query  = "select distinctrow ";
					$query .= "ctn_items._IDitem, ctn_items._title, ctn_items._date, ctn_items._delay, ctn_items._ID, ctn_items._visible, ctn_items._IP, ctn_items._IDmat, ctn_items._IDmat, ";
					$query .= "ctn._IDgrprd, ctn._IDgroup, ctn_items._type, ctn_items._IDctn, ctn_items._IDcours, edt_data._debut, edt_data._fin, ctn_items._texte, ctn_items._devoirs, ctn_items._observ ";
					$query .= "from ctn, ctn_items, edt_data ";
					if((isset($month) && $month != 0) && (isset($day) && $day != 0) && (isset($week) && $week == 'on'))
					{
						$tmp = get_lundi_dimanche_from_week(date('W', mktime(0, 0, 0, (int)$month, (int)$day, (int)$year))-1, (int)$year);
						$query .= "where (ctn_items._date >= '$tmp[0] 00:00:00' AND ctn_items._date <= '$tmp[1] 23:59:59') ";
					}
					else if($month == 0 && $day == 0) $query .= "where (ctn_items._date >= '$year-01-01 00:00:00' AND ctn_items._date <= '$year-12-31 23:59:59') ";
					else if($month != 0 && $day == 0) $query .= "where (ctn_items._date >= '$year-$month-01 00:00:00' AND ctn_items._date <= '$year-$month-31 23:59:59') ";
					else if($month == 0 && $day != 0) $query .= "where (ctn_items._date LIKE '$year-%-$day%' OR ctn_items._date LIKE '$year-%-$day%') ";
					else if($month != 0 && $day != 0) $query .= "where ctn_items._date LIKE '$year-$month-$day%' ";
					$query .= "AND edt_data._IDx = ctn_items._IDcours " ;
					if (isset($IDmat) && $IDmat) $query .= "AND ctn_items._IDmat = '$IDmat' ";

					if ($_SESSION['CnxAdm'] != 255)
					{
						if ($_SESSION['CnxGrp'] == 2) $query .= "AND ctn_items.`_ID` = '".$_SESSION['CnxID']."' ";
						if ($_SESSION['CnxGrp'] == 1) $query .= "AND ctn_items.`_IDctn` LIKE '%".$_SESSION['CnxClass']."%'";
					}
					$query .= "order by ctn_items._date desc ";

					$query_counter = $query;
					$query .= "LIMIT ".$MAXSHOW." OFFSET ".(($skpage * $MAXSHOW) - $MAXSHOW);

					// détermination du nombre de pages + nb de résultats
					$result = mysqli_query($mysql_link, $query);
					$result_counter = mysqli_query($mysql_link, $query_counter);
					$nbelem = mysqli_affected_rows($mysql_link);


					$page   = $nbelem;
					$show   = 1;

					if ( $nbelem ) {
						$page  = ( $page % $MAXPAGE )
							? (int) ($page / $MAXPAGE) + 1
							: (int) ($page / $MAXPAGE) ;

						$show  = ( $page % $MAXSHOW )
							? (int) ($page / $MAXSHOW) + 1
							: (int) ($page / $MAXSHOW) ;

						// initialisation
						$i     = 1;
						$first = 1 + (($skpage - 1) * $MAXPAGE);

						// se positionne sur la page ad hoc
						mysqli_data_seek($result, $first - 1);
						while ($row = mysqli_fetch_row($result) AND $i <= $MAXPAGE) {
							// lecture des absences
							$query  = "select _IDitem from absent_items ";
							$query .= "where _IDctn = '$row[0]' ";

							mysqli_query($mysql_link, $query);
							$absent = mysqli_affected_rows($mysql_link);

							// suppression des post
							$req  = $msg->read($CTN_CONFIRM);
							$del  = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $row[4] OR ($auth[6] == "O" AND ($auth[1] AND pow(2, $_SESSION["CnxGrp"] - 1))) )
								? "<a class=\"noprint\" href=\"".myurlencode($link_infos."&IDitem=$row[0]&submit=del")."\" onclick=\"return confirm('$req');\"><i class=\"fas fa-trash\" title=\"". $msg->read($CTN_DELETE) ."\"></i></a>"
								: "" ;

							// modification des post
							$maj  = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $row[4] OR ($auth[6] == "O" AND ($auth[1] AND pow(2, $_SESSION["CnxGrp"] - 1))) )
								? "<a class=\"noprint\" onclick=\"popupModal('ctn_post_add.php?IDx=$row[13]&sd=".date("m/j/Y", strtotime($row[2]))."&st=".date("H:i", strtotime($row[14]))."&et=".date("H:i", strtotime($row[15]))."&generique=off&ctn=on', '800');\" href=\"#\"><i class=\"fas fa-pencil-alt\" title=\"". $msg->read($CTN_UPDATE) ."\"></i></a>"
								: "" ;


							// affichage des classes
							$query  = "select _ident from campus_classe ";
							$query .= "where _IDclass = '$row[7]' ";
							$query .= "limit 1";

							$return = mysqli_query($mysql_link, $query);
							$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

							// affichage des matières
							$query  = "select _titre from campus_data ";
							$query .= "where _lang = '".$_SESSION["lang"]."' ";
							$query .= "AND _IDmat = '$row[8]' ";
							$query .= "limit 1";

							$return = mysqli_query($mysql_link, $query);
							$title  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;
							$type = ($row[12] == 0) ? "contenu du cours" : "à faire";

							echo '<tr>';
								echo '<td>'.$maj.' '.$del.'</td>';
								echo '<td>'.date("d/m/Y", strtotime($row[2]))." <strong>".date("H:i", strtotime($row[14]))."-".date("H:i", strtotime($row[15])).'</strong></td>';
								echo '<td><strong>'.$title[0].'</strong></td>';
								echo '<td>'.$row[16].'</td>';
								echo '<td>'.$row[17].'</td>';
								echo '<td>';
									$nom_class = '';
									foreach(explode(";", $row[12]) as $val)
										if ($val != '') $nom_class .= $classe_liste[$val].' - ';

									echo substr($nom_class, 0, -3);
								echo '</td>';
								echo '<td>'.$absent.'</td>';
							echo '</tr>';

							$i++;
						}
					}
				?>
			</table>

	  </div>
		<div class="card-footer text-muted">
	    <?php echo getPagination($skpage, $nbelem, $link_infos, 0); ?>
	  </div>
	</div>


</form>





<script>



	<?php
		$query = "SELECT DISTINCT user_id._IDclass, campus._IDclass, campus._ident FROM user_id, campus_classe campus WHERE user_id._IDclass = campus._IDclass AND user_id._adm = '1' AND user_id._IDgrp >= 1  ";
		$result = mysqli_query($mysql_link, $query);
		$promoSelect = "<option value=\"0\" selected disabled>Séléctionnez une promotion</option>";
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$promoSelect .= "<option value=\"".$row[1]."\">".$row[2]."</option>";
		}
		$promoSelect = addslashes($promoSelect);
	?>

	// Quand on clique sur le boutnon 'Feuille d'émargement:
	function generateEmargementDoc() {
		var html_element = "<select name=\"promo\" onchange=\"saveDateAndPromo()\" id=\"promo\"><?php echo $promoSelect; ?></select><br><input type=\"date\" min=\"<?php echo date("Y-m-d"); ?>\" onchange=\"saveDateAndPromo()\" id=\"date_suivi_pedago\" name=\"date_suivi_pedago\">";

		swal({
			title: 'Générer la feuille de suivi pédagogique',
			html: html_element,
			customClass: 'swal2-overflow',
		}).then(function(result) {

			var date = jQuery("#dateForSuiviPedago").val();
			var promo = jQuery("#promoForPedago").val();
			var win = window.open('ctn_suivi_peda.php?classID=' + promo + '&date=' + date, '_blank');
			if (win) {
				//Browser has allowed it to be opened
				win.focus();
			} else {
				//Browser has blocked it
				swal({
					title: "Erreur!",
					text: "Merci d'autoriser l'ouverture des popups pour ce site dans votre navigateur",
					icon: "success",
				});
			}
		});
	}

	function saveDateAndPromo() {
		var date = jQuery("#date_suivi_pedago").val();
		var promo = jQuery("#promo").val();
		jQuery("#dateForSuiviPedago").val(date);
		jQuery("#promoForPedago").val(promo);
	}

</script>
<input type="hidden" id="dateForSuiviPedago">
<input type="hidden" id="promoForPedago">

<!-- Pour le select, j'ai besoins de sweetalert 1 et 2 mais il ne faut pas l'inclure partout sinon c'est le bazar...
ET IL FAUT LES LAISSER DANS CET ORDRE!!! -->
<script src="script/sweetalert2.min.js"></script>
<script src="script/sweetalert.min.js"></script>
