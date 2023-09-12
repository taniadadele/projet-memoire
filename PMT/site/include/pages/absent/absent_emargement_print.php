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

    // echo "<b>Date dans le GET</b>: ".$dateGET."<br>";

    if ($classID != "")
    {
      $list_eleves_first_name = array();
      $list_eleves_last_name = array();
      $query = "SELECT `_name`, `_fname` FROM `user_id` WHERE `_IDclass` = '".$classID."' and `_adm` = 1 ORDER BY `_name` ASC";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $list_eleves_first_name[] = $row[0];
        $list_eleves_last_name[] = $row[1];

      }
    }


    $mat_1 = $pole_1 = $prof_1 = "";
    $mat_2 = $pole_2 = $prof_2 = "";
    $mat_3 = $pole_3 = $prof_3 = "";
    $mat_4 = $pole_4 = $prof_4 = "";
    if ($dateGET != "")
    {
      // Première plage horraire
      $tab_horaire = json_decode(getParam('plageHoraire'), TRUE);
      $dayNumberGet = date('N', strtotime($dateGET)) - 1;
      $weekNumberGet = date('W', strtotime($dateGET));
      $yearNumberGet = date('Y', strtotime($dateGET));
      $tab_horaire_1_start = $tab_horaire[1]['start'].":00";
	  $tab_horaire_1_start = strval(intval(substr($tab_horaire_1_start, 0, 2))-1).substr($tab_horaire_1_start, 2);
      $tab_horaire_1_end = $tab_horaire[1]['end'].":00";
      $tab_horaire_1_end = strval(intval(substr($tab_horaire_1_end, 0, 2))-1).substr($tab_horaire_1_end, 2);
      $query_mat_1  = "SELECT _IDmat, _ID_pma, _ID, _IDrmpl FROM edt_data WHERE _jour = '".$dayNumberGet."' AND _nosemaine = '".$weekNumberGet."' AND _annee = '".$yearNumberGet."' ";
      $query_mat_1 .= "AND _debut >= '".$tab_horaire_1_start."' AND _debut < '".$tab_horaire_1_end."' ";
      $query_mat_1 .= "AND _IDclass LIKE '%;".$classID.";%' ";
      $query_mat_1 .= "AND (_etat = 1 OR _etat = 5) ";
      $result_mat_1 = mysqli_query($mysql_link, $query_mat_1);
      while ($row_mat_1 = mysqli_fetch_array($result_mat_1, MYSQLI_NUM)) {
        $mat_1 = getMatNameByIdMat($row_mat_1[0]);
        $pole_1 = getPoleNameByIdPole(getPoleIDByPMAID($row_mat_1[1]));
        $prof_1 = getUserNameByID($row_mat_1[2]);
        if ($row_mat_1[3] != 0 && $row_mat_1[3] != "") $prof_1 .= " - ".getUserNameByID($row_mat_1[3]);

      }


      // Deuxième plage horaire
      $dayNumberGet = date('N', strtotime($dateGET)) - 1;
      $weekNumberGet = date('W', strtotime($dateGET));
      $yearNumberGet = date('Y', strtotime($dateGET));
      $tab_horaire_2_start = $tab_horaire[2]['start'].":00";
      $tab_horaire_2_end = $tab_horaire[2]['end'].":00";
      $query_mat_2  = "SELECT _IDmat, _ID_pma, _ID, _IDrmpl FROM edt_data WHERE _jour = '".$dayNumberGet."' AND _nosemaine = '".$weekNumberGet."' AND _annee = '".$yearNumberGet."' ";
      $query_mat_2 .= "AND _fin > '".$tab_horaire_2_start."' AND _fin <= '".$tab_horaire_2_end."' ";
      $query_mat_2 .= "AND _IDclass LIKE '%;".$classID.";%' ";
      $query_mat_2 .= "AND (_etat = 1 OR _etat = 5) ";
      $result_mat_2 = mysqli_query($mysql_link, $query_mat_2);
      while ($row_mat_2 = mysqli_fetch_array($result_mat_2, MYSQLI_NUM)) {
        $mat_2 = getMatNameByIdMat($row_mat_2[0]);
        $pole_2 = getPoleNameByIdPole(getPoleIDByPMAID($row_mat_2[1]));
        $prof_2 = getUserNameByID($row_mat_2[2]);
        if ($row_mat_2[3] != 0 && $row_mat_2[3] != "") $prof_2 .= " - ".getUserNameByID($row_mat_2[3]);

      }


      // Troisième plage horaire
      $dayNumberGet = date('N', strtotime($dateGET)) - 1;
      $weekNumberGet = date('W', strtotime($dateGET));
      $yearNumberGet = date('Y', strtotime($dateGET));
      $tab_horaire_3_start = $tab_horaire[3]['start'].":00";
      $tab_horaire_3_end = $tab_horaire[3]['end'].":00";
      $tab_horaire_3_end = strval(intval(substr($tab_horaire_3_end, 0, 2))-1).substr($tab_horaire_3_end, 2);
      $query_mat_3  = "SELECT _IDmat, _ID_pma, _ID, _IDrmpl FROM edt_data WHERE _jour = '".$dayNumberGet."' AND _nosemaine = '".$weekNumberGet."' AND _annee = '".$yearNumberGet."' ";
      $query_mat_3 .= "AND _debut >= '".$tab_horaire_3_start."' AND _debut < '".$tab_horaire_3_end."' ";
      $query_mat_3 .= "AND _IDclass LIKE '%;".$classID.";%' ";
      $query_mat_3 .= "AND (_etat = 1 OR _etat = 5) ";
      $result_mat_3 = mysqli_query($mysql_link, $query_mat_3);
      while ($row_mat_3 = mysqli_fetch_array($result_mat_3, MYSQLI_NUM)) {
        $mat_3 = getMatNameByIdMat($row_mat_3[0]);
        $pole_3 = getPoleNameByIdPole(getPoleIDByPMAID($row_mat_3[1]));
        $prof_3 = getUserNameByID($row_mat_3[2]);
        if ($row_mat_3[3] != 0 && $row_mat_3[3] != "") $prof_3 .= " - ".getUserNameByID($row_mat_3[3]);

      }


      // Quatrième plage horaire
      $dayNumberGet = date('N', strtotime($dateGET)) - 1;
      $weekNumberGet = date('W', strtotime($dateGET));
      $yearNumberGet = date('Y', strtotime($dateGET));
      $tab_horaire_4_start = $tab_horaire[4]['start'].":00";
      $tab_horaire_4_end = $tab_horaire[4]['end'].":00";
      $query_mat_4  = "SELECT _IDmat, _ID_pma, _ID, _IDrmpl FROM edt_data WHERE _jour = '".$dayNumberGet."' AND _nosemaine = '".$weekNumberGet."' AND _annee = '".$yearNumberGet."' ";
      $query_mat_4 .= "AND _fin > '".$tab_horaire_4_start."' ";
      $query_mat_4 .= "AND _IDclass LIKE '%;".$classID.";%' ";
      $query_mat_4 .= "AND (_etat = 1 OR _etat = 5) ";
      $result_mat_4 = mysqli_query($mysql_link, $query_mat_4);
      while ($row_mat_4 = mysqli_fetch_array($result_mat_4, MYSQLI_NUM)) {
        $mat_4 = getMatNameByIdMat($row_mat_4[0]);
        $pole_4 = getPoleNameByIdPole(getPoleIDByPMAID($row_mat_4[1]));
        $prof_4 = getUserNameByID($row_mat_4[2]);
        if ($row_mat_4[3] != 0 && $row_mat_4[3] != "") $prof_4 .= " - ".getUserNameByID($row_mat_4[3]);

      }

    }

    $mat_1 = substr(addslashes($mat_1), 0, 22);
    $pole_1 = substr(addslashes($pole_1), 0, 22);
    $prof_1 = substr(addslashes($prof_1), 0, 22);
    $mat_2 = substr(addslashes($mat_2), 0, 22);
    $pole_2 = substr(addslashes($pole_2), 0, 22);
    $prof_2 = substr(addslashes($prof_2), 0, 22);
    $mat_3 = substr(addslashes($mat_3), 0, 22);
    $pole_3 = substr(addslashes($pole_3), 0, 22);
    $prof_3 = substr(addslashes($prof_3), 0, 22);
    $mat_4 = substr(addslashes($mat_4), 0, 22);
    $pole_4 = substr(addslashes($pole_4), 0, 22);
    $prof_4 = substr(addslashes($prof_4), 0, 22);


    $htmlElements = "
      <body>

      <table width=\"90%\" style=\"width: 90%; margin: auto; table-layout: fixed;\">

        <!-- Ligne image et intitulé -->
        <tr style=\"border: 0px;\" class=\"image_row\">
          <td colspan=\"2\">
            <img src=\"download/logos/".$_SESSION['CfgIdent']."/logo01_transparent.png\" style=\"max-width: 100%;\">
          </td>
          <td colspan=\"2\"><b>Feuille journalière d'émargement</b><br><b>Date:</b> ".date('d/m/Y', strtotime($dateGET))."</td>
          <td colspan=\"2\"><b>".getClassNameByClassID($classID)."</b></td>

        </tr>

        <!-- Ligne matière -->
        <tr>
          <td colspan=\"2\">Matière</td>
          <td class=\"head_elements signature-row\" style=\"font-size: 12px;\">".$mat_1."</td>
          <td class=\"head_elements signature-row\" style=\"font-size: 12px;\">".$mat_2."</td>
          <td class=\"head_elements signature-row\" style=\"font-size: 12px;\">".$mat_3."</td>
          <td class=\"head_elements signature-row\" style=\"font-size: 12px;\">".$mat_4."</td>
        </tr>

        <!-- Ligne Nom professeur -->
        <tr>
          <td colspan=\"2\">Nom professeur</td>
          <td class=\"signature-row head_elements\" style=\"font-size: 12px;\">".$prof_1."</td>
          <td class=\"signature-row head_elements\" style=\"font-size: 12px;\">".$prof_2."</td>
          <td class=\"signature-row head_elements\" style=\"font-size: 12px;\">".$prof_3."</td>
          <td class=\"signature-row head_elements\" style=\"font-size: 12px;\">".$prof_4."</td>
        </tr>

        <!-- Ligne Signature professeur -->
        <tr>
          <td colspan=\"2\" class=\"signature-row\">Signature professeur</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
    ";

    $tab_horaire = json_decode(getParam('plageHoraire'), TRUE);
    function getTableHeader($tab_horaire)
    {
      // <!-- Ligne d'en-tête nom et prénom -->
      $toReturn = "
      <tr class=\"table-head\">
        <td colspan=\"2\" class=\"signature-row\">Noms et Prénoms</td>
        ";

      foreach ($tab_horaire as $key => $value) {
        $toReturn.= "<td><b>".$value['start']." - ".$value['end']."</td>";
      }
      $toReturn .= "</tr>";
      return $toReturn;
    }
    $htmlElements .= getTableHeader($tab_horaire);

    $compteur = 0;
    $currentPage = 0;
    foreach ($list_eleves_first_name as $key => $value) {
      if ($compteur == 14 && $currentPage == 0)
      {
        $htmlElements .= getTableHeader($tab_horaire);
        $currentPage++;
        $compteur = 0;
      }
      elseif ($compteur % 18 == 0 && $currentPage != 0 && $compteur != 0)
      {
        $htmlElements .= getTableHeader($tab_horaire);
        $currentPage++;
      }
      $htmlElements .= "<tr><td class=\"name signature-row\" style=\"font-size: 12px;\">".$value."</td><td class=\"name\" style=\"font-size: 12px;\">".$list_eleves_last_name[$key]."</td>";
      $htmlElements .= "<td></td><td></td><td></td><td></td></tr>";
      $compteur++;
    }
    // foreach ($list_eleves_first_name as $key => $value) {
      // if ($compteur == 15 && $currentPage == 0)
      // {
        // $htmlElements .= getTableHeader($tab_horaire);
        // $currentPage++;
        // $compteur = 0;
      // }
      // elseif ($compteur % 18 == 0 && $currentPage != 0 && $compteur != 0)
      // {
        // $htmlElements .= getTableHeader($tab_horaire);
        // $currentPage++;
      // }
      // $htmlElements .= "<tr><td class=\"name signature-row\" style=\"font-size: 12px;\">".$value."</td><td class=\"name\" style=\"font-size: 12px;\">".$list_eleves_last_name[$key]."</td>";
      // $htmlElements .= "<td></td><td></td><td></td><td></td></tr>";
      // $compteur++;
    // }
    // foreach ($list_eleves_first_name as $key => $value) {
      // if ($compteur == 15 && $currentPage == 0)
      // {
        // $htmlElements .= getTableHeader($tab_horaire);
        // $currentPage++;
        // $compteur = 0;
      // }
      // elseif ($compteur % 18 == 0 && $currentPage != 0 && $compteur != 0)
      // {
        // $htmlElements .= getTableHeader($tab_horaire);
        // $currentPage++;
      // }
      // $htmlElements .= "<tr><td class=\"name signature-row\" style=\"font-size: 12px;\">".$value."</td><td class=\"name\" style=\"font-size: 12px;\">".$list_eleves_last_name[$key]."</td>";
      // $htmlElements .= "<td></td><td></td><td></td><td></td></tr>";
      // $compteur++;
    // }


    $htmlElements .= "</table></body>
    <style>
      .signature-row {
        height: 50px;
      }

      td:first-child {
        font-weight: bold;
        background-color: #d2d2d2;
      }

      .name {
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
        background-color: #b5b5b5 !important;
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
        height: 50px !important;
        text-overflow: clip !important;

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

    if (isset($_GET['tofolder']) && $_GET['tofolder'] != '')
    {
      // Si on est en phase d'export alors on enregistre le fichier plutôt que de le télécharger
      $output = $dompdf->output();
      file_put_contents("tmp/".$_GET['tofolder']."/absences.pdf", $output);
    }
    else
    {
      // Output the generated PDF to Browser
      $dompdf->stream();
    }

  }
?>
