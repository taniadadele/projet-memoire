<?php
  require_once "../../../config.php";
  require_once "../../../include/sqltools.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  require("../parametre.php");
  require("../relation.php");

  if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
  else $action = "";

  if (isset($_POST['ident'])) $ident = addslashes(stripslashes($_POST['ident']));
  else $ident;
  if (isset($_POST['email'])) $email = addslashes(stripslashes($_POST['email']));
  else $email;


  if ($action == "checkIdent")
  {
    $error = 0;
    if ($ident != "")
    {
      $query = "SELECT * FROM `user_id` WHERE `_ident` = '".$ident."' ";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $error = 1;
      }
    }

    if ($error == 1) echo "error";
    else echo "success";

  }

  if ($action == "checkMail")
  {
    $error = 0;
    if ($email != "")
    {
      $query = "SELECT * FROM `user_id` WHERE `_email` = '".$email."' ";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $error = 1;
      }
    }

    if ($error == 1) echo "error";
    else echo "success";

  }

  // ---------------------------------------------------------------------------
  // Fonction: finishedWorkingOnCopies
  // Utilité:  Appelée par le server de traitement des copies (ip-ged)
  //           quand le traîtement est terminé
  // Notes:    Placé ici car pas de contrôle de session
  // ---------------------------------------------------------------------------
  if ($action == "finishedWorkingOnCopies")
  {
    setParam('isCurrentlyWorking', 2);
  }

?>
