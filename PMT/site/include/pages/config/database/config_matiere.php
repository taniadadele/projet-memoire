<h5><?php echo $msg->read($CONFIG_MATTER); ?></h5>


<?php
if (isset($newmat)) {
  if ($newmat > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);

  $result = mysqli_query($mysql_link, "select _titre, _color, _option, _code, _IDmat, _type from campus_data where _IDmat = '$newmat' AND _lang = '".$_SESSION["lang"]."' limit 1");
  $row    = mysqli_fetch_row($result);

  $color1 = '#'.$row[1];
  if ($color1 == '#') $color1 = '#aaaaaa';

  // Select des forfaits
  if(getParam('FORFAIT') == 1)
  {
    $resulta = mysqli_query($mysql_link, "SELECT _IDforfait FROM mat_forfait WHERE _IDmat = $newmat ");
    $rowa    = mysqli_fetch_row($resulta);
    $querylf  = "select _IDforfait, _Nom from forfait ";
    $resultlf = mysqli_query($mysql_link, $querylf);
    $forfaitval = '<div class="form-group col-md-3">';
      $forfaitval .= '<label for="forfaitval">Forfait</label>';
      $forfaitval .= '<select id="forfaitval" name="forfaitval" class="form-control custom-select">';
      $forfaitval .= '<option value=""></option>';
        while ($rowlf = mysqli_fetch_row($resultlf)) {
          if ($rowa[0] == $rowlf[0]) $selected = 'selected'; else $selected = '';
          $forfaitval .= '<option value="'.$rowlf[0].'" '.$selected.'>'.$rowlf[1].'</option>';
        }
      $forfaitval .= '</select>';
    $forfaitval .= '</div>';
  }
  ?>


  <input type="hidden" name="tablidx" value="4">
  <input type="hidden" name="newmat" value="<?php echo $newmat; ?>">
  <input type="hidden" name="pane" value="panemat">
  <input type="hidden" name="idrec" value="<?php echo $row[4]; ?>">
  <input type="hidden" name="currentPane" value="#tab_matieres">
  <input type="hidden" name="type_matiere" value="1">

  <div class="form-row">

    <!-- Le code de la matière -->
    <div class="form-group col-md-1">
      <label for="code"><?php echo $msg->read($CONFIG_NOMCODE); ?></label>
      <input type="text" class="form-control" id="code" name="code" size="10" value="<?php echo $row[3]; ?>" required>
    </div>

    <!-- Le nom de la matière -->
    <div class="form-group col-md-6">
      <label for="titre"><?php echo $msg->read($CONFIG_NOMMATIERE); ?></label>
      <input type="text" class="form-control" id="titre" name="titre" size="30" value="<?php echo $row[0]; ?>" required>
    </div>


    <?php if(getParam("FORFAIT")) echo $forfaitval; ?>

    <!-- Couleur de la matière -->
    <div class="form-group col-md-2">
      <label for="color1"><?php echo $msg->read($CONFIG_NOMCOULEUR); ?></label>
      <div class="input-group">
        <div class="input-group-prepend">
        <div class="input-group-text"><div class="dot" id="color1_dot" style="height: 1rem; width: 1rem; background-color: <?php echo $color1; ?>;"></div></div>
      </div>
      <input type="text" class="form-control" name="color1" id="color1" data-color="<?php echo $color1; ?>">
    </div>
    </div>

    <script>
      // Colorpicker
      $(document).ready(function(){
        $('#color1').colorpicker({
          inline: false,
          format: 'hex'
        }).on('colorpickerChange colorpickerCreate', function (e) {
          $('#color1_dot').css('background-color', e.value.toHexString());
        })
      })
    </script>
  </div>
  <input type="submit" value="<?php echo $value; ?>" name="submit" class="btn btn-success" />



<?php } else { ?>

  <table class="table table-striped">
    <tr>
      <th style="width: 1%;"><i class="fas fa-eye"></i></th>
      <th style="width: 1%;"><i class="fas fa-trash"></i></th>
      <th><?php echo $msg->read($CONFIG_IDENT); ?></th>
    </tr>
    <?php
    // On liste les matières
    $query  = "select _IDmat, _titre, _visible, _option, _code, _color, _type from campus_data where _lang = '".$_SESSION["lang"]."' and _type = 1 order by _titre";
    $result = mysqli_query($mysql_link, $query);
    while ($mat = mysqli_fetch_row($result)) {
      echo '<tr>';
        if ($mat[2] == 'O') $checked = 'checked'; else $checked = '';
        if ($mat[6] == 1) $update = '<a href="'.$mylink.'&tablidx=4&newmat='.$mat[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>'; else $update = "";

        $valforfait = '';
        if(getParam('FORFAIT') == 1)
        {
          $resulta = mysqli_query($mysql_link, "SELECT _IDforfait FROM mat_forfait WHERE _IDmat = $mat[0] ");
          $rowa    = mysqli_fetch_row($resulta);
          if ($rowa[0])
          {
            $resulta = mysqli_query($mysql_link, "SELECT _Nom FROM forfait WHERE _IDforfait = $rowa[0] ");
            $rowb    = mysqli_fetch_row($resulta);
            $valforfait = ' ('.$rowb[0].')';
          }
        }

        echo '<td><input type="checkbox" id="ism_'.$mat[0].'" name="ism[]" value="'.$mat[0].'" '.$checked.'></td>';
        if ($mat[6] == 1) echo '<td><input type="checkbox" id="delm_'.$mat[0].'" name="delm[]" value="'.$mat[0].'"></td>';
        else echo "<td></td>";
        echo '<td style="vertical-align: middle;"><div class="dot" style="background-color: #'.$mat[5].';"></div>&nbsp;&nbsp;'.$mat[1].'&nbsp;['.$mat[4].']&nbsp;'.$valforfait.'&nbsp;'.$update.'</td>';
      echo '</tr>';
      echo '<tr></tr>'; // Juste pour la coloration du tableau
    }
    ?>

    <tr>
      <td colspan="3">
        <a href="<?php echo $mylink; ?>&tablidx=4&newmat=-1" class="btn btn-secondary"><i class="fas fa-plus"></i>&nbsp;<?php echo $msg->read($CONFIG_ADDRECORD); ?></a>
      </td>
    </tr>
  </table>
<?php } ?>



<style>
.dot {
  height: 25px;
  width: 25px;
  border-radius: 50%;
  display: inline-block;
  vertical-align: middle;
}
</style>
