<?php
  session_start();
  require_once "../../../config.php";
  require_once "../../../include/sqltools.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  require("../parametre.php");
  require("../relation.php");
  include ('../protection_input.php');
  require("../auth_tools.php");

  if (isUserConnected())
  {

    if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
    else $action = "";
    if (isset($_POST['graduationYear'])) $graduationYear = addslashes(stripslashes($_POST['graduationYear']));
    else $graduationYear = "";

    // -------------------------------------------------------------------------
    // Fonction: Calcule la promotion (1ère année/2ème...) en fonction de
    //           l'année d'obtention du diplôme et du nombre d'années du cursus
    // Entrées : Année d'obtention du diplôme
    // Sorties : (text) Intitulé de l'année
    // -------------------------------------------------------------------------
    if ($action == "getPromotionByGraduationYear")
    {
      echo getPromotionByGraduationYear($graduationYear);
    }

    // -------------------------------------------------------------------------
    // Fonction: Re-Calcule les promotions (1ère année/2ème...) en fonction de
    //           l'année d'obtention du diplôme et du nombre d'années du cursus
    //           pour chaque promotion
    // Entrées : Aucunes
    // Sorties : Aucunes
    // -------------------------------------------------------------------------
    if ($action == "reassignPromotionByGraduationYear")
    {
      reAssignPromotionByGraduationYear();
    }


    // -------------------------------------------------------------------------
    // Fonction: Récupère le nombre de profs à qui on va envoyer
    //           un mail de confirmation de cours
    // Entrées : Aucunes
    // Sorties : (int) Nombre de personnes
    // -------------------------------------------------------------------------

    if ($action == "getNumberOfTeachersToSendMailTo")
    {
      $alreadyCounted =";";
  		$countOfPeoplesToSendMessageTo = 0;
  		$query = "SELECT `_ID`, `_IDrmpl` FROM `edt_data` WHERE `_etat` = 3 ";
  		$result = mysqli_query($mysql_link, $query);
  		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
  			if (strpos($alreadyCounted, ";".$row[0].";") === false && $row[0] != 0)
  			{
  				$alreadyCounted .= $row[0].";";
  				$countOfPeoplesToSendMessageTo++;
  			}
  			if (strpos($alreadyCounted, ";".$row[1].";") === false && $row[1] != 0)
  			{
  				$alreadyCounted .= $row[1].";";
  				$countOfPeoplesToSendMessageTo++;
  			}
  		}
      echo $countOfPeoplesToSendMessageTo;
    }



  }
?>
