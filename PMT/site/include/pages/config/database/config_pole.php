<h5><?php echo $msg->read($CONFIG_POLE); ?></h5>
<?php if (!isset($newpole) || $newpole == '') { ?>
  <table class="table table-striped">
    <tr>
      <th style="width: 1%;"><i class="fas fa-eye"></i></th>
      <th style="width: 1%;"><i class="fas fa-trash"></i></th>
      <th><?php echo $msg->read($CONFIG_IDENT); ?></th>
    </tr>
    <?php

    // lecture des pôles
    $query  = "SELECT * FROM `pole` WHERE 1 ORDER BY `pole`.`_ID` ASC ";
    $result = mysqli_query($mysql_link, $query);

    while ($pole = mysqli_fetch_row($result)) {
      if (isset($_GET['newpole']) && $_GET['newpole'] != '') continue;

      if ($pole[4] == 'O') $ckecked = 'checked'; else $ckecked = '';
      $update = '<a href="'.$mylink.'&tablidx=7&newpole='.$pole[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
      echo "<tr>";
        echo '<td><input type="checkbox" id="isp_'.$pole[0].'" name="isp[]" value="'.$pole[0].'" '.$ckecked.'></td>';
        echo '<td><input type="checkbox" id="delp_'.$pole[0].'" name="delp[]" value="'.$pole[0].'"></td>';
        echo '<td>'.$pole[0].'. '.$pole[1].'&nbsp;'.$update.'</td>';
      echo '</tr>';
      echo '<tr></tr>'; // Juste pour la coloration du tableau
    }
    echo '<tr>';
      echo '<td colspan="3">';
        echo '<a href="'.$mylink.'&tablidx=7&newpole=-1" class="btn btn-secondary"><i class="fas fa-plus"></i>&nbsp;'.$msg->read($CONFIG_ADDRECORD).'</a>';
      echo '</td>';
    echo '</tr>';
  echo '</table>';
}
else {
  if ($newpole > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);
  $result = mysqli_query($mysql_link, "select `_name` from `pole` where `_ID` = '$newpole'");
  $row = mysqli_fetch_row($result);
  ?>
  <input type="hidden" name="tablidx" value="8">
  <input type="hidden" name="pane" value="panepole">
  <input type="hidden" name="idrec" value="<?php echo $newpole; ?>">
  <input type="hidden" name="currentPane" value="#tab_poles">
  <div class="form-row">
    <!-- Le nom du pole -->
    <div class="form-group col-md-6">
      <label for="name_pole_text"><?php echo $msg->read($CONFIG_POLE_NAME); ?></label>
      <input type="text" class="form-control" id="name_pole_text" name="name_pole_text" size="30" value="<?php echo $row[0]; ?>" required>
    </div>
  </div>

  <h5><?php echo $msg->read($CONFIG_MATBYCLASS); ?></h5>

  <?php
  $current_pole = $_GET['newpole'];

  // On récupère les classes
  $classes = array();
  $query = "SELECT _code, _ident FROM campus_classe WHERE _visible = 'O' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result)) $classes[$row[0]] = $row[1];

  $query_annee = "SELECT `_code` FROM `campus_classe` WHERE `_visible` = 'O' ORDER BY `campus_classe`.`_code` ASC ";
  $result_annee = mysqli_query($mysql_link, $query_annee);
  while ($row_annee = mysqli_fetch_array($result_annee, MYSQLI_NUM)) {
    $title = $classes[$row_annee[0]].'&nbsp;('.getNiveauNameByNumNiveau($row_annee[0]).')';
    ?>
    <div class="form-row">
      <!-- Le nom du pole -->
      <div class="form-group col-md-6">
        <label for="Matiere_annee_<?php echo $row_annee[0]; ?>"><?php echo $title; ?></label>
        <input type="text" class="form-control" id="Matiere_annee_<?php echo $row_annee[0]; ?>" name="Matiere_annee_<?php echo $row_annee[0]; ?>" size="30">
      </div>
    </div>

    <script>
      $(document).ready(function() {
        // On initialise les tagit
        $('#Matiere_annee_<?php echo $row_annee[0]; ?>').tagit({
          autocomplete: {delay: 0, minLength: 1, source: 'getInfos.php?type=10', html: 'html'},
          allowDuplicates: false,
          singleField: false,
          fieldName: 'Matiere_annee_<?php echo $row_annee[0]; ?>[]',

          beforeTagRemoved: function(event, ui) {

            $('#errorWhenRemoveTagIT').html('0')
            checkIfSyllabusLink(event, ui);
            if ( $('#errorWhenRemoveTagIT').html() == '1'){
              $('#errorWhenRemoveTagIT').html('0');
              return false;
            }
          }
        });
      });


      function checkIfSyllabusLink(event, ui) {
        $.ajax({
          url : 'include/fonction/ajax/update_pma.php?action=checkForRemoveMatiere',
          type : 'POST', // Le type de la requête HTTP, ici devenu POST
          data : 'currentpole=' + ".$current_pole." + '&currentyear=' + ".$row_annee[0]." + '&tagUI=' + ui.tagLabel,

          //line added to get ajax response in sync
          async: false,

          dataType : 'html', // On désire recevoir du HTML
          success : function(code_html, statut){ // code_html contient le HTML renvoyé
            // alert(code_html);
            if (code_html != "error") $('#errorWhenRemoveTagIT').html('0');
            else
            {
              Toast.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'un syllabus est lié à cette matière'
              });
              $('#errorWhenRemoveTagIT').html("1");
            }
          }
        });
      }
    </script>

  <?php
  }

  $query_pma  = "SELECT DISTINCT _ID_pma, _ID_year, _ID_matiere FROM `pole_mat_annee` WHERE `_ID_pole` = '".$current_pole."' ";
  $result_pma = mysqli_query($mysql_link, $query_pma);
  $already_show = ';';
  $liste_pma = array();
  while ($row_pma = mysqli_fetch_array($result_pma)) {
    if (strpos($already_show, ';'.$row_pma[0].';') !== false) continue;
    $already_show .= $row_pma[0].';';
    unset($mat_name);
    $mat_name = getMatNameByIdMat($row_pma[2]);

    if ((!isset($mat_name) && $mat_name != '') || !isset($row_pma[2])) continue;
    $liste_pma[$row_pma[1]][] = array('name' => $mat_name, 'value' => $row_pma[2], 'id_pma' => $row_pma[0]);
  }

  // On ajoute les matières existantes dans les bonnes cases
  echo '<script>$(document).ready(function(){';
  // Pour chaque année
  foreach ($liste_pma as $year => $values_year) {
    // Pour chaque valeur
    foreach ($values_year as $key => $values_mat) {
      $content = $values_mat['name'].'<span style="display: none;" class="hidden">'.$values_mat['value'].'</span>';
      // echo '$("#Matiere_annee_'.$year.'").tagit("createTag", "'.str_replace('"', '\"', stripslashes($content)).'");';
      echo '$("#Matiere_annee_'.$year.'").tagit("createTag", "'.str_replace('"', "'", $content).'");';
    }
  }
  echo '})</script>';

  ?>
  <span style="display: none;" id="errorWhenRemoveTagIT">0</span>
<?php } ?>


<?php if (isset($newpole) && $newpole) { ?>
  <input type="hidden" name="submit" value="<?php echo $value; ?>">
<?php } ?>

<span style="display: none;" id="errorWhenRemovePole">0</span>
