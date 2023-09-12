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
 *		module   : mobile_ctn_add.php
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 16/03/2020
 *		modif    :
 */

	include("mobile_banner.php");

	require "msg/edt.php";
	require_once "include/TMessage.php";

	require "php/functions.php";

	require_once "include/calendar_tools.php";

  if (isset($_GET['IDx'])) $IDx = addslashes($_GET['IDx']);
  else $IDx = '';


	$currentPage = "ctn";
	include("mobile_menu.php");

  if ($_SESSION['CnxGrp'] <= 1) exit(0);

	// On crée la date
  $query  = "SELECT _jour, _nosemaine, _annee, _debut, _fin, _IDclass FROM edt_data WHERE ";
  $query .= "_IDx = '".$IDx."' ";
	$classes_ID = array();
  $result = mysql_query($query, $mysql_link);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    // Traitements convertion date time
    if ($row[1] < 10) $week_temp = "0".$row[1];
    else $week_temp = $row[1];
    $date_debut = php2MySqlTime(strtotime($row[2].'W'.$week_temp));
    $date_event = date($date_debut); // objet date
    $date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row[0]." day"); // ajout du nb de jours
    $date_event = date("Y-m-d", $date_event); // timesptamp en string
    $st = substr($row[3], 0, 5);
    $et = substr($row[4], 0, 5);
    $stf = $row[3];
    $edf = $row[4];
    $temp = explode(';', $row[5]);
    foreach ($temp as $key => $value) {
      if ($value != '') $classes_ID[$value] = array();
    }
  }

	// On récupère les données du formulaire
	$liste_abs_to_insert = array();
	if ($_POST['submit'] == "yes")
	{
		foreach ($_POST['abs'] as $key_abs => $value_abs) {
			if ($value_abs == 'on') $liste_abs_to_insert[$key_abs] = $_POST['raison'][$key_abs];
			elseif ($value_abs == 'off') $liste_abs_to_remove[$key_abs] = '';
		}

		// On enregistre les absences
		$values_to_insert = "values ";
		foreach ($liste_abs_to_insert as $key => $value) {
			$query = "SELECT * FROM absent_items WHERE _start <= '".$date_event." ".$st.":00' AND _end >= '".$date_event." ".$et.":00' AND _IDabs = '".$key."' LIMIT 1 ";
			// echo $query.'<br>';
			$result = mysql_query($query, $mysql_link);
			if (!mysql_num_rows($result)) $values_to_insert .= "('', '".$value."', '-1', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', NOW(), '1', '".$key."', '".$date_event." ".$st.":00', '".$date_event." ".$et.":00', '', '', 'O', 'N', '0', '', '', '0', '', 'O', 'N', ''), ";
			else
			{
				$query = "UPDATE absent_items SET _IDdata = '".$value."' WHERE _start <= '".$date_event." ".$st.":00' AND _end >= '".$date_event." ".$et.":00' AND _IDabs = '".$key."' ";
				mysql_query($query, $mysql_link);
			}
		}
		$values_to_insert = substr($values_to_insert, 0, -2);
		$query  = 'INSERT INTO absent_items ';
		$query .= $values_to_insert;
		mysql_query($query, $mysql_link);

		// On supprime les absences qui on été décochés
		foreach ($liste_abs_to_remove as $key => $value) {
			$query = "SELECT * FROM absent_items WHERE _start <= '".$date_event." ".$st.":00' AND _end >= '".$date_event." ".$et.":00' AND _IDabs = '".$key."' LIMIT 1 ";
			$result = mysql_query($query, $mysql_link);
			if (mysql_num_rows($result))
			{
				$query_delete = "DELETE FROM absent_items WHERE _start <= '".$date_event." ".$st.":00' AND _end >= '".$date_event." ".$et.":00' AND _IDabs = '".$key."' ";
				mysql_query($query_delete, $mysql_link);
			}
		}
	}






	// on récupère les élèves des différentes classes
  foreach ($classes_ID as $key => $value) {
    $query = "SELECT _ID, _fname, _name from user_id WHERE _IDclass = '".$key."' AND _adm = 1 ";
    $result = mysql_query($query, $mysql_link);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $classes_ID[$key][$row[0]] = $row[1].' '.$row[2];
    }
  }

	// On récupère les différentes raisons d'absence possible
	$absences_raisons = array();
	$query = "SELECT _IDdata, _texte FROM absent_data WHERE _lang = 'fr' AND _visible = 'O' ";
	$result = mysql_query($query, $mysql_link);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	  $absences_raisons[$row[0]] = $row[1];
	}


	$absences_previsio = array();

	echo '<br>';
	echo '<form action="#" method="POST">';

		echo '<div class="alert alert-warning" style="margin: 10px;"><b>N\'oubliez pas d\'enregistrer vos modifications</b>';
			echo '<p style="text-align: right; margin-top: 10px;"><button type="submit" class="btn btn-success" style="width: 100%;">Enregistrer</button></p>';
		echo '</div>';

		if ($_POST['submit'] == "yes")
		{
			echo '<div class="alert alert-success" style="margin: 10px;"><b><i class="fa fa-warning"></i>&nbsp;Modifications enregistrées</b>';
			echo '</div>';
		}

		echo '<input type="hidden" name="submit" value="yes">';

		echo '<table class="table table-bordered table-striped">';
			echo '<tr>';
				echo '<th style="width: 1%;">Absent&nbsp;?</th>';
				echo '<th>Élève</th>';
				echo '<th>Raison</th>';
			echo '</tr>';

		foreach ($classes_ID as $IDclass => $student_list) {

			echo '<tr style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; text-align: center; font-weight: bold;">';
				echo '<td colspan="3">';
					echo getClassNameByClassID($IDclass);
				echo "</td>";
			echo "</tr>";
		  foreach ($student_list as $studentID => $studentName) {
		    $query  = "select distinctrow ";
		    $query .= "absent_items._IDdata, absent_items._valid ";
		    $query .= "from absent_items ";
		    $query .= "where (absent_items._start <= '$date_event $stf' AND absent_items._end >= '$date_event $etf') ";
		    $query .= "AND absent_items._IDabs = '$studentID' ";
		    $result = mysql_query($query, $mysql_link);
		    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
					// $absences_previsio: IDeleve -> IDtypeAbs - Est validé ?
		      $absences_previsio[$studentID] = array('id_raison' => $row[0], 'justif' => $row[1]);
		    }

		    echo '<tr>';
					// Boite à cocher
					echo '<td style="text-align: center;">';
						if (is_array($absences_previsio[$studentID])) $checked = 'checked';
						else $checked = '';
						echo '<input type="hidden" name="abs['.$studentID.']" value="off">';
						echo '<input type="checkbox" name="abs['.$studentID.']" '.$checked.'>';
					echo '</td>';

					// Nom de 'étudiant'
					echo '<td>'.$studentName.'</td>';

					// Raison de l'absence
					echo '<td>';
						echo '<select name="raison['.$studentID.']" style="width: 100%;">';
							foreach ($absences_raisons as $key_abs => $value_abs) {
								if (!is_array($absences_previsio[$studentID]) && $key_abs == 2) $selected = 'selected';
								elseif (is_array($absences_previsio[$studentID]) && $absences_previsio[$studentID]['id_raison'] == $key_abs) $selected = 'selected';
								else $selected = '';
								echo '<option value="'.$key_abs.'" '.$selected.'>'.$value_abs.'</option>';
							}
						echo '</select>';
					echo '</td>';
		    echo '</tr>';
		  }
		}
		echo '</table>';
	echo '</form>';



	include("mobile_footer.php");

?>
