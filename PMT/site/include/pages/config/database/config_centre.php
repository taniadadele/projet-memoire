<h5><?php echo $msg->read($CONFIG_CENTERS); ?></h5>

<table class="table table-striped">
  <?php
  echo '<tr>';
    echo '<th style="width: 1%;"><i class="fas fa-eye"></i></th>';
    echo '<th style="width: 1%;"><i class="fas fa-trash"></i></th>';
    echo '<th>'.$msg->read($CONFIG_IDENT).'</th>';
  echo '</tr>';

  $query  = "select _IDcentre, _ident, _visible from config_centre where _lang = '".$_SESSION["lang"]."' order by _IDcentre ";
  $result = mysqli_query($mysql_link, $query);

  while ($centre = mysqli_fetch_row($result)) {
    echo '<tr>';
      if ($centre[2] == "O") $checked = 'checked'; else $checked = '';
      $update = '<a href="'.$mylink.'&tablidx=1&amp;newcentre='.$centre[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
      echo '<td><input type="checkbox" id="isc_'.$centre[0].'" name="isc[]" value="'.$centre[0].'" '.$checked.'></td>';
      echo '<td><input type="checkbox" id="delc_'.$centre[0].'" name="delc[]" value="'.$centre[0].'" onclick="return confirm(\'ATTENTION: La suppresion du centre entrainera la SUPPRESSION TOTALE ET DEFINITIVE des classes ainsi que des salles qui lui sont rattachées!\');"></td>';
      echo '<td>'.$centre[0].'. '.$centre[1].' '.$update.'</td>';
    echo '</tr>';
    echo '<tr></tr>'; // Juste pour la coloration du tableau
  }

  if (isset($newcentre) && $newcentre) {
    if ($newcentre > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);
    $result = mysqli_query($mysql_link, "select _ident from config_centre where _IDcentre = '$newcentre' AND _lang = '".$_SESSION["lang"]."' limit 1");
    $row    = mysqli_fetch_row($result);
    echo '<tr>';
      echo '<td colspan = "2"></td>';
      echo '<td>';
        echo '<div class="form-row">';
          echo '<input type="text" id="ident" name="ident" size="30" class="form-control col-10" value="'.$row[0].'" placeholder="'.$msg->read($CONFIG_NOMCENTRE).'">';
          echo '&nbsp;<button type="submit" name="submit" class="btn btn-success col-1">'.$value.'</button>';
        echo '</div>';
      echo '</td>';
    echo '</tr>';

    echo '<input type="hidden" name="tablidx" value="1">';
    echo '<input type="hidden" name="currentPane" value="#tab_centers">';
    echo '<input type="hidden" name="idrec" value="'.$newcentre.'">';
    echo '<input type="hidden" name="submit" value="'.$value.'">';
  }
  else {
    echo '<tr><td colspan="3"><a href="'.$mylink.'&tablidx=1&newcentre=-1" class="btn btn-secondary"><i class="fas fa-plus"></i>&nbsp;'.$msg->read($CONFIG_ADDRECORD).'</a></td></tr>';
  }
  ?>
</table>
