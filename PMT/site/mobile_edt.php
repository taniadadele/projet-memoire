<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2013 by IP-Solutions(contact@ip-solutions.fr)

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
 *		module   : mobile_edt.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */

	include("mobile_banner.php");

	require "msg/edt.php";
	require_once "include/TMessage.php";

	require "php/functions.php";

	$lessonStatus = $_GET['lessonStatus'];

	// qui suis-je ?
	$query  = "select distinctrow _adm, _IDgrp from user_id, user_session ";
	$query .= "where _IDsess = '$sid' " ;
	$query .= "AND user_id._ID = user_session._ID ";
	$query .= "order by _lastaction ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// vérification des droits
	$query   = "select _IDmod, _IDgrpwr from edt ";
	$query  .= "where _IDedt = '$IDedt' " ;
	$query  .= "limit 1";

	$result  = mysqli_query($mysql_link, $query);
	$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if ( $auth[0] != 255 AND $auth[0] != $row[0] AND !($row[1] & pow(2, $auth[1] - 1)) )
		exit;

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/edt.php");
	$msg->msg_search  = $keywords_search;
	$msg->msg_replace = $keywords_replace;

	// traitement commande
	if ( $submit ) {
		$query = ( $IDdata )
			? "update edt_data set _IDmat = '$IDmat', _IDclass = '$IDclass', _IDitem = '$IDitem', _ID = '$IDuser', _semaine = '$idweek', _group = '$idgroup', _delais = '$delay' where _IDdata = '$IDdata'"
			: "insert into edt_data values('', '$IDedt', '$IDcentre', '$IDmat', '$IDclass', '$IDitem', '$IDuser', '$idweek', '$idgroup', '$j', '$h', '$delay', 'O')" ;

		mysqli_query($mysql_link, $query);

		// retour à la fénêtre appelante
		print("
			<script type=\"text/javascript\">
			window.opener.location=\"index.php?item=".@$_GET["item"]."&IDedt=$IDedt&IDcentre=$IDcentre&IDitem=$IDitem&IDuser=$IDuser&IDclass=$IDclass\";
			self.close();
			</script>");
		}

	if ( $IDdata ) {
		$query   = "select _IDmat, _IDclass, _delais, _semaine, _IDitem, _group, _ID from edt_data ";
		$query  .= "where _IDdata = '$IDdata' " ;
		$query  .= "limit 1";

		$result  = mysqli_query($mysql_link, $query);
		$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$IDmat   = (int) $row[0];
		$IDclass = (int) $row[1];
		$delay   = $row[2];
		$idweek  = (int) $row[3];
		$IDitem  = (int) $row[4];
		$idgroup = (int) $row[5];
		$IDuser  = (int) $row[6];
	}


	$currentPage = "edt";
	include("mobile_menu.php");

	$query = "SELECT * FROM `edt_data` WHERE `_visible` = 'O' ";
	if ($_SESSION['CnxGrp'] == 1) $query .= "AND `_IDclass` LIKE '%;".$_SESSION['CnxClass'].";%' ";
	else $query .= "AND (`_ID` = '".$_SESSION['CnxID']."' OR `_IDrmpl` = '".$_SESSION['CnxID']."') ";


	// Création de la partie de la date de la requête
	$time_1 = "07:00";
	$time_2 = "23:45";

	$query .= "AND ((`_debut` >= '".$time_1.":00' AND `_debut` <= '".$time_2.":00') ";


	$query .= "AND (`_fin` >= '".$time_1.":00' AND `_fin` <= '".$time_2.":00') ";

	// La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
	// ")"

	// Gestion de la date
	$date_1 = date("d/m/Y");
	$date_2 = date("d/m/Y", strtotime('+1 years'));

	$startx = js2PhpTime($date_1." ".$time_1);
	$endx = js2PhpTime($date_2." ".$time_2);

	$startDay = date("N", $startx) - 1;
	$endDay = date("N", $endx) - 1;


	$query .= "AND ((`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` = '".date("W", $startx)."' AND `_jour` >= '".$startDay."') OR (`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` > '".date("W", $startx)."') OR `_annee` > '".date("Y", $startx)."') ";

	$query .= ") ";

	// Exclusion des matières de types indisponible
	$query .= "AND `_IDmat` in (SELECT `_IDmat` FROM `campus_data` WHERE `_type` != '2') ";

	// On veut voir les cours "Programmés" (que pour les élèves)
	if ($_SESSION['CnxAdm'] != 255 && $_SESSION['CnxGrp'] == 1 ) $query .= "AND (`_etat` = '1' OR `_etat` = '5') ";
	// Pour les admins et profs on ne veux pas voir les cours qui on été refusés
	if (!isset($lessonStatus) || $lessonStatus == '') $query .= "AND _etat != 6 ";


	if ($lessonStatus) {
		switch ($lessonStatus) {
			case 'accepted':
				$query .= "AND `_etat` = 5 ";
				echo '<div class="alert alert-success">Cours validés:</div>';
				break;
			case 'refused':
				$query .= "AND `_etat` = 6 ";
				echo '<div class="alert alert-danger">Cours refusés:</div>';
				break;
			case 'waiting':
				$query .= "AND (`_etat` = 4 || `_etat` = 3) ";
				echo '<div class="alert alert-warning">Cours en attente de validation:</div>';
				break;
		}
		echo '<a href="mobile_edt.php" style="margin-bottom: 10px;" class="btn btn-primary">Supprimer les filtres</a>';


	}


	// ORDER BY et LIMIT
	$query .= "ORDER BY `_annee`, `_nosemaine`, `_jour`, `_debut` ASC ";
	$query .= "LIMIT 100 ";

	// echo $query;

	$result = mysqli_query($mysql_link, $query);

	if (mysqli_num_rows($result) == 0)
	{
		echo '<div class="alert alert-warning" style="margin: 10px;">Aucun évènement à venir.';
		if ($_SESSION['CnxAdm'] == 255) echo '<br>Pour voir les emplois du temps d\'autres intervenants connectez vous depuis un ordinateur';
		echo '</div>';
	}
	elseif ($_SESSION['CnxGrp'] > 1 && getParam('mobile_edt_absence_gestion'))
	{
		echo '<div class="alert alert-warning" style="margin: 10px;"><b>Gérer les absences</b>';
			echo '<br>Cliquez sur un cours pour en modifier les absences';
		echo '</div>';
	}

		echo "<table class=\"table table-bordered table-striped\">";

		$currentDateToShow = "";
		$currentYearToShow = date("Y");

		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

			$listeClassesToShow = "";
			$listeClassesAlreadyShown = ";";
			$listeClasses = $row[4];
			$listeClasses = explode(";", $listeClasses);
			foreach ($listeClasses as $key => $value) {
				if ($value != 0 and strpos($listeClassesAlreadyShown, $value) === false)
				{
					$listeClassesAlreadyShown .= $value.";";
					if ($listeClassesToShow != "") $listeClassesToShow .= "<br>";
					$listeClassesToShow .= getClassNameByClassID($value);
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

			if ($ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
			else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));

			// if ($row[14] == 6) $alert_background_color = "style=\"background-color: #f2dede !important; border: 1px solid #eed3d7 !important; color: #b94a48 !important; text-align: center; vertical-align: middle; font-size: 20px;\"";
			if ($row[14] == 6) $alert_background_color = "class=\"refusedLesson\"";
			elseif ($row[14] == 5 or $row[14] == 1) $alert_background_color = "class=\"acceptedLesson\"";
			elseif ($row[14] == 4) $alert_background_color = "class=\"askLesson\"";
			else $alert_background_color = "";

			if ($row[14] == 5 or $row[14] == 1) $etat_symbol = "<i class=\"fa fa-check\" aria-hidden=\"true\"></i>";
			if ($row[14] == 6) $etat_symbol = "<i class=\"fa fa-times\" aria-hidden=\"true\"></i>";
			if ($row[14] == 4) $etat_symbol = "<i class=\"fa fa-question\" aria-hidden=\"true\"></i>";



			// Traitements convertion date time
			if ($row[13] < 10) $week_temp = "0".$row[13];
			else $week_temp = $row[13];
			$date_debut = php2MySqlTime(strtotime($row[16].'W'.$week_temp));


			$date_event = date($date_debut); // objet date
			$date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row[9]." day"); // ajout du nb de jours
			$currentDate = date("d/m/Y", $date_event); // timesptamp en string

			$dayNumber 			= date('N', $date_event);
			$currentYear 		= date("Y", $date_event);
			$dayNumberDate 	= date('d', $date_event);

			if ($currentDateToShow != $currentDate)
			{
				$currentDateToShow = $currentDate;

				if ($currentYearToShow != $currentYear)
				{
					$currentYearToShow = $currentYear;
					echo "<tr style=\"color: #155724; background-color: #d4edda; border-color: #c3e6cb;
					\">";
						echo "<td colspan=\"4\">";
						echo date('Y', $date);
						echo "</td>";
					echo "</tr>";
				}



				echo "<tr style=\"color: #004085; background-color: #cce5ff; border-color: #b8daff;\">";
					echo "<td colspan=\"4\">";
					echo getDayNameByDayNumber($row[9])." ".$dayNumberDate." ".getMonthNameByMonthNumber(date('m', $date));
					echo "</td>";
				echo "</tr>";
			}


			// Etats:
				// Validé: 5 - 1
				// Refusé: 6
				// En attente: 3 - 4

				if (($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] > 1)) {
					if (getParam('mobile_edt_cours_gestion')) {
						switch ($row[14]) {
							case 1:
							case 2:
								if (getParam('mobile_edt_absence_gestion')) echo '<tr id="row_'.$row[15].'" onclick="document.location = \'mobile_ctn_add.php?IDx='.$row[15].'\';">';
								else echo '<tr id="row_'.$row[15].'">';
								break;
							case 6:
								echo '<tr id="row_'.$row[15].'" class="refusedLesson">';
								break;
							case 3:
							case 4:
								echo '<tr id="row_'.$row[15].'" class="askLesson">';
								break;

							default:
								echo '<tr id="row_'.$row[15].'">';
								break;
						}
					}
					elseif (getParam('mobile_edt_absence_gestion')) echo '<tr id="row_'.$row[15].'" onclick="document.location = \'mobile_ctn_add.php?IDx='.$row[15].'\';">';
					else echo '<tr id="row_'.$row[15].'">';
				}
				else echo '<tr id="row_'.$row[15].'">';



				// Colonne Horaire
				$horaire = $heure_debut."h".$minutes_debut."<br>".$heure_fin."h".$minutes_fin;
				echo "<td><b>".$horaire."</b></td>";

				// Colonne Pôle et matière
				$pole_mat = $poleName."<br>".$matiereToShow;
				echo "<td>".$pole_mat."</td>";

				// Colonne classe
				if ($_SESSION['CnxGrp'] != 1) $classe_intervenant = $listeClassesToShow.'<br>'.getRoomNameByID($row[5]);
				// Colonne intervenant
				else $classe_intervenant = getUserNameByID($row[6]).getSecondTeacherNameByEventId($row[15]).'<br>'.getRoomNameByID($row[5]);
				echo '<td>'.$classe_intervenant.'</td>';

				// Colonne salle
				// echo "<td>".getRoomNameByID($row[5])."</td>";


				// Etats:
					// Validé: 5 - 1
					// Refusé: 6
					// En attente: 3 - 4


				// // On ne peux accéder au absences que si un cours est validé
				// switch ($row[14]) {
				// 	case 1:
				// 	case 5:
				// 	echo '<td></td>';
				// 		break;
				// 	default:
				// 		echo '<td>';
				// 				echo '<button class="btn btn-secondary" data-toggle="modal" id="button_info_'.$row[15].'" data-target="#modal_info_'.$row[15].'" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
				// 					echo '<i class="fa fa-ellipsis-h"></i>';
				// 				echo '</button>';
				// 				echo '</div>';
				// 		echo '</td>';
				//
				//
				//
				// 		echo '<!-- Modal -->';
				// 		echo '<div class="modal fade" id="modal_info_'.$row[15].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
				// 		  echo '<div class="modal-dialog modal-dialog-centered" role="document">';
				// 		    echo '<div class="modal-content">';
				// 		      echo '<div class="modal-header">';
				// 		        echo '<h5 class="modal-title" id="exampleModalLabel">Actions</h5>';
				// 		        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
				// 		          echo '<span aria-hidden="true">&times;</span>';
				// 		        echo '</button>';
				// 		      echo '</div>';
				// 		      echo '<div class="modal-body">';
				// 						echo '<b>Horaires: </b>'.str_replace('<br>', ' - ', $horaire);
				// 						echo '<br><b>'.$poleName.'</b> - '.$matiereToShow;
				// 						echo '<br><b>Classes: </b>'.$listeClassesToShow;
				// 						echo '<br><b>Salle: </b>'.getRoomNameByID($row[5]);
				// 						echo '<hr>';
				// 						echo '<div style="width: 100%; text-align: center;">';
				// 							echo '<a class="btn btn-success acceptCours" event_id="'.$row[15].'" href="#">Accepter le cours</a>';
				// 							echo '<hr><a class="btn btn-warning refuseCours" event_id="'.$row[15].'" href="#">Refuser le cours</a>';
				// 							if ($_SESSION['CnxAdm'] == 255)
				// 								echo '<hr><a class="btn btn-danger removeCours" event_id="'.$row[15].'" href="#">Supprimer le cours</a>';
				// 						echo '</div>';
				// 		      echo '</div>';
				// 		    echo '</div>';
				// 		  echo '</div>';
				// 		echo '</div>';
				// 		break;
				// }

				if (($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] > 1) && getParam('mobile_edt_cours_gestion')) {
					echo '<td>';
							echo '<button class="btn btn-secondary" data-toggle="modal" id="button_info_'.$row[15].'" data-target="#modal_info_'.$row[15].'" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
								echo '<i class="fa fa-ellipsis-h"></i>';
							echo '</button>';
							echo '</div>';
					echo '</td>';

					echo '<!-- Modal -->';
					echo '<div class="modal fade" id="modal_info_'.$row[15].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
						echo '<div class="modal-dialog modal-dialog-centered" role="document">';
							echo '<div class="modal-content">';
								echo '<div class="modal-header">';
									echo '<h5 class="modal-title" id="exampleModalLabel">Actions</h5>';
									echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
										echo '<span aria-hidden="true">&times;</span>';
									echo '</button>';
								echo '</div>';
								echo '<div class="modal-body">';
									echo '<b>Horaires: </b>'.str_replace('<br>', ' - ', $horaire);
									echo '<br><b>'.$poleName.'</b> - '.$matiereToShow;
									echo '<br><b>Classes: </b>'.$listeClassesToShow;
									echo '<br><b>Salle: </b>'.getRoomNameByID($row[5]);
									echo '<hr>';

									echo '<div style="width: 100%; text-align: center;">';
										if ($row[14] != 1 && $row[14] != 5) {
											echo '<a class="btn btn-success acceptCours" event_id="'.$row[15].'" href="#">Accepter le cours</a>';
											echo '<hr><a class="btn btn-warning refuseCours" event_id="'.$row[15].'" href="#">Refuser le cours</a>';
											if ($_SESSION['CnxAdm'] == 255) echo '<hr>';
										}
										if ($_SESSION['CnxAdm'] == 255)
											echo '<a class="btn btn-danger removeCours" event_id="'.$row[15].'" href="#">Supprimer le cours</a>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				}




			echo "</tr>";
			$i++;
		}
		echo "</table>";
?>


<script>

window.onload = function() {
	$('.acceptCours').click(function(){
		var eventid = $(this).attr('event_id');
		acceptCours(eventid);
	});

	$('.refuseCours').click(function(){
		var eventid = $(this).attr('event_id');
		refuseCours(eventid);
	});

	$('.removeCours').click(function(){
		var eventid = $(this).attr('event_id');
		removeCours(eventid);
	});
}


function acceptCours(id_event) {
  $.ajax({
    url : 'include/fonction/ajax/edt.php?action=acceptCours',
    type : 'POST', // Le type de la requête HTTP, ici devenu POST
    data : 'id_event=' + id_event,
    dataType : 'html', // On désire recevoir du HTML
    success : function(code_html, statut){ // code_html contient le HTML renvoyé
			if (code_html == 'success') {
				$('#modal_info_' + id_event).modal('hide');
				$('#button_info_' + id_event).before('<button class="btn btn-success"><i class="fa fa-check"></i></button>').remove();
			}

    }
  });
}


function refuseCours(id_event) {
  $.ajax({
    url : 'include/fonction/ajax/edt.php?action=refuseCours',
    type : 'POST', // Le type de la requête HTTP, ici devenu POST
    data : 'id_event=' + id_event,
    dataType : 'html', // On désire recevoir du HTML
    success : function(code_html, statut){ // code_html contient le HTML renvoyé
			if (code_html == 'success') {
				$('#row_' + id_event).hide();
				$('#modal_info_' + id_event).modal('hide');
			}
    }
  });
}

function removeCours(id_event) {
  $.ajax({
    url : 'include/fonction/ajax/edt.php?action=removeCours',
    type : 'POST', // Le type de la requête HTTP, ici devenu POST
    data : 'id_event=' + id_event,
    dataType : 'html', // On désire recevoir du HTML
    success : function(code_html, statut){ // code_html contient le HTML renvoyé
			if (code_html == 'success') {
				$('#row_' + id_event).hide();
				$('#modal_info_' + id_event).modal('hide');
			}
    }
  });
}
</script>

<?php


	if ($lessonStatus == 'accepted') {
		$sql  = "UPDATE `edt_data` SET `_etat` = '1' WHERE `_etat` = '5' ";
		if(mysqli_query($mysql_link, $sql)==false)
		{
			// Do nothing
		}
	}

?>

<style>
	.table td, .table th {
		padding: 5px;
	}

	<?php if ((!isset($lessonStatus) || $lessonStatus == '') && getParam('mobile_edt_cours_gestion')) { ?>
		.askLesson {
		  background-color: #fcf8e3 !important;
		  border: 1px solid #fbeed5 !important;
		  color: #c09853 !important;
		}
		.acceptedLesson {
		  background-color: #dff0d8 !important;
		  border: 1px solid #d6e9c6 !important;
		  color: #468847 !important;
		}
		.refusedLesson {
		  background-color: #f2dede !important;
		  border: 1px solid #eed3d7 !important;
		  color: #b94a48 !important;
		}
	<?php } ?>

	.dropdownButton_cours {
		font-size: 20px;
	}
</style>

<?php include("mobile_footer.php"); ?>
