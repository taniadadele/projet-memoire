<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2009 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : notes_pdf.php
 *		projet   : la page d'impression des bulletins de notes
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/12/09
 *		modif    :
 */

session_start();
$IDcentre = (int) @$_GET["IDcentre"];		// Identifiant du centre
$IDclass  = (int) @$_GET["IDclass"];		// Identifiant de la classe
$IDeleve  = (int) @$_GET["IDeleve"];		// Identifiant de l'élève
$year     = (int) @$_GET["year"];			// année
$period   = (int) @$_GET["period"];			// trimestre
error_reporting(0);
// error_reporting(E_ALL);
// ini_set("display_errors", 1);



  require_once "config.php";
  require_once "include/sqltools.php";
  require_once "php/functions.php";
  // require_once "../relation.php";
  include ('include/fonction/protection_input.php');
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  require("include/fonction/parametre.php");
  require("include/fonction/relation.php");
  require("include/fonction/absence.php");
  require("include/fonction/bulletin.php");
  require("include/notes.php");

  // include autoloader
  require_once 'lib/dompdf/autoload.inc.php';

  use Dompdf\Dompdf;

  if (!isset($_SESSION['CfgIdent'])) require("page_session.php");

  $footer_texte = getParam('certificat_scol_texte_footer');
  $logo_name = getParam('bulletin_logo');

  $htmlElements = "<link href=\"".$_SESSION["ROOTDIR"]."/css/font-awesome.min.css\" rel=\"stylesheet\" type=\"text/css\">";
  $list = json_decode(getParam('periodeList'), TRUE);

  // ----------------------------------------
  // En-tête du DOCUMENT
  // ----------------------------------------
  $htmlElements .= "<body style=\"font-size: 12px;\">";
    if (getParam('addSpaceBottomHeaderBulletinExport')) $temp = '<tr><td colspan="2"><br></td></tr>';
    $htmlElements .= "<table width=\"100%\" style=\"width: 100%; margin: auto; table-layout: fixed; /* margin-top: 30px; */ margin-bottom: 20px; font-size: 15px;\">

      <!-- Ligne image et intitulé -->
      <tr style=\"border: 0px;\" class=\"image_row\">
        <td>
          <img src=\"download/logos/".$_SESSION['CfgIdent']."/".$logo_name."\" style=\"max-width: 50%; padding-bottom: 20px;\">
        </td>

        <td style=\"padding-left: 55px; vertical-align: bottom; padding-bottom: 20px;\"><br><br><br><br><br><b>".getUserNameByID($IDeleve)."</b><br>".getUserAddressTwoLinesByUserID($IDeleve)."</td>

      </tr>
      ".$temp."
      <tr>
        <td>
          <b>".getUserNameByID($IDeleve)."</b><br>
          Date de naissance : ".getUserBirthdateByID($IDeleve)."


        </td>
        <td style=\"text-align: right;\">
          <b>Bulletin de notes</b><br>
          <!-- <b>".getUserClassByUserID($IDeleve)."</b>  -->Année : ".$year." - ".($year + 1)."

        </td>


      </tr>


      </table>";





        // if (getParam('centreCity') != 3 && getParam('centreCity') != 2) $htmlElements .= "<style>.bulletin_table {width: 71.3%;} </style>";
        // Est-ce que l'on est sur l'impression ?
      	$current_page_print = true;

        // // On affiche le tableau des notes (bulletin de notes)
        // $htmlElements .= getStudentNotesTable($year, $IDeleve, $period, 1);

        // On affiche le tableau des notes (bulletin de notes)
      	$notes_table = getStudentNotesTable($year, $IDeleve, $period, 1);
      	$htmlElements .= $notes_table['table'];



                                    ###    ##     ## ######## ########  ########
                                   ## ##   ##     ##    ##    ##     ## ##
                                  ##   ##  ##     ##    ##    ##     ## ##
      ####### ####### #######    ##     ## ##     ##    ##    ########  ######      ####### ####### #######
                                 ######### ##     ##    ##    ##   ##   ##
                                 ##     ## ##     ##    ##    ##    ##  ##
                                 ##     ##  #######     ##    ##     ## ########


      if (getParam('centreCity') != 1) {
      		// On récupère le texte du commentaire et le nombre d'heures de stage
      		$query_text = "SELECT _text, _stage_hours, _validated, _open_doors_hours, _attr FROM notes_text WHERE _IDeleve = '".$IDeleve."' AND _year = '".$year."' AND _period = '".$period."' ";
      		$text = '';
      		$stage_hours = 0;
      		$result_text = mysql_query($query_text);
      		while ($row_text = mysql_fetch_array($result_text, MYSQL_NUM)) {
      			$text                = $row_text[0];
      			$stage_hours         = $row_text[1];
      			$bulletin_validated  = $row_text[2];
            $open_doors_hours    = $row_text[3];
      			$attr                = json_decode($row_text[4], true);
      		}









      	// $htmlElements .= '<hr>';
        $htmlElements .= '<table style="width: 100%;">';
          $htmlElements .= '<tr>';
            if (!getParam('afficherHeureStageQue1AnneeBulletin') || getNiveauNumberByUserID($IDeleve) == 1) {
              $htmlElements .= '<td style="vertical-align: top; width: 50%;">';
                $htmlElements .= '<strong>Stage</strong>';
                $htmlElements .= '<br>';
                $htmlElements .= '<div style="vertical-align: middle;">';
                  if ($stage_hours == '') $stage_hours = 0;
                  $htmlElements .= 'Heures de stage effectuées : '.$stage_hours.' heures';
                $htmlElements .= '</div>';

                if (getParam('afficherHeuresSalonBulletin')) {
                  $htmlElements .= '<div style="vertical-align: middle;">';
                    if ($open_doors_hours == '') $open_doors_hours = 0;
                    $htmlElements .= 'Heures de portes ouvertes/salons : '.$open_doors_hours.' heures';
                  $htmlElements .= '</div>';
                }

                if (getParam('afficherRadioPassageRedoublementBulletin') && $period == 0) {
                  $htmlElements .= '<div style="vertical-align: middle;">';
                    $htmlElements .= 'Décision du Conseil Pédagogique : ';
                    if ($attr['redoublement_bulletin'] == 'passage_validated') $htmlElements .= 'passage en année supérieure';
                    elseif ($attr['redoublement_bulletin'] == 'redoublement') $htmlElements .= 'redoublement';
                    elseif ($attr['redoublement_bulletin'] == 'both') $htmlElements .= getParam('passageEtRedoublementSelectionnesTextBulletin');
                    else $htmlElements .= '';
                  $htmlElements .= '</div>';
                }


                $htmlElements .= '<br>';
                // $htmlElements .= '<strong>Validation</strong>';
                $htmlElements .= '<div style="vertical-align: middle;">';
                  $htmlElements .= '<table style="width: 100%;"><tr>';
                  if (getParam('afficherAppreciationBulletin')) $htmlElements .= '<td style="width: 70%"><strong>'.getParam('intituleRemarqueBulletin').'</strong><br>'.$text.'</td>';
                  $htmlElements .= '';
                  if ($bulletin_validated && getParam('canValidateSignatureBulletin')) $htmlElements .= '<td style="width: 30%"><img style="max-width: 150px;" src="download/logos/'.$_SESSION['CfgIdent'].'/'.getParam('bulletinSignature').'"></td>';
                  $htmlElements .= '</tr></table>';
                $htmlElements .= '</div>';
              $htmlElements .= '</td>';
            }
          $htmlElements .= '</tr>';
        $htmlElements .= '</table>';



      	$htmlElements .= '<hr />';

      	$htmlElements .= '<table style="width: 100%;">';
      		$htmlElements .= '<tr>';
      			$htmlElements .= '<td style="padding: 0px !important; /* border-right: 1px solid grey; */ padding-right: 5px !important; width: 30%; vertical-align: top;">';
              $htmlElements .= getCertificatTable($year, $IDeleve, getParam('modeCertificatTableBulletin'));
      			$htmlElements .= '</td>';


      			// ----------------------------------------
      			// Historique par pôle
      			// ----------------------------------------
      			$htmlElements .= '<td style="vertical-align: top; padding: 0px !important;">';
              $htmlElements .= getPoleAndMoyHistoryTable($year, $IDeleve, getParam('modePoleAndHistoryTableBulletin'), $notes_table['data']);
    				$htmlElements .= '</td>';
      		$htmlElements .= '</tr>';
      	$htmlElements .= '</table>';

      	$htmlElements .= '<br>';



        // --------------- ABSENCES ---------------
        $htmlElements .= getAbsenceTable($year, $IDeleve, getParam('modeAbsentTableBulletin'));
      }










                                 ##       ##    ##  #######  ##    ##
                                 ##        ##  ##  ##     ## ###   ##
                                 ##         ####   ##     ## ####  ##
      ####### ####### #######    ##          ##    ##     ## ## ## ##    ####### ####### #######
                                 ##          ##    ##     ## ##  ####
                                 ##          ##    ##     ## ##   ###
                                 ########    ##     #######  ##    ##


      if (getParam('centreCity') == 1) {
        // CERTIFICATS
        $htmlElements .= getCertificatTable($year, $IDeleve, getParam('modeCertificatTableBulletin'));
        // HISTORIQUE
        $htmlElements .= getPoleAndMoyHistoryTable($year, $IDeleve, getParam('modePoleAndHistoryTableBulletin'), $notes_table['data']);
        // ABSENCES
        if ($current_page_print) $mode = 2; else $mode = 1;
        $htmlElements .= getAbsenceTable($year, $IDeleve, $mode, getParam('modeAbsentTableBulletin'));






      	// <!-- *************** ZONES DE SAISIE *************** -->

      		// On récupère le texte du commentaire et le nombre d'heures de stage
      		$query_text = "SELECT _text, _stage_hours, _validated, _open_doors_hours, _attr FROM notes_text WHERE _IDeleve = '".$IDeleve."' AND _year = '".$year."' AND _period = '".$period."' ";
      		$text = '';
      		$stage_hours = 0;
      		$result_text = mysql_query($query_text);
      		while ($row_text = mysql_fetch_array($result_text, MYSQL_NUM)) {
      			$text                = $row_text[0];
      			$stage_hours         = $row_text[1];
      			$bulletin_validated  = $row_text[2];
            $open_doors_hours    = $row_text[3];
      			$attr                = json_decode($row_text[4], true);
      		}

      	$htmlElements .= '<hr>';
        $htmlElements .= '<table style="width: 100%;">';
          $htmlElements .= '<tr>';
            if (!getParam('afficherHeureStageQue1AnneeBulletin') || getNiveauNumberByUserID($IDeleve) == 1) {
              $htmlElements .= '<td style="vertical-align: top; width: 50%;">';
                $htmlElements .= '<strong>Stage</strong>';
                $htmlElements .= '<br>';
                $htmlElements .= '<div style="vertical-align: middle;">';
                  if ($stage_hours == '') $stage_hours = 0;
                  $htmlElements .= 'Heures de stage effectuées : '.$stage_hours.' heures';
                $htmlElements .= '</div>';

                if (getParam('afficherHeuresSalonBulletin')) {
                  $htmlElements .= '<div style="vertical-align: middle;">';
                    if ($open_doors_hours == '') $open_doors_hours = 0;
                    $htmlElements .= 'Heures de portes ouvertes/salons : '.$open_doors_hours.' heures';
                  $htmlElements .= '</div>';
                }

                if (getParam('afficherRadioPassageRedoublementBulletin') && $period == 0) {
                  $htmlElements .= '<div style="vertical-align: middle;">';
                    $htmlElements .= 'Décision du Conseil Pédagogique : ';
                    if ($attr['redoublement_bulletin'] == 'passage_validated') $htmlElements .= 'passage en année supérieure';
                    elseif ($attr['redoublement_bulletin'] == 'redoublement') $htmlElements .= 'redoublement';
                    elseif ($attr['redoublement_bulletin'] == 'both') $htmlElements .= getParam('passageEtRedoublementSelectionnesTextBulletin');
                    else $htmlElements .= '';
                  $htmlElements .= '</div>';
                }



                $htmlElements .= '<br>';
                // $htmlElements .= '<strong>Validation</strong>';
                $htmlElements .= '<div style="vertical-align: middle;">';
                  $htmlElements .= '<table style="width: 100%;"><tr>';
                  if (getParam('afficherAppreciationBulletin')) $htmlElements .= '<td style="width: 70%"><strong>'.getParam('intituleRemarqueBulletin').'</strong><br>'.$text.'</td>';
                  $htmlElements .= '';
                  if ($bulletin_validated && getParam('canValidateSignatureBulletin')) $htmlElements .= '<td style="width: 30%"><img style="max-width: 200px;" src="download/logos/'.$_SESSION['CfgIdent'].'/'.getParam('bulletinSignature').'"></td>';
                  $htmlElements .= '</tr></table>';
                $htmlElements .= '</div>';
              $htmlElements .= '</td>';
            }
          $htmlElements .= '</tr>';
        $htmlElements .= '</table>';




      }

























      if (getParam('afficherGrisColonnesEleveBulletin')) $student_datas = '.student_datas { background-color: #C0C0C0; }';
      else $student_datas = '';

    $htmlElements .= "
    <div style=\"text-align: center; top: 98%; /* left: 50%; */ width: 100%; font-weight: 14px; -ms-transform: translate(0, -50%); transform: translate(0, -50%); position: absolute;\">
      ".$footer_texte."

    </div>

    <style>

    ".$student_datas."

    /* En-tête de tableau */
  	.table_header {
  		background-color: #C0C0C0;
  		font-weight: bold;
      text-align: center;
  	}

  	.table_header_2 {
  		background-color: #E0E0E0;
  		font-weight: bold;
  		text-align: center;
  		padding: 0 3px;
  	}

  	.note_certificat {
  		padding: 5px;
  	}

    /* On retire les bordures des lignes de nom de pôles */
    .pole_name_row > td {
      border: none;
    }
    .pole_name_row td:first-child {
      border-left: 1px solid black !important;
    }
    .pole_name_row td:last-child {
      border-right: 1px solid black !important;
    }


    table {
      border-spacing : 0;
      border-collapse : collapse;
    }
    table tr td {
      padding: 0px;
    }


    .notes_mat_prof_td {
      padding-left: 10px;
    }

    .rattrapage_title {
      width: 1%;
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
  file_put_contents("tmp/".$_GET['tofolder']."/bulletin_".$year.".pdf", $output);
}
else
{
  // Output the generated PDF to Browser
  $dompdf->stream('bulletin_'.str_replace(' ', '_', getUserNameByID($IDeleve)).'.pdf');
}








?>
