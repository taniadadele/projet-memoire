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
 *		module   : user_planning.php
 *		projet   : la page de listing des heures réalisés par un formateur
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 01/10/2019
 *		modif    :
 */

  if (isset($_POST['userID'])) $userID = addslashes($_POST['userID']);
  elseif (isset($_GET['userID'])) $userID = addslashes($_GET['userID']);
  else $userID = 0;

  if (isset($_POST['classID'])) $classID = addslashes($_POST['classID']);
  elseif (isset($_GET['classID'])) $classID = addslashes($_GET['classID']);
  else $classID = 0;


  if (isset($_POST['year'])) $year = addslashes($_POST['year']);
  elseif (isset($_GET['year'])) $year = addslashes($_GET['year']);
  else $year = getParam('START_Y');

  // Si l'année sélectionné n'est pas l'année actuelle
  if ($year == getParam('START_Y'))
  {
    $period_list = json_decode(getParam('periodeList'), true);
    $period_date_list = json_decode(getParam('periodeDates'), true);
  }
  else
  {
    $query = "SELECT _valeur FROM `parametre` WHERE _code = 'periodeList' AND _annee = '".$year."' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $period_list = json_decode($row[0], true);
    }
    $query = "SELECT _valeur FROM `parametre` WHERE _code = 'periodeDates' AND _annee = '".$year."' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $period_date_list = json_decode($row[0], true);
    }
  }

  // ---------------------------------------------------------------------------
  // Récupération des heures réelles
  // ---------------------------------------------------------------------------
  $list_hours = Array();
  foreach ($period_list as $key => $value) {
    $temp_dates = array();
    $temp_dates[$key]['start']['date'] = str_replace('-', '', $period_date_list[$key."_start"]);
    $temp_dates[$key]['start']['timestamp'] = strtotime($temp_dates[$key]['start']['date']);
    $temp_dates[$key]['end']['date'] = str_replace('-', '', $period_date_list[$key."_end"]);
    $temp_dates[$key]['end']['timestamp'] = strtotime($temp_dates[$key]['end']['date']);

    // Pour chaque périodes:
    $start_week = date('W', $temp_dates[$key]['start']['timestamp']);
    $start_month = date('m', $temp_dates[$key]['start']['timestamp']);
    $end_month = date('m', $temp_dates[$key]['end']['timestamp']);
    $end_week = date('W', $temp_dates[$key]['end']['timestamp']);

    // Si on est dans la dernière semaine de l'année et que php nous donne comme semaine actuelle 01, alors on récupère la dernière semaine + 1
    if ($end_month == 12 && $end_week == 01)
    {
      $end_week = date('W', strtotime($temp_dates[$key]['end']['date']. ' previous week'));
      $end_week = $end_week + 1;
    }

    if ($start_month == 12 && $start_week == 01)
    {
      $start_week = date('W', strtotime($temp_dates[$key]['start']['date']. ' previous week'));
      $start_week = $start_week + 1;
    }

    // Si on est en janvier mais que la semaine affichée est la semaine 53 alors on met a la semaine 1
    if ($end_month == 1 && $end_week == 53) $end_week = 01;
    if ($start_month == 1 && $start_week == 53) $start_week = 01;



    $start_year = date('Y', $temp_dates[$key]['start']['timestamp']);
    $end_year = date('Y', $temp_dates[$key]['end']['timestamp']);
    $start_day = date('N', $temp_dates[$key]['start']['timestamp']) - 1;
    $end_day = date('N', $temp_dates[$key]['end']['timestamp']) - 1;

    $query  = "SELECT SUBTIME(_fin, _debut), _ID_pma, _IDclass FROM edt_data WHERE _visible = 'O' ";
    $query .= "AND (_annee > '".$start_year."' OR (_annee = '".$start_year."' AND (_nosemaine > '".$start_week."' OR (_nosemaine = '".$start_week."' AND _jour >= '".$start_day."')))) ";
    $query .= "AND (_annee < '".$end_year."' OR (_annee = '".$end_year."' AND (_nosemaine < '".$end_week."' OR (_nosemaine = '".$end_week."' AND _jour <= '".$end_day."')))) ";

    if ($classID == 0)
    {
      if ($userID != 0) $query .= "AND (_ID = '".$userID."' OR _IDrmpl = '".$userID."') ";
    }
    else $query .= "AND _IDclass LIKE '%;".$classID.";%' ";

    $query .= "AND _ID_pma != 0 ";
    $query .= "AND (_etat = 1 OR _etat = 5) ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      if (isset($list_hours[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key]))
      {
        $old = $list_hours[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key];
        $temp_2 = explode(':', $row[0]);
        $temp = date('H:i:s', strtotime('1970-01-01 '.$old.' + '.$temp_2[0].' hours + '.$temp_2[1].' minutes'));

        // Calcul du nombre d'heures
        $temp_3 = explode(':', $old);

        if ($temp_2[0] == 0 && $temp_2[1] == 0) $time = 0;
        else $time = ($temp_2[0] * 60) + $temp_2[1];
        $time = $time + ($temp_3[0] * 60) + $temp_3[1];

        $total_hours = floor($time / 60);
        $total_minutes = $time % 60;
        $time = $total_hours.':'.$total_minutes.':00';

        $list_hours[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key] = $time;
      }
      else
      {
        $list_hours[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key] = $row[0];
      }
    }
    // $list_hours_theo[Numéro d'année][PMA][Période] = Nombre d'heures
  }
  // ---------------------------------------------------------------------------


  // ---------------------------------------------------------------------------
  // Récupération des heures réelles + heures en attente
  // ---------------------------------------------------------------------------
  $list_hours_pending = Array();
  foreach ($period_list as $key => $value) {
    $temp_dates = array();
    $temp_dates[$key]['start']['date'] = str_replace('-', '', $period_date_list[$key."_start"]);
    $temp_dates[$key]['start']['timestamp'] = strtotime($temp_dates[$key]['start']['date']);
    $temp_dates[$key]['end']['date'] = str_replace('-', '', $period_date_list[$key."_end"]);
    $temp_dates[$key]['end']['timestamp'] = strtotime($temp_dates[$key]['end']['date']);

    // Pour chaque périodes:
    $start_week = date('W', $temp_dates[$key]['start']['timestamp']);
    $start_month = date('m', $temp_dates[$key]['start']['timestamp']);
    $end_month = date('m', $temp_dates[$key]['end']['timestamp']);
    $end_week = date('W', $temp_dates[$key]['end']['timestamp']);

    // Si on est dans la dernière semaine de l'année et que php nous donne comme semaine actuelle 01, alors on récupère la dernière semaine + 1
    if ($end_month == 12 && $end_week == 01)
    {
      $end_week = date('W', strtotime($temp_dates[$key]['end']['date']. ' previous week'));
      $end_week = $end_week + 1;
    }

    if ($start_month == 12 && $start_week == 01)
    {
      $start_week = date('W', strtotime($temp_dates[$key]['start']['date']. ' previous week'));
      $start_week = $start_week + 1;
    }

    // Si on est en janvier mais que la semaine affichée est la semaine 53 alors on met a la semaine 1
    if ($end_month == 1 && $end_week == 53) $end_week = 01;
    if ($start_month == 1 && $start_week == 53) $start_week = 01;


    $start_year = date('Y', $temp_dates[$key]['start']['timestamp']);
    $end_year = date('Y', $temp_dates[$key]['end']['timestamp']);
    $start_day = date('N', $temp_dates[$key]['start']['timestamp']) - 1;
    $end_day = date('N', $temp_dates[$key]['end']['timestamp']) - 1;

    $query  = "SELECT SUBTIME(_fin, _debut), _ID_pma, _IDclass FROM edt_data WHERE _visible = 'O' ";
    $query .= "AND (_annee > '".$start_year."' OR (_annee = '".$start_year."' AND (_nosemaine > '".$start_week."' OR (_nosemaine = '".$start_week."' AND _jour >= '".$start_day."')))) ";
    $query .= "AND (_annee < '".$end_year."' OR (_annee = '".$end_year."' AND (_nosemaine < '".$end_week."' OR (_nosemaine = '".$end_week."' AND _jour <= '".$end_day."')))) ";

    if ($classID == 0)
    {
      if ($userID != 0) $query .= "AND (_ID = '".$userID."' OR _IDrmpl = '".$userID."') ";
    }
    else $query .= "AND _IDclass LIKE '%;".$classID.";%' ";
    $query .= "AND _ID_pma != 0 ";
    $query .= "AND (_etat = 1 OR _etat = 5 OR _etat = 3 OR _etat = 4) ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      if (isset($list_hours_pending[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key]))
      {
        $old = $list_hours_pending[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key];
        $temp_2 = explode(':', $row[0]);
        // Calcul du nombre d'heures
        $temp_3 = explode(':', $old);

        if ($temp_2[0] == 0 && $temp_2[1] == 0) $time = 0;
        else $time = ($temp_2[0] * 60) + $temp_2[1];
        $time = $time + ($temp_3[0] * 60) + $temp_3[1];

        $total_hours = floor($time / 60);
        $total_minutes = $time % 60;
        $time = $total_hours.':'.$total_minutes.':00';

        $list_hours_pending[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key] = $time;
      }
      else
      {
        $list_hours_pending[substr(getClassYearByPMAID($row[1]), 0, 1)][$row[1]][$key] = $row[0];
      }
    }
    // $list_hours_theo[Numéro d'année][PMA][Période] = Nombre d'heures
  }
  // ---------------------------------------------------------------------------


  // ---------------------------------------------------------------------------
  // Récupération des heures théoriques
  // ---------------------------------------------------------------------------
  $list_hours_theo = Array();
  if ($year == getParam('START_Y')) $query  = "SELECT syl._IDPMA, syl._periode_1, syl._periode_2, syl._periode_total FROM campus_syllabus syl, pole_mat_annee pma INNER JOIN `pole` ON pma._ID_pole = pole._ID INNER JOIN campus_data mat ON pma._ID_matiere = mat._IDmat WHERE pma._ID_pma = syl._IDPMA ";
  else $query  = "SELECT syl._IDPMA, syl._periode_1, syl._periode_2, syl._periode_total FROM campus_syllabus_archive syl, pole_mat_annee pma INNER JOIN `pole` ON pma._ID_pole = pole._ID INNER JOIN campus_data mat ON pma._ID_matiere = mat._IDmat WHERE pma._ID_pma = syl._IDPMA AND _year = '".$year."' ";

  if ($classID == 0)
  {
    if ($userID != 0) $query .= "AND syl._idUser LIKE '%;".$userID.";%' ";
  }
  else $query .= "AND pma._ID_year = '".getClassNiveauByClassID($classID)."' ";

  $query .= "ORDER BY pole._name ASC, mat._IDmat ASC ";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $list_hours_theo[substr(getClassYearByPMAID($row[0]), 0, 1)][$row[0]][1] = $row[1];
    $list_hours_theo[substr(getClassYearByPMAID($row[0]), 0, 1)][$row[0]][2] = $row[2];
    // Période totale
    $list_hours_theo[substr(getClassYearByPMAID($row[0]), 0, 1)][$row[0]][0] = $row[3];
  }
  ksort($list_hours_theo);
  // ---------------------------------------------------------------------------
?>

<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<strong>Liste des heures de cours</strong>
	</div>
</div>

<div class="maincontent">
  <div style="text-align: right;">
    <form action="?item=16" id="formulaire" method="post">


      <select name="classID" onchange="document.forms.formulaire.submit()">
        <option value="0">Toutes les promos</option>
        <?php
          $query = "SELECT _IDclass FROM campus_classe WHERE _visible = 'O' ";
          $result = mysql_query($query);
          while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
            if ($row[0] == $classID) $selected = "selected";
            else $selected = "";
            echo "<option value=\"".$row[0]."\" ".$selected.">".getClassNameByClassID($row[0])."</option>";
          }
        ?>
      </select>


      <strong>&nbsp;OU&nbsp;</strong>

      <select name="userID" onchange="document.forms.formulaire.submit()">
        <?php
          echo "<option value=\"0\">Tous les intervenants</option>";
          if ($classID == 0 || $classID == "")
          {
            $list_profs = array();
            if ($year == getParam('START_Y')) $query = "SELECT DISTINCT _idUser FROM campus_syllabus WHERE 1 ";
            else $query = "SELECT DISTINCT _IDprof FROM campus_syllabus_archive WHERE _year = '".$year."' ";
            $result = mysql_query($query);
            $alreadyShown = ";";
            if ($userID != 0)
            {
              echo "<option value=\"".$userID."\" selected>".getUserNameByID($userID)."</option>";
              $alreadyShown .= $userID.';';
            }
            while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
              $listProf = explode(';', $row[0]);
              foreach ($listProf as $key => $value) {
                if (strpos($alreadyShown, ';'.$value.';') === false)
                {
                  if ($userID == $value) $selected = "selected";
                  else $selected = "";
                  if ($value != "" && getUserNameByID($value) != "") $list_profs[$value] = getUserNameByID($value);
                  $alreadyShown .= $value.";";
                }
              }
            }
            asort($list_profs);

            foreach ($list_profs as $key => $value) {
              if ($userID == $key) $selected = "selected";
              else $selected = "";
              echo "<option value=\"".$key."\" ".$selected.">".$value."</option>";
            }

          }

        ?>

      </select>

      <select name="year" onchange="document.forms.formulaire.submit()">
        <?php
          $query = "SELECT DISTINCT _year FROM campus_syllabus_archive WHERE 1 ORDER BY _year ASC ";
          $result = mysql_query($query);
          while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
            if ($row[0] == $year) $selected = "selected";
            else $selected = "";
            echo "<option value=\"".$row[0]."\" ".$selected.">".$row[0]." - ".($row[0] + 1)."</option>";
          }
          $current_year = getParam('START_Y');
          if ($current_year == $year) $selected = "selected";
          else $selected = "";
        ?>

        <option value="<?php echo $current_year; ?>" <?php echo $selected; ?>><?php echo $current_year." - ".($current_year + 1); ?></option>
      </select>
      <a href="user_planning_pdf.php?year=<?php echo $year; ?>&userID=<?php echo $userID; ?>&classID=<?php echo $classID; ?>" target="_blank" class="btn btn-default" style="margin-bottom: 10px;"><i class="fa fa-print"></i></a>
    </form>


  </div>

  <table style="width: 100%;" class="table table-striped">
    <tr>
      <th rowspan="2"></th>
      <?php
        if (!getParam('showOnlyTotalPeriodGeneral')) {
          foreach ($period_list as $key => $value) {
            echo "<th style=\"text-align: center;\" colspan=\"3\">".$value."</th>";
          }
        }

        echo "<th style=\"text-align: center;\" colspan=\"3\">Total</th>";
      ?>
    </tr>
    <tr>
      <?php
        if (!getParam('showOnlyTotalPeriodGeneral')) {
          foreach ($period_list as $key => $value) {
            echo "<th style=\"text-align: center;\">Théorique</th>";
            echo "<th style=\"text-align: center;\">Programmé</th>";
            echo "<th style=\"text-align: center;\" class=\"end_period\">Programmé + en attente</th>";
          }
        }

        echo "<th style=\"text-align: center;\">Théorique</th>";
        echo "<th style=\"text-align: center;\">Programmé</th>";
        echo "<th style=\"text-align: center;\">Programmé + en attente</th>";
      ?>
    </tr>
    <?php
      // Pour chaque année
      // foreach ($list_hours as $key => $value) {
      foreach ($list_hours_theo as $key => $value) {
        if ($key == 0) continue;
        echo "<tr>";
          echo "<th colspan=\"10\" style=\"text-align: center;\">".getNiveauNameByNiveauNumber($key)."</th>";
        echo "</tr>";
        // Pour chaque matière
        foreach ($value as $key_1 => $value_1_1) {
          unset($value_1);
          $value_1 = $list_hours[$key][$key_1];

          $poleID   = getPoleIDByPMAID($key_1);
          $poleName = getPoleNameByIdPole($poleID);
          $matID    = getMatIDByPMAID($key_1);
          $matName  = getMatNameByIdMat($matID);

          echo "<tr>";
            echo "<th>";
              echo $poleName." - ".$matName;
              if ($userID == 0 || $userID == "")
              {
                $prof_list = getPMASyllabusProfNameList($key_1);
                foreach ($prof_list as $key_2 => $value_2) {
                  echo "&nbsp;<span class=\"badge\">".$value_2."</span>";
                }
              }
            echo "</th>";
            // -----------------------------------------------------------------
            // Calcul et création des heures des différentes périodes
            // -----------------------------------------------------------------
            foreach ($period_list as $i => $value_void) {
              // ---------------------------------------------------------------
              // On crée les noms de variables pour les deux périodes
              // ---------------------------------------------------------------
              $heure_theo = 'heure_theo_'.$i;
              $heure_theo_min = 'heure_theo_'.$i.'_min';
              $heure_prog = 'heure_prog_'.$i;
              $heure_prog_min = 'heure_prog_'.$i.'_min';
              $heure_pending = 'heure_attente_'.$i;
              $$heure_pending_min = 'heure_attente_'.$i.'_min';
              $pending_color = 'color_pending_'.$i;
              $color = 'color_'.$i;

              // ---------------------------------------------------------------
              // On récupère l'heure théorque de la première période
              // ---------------------------------------------------------------
              $$heure_theo = $list_hours_theo[$key][$key_1][$i];
              // Si l'heure = 0 alors on affiche 00:00
              if ($$heure_theo == '00') $$heure_theo = '00:00';
              $temp = explode(':', $$heure_theo);
              // Si les minutes sont = à 0 alors on met 00
              if ($temp[1] == 0) $$heure_theo = $temp[0].':00';
              // Si les heures sont < à 10 alors on met un 0 devant
              if ($temp[0] < 10 && strlen($temp[0]) < 2) $$heure_theo = '0'.$$heure_theo;
              // On calcul le nombre de minutes à partir des heures et minutes
              $$heure_theo_min = ($temp[0] * 60) + $temp[1];

              // ---------------------------------------------------------------
              // On calcule le nombre d'heures programmés de la première période
              // ---------------------------------------------------------------
              $temp_2 = explode(':', $value_1[$i]);

              // On calcul le nombre de minutes
              if ($temp_2[0] == 0 && $temp_2[1] == 0) $time = 0;
              else $time = ($temp_2[0] * 60) + $temp_2[1];

              // On récupère le nombre d'heures et de minutes à partir du nombre de minutes
              $total_hours = floor($time / 60);
              $total_minutes = $time % 60;

              // Si le nombre total de minutes est < à 10 alors on met un 0 devant
              if ($total_minutes < 10) $total_minutes = '0'.$total_minutes;
              // On formate le nombre d'heures avec HH:mm
              $$heure_prog = $total_hours.':'.$total_minutes;
              // On stoque le nombre de minutes de la période
              $$heure_prog_min = $time;

              // Si le nombre d'heures théorique est suppérieur au nombre d'heures réelles alors on met en rouge
              if ($$heure_theo_min > $$heure_prog_min) $$color = "red-alert";
              // Si le nombre d'heures théorique est inférieur au nombre d'heures réelles alors on met en vert
              elseif ($$heure_theo_min < $$heure_prog_min) $$color = "green-alert";
              // Sinon on ne met pas de couleurs
              else $$color = "";


              // On récupère le temps de cours en attente
              $$heure_pending = $list_hours_pending[$key][$key_1][$i];
              $temp = explode(':', $$heure_pending);
              // On calcul le nombre de minutes des cours en attente
              if ($temp[0] == 0 && $temp[1] == 0) $$heure_pending_min = 0;
              else $$heure_pending_min = ($temp[0] * 60) + $temp[1];
              // On récupère le nombre d'heures et de minutes à partir du nombre de minutes
              $total_hours = floor($$heure_pending_min / 60);
              $total_minutes = $$heure_pending_min % 60;
              // Si le nombre total de minutes est < à 10 alors on met un 0 devant
              if ($total_minutes < 10) $total_minutes = '0'.$total_minutes;
              // On formate le nombre d'heures avec HH:mm
              $$heure_pending = $total_hours.':'.$total_minutes;
              // On stoque le nombre de minutes de la période
              $$heure_pending_min = $$heure_pending_min;

              if ($$heure_prog_min < $$heure_pending_min) $$pending_color = "yellow-alert";
              else $$pending_color = "";
            }

            // -----------------------------------------------------------------





            // -----------------------------------------------------------------
            // Récupération du nombre total d'heures programmés
            // -----------------------------------------------------------------
            $heure_theo_tot = $list_hours_theo[$key][$key_1][0];
            // Si l'heure = 0 alors on affiche 00:00
            if ($heure_theo_tot == '00') $heure_theo_tot = '00:00';
            $temp = explode(':', $heure_theo_tot);
            // Si les minutes sont = à 0 alors on met 00
            if ($temp[1] == 0) $heure_theo_tot = $temp[0].':00';
            // Si les heures sont < à 10 alors on met un 0 devant
            if ($temp[0] < 10 && strlen($temp[0]) < 2) $heure_theo_tot = '0'.$heure_theo_tot;
            // On calcul le nombre de minutes à partir des heures et minutes
            $heure_theo_tot_min = ($temp[0] * 60) + $temp[1];

            // ---------------------------------------------------------------
            // On calcule le nombre d'heures programmés (ou en attente) totales
            // ---------------------------------------------------------------
            $temp_1 = explode(':', $value_1[1]);
            $temp_2 = explode(':', $value_1[2]);

            $temp_3 = explode(':', $list_hours_pending[$key][$key_1][1]);
            $temp_4 = explode(':', $list_hours_pending[$key][$key_1][2]);

            // On calcul le nombre de minutes
            if ($temp_1[0] == 0 && $temp_1[1] == 0) $time = 0;
            else $time = ($temp_1[0] * 60) + $temp_1[1];
            if ($temp_2[0] == 0 && $temp_2[1] == 0) $time += 0;
            else $time += ($temp_2[0] * 60) + $temp_2[1];
            // On calcul les heures
            $total_hours = floor($time / 60);
            $total_minutes = $time % 60;

            // Si le nombre total de minutes est == à 0 alors on met 00
            if ($total_minutes == 0 || $total_minutes == '0') $total_minutes = '00';
            // Si le nombre total de minutes est < à 10 alors on met un 0 devant
            elseif ($total_minutes < 10) $total_minutes = '0'.$total_minutes;
            // On formate le nombre d'heures avec HH:mm
            $heure_prog_tot = $total_hours.':'.$total_minutes;
            // On stoque le nombre de minutes de la période
            $heure_prog_tot_min = $time;

            // Pour les heures en attentes:
            unset($time);
            if ($temp_3[0] == 0 && $temp_3[1] == 0) $time += 0;
            else $time += ($temp_3[0] * 60) + $temp_3[1];
            if ($temp_4[0] == 0 && $temp_4[1] == 0) $time += 0;
            else $time += ($temp_4[0] * 60) + $temp_4[1];
            // On calcul les heures
            $total_hours_pending = floor($time / 60);
            $total_minutes_pending = $time % 60;
            // Si le nombre total de minutes est == à 0 alors on met 00
            if ($total_minutes_pending == 0 || $total_minutes_pending == '0') $total_minutes_pending = '00';
            // Si le nombre total de minutes est < à 10 alors on met un 0 devant
            elseif ($total_minutes_pending < 10) $total_minutes_pending = '0'.$total_minutes_pending;
            // On formate le nombre d'heures avec HH:mm
            $heure_pending_tot = $total_hours_pending.':'.$total_minutes_pending;
            // On stoque le nombre de minutes de la période
            $heure_pending_tot_min = $time;


            // Si le nombre d'heures théorique est suppérieur au nombre d'heures réelles alors on met en rouge
            if ($heure_theo_tot_min > $heure_prog_tot_min) $color_tot = "red-alert";
            // Si le nombre d'heures théorique est inférieur au nombre d'heures réelles alors on met en vert
            elseif ($heure_theo_tot_min < $heure_prog_tot_min) $color_tot = "green-alert";
            // Sinon on ne met pas de couleurs
            else $color_tot = "";

            if ($heure_prog_tot_min < $heure_pending_tot_min) $pending_total_color = "yellow-alert";
            else $pending_total_color = "";
            // -----------------------------------------------------------------



            if (!getParam('showOnlyTotalPeriodGeneral')) {
              foreach ($period_list as $i => $value) {
                $heure_theo = 'heure_theo_'.$i;
                $heure_prog = 'heure_prog_'.$i;
                $heure_pending = 'heure_attente_'.$i;
                $pending_color = 'color_pending_'.$i;
                $color = 'color_'.$i;
                // Heure théorique
                echo "<td class=\"hours_time ".$$color."\">";
                  echo $$heure_theo;
                echo "</td>";
                // Heure programmé
                echo "<td class=\"hours_time ".$$color."\">";
                  echo $$heure_prog;
                echo "</td>";
                // Heure programmé + en attente
                echo "<td class=\"hours_time end_period ".$$pending_color."\">";
                  echo $$heure_pending;
                echo "</td>";
              }
            }



            // Heure théorique Période totale
            echo "<td class=\"hours_time ".$color_tot."\">";
              echo $heure_theo_tot;
            echo "</td>";

            // Heure programmé Période totale
            echo "<td class=\"hours_time ".$color_tot."\">";
              echo $heure_prog_tot;
            echo "</td>";

            // Heure programmé + en attente Période totale
            echo "<td class=\"hours_time ".$pending_total_color."\">";
              echo $heure_pending_tot;
            echo "</td>";



          echo "</tr>";
        }
      }
    ?>

    <style>
      .hours_time {
        text-align: center !important;
      }
      .end_period {
        border-right: 1px solid grey;
      }
      .red-alert {
        background-color: #f2dede !important;
        color: #b94a48 !important;
      }
      .green-alert {
        background-color: #dff0d8 !important;
        color: #468847 !important;
      }
      .yellow-alert {
        background-color: #fcf8e3 !important;
        color: #c09853 !important;
      }
    </style>
  </table>
</div>
