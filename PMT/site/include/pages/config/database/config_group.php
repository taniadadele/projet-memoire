<h5><?php print($msg->read($CONFIG_GROUPS)); ?></h5>

<table class="table table-striped">
  <tr>
    <th style="width: 1%;"><i class="fas fa-eye"></i></th>
    <th style="width: 1%;"><i class="fas fa-trash"></i></th>
    <th><?php echo $msg->read($CONFIG_IDENT); ?></th>
  </tr>
  <?php
  // lecture des groupes
  $query  = "select _IDgrp, _ident, _visible, _IDcat from user_group where _lang = '".$_SESSION["lang"]."' order by _IDgrp ";
  $result = mysqli_query($mysql_link, $query);
  while ($grp = mysqli_fetch_row($result)) {
    echo '<tr>';
      if ($grp[2] == 'O') $checked = 'checked'; else $checked = '';
      $update = '<a href="'.$mylink.'&tablidx=2&newgrp='.$grp[0].'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
      echo '<td><input type="checkbox" id="isg_'.$grp[0].'" name="isg[]" value="'.$grp[0].'" '.$checked.'></td>';
      echo '<td><input type="checkbox" id="delg_'.$grp[0].'" name="delg[]" value="'.$grp[0].'" onclick="checkForPoleDeletion();"></td>';
      echo '<td>'.$grp[0].'. '.$grp[1].' '.$update.'</td>';
    echo '</tr>';
    echo '<tr></tr>'; // Juste pour la coloration du tableau
  }

  if (isset($newgrp) && $newgrp) {
    if ($newgrp > 0) $value = $msg->read($CONFIG_MODIFY); else $value = $msg->read($CONFIG_CREAT);
    // affichage des groupes
    $result = mysqli_query($mysql_link, "select _ident, _delay from user_group where _IDgrp = '$newgrp' AND _lang = '".$_SESSION["lang"]."' limit 1");
    $row    = mysqli_fetch_row($result);
    echo '<tr>';
      echo '<td colspan="3">';
        echo '<div class="form-row">';
          echo '<input type="text" id="ident" class="form-control col-5" name="ident" size="30" value="'.$row[0].'" placeholder="'.$msg->read($CONFIG_NOMGROUPE).'">';
          echo '<div class="col-1" style="text-align: right;">'.$msg->read($CONFIG_ISVALID).':</div>';
          echo '<input type="text" id="delay" class="form-control col-4" name="delay" size="20" value="'.$row[1].'" placeholder="0000-00-00 00:00:00">';
          echo '&nbsp;<button type="submit" name="submit" class="btn btn-success col-1">'.$value.'</button>';
        echo '</div>';
      echo '</td>';
    echo '</tr>';

    echo '<input type="hidden" name="submit" value="'.$value.'">';
    echo '<input type="hidden" name="tablidx" value="2">';
    echo '<input type="hidden" name="idrec" value="'.$newgrp.'">';

  }
  else {
    echo '<tr><td colspan="3"><a href="'.$mylink.'&tablidx=1&newgrp=-1" class="btn btn-secondary"><i class="fas fa-plus"></i>&nbsp;'.$msg->read($CONFIG_ADDRECORD).'</a></td></tr>';
  }
  ?>

</table>
