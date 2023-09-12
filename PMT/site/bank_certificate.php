<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2020 by Thomas Dazy (contact@thomasdazy.fr)

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
 *		module   : bank_certificate.php
 *		projet   : Page d'impression d'un reçu des opérations bancaires
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 11/03/20
 */


// error_reporting(E_ALL);
// ini_set("display_errors", 1);

  session_start();

  require_once "config.php";
  require_once "include/sqltools.php";
  require_once "php/functions.php";
  include ('include/fonction/protection_input.php');
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  require("include/fonction/parametre.php");
  require("include/fonction/relation.php");

  // include autoloader
  require_once 'lib/dompdf/autoload.inc.php';

  use Dompdf\Dompdf;


  if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
  {
    $today_date                 = date('d/m/Y');
    $school_full_title          = getParam('titre_etablissement');
    $current_study_year         = getParam('START_Y')." - ".getParam('END_Y');
    $footer_texte               = getParam('certificat_scol_texte_footer');
    $school_full_title          = getParam('titre_etablissement');
    $logo_picture_name          = getParam('certificat_scol_logo');

    $study_year_text            = getParam('certificat_study_year');
    $scolarship_bill_text       = getParam('certificat_scolarship_bill');
    $signature_image            = getParam('certificat_image_bill_signature');
    $nom_tresorier              = getParam('certificat_nom_tresorier');
    $current_study_year         = getParam('START_Y')." - ".getParam('END_Y');

    $eleve_name                 = getUserNameByID($IDeleve);
    $eleve_classe               = getUserClassByUserID($IDeleve);

    if (isset($_GET['IDeleve']) && $_SESSION['CnxAdm'] == 255) $IDeleve = addslashes($_GET['IDeleve']);
    else $IDeleve = $_SESSION['CnxID'];
    if (isset($_GET['date_1'])) $date_1 = addslashes($_GET['date_1']);
    if (isset($_GET['date_2'])) $date_2 = addslashes($_GET['date_2']);

    $htmlElements = "
    <body>
      <div style=\"width: 100%; margin: auto;\">
        <img src=\"download/logos/".$_SESSION['CfgIdent']."/".$logo_picture_name."\" style=\"position: absolute; max-width: 30%;\">

        <div style=\"top: 20%; left: 50%; ; font-weight: 18px; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); position: absolute;\">
          <strong>".$scolarship_bill_text."</strong>
        </div>

        <div style=\"top: 45%; left: 50%; width: 80%; font-weight: 14px; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); position: absolute; text-align: center;\">
          Nom de l'étudiant: ".$eleve_name."<br>
          Année d'étude: ".$eleve_classe."<br>


          <br>
          <h3 style='text-align: center;'>".$study_year_text." ".$current_study_year."</h3>


          <table style='margin: auto;'>
            <tr>
              <th>Date du règlement</th>
              <th>Montant réglé</th>
            </tr>


          ";

          $query = "SELECT bank._ID, bank._date, bank._libele, bank._price, bank._IDeleve FROM bank_data bank WHERE 1 ";
          $query .= "AND bank._date >= '".$date_1."' ";
          $query .= "AND bank._date <= '".$date_2."' ";
          $query .= "AND bank._IDeleve = '".$IDeleve."' ";
          $result = mysqli_query($mysql_link, $query);
          $currentAmount = 0;
          while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            $htmlElements .= "<tr>";
              $htmlElements .= "<td>";
                $htmlElements .= date('d/m/Y', strtotime($row[1]));
              $htmlElements .= "</td>";
              $htmlElements .= "<td>";
                $htmlElements .= $row[3]." €";
              $htmlElements .= "</td>";

            $htmlElements .= "</tr>";



          }



      $htmlElements .= "

          </table>
        </div>

        <div style=\"text-align: right; top: 70%; left: 70%; width: 30%; font-weight: 14px; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); position: absolute;\">

          ".$nom_tresorier."<br>
          Trésorier de la ".$school_full_title."<br>
          <img src=\"download/logos/".$_SESSION['CfgIdent']."/".$signature_image."\" style=\"max-width: 100%;\">
        </div>

        <div style=\"text-align: center; top: 90%; /* left: 50%; */ width: 100%; font-weight: 14px; -ms-transform: translate(0, -50%); transform: translate(0, -50%); position: absolute;\">
          ".$footer_texte."

        </div>
      </div>
    </body>

    ";



    // echo $htmlElements;

    if ($_SESSION['CnxID'] != "" && $_SESSION['CnxID'] != 0)
    {
      // instantiate and use the dompdf class
      $dompdf = new Dompdf();
      $dompdf->loadHtml($htmlElements);

      // (Optional) Setup the paper size and orientation
      $dompdf->setPaper('A4', 'portrait');

      // Render the HTML as PDF
      $dompdf->render();

      // Output the generated PDF to Browser
      $dompdf->stream("Reçu_frais_scolarité.pdf");
    }


  }
?>
