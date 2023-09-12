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
 *		module   : user_certificat_scolarite.php
 *		projet   : Page d'impression du certificat de scolarité
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 04/09/19
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


  if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
  {



    $today_date                 = date('d/m/Y');
    $base_texte                 = getParam('certificat_scol_texte');
    $DIRECTOR_NAME              = getParam('certificat_scol_nom_directeur');
    $TITRE_DIR                  = getParam('certificat_scol_titre_directeur');
    $logo_picture_name          = getParam('certificat_scol_logo');
    $signature_picture_name     = getParam('certificat_scol_signature');
    $school_full_title          = getParam('titre_etablissement');
    $current_study_year         = getParam('START_Y')." - ".getParam('END_Y');
    $footer_texte               = getParam('certificat_scol_texte_footer');


    if ((addslashes($_GET['id']) != "" || isset($_GET['id'])) && ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4)) $user_ID = addslashes($_GET['id']);
    else $user_ID = $_SESSION['CnxID'];

    $user_name  = getUserNameByID($user_ID);
    $user_birth_date = getUserBirthdateByID($user_ID);
    $user_birth_place = getUserBirthPlaceByID($user_ID);
    $user_sexe  = getUserSexeByID($user_ID);
    $user_address = getUserAddressByUserID($user_ID);

    if ($user_sexe == "H" || $user_sexe == "A" || $user_sexe == "") $base_texte = getParam('certificat_scol_texte_h');
    else $base_texte = getParam('certificat_scol_texte_f');

    if ($user_birth_place == "") $user_birth_place = "<i>inconnu</i>";
    if (strlen($user_address) < 5)     $user_address     = "<i>inconnu</i>";

    $base_texte = str_replace('__DIRECTOR_NAME__', "<strong>".$DIRECTOR_NAME."</strong>", $base_texte);
    $base_texte = str_replace('__USER_NAME__', $user_name, $base_texte);
    $base_texte = str_replace('__DATE__', $today_date, $base_texte);
    $base_texte = str_replace('__TITRE_DIR__', $TITRE_DIR, $base_texte);
    $base_texte = str_replace('__TITRE_SCHOOL__', $school_full_title, $base_texte);
    $base_texte = str_replace('__USER_NAME__', $user_name, $base_texte);
    $base_texte = str_replace('__USER_BIRTH__', $user_birth_date, $base_texte);
    $base_texte = str_replace('__USER_BIRTH_PLACE__', $user_birth_place, $base_texte);
    $base_texte = str_replace('__CURRENT_STUDY_YEAR__', $current_study_year, $base_texte);
    $base_texte = str_replace('__SCHOOL_CITY__', $_SESSION['CfgCity'], $base_texte);
    $base_texte = str_replace('__USER_ADDRESS__', $user_address, $base_texte);
    $base_texte = str_replace('__USER_PROMOTION__', getUserClassByUserID($user_ID), $base_texte);

    if (getParam('certificat_scol_show_titre_etablissement_before_signature')) $nom_etablissement_before_signature = $school_full_title.'<br>';
    else $nom_etablissement_before_signature = '';

    $htmlElements = "
    <body>
      <div style=\"width: 100%; margin: auto;\">
        <img src=\"download/logos/".$_SESSION['CfgIdent']."/".$logo_picture_name."\" style=\"position: absolute; max-width: 30%;\">

        <div style=\"top: 20%; left: 50%; ; font-weight: 18px; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); position: absolute;\">
          <strong>CERTIFICAT DE SCOLARITE</strong>
        </div>

        <div style=\"top: 45%; left: 50%; width: 80%; font-weight: 14px; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); position: absolute;\">
        ".$base_texte."
        </div>

        <div style=\"text-align: right; top: 70%; left: 70%; width: 30%; font-weight: 14px; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); position: absolute;\">

          ".$DIRECTOR_NAME."<br>
          ".$TITRE_DIR."<br>
          ".$nom_etablissement_before_signature."
          <img src=\"download/logos/".$_SESSION['CfgIdent']."/".$signature_picture_name."\" style=\"max-width: 100%;\">
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
      $dompdf->stream("Certificat_de_scolarite.pdf");
    }


  }
?>
