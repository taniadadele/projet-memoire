<?php

include("fonction/parametre.php");
include("fonction/rubrique.php");
include("fonction/relation.php");
include("fonction/absence.php");
include("fonction/groupe.php");
include("fonction/display.php");
include("fonction/ged.php");
include("fonction/pagination.php");
include("fonction/edt.php");
include("fonction/bulletin.php");
include("fonction/notes.php");
include("fonction/classes.php");
include("fonction/user.php");
include("fonction/log.php");
include("fonction/colors.php");
include("fonction/page_path.php");

/****************************
/*          DIVERS          */
/****************************
/***** RecupÃ©ration du libellÃ© d'une classe avec son ID *****/
function getCodeUserID($userid) {
	global $mysql_link;
	$query  = "SELECT _ident FROM campus_classe WHERE _IDclass = (SELECT _IDclass FROM user_id WHERE _ID = '$userid' ) ";
	$return  = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
	$myrow   = ( $return ) ? mysqli_fetch_row($return) : 0 ;
	return ( $myrow ) ? $myrow[0] : 0 ;
}

function getRealBornDate($bornDateOriginal)
{
	if ($bornDateOriginal == '') return '';
	$bornDate = explode("/", $bornDateOriginal);
	$dateToReturn = $bornDate[2]."-".$bornDate[1]."-".$bornDate[0]." 00-00";
	if ($dateToReturn == "0000-00-00 00-00") $dateToReturn = "1970-01-01 00-00";
	return $dateToReturn;
}

function getReadableBornDate($bornDateOriginal)
{
	$bornDateOriginalTab = explode("-", $bornDateOriginal);

	if(count($bornDateOriginalTab) == 3 && $bornDateOriginalTab[0] != "00" && $bornDateOriginalTab[1] != "00" && $bornDateOriginalTab[2] != "0000")
	{
		$yearBorn = $bornDateOriginalTab[0];
		$monthBorn = $bornDateOriginalTab[1];
		$dayBorn = $bornDateOriginalTab[2];

		$dateToReturn = $dayBorn."/".$monthBorn."/".$yearBorn;
	}
	else $dateToReturn = "";

	return $dateToReturn;
}


/**
 * getLogoDir
 *
 * Donne le nom du dossier formaté dans le dossier download/logos/ de la configuration
 *
 * @param string $cfgIdent Le nom de la configuration actuelle
 * @return return string
 */
function getLogoDir($cfgIdent) {
	$chars_to_remove = array('\'', '"');	// Les caractères que l'on va retirer du chemin d'accès
	foreach ($chars_to_remove as $value) {
		$cfgIdent = str_replace($value, '', $cfgIdent);
	}
	return urlencode(addslashes(stripslashes($cfgIdent)));
}
?>
