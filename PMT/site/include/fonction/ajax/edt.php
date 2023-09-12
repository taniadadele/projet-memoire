<?php
session_start();

require_once "../../../config.php";
require_once "../../../include/sqltools.php";
require_once "../../../php/functions.php";
// require_once "../relation.php";
include ('../protection_input.php');
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
require("../parametre.php");
require("../relation.php");

require("../pagination.php");

require("../edt.php");
require("../auth_tools.php");
require("../../urlencode.php");

if (isUserConnected())
{
  // --------------------------------------------------
  // On récupère les éléments en POST - GET
  // --------------------------------------------------
  $elementsToGetInPost = array('IDpromotion', 'IDmatiere', 'IDprof', 'IDpole', 'date_1', 'date_2', 'time_1', 'time_2', 'lessonStatus', 'status', 'nbElemPerPage', 'remove', 'id_event', 'currentPage', 'type_UV');
  foreach ($elementsToGetInPost as $value)
    if (isset($_POST[$value])) $$value = addslashes(stripslashes($_POST[$value])); else $$value = '';

  $elementsToGetInGet = array('action');
  foreach ($elementsToGetInGet as $value)
    if (isset($_GET[$value])) $$value = addslashes(stripslashes($_GET[$value])); else $$value = '';

  $elementsToGetInPostOrGet = array('IDx', 'modif');
  foreach ($elementsToGetInPostOrGet as $value) {
    if (isset($_POST[$value])) $$value = addslashes(stripslashes($_POST[$value]));
    elseif (isset($_GET[$value])) $$value = addslashes(stripslashes($_GET[$value]));
    else $$value = '';
  }

  if (isset($_POST['sortBy'])) $sortBy_request = addslashes(stripslashes($_POST['sortBy']));
  elseif ($_SESSION['sortBy_liste_edt'] != "") $sortBy_request = $_SESSION['sortBy_liste_edt'];
  else $sortBy_request = '';

  if (isset($_POST['sortOrder'])) $sortOrder_request = addslashes(stripslashes($_POST['sortOrder']));
  elseif ($_SESSION['sortOrder_liste_edt'] != "") $sortOrder_request = $_SESSION['sortOrder_liste_edt'];
  else $sortOrder_request = '';


  //---------------------------------------------------------------------------
  function get_lundi_dimanche_from_week($week, $year)
  {
  	if(strftime("%W",mktime(0,0,0,01,01,$year))==1)
  	  $mon_mktime = mktime(0,0,0,01,(01+(($week-1)*7)), $year);
  	else
  	  $mon_mktime = mktime(0,0,0,01,(01+(($week)*7)), $year);

  	if(date("w",$mon_mktime)>1)
  	  $decalage = ((date("w", $mon_mktime)-1)*60*60*24);

  	$lundi = $mon_mktime - $decalage;
		$dimanche = $lundi + (6*60*60*24);

		return array(date("Y-m-d", $lundi), date("Y-m-d", $dimanche));
  }
  //---------------------------------------------------------------------------

  // Requêtes ajax pour l'edt
  if ($action == "getEdtList")
  {
    $query = "SELECT * FROM `edt_data` WHERE `_visible` = 'O' ";

    if ($IDpromotion != "0") $query .= "AND `_IDclass` LIKE '%;".$IDpromotion.";%' ";
    if ($IDmatiere != "0") $query .= "AND `_IDmat` = '".$IDmatiere."' ";
    if ($IDprof != "0" && $_SESSION['CnxAdm'] == 255) $query .= "AND (`_ID` = '".$IDprof."' OR `_IDrmpl` = '".$IDprof."') ";

    if ($IDpole != "0")
    {
      $query2 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_pole` = '".$IDpole."' ";
      $result2 = mysqli_query($mysql_link, $query2);
      $compteur = 0;
      while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
        if ($compteur == 0) $query .= "AND (";
        // La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
  		  // "))"

        $query3 = "SELECT `_ID_exam` FROM `campus_examens` WHERE `_ID_pma` = '".$row2[0]."' ";
        $result3 = mysqli_query($mysql_link, $query3);
        $compteur2 = 0;
        while ($row3 = mysqli_fetch_array($result3, MYSQLI_NUM)) {
          if (substr($query, -3) != "OR " and substr($query, -5) != "AND (") $query .= "OR ";
          // La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
          // "))"
          $query .= "`_ID_examen` = '".$row3[0]."' ";
          $compteur2++;
        }
        if (substr($query, -3) != "OR " and substr($query, -5) != "AND (") $query .= "OR ";
        // La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
  		  // "))"
        $query .= "`_ID_pma` = '".$row2[0]."' ";
        $compteur++;
      }
      $query .= ")";
    }

    // La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
    // "))"

    if ($lessonStatus == "") {
      // Création de la partie de la date de la requête
      $query .= "AND ((`_debut` >= '".$time_1.":00' AND `_debut` <= '".$time_2.":00') ";
      $query .= "AND (`_fin` >= '".$time_1.":00' AND `_fin` <= '".$time_2.":00') ";

      // La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
      // ")"


      $startx = strtotime(changeDateTypeFromFRToEN($date_1." ".$time_1));
      $endx = strtotime(changeDateTypeFromFRToEN($date_2." ".$time_2));

      $startDay = date('N', $startx) - 1;
      $endDay = date('N', $endx) - 1;

      if (date('m', $startx) == 1 && date('W', $startx) > 5) {
        $noSemaineStart = $startDay = 1;
      }
      else $noSemaineStart = date('W', $startx);

      if (date('m', $endx) == 1 && date('W', $endx) > 5) {
        $noSemaineEnd = $endDay = 1;
      }
      else $noSemaineEnd = date('W', $endx);



    	if(date("Y", $startx) == date("Y", $endx) && $noSemaineStart <= $noSemaineEnd) { // Cas normal
    		$query .= "AND ((`_annee` = '".date("Y", $startx)."' AND `_nosemaine` = '".$noSemaineStart."' AND `_jour` >= '".$startDay."') OR (`_annee` = '".date("Y", $startx)."' AND `_nosemaine` > '".$noSemaineStart."') OR (`_annee` > '".date("Y", $startx)."')) ";
    		$query .= "AND ((`_annee` = '".date("Y", $endx)."' AND `_nosemaine` = '".$noSemaineEnd."' AND `_jour` <= '".$endDay."') OR (`_annee` = '".date("Y", $endx)."' AND `_nosemaine` < '".$noSemaineEnd."') OR (`_annee` < '".date("Y", $endx)."') ) ";
    	} else if(date("Y", $startx) == date("Y", $endx) && date("W", $startx) > $noSemaineEnd) { // Cas date en fin d'année mais semaine 01 avec année de début
    		$query .= "AND ((`_annee` = '".date("Y", $startx)."' AND `_nosemaine` = '".$noSemaineStart."' AND `_jour` >= '".$startDay."') OR (`_annee` = '".date("Y", $startx)."' AND `_nosemaine` > '".$noSemaineStart."')  OR (`_annee` > '".date("Y", $startx)."')) ";
    		$query .= "AND ((`_annee` = '".date("Y", $endx)."' AND `_nosemaine` = '52' AND `_jour` <= '".$endDay."') OR (`_annee` = '".date("Y", $endx)."' AND `_nosemaine` < '52') OR (`_annee` < '".date("Y", $endx)."') ) ";
    	} else if(date("Y", $startx) != date("Y", $endx)) { // Cas date chevauchant 2 années
    		$query .= "AND ((`_annee` = '".date("Y", $startx)."' AND `_nosemaine` = '".$noSemaineStart."' AND `_jour` >= '".$startDay."') OR (`_annee` = '".date("Y", $startx)."' AND `_nosemaine` > '".$noSemaineStart."')  OR (`_annee` > '".date("Y", $startx)."')) ";
    		$query .= "AND ((`_annee` = '".date("Y", $endx)."' AND `_nosemaine` = '".$noSemaineEnd."' AND `_jour` <= '".$endDay."') OR (`_annee` = '".date("Y", $endx)."' AND `_nosemaine` < '".$noSemaineEnd."') OR (`_annee` < '".date("Y", $endx)."') ) ";
    	} else { // Cas autre
    		$query .= "AND ((`_annee` = '".date("Y", $startx)."' AND `_nosemaine` = '".$noSemaineStart."' AND `_jour` >= '".$startDay."') OR (`_annee` = '".date("Y", $startx)."' AND `_nosemaine` > '".$noSemaineStart."')  OR (`_annee` > '".date("Y", $startx)."')) ";
    		$query .= "AND ((`_annee` = '".date("Y", $endx)."' AND `_nosemaine` = '".$noSemaineEnd."' AND `_jour` <= '".$endDay."') OR (`_annee` = '".date("Y", $endx)."' AND `_nosemaine` < '".$noSemaineEnd."') OR (`_annee` < '".date("Y", $endx)."') ) ";
    	}
    }
    $connexionID = $_SESSION["CnxID"];

    // La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
    // "

    $_SESSION['sortBy_liste_edt'] = $sortBy_request;
    $_SESSION['sortOrder_liste_edt'] = $sortOrder_request;

    if ($sortOrder_request == "asc") $sortOrder = "asc";
    else $sortOrder = "desc";


    if ($sortBy_request == "pole") $sortBy = "_ID_pma";
    elseif ($sortBy_request == "mat") $sortBy = "_IDmat";
    elseif ($sortBy_request == "classe") $sortBy = "_IDclass";
    elseif ($sortBy_request == "prof") $sortBy = "_ID";
    elseif ($sortBy_request == "room") $sortBy = "_IDitem";
    elseif ($sortBy_request == "time") $sortBy = "_debut";
    // elseif ($sortBy_request == "date") $sortBy = "_jour ".$sortOrder.", _nosemaine ".$sortOrder.", _annee ";
    elseif ($sortBy_request == "date") $sortBy = "_annee ".$sortOrder.", _nosemaine ".$sortOrder.", _jour ";
    elseif ($sortBy_request == "state")
    {
      // Lorsque l'on tri sur l'état, on doit faire un tri très spécifique pour avoir dans la liste des résultats d'abord les 1 et 5 puis les 4
      $sortBy = "
        (
          CASE _etat

            WHEN '1'
            THEN 1

            WHEN '5'
            THEN 2

            WHEN '4'
            THEN 3

            WHEN '3'
            THEN 3
          END
        )
      ";
    }

    // Exclusion des matières de types indisponible
    $query .= "AND `_IDmat` in (SELECT `_IDmat` FROM `campus_data` WHERE `_type` != '2') ";
    if ($lessonStatus == "") $query .= ") ";

    // Si on est professeur
    if ($_SESSION["CnxAdm"] != 255 && $_SESSION['CnxGrp'] == 2) $query .= "AND (`_ID` = '".$connexionID."' OR `_IDrmpl` = '".$connexionID."') ";
    // Si on est étudiant
    if ($_SESSION['CnxGrp'] == 1 && !getParam('edt_visible_par_tous')) $query .= "AND `_IDclass` LIKE '%;".$_SESSION['CnxClass'].";%' ";


    if ($lessonStatus == "")
    {
      // Si on veut voir les cours "Programmés ou en attente"
      if ($status == 0)
      {
        if ($_SESSION['CnxAdm'] == 255) $query .= "AND (`_etat` = '1' OR `_etat` = '3' OR `_etat` = '4' OR `_etat` = '5') ";
        else $query .= "AND (`_etat` = '1' OR `_etat` = '4' OR `_etat` = '5') ";
      }

      // Si on veut voir les cours "Programmés"
      elseif ($status == 1) $query .= "AND (`_etat` = '1' OR `_etat` = '5') ";

      // Si on veut voir les cours "En attente"
      elseif ($status == 2)
      {
        if ($_SESSION['CnxAdm'] == 255) $query .= "AND (`_etat` = '4' OR `_etat` = '3') ";
        else $query .= "AND `_etat` = '4' ";
      }

      // Si on veut voir les cours "Refusés" (il faut être admin)
      elseif ($status == 3 && $_SESSION['CnxAdm'] == 255) $query .= "AND `_etat` = '6' ";



      // // Si on es admin et que l'on veut voir les cours en attente alors on montre aussi les cours acceptés par l'intervenant
      // elseif ($status == 2 and $_SESSION['CnxAdm'] == 255) $query .= "AND `_etat` != '1' AND `_etat` != '2' AND `_etat` != '3'  ";
      // // Sinon on es un intervenant et on montre les cours en attente excepté les cours acceptés
      // elseif ($status == 2) $query .= "AND `_etat` != '1' AND `_etat` != '2' AND `_etat` != '3' AND `_etat` != '5' ";
    }
    else
    {
      switch ($lessonStatus) {
        case 'accepted':
          $status = 5;
          echo '<div class="alert alert-success">Cours validés:</div>';
          break;
        case 'refused':
          $status = 6;
          echo '<div class="alert alert-danger">Cours refusés:</div>';
          break;
        case 'waiting':
          $status = 4;
          echo '<div class="alert">Cours en attente de validation:</div>';
          break;
      }
      $query .= "AND `_etat` = '".$status."' ";
    }

    // Tri sur le type:
    if ($type_UV != 0 && $type_UV != "")
    {
      switch ($type_UV) {
        case '1': $query .= "AND `_ID_examen` = 0 AND `_ID_pma` != 0 AND _IDmat != 123 AND _IDmat != 122 "; break;
        case '2': $query .= "AND `_ID_examen` != 0 "; break;
        case '3': $query .= "AND _IDmat = 122 AND _ID_pma =  0 "; break;
      }
    }



    if ($sortOrder_request != "") $query .= "ORDER BY ".$sortBy." ".$sortOrder." ";
    else $query .= "ORDER BY `_annee`, `_nosemaine`, `_jour`, `_debut` ASC ";

    if ($nbElemPerPage == "all") $currentPage = "";

    if ($nbElemPerPage != "all") $nbElemPerPage = getParam("nbElemPerPage");

    // Utilisé pour la pagination et le nombre d'heures
    $oldQuery = $query;

    if ($nbElemPerPage != "all") $query .= "LIMIT ".(($currentPage - 1) * $nbElemPerPage).", ".$nbElemPerPage." ";
// echo $query;
    $result = mysqli_query($mysql_link, $query);
    echo '<table class="table table-bordered table-striped">';
      echo '<tr>';
        if ($status == 0 or $status == 2 or $status == 3 || $status == 4 || $status == 6) echo '<th>Etat<a id="asc_state" class="orderBtn">▲</a><a id="des_state" class="orderBtn">▼</a></th>';
        echo '<th>Pôle <a id="asc_pole" class="orderBtn">▲</a><a id="des_pole" class="orderBtn">▼</a></th>';
        echo '<th>Matière <a id="asc_mat" class="orderBtn">▲</a><a id="des_mat" class="orderBtn">▼</a></th>';
        echo '<th>Classe <a id="asc_classe" class="orderBtn">▲</a><a id="des_classe" class="orderBtn">▼</a></th>';
        echo '<th>Intervenant <a id="asc_prof" class="orderBtn">▲</a><a id="des_prof" class="orderBtn">▼</a></th>';
        echo '<th>Salle <a id="asc_room" class="orderBtn">▲</a><a id="des_room" class="orderBtn">▼</a></th>';
        echo '<th>Date <a id="asc_date" class="orderBtn">▲</a><a id="des_date" class="orderBtn">▼</a></th>';
        echo '<th>Horaire <a id="asc_time" class="orderBtn">▲</a><a id="des_time" class="orderBtn">▼</a></th>';
        if ($status == 0 or $status == 2 or $status == 3 || $status == 4 || $status == 6) echo '<th></th>';
     echo '</tr>';

     // Section pour le calcul du nombre d'heure:
     $date_final_time = new DateTime('1970-01-01 00:00:00');

      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $listeClassesToShow = "";
        $listeClassesAlreadyShown = ";";
        $listeClasses = $row[4];
        $listeClasses = explode(";", $listeClasses);
        foreach ($listeClasses as $key => $value) {
          if ($value != 0 and strpos($listeClassesAlreadyShown, $value) === false)
          {
            $listeClassesAlreadyShown .= $value.";";
            $listeClassesToShow .= '<span class="badge">'.getClassNameByClassID($value).'</span> ';
          }
        }

        // // Création de la date
        $anneeDate = $row[16];
        $dateBase = $anneeDate."-01-01";
        $date = strtotime($dateBase);
        $daysToAdd = ($row[13] - 1) * 7;
        $date = strtotime("+".$daysToAdd." day", $date);
        $date = strtotime("+".$row[9]." day", $date);
        $date = strtotime("-1 day", $date);

        // Création de la durée
        $heure_debut = strtotime("10:00:00");
        $heure_fin = strtotime("12:30:00");
        $heure_debut = substr($row[10], 0, 2);
        $heure_fin = substr($row[11], 0, 2);
        $heure_duree = $heure_fin - $heure_debut;
        $minutes_debut = substr($row[10], 3, 2);
        $minutes_fin = substr($row[11], 3, 2);
        $minutes_duree = $minutes_fin - $minutes_debut;

        if ($minutes_duree < 0)
        {
          $heure_duree = $heure_duree - 1;
          $minutes_duree = 0 + (60 + $minutes_duree);
        }
        if ($heure_duree != 0) $duree = $heure_duree;

        if ($minutes_duree != 0 and $heure_duree != 0) $duree .= 'h'.$minutes_duree;
        elseif ($minutes_duree != 0) $duree = $minutes_duree.'min';
        elseif($minutes_duree == 0 and $heure_duree != 0) $duree .= 'h';

        // // Le calcul total de la durée se fait plus bas pour ne pas prendre en compte la pagination
        // $date_final_time->add(new DateInterval('PT'.$heure_duree.'H'));
        // $date_final_time->modify('+'.$minutes_duree.' minutes');

        $ID_examen = $row[23];
        $ID_PMA = $row[24];

          $matiereName = stripslashes(getMatNameByIdMat($row[3]));

          if ($ID_examen != 0) $matiereToShow = "<b>UV</b> - ".getUVNameByID($row[23]).' - '.$matiereName;
          elseif (getMatTypeByMatID($row[3]) == 3)
          {
            if ($row[22] != "") $matiereToShow = "<b>Agenda</b> - ".$row[22];
            else $matiereToShow = "<b>Agenda</b>";
          }
          else $matiereToShow = $matiereName;

        if ($ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
        else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));

        if ($row[14] == 6) $alert_background_color = 'class="table-danger row_status"';
        elseif ($row[14] == 5 or $row[14] == 1) $alert_background_color = 'class="table-success row_status"';
        elseif ($row[14] == 4 || $row[14] == 3) $alert_background_color = 'class="table-warning row_status"';
        else $alert_background_color = "";

        if ($row[14] == 5 or $row[14] == 1) $etat_symbol = '<i class="fa fa-check"></i>';
        if ($row[14] == 6) $etat_symbol = '<i class="fa fa-times"></i>';
        if ($row[14] == 4 || $row[14] == 3) $etat_symbol = '<i class="fa fa-question"></i>';

        echo '<tr id="row_'.$row[15].'" '.$alert.'>';
          // if ($status == 0 || $status == 4) echo "<td id=\"infoRow_".$row[15]."\" ".$alert_background_color.">".$etat_symbol."</td>";
          if ($status == 0 or $status == 2 or $status == 3 || $status == 4 || $status == 6) echo "<td id=\"infoRow_".$row[15]."\" ".$alert_background_color.">".$etat_symbol."</td>";

          // Colonne Pôle
          echo "<td>".$poleName."</td>";
          // Colonne matière
          echo "<td>".$matiereToShow."</td>";
          // Colonne classe
          echo "<td>".$listeClassesToShow."</td>";
          // Colonne intervenant
          echo "<td>".getUserNameByID($row[6]).getSecondTeacherNameByEventId($row[15])."</td>";
          // Colonne salle
          echo "<td>".getRoomNameByID($row[5])."</td>";

          //  Colonne date
          $querygrp  = "SELECT _IDgrp FROM user_id WHERE _ID = $row[6] ";
          $returngrp  = mysqli_query($mysql_link, $querygrp);
          $rowgrp   = ( $returngrp ) ? mysqli_fetch_row($returngrp) : 0 ;

          // // Correction de la date quand passage à l'année suivante
          // // Si les jours ne correspondent pas, alors on corrige:
          // if (($row[9] + 1) != date("N", $date)) $dayNumber = date('d', $date) - 1;
          // else $dayNumber = date('d', $date);


          // Traitements convertion date time
          if ($row[13] < 10) $week_temp = "0".$row[13];
          else $week_temp = $row[13];
          $date_debut = php2MySqlTime(strtotime($row[16].'W'.$week_temp));


          $date_event = date($date_debut); // objet date
          $date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row[9]." day"); // ajout du nb de jours
          $date_event = date("d/m/Y", $date_event); // timesptamp en string

          $dayNumber = date('d', $date);

          $value  = ($row[6] * 100) + $rowgrp[0];
          $lienVersEDT = "index.php?item=64&IDitem=0&cmde=&IDedt=2&IDitem=-$value&generique=off&setdate=$date";
          echo '<td><a href="'.$lienVersEDT.'" target="_blank">'.$date_event.'</a></td>';

          // Colonne Horaire
          echo "<td>De: ".$heure_debut."h".$minutes_debut." À: ".$heure_fin."h".$minutes_fin." (".$duree.")</td>";

          // Colonne d'état (si on est en mode de vérification)
          if ($status == 0 or $status == 2 or $status == 3 || $status == 4 || $status == 6)
          {
            if ($row[14] != 5 and $row[14] != 1)
            {
              if ($_SESSION['CnxAdm'] == 255 and $_SESSION['CnxID'] != $row[6]) echo '<td style="text-align: left !important;"><div class="btn-group" role="group" aria-label="Basic example">';
              else echo '<td>';
              if ($_SESSION["CnxID"] == $row[6] or $_SESSION["CnxAdm"] == 255)
              {
                echo "<button class=\"btn btn-success\" title=\"Accepter le cours\" id=\"accept_".$row[15]."\" onclick=\"acceptCours(".$row[15].");\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></button>";
                if ($row[14] != 6) echo "<button class=\"btn btn-warning\" title=\"Refuser le cours\" id=\"refuse_".$row[15]."\" onclick=\"refuseCours(".$row[15].");\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></button>";
              }
              if ($_SESSION["CnxAdm"] == 255) echo "<button class=\"btn btn-danger\" title=\"Supprimer le cours\" onclick=\"removeCours(".$row[15].");\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></button>";
              echo "</div></td>";
            }
            else echo "<td></td>";
          }
        echo "</tr>";
        $i++;
      }

      echo "</table>";

      // Result2 ne sert qu'à compter le nombre d'éléments renvoyés par la requête mais sans le limit
      $result2 = mysqli_query($mysql_link, $oldQuery);
      $numberOfResults = mysqli_num_rows($result2);

      // On calcule la durée sans prendre en compte la pagination
      while ($row = mysqli_fetch_array($result2)) {
        // Création de la durée
        $heure_debut = substr($row[10], 0, 2);
        $heure_fin = substr($row[11], 0, 2);
        $heure_duree = $heure_fin - $heure_debut;
        $minutes_debut = substr($row[10], 3, 2);
        $minutes_fin = substr($row[11], 3, 2);
        $minutes_duree = $minutes_fin - $minutes_debut;

        if ($minutes_duree < 0) $heure_duree = $heure_duree - 1;
        if ($minutes_duree < 0) $minutes_duree = 0 + (60 + $minutes_duree);
        if ($heure_duree != 0) $duree = $heure_duree;

        if ($minutes_duree != 0 and $heure_duree != 0) $duree .= 'h'.$minutes_duree;
        elseif ($minutes_duree != 0) $duree = $minutes_duree.'min';
        elseif($minutes_duree == 0 and $heure_duree != 0) $duree .= 'h';

        $date_final_time->add(new DateInterval('PT'.$heure_duree.'H'));
        $date_final_time->modify('+'.$minutes_duree.' minutes');
      }

      // Pagination
      echo getPagination($currentPage, $numberOfResults, '', true, getParam("nbElemPerPage"));

      echo '
      <script>
        $(document).ready(function(){
          $(".page-link").click(function(e){
            e.preventDefault();
            var pageClicked = $(this).attr("page");
            if (pageClicked == "all") $("#nbElemPerPage").val(pageClicked);
            else
            {
              $("#currentPage").val(pageClicked);
              $("#nbElemPerPage").val("");
            }
            toDoWhenPaginationChange();
          });
        })
      </script>
      ';

      $diff = date_diff($date_final_time, date_create('1970-01-01 00:00:00'));
      $total_minutes = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
      $total_hours = floor($total_minutes/60);
      $total_minutes = $total_minutes%60;

      echo '<div id="numberHours" style="display: none;">'.$total_hours.':'.$total_minutes.'</div>';
      echo '<div id="numberRows" style="display: none;">'.$numberOfResults.'</div>';
      if ($remove == "accepted")
      {
        $sql  = "UPDATE `edt_data` SET `_etat` = '1' WHERE `_etat` = '5' ";
        if(mysqli_query($mysql_link, $sql)==false)
        {
          // Do nothing
        }
        else echo '<script>console.log("Cours acceptés");</script>';
      }
  }



  if ($action == 'acceptCours' && ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] > 1))
  {
    $sql  = "UPDATE `edt_data` SET `_etat` = '5' ";
    $sql .= "WHERE `_IDx` = ".$id_event." ";
    if(mysqli_query($mysql_link, $sql)==false) echo "error";
    else echo "success";
  }

  if ($action == 'refuseCours' && ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] > 1))
  {
    $sql  = "UPDATE `edt_data` SET `_etat` = '6' ";
    $sql .= "WHERE `_IDx` = ".$id_event." ";
    if(mysqli_query($mysql_link, $sql)==false) echo "error";
    else echo "success";
  }


  if ($action == 'removeCours' && $_SESSION['CnxAdm'] == 255)
  {
    $sql  = "DELETE FROM `edt_data` ";
    $sql .= "WHERE `_IDx` = ".$id_event." ";
    if(mysqli_query($mysql_link, $sql)==false) echo "error";
    else echo "success";
  }


  if ($action == 'sendTeacherAndStudentMailRemove')
  {
    $query = "SELECT `_jour`, `_nosemaine`, `_annee`, `_debut`, `_fin` FROM `edt_data` WHERE _IDx = ".$IDx." ";
    // Création de la date
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $date = new DateTime();
      $date->setISODate($row[2],$row[1],($row[0] + 1)); //year , week num , day
      $end_date = $date->format('d-m-Y');
      $debut = $row[3];
      $fin = $row[4];
    }

    $elements = array(
      'modif' => $modif,
      'old_date' => $end_date.' de '.substr($debut, 0, 5).' à '.substr($fin, 0, 5),
      'to' => 'both'
    );
    $json = json_encode($elements);
    $_SESSION['temp_mail_edt'] = $json;

    if ($modif == 'delete') sendCheckMailToTeacher($IDx, $modif, 'function');
    $_SESSION['temp_mail_edt'] = $json;
    if ($modif == 'delete') sendCheckMailToStudents($IDx, $modif, 'function');
  }

  if ($action == 'sendTeacherMail')
  {
    $query = "SELECT `_jour`, `_nosemaine`, `_annee`, `_debut`, `_fin` FROM `edt_data` WHERE `_IDx` = ".$IDx." ";
    // Création de la date
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $date = new DateTime();
      $date->setISODate($row[2],$row[1],($row[0] + 1)); //year , week num , day
      $end_date = $date->format('d-m-Y');
      $debut = $row[3];
      $fin = $row[4];
    }


    $elements = array(
      'modif' => $modif,
      'old_date' => $end_date.' de '.substr($debut, 0, 5).' à '.substr($fin, 0, 5),
      'to' => 'teacher'
    );
    $json = json_encode($elements);
    $_SESSION['temp_mail_edt'] = $json;

    if ($modif == 'delete') sendCheckMailToTeacher($IDx, $modif, 'function');
  }

  if ($action == 'sendStudentMail')
  {

    $query = "SELECT `_jour`, `_nosemaine`, `_annee`, `_debut`, `_fin` FROM `edt_data` WHERE _IDx = ".$IDx." ";
    // Création de la date
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $date = new DateTime();
      $date->setISODate($row[2],$row[1],($row[0] + 1)); //year , week num , day
      $end_date = $date->format('d-m-Y');
      $debut = $row[3];
      $fin = $row[4];
    }

    $elements = array(
      'modif' => $modif,
      'old_date' => $end_date.' de '.substr($debut, 0, 5).' à '.substr($fin, 0, 5),
      'to' => 'students'
    );
    $json = json_encode($elements);
    $_SESSION['temp_mail_edt'] = $json;

    if ($modif == 'delete') sendCheckMailToStudents($IDx, $modif, 'function');
  }



  // Fonction qui est appelée par l'EDT pour chaque event pour vérifier si on est en tant que prof si celui-ci à le droit de modifier l'event (affichage du bouton 'editer les détails')
  if ($action == 'checkIfUserCanAccessModificationOfEvent')
  {
    if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) echo 'showButton';
    else
    {
      if ($_SESSION['CnxGrp'] > 1)
      {
        $query = "SELECT edt.`_ID`, edt.`_IDrmpl`, edt.`_IDmat`, campus.`_type` FROM `edt_data` edt JOIN `campus_data` campus ON edt.`_IDmat` = campus.`_IDmat` AND edt.`_IDx` = '".$IDx."' LIMIT 1 ";
        $result = mysqli_query($mysql_link, $query);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
          if ($row[3] == 2 && ($row[0] == $_SESSION['CnxID'] || $row[1] == $_SESSION['CnxID'])) echo "showButton";
          else echo 'hideButton';
        }
      }
      else echo 'hideButton';
    }
  }



  // Permet de récupérer l'ID d'une promo en fonction du PMA
  if ($action == 'getClassIDByYearAndPMA') {
    $year = $_POST['year'];
    $pma = $_POST['pma'];
    $IDx = $_POST['IDx'];

    $temp = explode('/', $_POST['date']);
    $date = $temp[2].'-'.$temp[1].'-'.$temp[0];

    $temp = explode('/', getParam('dateBasculeAnnee'));
    $date_bascule = $temp[2].'-'.$temp[1].'-'.$temp[0];

    // Si l'évènement existe déjà alors on récupère la classe de celui-ci
    if (isset($IDx) && $IDx != '' && $IDx != 0) {
      $query = "SELECT _IDclass FROM edt_data WHERE `_IDx` = '".$IDx."' LIMIT 1 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        if (substr_count($row[0], ';') == 2) echo str_replace(';', '', $row[0]);
      }
    }
    else {
      if (strtotime($date_bascule) > time() && strtotime($date) > strtotime($date_bascule)) {
        $query = "SELECT `_ID_year` FROM `pole_mat_annee` WHERE _ID_pma = '".$pma."' ";
        $result = mysqli_query($mysql_link, $query);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
          $query_class = "SELECT `_IDclass` FROM `campus_classe` WHERE `_code` = '".($row[0] - 1)."' ";
          $result_class = mysqli_query($mysql_link, $query_class);
          while ($row_class = mysqli_fetch_array($result_class, MYSQLI_NUM)) {
            echo $row_class[0];
          }
        }

      }
      else echo getClassIDByPMAID($pma);
    }
  }

  // Permet de récupérer l'ID d'une promo en fonction de l'ID de l'UV
  if ($action == 'getClassIDByYearAndUV') {
    $year = $_POST['year'];
    $UVid = $_POST['UVid'];
    $IDx = $_POST['IDx'];
    $pma = getPMAIDByUVID($UVid);


    $temp = explode('/', $_POST['date']);
    $date = $temp[2].'-'.$temp[1].'-'.$temp[0];

    $temp = explode('/', getParam('dateBasculeAnnee'));
    $date_bascule = $temp[2].'-'.$temp[1].'-'.$temp[0];

    // Si l'évènement existe déjà alors on récupère la classe de celui-ci
    if (isset($IDx) && $IDx != '' && $IDx != 0) {
      $query = "SELECT _IDclass FROM edt_data WHERE `_IDx` = '".$IDx."' LIMIT 1 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        if (substr_count($row[0], ';') == 2) echo str_replace(';', '', $row[0]);
      }
    }
    else {
      if (strtotime($date_bascule) > time() && strtotime($date) > strtotime($date_bascule)) {
        $query = "SELECT `_ID_year` FROM `pole_mat_annee` WHERE _ID_pma = '".$pma."' ";
        $result = mysqli_query($mysql_link, $query);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
          $query_class = "SELECT `_IDclass` FROM `campus_classe` WHERE `_code` = '".($row[0] - 1)."' ";
          $result_class = mysqli_query($mysql_link, $query_class);
          while ($row_class = mysqli_fetch_array($result_class, MYSQLI_NUM)) {
            echo $row_class[0];
          }
        }

      }
      else echo getClassIDByPMAID($pma);
    }
  }

}
// NE RIEN METTRE APRÈS CETTE FERMETURE
?>
