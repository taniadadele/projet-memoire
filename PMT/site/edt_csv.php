<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 by Thomas Dazy (contact@thomasdazy.fr)

   This file is part of Prométhée.

   Prométhée is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Prométhée is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/


/*
 *		module   : edt_list.htm
 *		projet   : Page de visualisation des éléments de l'edt en mode liste avec filtres
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 17/04/19
 */



 require_once "config.php";
 require_once "include/sqltools.php";
 require_once "php/functions.php";

 $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
 require("include/fonction/parametre.php");
 require("include/fonction/relation.php");




require_once("include/ical/zapcallib.php");


$title = "Simple Event";
// date/time is in SQL datetime format
$event_start = "2020-01-01 12:00:00";
$event_end = "2020-01-01 13:00:00";
// UID is a required item in VEVENT, create unique string for this event
// Adding your domain to the end is a good way of creating uniqueness
$uid = date('Y-m-d-H-i-s') . "@demo.icalendar.org";

function createIcalEvent($title, $event_start, $event_end, $description, $uid, $categories)
{
  // create the ical object
  $icalobj = new ZCiCal();
  // create the event within the ical object
  $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
  // add title
  $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
  // add start date
  $eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
  // add end date
  $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));
  // add uid
  $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
  // DTSTAMP is a required item in VEVENT
  $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));

$eventobj->addNode(new ZCiCalDataNode("CATEGORIES:" . $categories));


  // Add description
  $eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent($description)));
  // write iCalendar feed to stdout
  return $icalobj->export();
}

echo createIcalEvent($title, $event_start, $event_end, $description, $uid);





  $query = "SELECT * FROM `edt_data` WHERE 1 ";
  $result = mysqli_query($mysql_link, $query);

  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    if (getMatTypeByMatID($row[3]) != 2)
    {
      $listeClassesToShow = "";
      $listeClassesAlreadyShown = ";";
      $listeClasses = $row[4];
      $listeClasses = explode(";", $listeClasses);
      foreach ($listeClasses as $key => $value) {
        if ($value != 0 and strpos($listeClassesAlreadyShown, $value) === false)
        {
          $listeClassesAlreadyShown .= $value.";";
          $listeClassesToShow .= "<span class=\"badge\">".getClassNameByClassID($value)."</span>&nbsp;";
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

      if ($minutes_duree != 0 and $heure_duree != 0) $duree .= "h".$minutes_duree;
      elseif ($minutes_duree != 0) $duree = $minutes_duree."min";
      elseif($minutes_duree == 0 and $heure_duree != 0) $duree .= "h";

      // echo $heure_duree.":".$minutes_duree."---";


      $ID_examen = $row[23];
      $ID_PMA = $row[24];

      $matiereName = getMatNameByIdMat($row[3]);


      if ($ID_examen != 0) $matiereToShow = "<b>UV</b> - ".$matiereName;
      elseif (getMatTypeByMatID($row[3]) == 3) $matiereToShow = "<b>Agenda</b> - ".$row[22];
      else $matiereToShow = $matiereName;





    }


  }



?>
