<?php
  session_start();

  require_once "../../../config.php";
  require_once "../../../include/sqltools.php";
  require_once "../../../php/functions.php";
  include ('../protection_input.php');
  // require_once "../../session.php";

  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  require("../parametre.php");
  require("../relation.php");
  require("../auth_tools.php");

  if (isUserConnected())
  {

    if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
    else $action = "";

    if ($action == "sendRequest")
    {
      $query = "SELECT `_ID`, `_email` FROM `user_id` WHERE `_IDgrp` >= '2' ";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $userID = $row[0];
        $userEmail = $row[1];


        // Pour chaques professeurs,
        $compteurOfEventNumber = 0;
        $query2 = "SELECT `_IDx` FROM `edt_data` WHERE `_ID` = '".$userID."' AND `_visible` = 'O' AND `_etat` = '3' ";
        $result2 = mysqli_query($mysql_link, $query2);
      	while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
          // On compte le nombre d'évènements qu'ils on en attente
          $compteurOfEventNumber++;


          $sql  = "UPDATE `edt_data` SET `_etat` = '4' ";
    			$sql .= "WHERE `_IDx` = ".$row2[0]." ";
          if(mysqli_query($mysql_link, $sql)==false)
    			{
    				echo "error";
    			}
    			else
    			{
    				echo "success";
    			}



        }




        // S'il on au moins un évènement en attente
        if ($compteurOfEventNumber != 0)
        {
          // Alors on leurs envoie un mail

          $subject = "Cours en attente";

          // Headers
          $headers = "From: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
          $headers .= "Reply-To: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=UTF-8\r\n";



          // Message du mail
          $texte = "Bonjour, vous avez ".$compteurOfEventNumber." cours en attente de validation. Merci de <a href=\"http://".$_SESSION["CfgWeb"]."/index.php?item=29&action=confirmEvents\">cliquer ici</a> (depuis une ordinateur, pas un téléphone) pour confirmer vos disponibilités.";

          mail($userEmail, $subject, $texte, $headers);

          // echo $userEmail."-".$subject."-".$texte."-".$headers;

        }

      }
    }
  }
?>
