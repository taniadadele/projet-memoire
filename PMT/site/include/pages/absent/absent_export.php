<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 Thomas Dazy (contact@thomasdazy.fr)

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
 *		module   : user_account.php
 *		projet   : la page de gestion du compte utilisateur
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 16/10/2019
 *		modif    :
 */




 session_start();
 $IDeleve  = (int) @$_GET["IDeleve"];		// Identifiant de l'élève
 $year     = (int) @$_GET["year"];			// année
 error_reporting(0);
 // error_reporting(E_ALL);
 // ini_set("display_errors", 1);




   require_once "config.php";
   require_once "include/sqltools.php";
   require_once "php/functions.php";
   // require_once "../relation.php";
   include ('include/fonction/protection_input.php');
   $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
   // require("include/fonction/parametre.php");
   // require("include/fonction/relation.php");
   // require("include/fonction/absence.php");
   require("include/notes.php");

   // if (!isset($_SESSION['CfgIdent']) || $_SESSION['CfgIdent'] == "") require("page_session.php");
   require_once "page_session.php";

   // include autoloader
   require_once 'lib/dompdf/autoload.inc.php';

   use Dompdf\Dompdf;

   $footer_texte = getParam('certificat_scol_texte_footer');
   $logo_name = getParam('bulletin_logo');

   $htmlElements = "<link href=\"".$_SESSION["ROOTDIR"]."/css/font-awesome.min.css\" rel=\"stylesheet\" type=\"text/css\">";

 // ----------------------------------------
 // En-tête du tableau
 // ----------------------------------------
 $htmlElements .= "<body style=\"font-size: 12px;\">";
 $htmlElements .= "<table width=\"100%\" style=\"width: 100%; margin: auto; table-layout: fixed; margin-top: 30px; font-size: 15px;\">

   <!-- Ligne image et intitulé -->
   <tr style=\"border: 0px;\" class=\"image_row\">
     <td>
       <img src=\"download/logos/".$_SESSION['CfgIdent']."/".$logo_name."\" style=\"max-width: 50%; padding-bottom: 20px;\">
     </td>

     <td style=\"padding-left: 55px; vertical-align: bottom; padding-bottom: 20px;\"><b>".getUserNameByID($IDeleve)."</b><br>".getUserAddressTwoLinesByUserID($IDeleve)."</td>

   </tr>

   <tr>
     <td>
       <b>".getUserNameByID($IDeleve)."</b><br>
       Date de naissance: ".getUserBirthdateByID($IDeleve)."


     </td>
     <td style=\"text-align: right;\">
       <b>Liste des absences</b><br>
       <b>".getUserClassByUserID($IDeleve)."</b> Année: ".$year." - ".($year + 1)."

     </td>


   </tr>


   </table>";


 $htmlElements .= "<table style=\"width: 100%;\" cellspacing=\"0\" cellpadding=\"0\">";
   $htmlElements .= "<tr style=\"text-align: center; font-weight: bold; background-color: #C0C0C0;\"><td colspan=\"5\">Absences</td></tr>";
   $htmlElements .= "<tr style=\"text-align: center; font-weight: bold; background-color: #D0D0D0;\">";
     $htmlElements .= "<td>Justifié</td>";
     $htmlElements .= "<td>Début</td>";
     $htmlElements .= "<td>Fin</td>";
     $htmlElements .= "<td>Raison</td>";
     $htmlElements .= "<td>Commentaire</td>";
   $htmlElements .= "</tr>";

   $date_start = $year."-".getParam('START_M')."-".getParam('START_D')." 00:00:00";
   $date_end = ($year + 1)."-".getParam('END_M')."-".getParam('END_D')." 23:59:59";
   $query = "SELECT _IDdata, _start, _end, _texte, _valid, _file FROM absent_items WHERE _IDabs = '".$IDeleve."' AND _start >= '$date_start' AND _start <= '$date_end' ";
   $result = mysqli_query($mysql_link, $query);
   while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
     $htmlElements .= "<tr style=\"text-align: center;\">";
       if ($row[4] == O) $htmlElements .= "<td style=\"padding: 0 5px 0 40px;\"><i class=\"fa fa-check\"></i></td>";
       else $htmlElements .= "<td style=\"padding: 0 5px 0 40px;\"><i class=\"fa fa-times\"></i></td>";
       $htmlElements .= "<td style=\"padding: 0 5px;\">".date('d/m/Y H:m', strtotime($row[1]))."</td>";
       $htmlElements .= "<td style=\"padding: 0 5px;\">".date('d/m/Y H:m', strtotime($row[2]))."</td>";
       $reason = getReasonByID($row[0]);
       $htmlElements .= "<td style=\"padding: 0 5px;\">".$reason."</td>";
       $htmlElements .= "<td style=\"padding: 0 5px; width: 40%;\">".$row[3]."</td>";
     $htmlElements .= "</tr>";
   }
 $htmlElements .= "</table>";




   $htmlElements .= "
   <div style=\"text-align: center; top: 90%; /* left: 50%; */ width: 100%; font-weight: 14px; -ms-transform: translate(0, -50%); transform: translate(0, -50%); position: absolute;\">
     ".$footer_texte."

   </div>

   <style>



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



 ?>
