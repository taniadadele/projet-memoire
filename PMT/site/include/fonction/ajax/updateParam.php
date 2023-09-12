<?php
  session_start();
  require_once "../../../config.php";
  require_once "../../../include/sqltools.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  include ('../protection_input.php');
  require("../auth_tools.php");

  if (isUserConnected())
  {
    if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
    else $action = "";

    if (isset($_POST['paramValue'])) $paramValue = addslashes(stripslashes($_POST['paramValue']));
    else $paramValue = "";
    if (isset($_POST['paramComment'])) $paramComment = addslashes(stripslashes($_POST['paramComment']));
    else $paramComment = "";
    if (isset($_POST['paramCode'])) $paramCode = addslashes(stripslashes($_POST['paramCode']));
    else $paramCode = "";


    if ($action == "updateParam")
    {
      // echo "Boujour";
      $query = "UPDATE `parametre` SET `_valeur` = '".addslashes(html_entity_decode(stripslashes($paramValue)))."', `_comm` = '".addslashes(stripslashes($paramComment))."' WHERE `_code` = '".$paramCode."' ";
      // $query = "UPDATE `parametre` SET `_valeur` = '2019' WHERE `parametre`.`_code` = 'END_Y' ";
      $result = mysqli_query($mysql_link, $query);
      // echo $query;
      if ($result == "1") echo "success";
      else echo "error";
    }
  }

?>
