<?php






/**
 * Fonction: Afficher le select des UV
 * @param string $selectID 		    ID de la balise select
 * @param string $selectName      Nom de la balise select
 * @param string $selectedIDuv    L'ID de l'UV déjà sélectionné
 * @param int $onChangeSubmit	    Est-ce que l'on valide le formulaire au changement de valeur (défaut non)
 * @param string $formIDToSubmit	L'ID du formulaire à valider si $onChangeSubmit = 1
 * @param string $classes_to_add	Les classes supplémentaires du select
 * @return string  			          Le select
 */
function getUVSelect($selectID = 'IDuv', $selectName = 'IDuv', $selectedIDuv = 0, $onChangeSubmit = 0, $formIDToSubmit = 'formulaire', $classes_to_add = '') {
  global $mysql_link;
  $toReturn = $onChange = '';
  if ($onChangeSubmit) $onChange = 'onchange="document.forms.'.$formIDToSubmit.'.submit()"';

  $libelleRattrapage = json_decode(getParam('libelleRattrapageListeExamens'), true);

  $toReturn .= '<select id="'.$selectID.'" class="custom-select '.$classes_to_add.'" name="'.$selectName.'" '.$onChange.'>';
    $toReturn .= '<option value=""></option>';
    $query  = "SELECT `exam`.`_ID_exam`, `exam`.`_nom`, `exam`.`_ID_pma`, `exam`.`_type`, `exam`.`_oral` FROM `campus_examens` `exam` LEFT JOIN `pole_mat_annee` `pma` ON `exam`.`_ID_pma` = `pma`.`_ID_pma` ORDER BY `pma`.`_ID_year` ASC, ";
    $query .= "FIELD(exam.`_type`, 1, 3, 2, 4) ASC, `exam`.`_oral` DESC ";
    $result = mysqli_query($mysql_link, $query);
    $old_annee = $old_type = $old_type_2 = 0;
    $old_oral = '';
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $select = ( $selectedIDuv == $row[0] ) ? "selected=\"selected\"" : "" ;
      $annee_name = str_replace('</sup>', '', str_replace('<sup>', '', getClassYearByPMAID($row[2])));
      if (getClassYearByPMAID($row[2]) != $old_annee) {
      if ($old_annee != 0) $toReturn .= '</optgroup></optgroup>';
        $toReturn .= '<optgroup label="'.$annee_name.'"><optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;'.$libelleRattrapage['normal'].'">';
        $old_type = 0;
        $old_oral = '';
      }
      $old_annee = getClassYearByPMAID($row[2]);

      if (isUVRattrapage($row[0]) && $old_type == 0) {
        $toReturn .= '</optgroup><optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;'.$libelleRattrapage['rattrapage'].'">';
        $old_type = 1;
      }

      // Séparation entre certificats et examens classiques
      if ($old_type_2 != $row[3]) {
        if ($row[3] == 1 || $row[3] == 2) $text = 'Examen';
        else $text = 'Certificat';
        $toReturn .= '<option disabled>&nbsp;&nbsp;&nbsp;&nbsp;--- '.$text.' ---</option>';
        $old_type_2 = $row[3];
      }

      // Séparation entre examens oraux et écrits
      if ($old_oral != $row[4]) {
        if ($row[4] == 'N') $text = 'ÉCRIT'; else $text = 'ORAL';
        $toReturn .= '<option disabled>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$text.'</option>';
        $old_oral = $row[4];
      }

      $toReturn .= '<option value="'.$row[0].'" '.$select.'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row[1].'</option>';
    }
  $toReturn .= '</select>';

  return $toReturn;
}



?>
