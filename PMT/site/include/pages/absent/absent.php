<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : absent.php
 *		projet   : la page de visualisation des absences individuelles des élèves
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 12/02/06
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *					 		 25/10/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7 et maj du thème
 */

  // On récupère les éléments dans le post puis dans le get puis dans la session
  $post_get = array('regroup', 'idmenu', 'IDcentre', 'IDeleve', 'IDclass', 'IDgroup', 'IDgroupclass', 'IDgrp', 'name', 'year', 'month', 'day', 'IDmotif', 'IDmotif_all', 'abs_idmat', 'type', 'typeabs', 'IDalpha', 'etat', 'init', 'week', 'isok', 'sms', 'valid');
  foreach ($post_get as $value) {
    if (isset($_POST[$value])) $$value = $_POST[$value];
    elseif (isset($_GET[$value])) $$value = $_GET[$value];
    elseif (isset($_SESSION['ABSENT_'.$value])) $$value = $_SESSION['ABSENT_'.$value];
  }

  // On récupère les éléments dans le post puis dans le get
  $post_get = array('submit', 'IDitem');
  foreach ($post_get as $value) {
  	if (isset($_POST[$value])) $$value = $_POST[$value];
  	elseif (isset($_GET[$value])) $$value = $_GET[$value];
  }

  // On récupère les éléments dans le post
  $post = array('IDuser', 'start', 'end', 'IDdata', 'note', 'file', 'justified', 'token', 'IDabs', 'cbmotif');
  foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

  // On récupère les éléments dans le get
  $get = array('id', 'devalidation', 'validation');
  foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

  // On récupère les valeurs par défaut
  $default = array(
   'regroup' => 'oui',
   'year' => date('Y'),
   'month' => date('m'),
   'day' => 0,
   'etat' => 'T',
   'type' => 2
  );
  foreach ($default as $key => $value) if (!isset($$key) && isset($value)) $$key = $value;

  // On récupère les valeurs par défaut dans la session
  $default_session = array(
  	'IDcentre' => 'CnxCentre'
  );
  foreach ($default_session as $key => $value) if (!isset($$key) && isset($_SESSION[$value])) $$key = $_SESSION[$value];

  // Traitement des variables
  if (isset($IDalpha)) $IDalpha = str_replace('nbsp;', ' ', $IDalpha);

  // On stocke les variables en session
  $save_in_session = array('regroup', 'IDcentre', 'IDeleve', 'IDclass', 'IDgroup', 'IDgroupclass', 'IDgrp', 'name', 'year', 'month', 'day', 'IDmotif', 'IDmotif_all', 'abs_idmat', 'type', 'typeabs', 'IDalpha', 'etat', 'week', 'isok', 'sms');
  foreach ($save_in_session as $value) if (isset($$value)) $_SESSION['ABSENT_'.$value] = $$value;





// On enregistre les modifications/création via le popup
if (isset($_GET['submitAbs']) /* && $token == $_SESSION['token'] */) {
  if (isset($justified) && $justified == 'O') $justified = 'O'; else $justified = 'N';
  $datas = array(
    '_IDabs' => $IDuser,
    '_start' => date('Y-m-d H:i', strtotime(changeDateTypeFromFRToEN($start))),
    '_end' => date('Y-m-d H:i', strtotime(changeDateTypeFromFRToEN($end))),
    '_IDdata' => $IDdata,
    '_texte' => $note,
    '_valid' => $justified,
    '_ID' => $_SESSION['CnxID'],
    '_date' => date('Y-m-d H:i:s')
  );

  // Si on fait une mise à jours
  if (isset($IDabs) && $IDabs) $db->query("UPDATE absent_items SET ?u WHERE _IDitem = ?i ", $datas, $IDabs);
  // Si on crée une nouvelle absence
  else $db->query("INSERT INTO absent_items SET ?u ", $datas);
  if (isset($IDabs) && $IDabs) $last_elem_id = $IDabs;
  else $last_elem_id = $db->insertId();

  if (isset($_FILES['file'])) {
    if (!file_exists($DOWNLOAD.'/absent/files/')) mkdir($DOWNLOAD.'/absent/files/');
    $filename = $_FILES['file']['name'];
    $dest = $DOWNLOAD.'/absent/files/'.$last_elem_id;
    copy($_FILES['file']['tmp_name'], $dest);

    $db->query("UPDATE absent_items SET _file = ?s WHERE _IDitem = ?i ", $filename, $last_elem_id);
  }





}

// Token pour éviter la double validation
$_SESSION['token'] = $token = sha1(rand()); // random token




if($IDcentre != $_SESSION["IDcentre"]) unset($IDeleve, $IDclass, $IDgroupclass);



// Traitement retour
if(isset($_GET['detail']) && $_GET['detail'] == 'retour')
{
	$IDalpha = $_SESSION['IDalpha'] = $_SESSION['IDgroupclass'] = $IDgroupclass = '';
	$_SESSION['IDeleve'] = $_SESSION['class'] = $IDeleve = $IDclass = 0;
	$regroup = 'oui';
	$etat = 'T';

  unset($_SESSION['ABSENT_IDalpha'], $_SESSION['ABSENT_IDalpha'], $_SESSION['ABSENT_IDgroupclass'], $_SESSION['ABSENT_class'], $_SESSION['ABSENT_IDeleve'], $_SESSION['ABSENT_regroup'], $_SESSION['ABSENT_etat']);
  // // On supprime les filtres qui sont en session
  // $delete_in_session = array('regroup', 'IDcentre', 'IDeleve', 'IDclass', 'IDgroup', 'IDgroupclass', 'IDgrp', 'name', 'year', 'month', 'day', 'IDmotif', 'IDmotif_all', 'abs_idmat', 'type', 'typeabs', 'IDalpha', 'etat', 'week', 'isok', 'sms');
  // foreach ($delete_in_session as $value) if (isset($_SESSION['ABSENT_'.$value])) unset($_SESSION['ABSENT_'.$value]);
}

if(isset($IDeleve) && $IDeleve != 0) $IDgroupclass = '';

date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR' );
$good_date = date('Y-m-d H:i:s', time());

$startx   = mktime(0, 0, 0, getParam('START_M'), getParam('START_D'), getParam('START_Y'));
$endx     = mktime(23, 59, 59, getParam('END_M'), getParam('END_D'), getParam('END_Y'));
$startday = intval(date('N', $startx))-1;
$endday = intval(date('N', $endx))-1;

// Validation d'une absence
if(isset($validation) && $_SESSION["CnxAdm"] == 255) $db->query("UPDATE absent_items set _valid = 'O', _IDmod = ?i where _IDitem = ?i LIMIT 1 ", $_SESSION['CnxID'], $validation);

// On met à jours l'absence pour la dé-valider
if(isset($devalidation) && $_SESSION["CnxAdm"] == 255) $db->query("UPDATE absent_items set _valid = 'N' where _IDitem = ?i LIMIT 1 ", $devalidation);

//---------------------------------------------------------------------------
function get_semaine_from_week($week, $year)
{
  if(strftime('%W', mktime(0, 0, 0, 01, 01, $year)) == 1) $mon_mktime = mktime(0, 0, 0, 01, (01 + (($week - 1) * 7)), $year);
  else $mon_mktime = mktime(0, 0, 0, 01, (01 + ($week * 7)), $year);
  if(date('w',$mon_mktime) > 1) $decalage = ((date('w', $mon_mktime) - 1) * 60 * 60 * 24);
  $lundi = $mon_mktime - $decalage;
  $mardi = $lundi + (1 * 60 * 60 * 24);
  $mercredi = $lundi + (2 * 60 * 60 * 24);
  $jeudi = $lundi + (3 * 60 * 60 * 24);
  $vendredi = $lundi + (4 * 60 * 60 * 24);
  $samedi = $lundi + (5 * 60 * 60 * 24);
  $dimanche = $lundi + (6 * 60 * 60 * 24);
  return array(date('Y-m-d', $lundi), date('Y-m-d', $mardi), date('Y-m-d', $mercredi), date('Y-m-d', $jeudi), date('Y-m-d', $vendredi), date('Y-m-d', $samedi), date('Y-m-d', $dimanche));
}
//---------------------------------------------------------------------------
?>


<?php
	// lecture des droits
	$Query  = "select _IDmod, _IDgrprd, _IDgrpwr from absent ";
	$Query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' AND _IDgrp = '1' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;


	// lecture du centre de élève
	if (isset($IDeleve) && $IDeleve) $IDcentre = @$db->getRow("SELECT distinctrow user_id._IDcentre as centre from campus_classe, user_id where user_id._ID = ?i AND user_id._IDgrp = '1' ND user_id._IDclass = campus_classe._IDclass ", $IDeleve)->centre;

	// modification des utilisateurs
	if (isset($submit) && ($submit && ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] >= 2))) {
    switch ($submit) {
      case "delete" :
        $db->query("DELETE FROM absent_items WHERE _IDitem = ?i LIMIT 1", $IDitem);
        break;

      default :	// saisie
        $IDitem  = @$_POST["IDitem"];
        $cbmail  = @$_POST["cbmail"];
        $cbsms   = @$_POST["cbsms"];
        $cbok    = @$_POST["cbok"];
        $cbmotif = @$_POST["cbmotif"];
        $note    = @$_POST["note"];
        $to      = @$_POST["to"];
        if (isset($IDitem)) {
          for ($i = 0; $i < count($IDitem); $i++) {

            $idx = (int) $IDitem[$i];

            // valid
            if($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4)
            {
              if((isset($cbok[$idx]) && $cbok[$idx] != 'no') || !isset($cbok[$idx]))
              {
                if (isset($cbok[$idx]) && $cbok[$idx] != 'no')
                {
                  // date d'envoi du message
                  $date   = date('Y-m-d H:i:s');

                  // Vérification s'il y a eu une modification
                  $sqlcheck  = "SELECT * FROM absent_items ";
                  $sqlcheck .= "WHERE _IDitem = '".$IDitem[$i]."' ";
                  $sqlcheck .= "AND _IDdata = '".$cbmotif[$idx]."' ";
                  $sqlcheck .= "AND _texte = '".addslashes(trim($note[$idx]))."' ";
                  $sqlcheck .= "AND _valid = 'O' ";
                  $resultcheck = mysqli_query($mysql_link, $sqlcheck);
                  $rowcheck  = ( $resultcheck ) ? mysqli_num_rows($resultcheck) : 0 ;

                  if(!$rowcheck)
                  {
                    $Query  = "update absent_items ";
                    $Query .= "set _valid = 'O', _IDmod = '".$_SESSION["CnxID"]."', _sms = '$good_date', _IDdata = '".$cbmotif[$idx]."' ";
                    $Query .= "where _IDitem = '".$IDitem[$i]."' ";
                    $Query .= "limit 1";
                    mysqli_query($mysql_link, $Query);

                    // Justification rétroactive
                    // Séléction de la tranche horaire de l'absence
                    $gethoraire = "SELECT _start, _end, _IDabs, _texte FROM absent_items WHERE _IDitem = '".$IDitem[$i]."' LIMIT 1";
                    $resulthoraire = mysqli_query($mysql_link, $gethoraire);
                    $rowhoraire    = ( $resulthoraire ) ? remove_magic_quotes(mysqli_fetch_row($resulthoraire)) : 0 ;

                    // Recherche si absence dans la journée
                    $queryabs  = "SELECT DISTINCT absent_items._start, absent_items._end, campus_data._titre, absent_items._IDitem FROM absent_items, ctn_items, campus_data WHERE absent_items._IDabs = $rowhoraire[2] ";
                    $queryabs .= "AND absent_items._start >= '$rowhoraire[0]' ";
                    $queryabs .= "AND absent_items._end <= '$rowhoraire[1]' ";
                    $queryabs .= "AND absent_items._IDctn = ctn_items._IDitem ";
                    $queryabs .= "AND ctn_items._IDmat = campus_data._IDmat ";
                    $queryabs .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";

                    $resultabs = mysqli_query($mysql_link, $queryabs);
                    $rowabs    = ( $resultabs ) ? remove_magic_quotes(mysqli_fetch_row($resultabs)) : 0 ;

                    if($rowabs)
                    {
                      while ($rowabs)
                      {
                        $Query  = "update absent_items ";
                        $Query .= "set _valid = 'O', _IDmod = '".$_SESSION["CnxID"]."', _sms = '$good_date', _texte = '$rowhoraire[3]',  _IDdata = '".$cbmotif[$idx]."' ";
                        $Query .= "where _IDitem = $rowabs[3] ";
                        $Query .= "limit 1";
                        mysqli_query($mysql_link, $Query);
                        $rowabs = remove_magic_quotes(mysqli_fetch_row($resultabs));
                      }
                    }
                  }
                }
                else
                {
                  // date d'envoi du message
                  $date   = date("Y-m-d H:i:s");

                  // Vérification s'il y a eu une modification
                  $sqlcheck  = "SELECT * FROM absent_items ";
                  $sqlcheck .= "WHERE _IDitem = '".$IDitem[$i]."' ";
                  $sqlcheck .= "AND _IDdata = '".$cbmotif[$idx]."' ";
                  $sqlcheck .= "AND _texte = '".addslashes(trim($note[$idx]))."' ";
                  $sqlcheck .= "AND _valid = 'N' ";
                  $resultcheck = mysqli_query($mysql_link, $sqlcheck);
                  $rowcheck  = ( $resultcheck ) ? mysqli_num_rows($resultcheck) : 0 ;

                  if(!$rowcheck)
                  {
                    $db->query("UPDATE absent_items set _valid = 'N', _IDdata = ?i where _IDitem = ?i limit 1 ", $cbmotif[$idx], $IDitem[$i]);

                    // Déjustification rétroactive
                    // Séléction de la tranche horaire de l'absence
                    $gethoraire = "SELECT _start, _end, _IDabs, _texte FROM absent_items WHERE _IDitem = '".$IDitem[$i]."' LIMIT 1";
                    $resulthoraire = mysqli_query($mysql_link, $gethoraire);
                    $rowhoraire    = ( $resulthoraire ) ? remove_magic_quotes(mysqli_fetch_row($resulthoraire)) : 0 ;

                    // Recherche si absence dans la journée
                    $queryabs  = "SELECT DISTINCT absent_items._start, absent_items._end, campus_data._titre, absent_items._IDitem FROM absent_items, ctn_items, campus_data WHERE absent_items._IDabs = $rowhoraire[2] ";
                    $queryabs .= "AND absent_items._start >= '$rowhoraire[0]' ";
                    $queryabs .= "AND absent_items._end <= '$rowhoraire[1]' ";
                    $queryabs .= "AND absent_items._IDctn = ctn_items._IDitem ";
                    $queryabs .= "AND ctn_items._IDmat = campus_data._IDmat ";
                    $queryabs .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";
                    $resultabs = mysqli_query($mysql_link, $queryabs);
                    while ($rowabs = mysqli_fetch_row($resultabs)) $db->query("UPDATE absent_items set _valid = 'N', _IDmod = ?i, _sms = ?s, _texte = ?s,  _IDdata = ?i where _IDitem = ?i limit 1 ", $_SESSION['CnxID'], $good_date, $rowhoraire[3], $cbmotif[$idx], $rowabs[3]);
                  }
                }
              }
            }

            // maj des notes
            if (isset($note[$idx])) {
              $db->query("UPDATE absent_items set _texte = ?s where _IDitem = ?i limit 1 ", trim($note[$idx]), $IDitem[$i]);
            }

            // maj des motif
            if (isset($cbmotif[$idx])) {
              $db->query("UPDATE absent_items set _IDdata = ?i where _IDitem = ?i limit 1 ", $cbmotif[$idx], $IDitem[$i]);
            }
          }
        }
        break;
    }
  }



	// initialisation
  // $href = "item=$item&cmde=$cmde&IDcentre=$IDcentre&IDgroup=$IDgroup&name=$name&type=$type&etat=$etat&IDalpha=$IDalpha&abs_idmat=$abs_idmat&IDclass=$IDclass&year=$year&month=$month&day=$day&lang=".$_SESSION["lang"];
	$href = 'item='.(@$item).'&cmde='.(@$cmde).'&IDcentre='.(@$IDcentre).'&IDgroup='.(@$IDgroup).'&name='.(@$name).'&type='.(@$type).'&etat='.(@$etat).'&IDalpha='.(@$IDalpha).'&abs_idmat='.(@$abs_idmat).'&IDclass='.(@$IDclass).'&year='.(@$year).'&month='.(@$month).'&day='.(@$day).'&lang='.$_SESSION['lang'];

	$display = ($_SESSION["CnxGrp"] != 1) ? "" : "display: none";
?>







<?php
	$temp = array('IDcentre', 'IDeleve', 'IDclass', 'IDgroup', 'IDgroupclass', 'IDgrp', 'name', 'year', 'IDmotif', 'IDmotif_all', 'abs_idmat', 'type', 'typeabs', 'IDalpha', 'etat', 'month', 'day', 'regroup', 'week', 'isok', 'email', 'sms', 'valid');
	$export_link = 'exports.php?item='.$item;
	foreach ($temp as $value) if (isset($$value)) $export_link .= '&'.$value.'='.$$value;
?>




<?php
// On récupère les motifs d'absence
$motifs = $db->getAll("SELECT `_IDdata` as `IDdata`, `_texte` as `texte` from `absent_data` where `_visible` = 'O' AND `_lang` = ?s order by `_texte` ", $_SESSION["lang"]);

?>




<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($ABSENT_LIST); ?></h1>
  <div style="float: right; text-align: right;">
    <div class="mb-3 d-print-none">
			<?php
			if ($_SESSION['CnxGrp'] > 1) { ?>
        <?php if (getParam('afficherBoutonFeuilleEmargement')) { ?>
  				<button type="button" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm" onclick="generateEmargementDoc()"><i class="fas fa-file fa-sm text-white-50"></i>&nbsp;Feuille d'émargement</button>
        <?php } ?>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" id="newAbsBtn"><i class="fas fa-plus fa-sm text-white-50"></i>&nbsp;Nouvelle absence</button>
			<?php } ?>
      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?php echo $export_link; ?>">
        <i class="fas fa-upload fa-sm text-white-50" title="Export"></i>&nbsp;Exporter
      </a>
      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="#">
        <i class="fas fa-print fa-sm text-white-50" title="Imprimer"></i>&nbsp;Imprimer
      </a>
    </div>

    <div class="mb-3">
			<form id="formulaire" action="index.php" method="post" style="display: inline-block;">
				<input type="hidden" name="item" value="<?php echo $item; ?>">
				<input type="hidden" name="cmde" value="<?php echo $cmde; ?>">
				<input type="hidden" name="IDeleve" id="IDeleve_hidden" value="<?php echo $IDeleve; ?>">
				<input type="hidden" name="init" value="oui">
				<input type="hidden" name="detail" value="non">
				<input type="hidden" name="idmenu" value="<?php echo $idmenu; ?>">


				<div class="form-row mb-3">

					<?php if((!isset($regroup) || $regroup != 'oui') || (isset($IDalpha) && $IDalpha != '') || (isset($type) && $type != 2) || (isset($etat) && $etat != 'T') || (isset($typeabs) && $typeabs != 'T') || (isset($IDmotif) && $IDmotif != '' && $IDmotif != 0) || (isset($abs_idmat) && $abs_idmat != 0) || (isset($IDclass) && $IDclass != 0)) { ?>
						<a href="index.php?item=<?php echo $item; ?>&cmde=<?php echo $cmde; ?>&detail=retour" class="btn btn-secondary shadow-sm"><i class="fa fa-times" aria-hidden="true"></i></a>
					<?php } ?>

					<!-- Sélection du centre -->
					<div class="col" style="<?php if (!$showCenter) echo 'display: none;'; ?>">
						<?php echo centerSelect(@$IDcentre, 'formulaire', 'IDcentre', 'IDcentre', false, 'btn d-none d-sm-inline-block btn-sm shadow-sm'); ?>
					</div>


					<?php if ($_SESSION['CnxGrp'] > 1) { ?>
						<div class="col">
							<!-- Types d'absences -->
							<select id="type" name="type" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
								<option value="2" <?php if ((isset($type) && $type == 2) || !isset($type)) echo 'selected'; ?>>Tout</option>
								<option value="0" <?php if ((isset($type) && $type == 0)) echo 'selected'; ?>><?php echo $msg->read($ABSENT_REEL); ?></option>
								<option value="1" <?php if (isset($type) && $type == 1) echo 'selected'; ?>><?php echo $msg->read($ABSENT_PREVISU); ?></option>
							</select>
						</div>

						<div class="col">
							<!-- Absences justifiées/injustifiées -->
							<select id="etat" name="etat" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
								<option value="T" <?php if ((isset($etat) && $etat == 'T') || !isset($etat)) echo 'selected'; ?>><?php echo $msg->read($ABSENT_ALL2); ?></option>
								<option value="O" <?php if (isset($etat) && $etat == 'O') echo 'selected'; ?>><?php echo $msg->read($ABSENT_JUSTIFIED); ?></option>
								<option value="N" <?php if (isset($etat) && $etat == 'N') echo 'selected'; ?>><?php echo $msg->read($ABSENT_NOTJUSTIFIED); ?></option>
							</select>
						</div>

						<div class="col">
							<!-- Type absence/non comptabilisé -->
							<select id="typeabs" name="typeabs" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
								<option value="T" <?php if ((isset($typeabs) && $typeabs == 'T') || !isset($typeabs)) echo 'selected'; ?>><?php echo $msg->read($ABSENT_ALL2); ?></option>
								<option value="ABS" <?php if (isset($typeabs) && $typeabs == 'ABS') echo 'selected'; ?>><?php echo $msg->read($ABSENT_ABSENCENC); ?></option>
								<option value="NC" <?php if (isset($typeabs) && $typeabs == 'NC') echo 'selected'; ?>><?php echo $msg->read($ABSENT_NC); ?></option>
							</select>
						</div>

						<div class="col">
							<!-- Regroupement des absences par élèves -->
							<select id="regroup" name="regroup" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
								<option value="oui" <?php if ((isset($regroup) && $regroup == 'oui') || !isset($regroup)) echo 'selected'; ?>>Regrouper les absences par élèves</option>
								<option value="non" <?php if (isset($regroup) && $regroup != 'oui') echo 'selected'; ?>>Afficher toutes les absences séparées</option>
							</select>
						</div>
					<?php } ?>
				</div>


        <div class="form-row mb-3">
          <?php if ($_SESSION['CnxGrp'] > 1) { ?>
            <div class="col">
              <select id="IDmotif" name="IDmotif" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
                <?php
                echo '<option value="0">'.$msg->read($ABSENT_MOTIF).'</option>';
                foreach ($motifs as $motif) {
                  if ($motif->IDdata == $IDmotif) $selected = 'selected'; else $selected = '';
                  echo '<option value="'.$motif->IDdata.'" '.$selected.'>'.$motif->texte.'</option>';
                }
                ?>
              </select>
            </div>

            <div class="col">
              <select id="year" name="year" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
                <?php
                $query  = "select min(_start) as min, max(_end) as max from absent_items";
                $years_select = $db->getRow("SELECT min(`_start`) as `min`, max(`_end`) as `max` from `absent_items` ");
                if (is_object($years_select) && $years_select->min) $min = date('Y', strtotime($years_select->min)); else $min = date('Y');
                if (is_object($years_select) && $years_select->max) $max = date('Y', strtotime($years_select->max)); else $max = date('Y');
                for ($i = $min; $i <= $max; $i++) {
                  if ($year == $i) $selected = 'selected'; else $selected = '';
                  echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                }
                ?>
              </select>
            </div>

            <div class="col">
              <select id="month" name="month" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
                <option value="0">Tous les mois</option>
                <?php
                  for ($i = 1; $i <= 12; $i++) {
                    if ($month == $i) $selected = 'selected'; else $selected = '';
                    echo '<option value="'.$i.'" '.$selected.'>'.getMonthNameByMonthNumber($i).'</option>';
                  }
                ?>
              </select>
            </div>

            <div class="col">
              <select id="day" name="day" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
                <option value="0">Tous les jours</option>
                <?php
                  for ($i = 1; $i <= 31; $i++) {
                    if ($day == $i) $selected = 'selected'; else $selected = '';
                    echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                  }
                ?>
              </select>
            </div>

            <?php if($month != 0 && $day != 0) { ?>
              <div class="col text-center">
                <div class="input-group text-center">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input type="checkbox" name="week" id="week" value="on" <?php if ($week == 'on') echo 'checked'; ?> onchange="document.forms.formulaire.submit()" title="<?php echo $msg->read($ABSENT_CURRWEEK, date("W", mktime(0, 0, 0, $month, $day, $year))); ?>">
                    </div>
                  </div>
                  <div class="input-group-append">
                    <span class="input-group-text" id="inputGroup-sizing-sm"><?php echo $msg->read($ABSENT_CURRWEEK, date("W", mktime(0, 0, 0, $month, $day, $year))); ?></span>
                  </div>
                </div>
              </div>
            <?php } ?>

            <?php if(!isset($type) || !$type) { ?>
              <div class="col">
                <select id="abs_idmat" name="abs_idmat" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
                  <option value="0"><?php echo $msg->read($ABSENT_MATTER); ?></option>
                  <?php
                    $abs_idmat_select = $db->getAll("SELECT `_IDmat` as `IDmat`, `_titre` as `titre` from `campus_data` where `_visible` = 'O' AND `_lang` = ?s order by `_titre` ", $_SESSION["lang"]);
                    foreach ($abs_idmat_select as $abs_idmat_select_data) {
                      if ($abs_idmat == $abs_idmat_select_data->IDmat) $selected = 'selected'; else $selected = '';
                      echo '<option value="'.$abs_idmat_select_data->IDmat.'" '.$selected.'>'.$abs_idmat_select_data->titre.'</option>';
                    }
                  ?>
                </select>
              </div>
            <?php } ?>

            <div class="col">
              <?php echo getClassSelect('IDclass', 'IDclass', @$IDclass, true, 'formulaire', true, false, 'btn d-none d-sm-inline-block btn-sm shadow-sm'); ?>
            </div>

          <?php } ?>

        </div>

				<div class="form-row">
					<div class="col">
						<!-- Champ de recherche -->
						<div class="input-group">
						  <input type="text" class="form-control shadow-sm" id="appendedInputButton" placeholder="Entrez un nom" name="IDalpha" value="<?php if (isset($IDalpha)) echo stripslashes($IDalpha); ?>">
						  <div class="input-group-append">
						    <button class="btn btn-outline-secondary shadow-sm" type="submit">Ok</button>
						  </div>
						</div>
					</div>
				</div>
      </form>
    </div>

  </div>
</div>

<?php

// affichage des absences
$query  = $db->parse("select distinctrow ");
$query .= $db->parse("user_id._name as user_id_name, user_id._fname as user_id_fname, user_id._ID as user_id_ID, user_id._IDclass as user_id_IDclass, user_id._email as user_id_email, user_id._tel as user_id_tel, ");
$query .= $db->parse("absent_items._email as absent_items_email, absent_items._sms as absent_items_sms, absent_items._ID as absent_items_ID, absent_items._IP as absent_items_IP, absent_items._IDdata as absent_items_IDdata, absent_items._start as absent_items_start, absent_items._IDctn as absent_items_IDctn, absent_items._texte as absent_items_texte, absent_items._IDitem as absent_items_IDitem, absent_items._IDmod as absent_items_IDmod, absent_items._isok as absent_items_isok, ");
$query .= $db->parse("campus_classe._ident as campus_classe_ident, absent_items._end as absent_items_end, absent_items._date as absent_items_date, absent_items._valid as absent_items_valid, absent_items._file as absent_items_file, absent_items._IDabs as absent_items_IDabs ");
$query .= $db->parse("from user_id, absent_items, campus_classe, absent_data ");
$query .= (!isset($type) || !$type) ? ", ctn_items " : " " ;
if($month != 0 && $day != 0 && $week == "on")
{
  $tmp = get_semaine_from_week(date("W", mktime(0, 0, 0, $month, $day, $year))-1, $year);
  $query .= $db->parse("where ((absent_items._start <= ?s AND absent_items._end >= ?s) OR ", $tmp[0].' 23:59:59', $tmp[0].' 00:00:00');
  $query .= $db->parse("(absent_items._start <= ?s AND absent_items._end >= ?s) OR ", $tmp[1].' 23:59:59', $tmp[1].' 00:00:00');
  $query .= $db->parse("(absent_items._start <= ?s AND absent_items._end >= ?s) OR ", $tmp[2].' 23:59:59', $tmp[2].' 00:00:00');
  $query .= $db->parse("(absent_items._start <= ?s AND absent_items._end >= ?s) OR ", $tmp[3].' 23:59:59', $tmp[3].' 00:00:00');
  $query .= $db->parse("(absent_items._start <= ?s AND absent_items._end >= ?s) OR ", $tmp[4].' 23:59:59', $tmp[4].' 00:00:00');
  $query .= $db->parse("(absent_items._start <= ?s AND absent_items._end >= ?s) OR ", $tmp[5].' 23:59:59', $tmp[5].' 00:00:00');
  $query .= $db->parse("(absent_items._start <= ?s AND absent_items._end >= ?s)) ", $tmp[6].' 23:59:59', $tmp[6].' 00:00:00');
}
// Année
elseif((!isset($month) || $month == 0) && (!isset($day) || $day == 0)) $query .= $db->parse("where (absent_items._start >= ?s AND absent_items._start <= ?s) ", $year.'-01-01 00:00:00', $year.'-12-31 23:59:59');
// Mois
elseif((!isset($month) || $month != 0) && (!isset($day) || $day == 0)) $query .= $db->parse("where (absent_items._start >= ?s AND absent_items._start <= ?s) ", $year.'-'.$month.'-01 00:00:00', $year.'-'.$month.'-31 23:59:59');
// date sans mois
elseif((!isset($month) || $month == 0) && (!isset($day) || $day != 0)) $query .= $db->parse("where (absent_items._start LIKE ?s OR absent_items._end LIKE ?s) ", $year.'-%-'.$day.'%', $year.'-%-'.$day.'%');
// Jour
elseif((!isset($month) || $month != 0) && (!isset($day) || $day != 0)) $query .= $db->parse("where (absent_items._start <= ?s AND absent_items._end >= ?s) ", $year.'-'.$month.'-'.$day.' 23:59:59', $year.'-'.$month.'-'.$day.' 00:00:00');

$query .= $db->parse("AND user_id._ID = absent_items._IDabs AND absent_data._IDdata = absent_items._IDdata ");

if (!isset($type) || !$type) $query .= $db->parse("AND absent_items._IDctn ");
if (isset($name) && $name) $query .= $db->parse("AND user_id._name like ?s ", $name.'%');

if ($_SESSION['CnxGrp'] >= 1) {
  if (isset($IDclass) && $IDclass) $query .= $db->parse("AND user_id._IDclass = ?i ", $IDclass);
}
else $query .= $db->parse("AND user_id._IDclass = ?i ", $_SESSION["CnxClass"]);
$query .= $db->parse("AND user_id._IDclass = campus_classe._IDclass ");
if (isset($IDcentre) && $IDcentre) $query .= $db->parse("AND campus_classe._IDcentre = ?i ", $IDcentre);

// Si mode filtre groupe
if(isset($IDgrp) && $IDgrp) $query .= $db->parse("AND (user_id._ID = ?s) ", $IDgroupeleve);
elseif (isset($IDeleve) && $IDeleve != '' && $IDeleve != 0) $query .= $db->parse("AND user_id._ID = ?i ", $IDeleve);

if (isset($IDalpha) && $IDalpha) $query .= $db->parse("AND CONCAT(user_id._name, ' ', user_id._fname) LIKE ?s ", '%'.$IDalpha.'%');
$query .= $db->parse("AND user_id._IDgrp = '1' ");

if (isset($IDmotif) && $IDmotif) $query .= $db->parse("AND absent_items._IDdata = ?i ", $IDmotif);
if (isset($abs_idmat) && $abs_idmat) $query .= $db->parse("AND ctn_items._IDmat = ?i ", $abs_idmat);

if ((isset($type) && $type != '2') || !isset($type))
{
  if ((isset($type) && $type == 0)) $query .= $db->parse("AND absent_items._IDctn != '0' ");
  if (isset($type) && $type == 1) $query .= $db->parse("AND absent_items._IDctn LIKE '0' ");
  if ((isset($type) && $type == 0)) $query .= $db->parse("AND absent_items._IDctn = ctn_items._IDitem ");
}

if (!isset($typeabs) || $typeabs == 'ABS') $query .= $db->parse("AND absent_data._texte NOT LIKE '%[NC]%' ");
if (!isset($typeabs) || $typeabs == 'NC') $query .= $db->parse("AND absent_data._texte LIKE '%[NC]%' ");
if (isset($etat) && $etat == 'O') $query .= $db->parse("AND absent_items._valid = 'O' ");
elseif (isset($etat) && $etat == 'N') $query .= $db->parse("AND absent_items._valid = 'N' ");

$query .= $db->parse("ORDER BY absent_items._start DESC ");
$query_absences = $query;
$absences = $db->getAll($query);
$nbelem = $db->affectedRows();
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, true); ?><?php echo $msg->getTrad('_RESULTs'); ?>: <?php echo $nbelem; ?></h6>
  </div>
  <div class="card-body">
    <?php if ($nbelem) { ?>
      <table class="table table-striped">
        <tr>
          <?php if ((!isset($regroup) || $regroup != 'oui') && $_SESSION['CnxGrp'] != 1) { ?>
            <th style="white-space: nowrap;">
              <?php if ($_SESSION['CnxGrp'] == 4) { ?>
                <input type="checkbox" id="cocheTout" name="isok" value="1" title="<?php echo $msg->read($ABSENT_TTSEL); ?>">
                <a class="align-middle fas fa-question link-unstyled" role="button" data-placement="left" data-toggle="popover" title="" data-content="<?php echo $msg->read($ABSENT_INFOCOCHE); ?>"></a>
              <?php } ?>
            </th>
          <?php } ?>
          <th>
            <?php if($IDclass != 0) { ?>
              <select id="IDeleve" name="IDeleve" onchange="changeIDeleve()" class="custom-select">
                <option value="0"></option>
                  <?php
                    $query  = "select _ID, _name, _fname from user_id where _IDclass = '$IDclass' ANd _IDgrp = '1' AND _adm = 1 AND _visible = 'O' order by _name, _fname ";
                    $result = mysqli_query($mysql_link, $query);
                    while ($row = mysqli_fetch_row($result)) {
                      if ($IDeleve == $row[0]) $selected = 'selected'; else $selected = '';
                      echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'&nbsp;'.$row[2].'</option>';
                    }
                  ?>
              </select>
            <?php } else echo 'Élève'; ?>
          </th>
          <th>Promotion</th>
          <th>Matière</th>
          <th>Raison</th>
          <th>Commentaire</th>
          <th>Date/durée</th>
          <th class="d-print-none"></th>
          <th class="d-print-none"></th>
        </tr>


        <form id="selection" action="index.php" method="post">
          <?php
            $hidden_form_data = array('item', 'cmde', 'IDcentre', 'type', 'IDgroup', 'IDmotif', 'abs_idmat', 'IDclass', 'year', 'month', 'day', 'week');
            foreach ($hidden_form_data as $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
          ?>

          <?php
          if (isset($isok) && $isok) $okall  = 'checked'; else $okall  = '';
      		$count = $same_ligne = $last_motif = 0;
      		$count_user = 1;
      		$last_justif = $last_date = '';
          $count_by_user = $justification_by_user = array();

          foreach ($absences as $absence) {
            // On stocke le nombre d'absences par élèves
            if (!isset($count_by_user[$absence->user_id_ID])) $count_by_user[$absence->user_id_ID] = 0;
            $count_by_user[$absence->user_id_ID] = $count_by_user[$absence->user_id_ID] + 1;

            // Si une seule absence non justifiée, alors en vue regroupé tout injustifié
            if ($absence->absent_items_valid == 'N' || $absence->absent_items_valid == 'A') $justification_by_user[] = $absence->user_id_ID;

            if (((isset($regroup) && $regroup == 'oui') && (isset($last_user) && $absence->user_id_ID != $last_user) || !isset($last_user)) || (!isset($regroup) || $regroup != 'oui')) {
              // Si on est étudiant, on ne peux que voir les absences sans les modifier
              if ($_SESSION["CnxAdm"] != 255 || $_SESSION["CnxGrp"] != 4) $disbl = $rdonly = 'disabled'; else $disbl = $rdonly = '';

        			// autorisation de rentrer en cours
        			if( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 )
        			{
                if ($absence->absent_items_valid == 'O') $checked = 'checked'; else $checked = '';

                $valid = '<div class="custom-control custom-checkbox">';
                  $valid .= '<input type="checkbox" class="custom-control-input" id="cbok_'.$absence->absent_items_IDitem.'" name="cbok['.$absence->absent_items_IDitem.']" value="'.$absence->user_id_ID.'" '.$rdonly.' '.$okall.' '.$checked.'>';
                  $valid .= '<label class="custom-control-label" for="cbok_'.$absence->absent_items_IDitem.'"></label>';
                $valid .= '</div>';
        			}
        			else $valid = '';

        			// envoie du mél aux parents
              if (strlen($absence->user_id_email) && ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4)) $mailto = '<a href="mailto:'.$absence->user_id_email.'"><i class="fas fa-envelope"></i></a>';
              else $mailto = '';

        			// recherche du cours
              $cours = $db->getRow("SELECT `campus_data`.`_titre` as `titre`, `ctn_items`.`_note`, `ctn_items`.`_IDcours` as `IDcours` from `campus_data`, `ctn_items` where `ctn_items`.`_IDitem` = ?i AND `campus_data`.`_IDmat` = `ctn_items`.`_IDmat` AND `campus_data`.`_lang` = ?s limit 1 ", $absence->absent_items_IDctn, $_SESSION["lang"]);

        			// recherche du prof
              if (isset($cours->IDcours) && $cours->IDcours) $profid = $db->getRow("SELECT `user_id`.`_ID` as `id` from `user_id`, `edt_data` WHERE `edt_data`.`_ID` = `user_id`.`_ID` AND `edt_data`.`_IDx` = ?i limit 1 ", $cours->IDcours)->id;
              else $profid = 0;

        			// lecture de l'auteur du message
        			$from   = getUserNameByID($absence->absent_items_ID).' '.$absence->absent_items_date;

              // Lien pour mettre à jours l'absence
              if ($_SESSION['CnxAdm'] == 255 OR $_SESSION['CnxGrp'] == 4 OR $_SESSION['CnxID'] == $auth[0] OR ($_SESSION['CnxGrp'] == 2 AND $_SESSION['CnxID'] == $absence->absent_items_ID)) $update = '<a href="#" onclick="editAbsence('.$absence->absent_items_IDitem.')"><i class="fas fa-pencil-alt"></i></a>';
              else $update = '';

              // Lien pour supprimer l'absence
              if ($_SESSION['CnxAdm'] == 255 OR $_SESSION['CnxGrp'] == 4 OR $_SESSION['CnxID'] == $auth[0] OR ($_SESSION['CnxGrp'] == 2 AND $_SESSION['CnxID'] == $absence->absent_items_ID)) $delete = '<a href="'.myurlencode('index.php?'.$href.'&IDitem='.$absence->absent_items_IDitem.'&submit=delete').'"><i class="fas fa-trash"></i></a>';
              else $delete = '';

              // Le lien de la pièce jointe
              if ($absence->absent_items_file != '') $isPj = '<a href="'.myurlencode('download/absent/download_handler.php?item='.$absence->absent_items_IDitem).'" target="_blank"><i class="fas fa-link"></i></a>';
              else $isPj = '';

              unset($justify, $justified_cell_class);
              // Bouton de validation/dévalidation de l'absence
              if($absence->absent_items_valid == 'N' && ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4))
              {
                $justified_cell_class = 'bg-danger';
                $text_justifié = $msg->read($ABSENT_NOTJUSTIFIED);

                if($regroup == 'oui') $justify =  '<a class="checkval" href="#"><i class="fas fa-check" title="'.$msg->read($ABSENT_JUSTIFIER).'"></i></a>';
                else $justify =  '<a href="index.php?item=63&cmde=show&validation='.$absence->absent_items_IDitem.'&IDcentre='.$IDcentre.'&name=&IDclass='.$IDclass.'&year='.$year.'&month='.$month.'&day='.$day.'"><i class="fas fa-check" title="'.$msg->read($ABSENT_JUSTIFIER).'"></i></a>';
              }
              elseif($absence->absent_items_valid == 'O' && ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4))
              {
                $justified_cell_class = 'bg-success';
                $text_justifié = $msg->read($ABSENT_JUSTIFIED);

                if($regroup == 'oui') $justify =  '<a class="checkval" href="#"><i class="fas fa-times" title="'.$msg->read($ABSENT_DEJUSTIFIER).'"></i></a>';
                else $justify =  '<a href="index.php?item=63&cmde=show&devalidation='.$absence->absent_items_IDitem.'&IDcentre='.$IDcentre.'&name=&IDclass='.$IDclass.'&year='.$year.'&month='.$month.'&day='.$day.'"><i class="fas fa-times" title="'.$msg->read($ABSENT_DEJUSTIFIER).'"></i></a>';
              }
              else $justify = '';

              // Pas le bouton pour justifier les absences en vue regroupement
              if ((isset($regroup) && $regroup == 'oui') || $_SESSION['CnxGrp'] == 1) $justify = $valid = $isPj = $update = $delete = '';

              echo '<tr id="tr_'.$same_ligne.'_'.$absence->user_id_ID.'">';
                // Boîte à cocher pour justifier
                if ((!isset($regroup) || $regroup != 'oui') && $_SESSION['CnxGrp'] != 1) echo '<td class="text-center">'.$valid.'</td>';

                // Nom de l'élève et badge de regroupement
                echo '<td style="white-space: nowrap;">';
                  if (isset($regroup) && $regroup == 'oui') echo '<a href="?item='.$item.'&cmde='.$cmde.'&IDalpha='.$absence->user_id_name.'nbsp;'.$absence->user_id_fname.'&regroup=non"><span class="badge badge-secondary" id="count_badge_user_'.$absence->user_id_ID.'"></span></a>&nbsp;';
                  echo '<a href="'.myurlencode('index.php?item=38&cmde=account&ID='.$absence->user_id_ID).'">'.$absence->user_id_name.'&nbsp;'.$absence->user_id_fname.'</a>';
                echo '</td>';

                // Le nom de la matière
                echo '<td class="text-center">'.$absence->campus_classe_ident.'</td>';
                echo '<td>';
                  echo '<a href="#" data-placement="bottom" data-toggle="tooltip" title="'.getUserNameByID($profid).'">';
                    if (isset($cours->titre)) echo $cours->titre;
                  echo '</a>';
                echo '</td>';

                // Le motif
                echo '<td>';
                  echo '<select id="cbmotif[]" class="selectunique custom-select" name="cbmotif['.$absence->absent_items_IDitem.']" '.$disbl.'>';
                    echo '<option value="0">'.$msg->read($ABSENT_MOTIF).'</option>';
                    foreach ($motifs as $motif) {
                      if ($motif->IDdata == $absence->absent_items_IDdata) $selected = 'selected'; else $selected = '';
                      echo '<option value="'.$motif->IDdata.'" '.$selected.'>'.$motif->texte.'</option>';
                    }
                  echo '</select>';
                echo '</td>';

                // Commentaire
                echo '<td>';
                  echo '<textarea rows="1" class="textrem form-control" id="note_'.$absence->absent_items_IDitem.'" name="note['.$absence->absent_items_IDitem.']" cols="40" '.$rdonly.'>'.$absence->absent_items_texte.'</textarea>';
                  echo '<input type="hidden" name="to['.$absence->absent_items_IDitem.']" value="'.$absence->user_id_email.':'.$absence->user_id_tel.'">';
                  echo '<input type="hidden" name="IDitem['.$count.']" value="'.$absence->absent_items_IDitem.'">';
                echo '</td>';

                // Date de début et de fin
                echo '<td><strong>'.date('d/m/Y H:i', strtotime($absence->absent_items_start)).'</strong>'.' - '.date('d/m/Y H:i', strtotime($absence->absent_items_end)).'</td>';

                // On affiche les boutons d'options
                echo '<td style="white-space: nowrap;">'.$justify.'&nbsp;'.$update.'&nbsp;'.$isPj.'&nbsp;'.$delete.'&nbsp;'.$mailto.'</td>';

                // Case justifiée
                echo '<td user_id="'.$absence->user_id_ID.'" class="justify_td align-middle text-center text-white '.$justified_cell_class.'">'.$text_justifié.'</td>';
              echo '</tr>';
            }

      			$count++;
      			$count_user++;
      			$last_user = $absence->user_id_ID;
      			$last_motif = $absence->absent_items_IDdata;
      			$last_justif = $absence->absent_items_valid;
      			$last_date = date('d/m/Y', strtotime($absence->absent_items_start));
      		}
          foreach ($count_by_user as $user_id => $count_user) echo '<div class="count_user d-none" user_id="'.$user_id.'" id="count_user_'.$user_id.'">'.$count_user.'</div>';  // Utilisé pour afficher le nb d'absences de chaque élèves

          // Si une seule absence injustifiée en vue regroupé, tout injustifié
          foreach ($justification_by_user as $user_id) echo '<div class="dejustify_user d-none" user_id="'.$user_id.'"></div>';
          ?>

          <input type="hidden" name="submit" value="1">
        </form>
      </table>
    <?php } ?>
  </div>
  <div class="card-footer text-muted">
    <button class="btn btn-success" type="submit" form="selection"><?php echo $msg->getTrad('_SAVE'); ?></button>
  </div>
</div>



<script>
  // Pour afficher le nombre d'absences d'un élève
  $('.count_user').each(function(){
    var user_id = $(this).attr('user_id');
    var count = $(this).html();
    $('#count_badge_user_' + user_id).html(count);
    if (count > 1) $('#count_badge_user_' + user_id).removeClass('badge-secondary').addClass('badge-primary');
  });

  // Pour que si une seule absence injustifiée en vue regroup, tout injustifié
  <?php if (isset($regroup) && $regroup == 'oui') { ?>
    $(document).ready(function(){
      $('.dejustify_user').each(function(){
        var user_id = $(this).attr('user_id');
        console.log(user_id);
        $('.justify_td[user_id="' + user_id + '"]').removeClass('bg-success').addClass('bg-danger').html('<?php echo $msg->read($ABSENT_NOTJUSTIFIED); ?>');

      })
    });
  <?php } ?>


</script>





<script>
// Lorque l'on clique sur la checkbox pour tout séléctionner, on les check toutes
	$("#cocheTout").change(function(){
		if (this.checked) $("input[id*=cbok_]").prop("checked", true);
		else $("input[id*=cbok_]").prop("checked", false);
	});

	// Quand on check une checkbox, si elle sont toutes check, alors on check la générale, sinon, on la uncheck
	$("input[id*=cbok_]").change(function(){
		if (!this.checked) $("#cocheTout").prop("checked", false);
	});


	<?php
		$query = "SELECT DISTINCT user_id._IDclass, campus._IDclass, campus._ident FROM user_id, campus_classe campus WHERE user_id._IDclass = campus._IDclass AND user_id._adm = '1' ";
		$result = mysqli_query($mysql_link, $query);
		$promoSelect = "<option value=\"0\" selected disabled>Séléctionnez une promotion</option>";
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $promoSelect .= "<option value=\"".$row[1]."\">".$row[2]."</option>";
		$promoSelect = addslashes($promoSelect);
	?>

	// Quand on clique sur le boutnon 'Feuille d'émargement:
	function generateEmargementDoc() {
		var html_element = "<select name=\"promo\" onchange=\"saveDateAndPromo()\" id=\"promo\"><?php echo @$promoSelect; ?></select><br><input type=\"date\" min=\"<?php echo date("Y-m-d"); ?>\" onchange=\"saveDateAndPromo()\" id=\"date_emargement\" name=\"date_emargement\">";

		swal({
			title: 'Générer la feuille d\'émargement',
			html: html_element,
			customClass: 'swal2-overflow',
		}).then(function(result) {

			var date = $("#dateForEmargement").val();
			var promo = $("#promoForEmargement").val();
			var win = window.open('absent_emargement_print.php?classID=' + promo + '&date=' + date, '_blank');
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
		var date = $("#date_emargement").val();
		var promo = $("#promo").val();
		$("#dateForEmargement").val(date);
		$("#promoForEmargement").val(promo);
	}

</script>




<!-- Modal de création/modif d'absence -->
<div class="modal fade" id="newAbsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nouvelle absence</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
        <button type="submit" class="btn btn-primary" form="abs_edit">Enregistrer</button>
      </div>
    </div>
  </div>
</div>



<script>
  $(document).ready(function(){
    $('#newAbsBtn').click(function(){
      newAbsence();
    });
  });

  // Ouverture et remplissage du modal de nouvelle absence
  function newAbsence() {
    $.ajax({
      url : 'include/ajax/absent/edit_absent.php',
      type : 'POST',
      data : '',
      async: true,
      dataType : 'html',
      success : function(code_html, statut){
        $('#newAbsModal').find('.modal-body').html(code_html);
        $('#newAbsModal').modal('show');
      }
    });
  }

  // Ouverture et remplissage du modal de modification d'absence
  function editAbsence(absid) {
    $.ajax({
      url : 'include/ajax/absent/edit_absent.php',
      type : 'POST',
      data : 'IDabs=' + absid,
      async: true,
      dataType : 'html',
      success : function(code_html, statut){
        $('#newAbsModal').find('.modal-body').html(code_html);
        $('#newAbsModal').modal('show');
      }
    });
  }
</script>


<script>
  // Quand on sélectionne un élève, on met la valeur dans le formulaire et on envoi le formulaire
  function changeIDeleve() {
    var IDeleve = $('#IDeleve').val();
    $('#IDeleve_hidden').val(IDeleve);
    document.forms.formulaire.submit();
  }
</script>

<input type="hidden" id="dateForEmargement">
<input type="hidden" id="promoForEmargement">
