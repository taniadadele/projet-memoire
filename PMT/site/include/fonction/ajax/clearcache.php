<?php

  require_once "../../../config.php";
  require_once "../../../include/sqltools.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

  if (isset($_GET['action'])) $action = $_GET['action'];
  else $action = "";

  if ($action == "clearcache")
  {

    $query = "DELETE FROM `campus_syllabus` WHERE `_IDPMA` = '' ";
    @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));



    echo "success";
  }

?>
