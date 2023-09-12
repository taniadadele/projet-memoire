<?php
function sendEdtList($email)
{
  //!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
  require_once "include/TMessage.php";
  require "msg/mail.php";
  $msg_mail  = new TMessage("msg/fr/mail.php");
  $msg_mail->msg_mail_search  = $keywords_search;
  $msg_mail->msg_mail_replace = $keywords_replace;

  $nextWeekNumber = date( 'W', strtotime( 'next week' ) );
  $msg = $msg_mail->read($MAIL_COURSES_OF_WEEK);
  $msg .= "<table cellpadding=\"10\" cellspacing=\"0\">";
    $msg .= "<tr style=\"font-weight: bold;\" bgcolor=\"#c7c7c7\">";
      $msg .= "<td>Pole</td>";
      $msg .= "<td>Matière</td>";
      $msg .= "<td>Classe</td>";
      $msg .= "<td>Salle</td>";
      $msg .= "<td>Date</td>";
      $msg .= "<td>Horaire</td>";
    $msg .= "</tr>";


  $query2  = "SELECT edt_data._jour, edt_data._debut, edt_data._fin, edt_data._nosemaine, edt_data._etat, edt_data._IDmat, edt_data._text, edt_data._ID_examen, edt_data._ID_pma, campus_data._type, campus_data._titre, pole_mat_annee._ID_pole, pole_mat_annee._ID_year, pole._name, edt_data._IDitem, edt_data._annee FROM edt_data INNER JOIN campus_data ";
  $query2 .= "INNER JOIN pole_mat_annee ";
  $query2 .= "INNER JOIN pole ";
  $query2 .= "ON campus_data._IDmat = edt_data._IDmat AND pole_mat_annee._ID_pma = edt_data._ID_pma AND pole._ID = pole_mat_annee._ID_pole ";
  $query2 .= "AND edt_data._nosemaine = '".$nextWeekNumber."' AND edt_data._annee = '".date('Y')."' AND (edt_data._ID IN (SELECT _ID FROM user_id WHERE _email = '".$email."') OR edt_data._IDrmpl IN (SELECT _ID FROM user_id WHERE _email = '".$email."')) ";
  $query2 .= "AND (edt_data._etat = 1 OR edt_data._etat = 5) ";
  $query2 .= "ORDER BY edt_data._jour ASC, edt_data._debut ASC ";



  $compteur = 0;
  $result2 = mysqli_query($mysql_link, $query2);
  while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
    // On exclus les matières indisonibles
    if ($row2[9] != 2)
    {
      $graduationYear = getGraduationYearByClassNumber($row2[12]);
      $classID = getClassIDByGraduationYear($graduationYear);
      $className = getClassNameByClassID($classID);

      $anneeDate = $row2[15];
      $dateBase = $anneeDate."-01-01";
      $date = strtotime($dateBase);
      $daysToAdd = ($row2[3] - 1) * 7;
      $date = strtotime("+".$daysToAdd." day", $date);
      $date = strtotime("+".$row2[0]." day", $date);
      $date = strtotime("-1 day", $date);

      $heure_debut = substr($row2[1], 0, 2);
      $heure_fin = substr($row2[2], 0, 2);
      $minutes_debut = substr($row2[1], 3, 2);
      $minutes_fin = substr($row2[2], 3, 2);

      if ($compteur % 2 == 0) $bgcolor = "#FFFFFF";
      else $bgcolor = "#e1e1e1";

      if ($row2[9] == 3)
      {
        $titre = $row2[6];
        $pole = "Agenda";
      }
      else
      {
        $titre = $row2[10];
        $pole = $row2[13];
      }

      // Correction de la date quand passage à l'année suivante
      // Si les jours ne correspondent pas, alors on corrige:
      if (($row[9] + 1) != date("N", $date)) $dayNumber = date('d', $date) - 1;
      else $dayNumber = date('d', $date) + 1;

      $msg .= "<tr bgcolor=\"".$bgcolor."\">";
        // $msg .= "<td>".$row2[13]."</td>";
        $msg .= "<td>".$pole."</td>";
        // $msg .= "<td>".$row2[10]."</td>";
        $msg .= "<td>".$titre."</td>";
        $msg .= "<td>".$className."</td>";
        $msg .= "<td>".getRoomNameByID($row2[14])."</td>";
        $msg .= "<td>".getDayNameByDayNumber($row2[0])." ".$dayNumber." ".getMonthNameByMonthNumber(date('m', $date))." ".date('Y', $date)."</td>";
        $msg .= "<td>De: ".$heure_debut."h".$minutes_debut." À: ".$heure_fin."h".$minutes_fin."</td>";
      $msg .= "</tr>";

      $compteur++;
    }
  }

  $msg .= "</table>";
  if ($compteur != 0) return $msg;
  else return false;
}

function getUserIdFromUserMail($userMail)
{
  $query = "SELECT `_ID` FROM `user_id` WHERE `_email` = '".$userMail."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}


// ----------------------------------------------------------------------------
// Fonction: Envoie un mail lors de la création/modif/suppression d'un
//           évènement pour prévenir les enseignants ET les étudiants
// ----------------------------------------------------------------------------
function sendCheckMailToTeacherAndStudents($IDevent, $typeOfModification, $ajax = "false")
{
  $temp = $_SESSION['temp_mail_edt'];
  sendCheckMailToStudents($IDevent, $typeOfModification, $ajax);
  $_SESSION['temp_mail_edt'] = $temp;
  sendCheckMailToTeacher($IDevent, $typeOfModification, $ajax);
  return true;
}





// ----------------------------------------------------------------------------
// Fonction: Envoie un mail lors de la création/modif/suppression d'un
//           évènement pour prévenir les enseignants
// ----------------------------------------------------------------------------
function sendCheckMailToTeacher($IDevent, $typeOfModification, $ajax = "false")
{
  if ($ajax == "true") $rootdir = "../../..";
  elseif ($ajax == 'session') $rootdir = "..";
  elseif ($ajax == 'function') $rootdir = '../../..';
  else $rootdir = $_SESSION['ROOTDIR'];

  //!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
  require_once $rootdir."/include/TMessage.php";
  require $rootdir."/msg/mail.php";
  $msg_mail  = new TMessage($rootdir."/msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
  $query = "SELECT * FROM `edt_data` WHERE `_IDx` = '".$IDevent."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $ID_PMA = $row[24];
    $ID_examen = $row[23];
    $matiereName = getMatNameByIdMat($row[3]);

    // Headers
    $headers = "From: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
    $headers .= "Reply-To: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
    $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
    $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);
    $toBcc = "Bcc: ";
    $toProf = "";
    // Récupérations des emails
    $queryuser  = "select `_email` from `user_id` ";
    $queryuser .= "where _ID = ".$row[6]." OR _ID = ".$row[18]." ";
    $queryuser .= "and `_adm` > 0 ";
    $resultuser = mysqli_query($mysql_link, $queryuser);
    while ($rowuser = mysqli_fetch_array($resultuser, MYSQLI_NUM)) {
      if ($toBcc != "Bcc: ") $toBcc .= ",";
      if ($toProf != "") $toProf .= ", ";
      $toBcc .= $rowuser[0];
      if (getParam("MAIL_DEV_MODE") == "") $toProf .= $rowuser[0];
      else $toProf = getParam("MAIL_DEV_MODE");
    }
    if (substr($toBcc, -1) == ",") $toBcc = substr($toBcc, 0, -1);
    if (getParam("MAIL_DEV_MODE") == "") $headers .= $toBcc."\r\n";
    $headers .= "\r\n";

    switch ($typeOfModification) {
      case 'insert':
        $subject = $msg_mail->read($MAIL_EDT_SUBJECT_ELEM_ADD);
        $msg = $msg_mail->read($MAIL_EDT_ELEMENT_ADDED);
        break;
      case 'update':
        $subject = $msg_mail->read($MAIL_EDT_SUBJECT_ELEM_MOD);
        $msg = $msg_mail->read($MAIL_EDT_ELEMENT_MODIFIED);
        break;
      case 'delete':
        $subject = $msg_mail->read($MAIL_EDT_SUBJECT_ELEM_REM);
        $msg = $msg_mail->read($MAIL_EDT_ELEMENT_REMOVED);
        break;
    }

    // Création de la date
    $gendate = new DateTime();
    $gendate->setISODate($row[16],$row[13],($row[9] + 1)); //year , week num , day

    // Création de la durée
    $heure_debut = strtotime("10:00:00");
    $heure_fin = strtotime("12:30:00");
    $heure_debut = substr($row[10], 0, 2);
    $heure_fin = substr($row[11], 0, 2);
    $minutes_debut = substr($row[10], 3, 2);
    $minutes_fin = substr($row[11], 3, 2);

    if (isset($ID_examen) && $ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
    else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));

    $msg .= "<br>";

    if ($ID_examen != 0) $matiereToShow = "<b>UV</b> - ".$matiereName;
    elseif (getMatTypeByMatID($row[3]) == 3) $matiereToShow = "<b>Agenda</b> - ".$row[22];
    else $matiereToShow = $matiereName;
    if ($ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
    else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));

    $msg .= "cours de ".$matiereToShow;
    // Date
    $msg .= " du <b>".getDayNameByDayNumber(date('N', strtotime($gendate->format('d-m-Y'))) - 1).' '.date('d', strtotime($gendate->format('d-m-Y'))).' '.getMonthNameByMonthNumber(date('m', strtotime($gendate->format('d-m-Y')))).' '.date('Y', strtotime($gendate->format('d-m-Y')))."</b>";
    // Horaire
    $msg .= " de: <b>".$heure_debut."h".$minutes_debut." à: ".$heure_fin."h".$minutes_fin."</b>";

    if (isset($_SESSION['temp_mail_edt']))
    {
      $datas = json_decode($_SESSION['temp_mail_edt'], TRUE);
      if ($datas['modif'] != 'delete')
        $msg .= ' (anciennement: '.$datas['old_date'].')';
      unset($_SESSION['temp_mail_edt']);
    }

    $body = "";
    $body .= $msg;
    // On ajoute la signature au mail:
    $body .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER_EDT);
    $body .= $_SESSION["CfgAdr"] . "<br>";
    if (getParam('showLinkToSiteMail')) $body .= "<a href=\"http://".$_SESSION['CfgWeb']."\">".$_SESSION["CfgWeb"]."</a>";

    if (getParam("MAIL_DEV_MODE") != "") $toProf = getParam("MAIL_DEV_MODE");
    mail($toProf, $subject, $body, $headers);

    // On log les résultats
    // 3 = Ajout de cours
    // 4 = Modif de cours
    // 5 = Suppression de cours
    switch ($typeOfModification) {
      case 'insert': $type = "3"; break;
      case 'update': $type = "4"; break;
      case 'delete': $type = "5"; break;
    }
    $temp = str_replace('Bcc: ', '', $toBcc);
    $temp2 = str_replace(',', ';', $temp);
    $temp3 = ';'.$toProf.';'.$temp2.';';
    $temp3 = str_replace(';;', ';', $temp3);
    $query = "INSERT INTO mail_log SET _id = NULL, _date = NOW(), _type = '".$type."', _dest_count = '".(substr_count($temp3, ';') - 1)."', _dest = '".$temp3."' ";
    mysqli_query($mysql_link, $query);
  }
  return true;
}



// ----------------------------------------------------------------------------
// Fonction: Envoie un mail lors de la création/modif/suppression d'un
//           évènement pour prévenir les étudiants
// ----------------------------------------------------------------------------
function sendCheckMailToStudents($IDevent, $typeOfModification, $ajax = "false")
{
  if ($ajax == "true") $rootdir = "../../..";
  elseif ($ajax == 'session') $rootdir = "..";
  elseif ($ajax == 'function') $rootdir = '../../..';
  else $rootdir = $_SESSION['ROOTDIR'];

  //!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
  require_once $rootdir."/include/TMessage.php";
  require $rootdir."/msg/mail.php";
  $msg_mail  = new TMessage($rootdir."/msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
  $query = "SELECT * FROM `edt_data` WHERE `_IDx` = '".$IDevent."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $ID_PMA = $row[24];
    $ID_examen = $row[23];
    $matiereName = getMatNameByIdMat($row[3]);

    // Headers
    $headers = "From: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
    $headers .= "Reply-To: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
    $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
    $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);

    $toBcc = "Bcc: ";

    $listeClassesToShow = "";
    $listeClassesAlreadyShown = ";";
    $listeClasses = $row[4];
    $listeClasses = explode(";", $listeClasses);
    foreach ($listeClasses as $key => $value) {
      if ($value != 0 and strpos($listeClassesAlreadyShown, $value) === false)
      {
        $listeClassesAlreadyShown .= $value.";";
        // Classe
        $queryclass  = "select _email from user_id ";
        $queryclass .= "where _IDclass = '$value' ";
        $queryclass .= "and `_adm` > 0 ";

        $resultclass = mysqli_query($mysql_link, $queryclass);
        while ($rowclass = mysqli_fetch_array($resultclass, MYSQLI_NUM)) {
          if ($toBcc != "Bcc: ") $toBcc .= ",";
          if (getParam("MAIL_DEV_MODE") == "") $toBcc .= $rowclass[0];
          else $toProf = getParam("MAIL_DEV_MODE");
        }
      }
    }

    if (substr($toBcc, -1) == ",") $toBcc = substr($toBcc, 0, -1);
    if (getParam("MAIL_DEV_MODE") == "") $headers .= $toBcc."\r\n";
    $headers .= "\r\n";
    switch ($typeOfModification) {
      case 'insert':
        $subject = $msg_mail->read($MAIL_EDT_SUBJECT_ELEM_ADD);
        $msg = $msg_mail->read($MAIL_EDT_ELEMENT_ADDED);
        break;
      case 'update':
        $subject = $msg_mail->read($MAIL_EDT_SUBJECT_ELEM_MOD);
        $msg = $msg_mail->read($MAIL_EDT_ELEMENT_MODIFIED);
        break;
      case 'delete':
        $subject = $msg_mail->read($MAIL_EDT_SUBJECT_ELEM_REM);
        $msg = $msg_mail->read($MAIL_EDT_ELEMENT_REMOVED);
        break;
    }
    // Création de la date
    $gendate = new DateTime();
    $gendate->setISODate($row[16],$row[13],($row[9] + 1)); //year , week num , day

    // Création de la durée
    $heure_debut = strtotime("10:00:00");
    $heure_fin = strtotime("12:30:00");
    $heure_debut = substr($row[10], 0, 2);
    $heure_fin = substr($row[11], 0, 2);
    $minutes_debut = substr($row[10], 3, 2);
    $minutes_fin = substr($row[11], 3, 2);

    if ($ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
    else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));
    $msg .= "<br>";

    if ($ID_examen != 0) $matiereToShow = "<b>UV</b> - ".$matiereName;
    elseif (getMatTypeByMatID($row[3]) == 3) $matiereToShow = "<b>Agenda</b> - ".$row[22];
    else $matiereToShow = $matiereName;
    if ($ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
    else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));

    $msg .= "cours de ".$matiereToShow;
    // Date
    $msg .= " du <b>".getDayNameByDayNumber(date('N', strtotime($gendate->format('d-m-Y'))) - 1).' '.date('d', strtotime($gendate->format('d-m-Y'))).' '.getMonthNameByMonthNumber(date('m', strtotime($gendate->format('d-m-Y')))).' '.date('Y', strtotime($gendate->format('d-m-Y')))."</b>";
    // Horaire
    $msg .= " de: <b>".$heure_debut."h".$minutes_debut." à: ".$heure_fin."h".$minutes_fin."</b>";
    if (isset($_SESSION['temp_mail_edt']))
    {
      $datas = json_decode($_SESSION['temp_mail_edt'], TRUE);
      if ($datas['modif'] != 'delete')
        $msg .= ' (anciennement: '.$datas['old_date'].')';
      unset($_SESSION['temp_mail_edt']);
    }
    $body = "";
    $body .= $msg;
    // On ajoute la signature au mail:
    $body .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER_EDT);
    $body .= $_SESSION["CfgAdr"] . "<br>";
    if (getParam('showLinkToSiteMail')) $body .= "<a href=\"http://".$_SESSION['CfgWeb']."\">".$_SESSION["CfgWeb"]."</a>";

    if (getParam("MAIL_DEV_MODE") != "") $toProf = getParam("MAIL_DEV_MODE");

    mail($toProf, $subject, $body, $headers);

    // On log les résultats

    // 3 = Ajout de cours
    // 4 = Modif de cours
    // 5 = Suppression de cours
    switch ($typeOfModification) {
      case 'insert': $type = "3"; break;
      case 'update': $type = "4"; break;
      case 'delete': $type = "5"; break;
    }
    $temp = str_replace('Bcc: ', '', $toBcc);
    $temp2 = str_replace(',', ';', $temp);
    $temp3 = ';'.$toProf.';'.$temp2.';';
    $temp3 = str_replace(';;', ';', $temp3);

    $query = "INSERT INTO mail_log SET _id = NULL, _date = NOW(), _type = '".$type."', _dest_count = '".(substr_count($temp3, ';') - 1)."', _dest = '".$temp3."' ";
    mysqli_query($mysql_link, $query);
  }
  return true;
}

function getSecondTeacherNameByEventId($eventID)
{
  $toReturn = "";
  $query = "SELECT `_IDrmpl` FROM `edt_data` WHERE `_IDx` = '".$eventID."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    if ($row[0] != 0)
    {
      $toReturn = " - ".getUserNameByID($row[0]);
    }
  }
  return $toReturn;
}


?>
