
  <h5>Actions</h5>

  <table class="width100">
    <tr>
      <td></td>
      <td colspan="3">
      <span class="btn btn-danger" id="emptyCacheButton" style="margin: 20px 20px; 10px 20px;"><?php echo $msg->read($CONFIG_EMPTY_CACHE_BUTTON); ?></span>
      <span class="btn btn-danger" id="sendMailDisponibilityEdt">Envoyer les demandes de confirmation de cours en attente</span>
      <span class="badge">Nombres de mails en attente d'envois: <span id="countOfPeoplesToSendMessageTo"></span></span>
      </td>
    </tr>

  </table>

  <hr>
  <h5>Paramètres</h5>


  <?php $paramList = stripslashes(trim(getParam('paramGestionList'))); ?>


  <div id="parameters" style="display: block;">
    <table class="width100">
      <tr style="font-weight: bold;">
        <td class="align-right">
          <?php print($msg->read($CONFIG_CODE)); ?>
        </td>
        <td class="align-center" style="width: 25%;">
          <?php print($msg->read($CONFIG_VALUE)); ?>
        </td>
        <td class="align-center">
          <?php print($msg->read($CONFIG_COMMENT)); ?>
        </td>
        <td></td>
      </tr>
      <?php
        $query  = "SELECT * FROM `parametre` ";
        $result = mysqli_query($mysql_link, $query);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
          if (strpos($paramList, "\"".$row[0]."\"") !== false)
          {
            echo "<tr>";
            	echo "<td class=\"align-right\" style=\"width: 20% !important;\">";
            		echo "<label for=\"input_value_".addslashes($row[0])."\" style=\"font-weight: bold;\">".$row['0']."</label>";
            	echo "</td>";
            	echo "<td class=\"align-right\">";

            if (substr($row[1], 0, 1) == "{") echo "<textarea class=\"form-control\" rows=\"5\" id=\"input_value_".addslashes($row[0])."\" name=\"input_value_".addslashes($row[0])."\" style=\"width: 90%\">".stripslashes($row[1])."</textarea>";
            else echo "		<input type=\"text\" class=\"form-control\" id=\"input_value_".addslashes($row[0])."\" name=\"input_value_".addslashes($row[0])."\" style=\"width: 90%\" value=\"".$row[1]."\" />";
            	echo "</td>";
            	echo "<td class=\"align-right\">";
            		echo "<input type=\"text\" class=\"form-control\" id=\"input_comment_".addslashes($row[0])."\" name=\"input_comment_".addslashes($row[0])."\" style=\"width: 90%; margin-bottom: 0px !important;\" value=\"".$row[3]."\" />";
            	echo "</td>";
            	echo "<td class=\"align-center\" style=\"vertical-align: middle;\">";
            		echo "<span class=\"btn btn-success updateParam\" paramCode=\"".addslashes($row[0])."\">".$msg->read($CONFIG_VALIDATION_BUTTON)."</span>";
            	echo "</td>";
            echo "</tr>";
          }


        }
      ?>




    </table>
  </div>
