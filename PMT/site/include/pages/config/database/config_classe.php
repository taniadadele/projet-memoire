<h5><?php echo $msg->read($CONFIG_CLASS); ?> </h5>

<table class="table table-striped">
  <tr>
    <th style="width: 1%;"><i class="fas fa-eye"></i></th>
    <th style="width: 1%;"><i class="fas fa-trash"></i></th>
    <th><?php echo $msg->read($CONFIG_IDENT); ?></th>
  </tr>

  <?php
  // lecture des classes
  $query  = 'select _IDclass, _ident, _visible, _code, `_graduation_year` from campus_classe where _IDcentre = "'.$IDcentre.'" order by _code, _IDclass DESC ';
  $result = mysqli_query($mysql_link, $query);

  while ($classe = mysqli_fetch_row($result)) {
    echo '<tr>';
      if ($classe[2] == 'O') $checked = 'checked'; else $checked = '';
      $update = '<a href="'.$mylink.'&tablidx=3&newclass='.$classe[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
      echo '<td><input type="checkbox" id="isk_'.$classe[0].'" name="isk[]" value="'.$classe[0].'" '.$checked.'></td>';
      echo '<td><input type="checkbox" id="delk_$classe'.$classe[0].'" name="delk[]" value="'.$classe[0].'"></td>';
      echo '<td>'.$classe[0].'. '.$classe[1].' ['.$classe[3].'] '.$classe[4].' '.$update.'</td>';
    echo '</tr>';
    echo '<tr></tr>'; // Juste pour la coloration du tableau
  }

  if (isset($newclass) && $newclass)
  {
    if ($newclass > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);
    $result = mysqli_query($mysql_link, "select _ident, _code, _graduation_year from campus_classe where _IDclass = '$newclass'");
    $row    = mysqli_fetch_row($result);

    echo '<tr>';
      echo '<td colspan="3">';
        echo '<div class="form-row">';
          echo '<input type="text" class="form-control col-4" id="text" name="text" size="30" value="'.$row[0].'" placeholder="'.$msg->read($CONFIG_NOMCLASS).'" required>';
          echo '&nbsp;<input type="number" class="form-control col-1" id="grad_year" name="grad_year" size="15" value="'.$row[2].'" placeholder="'.$msg->read($CONFIG_GRADUATION_YEAR).'">';
          echo '&nbsp;<span id="currentPromotion"></span>';
        echo '</div>';
      echo '</td>';
    echo '</tr>';

    echo '<input type="hidden" name="code" value="'.$row[1].'">';
    echo '<input type="hidden" name="tablidx" value="3">';
    echo '<input type="hidden" name="pane" value="paneclass">';
    echo '<input type="hidden" name="idrec" value="'.$newclass.'">';



    /*----------- MATIERES ------------*/
    $tab_matclass = getMatClass($newclass, $_SESSION["lang"]);
    echo '<tr></tr>'; // Juste pour la coloration du tableau
    echo '<tr>';
      echo '<td colspan="3">';
        echo '<h5>'.$msg->read($CONFIG_MATCLASS).'</h5>';
        echo '<ul>';
          $query  = "select _IDmat, _titre, _visible, _option, _code from campus_data where _lang = '".$_SESSION["lang"]."' AND _type = 1 order by _titre ";
          $result = mysqli_query($mysql_link, $query);
          while ($mat = mysqli_fetch_row($result))
          {
            if (array_key_exists($mat[0], $tab_matclass)) $checked = 'checked'; else $checked = '';
            echo '<li style="list-style: none; display: inline-block; margin: 5px; background-color: #cccccc; border-radius: 4px; padding: 3px;"><input type="checkbox" id="matclassid_'.$mat[0].'" name="matclass[]" value="'.$mat[0].'" style="/* margin-top: -2px */" '.$checked.'> <label for="matclassid_'.$mat[0].'"> '.$mat[1].'</label></li>';
          }
        echo "</ul>";
      echo '</td>';
    echo '</tr>';


    // ----------- PROFS ------------
    $tab_profclass = getClasseProf($newclass);
    echo '<tr></tr>'; // Juste pour la coloration du tableau
    echo '<tr>';
      echo '<td colspan="3">';
        echo '<h5>'.$msg->read($CONFIG_MATPROF).'</h5>';
        echo '<ul>';
          // lecture des profs
          $Query  = "select _ID, _name, _fname from user_id ";
          $Query .= "where _IDgrp = 2 ";
          $Query .= "order by _ID";

          // affichage des matières
          $result = mysqli_query($mysql_link, $Query);

          while ($prof = mysqli_fetch_row($result))
          {
            if (array_key_exists($prof[0], $tab_profclass)) $checked = 'checked'; else $checked = '';
            echo '<li style="list-style: none; display: inline-block; margin: 5px; background-color: #cccccc; border-radius: 4px; padding: 3px;"><input type="checkbox" id="profclassid_'.$prof[0].'" name="profclass[]" value="'.$prof[0].'" style="margin-top: -2px" '.$checked.'/> <label for="profclassid_'.$prof[0].'"> '.$prof[1].' '.$prof[2].'</label></li>';
          }
        echo '</ul>';
      echo '</td>';
    echo '</tr>';
  }
  else echo '<tr><td colspan="3"><a href="'.$mylink.'&tablidx=3&newclass=-1" class="btn btn-secondary"><i class="fas fa-plus"></i>&nbsp;'.$msg->read($CONFIG_ADDRECORD).'</a>';
  ?>

</table>

<?php if (isset($newclass)) { ?>
  <hr />
  <input type="submit" value="<?php echo $value; ?>" class="btn btn-success" name="submit" />
<?php } ?>
