<h5><?php print($msg->read($CONFIG_MOTIF)); ?></h5>
<?php
if (isset($newmotif)) {
  if ($newmotif > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);
  $result = mysqli_query($mysql_link, "select _texte from absent_data where _IDdata = '$newmotif' AND _lang = '".$_SESSION["lang"]."' limit 1");
  $row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

  echo '<input type="hidden" name="tablidx" value="5">';
  echo '<input type="hidden" name="idrec" value="'.$newmotif.'">';


  echo '<div class="form-row">';
    echo '<input type="text" class="form-control col-10" id="ident" name="ident" size="30" value="'.$row[0].'" placeholder="'.$msg->read($CONFIG_NOMMOTIF).'">';
    echo '&nbsp;<input type="submit" value="'.$value.'" name="submit" class="btn btn-success col-1">';
  echo '</div>';

}
else { ?>
  <table class="table table-striped">
    <tr>
      <th style="width: 1%;"><i class="fas fa-eye"></i></th>
      <th style="width: 1%;"><i class="fas fa-trash"></i></th>
      <th><?php echo $msg->read($CONFIG_IDENT); ?></th>
    </tr>
    <?php
    $query  = "select _IDdata, _texte, _visible from absent_data where _lang = '".$_SESSION["lang"]."' order by _IDdata ";
    $result = mysqli_query($mysql_link, $query);
    while ($data = mysqli_fetch_row($result)) {
      if ($data[2] == 'O') $checked = 'checked'; else $checked = '';
      echo '<tr>';
        $update = '<a href="'.$mylink.'&tablidx=5&newmotif='.$data[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
        echo '<td><input type="checkbox" id="ismo_'.$data[0].'" name="ismo[]" value="'.$data[0].'" '.$checked.'></td>';
        echo '<td><input type="checkbox" id="delmo_'.$data[0].'" name="delmo[]" value="'.$data[0].'"></td>';
        echo '<td>'.$data[0].'. '.$data[1].'&nbsp;'.$update.'</td>';
      echo '</tr>';
      echo '<tr></tr>'; // Juste pour la coloration du tableau
    }
    echo '<tr>';
      echo '<td colspan="3">';
        echo '<a href="'.$mylink.'&tablidx=5&newmotif=-1" class="btn btn-secondary"><i class="fas fa-plus"></i>&nbsp;'.$msg->read($CONFIG_ADDRECORD).'</a>';
      echo '</td>';
    echo '</tr>';
    ?>
  </table>


<?php } ?>
