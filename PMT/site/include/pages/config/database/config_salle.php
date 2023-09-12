<h5><?php echo $msg->read($CONFIG_SALLES); ?></h5>

<?php
if (isset($newsalle))
{
  echo $alert->info('Visioconférences', 'Pour que l\'option de visioconférence soit disponible sur l\'emploi du temps, le nom de la salle dois contenir \'visio\'');
  if ($newsalle > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);
  $result = mysqli_query($mysql_link, "select _title from edt_items where _IDitem = '$newsalle'");
  $row    = mysqli_fetch_row($result);
  echo '<input type="hidden" name="tablidx" value="6">';
  echo '<input type="hidden" name="pane" value="panesalle">';
  echo '<input type="hidden" name="idrec" value="'.$newsalle.'">';
  echo '<div class="form-row">';
    echo '<input type="text" class="form-control col-10" id="text" name="text" size="30" value="'.$row[0].'" placeholder="'.$msg->read($CONFIG_NOMSALLE).'" autofocus>';
    echo '&nbsp;<input type="submit" value="'.$value.'" class="btn btn-success" name="submit">';
  echo '</div>';
}
else {
?>
  <table class="table table-striped">
    <tr>
      <th style="width: 1%;"><i class="fas fa-trash"></i></th>
      <th><?php echo $msg->read($CONFIG_IDENT); ?></th>
    </tr>
    <?php
    // lecture des salles
    $query  = "SELECT _IDitem, _title FROM edt_items WHERE _IDcentre = '$IDcentre' ORDER BY _IDitem";
    $result = mysqli_query($mysql_link, $query);
    while ($salle = mysqli_fetch_row($result))
    {
      echo '<tr>';
        $update = '<a href="'.$mylink.'&tablidx=6&newsalle='.$salle[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
        echo '<td><input type="checkbox" id="delsa_'.$salle[0].'" name="delsa[]" value="'.$salle[0].'" onchange="$(\'#gosalle\').val(\'go\');"></td>';
        echo '<td>'.$salle[0].'. '.$salle[1].'&nbsp;'.$update.'</td>';
      echo '</tr>';
      echo '<tr></tr>'; // Juste pour la coloration du tableau
    }
    echo '<tr>';
      echo '<td colspan="6">';
        echo '<a href="'.$mylink.'&tablidx=6&newsalle=-1" class="btn btn-secondary"><i class="fas fa-pencil-alt"></i>&nbsp;'.$msg->read($CONFIG_ADDRECORD).'</a>';
      echo '</td>';
    echo '</tr>';
    ?>
  </table>
<?php } ?>
