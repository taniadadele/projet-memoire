<?php
/***** Affecter une matiÃ¨re Ã  une classe *****/
function setMatClass($IDmat, $IDclass) {
	global $mysql_link;
	$query  = "SELECT * ";
	$query .= "FROM class_mat_user ";
	$query .= "WHERE _IDRmat = $IDmat ";
	$query .= "AND _IDRclass = $IDclass ";
	$query .= "AND _IDRuser = 0 ";
	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());

	if(mysqli_num_rows($req) == 0) // Si existe pas on ajoute la relation
	{
		$query  = "INSERT INTO class_mat_user VALUES('$IDclass', '$IDmat', '0', '') ";
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
	}
}

/***** Affecter une matiÃ¨re Ã  un prof *****/
function setMatProf($IDmat, $IDprof) {
	global $mysql_link;
	$query  = "SELECT * ";
	$query .= "FROM class_mat_user ";
	$query .= "WHERE _IDRmat = $IDmat ";
	$query .= "AND _IDRclass = 0 ";
	$query .= "AND _IDRuser = $IDprof ";
	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());

	if(mysqli_num_rows($req) == 0) // Si existe pas on ajoute la relation
	{
		$query  = "INSERT INTO class_mat_user VALUES('0', '$IDmat', '$IDprof', '') ";
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
	}
}

/***** Affecter un prof Ã  une classe *****/
function setClasseProf($IDprof, $IDclass, $attr) {
	global $mysql_link;
	$query  = "SELECT * ";
	$query .= "FROM class_mat_user ";
	$query .= "WHERE _IDRmat = 0 ";
	$query .= "AND _IDRclass = $IDclass ";
	$query .= "AND _IDRuser = $IDprof ";
	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());

	if(mysqli_num_rows($req) == 0) // Si existe pas on ajoute la relation
	{
		$query  = "INSERT INTO class_mat_user VALUES('$IDclass', '0', '$IDprof', '$attr') ";
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
	}
}

/***** RÃ©cupÃ¨re les matiÃ¨res d'une classe *****/
function getMatClass($IDclass, $lang = "FR") {
	global $mysql_link;
	$val = Array();
	$query  = "SELECT d._IDmat 'IDmat', d._titre 'titre', d._option 'option' ";
	$query .= "FROM class_mat_user cmu, campus_data d ";
	$query .= "WHERE cmu._IDRmat = d._IDmat ";
	$query .= "AND d._lang = '$lang' ";
	$query .= "AND cmu._IDRuser IS NULL ";
	$query .= "AND cmu._IDRclass = $IDclass ";
	$query .= "ORDER BY d._IDmat, d._option ";

	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());

	while($data = mysqli_fetch_assoc($req))
    {
		$val[$data["IDmat"]]["titre"] = $data["titre"];
		$val[$data["IDmat"]]["option"] = $data["option"];
    }

	return $val;
}

/***** RÃ©cupÃ¨re les matiÃ¨res d'un prof *****/
function getMatProf($IDprof, $lang = "FR") {
	global $mysql_link;
	$val = Array();
	$query  = "SELECT d._IDmat 'IDmat', d._titre 'titre' ";
	$query .= "FROM class_mat_user cmu, campus_data d ";
	$query .= "WHERE cmu._IDRmat = d._IDmat ";
	$query .= "AND d._lang = '$lang' ";
	$query .= "AND cmu._IDRclass = 0 ";
	$query .= "AND cmu._IDRuser = $IDprof ";
	$query .= "ORDER BY d._IDmat ";

	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());

	while($data = mysqli_fetch_assoc($req))
    {
		$val[$data["IDmat"]] = $data["titre"];
    }

	return $val;
}

/***** RÃ©cupÃ¨re les profs d'une classe *****/
function getClasseProf($IDclass) {
	global $mysql_link;
	$val = Array();
	$query  = "SELECT u._ID 'IDuser', u._name 'name', u._fname 'fname' ";
	$query .= "FROM class_mat_user cmu, user_id u ";
	$query .= "WHERE cmu._IDRuser = u._ID ";
	$query .= "AND cmu._IDRmat IS NULL ";
	$query .= "AND cmu._IDRclass = $IDclass ";
	$query .= "AND u._IDgrp = 2 ";
	$query .= "ORDER BY u._ID ";

	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());

	while($data = mysqli_fetch_assoc($req))
    {
		$val[$data["IDuser"]] = $data["name"]." ".$data["fname"];
    }

	return $val;
}

// ---------------------------------------------------------------------------
// Fonction: Donne le nom du niveau en fonction du numéro du niveau
// IN:		   Le numéro du niveau (INT)
// OUT: 		 Le nom du niveau (TEXT)
// ---------------------------------------------------------------------------
function getNiveauNameByNumNiveau($IDclass) {
	global $mysql_link;
	if ($IDclass == '') return '';
	$query  = "SELECT `_valeur` FROM `parametre` WHERE `_code` = 'annee-niveau' ";
	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
	while($row = mysqli_fetch_array($req))
	{
		$tableau_valeurs = json_decode($row[0], TRUE);
		$nomDeLaClasse = $tableau_valeurs[$IDclass];
	}
	return $nomDeLaClasse;
}


// ---------------------------------------------------------------------------
// Fonction: Donne le nom du niveau en fonction de l'ID de l'utilisateur
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 Le nom du niveau (TEXT)
// ---------------------------------------------------------------------------
function getNiveauNameByUserID($userID) {
	global $mysql_link;
	$query  = "SELECT `_code` ";
  $query .= "FROM `campus_classe` ";
  $query .= "WHERE `_IDclass` = (SELECT `_IDclass` FROM `user_id` WHERE `_ID` = ".$userID.") ";
	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
	while($row = mysqli_fetch_array($req))
	{
		return getNiveauNameByNumNiveau($row[0]);
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'ID du niveau en fonction de l'ID de l'utilisateur
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 L'id du niveau (INT)
// ---------------------------------------------------------------------------
function getNiveauNumberByUserID($userID) {
	global $mysql_link;
	$query  = "SELECT `_code` ";
  $query .= "FROM `campus_classe` ";
  $query .= "WHERE `_IDclass` = (SELECT `_IDclass` FROM `user_id` WHERE `_ID` = ".$userID.") ";
	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
	while($row = mysqli_fetch_array($req))
	{
		return $row[0];
	}
}



// ---------------------------------------------------------------------------
// Fonction: Donne le nom et prénom de l'utilisateur en fonction de son ID
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 Le nom et prénom de l'utilisateur (TEXT)
// ---------------------------------------------------------------------------
function getUserNameByID($idUser) {
	global $mysql_link;
	if (!$idUser) return '';
  $query  = "SELECT _name, _fname FROM user_id WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0]." ".$row[1];
  }
}


/**
 * getUserMailByID
 *
 * Donne l'adresse mail de l'utilisateur en fonction de son ID
 *
 * @param int $isUser L'ID de l'utilisateur
 * @return return string
 */
function getUserMailByID($idUser) {
	global $mysql_link;
  $query  = "SELECT _email FROM user_id WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) return $row[0];
}

// ---------------------------------------------------------------------------
// Fonction: Donne la date de naissance de l'utilisateur en fonction de son ID
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 La date de naissance (TEXT)
// ---------------------------------------------------------------------------
function getUserBirthdateByID($idUser) {
	global $mysql_link;
  $query  = "SELECT _born ";
  $query .= "FROM user_id ";
  $query .= "WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return substr($row[0], 8, 2)."/".substr($row[0], 5, 2)."/".substr($row[0], 0, 4);
  }
}

// ---------------------------------------------------------------------------
// Fonction: Donne le lieu de naissance de l'utilisateur en fonction de son ID
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 Le lieu de naissance (TEXT)
// ---------------------------------------------------------------------------
function getUserBirthPlaceByID($idUser) {
	global $mysql_link;
  $query  = "SELECT _valeur ";
  $query .= "FROM rubrique_data ";
  $query .= "WHERE _IDrubrique = '7' AND _IDdata = '".$idUser."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}

// ---------------------------------------------------------------------------
// Fonction: Vérifie si l'utilisateur est un étudiant
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 true si étudiant et false sinon
// ---------------------------------------------------------------------------
function isUserStudent($idUser) {
	global $mysql_link;
  $query  = "SELECT _IDgrp ";
  $query .= "FROM user_id ";
  $query .= "WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    if ($row[0] == 1) return true;
		else return false;
  }
}

// ---------------------------------------------------------------------------
// Fonction: Vérifie si l'utilisateur est un intervenant
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 true si intervenant et false sinon
// ---------------------------------------------------------------------------
function isUserTeacher($idUser) {
	global $mysql_link;
  $query  = "SELECT _IDgrp ";
  $query .= "FROM user_id ";
  $query .= "WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    if ($row[0] == 2) return true;
		else return false;
  }
}

// ---------------------------------------------------------------------------
// Fonction: Vérifie si l'utilisateur est un administrateur
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 true si admin et false sinon
// ---------------------------------------------------------------------------
function isUserAdmin($idUser) {
	global $mysql_link;
  $query  = "SELECT _IDgrp ";
  $query .= "FROM user_id ";
  $query .= "WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    if ($row[0] == 4) return true;
		else return false;
  }
}


// ---------------------------------------------------------------------------
// Fonction: Donne le sexe d'un utilisateur
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 Le sexe (H = homme, F = femme, A = annonyme) (TEXT)
// ---------------------------------------------------------------------------
function getUserSexeByID($idUser) {
	global $mysql_link;
  $query  = "SELECT `_sexe` ";
  $query .= "FROM `user_id` ";
  $query .= "WHERE `_ID` = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}


// ---------------------------------------------------------------------------
// Fonction: Donne le nom de la classe de l'utilisateur en fonction de son ID
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 Le nom de sa classe (TEXT)
// ---------------------------------------------------------------------------
function getUserClassByUserID($idUser) {
	global $mysql_link;
  $query  = "SELECT _ident ";
  $query .= "FROM campus_classe ";
  $query .= "WHERE _IDclass IN (SELECT _IDclass FROM user_id WHERE _ID = ".$idUser.") ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'ID' de la classe de l'utilisateur en fonction de son ID
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 L'ID de la classe (INT)
// ---------------------------------------------------------------------------
function getUserClassIDByUserID($idUser) {
	global $mysql_link;
  $query  = "SELECT _IDclass FROM user_id WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}

// ---------------------------------------------------------------------------
// Fonction: Donne le nom de la classe en fonction de son ID
// IN:		   L'ID de la classe (INT)
// OUT: 		 Le nom de la classe (TEXT)
// ---------------------------------------------------------------------------
function getClassNameByClassID($idClass) {
	global $mysql_link;
  $query  = "SELECT _ident ";
  $query .= "FROM campus_classe ";
  $query .= "WHERE _IDclass = ".$idClass." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}

function getPoleNameByIdPole($idPole) {
	global $mysql_link;
	$query  = "SELECT `_name` ";
  $query .= "FROM `pole` ";
  $query .= "WHERE `_ID` = ".$idPole." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return stripslashes($row[0]);
  }
}

function getMatNameByIdMat($idMat) {
	global $mysql_link;
	$query  = "SELECT `_titre` ";
  $query .= "FROM `campus_data` ";
  $query .= "WHERE `_IDmat` = ".$idMat." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return stripslashes($row[0]);
  }
}

// ---------------------------------------------------------------------------
// Fonction: Donne le numéro de promotion (1ère année, 2ème...) en fonction de
//					 l'ID du PMA (Liaison Pôle matière année)
// IN:		   ID du PMA (Liaison Pôle matière année) (INT)
// OUT: 		 Numéro de promotion (1ère année, 2ème...) (TEXT)
// ---------------------------------------------------------------------------
function getClassYearByPMAID($idPMA) {
	global $mysql_link;
	$query = "SELECT `_ID_year` FROM `pole_mat_annee` WHERE `_ID_pma` = '".$idPMA."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$listeAnnee = json_decode(getParam("annee-niveau"), TRUE);
		return $listeAnnee[$row[0]];
  }
}


// ---------------------------------------------------------------------------
// Fonction: Donne le numéro de promotion (1ère année, 2ème...) en fonction de
//					 l'année d'obtention du diplôme
// IN:		   Année d'obtention du diplôme (INT)
// OUT: 		 Numéro de promotion (1ère année, 2ème...) (TEXT)
// ---------------------------------------------------------------------------
function getPromotionByGraduationYear($graduationYear)
{
	global $mysql_link;
	$currentYear = getParam("END_Y");
	$currentPromotion = $graduationYear - $currentYear;
	$nb_years_before_graduation = getParam('nbrAnneeAvantDiplome');

	switch ($currentPromotion) {
		case '-1':
			$toReturn = getNiveauNameByNumNiveau($nb_years_before_graduation + 1);
			if ($toReturn == "") return "error";
			else return $toReturn;
			break;

		default:
			if ($nb_years_before_graduation - $currentPromotion > ($nb_years_before_graduation + 1) or $nb_years_before_graduation - $currentPromotion < 1) return "error";
			else return getNiveauNameByNumNiveau($nb_years_before_graduation - $currentPromotion);
			break;
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'année d'obtention du diplôme en fonction du numéro de
//					 promotion (1ère année, 2ème...)
// IN:		   Numéro de promotion (1, 2...) (INT)
// OUT: 		 Année d'obtention du diplôme (INT)
// ---------------------------------------------------------------------------
function getGraduationYearByClassNumber($classNumber)
{
	global $mysql_link;
	$query = "SELECT `_graduation_year` FROM `campus_classe` WHERE `_code` = '".$classNumber."' ";
	$result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'année d'obtention du diplôme en fonction de l'ID de la classe
// IN:		   ID de la classe (INT)
// OUT: 		 Année d'obtention du diplôme (INT)
// ---------------------------------------------------------------------------
function getGraduationYearByClassID($classID)
{
	global $mysql_link;
	$query = "SELECT `_graduation_year` FROM `campus_classe` WHERE `_IDclass` = '".$classID."' ";
	$result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'ID de la classe en fonction de l'année d'obtention du
//					 diplôme
// IN:		   Année d'obtention du diplôme (INT)
// OUT: 		 ID de la classe (INT)
// ---------------------------------------------------------------------------
function getClassIDByGraduationYear($graduationYear)
{
	global $mysql_link;
	$query = "SELECT `_IDclass` FROM `campus_classe` WHERE `_graduation_year` = '".$graduationYear."' ";
	$result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'ID de la classe en fonction de l'ID du PMA
//					 (Liaison Pôle matière année)
// IN:		   ID du PMA (Liaison Pôle matière année) (INT)
// OUT: 		 ID de la classe (INT)
// ---------------------------------------------------------------------------
function getClassIDByPMAID($idPMA) {
	global $mysql_link;
	$query = "SELECT `_ID_year` FROM `pole_mat_annee` WHERE `_ID_pma` = '".$idPMA."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$graduationYear = getGraduationYearByClassNumber($row[0]);
		return getClassIDByGraduationYear($graduationYear);
  }
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'ID de la matière en fonction de l'ID du PMA
//					 (Liaison Pôle matière année)
// IN:		   ID du PMA (Liaison Pôle matière année) (INT)
// OUT: 		 ID de la matière (INT)
// ---------------------------------------------------------------------------
function getMatIDByPMAID($pmaID)
{
	global $mysql_link;
	$query = "SELECT `_ID_matiere` FROM `pole_mat_annee` WHERE `_ID_pma` = '".$pmaID."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'ID du pôle en fonction de l'ID du PMA
//					 (Liaison Pôle matière année)
// IN:		   ID du PMA (Liaison Pôle matière année) (INT)
// OUT: 		 ID du pôle (INT)
// ---------------------------------------------------------------------------
function getPoleIDByPMAID($pmaID)
{
	global $mysql_link;
	$query = "SELECT `_ID_pole` FROM `pole_mat_annee` WHERE `_ID_pma` = '".$pmaID."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'ID du pôle en fonction de l'ID de l'UV
// IN:		   ID de l'UV (INT)
// OUT: 		 ID du pôle (INT)
// ---------------------------------------------------------------------------
function getPoleIDByUVID($uvID)
{
	global $mysql_link;
	$query = "SELECT `_ID_pma` FROM `campus_examens` WHERE `_ID_exam` = '".$uvID."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return getPoleIDByPMAID($row[0]);
  }
}

function getDayNameByDayNumber($dayNumber)
{
	global $mysql_link;
	switch ($dayNumber) {
		case '0': return "Lundi"; break;
		case '1': return "Mardi"; break;
		case '2': return "Mercredi"; break;
		case '3': return "Jeudi"; break;
		case '4': return "Vendredi"; break;
		case '5': return "Samedi"; break;
		case '6': return "Dimanche"; break;
	}
}

function getRoomNameByID($idRoom) {
	global $mysql_link;
  $query  = "SELECT _title ";
  $query .= "FROM edt_items ";
  $query .= "WHERE _IDitem = ".$idRoom." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}



function getMatTypeByMatID($matID)
{
	global $mysql_link;
	$query  = "SELECT _type, _code ";
  $query .= "FROM campus_data ";
  $query .= "WHERE _IDmat = ".$matID." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		if ($row[1] == 'Agd') return 3;
		if ($row[1] == 'Ind' && $row[0] == 2) return 2;
    return $row[0];
  }
}

function getUserGroupByID($userID)
{
	global $mysql_link;
	$query  = "SELECT `_ident` ";
  $query .= "FROM `user_group` ";
  $query .= "WHERE `_IDgrp` = (SELECT `_IDgrp` FROM `user_id` WHERE `_ID` = ".$userID.") ";
	$query .= "AND `_lang` = '".$_SESSION['lang']."' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }
}

function getClassNameByUserID($userID)
{
	global $mysql_link;
	$query  = "SELECT `_IDclass` ";
	$query .= "FROM `user_id` ";
	$query .= "WHERE `_ID` = '".$userID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return getClassNameByClassID($row[0]);
	}
}

function getClassIDByUserID($userID)
{
	global $mysql_link;
	$query  = "SELECT `_IDclass` ";
	$query .= "FROM `user_id` ";
	$query .= "WHERE `_ID` = '".$userID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Donne le nom d'un examen (UV) en fonction de son ID
// IN:		   ID de l'examen (INT)
// OUT: 		 Le nom de l'examen (TEXT)
// ---------------------------------------------------------------------------
function getUVNameByUVID($uvID)
{
	global $mysql_link;
	$query  = "SELECT `_nom` ";
	$query .= "FROM `campus_examens` ";
	$query .= "WHERE `_ID_exam` = '".$uvID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne le coef d'un examen (UV) en fonction de son ID
// IN:		   ID de l'examen (INT)
// OUT: 		 Le coef de l'examen (int)
// ---------------------------------------------------------------------------
function getUVCoefByUVID($uvID)
{
	global $mysql_link;
	$query  = "SELECT `_coef` ";
	$query .= "FROM `campus_examens` ";
	$query .= "WHERE `_ID_exam` = '".$uvID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}

function getUVPMAByUVID($uvID)
{
	global $mysql_link;
	$query  = "SELECT `_ID_pma` ";
	$query .= "FROM `campus_examens` ";
	$query .= "WHERE `_ID_exam` = '".$uvID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'ID de l'utilisateur en fonction du code barre
// IN:		   Le code barre (INT)
// OUT: 		 L'ID de l'utilisateur (INT)
// ---------------------------------------------------------------------------
function getUserIDByBarcode($barecode)
{
	global $mysql_link;
	$query  = "SELECT `_IDdata` ";
	$query .= "FROM `rubrique_data` ";
	$query .= "WHERE `_IDrubrique` = '10' AND `_valeur` = '".$barecode."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Re-calcule le numéro de promo (1ère année, 2ème...) en fonction de
//					 l'année d'obtention du diplôme et de l'année courante
// IN:		   Aucuns
// OUT: 		 Aucuns
// ---------------------------------------------------------------------------
function reAssignPromotionByGraduationYear()
{
	global $mysql_link;
	$query = "SELECT `_IDclass`, `_graduation_year` FROM `campus_classe` WHERE 1 ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$promName = getPromotionByGraduationYear($row[1]);
		if ($promName != "error")
		{
			$promNumber = substr($promName, 0, 1);
			$query_update = "UPDATE campus_classe SET `_code` = '".$promNumber."' WHERE `_IDclass` = '".$row[0]."' ";
			$req = mysqli_query($mysql_link, $query_update) or die('Erreur SQL !<br>'.$query_update.'<br>'.mysqli_error());
		}
		else
		{
			$query_update = "UPDATE campus_classe SET `_visible` = 'N', `_code` = '0' WHERE `_IDclass` = '".$row[0]."' ";
			$req = mysqli_query($mysql_link, $query_update) or die('Erreur SQL !<br>'.$query_update.'<br>'.mysqli_error());
		}
	}
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'adresse d'un utilisateur en fonction de son ID
// IN:		   ID de l'utilisateur (INT)
// OUT: 		 Son adresse (TEXT)
// ---------------------------------------------------------------------------
function getUserAddressByUserID($userID)
{
	global $mysql_link;
	$query = "SELECT `_adr1`, `_adr2`, `_cp`, `_city` FROM `user_id` WHERE `_ID` = '".$userID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'adresse d'un utilisateur sur deux lignes en fonction de son ID
// IN:		   ID de l'utilisateur (INT)
// OUT: 		 Son adresse (TEXT)
// ---------------------------------------------------------------------------
function getUserAddressTwoLinesByUserID($userID)
{
	global $mysql_link;
	$query = "SELECT `_adr1`, `_adr2`, `_cp`, `_city` FROM `user_id` WHERE `_ID` = '".$userID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0]." ".$row[1]."<br>".$row[2]." ".$row[3];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'ID de la matière en fonction de l'ID de l'UV
// IN:		   ID de l'UV (INT)
// OUT: 		 ID de la matière (INT)
// ---------------------------------------------------------------------------
function getMatIDByUVID($uvID)
{
	global $mysql_link;
	return getMatIDByPMAID(getPMAIDByUVID($uvID));
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'ID du PMA en fonction de l'ID de l'UV
// IN:		   ID de l'UV (INT)
// OUT: 		 ID du PMA (INT)
// ---------------------------------------------------------------------------
function getPMAIDByUVID($uvID)
{
	global $mysql_link;
	$query = "SELECT _ID_pma FROM campus_examens WHERE _ID_exam = '".$uvID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Vérifie si l'examen est un certificat
// IN:		   ID de l'UV (INT)
// OUT: 		 1 si certificat, 0 sinon (INT)
// ---------------------------------------------------------------------------
function isUVCertificat($uvID)
{
	global $mysql_link;
	$query_exam = "SELECT _type FROM campus_examens WHERE _ID_exam = '".$uvID."' ";
	$result_exam = mysqli_query($mysql_link, $query_exam);
	while ($row_exam = mysqli_fetch_array($result_exam, MYSQLI_NUM)) {
		// Si l'examen est un certificat
		if ($row_exam[0] == 3 || $row_exam[0] == 4) return 1;
		else return 0;
	}
}



// ---------------------------------------------------------------------------
// Fonction: Donne le nom d'un UV en fonction de son ID
// IN:		   ID de l'UV (INT)
// OUT: 		 Le nom de l'UV (TEXT)
// ---------------------------------------------------------------------------
function getUVNameByID($uvID)
{
	global $mysql_link;
	$query = "SELECT _nom FROM campus_examens WHERE _ID_exam = '".$uvID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne la note de l'UV de rattrapage d'un UV
// IN:		   ID de l'UV (INT)
// IN:		   ID de l'élève (INT)
// OUT: 		 La note de rattrapage (FLOAT)
// ---------------------------------------------------------------------------
function getUVRattNote($uvID, $IDeleve)
{
	global $mysql_link;
	$query = "SELECT _ID_exam FROM campus_examens WHERE _ID_parent = '".$uvID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$query = "SELECT _value FROM notes_items WHERE _IDeleve = '".$IDeleve."' AND _value != '' AND _IDdata = (SELECT _IDdata FROM notes_data WHERE _IDmat = '".($row[0] + 100000)."') ";
		$result_1 = mysqli_query($mysql_link, $query);
		while ($row_1 = mysqli_fetch_array($result_1, MYSQLI_NUM)) {
			return $row_1[0];
		}
	}
	return null;
}


// ---------------------------------------------------------------------------
// Fonction: Donne la note de rattrapage d'un PMA
// IN:		   ID du PMA (INT)
// IN:		   ID de l'élève (INT)
// OUT: 		 La note de rattrapage (FLOAT)
// ---------------------------------------------------------------------------
function getPMARattNote($pmaID, $IDeleve)
{
	global $mysql_link;
	$query = "SELECT _ID_exam FROM campus_examens WHERE `_type` = 2 AND _ID_pma = '".$pmaID."' ";
	// echo $query;
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$query = "SELECT _value FROM notes_items WHERE _IDeleve = '".$IDeleve."' AND _value != '' AND _IDdata = (SELECT _IDdata FROM notes_data WHERE _IDmat = '".($row[0] + 100000)."') ";
		$result_1 = mysqli_query($mysql_link, $query);
		while ($row_1 = mysqli_fetch_array($result_1, MYSQLI_NUM)) {
			return $row_1[0];
		}
	}
	return null;
}

// ---------------------------------------------------------------------------
// Fonction: Donne le coef de l'exam de rattrapage d'un PMA
// IN:		   ID du PMA (INT)
// IN:		   ID de l'élève (INT)
// OUT: 		 Le coef de l'exam de rattrapage (FLOAT)
// ---------------------------------------------------------------------------
function getPMARattCoef($pmaID, $IDeleve)
{
	global $mysql_link;
	$query = "SELECT _coef FROM campus_examens WHERE `_type` = 2 AND _ID_pma = '".$pmaID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Donne la note maximum possible de l'exam de rattrapage d'un PMA
// IN:		   ID du PMA (INT)
// IN:		   ID de l'élève (INT)
// OUT: 		 La note maximum possible de l'exam de rattrapage (FLOAT)
// ---------------------------------------------------------------------------
function getPMARattNoteMax($pmaID, $IDeleve)
{
	global $mysql_link;
	$query = "SELECT _note_max FROM campus_examens WHERE `_type` = 2 AND _ID_pma = '".$pmaID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Vérifie si l'UV est un UV de rattrapage
// IN:		   ID de l'UV (INT)
// OUT: 		 True si UV de rattrapage, FALSE sinon (BOOL)
// ---------------------------------------------------------------------------
function isUVRattrapage($uvID)
{
	global $mysql_link;
	$isRatt = false;
	$query = "SELECT _ID_parent, _type FROM campus_examens WHERE _ID_exam = '".$uvID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		if (!is_null($row[0]) && $row[0] != 0 && $row[0] != '') $isRatt = true;
		if ($row[1] == 2 || $row[1] == 4) $isRatt = true;
	}
	return $isRatt;
}


// ---------------------------------------------------------------------------
// Fonction: Donne le numéro de niveau de l'utilisateur en fonction de son ID
// 					 et de l'année souhaité
// IN:		   ID de l'utilisateur (INT)
//					 L'année souhaité (INT)
// OUT: 		 Le numéro du niveau (INT)
// ---------------------------------------------------------------------------
function getUserNiveauNumberByUserIDAndYear($userID, $year)
{
	global $mysql_link;
	$query = "SELECT _niveau FROM user_log WHERE _IDuser = '".$userID."' AND _year = '".$year."' LIMIT 1 ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  return $row[0];
	}

	$query = "SELECT _code FROM campus_classe WHERE _IDclass IN (SELECT _IDclass FROM user_id WHERE _ID = '".$userID."') ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'ID de l'utilisateur en fonction de son numéro INE
// IN:		   Le numéro INE (INT)
// OUT: 		 L'ID de l'utilisateur (INT)
// ---------------------------------------------------------------------------
function getUserIDByINE($INE)
{
	global $mysql_link;
	$query = "SELECT _IDdata FROM rubrique_data WHERE _valeur = '".$INE."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne la liste des profs d'un syllabus en fonction du PMA
// IN:		   L'ID du PMA (INT)
// OUT: 		 La liste des profs (ARRAY)
// ---------------------------------------------------------------------------
function getPMASyllabusProfNameList($pmaID, $oneLetterFirstName = 0)
{
	global $mysql_link;
	$prof_list = array();
	$query = "SELECT _idUser FROM campus_syllabus WHERE _IDPMA = '".$pmaID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$temp = explode(';', $row[0]);
		foreach ($temp as $key => $value) {
			if ($value != "") {
				if ($oneLetterFirstName) {
					$query  = "SELECT _name, _fname ";
					$query .= "FROM user_id ";
					$query .= "WHERE _ID = ".$value." ";
					$result = mysqli_query($mysql_link, $query);
					while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
						$prof_list[] = $row[0].' '.substr($row[1], 0, 1).'.';
					}
				}
				else $prof_list[] = getUserNameByID($value);

			}
		}
	}
	return $prof_list;
}



// ---------------------------------------------------------------------------
// Fonction: Donne le code barre d'un utilisateur en fonction de son ID
// IN:		   L'ID de l'utilisateur (INT)
// OUT: 		 Son code barre (TEXT)
// ---------------------------------------------------------------------------
function getUserBarCodeByID($userID)
{
	global $mysql_link;
	$query = "SELECT _valeur FROM rubrique_data WHERE _IDdata = '".$userID."' AND _IDrubrique = '10' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'id d'un utilisateur en fonction de son mail
// IN:		   L'email de l'utilisateur (TEXT)
// OUT: 		 Son ID (INT)
// ---------------------------------------------------------------------------
function getUserIDByEmail($userEmail)
{
	global $mysql_link;
	$query = "SELECT _ID FROM user_id WHERE _email = '".$userEmail."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Donne l'email d'un utilisateur en fonction de son id
// IN:		   Son ID (INT)
// OUT: 		 L'email de l'utilisateur (TEXT)
// ---------------------------------------------------------------------------
function getUserEmailByID($userID)
{
	global $mysql_link;
	$query = "SELECT _email FROM user_id WHERE _ID = '".$userID."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		return $row[0];
	}
}

// ---------------------------------------------------------------------------
// Fonction: Vérifie si une classe est visible ou invisible
// IN:		   L'ID de la classe (INT)
// OUT: 		 true si visible et false sinon (BOOL)
// ---------------------------------------------------------------------------
function checkIfClassIsVisibleByID($IDclass)
{
	global $mysql_link;
	$query = "SELECT `_visible` FROM `campus_classe` WHERE `_IDclass` = '".$IDclass."' LIMIT 1 ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  if ($row[0] == 'O') return true;
		else return false;
	}
}


// ---------------------------------------------------------------------------
// Fonction: Récupère le code du type de note en fonction de l'id tu type de note
// IN:		   L'ID du type de la note (INT)
// OUT: 		 Le code du type de note (TEXT)
// ---------------------------------------------------------------------------
function getNoteTypeCodeFromTypeID($typeID)
{
	global $mysql_link;
	$query = "SELECT `_ident` FROM `notes_type` WHERE `_IDtype` = '".$typeID."' LIMIT 1 ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  return $row[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Transforme une date du format français au format anglais
// IN:		   $date la date à transformer
// OUT: 		 Le code du type de note (TEXT)
// ---------------------------------------------------------------------------
function changeDateTypeFromFRToEN($date) {
	global $mysql_link;
	if (strpos($date, ':') !== false) {
		$temp_1 = explode(' ', $date);
		$temp = explode('/', $temp_1[0]);
		return $temp[2].'-'.$temp[1].'-'.$temp[0].' '.$temp_1[1];
	}
	else {
		$temp = explode('/', $date);
		return $temp[2].'-'.$temp[1].'-'.$temp[0];
	}
}


// ---------------------------------------------------------------------------
// Fonction: Donne l'ID du niveau en fonction de l'ID du PMA
// IN:		   L'ID du PMA (INT)
// OUT: 		 L'id du niveau (INT)
// ---------------------------------------------------------------------------
function getNiveauNumberByPMA($pma) {
	global $mysql_link;
	$query  = "SELECT `_ID_year` ";
  $query .= "FROM `pole_mat_annee` ";
  $query .= "WHERE `_ID_pma` = '".$pma."' ";
	$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
	while($row = mysqli_fetch_array($req))
	{
		return $row[0];
	}
}

?>
