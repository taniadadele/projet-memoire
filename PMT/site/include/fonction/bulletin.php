<?php
/**
 * Fonctions pour l'affichage/traitement du bulletin
 */




/**
 * Fonction: Afficher le tableau des absences
 * @param int $year 		année voulue
 * @param int $IDeleve  ID de l'élève
 * @param int $mode			Le mode d'affichage voulu: 1: Détail des absences, 2: Nb de journée/demi-journée d'absence (justifiées et injustifiées)
 * @return string  			Le tableau
 */
function getAbsenceTable($year, $IDeleve, $mode = 1) {

  // On vérifie que l'on veuille afficher les absences sur le bulletin
  if (!getParam('absence_show_bulletin')) return '';

  $toReturn = '';

  // Si on affiche le détail des absences
  if ($mode == 1) {
    $date_start = $year."-".getParam('START_M')."-".getParam('START_D')." 00:00:00";
    $date_end = ($year + 1)."-".getParam('END_M')."-".getParam('END_D')." 23:59:59";
    $query = "SELECT _IDdata, _start, _end, _texte, _valid, _file FROM absent_items WHERE _IDabs = '".$IDeleve."' AND _start >= '$date_start' AND _start <= '$date_end' ";
    $result = mysql_query($query);
    // On affiche le tableau des absences que si on a des données à afficher
    if (mysql_num_rows($result)) {
      $toReturn .= '<h5 style="'.$hide.'">Absences</h5>';
      $toReturn .= '<table style="width: 100%; '.$hide.'">';
        $toReturn .= '<tr style="text-align: center; font-weight: bold; background-color: #C0C0C0;"><td colspan="5">Absences</td></tr>';
        $toReturn .= '<tr style="text-align: center; font-weight: bold; background-color: #D0D0D0;">';
          $toReturn .= '<td>Justifié</td>';
          $toReturn .= '<td>Début</td>';
          $toReturn .= '<td>Fin</td>';
          $toReturn .= '<td>Raison</td>';
          $toReturn .= '<td>Commentaire</td>';
        $toReturn .= '</tr>';

      while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
        $toReturn .= "<tr style=\"text-align: center;\">";
          if ($row[4] == O) $toReturn .= '<td style="padding: 0 5px;"><i class="fa fa-check"></i></td>';
          else $toReturn .= '<td style="padding: 0 5px;"><i class="fa fa-times"></i></td>';
          $toReturn .= '<td style="padding: 0 5px;">'.date('d/m/Y H:m', strtotime($row[1])).'</td>';
          $toReturn .= '<td style="padding: 0 5px;">'.date('d/m/Y H:m', strtotime($row[2])).'</td>';
          $toReturn .= '<td style="padding: 0 5px;">'.getReasonByID($row[0]).'</td>';
          $toReturn .= '<td style="padding: 0 5px; width: 40%;">'.$row[3].'</td>';
        $toReturn .= '</tr>';
      }
      $toReturn .= '</table>';
    }
  }

  // Si on veux juste le compte d'absences totales et d'absences non justifiées
  elseif ($mode == 2) {
    $toReturn .= '<h5>Absences</h5>';
    $date_start = $year."-".getParam('START_M')."-".getParam('START_D')." 00:00:00";
    $date_end = ($year + 1)."-".getParam('END_M')."-".getParam('END_D')." 23:59:59";
    $query = "SELECT COUNT(*) FROM absent_items WHERE _IDabs = '".$IDeleve."' AND _start >= '$date_start' AND _start <= '$date_end' AND _IDdata != 2 ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) $absence_demi_journée = $row[0] / 2;

    // Absences non justifiés
    $query = "SELECT COUNT(*) FROM absent_items WHERE _IDabs = '".$IDeleve."' AND _start >= '$date_start' AND _start <= '$date_end' AND _IDdata != 2 AND _valid = 'N' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) $absence_demi_journée_unjustified = $row[0] / 2;

    $toReturn .= '<table style="width: 30%; '.$hide.'">';
      $toReturn .= '<tr><th colspan="2" class="table_header">ABSENCES</th></tr>';
      $toReturn .= '<tr>';
        if ($absence_demi_journée > 1) $accord = 's'; else $accord = '';
        $toReturn .= '<td>Demi-journée'.$accord.' d\'absence'.$accord.'</td>';
        $toReturn .= '<td>'.$absence_demi_journée.'</td>';
      $toReturn .= '</tr>';
      $toReturn .= '<tr>';
        if ($absence_demi_journée_unjustified > 1) $accord = 's'; else $accord = '';
        $toReturn .= '<td>Dont absence'.$accord.' non justifiée'.$accord.'</td>';
        $toReturn .= '<td>'.$absence_demi_journée_unjustified.'</td>';
      $toReturn .= '</tr>';
    $toReturn .= '</table>';
  }
  return $toReturn;
}








/**
 * Fonction: Afficher le tableau des certificats
 * @param int $year 		année voulue
 * @param int $IDeleve  ID de l'élève
 * @param int $mode			Le mode d'affichage voulu: 1: affichage vertical, 2: affichage horizontal
 * @return string  			Le tableau
 */
function getCertificatTable($year, $IDeleve, $mode = 1) {
  $toReturn = '';
  if (getParam('afficherSur20MoyenneBulletin')) $sur = 20; else $sur = 0;

  // Si on veux un affichage vertical
  if ($mode == 1) {

    $query  = "SELECT data._IDmat, data._period, items._value, data._IDclass, data._year FROM notes_data data, notes_items items WHERE items._IDdata = data._IDdata AND data._IDmat >= 100000 AND items._IDeleve = '".$IDeleve."' AND items._value != '' ";
    $query .= "AND data._year <= '".$year."' ";
    $query .= "ORDER BY data._year ASC ";
    $result = mysql_query($query);
    $oldYear = "";
    if (!mysql_num_rows($result)) return '';

    $toReturn .= '<table style="width: 100%;" border="1">';
      $toReturn .= '<tr style="text-align: center; font-weight: bold;">';
        $toReturn .= '<td colspan="3" style="background-color:#C0C0C0;">Certificats</td>';
      $toReturn .= '</tr>';
      $toReturn .= '<tr>';
        $toReturn .= '<th>Nom</th>';
        $toReturn .= '<th style="width: 1%; padding: 0 2px;">Note</th>';
        $toReturn .= '<th style="width: 1%; padding: 0 2px;">Ratt.</th>';
      $toReturn .= '</tr>';
      while ($row = mysql_fetch_array($result, MYSQL_NUM))
      {
        $uvID = $row[0] - 100000;
        if (isUVRattrapage($uvID)) continue;
        $uvName = getUVNameByID($uvID);
        $uvRattrapageNote = getUVRattNote($uvID, $IDeleve);

        // On récupère l'intitulé du niveau lors de l'année en question de l'élève en question
        $userYearNiveauName = getNiveauNameByNumNiveau(getUserNiveauNumberByUserIDAndYear($IDeleve, $row[4]));
        if ($userYearNiveauName == "") $userYearNiveauName = getNiveauNameByUserID($IDeleve);
        if ($oldYear != $userYearNiveauName)
        {
          $toReturn .= "<tr style=\"/* background-color:#D0D0D0;*/ /* border-top: 1px dashed grey !important; */ /* text-align: center; */ font-weight: bold;\">";
            $toReturn .= "<td colspan=\"3\">".$userYearNiveauName."</td>";
          $toReturn .= "</tr>";
          $oldYear = $userYearNiveauName;
        }

        if (isUVCertificat($uvID))
        {
          $toReturn .= "<tr>";
            $toReturn .= "<td style=\"font-weight: bold; padding-left: 10px;\">$uvName</td>";
            $toReturn .= '<td style="text-align: center;">'.makeNote($row[2], 2, $sur).'</td>';
            if (!is_null(getUVRattNote($uvID, $IDeleve))) $toReturn .= '<td style="text-align: center;">'.makeNote($uvRattrapageNote, 2, $sur).'</td>';
            else $toReturn .= '<td></td>';
          $toReturn .= "</tr>";
        }
      }
    $toReturn .= '</table>';
  }


  // Si on veux un affichage horizontal
  elseif ($mode == 2) {
    // On récupère les données
    $certificats = array();
    $query  = "SELECT data._IDmat, data._period, items._value, data._IDclass, data._year FROM notes_data data, notes_items items WHERE items._IDdata = data._IDdata AND data._IDmat >= 100000 AND items._IDeleve = '".$IDeleve."' AND items._value != '' ";
    $query .= "AND data._year <= '".$year."' AND data._IDmat > 100000 ORDER BY data._year ASC ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM))
    {
      $uvID = $row[0] - 100000;
      if (isUVRattrapage($uvID)) continue;
      $uvName = getUVNameByID($uvID);
      $uvRattrapageNote = getUVRattNote($uvID, $IDeleve);

      // On récupère l'intitulé du niveau lors de l'année en question de l'élève en question
      $userYearNiveauNumber = getUserNiveauNumberByUserIDAndYear($IDeleve, $row[4]);
      if (!$userYearNiveauNumber) $userYearNiveauNumber = getNiveauNumberByUserID($IDeleve);
      // $userYearNiveauName = getNiveauNameByNumNiveau($userYearNiveauNumber);
      if (isUVCertificat($uvID))
      {
        // On enregistre le certificat dans un tableau
        $certificats[$userYearNiveauNumber][$uvID] = array(
          'name' => $uvName,
          'note' => $row[2],
          'note_ratt' => $uvRattrapageNote
        );
      }
    }
    if (!count($certificats)) return '';

    // On affiche les données
    $toReturn .= '<h5>Certificats</h5>';
    $toReturn .= '<table>';
      $toReturn .= '<tr>';
        $compteur = 0;
        foreach ($certificats as $userNiveau => $value) {
          if (!$compteur) $border_table_right = 'border-right: 0px;';
          else $border_table_right = '';
          $toReturn .= '<td style="padding: 0px;">';
            $toReturn .= '<table cellpadding="0" cellspacing="0" border="1" style="border: 1px solid black; '.$border_table_right.'">';
              $toReturn .= '<tr>';
                $toReturn .= '<th colspan="3" class="table_header">'.$userNiveau.'A</th>';
              $toReturn .= '</tr>';
              $toReturn .= '<tr>';
                $toReturn .= '<td class="table_header_2"></td>';
                $toReturn .= '<td class="table_header_2">Note</td>';
                $toReturn .= '<td class="table_header_2">Ratt.</td>';
              $toReturn .= '</tr>';
              foreach ($value as $uvID => $uvInfos) {
                $toReturn .= '<tr>';
                  $toReturn .= '<td style="font-weight: bold;">'.$uvInfos['name'].'</td>';
                  $toReturn .= '<td class="note_certificat">'.makeNote($uvInfos['note'], 2, $sur).'</td>';
                  if (!is_null($uvInfos['note_ratt'])) $toReturn .= '<td class="note_certificat">'.makeNote($uvInfos['note_ratt'], 2, $sur).'</td>';
                  else $toReturn .= '<td class="note_certificat"></td>';
                $toReturn .= '</tr>';
              }
            $toReturn .= '</table>';
          $toReturn .= '</td>';
          $compteur++;
        }
      $toReturn .= '</tr>';
    $toReturn .= '</table>';
  }
  return $toReturn;
}









/**
 * Fonction: Afficher les tableaux d'historique
 * @param int $year 		année voulue
 * @param int $IDeleve  ID de l'élève
 * @param int $mode			Le mode d'affichage voulu: 1: affichage horizontal, 2: affichage vertical
 * @return string  			Le tableau
 */
function getPoleAndMoyHistoryTable($year, $IDeleve, $mode = 1, $notes_current_history = array()) {
  $toReturn = '';
  if (getParam('afficherSur20MoyenneBulletin')) $sur = 20; else $sur = 0;

  // Si on veux la vue horizontale
  if ($mode == 1) {
    // On récupère les différentes classes dans lesquels l'utilisateur à pu être (en prenant en compte le redoublement)
    $list_annee = array();
    $classe_already_shown = ';';
    $query = "SELECT _niveau, _year FROM user_log WHERE _IDuser = '".$IDeleve."' AND _year < '".$year."' ORDER BY _year ASC ";
    $result = mysql_query($query);
    $compteur = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      if (strpos($classe_already_shown, ';'.$row[0].';') !== false) $liste_annee[$row[1]] = $row[0].'R';
      else $liste_annee[$row[1]] = $row[0];
      $classe_already_shown .= $row[0].';';
      $compteur++;
    }
    // if ($compteur == 0) $compteur = 1;

    $compteur++;
    $niveau_to_show = getUserNiveauNumberByUserIDAndYear($IDeleve, $year);
    $liste_annee[$year] = $niveau_to_show;

    // Récupération de l'historique des moyennes par pôles
    $tempArray = Array();
    $query = "SELECT _niveau, _attr, _year FROM user_log WHERE _IDuser = '".$IDeleve."' AND _year < '".$year."' ORDER BY _year DESC ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $niveauNumber = $row[0];
      $annee = $row[2];
      $historique = json_decode($row[1], TRUE);
      foreach ($historique['moyenne_pole'] as $key => $value) {
        // $tempArray[$key][$niveauNumber] = $value;
        $tempArray[$key][$annee] = $value;
      }
    }

    foreach ($notes_current_history['eleve']['pole'] as $poleID => $pole_note) {
      $tempArray[$poleID][$year] = $pole_note;
    }

    $historique_moyennes[$year] = $notes_current_history['eleve']['general'];
    $temp = array('pole' => $notes_current_history['classe']['pole'], 'general' => $notes_current_history['classe']['general']);
    $historique_classe[$year] = $temp;


    if (count($tempArray)) {
      $toReturn .= '<table style="width: 100%; text-align: center;">';
        $toReturn .= '<tr>';
          $toReturn .= '<!-- Historique par pôles -->';
          $toReturn .= '<td style="width: 30%; padding: 0px !important; vertical-align: top; /* border-right: 1px solid grey; */ padding-right: 5px !important;">';
            $toReturn .= '<table style="width: 100%; margin: 0px; text-align: center;" border="1">';
              $toReturn .= '<tr style="background-color: #C0C0C0; font-weight: bold;">';
                $toReturn .= '<td rowspan="2">';
                  $toReturn .= 'Pôle';
                $toReturn .= '</td>';
                $toReturn .= '<td colspan="'.$compteur.'">';
                  $toReturn .= 'Année';
                $toReturn .= '</td>';
              $toReturn .= '</tr>';
              $toReturn .= '<tr style="background-color: #C0C0C0; font-weight: bold;">';
                // On affiche l'en-tête du tableau avec les différentes classes dans lesquels l'utilisateur à pu être
                foreach ($liste_annee as $key => $value) {
                  $toReturn .= '<td style="width: 1%; padding: 0 2px;">'.$value.'</td>';
                }
              $toReturn .= '</tr>';
              foreach ($tempArray as $key => $value) {
                $toReturn .= "<tr>";
                  $toReturn .= "<td style=\"/* background-color: #D0D0D0; */ font-weight: bold;\">".getPoleNameByIdPole($key)."</td>";
                  foreach ($liste_annee as $key_a => $value_a) {
                    $toReturn .= '<td style="width: 1%; padding: 0 2px;">'.makeNote($value[$key_a], 2, $sur).'</td>';
                  }
                $toReturn .= "</tr>";
              }
            $toReturn .= '</table>';
          $toReturn .= '</td>';
          $toReturn .= '<!-- Historique des moyennes générales -->';
          $toReturn .= '<td style="width: 30%; padding: 0px !important; vertical-align: top;">';
            $toReturn .= '<table style="width: 100%; margin: 0px; text-align: center;" border="1">';
              $toReturn .= '<tr style="text-align: center; font-weight: bold;">';
                $toReturn .= '<td colspan="2" style="background-color:#C0C0C0;">Moyenne générale</td>';
              $toReturn .= '</tr>';
                // On récupère les différentes classes dans lesquels l'utilisateur à pu être (en prenant en compte le redoublement)
                $list_annee = array();
                $classe_already_shown = ';';
                $query = "SELECT _niveau, _year, _attr FROM user_log WHERE _IDuser = '".$IDeleve."' AND _year < '".$year."' ORDER BY _year ASC ";
                $result = mysql_query($query);
                while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
                  $toReturn .= '<tr>';
                    $toReturn .= '<td style="width: 50%; font-weight: bold;">'.getNiveauNameByNumNiveau($row[0]);
                    if (strpos($classe_already_shown, ';'.$row[0].';') !== false) $toReturn .= ' (redoublement)</td>';
                    $historique = json_decode($row[2], TRUE);
                    $toReturn .= '<td>';
                      $toReturn .= makeNote($historique['moyene_generale'], 2, $sur);
                    $toReturn .= '</td>';
                  $toReturn .= '</tr>';
                  $classe_already_shown .= $row[0].';';
                }
                $toReturn .= '<tr>';
                  $toReturn .= '<td style="width: 50%; font-weight: bold;">'.getNiveauNameByNumNiveau($liste_annee[$year]);
                  if (strpos($classe_already_shown, ';'.$liste_annee[$year].';') !== false) $toReturn .= ' (redoublement)</td>';

                  $historique = json_decode($row[2], TRUE);
                  $toReturn .= '<td>';
                    $toReturn .= makeNote($historique_moyennes[$year], 2, $sur);
                  $toReturn .= '</td>';
                $toReturn .= '</tr>';
                $classe_already_shown .= $row[0].';';
            $toReturn .= '</table>';
          $toReturn .= '</td>';
        $toReturn .= '</tr>';
      $toReturn .= '</table>';
    }
  }

  // Si on veux une vue verticale
  elseif ($mode == 2) {
    // On récupère les différentes classes dans lesquels l'utilisateur à pu être (en prenant en compte le redoublement)
    $list_annee = array();
    $classe_already_shown = ';';
    $query = "SELECT _niveau, _year FROM user_log WHERE _IDuser = '".$IDeleve."' and _year < '".$year."' ORDER BY _year ASC ";
    $result = mysql_query($query);
    $compteur = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      if (strpos($classe_already_shown, ';'.$row[0].';') !== false) $liste_annee[$row[1]] = $row[0].'R';
      else $liste_annee[$row[1]] = $row[0];
      $classe_already_shown .= $row[0].';';
      $compteur++;
    }

    $niveau_to_show = getUserNiveauNumberByUserIDAndYear($IDeleve, $year);
    $liste_annee[$year] = $niveau_to_show;

    if (!count($liste_annee)) return '';
    if ($compteur == 0) $compteur = 1;
    $toReturn .= '<h5>Cursus etiopatique</h5>';
    $toReturn .= '<table style="width: 100%; margin: 0px; text-align: center;" border="1">';
      $toReturn .= '<tr style="background-color: #C0C0C0; font-weight: bold;">';
        $toReturn .= '<td rowspan="2">Pôle</td>';
        $colspan = count($liste_annee) * 2;
        $toReturn .= '<td colspan="'.$colspan.'">Année</td>';
      $toReturn .= '</tr>';
      $toReturn .= '<tr style="background-color: #C0C0C0; font-weight: bold;">';
        // On affiche l'en-tête du tableau avec les différentes classes dans lesquels l'utilisateur à pu être
        foreach ($liste_annee as $key => $value) {
          $toReturn .= '<td colspan="2" style="width: 1%; padding: 0 2px;">'.$value.'A</td>';
        }
      $toReturn .= '</tr>';
      $toReturn .= '<tr>';
        $toReturn .= '<td class="table_header_2"></td>';
        foreach ($liste_annee as $key => $value) {
          $toReturn .= '<td class="table_header_2" style="width: 1%;">Etudiant</td>';
          $toReturn .= '<td class="table_header_2" style="width: 1%;">Classe</td>';
        }
      $toReturn .= '</tr>';
      // Récupération de l'historique des moyennes par pôles
      $tempArray = array();
      $historique_moyennes = array();
      $query = "SELECT _niveau, _attr, _year FROM user_log WHERE _IDuser = '".$IDeleve."' AND _year < '".$year."' ORDER BY _year DESC ";
      $result = mysql_query($query);
      while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
        $niveauNumber = $row[0];
        $annee = $row[2];
        $historique = json_decode($row[1], TRUE);
        foreach ($historique['moyenne_pole'] as $key => $value) {
          $tempArray[$key][$annee] = $value;
        }
        $historique_moyennes[$annee] = $historique['moyene_generale'];
        $historique_classe[$annee] = $historique['moyenne_classe'];
      }


      foreach ($notes_current_history['eleve']['pole'] as $poleID => $pole_note) {
        $tempArray[$poleID][$year] = $pole_note;
      }

      $historique_moyennes[$year] = $notes_current_history['eleve']['general'];
      $temp = array('pole' => $notes_current_history['classe']['pole'], 'general' => $notes_current_history['classe']['general']);
      $historique_classe[$year] = $temp;

      foreach ($tempArray as $key => $value) {
        $toReturn .= '<tr>';
          $toReturn .= '<td>'.getPoleNameByIdPole($key).'</td>';
          foreach ($liste_annee as $key_a => $value_a) {
            $toReturn .= '<td style="width: 1%; padding: 0 2px;">'.makeNote($value[$key_a], 2, $sur).'</td><td>'.makeNote($historique_classe[$key_a]['pole'][$key], 2, $sur).'</td>';
          }
        $toReturn .= '</tr>';
      }
      $toReturn .= '<!-- Moyenne générale -->';
      $toReturn .= '<tr style="border-top: 1px solid black;">';
        $toReturn .= '<td style="font-weight: bold;">MOYENNES GÉNÉRALES</td>';
        foreach ($liste_annee as $key_a => $value_a) {
        $toReturn .= '<td style="font-weight: bold;">'.makeNote($historique_moyennes[$key_a], 2, $sur).'</td><td>'.makeNote($historique_classe[$key_a]['general'], 2, $sur).'</td>';
        }
        if (!count($liste_annee)) $toReturn .= '<td></td>';
      $toReturn .= '</tr>';
      $toReturn .= '<!-- Heures de stage -->';
      $toReturn .= '<tr>';
        $toReturn .= '<td style="font-weight: bold;">STAGES (nb heures)</td>';
        foreach ($liste_annee as $key_a => $value_a) {
          $query = "SELECT SUM(_stage_hours) FROM notes_text WHERE _IDeleve = '".$IDeleve."' AND _year = '".$key_a."' AND _period = 0 ";
          $result = mysql_query($query);
          while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
            $stage_hours_student = $row[0];
          }
          // Moyenne du nombre d'heures de la promo
          $stage_hours_classe = $compteur = 0;
          $query = "SELECT SUM(_stage_hours) FROM notes_text WHERE _IDclass = '".getClassIDByUserID($IDeleve)."' AND _year = '".$key_a."' GROUP BY _IDeleve ";
          $result = mysql_query($query);
          while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
            $stage_hours_classe += $row[0];
            $compteur++;
          }
          $stage_hours_classe = $stage_hours_classe / $compteur;
          $toReturn .= '<td style="font-weight: bold;">'.$stage_hours_student.'</td><td>'.round($stage_hours_classe).'</td>';
        }
        if (!count($liste_annee)) $toReturn .= '<td></td>';
      $toReturn .= '</tr>';
    $toReturn .= '</table>';
  }
  return $toReturn;
}





/**
 * Fonction: Afficher le tableau de bulletin des notes
 * @param int $year 		année voulue
 * @param int $IDeleve  ID de l'élève
 * @param int $period	  La période voulue
 * @param int $isPrint  Est-ce que l'on est en mode impression ou juste affichage (0 = affichage = défault)
 * @return string  			Le tableau
 */
function getStudentNotesTable($year, $IDeleve, $period = 0, $isPrint = 0) {
  $toReturn = '';
  $list = json_decode(getParam('periodeList'), TRUE);
  if (getParam('afficherSur20MoyenneBulletin')) $sur = 20; else $sur = 0;
  $IDclass = getUserClassIDByUserID($IDeleve);

  $nbColumn = $nbColumnBeforeMoyenneGene = 0;
  $toReturn .= '<table width="100%" cellspacing="1" cellpadding="2" style="text-align: center;" border="1" class="bulletin_table">';
    $toReturn .= "<tr class=\"align-center\" style=\"background-color:#C0C0C0; font-weight: bold;\">";
      if (getParam('forceWidthColonneBulletin') && !$isPrint) $temp_width = 'width: 30%;'; else $temp_width = '';
      $toReturn .= '<td style="'.$temp_width.'">Matières</td>';
      $nbColumn += 1;
      $nbColumnBeforeMoyenneGene++;

      // Si on affiche pas les notes de toute l'année
      if ($period != 0)
      {
        if (getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) {
          $toReturn .= "<td style=\"padding: 0 5px; white-space:nowrap;\">".$list[$period]."</td>";
          $nbColumn += 1;
          $nbColumnBeforeMoyenneGene += 1;
        }
        $toReturn .= '<td style="width: 1%;">Nb notes</td>';
        $toReturn .= '<td style="padding: 0 10px; white-space:nowrap; width: 1%;">Moyenne</td>';
        // Si on affiche la moyenne de la classe
        if (getParam('afficherMoyenneClasseBulletin')) {
          $toReturn .= '<td style="padding: 0 10px; white-space:nowrap; width: 1%;">Classe</td>';
          $nbColumn += 1;
          $nbColumnBeforeMoyenneGene += 1;
        }
        $nbColumn += 2;
      }
      else
      {
        $listPeriod = json_decode(getParam('periodeList'), TRUE);
        if (!getParam('afficherQueLaPeriodeTotaleBulletin') && ((getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) || getParam('afficherMoyenneParPeriodeBulletin'))) {
          foreach ($listPeriod as $key => $value) {
            // Si on affiche le détail des notes par matière sur la vue annuelle du bulletin
            if (getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) {
              $toReturn .= '<td style="padding: 0 10px; white-space:nowrap; width: 1%;">'.$listPeriod[$key].'</td>';
              $nbColumn += 1;
              $nbColumnBeforeMoyenneGene += 1;
            }

            // Si on affiche la moyenne de chaque période de chaque matière sur le bulletin en vue annuelle
            if (getParam('afficherMoyenneParPeriodeBulletin')) {
              $toReturn .= '<td style="padding: 0 10px; white-space:nowrap; width: 1%;">'.$listPeriod[$key].'&nbsp;Moy</td>';
              $nbColumn += 1;
              $nbColumnBeforeMoyenneGene += 1;
            }
            // Si on affiche la moyenne de la classe
            if (getParam('afficherMoyenneClasseBulletin')) {
              $toReturn .= '<td style="padding: 0 10px; white-space:nowrap; width: 1%;">Classe</td>';
              $nbColumn += 1;
              $nbColumnBeforeMoyenneGene += 1;
            }
          }
        }



        // Est-ce que l'on affiche le détail des notes sur toute l'année ?
        if ((getParam('afficherDetailNoteAllPeriodeBulletin')) || (getParam('afficherDetailNoteParPeriodeBulletin') && getParam('afficherQueLaPeriodeTotaleBulletin'))) {
          if (getParam('forceWidthColonneBulletin') && !$isPrint) $temp_width = 'width: 15%;'; else $temp_width = '';
          $toReturn .= '<td style="'.$temp_width.'">Notes</td>';
          $nbColumn += 1;
          $nbColumnBeforeMoyenneGene += 1;
        }

        // Est-ce que l'on affiche le compteur de notes pour chaque ligne du bulletin
        if (getParam('afficherCompteurNotesMatBulletin')) {
          $toReturn .= "<td style=\"width: 1%;\">Nb notes</td>";
          $nbColumn += 1;
          $nbColumnBeforeMoyenneGene += 1;
        }

        if (getParam('forceWidthColonneBulletin')) $temp_padding = 20; else $temp_padding = 10;
        $toReturn .= '<td style="padding: 0 '.$temp_padding.'px; white-space: nowrap; width: 1%;">Moyenne</td>';
        // Si on affiche la moyenne de la classe
        if (getParam('afficherMoyenneClasseBulletin')) {
          $toReturn .= '<td style="padding: 0 10px; white-space: nowrap; width: 1%;">Classe</td>';
          $nbColumn += 1;
        }
        $libelleRattrapage = getParam('texteLabelColonneRattrapage');
        $toReturn .= '<td style=" padding: 0 '.$temp_padding.'px; /* white-space: nowrap; */ width: 1%;">'.$libelleRattrapage.'</td>';
        $libelleNoteRattrapage = getParam('texteLabelColonneNoteRattrapage');
        $toReturn .= '<td style=" padding: 0 '.$temp_padding.'px; /* white-space: nowrap; */ width: 1%;">'.$libelleNoteRattrapage.'</td>';
        $nbColumn += 3;
      }
    $toReturn .= "</tr>";


    // ----------------------------------------
    // Récupération des notes
    // ----------------------------------------
    $temp = array();
    // Pour chaque matière
    $query = "SELECT DISTINCT notes._IDmat, notes._IDdata, notes._period, notes._type, notes._total, notes._coef, pole_mat_annee._ID_pole, pole._name FROM notes_data notes INNER JOIN pole_mat_annee pole_mat_annee INNER JOIN pole pole WHERE (pole_mat_annee._ID_pma = notes._IDmat OR notes._IDmat > 100000) AND pole_mat_annee._ID_pole = pole._ID AND ";
    $query .= "notes._year = '$year' AND notes._IDdata IN (SELECT _IDdata FROM notes_items WHERE _IDeleve = '$IDeleve') ";
    if ($period != 0) $query .= "AND notes._period = '$period' ";
    $query .= "ORDER BY pole._name ASC ";
    $note_array = $note_classe_array = $moyenne_classe = array();
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $note_type  = explode(';', $row[3]);
      $note_total = explode(';', $row[4]);
      $note_coef  = explode(';', $row[5]);

      $IDmat = $row[0];

      if ($row[0] > 100000) $poleID = getPoleIDByUVID($row[0] - 100000);
      else $poleID = getPoleIDByPMAID($row[0]);

      // Si on ne veux pas afficher les examens (UV) dans le bulletin:
      if (!getParam('afficherExamensDansBulletin') && $row[0] > 100000) continue;
      if ($row[0] > 100000 && isUVRattrapage($row[0] - 100000)) continue;

      // Pour l'élève sélectionné
      $query_note = "SELECT _value, _index, _IDitems FROM notes_items WHERE _IDdata = '".$row[1]."' AND _IDeleve = '".$IDeleve."' ORDER BY _index ASC ";
      $result_note = mysql_query($query_note);
      while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM)) {
        $index_note = $row_note[1];
        $show_note = 1;
        if ($row[0] > 100000)
        {
          if (isUVCertificat($row[0] - 100000)) $show_note = 0;
          $index_note += 100000;
        }
        // Si la note est un certificat (pour lyon)
        if ($note_type[$row_note[1]] == 100) $show_note = 0;
        if ($row_note[0] != "" && $show_note == 1) $note_array[$poleID][$IDmat][$row[2]][$index_note] = $row_note[0].";".$note_type[$row_note[1]].";".$note_total[$row_note[1]].";".$note_coef[$row_note[1]];
      }

      // Pour tous les élèves de la promo
      $compteur = 0;
      $result_note = mysql_query("SELECT _value, _index FROM notes_items WHERE _IDdata = '".$row[1]."' ORDER BY _index ASC ");
      while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM)) {
        if (($row[0] > 100000 && isUVCertificat($row[0] - 100000)) || $note_type[$row_note[1]] == 100 || $row_note[0] == '') continue;		// Si la note est un certificat ou si la note est vide
        $compteur++;
        $temp[$IDmat][$row[2]][$compteur] = $row_note[0].";".$note_type[$row_note[1]].";".$note_total[$row_note[1]].";".$note_coef[$row_note[1]];
      }
    }

    // Pour le calcul des moyennes de classe par période
    $moy_classe_array = $temp_2 = array();
    $moyenne_temp_annuel_total = $moyenne_temp_count_annuel_total = 0;
    // Pour chaque mat
    foreach ($temp as $IDmat => $temp_periode) {
      $moyenne_temp_annuel_mat = $moyenne_temp_count_annuel_mat = 0;
      // Pour chaque période
      foreach ($temp_periode as $periode_temp => $notes_list) {
        $moyenne_temp = $moyenne_temp_count = 0;

        // Pour chaque note
        foreach ($notes_list as $note) {

          $temp_1 = explode(';', $note);
          if ($temp_1[1] == 100) continue;
          // Moyenne par période
          $moyenne_temp += (($temp_1[0] / $temp_1[2]) * 20) * $temp_1[3];
          $moyenne_temp_count += $temp_1[3];
          // Moyenne annuelle par mat
          $moyenne_temp_annuel_mat += (($temp_1[0] / $temp_1[2]) * 20) * $temp_1[3];
          $moyenne_temp_count_annuel_mat += $temp_1[3];
          // Moyenne annuelle totale (de tt les matières)
          $moyenne_temp_annuel_total += (($temp_1[0] / $temp_1[2]) * 20) * $temp_1[3];
          $moyenne_temp_count_annuel_total += $temp_1[3];

          $temp_2[$periode_temp]['total_note'] += (($temp_1[0] / $temp_1[2]) * 20) * $temp_1[3];
          $temp_2[$periode_temp]['total_count'] += $temp_1[3];
        }
        $moy_classe_array[$periode_temp][$IDmat] = reduceNoteSizeDecimal($moyenne_temp / $moyenne_temp_count);
      }
      $moy_classe_array[0][$IDmat] = reduceNoteSizeDecimal($moyenne_temp_annuel_mat / $moyenne_temp_count_annuel_mat);
    }
    // Moyenne de toute les matières par périodes
    foreach ($temp_2 as $key => $value) {
      $moy_classe_array[$key][0] = reduceNoteSizeDecimal($value['total_note'] / $value['total_count']);
    }
    // Moyenne de toutes les matières, pour toute l'année
    $moy_classe_array[0][0] = reduceNoteSizeDecimal($moyenne_temp_annuel_total / $moyenne_temp_count_annuel_total);



    // Pour la moyenne générale
    $moyenneGeneNoteSum = $moyenneGeneCoefSum = 0;

    // Pour la moyenne par pôle
    $moyennePole = Array();

    if ($period == 0) $maxPeriod = max(array_keys($list));
    else $maxPeriod = $period;

    if ($period == 0) $minPeriod = 1;
    else $minPeriod = $period;


    // ----------------------------------------
    // Récupérations des moyennes générales et moyennes par pôle de la promo
    // ----------------------------------------
    // On récupère les moyennes des classes
    $notes_classe_temp = array();
    $query = "SELECT DISTINCT _IDmat, _IDdata, _period, _type, _total, _coef, _IDclass FROM notes_data WHERE notes_data._year = '".$year."' AND notes_data._IDclass = '".$IDclass."' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $note_type  = explode(';', $row[3]);
      $note_total = explode(';', $row[4]);
      $note_coef  = explode(';', $row[5]);

      if ($row[0] > 100000) $IDpole = getPoleIDByPMAID(getPMAIDByUVID($row[0] - 100000));
      else $IDpole = getPoleIDByPMAID($row[0]);

      $query_note = "SELECT _value, _index FROM notes_items WHERE _IDdata = '".$row[1]."' AND _value != '' ORDER BY _index ASC ";
      $result_note = mysql_query($query_note);
      while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM)) {
        // On ne prend pas les certificats ou les rattrapages
        if (($row[0] > 100000 && (isUVCertificat($row[0] - 100000)) || isUVRattrapage($row[0] - 100000)) || $row_note[0] == '' || is_null($row_note[0])) continue;
        // On stoque les données
        $notes_classe_temp[$row[6]][$IDpole][] = array(
          'note' => $row_note[0],
          'type' => $note_type[$row_note[1]],
          'max'  => $note_total[$row_note[1]],
          'coef' => $note_coef[$row_note[1]]
        );
      }
    }
    $notes_classe = array();

    // On stoque le compte des notes et des coef pour calcul ultérieur des moyennes
    foreach ($notes_classe_temp as $classID => $pole_array) {
      $notes_classe[$classID]['total_note'] = $notes_classe[$classID]['count_coef'] = 0;
      foreach ($pole_array as $poleID => $notes_temp_array) {
        $notes_classe[$classID]['poles'][$poleID]['total_note'] = $notes_classe[$classID]['poles'][$poleID]['count_coef'] = 0;
        foreach ($notes_temp_array as $index => $note_array_temp) {
          // Pour la moyenne générale
          $notes_classe[$classID]['total_note'] += (($note_array_temp['note'] / $note_array_temp['max']) * 20) * $note_array_temp['coef'];
          $notes_classe[$classID]['count_coef'] += $note_array_temp['coef'];
          // Pour la moyenne par pôle
          $notes_classe[$classID]['poles'][$poleID]['total_note'] += (($note_array_temp['note'] / $note_array_temp['max']) * 20) * $note_array_temp['coef'];
          $notes_classe[$classID]['poles'][$poleID]['count_coef'] += $note_array_temp['coef'];
        }
      }
    }

    // On calcule les moyennes
    foreach ($notes_classe as $classID => $notes_values) {
      $moyenne_classe[$classID]['general'] = ($notes_values['total_note'] / $notes_values['count_coef']);
      foreach ($notes_values['poles'] as $poleID => $notes_infos) {
        $moyenne_classe[$classID]['pole'][$poleID] = ($notes_infos['total_note'] / $notes_infos['count_coef']);
      }
    }

    // ----------------------------------------
    // Affichage des notes
    // ----------------------------------------
    $compteur_de_notes_total = 0;

    // Pour chaque pôle
    foreach ($note_array as $poleID => $poleData) {
      $nbColumnShownAfterMoyennePole = 0;
      if (getParam('afficherNomPolesBleuBulletin')) $pole_color= 'color: blue;';
      else $pole_color = '';

      $toReturn .= '<tr class="pole_name_row">';

        // On affiche le nom du pôle
        $toReturn .= '<td style="text-align: left; font-weight: bold;"><span style="'.$pole_color.'">'.stripslashes(mb_strtoupper(getPoleNameByIdPole($poleID))).'</span></td>';
        if (getParam('afficherMoyennePoleEnBleuBulletin')) $textColor = 'color: blue;';
        else $textColor = '';

        if ($period != 0)
        {
          if (getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) $toReturn .= "<td></td>"; // Nom de la période
          $toReturn .= "<td></td>"; // Nb notes
          $toReturn .= '<td style="font-weight: bold;"><span>*moyenne_'.$poleID.'*</span></td>'; // Moyenne
          if (getParam('afficherMoyenneClasseBulletin')) $toReturn .= '<td></td>'; // Moyenne classe
        }
        else
        {
          if (!getParam('afficherQueLaPeriodeTotaleBulletin') && ((getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) || getParam('afficherMoyenneParPeriodeBulletin'))) {
            foreach (json_decode(getParam('periodeList'), TRUE) as $key => $value) {
              if (getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) $toReturn .= "<td></td>"; // Nom de la période (si détail des notes)
              if (getParam('afficherMoyenneParPeriodeBulletin')) $toReturn .= '<td></td>'; // Moyenne de la période
              if (getParam('afficherMoyenneClasseBulletin')) $toReturn .= '<td></td>'; // Moyenne de la classe sur la période
            }
          }

          if ((getParam('afficherDetailNoteAllPeriodeBulletin') && $period == 0) || (getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0)) $toReturn .= '<td></td>'; // Détail des notes sur l'année
          if (getParam('afficherCompteurNotesMatBulletin')) $toReturn .= "<td></td>"; // Nb notes
          $toReturn .= '<td style="font-weight: bold;"><span style="'.$textColor.'">*moyenne_'.$poleID.'*</span></td>'; // Moyenne
          if (getParam('afficherMoyenneClasseBulletin')) $toReturn .= '<td>'.makeNote($moyenne_classe[$IDclass]['pole'][$poleID], 2).'</td>'; // Moyenne Classe
          if (getParam('afficherRattrapagePolesBulletin')) $toReturn .= '<td style="font-weight: bold;"><span class="ratt_force_pass" mat="p_'.$poleID.'">*validate_'.$poleID.'*</span></td>'; // Rattrapage
          else $toReturn .= '<td></td>';
          $toReturn .= "<td></td>"; // Note rattrapage
        }
      $toReturn .= '</tr>';
      // Pour la moyenne par pôle
      $moyennePoleNoteSum = $moyennePoleCoefSum = 0;
      $pole_note_inf_sept[$poleID] = false;


      // Pour chaque matière:
      foreach ($poleData as $key_mat => $value_mat)
      {

        if ($key_mat > 100000) $isUV = true; else $isUV = false;


        $toReturn .= "<tr>";
          if (!$isUV) $matName = stripslashes(getMatNameByIdMat(getMatIDByPMAID($key_mat)));
          else $matName = 'UV - '.stripslashes(getUVNameByUVID(($key_mat - 100000)));

          if (!getParam('afficherSeparationPolesBulletin')) $toReturn .= "<td style='text-align: left; font-weight: bold;'>".stripslashes(getPoleNameByIdPole($poleID)." - ".getMatNameByIdMat(getMatIDByPMAID($key_mat)));
          else $toReturn .= "<td style='text-align: left; font-weight: bold; padding-left: 20px;'>".$matName;

          if (getParam('afficherProfsBulletin')) {
            $toReturn .= '&nbsp;/&nbsp;<span style="font-size: '.getParam('tailleAffichageNomProfsBulletin').'px;">';
            $already_showed_prof_count = 0;
            if ($isUV) $ID_matiere_prof = getPMAIDByUVID($key_mat - 100000); else $ID_matiere_prof = $key_mat;
            foreach (getPMASyllabusProfNameList($ID_matiere_prof) as $value) {
              // if ($already_showed_prof_count > 0) $toReturn .= '&nbsp;|&nbsp;';
              if ($already_showed_prof_count == 1 && getParam('affichageProfsPointsSuspensionBulletin')) $toReturn .= '...';	// Étant donné que l'on affiche qu'un prof alors on met des points de suspenssions
              $already_showed_prof_count++;
              if ($already_showed_prof_count > 1) continue;			// On affiche qu'un prof
              $toReturn .= $value;
            }
            $toReturn .= '</span>';
          }
          $toReturn .= '</td>';

          $sommeNotesWithCoef = $sommeCoefs = $compteur_de_notes = 0;
            // Pour chaque période sélectionnée
            for ($i = $minPeriod; $i <= $maxPeriod; $i++)
            {
              // Pour la moyenne par période par matiere
              $moyennePeriodeMatNoteSum = $moyennePeriodeMatCoefSum = 0;

              $value_periode = $value_mat[$i];
              if (getParam('afficherDetailNoteAllPeriodeBulletin') && $period == 0)
                for ($j = $minPeriod; $j <= $maxPeriod; $j++)
                  if ($i != $j)
                    foreach ($value_mat[$j] as $key => $value)
                      array_push($value_periode, $value);

              // Si on ne souhaite pas afficher la colonne du second semestre:
              if ((getParam('afficherDetailNoteAllPeriodeBulletin') || (getParam('afficherDetailNoteParPeriodeBulletin') && getParam('afficherQueLaPeriodeTotaleBulletin') )) && $minPeriod != $i && $period == 0) continue;

              if ((getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) || (getParam('afficherDetailNoteAllPeriodeBulletin') && $period == 0)) $toReturn .= '<td class="student_datas">';

                // Pour chaque notes
                foreach ($value_periode as $key_index => $value_index)
                {
                  $notes_values = explode(';', $value_index);

                  if ((getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) || (getParam('afficherDetailNoteAllPeriodeBulletin') && $period == 0)) {
                    if (getParam('afficherTypeNoteBulletin')) $note_type_text = getNoteTypeCodeFromTypeID($notes_values[1]); else $note_type_text = '';
                    if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && $notes_values[0] < 10) $toReturn .= '<span style="color: red;">';
                    $toReturn .= makeNote($notes_values[0], 2, $notes_values[2], $notes_values[3], $note_type_text, 1)."&nbsp;&nbsp;";
                    if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && $notes_values[0] < 10) $toReturn .= '</span>';
                  }
                  $compteur_de_notes++;
                  $compteur_de_notes_total++;

                  if ($isUV) $currentCoef = getUVCoefByUVID($key_mat - 100000);
                  else $currentCoef = $notes_values[3];

                  // Calcul de la moyenne par matière
                  $sommeNotesWithCoef += (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
                  $sommeCoefs += $currentCoef;

                  // Pour la moyenne par période
                  $moyennePeriodNoteSum[$i] += (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
                  $moyennePeriodCoefSum[$i] += $currentCoef;

                  // Pour la moyenne par période par matiere
                  $moyennePeriodeMatNoteSum += (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
                  $moyennePeriodeMatCoefSum += $currentCoef;

                  // Pour la moyenne générale
                  $moyenneGeneNoteSum += (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
                  $moyenneGeneCoefSum += $currentCoef;

                  // Pour la moyenne par pôle affichée
                  $moyennePoleNoteSum += (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
                  $moyennePoleCoefSum += $currentCoef;


                  // Pour la moyenne par pôle
                  $tempArray = array();
                  $tempArray['note'] = (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
                  $tempArray['coef'] = $currentCoef;
                  $moyennePole[$poleID][] = $tempArray;
                  $moyennePoleByMat[$poleID][$key_mat][] = $tempArray; // Sert au calcul de la moyenne par pole en prenant en compte les rattrapages

                }
              if ((getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) || (getParam('afficherDetailNoteAllPeriodeBulletin') && $period == 0)) $toReturn .= "</td>";


              // Si on affiche la moyenne de chaque période de chaque matière sur le bulletin en vue annuelle
              if (getParam('afficherDetailNoteParPeriodeBulletin') || getParam('afficherMoyenneParPeriodeBulletin')) {
                $note_temp = makeNote($moyennePeriodeMatNoteSum / $moyennePeriodeMatCoefSum, 2, $sur);
                if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && reduceNoteSizeDecimal($moyennePeriodeMatNoteSum / $moyennePeriodeMatCoefSum) < 10) $note_temp = '<span style="color: red;">'.$note_temp.'</span>';

                if (!$moyennePeriodeMatCoefSum) $toReturn .= '<td></td>';
                elseif (getParam('afficherMoyenneParPeriodeBulletin') && $period == 0 && reduceNoteSizeDecimal($moyennePeriodeMatNoteSum / $moyennePeriodeMatCoefSum) != '') $toReturn .= '<td class="student_datas">'.$note_temp.'</td>';
                elseif (getParam('afficherMoyenneParPeriodeBulletin') && $period == 0) $toReturn .= '<td class="student_datas"></td>';
              }


              if (!getParam('afficherQueLaPeriodeTotaleBulletin') && (getParam('afficherDetailNoteParPeriodeBulletin') || getParam('afficherMoyenneParPeriodeBulletin'))) {
                // Si on affiche la moyenne de la classe sur la vue annuelle
                $note_temp = makeNote($moy_classe_array[$i][$key_mat], 2, $sur);
                if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && $moy_classe_array[$i][$key_mat] < 10) $note_temp = '<span style="color: red;">'.$note_temp.'</span>';
                // else $note_temp = makeNote($moy_classe_array[$i][$key_mat]);
                if (getParam('afficherMoyenneClasseBulletin') && $period == 0 && $moy_classe_array[$i][$key_mat] != '') $toReturn .= '<td>'.$note_temp.'</td>';
                elseif (getParam('afficherMoyenneClasseBulletin') && $period == 0) $toReturn .= '<td></td>';
              }

            }
            // Nombre de notes
            if (getParam('afficherCompteurNotesMatBulletin') || $period != 0) $toReturn .= "<td>".$compteur_de_notes."</td>";

            // Moyenne
            $moyenne = $sommeNotesWithCoef / $sommeCoefs;
            if ($moyenne < 7 && $moyenne) $pole_note_inf_sept[$poleID] = true;
            $note_temp = makeNote($moyenne, 2, $sur);
            if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && $moyenne < 10) $note_temp = '<span style="color: red;">'.$note_temp.'</span>';

            if (!$sommeCoefs) $toReturn .= '<td></td>';
            elseif (!is_null($moyenne) && isset($moyenne)) $toReturn .= '<td style="font-weight: bold;" class="student_datas">'.$note_temp."</td>";
            else $toReturn .= '<td></td>';

            // Si on affiche la moyenne de la classe
            if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && $moy_classe_array[0][$key_mat] < 10) $note_color = 'color: red;';
            else $note_color = '';
            $note_temp = makeNote($moy_classe_array[0][$key_mat], 2, $sur);
            if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && $moy_classe_array[0][$key_mat] < 10) $note_temp = '<span style="color: red;">'.$note_temp.'</span>';
            // else $note_temp = makeNote($moy_classe_array[0][$key_mat], 2, $sur);
            if (getParam('afficherMoyenneClasseBulletin') && $moy_classe_array[0][$key_mat] != '') $toReturn .= '<td>'.$note_temp.'</td>';
            elseif (getParam('afficherMoyenneClasseBulletin')) $toReturn .= '<td></td>';

            // Rattrapage
            if ($period == 0)
            {
              // if ($moyenne >= getParam('note_max_rattrapage')) $toReturn .= "<td></td>";

              $force_validate = $force_validate_class = false;
              $query = 'SELECT * FROM notes_rattrapage WHERE _matt_validation = 1 AND _IDeleve = '.$IDeleve.' AND _year = '.$year.' AND _period = '.$period.' AND _ID_pma = '.$key_mat;
              $result = mysql_query($query);
              while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
                $force_validate = true;
                $force_validate_class = 'ratt_force_pass';
              }

              // Si on passe au rattrapage
              if ($moyenne >= getParam('note_min_rattrapage') && $moyenne < getParam('note_max_rattrapage') && !$force_validate)
              {
                if (getParam('afficherCheckOuTextRattrapageBulletin') == 'check') $toReturn .= "<td style=\"text-align: center;\" class=\"ratt_force_pass\" mat=\"".$key_mat."\"><i class=\"fa fa-check\"></i></td>";
                elseif (getParam('afficherCheckOuTextRattrapageBulletin') == 'valide') $toReturn .= '<td style="text-align: center;" class="ratt_force_pass" mat="'.$key_mat.'">Non validé</td>';
                else $toReturn .= "<td style=\"text-align: center;\" class=\"ratt_force_pass\" mat=\"".$key_mat."\">Oui</td>";

                if (!$isUV && !is_null(getPMARattNote($key_mat, $IDeleve))) $toReturn .= '<td>'.makeNote(getPMARattNote($key_mat, $IDeleve), 2, getPMARattNoteMax($key_mat, $IDeleve), getPMARattCoef($key_mat, $IDeleve), '', 1).'</td>';
                elseif (!is_null(getUVRattNote(($key_mat - 100000), $IDeleve))) $toReturn .= '<td>'.makeNote(getUVRattNote(($key_mat - 100000), $IDeleve)).'</td>';
                else $toReturn .= '<td></td>';
              }
              else
              {
                if ($force_validate) $force_validate = '<sup>*</sup>';
                if ($isPrint) $force_validate = '';

                if (getParam('afficherCheckOuTextRattrapageBulletin') == 'check') $toReturn .= '<td></td>';
                elseif (getParam('afficherCheckOuTextRattrapageBulletin') == 'valide') $toReturn .= '<td style="text-align: center;" class="'.$force_validate_class.'" mat="'.$key_mat.'">Validé'.$force_validate.'</td>';
                else $toReturn .= "<td style=\"text-align: center;\" class=\"".$force_validate_class."\" mat=\"".$key_mat."\">Non".$force_validate."</td>";
                $toReturn .= '<td></td>';
              }
            }
        $toReturn .= '</tr>';
      }
    }


    // On calcule la moyenne de chaque pôles en vérifiant la moyenne de chaque matière pour un potentiel rattrapage
    foreach ($moyennePoleByMat as $poleID => $poleData) {
      $temp_pole_count = $temp_pole_total = 0;
      foreach ($poleData as $pmaID => $matData) {
        $temp_mat_count = $temp_mat_total = 0;
        foreach ($matData as $value) {
          $temp_mat_total += $value['note'];
          $temp_mat_count += $value['coef'];
        }
        $temp_mat = $temp_mat_total / $temp_mat_count;
        if ($temp_mat >= getParam('note_min_rattrapage') && $temp_mat < getParam('note_max_rattrapage') && !is_null(getPMARattNote($pmaID, $IDeleve)) && $pmaID < 100000) {
          $temp_pole_total += ((getPMARattNote($pmaID, $IDeleve) / getPMARattNoteMax($pmaID, $IDeleve)) * 20) * getPMARattCoef($pmaID, $IDeleve);
          $temp_pole_count += getPMARattCoef($pmaID, $IDeleve);
        }
        else {
          $temp_pole_total += $temp_mat_total;
          $temp_pole_count += $temp_mat_count;
        }

      }
      $moyennePole[$poleID] = $temp_pole_total / $temp_pole_count;
    }

    // ----------------------------------------
    // Moyenne
    // ----------------------------------------
    if (getParam('calculMoyenneGeneraleNotesOuPolesBulletin') == 'poles') {
      $temp_count = $temp_total = 0;
      foreach ($moyennePole as $key => $value) {
        $temp_total += $value;
        $temp_count++;
      }
      $moyenneGene = $temp_total / $temp_count;
    }
    else {
      // Calcul de la moyenne générale en prenant en compte les notes de rattrapage
      $moyenne_gene_temp = array('notes' => 0, 'coefs' => 0);

      foreach ($moyennePoleByMat as $poleID => $poleData) {
        $temp_pole_count = $temp_pole_total = 0;
        foreach ($poleData as $pmaID => $matData) {
          $temp_mat_count = $temp_mat_total = 0;
          foreach ($matData as $value) {
            if ($value['coef'] != 0) {
              $temp_mat_total += $value['note'];
              $temp_mat_count += $value['coef'];
            }
          }
          $temp_mat = $temp_mat_total / $temp_mat_count;
          if ($temp_mat >= getParam('note_min_rattrapage') && $temp_mat < getParam('note_max_rattrapage') && !is_null(getPMARattNote($pmaID, $IDeleve)) && getPMARattCoef($pmaID, $IDeleve) > 0 && $pmaID < 100000) {
            $ratt_coef = getPMARattCoef($pmaID, $IDeleve);
            if ($ratt_coef != 0) {
              $moyenne_gene_temp['notes'] += ((getPMARattNote($pmaID, $IDeleve) / getPMARattNoteMax($pmaID, $IDeleve)) * 20) * $ratt_coef;
              $moyenne_gene_temp['coefs'] += getPMARattCoef($pmaID, $IDeleve);
            }

          }
          else {
            $moyenne_gene_temp['notes'] += $temp_mat_total;
            $moyenne_gene_temp['coefs'] += $temp_mat_count;
          }
        }
      }
      $moyenneGene = $moyenne_gene_temp['notes'] / $moyenne_gene_temp['coefs'];
    }

    if (getParam('afficherGrisLigneMoyenneBulletin')) $rowColor = 'background-color:#C0C0C0;';
    else $rowColor = '';
    $toReturn .= '<tr style="border-top: 1px solid grey; font-weight: bold; '.$rowColor.'">';
      $toReturn .= '<td style="text-align: left;">Moyenne : </td>';
      if ($period != 0)
      {
        if (getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) $toReturn .= '<td></td>'; // Période
        $toReturn .= '<td>'.$compteur_de_notes_total.'</td>'; // Nb notes
        $toReturn .= '<td>'.makeNote($moyennePeriodNoteSum[$period] / $moyennePeriodCoefSum[$period], 2, $sur).'</td>'; // Moyenne
        if (getParam('afficherMoyenneClasseBulletin')) $toReturn .= '<td>'.makeNote($moy_classe_array[$period][0], 2, $sur).'</td>'; // Moyenne classe
      }
      else
      {
        if (!getParam('afficherQueLaPeriodeTotaleBulletin') && ((getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) || getParam('afficherMoyenneParPeriodeBulletin'))) {
          foreach ($listPeriod as $key => $value) {
            if (getParam('afficherDetailNoteParPeriodeBulletin') && $period != 0) $toReturn .= '<td></td>'; // Nom de la période
            if (getParam('afficherMoyenneParPeriodeBulletin')) $toReturn .= '<td>'.makeNote($moyennePeriodNoteSum[$key] / $moyennePeriodCoefSum[$key], 2, $sur).'</td>'; // Moyenne de la période
            if (getParam('afficherMoyenneClasseBulletin')) $toReturn .= '<td>'.makeNote($moy_classe_array[$key][0], 2, $sur).'</td>'; // Moyenne classe
          }
        }
        if ((getParam('afficherDetailNoteAllPeriodeBulletin')) || (getParam('afficherDetailNoteParPeriodeBulletin') && getParam('afficherQueLaPeriodeTotaleBulletin')))
          $toReturn .= '<td></td>'; // Notes

        if (getParam('afficherCompteurNotesMatBulletin')) $toReturn .= '<td>'.$compteur_de_notes_total.'</td>'; // Nb notes
        $toReturn .= '<td>'.makeNote($moyenneGene, 2, $sur).'</td>'; // Moyenne
        if (getParam('afficherMoyenneClasseBulletin')) $toReturn .= '<td>'.makeNote($moy_classe_array[0][0], 2, $sur).'</td>'; // Moyenne classe
        $toReturn .= '<td></td>'; // Rattrapage
        $toReturn .= '<td></td>'; // Note rattrapage
      }
    $toReturn .= '</tr>';

  $toReturn .= '</table>';
  // On met les moyennes des pôles dans un span invisible pour les replacer après
  foreach ($moyennePole as $poleID => $poleMoyenne) {

    $force_validate = false;
    $force_validate_text = '';
    $query = 'SELECT * FROM notes_rattrapage WHERE _matt_validation = 1 AND _IDeleve = '.$IDeleve.' AND _year = '.$year.' AND _period = '.$period.' AND _IDpole = '.$poleID;
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $force_validate = true;
      if (!$isPrint) $force_validate_text = '<sup>*</sup>';
    }
    if (($poleMoyenne >= 10 && !$pole_note_inf_sept[$poleID]) || $force_validate) {
      if (getParam('afficherCheckOuTextRattrapageBulletin') == 'check') $pole_validation = '';
      elseif (getParam('afficherCheckOuTextRattrapageBulletin') == 'valide') $pole_validation = 'Validé'.$force_validate_text;
      else $pole_validation = 'Non'.$force_validate_text;
    }
    else {
      if (getParam('afficherCheckOuTextRattrapageBulletin') == 'check') $pole_validation = 'check';
      elseif (getParam('afficherCheckOuTextRattrapageBulletin') == 'valide') $pole_validation = 'Non validé';
      else $pole_validation = 'Oui';
    }

    $color = '';
    if (getParam('afficherMoyennePolesBleuBulletin')) $color = 'blue';
    if (getParam('afficherNotesMoinsDeDixEnRougeBulletin') && $poleMoyenne < 10) $color = 'red';
    if (getParam('afficherSur20MoyenneBulletin')) $sur = 20; else $sur = 0;
    $moyenneToShow = '<span style="color: '.$color.';">'.makeNote($poleMoyenne, 2, $sur).'</span>';

    $toReturn = str_replace('*moyenne_'.$poleID.'*', $moyenneToShow, $toReturn);
    $toReturn = str_replace('*validate_'.$poleID.'*', $pole_validation, $toReturn);
  }
  $temp = array();
  $temp['general'] = $moyenneGene;
  $temp['pole'] = $moyennePole;

  return array('table' => $toReturn, 'data' => array('eleve' => $temp, 'classe' => $moyenne_classe[$IDclass]));
}





// ---------------------------------------------------------------------------
// Fonction: Formate la note avec le bon affichage
// IN:		   La note (FLOAT) et le nombre de décimales voulu (défaut 2) (INT) et la note max pour l'exam (defaut 20) (INT)
// OUT: 		 La note formatée (TEXT)
// ---------------------------------------------------------------------------
function makeNote($note, $decimals = 2, $sur = 20, $coef = 0, $noteType = '', $forceShowCoef = 0) {
	if (($note != '' && !is_null($note)) || $note == 0) {
		$note = number_format(round($note, $decimals), $decimals);
		if ($sur) $note = $note.'/'.$sur;
    if ($coef || $forceShowCoef) $note .= '<sup>'.$coef.'</sup>';
    if ($noteType) $note .= '<sub>'.$noteType.'</sub>';
		return $note;
	}
	return ''; // Si la note n'existe pas, alors on envois rien
}



?>
