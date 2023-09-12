<h5>Forfait</h5>

<?php
if (isset($newforfait)) {
  if ($newforfait > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);
  $result = mysqli_query($mysql_link, "select _Nom from forfait where _IDforfait = '$newforfait' limit 1");
  $row    = mysqli_fetch_row($result);
  echo '<div class="form-row">';
    echo '<input type="text" class="form-control col-10" id="identforfait" name="identforfait" size="30" value="'.$row[0].'">';
    echo '&nbsp;<input type="submit" class="btn btn-success col-1" name="submit" value="'.$value.'">';
  echo '</div>';
  echo '<input type="hidden" name="tablidx" value="7">';
  echo '<input type="hidden" name="idrec" value="'.$newforfait.'">';
  echo '<input type="hidden" name="pane" value="paneforfait">';
}
else {
?>
  <table class="table table-striped">
    <tr>
      <th style="width: 1%;"><i class="fas fa-trash"></i></th>
      <th><?php echo $msg->read($CONFIG_IDENT); ?></th>
    </tr>
    <?php
    $query  = "select _IDforfait, _Nom from forfait order by _IDforfait ";
    $result = mysqli_query($mysql_link, $query);
    while ($data = mysqli_fetch_row($result)) {
      echo '<tr>';
        $update = '<a href="'.$mylink.'&tablidx=7&newforfait='.$data[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
        echo '<td>';
          echo '<input type="checkbox" id="delforfait_'.$data[0].'" name="delforfait[]" value="'.$data[0].'">';
        echo '</td>';
        echo '<td>'.$data[0].'. '.$data[1].'&nbsp;'.$update.'</td>';
      echo '</tr>';
      echo '<tr></tr>'; // Juste pour la coloration du tableau
    }
    echo '<tr><td colspan="2"><a href="'.$mylink.'&tablidx=7&newforfait=-1" class="btn btn-secondary"><i class="fas fa-plus"></i>&nbsp;'.$msg->read($CONFIG_ADDRECORD).'</a></td></tr>';
    ?>
  </table>
<?php } ?>
