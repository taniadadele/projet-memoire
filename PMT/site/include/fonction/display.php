<?php
/***** Affiche la <select> des centres *****/
function DisplayListCentre($selected = 0, $disabled = false)
{
	$disabled = ($disabled) ? "disabled =\"disabled\"" : "";
	print("
	<select id=\"IDcentre\" name=\"IDcentre\" onchange=\"document.forms.formulaire.submit()\" $disabled>");

	// lecture des centres constitutifs
	$query  = "select _IDcentre, _ident from config_centre ";
	$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
	$query .= "order by _IDcentre";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	while ($row)
	{
		printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($selected == $row[0]) ? "selected=\"selected\"" : "");
		$row = remove_magic_quotes(mysqli_fetch_row($result));
	}

	print("
		</select> <i class=\"icon-home\"></i>");
}

/***** Affiche la <select> des groupes *****/
function DisplayListGroupe($selected = 0, $IDcentre, $disabled = false, $onchange = false)
{
	$disabled = ($disabled) ? "disabled =\"disabled\"" : "";
	$onchange = ($onchange) ? "onchange=\"document.forms.formulaire.submit()\"" : "";

	print("
	<select id=\"IDgrp\" name=\"IDgrp\" $onchange $disabled>
		<option value=\"0\"></option>");

	// Recherche des groupes
	$query  = "SELECT _IDgrp, _nom ";
	$query .= "FROM groupe_nom ";
	$query .= "WHERE _visible = 'O' ";
	$query .= (getParam("GROUPE_IDCENTRE") == 1) ? "AND _IDcentre = $IDcentre " : "";
	$query .= "ORDER BY _IDgrp desc ";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	while ($row)
	{
		printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($selected == $row[0]) ? "selected=\"selected\"" : "");
		$row = remove_magic_quotes(mysqli_fetch_row($result));
	}

	print("
		</select>");
}



/**
 * Afficher les boutons de validation et retour
 *
 * Fonction qui affiche les boutons valider (submit du formulaire) et retour (lien vers index.php)
 * Ne pas appeler avec un 'echo', juste appeler la fonction
 *
 * @return return null
 */
function showValidateBackButtons() {
	echo '<table class="width100">';
		echo '<tr>';
		echo '<td style="width:10%;" class="valign-middle align-center"><button type="submit" name="valid" class="btn btn-success">Valider</button></td>';
		echo '<td class="valign-middle">Pour valider</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td class="valign-middle align-center"><a href="index.php" class="btn btn-danger">Fermer</a></td>';
			echo '<td class="valign-middle">Pour revenir à l\'accueil</td>';
		echo '</tr>';
	echo '</table>';
}


function tel($str) {
	if(strlen($str) == 10) {
		$res  = substr($str, 0, 2).'.';
		$res .= substr($str, 2, 2).'.';
		$res .= substr($str, 4, 2).'.';
		$res .= substr($str, 6, 2).'.';
		$res .= substr($str, 8, 2);
		return $res;
	}
}



/**
 * centerSelect
 *
 * Affiche le select de sélection de centre
 *
 * @param int $IDcentre L'ID du centre actuellement sélectionné
 * @param string $formName Le nom du formulaire dans lequel se trouve le select
 * @param string $selectName Le nom du select (attribut name)
 * @param string $selectID L'ID du select (attribut ID)
 * @param boolean $showText Est-ce que l'on affiche le texte au dessus du select
 * @param string $additionalClasses Les classes à ajouter au select
 * @return return string Le select à afficher
 */
function centerSelect($IDcentre, $formName = 'formulaire', $selectName = 'IDcentre', $selectID = 'IDcentre', $showText = true, $additionalClasses = '', $submitOnChange = true) {
	global $mysql_link;
	global $db;
	$toReturn = '';

	// On affiche le choix du centre uniquement si on le souhaite
	if (!getParam('showCenter')) $displaycentre = 'display: none;'; else $displaycentre = '';

	$toReturn .= '<div class="form-group" style="'.$displaycentre.'">';
		if ($showText) $toReturn .= '<label for="'.$selectID.'" style="'.$displaycentre.'">Centre constitutif</label>';
		if ($submitOnChange) $submitOnChange = 'onchange="document.forms.'.$formName.'.submit()"'; else $submitOnChange = '';
		$toReturn .= '<select id="'.$selectID.'" name="'.$selectName.'" class="custom-select '.$additionalClasses.'" '.$submitOnChange.' style="'.$displaycentre.'">';
			$query  = "select _IDcentre, _ident from config_centre where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' order by _IDcentre ";
			$result = mysqli_query($mysql_link, $query);
			while ($row = mysqli_fetch_row($result)) {
				if ($IDcentre == $row[0]) $selected = 'selected'; else $selected = '';
				$toReturn .= '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
			}
		$toReturn .= "</select>";
	$toReturn .= '</div>';
	return $toReturn;
}




/**
 * getUserPictureNameBox
 *
 * Donne une image avec l'image de profil de l'utilisateur et son nom en dessous
 *
 * @param int $userID L'ID de l'utilisateur
 * @param int $size La taille de l'image
 * @param string $color La classe couleur bootstrap (ex: primary, danger, info...) du fond du nom (le texte s'adapte)
 * @return return string L'image avec le nom
 */
function getUserPictureNameBox($userID = 0, $size = 120, $color = 'primary')
{
	switch ($color) {
		case 'primary': 		$text_color = 'white'; break;
		case 'secondary': 	$text_color = 'white'; break;
		case 'success': 		$text_color = 'white'; break;
		case 'danger': 			$text_color = 'white'; break;
		case 'warning': 		$text_color = 'dark';  break;
		case 'info': 				$text_color = 'white'; break;
		case 'light': 			$text_color = 'dark';  break;
		case 'dark': 				$text_color = 'white'; break;
		case 'white': 			$text_color = 'dark';  break;
		case 'transparent': $text_color = 'dark';  break;
	}
	$toReturn = '';
	$toReturn .= '<div class="profile-header-container" style="display: inline-block;">';
		$toReturn .= '<div class="profile-header-img">';
			$toReturn .= '<img class="img-circle border rounded-circle" src="'.getUserPictureLink($userID).'" style="width: '.$size.'; height: '.$size.';">';
			$toReturn .= '<!-- badge -->';
			$toReturn .= '<div class="image-bottom-label-container">';
				$toReturn .= '<span class="bg-'.$color.' text-'.$text_color.' rounded-pill px-3 py-10">'.getUserNameByID($userID).'</span>';
			$toReturn .= '</div>';
		$toReturn .= '</div>';
	$toReturn .= '</div>';
	return $toReturn;
}

/**
 * mb_ucfirst
 *
 * Met la première lettre d'une chaine de caractère en majuscule en prenant en compte les caractères spéciaux
 *
 * @param string $str La variable dont on met la lettre en majuscule
 * @return return string
 */
function mb_ucfirst($str) {
	$fc = mb_strtoupper(mb_substr($str, 0, 1));
	return $fc.mb_substr($str, 1);
}


/**
 * getMonthNameByMonthNumber
 *
 * Donne le nom du mois en fonction de son numéro
 *
 * @param int $monthNumber Le numéro du mois
 * @return return string
 */
function getMonthNameByMonthNumber($monthNumber)
{
	switch ($monthNumber) {
		case 1:  return 'Janvier'; 		break;
		case 2:  return 'Février'; 		break;
		case 3:  return 'Mars'; 			break;
		case 4:  return 'Avril'; 			break;
		case 5:  return 'Mai'; 				break;
		case 6:  return 'Juin'; 			break;
		case 7:  return 'Juillet'; 		break;
		case 8:  return 'Août'; 			break;
		case 9:  return 'Septembre'; 	break;
		case 10: return 'Octobre'; 		break;
		case 11: return 'Novembre'; 	break;
		case 12: return 'Décembre'; 	break;
	}
}
?>
