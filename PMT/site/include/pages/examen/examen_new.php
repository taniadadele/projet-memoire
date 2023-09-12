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
 *		module   : examen_new.htm
 *		projet   : Page de création/modification des examen
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 14/03/19
 */

$cmde = $_GET['cmde'];

if (isset($_GET['idexam']) && $_GET['idexam'] != "")
{
  $IDexam = $_GET['idexam'];
  $query = "SELECT * FROM `campus_examens` WHERE `_ID_exam` = '".$IDexam."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $typeExam     = $row[1];
    $nameExam     = $row[2];
    $coefExam     = $row[3];
    $noteMaxExam  = $row[4];
    if ($row[5] == "O") $oralExam = 1;
    else $oralExam = 0;
    $idPMA        = $row[6];
    $parentExam   = $row[7];
  }
}
else
{
  $IDexam       = "";
  $typeExam     = "";
  $nameExam     = "";
  $coefExam     = "1";
  $noteMaxExam  = "20";
  $oralExam     = 0;
  $idPMA        = "";
}

  if (isset($_POST['isNew']) && $_POST['isNew'] == "modif")
  {
    $typeExam     = addslashes($_POST['typeExam']);
    $nameExam     = addslashes($_POST['nameExam']);
    $coefExam     = addslashes($_POST['coefExam']);
    $noteMaxExam  = addslashes($_POST['noteMaxExam']);
    $parentExam   = addslashes($_POST['parentExam']);



    if ($_POST['oralexam'] == "1")
    {
      $oralExamInsert = "O";
      $oralExam = 1;
    }
    else
    {
      $oralExamInsert = "N";
      $oralExam = 0;
    }
    $idPMA        = $_POST['PMA'];

    $query  = "UPDATE `campus_examens` SET `_type` = '".$typeExam."', `_nom` = '".$nameExam."', `_coef` = '".$coefExam."', `_note_max` = '".$noteMaxExam."', `_oral` = '".$oralExamInsert."', ";
    $query .= "`_ID_pma` = ".$idPMA.", ";
    if ($parentExam != "") $query .= "`_ID_parent` = '".$parentExam."' ";
    else $query .= "`_ID_parent` = NULL ";
    $query .= "WHERE `_ID_exam` = '".$IDexam."' ";
    @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }
  elseif (isset($_POST['isNew']) && $_POST['isNew'] == "new" && $IDexam == "")
  {
    $typeExam     = addslashes($_POST['typeExam']);
    $nameExam     = addslashes($_POST['nameExam']);
    $coefExam     = addslashes($_POST['coefExam']);
    $noteMaxExam  = addslashes($_POST['noteMaxExam']);
    $parentExam   = addslashes($_POST['parentExam']);
    if ($_POST['oralexam'] == "1")
    {
      $oralExamInsert = "O";
      $oralExam = 1;
    }
    else
    {
      $oralExamInsert = "N";
      $oralExam = 0;
    }
    $idPMA        = $_POST['PMA'];

    // $query = "UPDATE `campus_examens` SET `_type` = '".$typeExam."', `_nom` = '".$nameExam."', `_coef` = '".$coefExam."', `_note_max` = '".$noteMaxExam."', `_oral` = '".$oralExamInsert."', `_ID_pma` = ".$idPMA." ";
    // if ($parentExam != "") $query .= ", `_ID_parent` = '".$parentExam."' ";
    // else $query .= ", `_ID_parent` = NULL ";
    // $query .= "WHERE `_ID_exam` = '".$IDexam."' ";

    if ($parentExam == "") $IDParentExam = "NULL";
    else $IDParentExam = "'".$parentExam."'";

    $query = "INSERT INTO `campus_examens`(`_ID_exam`, `_type`, `_nom`, `_coef`, `_note_max`, `_oral`, `_ID_pma`, `_ID_parent`) VALUES (NULL, '".$typeExam."', '".$nameExam."', '".$coefExam."', '".$noteMaxExam."', '".$oralExamInsert."', ".$idPMA.", ".$IDParentExam.") ";
    @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
    $IDexam = mysqli_insert_id($mysql_link);
  }




  if (isset($_GET['submit']) && $_GET['submit'] == "del")
  {
    $query = "DELETE FROM `campus_examens` WHERE `_ID_exam` = '".$IDexam."' ";
    @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }

  $submit   = @$_GET["submit"];
?>




<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($EXAMEN_LIST); ?></h1>
</div>




<div class="card shadow mb-4">
  <!-- <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Résultats: </h6>
  </div> -->
  <div class="card-body">
  	<form id="formulaire" action="index.php?item=<?php echo $item; ?>&cmde=<?php echo $cmde; ?>&idexam=<?php echo $IDexam; ?>&tri=on" method="post">



      <!-- Nom de l'exam -->
      <div class="form-group">
        <label for="nameExam"><?php echo $msg->read($EXAMEN_NAME_INPUT); ?></label>
        <input type="text" class="form-control" id="nameExam" name="nameExam" value="<?php echo $nameExam; ?>" required>
      </div>

      <!-- Type d'exam -->
      <div class="form-group">
          <label for="typeExam"><?php echo $msg->read($EXAMEN_TYPE_INPUT); ?></label>
          <select class="form-control custom-select" id="typeExam" name="typeExam" required>
            <?php
              if ($typeExam == "") $selected = "selected";
              else $selected = "";
              echo "<option value=\"\" ".$selected." disabled>".$msg->read($EXAMEN_TYPE_INPUT_SELECTED)."</option>";
              $examType = getParam('type-examen');
              $examType = json_decode($examType, TRUE);
              foreach ($examType as $key => $value) {
                if ($typeExam == $key) $selected = "selected";
                else $selected = "";
                echo "<option value=\"".$key."\" ".$selected.">".$value."</option>";
              }
            ?>
          </select>
        </div>

        <!-- Examen initial (si rattrapage) -->
        <div class="form-group" id="rattrapage_row">
          <label for="parentExam">Certificat initial</label>
          <select class="form-control custom-select" id="parentExam" name="parentExam">
            <?php
              if ($parentExam == "") $selected = "selected";
              else $selected = "";
              echo "<option value=\"\" ".$selected.">Certificat initial (SI RATTRAPAGE)</option>";
              $query = "SELECT `_ID_exam`, `_nom` FROM `campus_examens` WHERE `_ID_parent` IS NULL AND `_ID_exam` != '".$IDexam."' AND `_type` = 3 ";
              $result = mysqli_query($mysql_link, $query);
              while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                if ($parentExam == $row[0]) $selected = "selected"; else $selected = "";
                echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
              }
            ?>
          </select>
        </div>

        <!-- Examen oral ? -->
        <div class="form-group form-check form-control-lg">
          <?php if ($oralExam == 1) $checked = "checked"; else $checked = ""; ?>
          <input type="checkbox" class="form-check-input" id="oralexam" name="oralexam" value="1" <?php $checked; ?>>
          <label class="form-check-label" for="oralexam"><?php echo $msg->read($EXAMEN_ORAL_INPUT); ?></label>
        </div>

        <!-- PMA -->
        <div class="form-group">
          <label for="PMA"><?php echo $msg->read($EXAMEN_PMA_INPUT); ?></label>
          <select class="form-control custom-select" id="PMA" name="PMA">
            <?php
              $selected_option = "";
              if ($idPMA == 0) $selected_option = "selected";
              echo '<option value="0" '.$selected_option.'>Sélectionnez une matière</option>';
              $selected_option = "";
              $listeAnnee = json_decode(getParam("annee-niveau"), TRUE);
              $query2 = "SELECT * FROM `pole_mat_annee` WHERE 1 ORDER BY `_ID_year` ASC, `_ID_pole` ASC, `_ID_matiere` ASC ";
              $result2 = mysqli_query($mysql_link, $query2);
              while ($row = mysqli_fetch_array($result2, MYSQLI_NUM)) {
                if ($idPMA == $row[0]) $selected_option = "selected"; else $selected_option = "";
                echo '<option value="'.$row[0].'" '.$selected_option.'>'.$listeAnnee[$row[1]].' - '.getPoleNameByIdPole($row[2]).' - '.getMatNameByIdMat($row[3]).'</option>';
              }
            ?>
          </select>
        </div>

        <!-- Coef -->
        <div class="form-group">
          <label for="coefExam"><?php echo $msg->read($EXAMEN_COEF_INPUT); ?></label>
          <input type="number" class="form-control" id="coefExam" name="coefExam" value="<?php echo $coefExam; ?>" required>
        </div>

        <!-- Note max -->
        <div class="form-group">
          <label for="noteMaxExam"><?php echo $msg->read($EXAMEN_NOTE_MAX_INPUT); ?></label>
          <input type="number" class="form-control" id="noteMaxExam" name="noteMaxExam" value="<?php echo $noteMaxExam; ?>" required>
        </div>


        <?php if ($IDexam != "") $valueHidden = "modif"; else $valueHidden = "new"; ?>
        <input type="hidden" name="isNew" value="<?php echo $valueHidden; ?>">



        <button class="btn btn-danger" type="button" onclick="window.location.href = 'index.php?item=<?php echo $item; ?>';"><?php echo $msg->read($EXAMEN_CANCEL); ?></button>
        <button class="btn btn-success" type="submit"><?php echo $msg->read($EXAMEN_VALIDATE); ?></button>
    	<?php
        //
        // echo "<table style=\"width: 100%;\">";
        //
        //
        //
        //
        //
        //
        //   echo "<tr>";
        //     echo "<td class=\"labelForData\">";
        //       echo "<button type=\"submit\" class=\"btn btn-success\">".$msg->read($EXAMEN_VALIDATE)."</button>";
        //       echo "<br>";
        //       echo "<a href=\"index.php?item=".$item."\">";
        //         echo "<span class=\"btn btn-danger\" style=\"margin-top: 5px;\">".$msg->read($EXAMEN_CANCEL)."</span>";
        //       echo "</a>";
        //     echo "</td>";
        //   echo "</tr>";
        // echo "</table>";
      ?>

  	</form>


    <script>
    jQuery("#typeExam").on('change', function(){
      checkIfRattrapage();
    });

    checkIfRattrapage();

    function checkIfRattrapage() {
      if (jQuery("#typeExam").val() != 4)
      {
        jQuery("#rattrapage_row").hide();
        jQuery("#parentExam").val('');
      }
      else jQuery("#rattrapage_row").show();
    }
    </script>



  </div>
</div>
