<?php
// ---------------------------------------------------------------------------
// Ne pas apeller cette page avec include/fonction.php!!!!!!!
// ---------------------------------------------------------------------------
// include('include/notes.php');
include('../../notes.php');
// error_reporting(E_ERROR | E_WARNING | E_PARSE);

// error_reporting(E_ALL);
//    ini_set("display_errors", 1);

// ---------------------------------------------------------------------------
// Fonction: S'occupe de faire les différents traitements pour passer à
//           l'année suivante
// IN:		   -
// OUT: 		 Est-ce que l'opération à réussie (TRUE/FALSE)
// ---------------------------------------------------------------------------
function changeToNextYear() {
  // $time_start = microtime(true);

  $currentYear = getParam('START_Y');
  // echo "CURRENT YEAR: ".$currentYear."<br>";
  $newYear = $currentYear + 1;
// $currentYear = 2019;


  // On stoque les ID des examens de type Certificats pour ne pas revérifier à chaque fois
  $liste_certificats = array();
  $query_exam = "SELECT _ID_exam FROM campus_examens WHERE _type = 3 OR _type = 4 ";
  $result_exam = mysql_query($query_exam);
  while ($row_exam = mysql_fetch_array($result_exam, MYSQL_NUM)) $liste_certificats[] = $row_exam[0];

  // On stoque les liaisons pole -> pma pour ne pas les récupérer à chaque fois
  $pole_pma = array();
  $query = "SELECT `_ID_pole`, `_ID_pma` FROM `pole_mat_annee` WHERE 1 ";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) $pole_pma[$row[1]] = $row[0];

  // On stoque dans un tableau les correspondances POLE ID -> UV ID pour ne pas les récupérer à chaque fois
  $exam_pole = array();
  $query = "SELECT `_ID_pma`, `_ID_exam` FROM `campus_examens` WHERE 1 ";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) $exam_pole[$row[1]] = $pole_pma[$row[0]];

  // On stoque les paramètres pour ne pas avoir à les récupérer à chaque fois
  $param_list = array();
  $query = "SELECT _code, _valeur FROM parametre WHERE _IDuser = 0 ";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) $param_list[$row[0]] = $row[1];

  // On stoque les ID des examens de type rattrapage pour ne pas revérifier à chaque fois
  $liste_rattrapage = array();
  $query = "SELECT _ID_exam FROM campus_examens WHERE _type = 2 OR _type = 4 AND _ID_parent ";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) $liste_rattrapage = $row[0];

  // getNiveauNameByNumNiveau
  $niveauNameByNumNiveau = json_decode($param_list['annee-niveau'], TRUE);

  // getUserClassByUserID
  $userClassByUserID = array();
  $query = "SELECT _ID, _IDclass FROM user_id WHERE _adm = 1";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) $userClassByUserID[$row[0]] = $row[1];




  // 1. On enregistre les syllabus dans la table campus_syllabus_archive

    // On récupère tous les syllabus
    $query = "SELECT _IDPMA, _idUser, _periode_1, _periode_2, _periode_total FROM campus_syllabus WHERE _visible = 'O' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      // On les enregistres dans l'archive
      $query_insert  = "INSERT INTO `campus_syllabus_archive`(`_IDx`, `_year`, `_IDpma`, `_IDprof`, `_periode_1`, `_periode_2`, `_periode_tot`) ";
      $query_insert .= "VALUES (NULL, '".$currentYear."', '".$row[0]."', '".$row[1]."', '".$row[2]."', '".$row[3]."', '".$row[4]."') ";
      mysql_query($query_insert);
    }


  // 2. On sauvegarde les logs des users:
    $list_eleve = Array();
    $query = "SELECT _ID, _IDclass from user_id where _IDclass IN (SELECT _IDclass FROM campus_classe WHERE _visible = 'O') ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $list_eleve[$row[0]] = $row[1];
    }

    $list = json_decode($param_list['periodeList'], TRUE);


    // On récupère les moyennes des classes pour les stocker dans la table user_log
    $notes_classe_temp = array();
    $query = "SELECT DISTINCT _IDmat, _IDdata, _period, _type, _total, _coef, _IDclass FROM notes_data WHERE notes_data._year = '".$currentYear."' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $note_type  = explode(';', $row[3]);
      $note_total = explode(';', $row[4]);
      $note_coef  = explode(';', $row[5]);

      if ($row[0] > 100000) $IDpole = $pole_pma[$exam_pole[$row[0] - 100000]];
      else $IDpole = $pole_pma[$row[0]];

      $query_note = "SELECT _value, _index FROM notes_items WHERE _IDdata = '".$row[1]."' AND _value != '' ORDER BY _index ASC ";
      $result_note = mysql_query($query_note);
      while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM)) {
        // On ne prend pas les certificats ou les rattrapages
        if (($row[0] > 100000 && (in_array(($row[0] - 100000), $liste_certificats)) || is_array(($row[0] - 100000), $liste_rattrapage)) || $row_note[0] == '' || is_null($row_note[0])) continue;
        // On stoque les données
        $notes_classe_temp[$row[6]][$IDpole][] = array(
          'note' => $row_note[0],
          'type' => $note_type[$row_note[1]],
          'max'  => $note_total[$row_note[1]],
          'coef' => $note_coef[$row_note[1]]
        );
      }
    }

    $notes_classe = $moyenne_classe = array();

    // On stoque le compte des notes et des coef pour calcul ultérieur des moyennes
    foreach ($notes_classe_temp as $classID => $pole_array) {
      $notes_classe[$classID]['total_note'] = $notes_classe[$classID]['count_coef'] = 0;
      foreach ($pole_array as $poleID => $notes_temp_array) {
        $notes_classe[$classID]['poles'][$poleID]['total_note'] = $notes_classe[$classID]['poles'][$poleID]['count_coef'] = 0;
        foreach ($notes_temp_array as $index => $note_array) {
          // Pour la moyenne générale
          $notes_classe[$classID]['total_note'] += (($note_array['note'] / $note_array['max']) * 20) * $note_array['coef'];
          $notes_classe[$classID]['count_coef'] += $note_array['coef'];
          // Pour la moyenne par pôle
          $notes_classe[$classID]['poles'][$poleID]['total_note'] += (($note_array['note'] / $note_array['max']) * 20) * $note_array['coef'];
          $notes_classe[$classID]['poles'][$poleID]['count_coef'] += $note_array['coef'];
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
    // Récupérations des moyennes générales et moyennes par pôle de la promo
    // ----------------------------------------
    // On récupère les moyennes des classes
    $notes_classe_temp = array();
    $query = "SELECT DISTINCT _IDmat, _IDdata, _period, _type, _total, _coef, _IDclass FROM notes_data WHERE notes_data._year = '".$currentYear."' AND notes_data._IDclass = '".$IDclass."' ";
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
        if (($row[0] > 100000 && ( in_array(($row[0] - 100000), $liste_certificats)) || in_array(($row[0] - 100000), $liste_certificats)) || $row_note[0] == '' || is_null($row_note[0])) continue;
        // On stoque les données
        $notes_classe_temp[$row[6]][$IDpole][] = array(
          'note' => $row_note[0],
          'type' => $note_type[$row_note[1]],
          'max'  => $note_total[$row_note[1]],
          'coef' => $note_coef[$row_note[1]]
        );
      }
    }
    $notes_classe = $moyenne_classe = array();

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








    foreach ($list_eleve as $key => $value) {
// if ($key != 3344) continue;
      // On vide pour ne pas avoir de résidus d'autres élèves
      if (isset($moyennePoleByMat)) unset($moyennePoleByMat);
      $IDeleve = $key;
      $IDclass = $value;
$period = 0;
      // Même partie que le bulletin:
      $list = json_decode($param_list['periodeList'], TRUE);

      // ----------------------------------------
      // Récupération des notes
      // ----------------------------------------
      $temp = array();
      // Pour chaque matière
      $query = "SELECT DISTINCT notes._IDmat, notes._IDdata, notes._period, notes._type, notes._total, notes._coef, pole_mat_annee._ID_pole, pole._name FROM notes_data notes INNER JOIN pole_mat_annee pole_mat_annee INNER JOIN pole pole WHERE (pole_mat_annee._ID_pma = notes._IDmat OR notes._IDmat > 100000) AND pole_mat_annee._ID_pole = pole._ID AND ";
      $query .= "notes._year = '$currentYear' AND notes._IDdata IN (SELECT _IDdata FROM notes_items WHERE _IDeleve = '$IDeleve') ";
      if ($period != 0) $query .= "AND notes._period = '$period' ";
      $query .= "ORDER BY pole._name ASC ";
// echo $query;
      $note_array = Array();
      $note_classe_array = Array();
      $result = mysql_query($query);
      while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
        // echo 'newMat<br>';
        $note_type  = explode(';', $row[3]);
        $note_total = explode(';', $row[4]);
        $note_coef  = explode(';', $row[5]);

        $IDmat = $row[0];

        if ($row[0] > 100000) $poleID = $exam_pole[($row[0] - 100000)];
        else $poleID = $pole_pma[$row[0]];

        // Si on ne veux pas afficher les examens (UV) dans le bulletin:
        if (!$param_list['afficherExamensDansBulletin'] && $row[0] > 100000) continue;
        if ($row[0] > 100000 && in_array(($row[0] - 100000), $liste_rattrapage)) continue;





        // Pour l'élève sélectionné
        $query_note = "SELECT _value, _index FROM notes_items WHERE _IDdata = '".$row[1]."' AND _IDeleve = '".$IDeleve."' AND _value != '' ORDER BY _index ASC ";
        $result_note = mysql_query($query_note);
        while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM)) {
          $index_note = $row_note[1];
          $show_note = 1;
          if ($row[0] > 100000)
          {
            if (!in_array(($row[0] - 100000), $liste_certificats)) $show_note = 0;
            $index_note += 100000;
          }
          // Si la note est un certificat (pour lyon)
          if ($note_type[$row_note[1]] == 100) $show_note = 0;
          if ($row_note[0] != "" && $show_note == 1) $note_array[$poleID][$IDmat][$row[2]][$index_note] = $row_note[0].";".$note_type[$row_note[1]].";".$note_total[$row_note[1]].";".$note_coef[$row_note[1]];
        }

        // --------- PARTIE QUI PREND DU TEMPS --------- \\
        // Pour tous les élèves de la promo
        $compteur = 0;
        $result_note = mysql_query("SELECT _value, _index FROM notes_items WHERE _IDdata = '".$row[1]."' ORDER BY _index ASC ");
        while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM)) {
          if (($row[0] > 100000 && in_array(($row[0] - 100000), $liste_certificats)) || $note_type[$row_note[1]] == 100 || $row_note[0] == '') continue;		// Si la note est un certificat ou si la note est vide
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
      if (isset($moyennePole)) unset($moyennePole);
      $moyennePole = array();
      if ($period == 0) {
        $maxPeriod = max(array_keys($list));
        $minPeriod = 1;
      }
      else $maxPeriod = $minPeriod = $period;




      // ----------------------------------------
      // Gestion des notes
      // ----------------------------------------
      $compteur_de_notes_total = 0;

      // Pour chaque pôles
      foreach ($note_array as $poleID => $poleData) {
        // Pour chaque matière:
        foreach ($poleData as $key_mat => $value_mat)
        {
          if ($key_mat > 100000) $isUV = true; else $isUV = false;

          $sommeNotesWithCoef = $sommeCoefs = $compteur_de_notes = 0;
          // Pour chaque période sélectionnée
          for ($i = $minPeriod; $i <= $maxPeriod; $i++)
          {
            // Pour la moyenne par période par matiere
            $value_periode = $value_mat[$i];
            if ($param_list['afficherDetailNoteAllPeriodeBulletin'] && $period == 0)
              for ($j = $minPeriod; $j <= $maxPeriod; $j++)
                if ($i != $j)
                  foreach ($value_mat[$j] as $key => $value)
                    array_push($value_periode, $value);

            // Si on ne souhaite pas afficher la colonne du second semestre:
            if (($param_list['afficherDetailNoteAllPeriodeBulletin'] || ($param_list['afficherDetailNoteParPeriodeBulletin'] && $param_list['afficherQueLaPeriodeTotaleBulletin'] )) && $minPeriod != $i && $period == 0) continue;
            // Pour chaque notes
            foreach ($value_periode as $key_index => $value_index)
            {
              $notes_values = explode(';', $value_index);

              $compteur_de_notes++;
              $compteur_de_notes_total++;

              if ($isUV) $currentCoef = getUVCoefByUVID($key_mat - 100000);
              else $currentCoef = $notes_values[3];

              // Calcul de la moyenne par matière
              $sommeNotesWithCoef += (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
              $sommeCoefs += $currentCoef;

              // Pour la moyenne générale
              $moyenneGeneNoteSum += (($notes_values[0] / $notes_values[2]) * 20) * $currentCoef;
              $moyenneGeneCoefSum += $currentCoef;

              // Pour la moyenne par pôle
              $tempArray = Array();
              $tempArray['note'] = (($notes_values[0] / $notes_values[2]) * 20) * $notes_values[3];
              $tempArray['coef'] = $notes_values[3];
              $moyennePole[$poleID][$key_mat][] = $tempArray;
              $moyennePoleByMat[$poleID][$key_mat][] = $tempArray; // Sert au calcul de la moyenne par pole en prenant en compte les rattrapages
            }
          }
          // Moyenne
          $moyenne = $sommeNotesWithCoef / $sommeCoefs;
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

          if ($pmaID < 100000 && $temp_mat >= $param_list['note_min_rattrapage'] && $temp_mat < $param_list['note_max_rattrapage'] && getPMARattNote($pmaID, $IDeleve)) {
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
// echo '<pre>';
// print_r($moyennePole);
// echo '</pre>';

      // Moyenne générale
      if ($param_list['calculMoyenneGeneraleNotesOuPolesBulletin'] == 'poles') {
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

      $moyenne_log = array();
      $moyenne_log['moyene_generale'] = $moyenneGene;
      $moyenne_log['moyenne_pole']		= $moyennePole;
      $currentNiveauForLog = substr(getNiveauNameByUserID($IDeleve), 0, 1);
// $currentNiveauForLog = $currentNiveauForLog - 1;

      // $moyenne_log['moyenne_classe']  = $moyenne_classe[getUserClassIDByUserID($IDeleve)];
      $moyenne_log['moyenne_classe']  = $moyenne_classe[$userClassByUserID[$IDeleve]];
      $moyenne_log = json_encode($moyenne_log);
      // On vérifie que l'élément n'existe pas dans la BDD:
      $query = "SELECT _ID FROM user_log WHERE _IDuser = '".$IDeleve."' AND _year = '".$currentYear."' ";
      $result = mysql_query($query);
      $num_rows = mysql_num_rows($result);
      // Si l'élément n'existe pas encore, alors on le crée
      if ($num_rows == 0)
      {
        if ($currentNiveauForLog == "") $currentNiveauForLog = 0;
        $query_log = "INSERT INTO `user_log`(`_ID`, `_IDuser`, `_year`, `_niveau`, `_attr`) VALUES (NULL, '".$IDeleve."', '".$currentYear."', '".$currentNiveauForLog."', '".$moyenne_log."') ";

      }
      // Sinon on le met à jour
      else
      {
        $query_log = "UPDATE `user_log` SET `_attr` = '".$moyenne_log."' WHERE `_IDuser` = '".$IDeleve."' AND `_year` = '".$year."' ";
      }
      mysql_query($query_log) or die('Erreur SQL !<br>'.$query_log.'<br>'.mysql_error());
    }

  // 3. On sauvegarde les paramètres dans la table parametre_archive
    $query = "SELECT _code, _valeur, _IDuser, _comm FROM parametre WHERE 1 ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $query_insert = "INSERT INTO `parametre_archive`(`_annee`, `_code`, `_valeur`, `_IDuser`, `_comm`) VALUES ('".$currentYear."', '".$row[0]."', '".$row[1]."', '".$row[2]."', '".$row[3]."') ";
      mysql_query($query_insert);
    }

  // 4. On change l'année dans les paramètres
    setParam('START_Y', $newYear);
    setParam('END_Y', ($newYear + 1));

    // On change les dates de périodes
    $current_periode = json_decode(getParam('periodeDates'), TRUE);
    $new_periode = array();
    foreach ($current_periode as $key => $value) {
      $new_periode[$key] = date('Y-m-d', strtotime($value.' + 1 year'));
    }
    setParam('periodeDates', json_encode($new_periode));
    setParam('periode_courante', 1);

  // 5. On re-calcule les années des différentes promotions
    reAssignPromotionByGraduationYear();





// $time_end = microtime(true);
//
// //dividing with 60 will give the execution time in minutes otherwise seconds
// $execution_time = ($time_end - $time_start)/60;
//
// //execution time of the script
// echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
// // if you get weird results, use number_format((float) $execution_time, 10)


}




?>
