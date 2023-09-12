<?php
/***** Récupère les personnes d'un groupe *****/
function getUsersByIDgrp($IDgrp)
{
	global $mysql_link;
	$query  = "SELECT u._ID, u._name, u._fname ";
	$query .= "FROM user_id u, groupe g ";
	$query .= "WHERE u._ID = g._IDeleve ";
	$query .= "AND g._IDgrp = $IDgrp ";
	$query .= "order by u._name";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	while($row)
	{
		$users[$row[0]] = "$row[1] $row[2]";
		$row = remove_magic_quotes(mysqli_fetch_row($result));
	}

	return $users;
}
/***** Récupère le nom d'un groupe *****/
function getNomByIDgrp($IDgrp)
{
	global $mysql_link;
	$query  = "SELECT _nom ";
	$query .= "FROM groupe_nom ";
	$query .= "WHERE _IDgrp = $IDgrp ";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	return $row[0];
}
/***** Récupère les groupes d'une personne *****/
function getGroupsByIDuser($IDuser)
{
	global $mysql_link;
	$query  = "SELECT gn._IDgrp, gn._nom ";
	$query .= "FROM groupe g, groupe_nom gn ";
	$query .= "WHERE g._IDgrp = gn._IDgrp ";
	$query .= "AND g._IDeleve = $IDuser ";
	$query .= "order by gn._IDgrp desc";

	$result = mysqli_query($mysql_link, $query);

	$groups = array();
	while($row = mysqli_fetch_row($result)) $groups[$row[0]] = "$row[1]";

	return $groups;
}

/***** Récupère les groupes *****/
function getGroups($IDcentre = 0, $autocomplete = "")
{
	global $mysql_link;
	// Recherche des groupes
	$query  = "SELECT _IDgrp, _nom ";
	$query .= "FROM groupe_nom ";
	$query .= "WHERE _visible = 'O' ";
	$query .= (getParam("GROUPE_IDCENTRE") == 1 && $IDcentre != 0) ? "AND _IDcentre = $IDcentre " : "";
	$query .= ($autocomplete != "") ? "AND _nom LIKE '%$autocomplete%' " : "";
	$query .= "ORDER BY _IDgrp desc ";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	while($row)
	{
		$groups[$row[0]] = "$row[1]";
		$row = remove_magic_quotes(mysqli_fetch_row($result));
	}

	return $groups;
}

/**
 * showPMAList
 *
 * Affiche le select des PMA avec la recherche intégrée
 *
 * @param string $nameOfSelect Le nom du select
 * @param int $idSelected L'ID de la valeur sélectionnée
 * @param bool $onclick_wanted Est-ce que l'on appelle la fonction selectPMAChanged quand on change la valeur du select
 * @return return type
 */
function showPMAList($nameOfSelect, $idSelected, $onclick_wanted = 1)
{
	global $mysql_link;
	if ($nameOfSelect == "IDmat") $onClick = "onchange=\"document.forms.formulaire.submit()\"";
	elseif ($onclick_wanted) $onClick = "onchange=\"selectPMAChanged();\"";
	else $onClick = '';
	$toReturn = '';
	$toReturn = '<select '.$onClick.' id="'.$nameOfSelect.'" name="'.$nameOfSelect.'" required class="selectpicker" data-show-subtext="false" data-live-search="true">';

	$classes = array();
	$query = "SELECT _code, _ident FROM campus_classe WHERE _visible = 'O' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result)) $classes[$row[0]] = $row[1];

	$selected_option = "";
	if ($idSelected == 0 || $idSelected == "") $selected_option = "selected";
	else $selected_option = "";
	$toReturn .= "<option value=\"0\" ".$selected_option.">Sélectionnez une matière</option>";
	$selected_option = "";
	$listeAnnee = json_decode(getParam("annee-niveau"), TRUE);
	$currentYear = 0;
		$query = "SELECT * FROM `pole_mat_annee` WHERE 1 ORDER BY `_ID_year` ASC, `_ID_pole` ASC, `_ID_matiere` ASC ";
		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while($row)
		{
			$annee_label = str_replace('<sup>', '', str_replace('</sup>', '', $classes[$row[1]]));
			if ($row[1] != $currentYear and $currentYear != 0) $toReturn .= "</optgroup>";
			if ($row[1] != $currentYear) $toReturn .= "<optgroup label=\"".$annee_label."\">";

				if ($idSelected == $row[0]) $selected_option = "selected";
				else $selected_option = "";

			if (getParam('showAnneeInPMASelectList')) $showAnnee = $classes[$row[1]]." - ";
			else $showAnnee = '';
			$toReturn .= "<option value=\"".$row[0]."\" ".$selected_option.">".$showAnnee.getPoleNameByIdPole($row[2])." - ".getMatNameByIdMat($row[3])."</option>";
			$currentYear = $row[1];

			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}



		$toReturn .= "</optgroup>";
$toReturn .= "</select>";



	// $toReturn .= "<style>";
	// 	$toReturn .= ".bootstrap-select.btn-group .dropdown-menu li {";
	// 		$toReturn .= "margin-left: 10px;";
	// 		$toReturn .= "margin-right: 10px;";
	// 	$toReturn .= "}";
	// 	$toReturn .= ".bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {";
	// 		$toReturn .= "width: 100% !important;";
	// 	$toReturn .= "}";
	// $toReturn .= "</style>";

	// $toReturn .= "<link rel=\"stylesheet\" href=\"css/bootstrap-select.min.css\" />";
	// $toReturn .= "<script src=\"script/bootstrap-select.min.js\"></script>";


// $toReturn .= '<script src="vendor/jquery/jquery.min.js"></script>';
// 	$toReturn .= '<script type="text/javascript" src="js/bootstrap-select/bootstrap-select.min.js"></script>';
// 	$toReturn .= '<link rel="stylesheet" type="text/css" href="css/bootstrap-select.min.css" />';


return $toReturn;
}






/**
 * Donne le nom d'un groupe en fonction de l'ID du groupe
 *
 * @param int $IDgrp L'ID du groupe
 * @return return string Le nom du groupe
 */
function getGroupNameByID($IDgrp)
{
	global $mysql_link;
	$query  = 'SELECT _nom FROM groupe_nom WHERE _IDgrp = '.$IDgrp.' ';
	$result = mysqli_query($mysql_link, $query);
	return mysqli_fetch_row($result)[0];
}

?>
