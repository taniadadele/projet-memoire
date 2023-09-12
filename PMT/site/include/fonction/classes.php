<?php


/**
 * Fonction: Afficher le select des Classes
 * @param string $selectID 		      ID de la balise select
 * @param string $selectName        Nom de la balise select
 * @param string $selectedIDclasse  L'ID de la classe déjà sélectionné
 * @param int $onChangeSubmit	      Est-ce que l'on valide le formulaire au changement de valeur (défaut non)
 * @param string $formIDToSubmit	  L'ID du formulaire à valider si $onChangeSubmit = 1
 * @param int $canSelectTitle       Est-ce que l'on peux sélectionner le titre (qui est désactivé par défaut)
 * @param int $isDisabled           Est-ce que le select est disabled (défaut 0 = non)
 * @param string $class_to_add      Classes à ajouter au select
 * @return string  			            Le select
 */
function getClassSelect($selectID = 'IDclass', $selectName = 'IDclass', $selectedIDclasse = 0, $onChangeSubmit = 0, $formIDToSubmit = 'formulaire', $canSelectTitle = 0, $isDisabled = 0, $class_to_add = '') {
  global $mysql_link;
  $selected = $disabled = $selectDisabled = $onChange = '';
  if ($onChangeSubmit) $onChange = 'onchange="document.forms.'.$formIDToSubmit.'.submit()"';
  if ($isDisabled) $selectDisabled = 'disabled';
  $toReturn = '<select id="'.$selectID.'" class="custom-select '.$class_to_add.'" name="'.$selectName.'" '.$onChange.' '.$selectDisabled.'>';
  if (!$selectedIDclasse) $selected = 'selected';
  if (!$canSelectTitle) $disabled = 'disabled';
  $toReturn .= '<option value="0" '.$disabled.' '.$selected.'>Sélectionnez une classe</option>';

  $query = "SELECT _IDclass, _ident, _code FROM campus_classe WHERE _visible = 'O' ORDER BY _code ASC ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    if ($row[0] == $selectedIDclasse) $selected = 'selected'; else $selected = '';
    $toReturn .= '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
  }
  $toReturn .= '</select>';
  return $toReturn;
}


/**
 * Fonction: Afficher le select des Niveaux
 * @param string $selectID 		      ID de la balise select
 * @param string $selectName        Nom de la balise select
 * @param string $selectedIDniveau  L'ID du niveau déjà sélectionné
 * @param int $onChangeSubmit	      Est-ce que l'on valide le formulaire au changement de valeur (défaut non)
 * @param string $formIDToSubmit	  L'ID du formulaire à valider si $onChangeSubmit = 1
 * @param int $canSelectTitle       Est-ce que l'on peux sélectionner le titre (qui est désactivé par défaut)
 * @param int $isDisabled           Est-ce que le select est disabled (défaut 0 = non)
 * @return string  			            Le select
 */
function getNiveauSelect($selectID = 'IDpromotion', $selectName = 'IDpromotion', $selectedIDniveau = 0, $onChangeSubmit = 0, $formIDToSubmit = 'formulaire', $canSelectTitle = 0, $isDisabled = 0) {
  global $mysql_link;
  $selected = $disabled = $selectDisabled = $onChange = '';
  if ($onChangeSubmit) $onChange = 'onchange="document.forms.'.$formIDToSubmit.'.submit()"';
  $toReturn = '<select id="'.$selectID.'" class="custom-select" name="'.$selectName.'" '.$onChange.' '.$selectDisabled.'>';
    if ($selectedIDniveau == 0) $selected = 'selected';
    if ($_SESSION['CnxGrp'] != 1) $toReturn .= '<option value="0" '.$selected.'>Toutes les années</option>';
    $selected = $alreadyShown = '';
    $listeAnnee = json_decode(getParam("annee-niveau"), TRUE);

    $query = "SELECT _code FROM campus_classe WHERE _visible = 'O' ";
    $query .= "ORDER BY _code ASC ";

    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $numeroAnnee = $row[0];
      $nomAnnee = getNiveauNameByNumNiveau($row[0]);
      if ($selectedIDniveau == $numeroAnnee) $selected = 'selected';
      else $selected = '';
      $query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_year` = '".$numeroAnnee."' ";
      $result2 = mysqli_query($mysql_link, $query2);
      while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
        $query3 = "SELECT * FROM `campus_syllabus` WHERE `_IDPMA` = '".$row2[0]."' LIMIT 1 ";
        $result3 = mysqli_query($mysql_link, $query3);
        $numRows = mysqli_num_rows($result3);
        if ($numRows != 0 and strpos($alreadyShown, ';'.$numeroAnnee) === false)
        {
          $alreadyShown .= ';'.$numeroAnnee;
          $toReturn .= '<option value="'.$numeroAnnee.'" '.$selected.'>'.getNiveauNameByNumNiveau($numeroAnnee).'</option>';
        }
      }
    }
  $toReturn .= '</select>';

  return $toReturn;
}






/**
 * Donne le nom du niveau en fonction de son numéro
 *
 * Donne le nom du niveau en toutes lettres en fonction du numéro de niveau donné
 *
 * @param int var Le numéro du niveau
 * @return return string Le nom du niveau en toutes lettres
 */
function getNiveauNameByNiveauNumber($niveau)
{
  global $mysql_link;
	$list_niveau = json_decode(getParam('annee-niveau'), true);

	return $list_niveau[$niveau];
}


/**
 * Donne le numéro du niveau d'une classe
 *
 * Donne le numéro du niveau (1, 2...) en fonction de l'ID de la classe
 *
 * @param int var L'ID de la classe
 * @return return int Le numéro du niveau
 */
function getClassNiveauByClassID($classID)
{
  global $mysql_link;
	$query = "SELECT _code FROM campus_classe WHERE _IDclass = '".$classID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  return $row[0];
	}
}



/**
 * Donne un ID classe en fonction d'un niveau
 *
 * Donne l'ID d'une classe en fonction de son numéro de niveau
 *
 * @param int var Le numéro de niveau
 * @return return int L'ID de la classe
 */
function getClassIDByNiveauNumber($niveau) {
  global $mysql_link;
  $query = "SELECT `_IDclass` FROM `campus_classe` WHERE `_code` = '".$niveau."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}











?>
