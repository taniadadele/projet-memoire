<?php
/*-----------------------------------------------------------------------*
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
 *		module   : rattrapage_view.php
 *		projet   : Page de visualisation des rattrapages
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 14/04/20
 */


if (isset($_POST['IDclass'])) $IDclass = addslashes($_POST['IDclass']);
else $IDclass = 0;
if (isset($_POST['IDuv'])) $IDuv = addslashes($_POST['IDuv']);
else $IDuv = 0;
if (isset($_POST['IDexam'])) $IDexam = addslashes($_POST['IDexam']);
else $IDexam = 0;
if (isset($_POST['IDeleve'])) $IDeleve = addslashes($_POST['IDeleve']);
else $IDeleve = 0;
$list_periodes = json_decode(getParam('periodeList'), TRUE);
$year = getParam('START_Y');

// Si on viens d'arriver sur la page, on affiche la 1ère année
if (!isset($_POST['IDclass']) && !isset($_POST['IDuv']) && !isset($_POST['IDeleve'])) {
  $query = 'SELECT _ident, _IDclass FROM campus_classe WHERE _visible = "O" ORDER BY _code ASC LIMIT 1 ';
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    if ($IDclass == 0) $IDclass = $row[1];
  }
}


if ($IDeleve != 0 && $IDclass == 0) $IDclass = getUserClassIDByUserID($IDeleve);

$listStudentClass = array();
$query = 'SELECT _ID, _name, _fname FROM user_id WHERE _adm = 1 AND _IDclass != 0 ';
if ($IDclass != 0) $query .= "AND _IDclass = '".$IDclass."' ";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
  $listStudentClass[$row[0]] = $row[1].' '.$row[2];
}


// Vérification si étudiant:
if ($_SESSION['CnxAdm'] != 255) {
  $IDeleve = $_SESSION['CnxID'];
  $IDclass = getUserClassIDByUserID($IDeleve);
}



$year = getParam('START_Y');
?>


<!-- Titre -->
<div class="maintitle">
	<div style="text-align: center; font-weight: bold;">
    Liste des rattrapages
	</div>
</div>


<!-- Contenu -->
<div class="maincontent">
  <div style="text-align:center;">

    <!-- Champ de recherche -->
    <form id="formulaire" action="index.php?item=<?php echo $item; ?><?php if (isset($_GET['elem'])) echo '&elem='.$_GET['elem']; ?>" method="post">
      <div style="display: inline-block; float: right;">
        <!-- Select de la classe -->
        <!-- onchange='document.forms.formulaire.submit()' -->
        <?php if ($_SESSION['CnxAdm'] == 255) { ?>
          <select name='IDclass'>
            <option value="0">Sélectionnez une classe</option>
            <?php
              $query = 'SELECT _ident, _IDclass FROM campus_classe WHERE _visible = "O" ORDER BY _code ASC ';
              $result = mysql_query($query);
  						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
                // if ($IDclass == 0) $IDclass = $row[1];
                if ($IDclass == $row[1]) $selected = 'selected';
                else $selected = '';
                // if ($IDeleve != 0 && $selected == '') continue;
                echo '<option value="'.$row[1].'" '.$selected.'>'.$row[0].'</option>';
              }
            ?>
          </select>
        <?php } ?>


        <?php if ($_GET['elem'] == 'exam' || (!isset($_GET['elem']) && getParam('liste_rattrapage_show_exam'))) { ?>
          <select name='IDexam' onchange='document.forms.formulaire.submit()'>
            <option value="0">Sélectionnez un Examen</option>
            <?php
              $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND _year = '".$year."' AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 1) ";
              if ($IDclass != 0) $query .= "AND _IDclass = '".$IDclass."' ";
              $result = mysql_query($query);
              while ($row = mysql_fetch_array($result, MYSQL_NUM))
              {
                $uvID = $row[0] - 100000;
                // if (isUVRattrapage($uvID)) continue;
                if ($IDexam == $uvID) $selected = 'selected';
                else $selected = '';
                if (getUVNameByID($uvID) != '') {
                  echo '<option value="'.$uvID.'" '.$selected.'>'.getUVNameByID($uvID).'</option>';
                }
                else {
                  echo '<option value="'.$uvID.'" '.$selected.'>'.getPoleNameByIdPole(getPoleIDByUVID($uvID)).' - '.getMatNameByIdMat(getMatIDByUVID($uvID)).'</option>';
                }

              }
            ?>
          </select>
        <?php } ?>

        <?php if ($_GET['elem'] == 'certif') { ?>
          <select name='IDuv' onchange='document.forms.formulaire.submit()'>
            <option value="0">Sélectionnez un certificat</option>
            <?php
              $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND _year = '".$year."' AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 3) ";
              if ($IDclass != 0) $query .= "AND _IDclass = '".$IDclass."' ";
              $result = mysql_query($query);
              while ($row = mysql_fetch_array($result, MYSQL_NUM))
              {
                $uvID = $row[0] - 100000;
                // if (isUVRattrapage($uvID)) continue;
                if ($IDuv == $uvID) $selected = 'selected';
                else $selected = '';
                if (getUVNameByID($uvID) != '') {
                  echo '<option value="'.$uvID.'" '.$selected.'>'.getUVNameByID($uvID).'</option>';
                }
                else {
                  echo '<option value="'.$uvID.'" '.$selected.'>'.getPoleNameByIdPole(getPoleIDByUVID($uvID)).' - '.getMatNameByIdMat(getMatIDByUVID($uvID)).'</option>';
                }

              }
            ?>
          </select>
        <?php } ?>


        <?php if ($_SESSION['CnxAdm'] == 255) { ?>
          <select name='IDeleve'>
            <option value="0">Sélectionnez un élève</option>
            <?php
              $query = 'SELECT _ID, _name, _fname FROM user_id WHERE _visible = "O" AND _adm = 1 ';
              if ($IDclass != 0) $query .= "AND _IDclass = '".$IDclass."' ";
              $query .= "AND _IDclass != 0 ";
              $query .= 'ORDER BY _name ASC, _fname ASC ';
              $result = mysql_query($query);
  						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
                if ($IDeleve == $row[0]) $selected = 'selected';
                else $selected = '';
                echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].' '.$row[2].'</option>';
              }
            ?>
          </select>
        <?php } ?>

        <a href="exports.php?item=<?php echo $_GET['item']; ?>&IDclass=<?php echo $IDclass; ?>&IDuv=<?php echo $IDuv; ?>&IDeleve=<?php echo $IDeleve; ?>&elem=<?php echo $_GET['elem']; ?>" class="btn btn-default" style="color: black; margin-bottom: 10px; margin-left: 5px;"><i class="fa fa-upload"></i></a>
      </div>
    </form>

    <hr style="width:100%;" />




    <div class="btn-group" role="group" aria-label="...">
      <?php if (getParam('liste_rattrapage_show_mat')) { ?>
        <a class="btn btn-default <?php if ($_GET['elem'] == 'mat' || !isset($_GET['elem'])) echo 'active'; ?>" href="?item=65&elem=mat">Matière</a>
      <?php } ?>
      <?php if (getParam('liste_rattrapage_show_exam')) { ?>
        <a class="btn btn-default <?php if ($_GET['elem'] == 'exam' || (!isset($_GET['elem']) && !getParam('liste_rattrapage_show_mat'))) echo 'active'; ?>" href="?item=65&elem=exam">Examen</a>
      <?php } ?>

      <a class="btn btn-default <?php if ($_GET['elem'] == 'certif' || (!isset($_GET['elem']) && !getParam('liste_rattrapage_show_mat') && !getParam('liste_rattrapage_show_exam'))) echo 'active'; ?>" href="?item=65&elem=certif">Certificat</a>
    </div>

    <br><br>

    <table class="table table-striped">
      <tr>
        <th>Nom</th>
        <th>Note</th>
        <th>Note rattrapage</th>
      </tr>
      <?php



        if ($_GET['elem'] == 'mat' || (!isset($_GET['elem']) && getParam('liste_rattrapage_show_mat'))) {
          $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE (( `_IDmat` > 100000 AND (`_IDmat` - 100000) NOT IN (SELECT _ID_exam FROM campus_examens WHERE _type != 1)) OR `_IDmat` < 100000) AND _year = '".$year."' ";
          $isMatAgv = true;
        }
        elseif ($_GET['elem'] == 'exam' || (!isset($_GET['elem']) && !getParam('liste_rattrapage_show_mat'))) {
          $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 1 OR _type = 2) AND _year = '".$year."' ";
          $isMatAgv = false;
        }
        else {
          $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 3 OR _type = 4) AND _year = '".$year."' ";
          $isMatAgv = false;
        }

        // switch ($_GET['elem']) {
        //   case 'exam':
        //     $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 1 OR _type = 2) AND _year = '".$year."' ";
        //     $isMatAgv = false;
        //     break;
        //   case 'certif':
        //     $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 3 OR _type = 4) AND _year = '".$year."' ";
        //     $isMatAgv = false;
        //     break;
        //   default:
        //   case 'mat':
        //     $query = "SELECT _IDmat, _IDdata FROM notes_data WHERE (( `_IDmat` > 100000 AND (`_IDmat` - 100000) NOT IN (SELECT _ID_exam FROM campus_examens WHERE _type != 1)) OR `_IDmat` < 100000) AND _year = '".$year."' ";
        //     $isMatAgv = true;
        //     break;
        // }


        if ($IDclass != 0) $query .= "AND _IDclass = '".$IDclass."' ";
        if ($IDuv != 0) $query .= "AND _IDmat = '".($IDuv + 100000)."' ";       // Si on ne veux qu'un seul exam
        if ($IDexam != 0) $query .= "AND _IDmat = '".($IDexam + 100000)."' ";       // Si on ne veux qu'un seul exam
        $query .= "GROUP BY _IDmat";

        $result = mysql_query($query);
        $oldMat = 0;

        while ($row = mysql_fetch_array($result, MYSQL_NUM))
        {
          if (!$isMatAgv) $uvID = $row[0] - 100000;
          else $uvID = $row[0];
          if (!$isMatAgv && isUVRattrapage($uvID)) continue;                                  // On ne veux pas les examens de type rattrapage
          elseif ($isMatAgv && $uvID > 100000) continue;
          if ($IDeleve == 0)
          {
            $header = '';
            $header = '<tr>';
              $header .= '<th colspan="3" style="border-top: 1px dashed grey !important;">';
                if (!$isMatAgv) $header .= getUVNameByID($uvID);
                else $header .= getPoleNameByIdPole(getPoleIDByPMAID($uvID)).' - '.getMatNameByIdMat(getMatIDByPMAID($uvID));
              $header .= '</th>';
            $header .= '</tr>';

            $oldMat = $uvID;
            foreach ($listStudentClass as $studentID => $studentName) {
              $note = '';
              $periodeList = json_decode(getParam('periodeList'), TRUE);
              $tempMat = '';
              foreach ($periodeList as $key => $value) {
                // On fait la moyenne sur la période
                $query_temp = "SELECT `_IDdata` FROM `notes_data` WHERE `_IDdata` != ".$row[1]." AND `_IDmat` = '".$row[0]."' ";
                $result_temp = mysql_query($query_temp);
                while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
                {
                  $tempMat .= " OR `_IDdata` = '".$row_temp[0]."' ";
                }
              }
              $showNote = false;
              if (!$isMatAgv) {
                $query_note = "SELECT `_value` FROM `notes_items` WHERE `_IDdata` = '".$row[1]."' AND `_IDeleve` = '".$studentID."' AND _value != '' ";
                $result_note = mysql_query($query_note);
                while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
                {
                  $note = $row_note[0];
                  $showNote = true;
                }
              }
              else {
                $notes_total = 0;
                $notes_coef = 0;
                $temp_query = "SELECT `_total`, `_coef`, `_IDdata` FROM `notes_data` WHERE (`_IDdata` = '".$row[1]."' ".$tempMat.")";
                $result_temp = mysql_query($temp_query);
                while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
                {
                  $total = explode(';', $row_temp[0]);
                  $coef = explode(';', $row_temp[1]);

                  $query_note = "SELECT `_value`, `_index` FROM `notes_items` WHERE `_IDeleve` = '".$studentID."' AND`_IDdata` = '".$row_temp[2]."' AND `_value` != '' ";
                  $result_note = mysql_query($query_note);
                  while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
                  {
                    // On calcule la moyenne
                    $notes_total += (($row_note[0] / $total[$row_note[1]]) * 20) * $coef[$row_note[1]];
                    $notes_coef += $coef[$row_note[1]];
                    $showNote = true;
                  }
                }
                $note = round($notes_total / $notes_coef, 1);
              }
              if (isset($notes_coef) && $notes_coef == 0) continue;
              if (!$showNote) continue;
              if (!$isMatAgv) $uvRattrapageNote = getUVRattNote($uvID, $studentID);
              else $uvRattrapageNote = getPMARattNote($uvID, $studentID);
              if ($note >= getParam('note_min_rattrapage') && $note < getParam('note_max_rattrapage')) {
                if ($header != '') {
                  echo $header;
                  $header = '';
                }
                echo '<tr>';
                  echo '<td style="padding-left: 20px;">'.$studentName.'</td>';
                  echo '<td>'.round($note, 1).'</td>';
                  echo '<td>'.round($uvRattrapageNote, 1).'</td>';
                echo '</tr>';
              }

              unset($note, $uvRattrapageNote);
            }
          }
          else
          {
            $periodeList = json_decode(getParam('periodeList'), TRUE);
            $tempMat = '';
            foreach ($periodeList as $key => $value) {
              // On fait la moyenne sur la période
              $query_temp = "SELECT `_IDdata` FROM `notes_data` WHERE `_IDdata` != ".$row[1]." AND `_IDmat` = '".$row[0]."' ";
              $result_temp = mysql_query($query_temp);
              while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
              {
                $tempMat .= " OR `_IDdata` = '".$row_temp[0]."' ";
              }
            }
            if (!$isMatAgv) {
              $query_note = "SELECT `_value` FROM `notes_items` WHERE `_IDdata` = '".$row[1]."' AND `_IDeleve` = '".$IDeleve."' AND _value != '' ";
              $result_note = mysql_query($query_note);
              while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
              {
                $note = $row_note[0];
              }
            }
            else {
              $notes_total = 0;
              $notes_coef = 0;
              $temp_query = "SELECT `_total`, `_coef`, `_IDdata` FROM `notes_data` WHERE (`_IDdata` = '".$row[1]."' ".$tempMat.")";
              $result_temp = mysql_query($temp_query);
              while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
              {
                $total = explode(';', $row_temp[0]);
                $coef = explode(';', $row_temp[1]);

                $query_note = "SELECT `_value`, `_index` FROM `notes_items` WHERE `_IDeleve` = '".$IDeleve."' AND`_IDdata` = '".$row_temp[2]."' AND `_value` != '' ";
                $result_note = mysql_query($query_note);
                while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
                {
                  // On calcule la moyenne
                  $notes_total += (($row_note[0] / $total[$row_note[1]]) * 20) * $coef[$row_note[1]];
                  $notes_coef += $coef[$row_note[1]];
                }
              }
              $note = round($notes_total / $notes_coef, 1);
            }
            if (isset($notes_coef) && $notes_coef == 0) continue;
            $uvRattrapageNote = getUVRattNote($uvID, $IDeleve);
            if ($note < getParam('note_min_rattrapage') || $note >= getParam('note_max_rattrapage')) continue;
            if ($note >= getParam('note_min_rattrapage') && $note < getParam('note_max_rattrapage')) {
              if ($header != '') {
                echo $header;
                $header = '';
              }
              echo '<tr>';
                echo '<th>';
                  if (!$isMatAgv) echo getUVNameByID($uvID);
                  else echo getPoleNameByIdPole(getPoleIDByPMAID($uvID)).' - '.getMatNameByIdMat(getMatIDByPMAID($uvID));
                echo '</th>';
                echo '<td>'.$note.'</td>';
                echo '<td>'.$uvRattrapageNote.'</td>';
              echo '</tr>';
            }
            unset($note, $uvRattrapageNote);
          }
        }
      ?>
    </table>

  </div>
</div>







<?php if (!isset($_GET['elem']) || $_GET['elem'] != 'certif') { ?>

  <script>
    jQuery('select[name="IDclass"]').on('change', function(){
      jQuery('select[name="IDeleve"]').val('0');
      document.forms.formulaire.submit();
    });

    jQuery('select[name="IDeleve"]').on('change', function(){
      jQuery('select[name="IDclass"]').val('0');
      document.forms.formulaire.submit();
    });
  </script>
<?php } else { ?>
  <script>
    jQuery('select[name="IDclass"]').on('change', function(){
      document.forms.formulaire.submit();
    });

    jQuery('select[name="IDeleve"]').on('change', function(){
      document.forms.formulaire.submit();
    });
  </script>


<?php } ?>
