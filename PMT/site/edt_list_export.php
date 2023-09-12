<?php
  session_start();
  require_once "config.php";
  require_once "include/sqltools.php";
  require_once "php/functions.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

  require("include/fonction/parametre.php");
  require("include/fonction/relation.php");

  require("include/fonction/pagination.php");

  require("include/fonction/edt.php");


  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="edt.ics"');
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');

  // Librarie iCal
  // require_once 'php/iCal/vendor/autoload.php';
  $currentDir = str_replace("/test", "", __DIR__);
  require_once $currentDir.'/php/iCal/vendor/autoload.php';

  // set default timezone (PHP 5.4)
  $tz  = 'Europe/Berlin';
  $dtz = new \DateTimeZone($tz);
  date_default_timezone_set($tz);


  $sortorder          = $_GET['sortOrder'];
  $sortby             = $_GET['sortBy'];
  $idpromotion        = $_GET['idpromotion'];
  $idmatiere          = $_GET['idmatiere'];
  $idpole             = $_GET['idpole'];
  $idprof             = $_GET['idprof'];
  $date_1             = str_replace(".", "/", $_GET['date_1']);
  $date_2             = str_replace(".", "/", $_GET['date_2']);
  $time_1             = $_GET['time_1'];
  $time_2             = $_GET['time_2'];
  $status             = $_GET['status'];
  $lessonstatus       = $_GET['lessonStatus'];

  $query = "SELECT * FROM `edt_data` WHERE `_visible` = 'O' ";

  if ($idpromotion != "0") $query .= "AND `_IDclass` LIKE '%;".$idpromotion.";%' ";
  if ($idmatiere != "0") $query .= "AND `_IDmat` = '".$idmatiere."' ";
  if ($idprof != "0" && $_SESSION['CnxAdm'] == 255) $query .= "AND `_ID` = '".$idprof."' ";

  if ($idpole != "0")
  {
    $query2 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_pole` = '".$idpole."' ";
    $result2 = mysqli_query($mysql_link, $query2);
    $compteur = 0;
    while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
      if ($compteur == 0) $query .= "AND (";

      $query3 = "SELECT `_ID_exam` FROM `campus_examens` WHERE `_ID_pma` = '".$row2[0]."' ";
      $result3 = mysqli_query($mysql_link, $query3);
      $compteur2 = 0;
      while ($row3 = mysqli_fetch_array($result3, MYSQLI_NUM)) {
        if (substr($query, -3) != "OR " and substr($query, -5) != "AND (") $query .= "OR ";
        $query .= "`_ID_examen` = '".$row3[0]."' ";
        $compteur2++;
      }
      if (substr($query, -3) != "OR " and substr($query, -5) != "AND (") $query .= "OR ";
      $query .= "`_ID_pma` = '".$row2[0]."' ";
      $compteur++;
    }
    $query .= ")";
  }

  // La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
  // "))"

  if ($lessonstatus == "") {
    // Création de la partie de la date de la requête
    $date_1 = $date_1;
    $date_2 = $date_2;
    $time_1 = $time_1;
    $time_2 = $time_2;

    $query .= "AND (`_debut` >= '".$time_1.":00' AND `_debut` <= '".$time_2.":00') ";


    $query .= "AND (`_fin` >= '".$time_1.":00' AND `_fin` <= '".$time_2.":00') ";


    $startx = js2PhpTime($date_1." ".$time_1);
    $endx = js2PhpTime($date_2." ".$time_2);

    $startDay = date("N", $startx) - 1;
    $endDay = date("N", $endx) - 1;

    $query .= "AND ((`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` = '".date("W", $startx)."' AND `_jour` >= '".$startDay."') OR (`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` > '".date("W", $startx)."')) ";
    $query .= "AND ((`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` = '".date("W", $endx)."' AND `_jour` <= '".$endDay."') OR (`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` < '".date("W", $endx)."')) ";
  }
  $connexionID = $_SESSION["CnxID"];

  if ($_SESSION["CnxAdm"] != 255) $query .= "AND `_ID` = '".$connexionID."' ";


  if ($lessonstatus == "" or $lessonstatus == "0")
  {
    // Si on veut voir les cours "Programmés ou en attente"
    if ($status == 0) $query .= "AND (`_etat` = '1' OR `_etat` = '4' OR `_etat` = '5') ";

    // Si on veut voir les cours "Programmés"
    elseif ($status == 1) $query .= "AND (`_etat` = '1' OR `_etat` = '5') ";
    // Si on veut voir les cours "En attente"
    elseif ($status == 2) $query .= "AND `_etat` = '4' ";
    // Si on veut voir les cours "Refusés" (il faut être admin)
    elseif ($status == 3) $query .= "AND `_etat` = '6' ";


  }
  else
  {
    switch ($lessonstatus) {
      case 'accepted':
        $status = 5;
        // echo "<div class=\"alert alert-success\">Cours validés:</div>";
        break;
      case 'refused':
        $status = 6;
        // echo "<div class=\"alert alert-danger\">Cours refusés:</div>";
        break;
      case 'waiting':
        $status = 4;
        // echo "<div class=\"alert\">Cours en attente de validation:</div>";
        break;
    }
    $query .= "AND `_etat` = '".$status."' ";
  }

  if ($sortorder == "asc") $sortOrder = "asc";
  else $sortOrder = "desc";


  if ($sortby == "pole") $sortBy = "_ID_pma";
  elseif ($sortby == "mat") $sortBy = "_IDmat";
  elseif ($sortby == "classe") $sortBy = "_IDclass";
  elseif ($sortby == "prof") $sortBy = "_ID";
  elseif ($sortby == "room") $sortBy = "_IDitem";
  elseif ($sortby == "time") $sortBy = "_debut";
  elseif ($sortby == "date") $sortBy = "_jour ".$sortOrder.", _nosemaine ".$sortOrder.", _annee ";
  elseif ($sortby == "state")
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
          END
      )
    ";
  }

  // Exclusion des matières de types indisponible
  $query .= "AND `_IDmat` in (SELECT `_IDmat` FROM `campus_data` WHERE `_type` != '2') ";

  if ($sortorder != "" and $sortorder != "0") $query .= "ORDER BY ".$sortBy." ".$sortOrder." ";
  else $query .= "ORDER BY `_annee`, `_nosemaine`, `_jour`, `_debut` ASC ";

// echo $query."<br>";

  $result = mysqli_query($mysql_link, $query);

  // 1. Create new calendar
  $vCalendar = new \Eluceo\iCal\Component\Calendar($_SESSION['CfgWeb']);

  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

    $listeClassesToShow = "";
    $listeClassesAlreadyShown = ";";
    $listeClasses = $row[4];
    $listeClasses = explode(";", $listeClasses);

    foreach ($listeClasses as $key => $value) {
      if ($value != 0 and strpos($listeClassesAlreadyShown, $value) === false)
      {
        $listeClassesAlreadyShown .= $value.";";
        if ($listeClassesToShow != "") $listeClassesToShow .= " - ";
        $listeClassesToShow .= "".getClassNameByClassID($value);
      }
    }

    // Création de la date
    $anneeDate = $row[16];
    $dateBase = $anneeDate."-01-01";
    $date = strtotime($dateBase);
    $daysToAdd = ($row[13] - 1) * 7;
    $date = strtotime("+".$daysToAdd." day", $date);
    $date = strtotime("+".$row[9]." day", $date);
    $date = strtotime("-1 day", $date);
    // Création de la durée
    // $heure_debut = strtotime("10:00:00");
    // $heure_fin = strtotime("12:30:00");

    $heure_debut = substr($row[10], 0, 2);
    $heure_fin = substr($row[11], 0, 2);

    $minutes_debut = substr($row[10], 3, 2);
    $minutes_fin = substr($row[11], 3, 2);

    $ID_examen = $row[23];
    $ID_PMA = $row[24];

    $matiereName = getMatNameByIdMat($row[3]);

    if ($ID_examen != 0) $matiereToShow = "UV - ".$matiereName;
    elseif (getMatTypeByMatID($row[3]) == 3) $matiereToShow = "Agenda - ".$row[22];
    else $matiereToShow = $matiereName;

    if ($ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
    else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));

    if ($row[18] != "" && $row[18] != 0) $nameRmpl = " - ".getUserNameByID($row[18]);
    else $nameRmpl = "";

    // On crée les descriptions/titres des évènements
    $eventTitle = $poleName." - ".$matiereToShow;
    $eventTitleDescription = $listeClassesToShow. "\n\r".$poleName."\n\r".$matiereToShow."\n\r".getUserNameByID($row[6]).$nameRmpl;
    $eventTitleDescriptionHTML = $listeClassesToShow. "<br><b>".$poleName." - ".$matiereToShow."</b><br>".getUserNameByID($row[6]).$nameRmpl;

    // Correction de la date quand passage à l'année suivante
    // Si les jours ne correspondent pas, alors on corrige:
    if (($row[9] + 1) != date("N", $date)) $dayNumber = date('d', $date) - 1;
    else $dayNumber = date('d', $date);

    $vEvent = new \Eluceo\iCal\Component\Event();

    $vEvent->setDtStart(new \DateTime(date('Y', $date)."-".date('m', $date)."-".$dayNumber."T".$heure_debut.$minutes_debut."00"));
    $vEvent->setDtEnd(new \DateTime(date('Y', $date)."-".date('m', $date)."-".$dayNumber."T".$heure_fin.$minutes_fin."00"));

    // Titre de l'évènement
    $vEvent->setSummary($eventTitle);
    // Description
    $vEvent->setDescription($eventTitleDescription);
    // Description en HTML
    $vEvent->setDescriptionHTML($eventTitleDescriptionHTML);
    // add some location information for apple devices
    $vEvent->setLocation(getRoomNameByID($row[5]));

    $vEvent->setUseTimezone(true);
    // 3. Add event to calendar
    $vCalendar->addComponent($vEvent);


  }

  echo $vCalendar->render();


?>
