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
 *		module   : absent_emargement_print.php
 *		projet   : Page d'impression de la feuille d'émargement d'une classe pour les admins
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 25/07/19
 */

//
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

  session_start();

  require_once "config.php";
  require_once "include/sqltools.php";
  require_once "php/functions.php";
  // require_once "../relation.php";
  include ('include/fonction/protection_input.php');
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  require("include/fonction/parametre.php");
  require("include/fonction/relation.php");

  // include autoloader
  require_once 'lib/dompdf/autoload.inc.php';

  use Dompdf\Dompdf;


  if (($_SESSION['CnxGrp'] == 4 || $_SESSION['CnxAdm'] == 255) && @$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
  {

    if (isset($_GET['classID'])) $classID = addslashes($_GET['classID']);
    else $classID = "";

    if (isset($_GET['date'])) $dateGET = addslashes($_GET['date']);
    else $dateGET = "";

    $dayNumberGet = date('N', strtotime($dateGET)) - 1;
    $weekNumberGet = date('W', strtotime($dateGET));
    $yearNumberGet = date('Y', strtotime($dateGET));

    // echo "<b>Date dans le GET</b>: ".$dateGET."<br>";





    $htmlElements = "
      <body>

      <table width=\"90%\" style=\"width: 90%; margin: auto; table-layout: fixed;\">

        <!-- Ligne image et intitulé -->
        <tr style=\"border: 0px;\" class=\"image_row\">
          <td colspan=\"2\">
            <img src=\"download/logos/".$_SESSION['CfgIdent']."/logo01_transparent.png\" style=\"max-width: 100%;\">
          </td>
          <td><b>Feuille de suivi pédagogique</b></td>
          <td><b>".getClassNameByClassID($classID)."</b></td>
        </tr>

        <!-- Ligne d'en-tête -->
        <tr class=\"table-head\">
          <td>Heure</td>
          <td>Professeur</td>
          <td>Matiere</td>
          <td>Contenu</td>
        </tr>

        <tr class=\"table-head\">
          <td colspan=\"4\" class=\"head_elements\">".date('d/m/Y', strtotime($dateGET))."</td>
        </tr>


    ";

    $query = "SELECT _ID, _IDrmpl, _debut, _fin, _IDmat, _ID_pma, _text, _ID_examen, _IDx FROM edt_data WHERE _jour = '".$dayNumberGet."' AND _nosemaine = '".$weekNumberGet."' AND _annee = '".$yearNumberGet."' ";
    $query .= "AND _IDclass LIKE '%".$classID."%' ";
    $query .= "AND (_etat = 1 OR _etat = 5) ";
    $query .= "ORDER BY _debut ASC ";
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
    {
      $matiereName = getMatNameByIdMat($row[4]);
      if ($row[7] != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($row[7]));
      else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($row[5]));
      if ($row[7] != 0) $matiereToShow = "<b>UV</b> - ".$matiereName;
      elseif (getMatTypeByMatID($row[4]) == 3)
      {
        if ($row[6] != "") $matiereToShow = "<b>Agenda</b> - ".$row[6];
        else $matiereToShow = "<b>Agenda</b>";
      }
      else $matiereToShow = $poleName." - ".$matiereName;

      $teacherName = getUserNameByID($row[0]);
      if ($row[1] != 0 && $row[1] != "" && $row[1] != $row[0]) $teacherName .= "<br>".getUserNameByID($row[1]);
      $contenu = "";
      $query_ctn = "SELECT _texte FROM ctn_items WHERE _IDcours = '".$row[8]."' ";
      $result_ctn = mysqli_query($mysql_link, $query_ctn);
      while ($row_ctn = mysqli_fetch_array($result_ctn, MYSQLI_NUM))
      {
        $contenu = $row_ctn[0];
      }


      $htmlElements .= "<tr>";
        // On affiche l'horraire du cours:
        $htmlElements .= "<td style=\"height: 107px; min-height: 107px; width: 10%; text-align: center\">".substr($row[2], 0, 5)."<br>".substr($row[3], 0, 5)."</td>";
        // On affiche le prof:
        $htmlElements .= "<td style=\"width: 20%;\">".$teacherName."</td>";
        // On affiche la matière:
        $htmlElements .= "<td style=\"width: 30%;\">".$matiereToShow."</td>";
        // Case vide de suivi:
        $htmlElements .= "<td>".$contenu."</td>";
      $htmlElements .= "</tr>";
    }


    if ($dayNumberGet < 6)
    {

      $htmlElements .= "
      <tr class=\"table-head\">
        <td colspan=\"4\" class=\"head_elements\">".date('d/m/Y', strtotime($dateGET.'+ 1 day'))."</td>
      </tr>
      ";
      $dayNumberGet = date('N', strtotime($dateGET.'+ 1 day')) - 1;

      $query = "SELECT _ID, _IDrmpl, _debut, _fin, _IDmat, _ID_pma, _text, _ID_examen, _IDx FROM edt_data WHERE _jour = '".$dayNumberGet."' AND _nosemaine = '".$weekNumberGet."' AND _annee = '".$yearNumberGet."' ";
      $query .= "AND _IDclass LIKE '%".$classID."%' ";
      $query .= "ORDER BY _debut ASC ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
      {
        $matiereName = getMatNameByIdMat($row[4]);
        if ($row[7] != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($row[7]));
        else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($row[5]));
        if ($row[7] != 0) $matiereToShow = "<b>UV</b> - ".$matiereName;
        elseif (getMatTypeByMatID($row[4]) == 3)
        {
          if ($row[6] != "") $matiereToShow = "<b>Agenda</b> - ".$row[6];
          else $matiereToShow = "<b>Agenda</b>";
        }
        else $matiereToShow = $poleName." - ".$matiereName;

        $teacherName = getUserNameByID($row[0]);
        if ($row[1] != 0 && $row[1] != "" && $row[1] != $row[0]) $teacherName .= "<br>".getUserNameByID($row[1]);
        $contenu = "";
        $query_ctn = "SELECT _texte FROM ctn_items WHERE _IDcours = '".$row[8]."' ";
        $result_ctn = mysqli_query($mysql_link, $query_ctn);
        while ($row_ctn = mysqli_fetch_array($result_ctn, MYSQLI_NUM))
        {
          $contenu = $row_ctn[0];
        }


        $htmlElements .= "<tr>";
          // On affiche l'horraire du cours:
          $htmlElements .= "<td style=\"height: 107px; min-height: 107px; width: 10%; text-align: center\">".substr($row[2], 0, 5)."<br>".substr($row[3], 0, 5)."</td>";
          // On affiche le prof:
          $htmlElements .= "<td style=\"width: 20%;\">".$teacherName."</td>";
          // On affiche la matière:
          $htmlElements .= "<td style=\"width: 30%;\">".$matiereToShow."</td>";
          // Case vide de suivi:
          $htmlElements .= "<td>".$contenu."</td>";
        $htmlElements .= "</tr>";
      }
    }







  }



    $htmlElements .= "</table></body>
    <style>
      .signature-row {
        height: 50px;
      }

      td:first-child {
        /* font-weight: bold; */
        /* background-color: #d2d2d2; */
      }

      .date {
        font-weight: bold;
        background-color: #d2d2d2;
        width: 15%;
      }

      .image_row {
        border: 0px;
        background-color: white;
      }
      .image_row td {
        border: 0px;
        background-color: white;
      }

      .table-head td {
        background-color: #b5b5b5;
        font-weight: bold;
      }

      table {
        border-collapse: collapse;
        overflow: hidden;

      }

      th, td {
        border: 1px solid black;
        text-align: center;
      }


      .head_elements {
        /* width: 16%; */
        width: 10px !important;
        /* height: 50px !important; */
        text-overflow: clip !important;

        background-color: #d2d2d2 !important;

        overflow: hidden !important;

        overflow-wrap: break-word;


        word-break: break-word;
        /* white-space: nowrap; */
      }

    </style>";

    // echo $htmlElements;

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();
    $dompdf->loadHtml($htmlElements);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();

?>
