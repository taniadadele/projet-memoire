<?php
  session_start();
  require_once "../../../config.php";
  require_once "../../../include/sqltools.php";
  require_once "../../../php/functions.php";
  include ('../protection_input.php');

  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  require("../parametre.php");
  require("../relation.php");
  require("../auth_tools.php");

  if (isUserConnected())
  {

    if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
    else $action = "";

    $post = array('currentpole', 'currentyear', 'tagUI', 'IDPMA', 'IDprof', 'numOfSelect', 'ID_event', 'IDUV', 'date', 'heure_debut', 'heure_fin', 'ID_prof', 'matID', 'ID_room', 'ID_promo');
    foreach ($post as $value) {
      if (isset($_POST[$value])) $$value = $_POST[$value];
      else $$value = '';
    }



    if ($action == "checkForRemoveMatiere")
    {
      $currentMatiere = $tagUI;

      $error = 0;
      $query  = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_year` = '".$currentyear."' AND `_ID_pole` = '".$currentpole."' AND `_ID_matiere` = '".$currentMatiere."' ";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $IDPMA = $row[0];
      }

      $query  = "SELECT * FROM `campus_syllabus`WHERE `_IDPMA` = '".$IDPMA."' ";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "error";
        $error = 1;
      }
      if ($error == 0) echo "success";
    }

    if ($action == "checkForRemovePole")
    {
      $error = 0;
      $query  = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_pole` = '".$currentpole."' ";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $query1  = "SELECT * FROM `campus_syllabus`WHERE `_IDPMA` = '".$row[0]."' ";
        $result1 = mysqli_query($mysql_link, $query1);
      	while ($row1 = mysqli_fetch_array($result1, MYSQLI_NUM)) {
          $error = 1;
        }
      }
      if ($error == 0) echo "success";
      else echo "error";
    }

    if ($action == "getMatIDByPMAID")
    {
      $query  = "SELECT `_ID_matiere` FROM `pole_mat_annee` WHERE `_ID_pma` = '".$IDPMA."' ";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo $row[0];
      }
    }

    if ($action == "getClassIDByPMAID")
    {
      echo getClassIDByPMAID($IDPMA);
    }

    if ($action == "getProfSelectByPMAID")
    {
      echo "<select name=\"IDuser\" id=\"IDuser\" style=\"width: 100%;\" required>";
      if ($IDprof == "") $selected = "selected=\"selected\"";
      else $selected = "";
      echo "<option value=\"0\" $selected></option>";

      $query  = "SELECT `_idUser` FROM `campus_syllabus` WHERE `_IDPMA` = '".$IDPMA."' ";
      // $query .= "order by _name";
      $profs = "";
      $result = mysqli_query($mysql_link, $query);
    	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $profs .= $row[0];
      }

      echo "<optgroup label=\"Enseignants du syllabus\">";
        $profArray = explode(";", $profs);
        foreach ($profArray as $key => $idProf) {
          if ($idProf != "")
          {
            if ($IDprof == $idProf) $selected = "selected=\"selected\"";
            else $selected = "";

            echo "<option value=\"".$idProf."\" ".$selected.">".getUserNameByID($idProf)."</option>";
          }
        }
      echo "</optgroup>";

      echo "<optgroup label=\"Autres enseignants\">";
        $query = "SELECT `_ID` FROM `user_id` WHERE `_adm` >= 1 AND _IDgrp != 1 ";

        foreach ($profArray as $key => $idProf) {
          if ($idProf != "")
          {
            $query .= "AND `_ID` != '".$idProf."' ";
          }
        }
        $query .= "order by `_name`";
        $result = mysqli_query($mysql_link, $query);
      	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

          if ($IDprof == $row[0]) $selected = "selected=\"selected\"";
          else $selected = "";

          echo "<option value=\"".$row[0]."\" ".$selected.">".getUserNameByID($row[0])."</option>";
        }

      echo "</optgroup>";
      echo "</select>";
    }


    if ($action == "getProfSelectByPMAIDWithSelect")
    {
      if ($numOfSelect == 0) $query  = "SELECT `_ID` FROM `edt_data` WHERE `_IDx` = '".$ID_event."' ";
      else $query  = "SELECT `_IDrmpl` FROM `edt_data` WHERE `_IDx` = '".$ID_event."' ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $profSelected = $row[0];
      }
      echo "<select name=\"IDuser\" id=\"IDuser\" style=\"width: 100%;\" required>";
      if ($IDprof == "") $selected = "selected=\"selected\"";
      else $selected = "";
      echo "<option value=\"0\" $selected></option>";
      $query  = "SELECT `_idUser` FROM `campus_syllabus` WHERE `_IDPMA` = '".$IDPMA."' ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $profs = $row[0];
      }

      echo "<optgroup label=\"Enseignants du syllabus\">";
        $profArray = explode(";", $profs);
        foreach ($profArray as $key => $idProf) {
          if ($idProf != "")
          {
            if ($idProf == $profSelected) $selected = "selected";
            else $selected = "";
            echo "<option value=\"".$idProf."\" ".$selected.">".getUserNameByID($idProf)."</option>";
          }
        }
      echo "</optgroup>";

      echo "<optgroup label=\"Autres enseignants\">";
        $query = "SELECT `_ID` FROM `user_id` WHERE `_adm` >= 1 AND _IDgrp != 1 ";
        foreach ($profArray as $key => $idProf) {
          if ($idProf != "")
          {
            $query .= "AND `_ID` != '".$idProf."' ";
          }
        }
        $result = mysqli_query($mysql_link, $query);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
          if ($row[0] == $profSelected) $selected = "selected";
          else $selected = "";
          echo "<option value=\"".$row[0]."\" ".$selected.">".getUserNameByID($row[0])."</option>";
        }

      echo "</optgroup>";
      echo "</select>";
    }

    if ($action == "getProfSelect")
    {
      // if ($numOfSelect == 1) echo "<option value=\"0\"></option>";
      echo "<option value=\"0\" selected></option>";
      // echo "<select name=\"IDuser\" id=\"IDuser\" style=\"width: 100%;\" onchange=\"changeIntervenant();\" required>";
        $query = "SELECT `_ID` FROM `user_id` WHERE `_adm` >= 1 AND _IDgrp != 1 ";
        $query .= "order by _name";
        $result = mysqli_query($mysql_link, $query);
      	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
          echo "<option value=\"".$row[0]."\">".getUserNameByID($row[0])."</option>";
        }
      // echo "</select>";
    }

    if ($action == "getPMAIDByUVID")
    {
      $query = "SELECT `_ID_pma` FROM `campus_examens` WHERE `_ID_exam` = '".$IDUV."' ";
      $result = mysqli_query($mysql_link, $query);
      // echo $query;
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo $row[0];
      }
    }

    // ---------------------------------------------------------------------------
    // Fonction: Vérifie si le prof est occupé sur le créneau horraire
    // IN:		   L'ID du prof, le créneau horraire et la date (INT)
    // OUT: 		 Est-ce que le prof est occupé ? (TEXT)
    // ---------------------------------------------------------------------------
    if ($action == "checkIfProfBusy")
    {
      $IDprof = $ID_prof;
      $IDevent = $ID_event;

      $startx = js2PhpTime($date." ".$heure_debut);
      $endx = js2PhpTime($date." ".$heure_fin);

      $error = 0;

      $startDay = date("N", $startx) - 1;
      $endDay = date("N", $endx) - 1;

      $query  = "select * ";
      $query .= "from edt_data ";
      $query .= "WHERE ";
      $query .= "_ID = $IDprof ";
      $query .= "AND _IDx != '".$IDevent."' ";

      $query .= "AND (`_etat` = '1' OR `_etat` = '3' OR `_etat` = '4' OR `_etat` = '5') ";
      $query .= "AND _nosemaine = ".date("W", $startx)." AND _annee = ".date("Y", $startx)." AND _jour = '".$startDay."' ";
      $query .= "AND (((_debut >= '".date("H:i:s", $startx)."' AND _debut < '".date("H:i:s", $endx)."') OR (_fin > '".date("H:i:s", $startx)."' AND _fin <= '".date("H:i:s", $endx)."') ) ";

      $query .= "OR (_debut < '".date("H:i:s", $startx)."' AND _fin > '".date("H:i:s", $endx)."'))";

      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $error = 1;
      }
      if ($error == 1) echo "error";
      else echo "success";
    }


    // ---------------------------------------------------------------------------
    // Fonction: Vérifie si la classe est occupé sur le créneau horraire
    // IN:		   L'ID du pma, le créneau horraire et la date (INT)
    // OUT: 		 Est-ce que la classe est occupé ? (TEXT)
    // ---------------------------------------------------------------------------
    if ($action == "checkIfClassBusy")
    {
      $IDclass = getClassIDByPMAID($IDPMA);
      $IDevent = $ID_event;

      $startx = js2PhpTime($date." ".$heure_debut);
      $endx = js2PhpTime($date." ".$heure_fin);

      $error = 0;

      $startDay = date("N", $startx) - 1;
      $endDay = date("N", $endx) - 1;

      $query  = "select * ";
      $query .= "from edt_data ";
      $query .= "WHERE ";
      $query .= "_IDclass LIKE '%;".$IDclass.";%' ";
      $query .= "AND _IDx != '".$IDevent."' ";

      $query .= "AND (`_etat` = '1' OR `_etat` = '3' OR `_etat` = '4' OR `_etat` = '5') ";
      $query .= "AND _nosemaine = ".date("W", $startx)." AND _annee = ".date("Y", $startx)." AND _jour = '".$startDay."' ";
      $query .= "AND (((_debut >= '".date("H:i:s", $startx)."' AND _debut < '".date("H:i:s", $endx)."') OR (_fin > '".date("H:i:s", $startx)."' AND _fin <= '".date("H:i:s", $endx)."') ) ";

      $query .= "OR (_debut < '".date("H:i:s", $startx)."' AND _fin > '".date("H:i:s", $endx)."'))";

      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $error = 1;
      }
      if ($error == 1) echo "error";
      else echo "success";
    }



    // ---------------------------------------------------------------------------
    // Fonction: Vérifie si la promo est occupé sur le créneau horraire
    // IN:		   L'ID de la promo, le créneau horraire et la date (INT)
    // OUT: 		 Est-ce que la promo est occupé ? (TEXT)
    // ---------------------------------------------------------------------------
    if ($action == "checkIfPromoBusy")
    {
      $IDclass = $ID_promo;
      $IDevent = $ID_event;

      $startx = js2PhpTime($date." ".$heure_debut);
      $endx = js2PhpTime($date." ".$heure_fin);

      $error = 0;

      $startDay = date("N", $startx) - 1;
      $endDay = date("N", $endx) - 1;

      $query  = "select * ";
      $query .= "from edt_data ";
      $query .= "WHERE ";
      $query .= "_IDclass LIKE '%;".$IDclass.";%' ";
      $query .= "AND _IDx != '".$IDevent."' ";

      $query .= "AND (`_etat` = '1' OR `_etat` = '3' OR `_etat` = '4' OR `_etat` = '5') ";
      $query .= "AND _nosemaine = ".date("W", $startx)." AND _annee = ".date("Y", $startx)." AND _jour = '".$startDay."' ";
      $query .= "AND (((_debut >= '".date("H:i:s", $startx)."' AND _debut < '".date("H:i:s", $endx)."') OR (_fin > '".date("H:i:s", $startx)."' AND _fin <= '".date("H:i:s", $endx)."') ) ";

      $query .= "OR (_debut < '".date("H:i:s", $startx)."' AND _fin > '".date("H:i:s", $endx)."'))";

      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $error = 1;
      }
      if ($error == 1) echo "error";
      else echo "success";
    }



    if ($action == "checkIfRoomBusy")
    {
      $IDroom = $ID_room;
      $IDevent = $ID_event;

      $startx = js2PhpTime($date." ".$heure_debut);
      $endx = js2PhpTime($date." ".$heure_fin);

      $error = 0;

      $startDay = date("N", $startx) - 1;
      $endDay = date("N", $endx) - 1;

      $query  = "select * ";
      $query .= "from edt_data ";
      $query .= "WHERE ";
      $query .= "_IDitem = $IDroom ";
      $query .= "AND _IDx != '".$IDevent."' ";
      $query .= "AND _etat = 1 ";
      $query .= "AND _nosemaine = ".date("W", $startx)." AND _annee = ".date("Y", $startx)." AND _jour = '".$startDay."' ";
      $query .= "AND (((_debut >= '".date("H:i:s", $startx)."' AND _debut < '".date("H:i:s", $endx)."') OR (_fin > '".date("H:i:s", $startx)."' AND _fin <= '".date("H:i:s", $endx)."') ) ";
      $query .= "OR (_debut < '".date("H:i:s", $startx)."' AND _fin > '".date("H:i:s", $endx)."'))";
      $result = mysqli_query($mysql_link, $query);

      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $error = 1;
      }

      if ($error == 1) echo "error";
      else echo "success";

    }

    if ($action == "gedIfMatIsLinkToPole")
    {
      $toReturn = ";";
      $query = "SELECT pole_mat_annee.`_ID_pma`, pole_mat_annee.`_ID_matiere`, pole.`_name` FROM `pole_mat_annee` INNER JOIN `pole` ON pole_mat_annee.`_ID_pole` = pole.`_ID` ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $toReturn .= $row[1].";";

      }
      $query = "SELECT _IDmat FROM campus_data WHERE _type = 2 OR _type = 3 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $toReturn .= $row[0].";";
      }
      // echo $query."\n\r";
      echo $toReturn;
    }
  }

?>
