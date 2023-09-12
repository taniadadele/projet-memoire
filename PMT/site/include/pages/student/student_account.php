<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2005-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)

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
 *		module   : student_account.php
 *		projet   : la page de création/modification d'un compte élève
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 18/05/19
 *		modif    : 18/05/19 - Thomas Dazy
 * 	                 Refonte du code de la page
 */

if (isset($_SESSION['Litem']) && $_SESSION['Litem'] != '' && $_SESSION['Litem'] != 0) $Litem = $_SESSION['Litem'];
elseif (isset($_GET['Litem'])) $Litem = $_GET['Litem'];
else $Litem = 1;

if (isset($_GET['action']) && $_GET['action'] == "submit" AND ($_SESSION['CnxAdm'] == 255 OR $_SESSION['CnxID'] == $_POST['userID']))
{
	// Tableau qui stoque la liste des éléments à récupérer dans le POST
	$fieldArrayElementsToCheck = array(
		"classID",
		"userAdm",
		"userIdent",
		"userName",
		"userFname",
		"userSexe",
		"userEmail",
		"userTel",
		"userMobile",
		"userBorn",
		"userAddr1",
		"userCP",
		"userCity",
		"userINE",
		"userAddrStudy",
		"userCPStudy",
		"userCityStudy",
		"userBornCity",
		"userEnteringYear",
		"userBareCode",
		"userPriceScolarity",
		"userPayingMode",
		"userComment",
		"userDiploma",
		"userPassword"
	);
	// Tableau qui détermine dans quelle base de donnée il faut modifier les éléments
	$fieldArrayLinkWithDatabase = array(
		"classID"							=> "user_id",
		"userAdm"							=> "user_id",
		"userIdent"						=> "user_id",
		"userName"						=> "user_id",
		"userFname"						=> "user_id",
		"userSexe"						=> "user_id",
		"userEmail"						=> "user_id",
		"userTel"							=> "user_id",
		"userMobile"					=> "user_id",
		"userBorn"						=> "user_id",
		"userAddr1"						=> "user_id",
		"userCP"							=> "user_id",
		"userCity"						=> "user_id",
		"userPassword"				=> "user_id",

		"userINE"							=> "rubrique_data",
		"userAddrStudy"				=> "rubrique_data",
		"userCPStudy"					=> "rubrique_data",
		"userCityStudy"				=> "rubrique_data",
		"userBornCity"				=> "rubrique_data",
		"userEnteringYear"		=> "rubrique_data",
		"userBareCode"				=> "rubrique_data",
		"userPriceScolarity"	=> "rubrique_data",
		"userPayingMode"			=> "rubrique_data",
		"userComment"					=> "rubrique_data",
		"userDiploma"					=> "rubrique_data"
	);

	// Tableau qui détermine quel est le champ de la BDD pour user_id et quel est l'ID de la rubrique pour rubrique_data
	$fieldArrayDatabaseFieldNameOrValue = array(
		"classID"							=> "_IDclass",
		"userAdm"							=> "_adm",
		"userIdent"						=> "_ident",
		"userName"						=> "_name",
		"userFname"						=> "_fname",
		"userSexe"						=> "_sexe",
		"userEmail"						=> "_email",
		"userTel"							=> "_tel",
		"userMobile"					=> "_mobile",
		"userBorn"						=> "_born",
		"userAddr1"						=> "_adr1",
		"userCP"							=> "_cp",
		"userCity"						=> "_city",
		"userPassword"				=> "_passwd",

		"userINE"							=> "2",
		"userAddrStudy"				=> "4",
		"userCPStudy"					=> "5",
		"userCityStudy"				=> "6",
		"userBornCity"				=> "7",
		"userEnteringYear"		=> "8",
		"userBareCode"				=> "10",
		"userPriceScolarity"	=> "11",
		"userPayingMode"			=> "12",
		"userComment"					=> "13",
		"userDiploma"					=> "15"
	);

	// Pour user_id
	$query_user_id_update = "UPDATE `user_id` SET ";

	foreach ($_POST as $key => $value) {

		if (in_array($key, $fieldArrayElementsToCheck))
		{
			// Construction des requêtes
			if ($fieldArrayLinkWithDatabase[$key] == "user_id")
			{
				// Pour user_id:
				switch ($fieldArrayDatabaseFieldNameOrValue[$key]) {
					case '_passwd':
						if ($value != "") $query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".md5($value)."', ";
						break;
					case '_IDclass':
						if ($_POST['IDgrp'] != 1) $query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = NULL, ";
						else $query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".$_POST['classID']."', ";
						break;
					case '_born':
						$query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".getRealBornDate($value)."', ";
						break;

					default:
						$query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".addslashes($value)."', ";
						break;
				}
				// if ($fieldArrayDatabaseFieldNameOrValue[$key] == "_passwd")
				// {
				// 	if ($value != "") $query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".md5($value)."', ";
				// }
				// elseif ($fieldArrayDatabaseFieldNameOrValue[$key] == "_IDclass")
				// {
				// 	if ($_POST['IDgrp'] == 1) $userClass = 0;
				// 	else $userClass = $_POST['classID'];
				// 	$query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".$userClass."', ";
				// }
				// elseif ($fieldArrayDatabaseFieldNameOrValue[$key] == "_born")
				// {
				// 	$query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".getRealBornDate($value)."', ";
				// }
				//
				// else
				// {
				// 	$query_user_id_update .= "`".$fieldArrayDatabaseFieldNameOrValue[$key]."` = '".$value."', ";
				// }

			}
		}
	}

	// Allan : Un membre de l'administration n'est pas forcement super admin !
	/*if ($_POST['IDgrp'] == 4 AND $_SESSION['CnxAdm'] == 255)
	{
		$userAdm = 255;
		$query_user_id_update .= "`_adm` = '".$userAdm."', ";
		$query_user_id_update .= "`_IDgrp` = '".$_POST['IDgrp']."', ";
	}
	else
	{
		$userAdm = 1;
		$query_user_id_update .= "`_adm` = '".$userAdm."', ";
		$query_user_id_update .= "`_IDgrp` = '".$_POST['IDgrp']."', ";
	}*/
	if ($_POST['IDgrp'] != 1) $query_user_id_update .= "`_IDclass` = NULL, ";
	$query_user_id_update .= "`_IDgrp` = '".$_POST['IDgrp']."', ";
	if ($_POST['IDgrp'] != 4) $query_user_id_update .= "`_adm` = '1', ";


	$query_user_id_update = substr($query_user_id_update, 0, -2);
	$query_user_id_update .= " WHERE `_ID` = '".$_POST['userID']."'";

	$query_user_id = "select * from `user_id` where _ID = '".$_POST['userID']."' ";

	$result_user_id = mysqli_query($mysql_link, $query_user_id);
	if (mysqli_num_rows($result_user_id) > 0 and $_POST['userID'] != 0)
	{
		// Alors on la met à jour
		$new_query_user_id = $query_user_id_update;
	}
	else
	{
		// Sinon on la crée
		if ($_POST['IDgrp'] != 1) $userClass = 0;
		else $userClass = $_POST['classID'];
		$password = md5($_POST['userPassword']);
		if ($_POST['IDgrp'] == 4 && $_SESSION['CnxAdm'] == 255) $userAdm = 255;
		else $userAdm = 1;
		$new_query_user_id = "INSERT INTO `user_id`(`_IDgrp`, `_IDclass`, `_ident`, `_passwd`, `_name`, `_fname`, `_sexe`, `_email`, `_tel`, `_mobile`, `_born`, `_adr1`, `_cp`, `_city`, `_msg`, `_res`, `_cnx`, `_IP`, `_avatar`, `_persistent`, `_chs`, `_regime`, `_bourse`, `_delegue`, `_visible`, `_delay`, `_centre`, `_IDmat`, `_lang`, `_IDtuteur1`, `_IDtuteur2`, `_code`, `_numen`, `_title`, `_fonction`, `_adr2`, `_signature`, `_date`, `_lastcnx`, `_adm`) ";
		$new_query_user_id .= "VALUES ('".addslashes($_POST['IDgrp'])."', ";
		if (($userClass == 0) || ($_POST['IDgrp'] != 1)) $new_query_user_id .= "NULL, ";
		else $new_query_user_id .= "'".$userClass."', ";
		$new_query_user_id .= "'".addslashes($_POST['userIdent'])."', '".$password."', '".addslashes($_POST['userName'])."', '".addslashes($_POST['userFname'])."', '".$_POST['userSexe']."', '".addslashes($_POST['userEmail'])."', '".$_POST['userTel']."', '".$_POST['userMobile']."', '".getRealBornDate($_POST['userBorn'])."', '".addslashes($_POST['userAddr1'])."', '".$_POST['userCP']."', '".addslashes($_POST['userCity'])."', '0', '0', '0', '0', '0', 'N', 'N', 'E', 'N', 'N', 'O', NULL, '0', '', 'fr', '0', '0', '', '', '', '', '', '', NOW(), NOW(), '".$userAdm."') ";

	}

	$new_result_user_id = mysqli_query($mysql_link, $new_query_user_id);
	$modif_status = "success";
	//echo $new_query_user_id;

	if ($_GET['ID'] == "" or $_GET['ID'] == 0) $userID = mysqli_insert_id($mysql_link);
	else $userID = $_GET['ID'];

	foreach ($_POST as $key => $value) {

		if (in_array($key, $fieldArrayElementsToCheck))
		{
			// Construction des requêtes
			if ($fieldArrayLinkWithDatabase[$key] == "rubrique_data")
			{
				// Pour rubrique_data
				$query_rubrique_data_test_if_exist  = "select * from `rubrique_data` where `_IDrubrique` = '".$fieldArrayDatabaseFieldNameOrValue[$key]."' AND `_IDdata` = '".$userID."' ";
				$result_rubrique_data_test_if_exist = mysqli_query($mysql_link, $query_rubrique_data_test_if_exist);

				// Si il y a une valeur pour la rubrique
				if (mysqli_num_rows($result_rubrique_data_test_if_exist) > 0)
				{
					// Alors on la met à jour
					$query_rubrique_data = "UPDATE `rubrique_data` SET `_valeur` = '".addslashes($value)."' WHERE `_IDrubrique` = '".$fieldArrayDatabaseFieldNameOrValue[$key]."' AND `_IDdata` = '".$userID."' ";
				}
				else
				{
					// Sinon on la crée
					$query_rubrique_data = "INSERT INTO `rubrique_data`(`_IDrubrique`, `_IDdata`, `_valeur`) VALUES ('".$fieldArrayDatabaseFieldNameOrValue[$key]."', '".$userID."', '".addslashes($value)."') ";
				}

				$result_rubrique_data = mysqli_query($mysql_link, $query_rubrique_data);
			}
		}
	}

	// Upload de l'image de profil
	$file = @$_FILES["UploadedFile"]["tmp_name"];
	if ( $file ) {
		require_once "include/gallery.php";
		$dest   = "download/photo/eleves/";
		// echo "<div class=\"alert alert-warning\" style=\"text-align: center;\">La mise à jour de l'image peut prendre jusqu'à 7 jours</div>";

    $type = strtolower(substr(strrchr($_FILES["UploadedFile"]["name"],"."),1));
    if ($type != 'jpeg' && $type != 'jpg') $alert->error('Format', 'L\'image doit être au format JPEG');
		else {
      $alert->info('Image envoyée', 'La mise à jour de l\'image peut prendre jusqu\'à 7 jours');
      // création de la vignette
  		vignette("$file|".@$_FILES["UploadedFile"]["name"], $dest, $userID.'.jpg', $srcWidth, $srcHeight);
  		$file = $dest.$userID.'.jpg';
      image_resize($file, $file, 150, 150, 1);
    }
	}
}







if (isset($_GET['ID']) AND (!isset($userID) || $userID == "" OR $userID == 0)) $userID = $_GET['ID'];
elseif (isset($userID) AND $userID != "" AND $userID != 0) $userID = $userID;
else $userID = 0;

// Si l'utilisateur est déjà existant, on récupère ses informations
if ($userID != 0 and $userID != "")
{


	$query = "SELECT * FROM `user_id` WHERE `_ID` = '".$userID."' ";

	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

		$userFormation 			= $row[1];
		$classID						= $row[3];
		$userAdm						= $row[8];
		$userIdent					= $row[9];
		$userName						= $row[11];
		$userFname					= $row[12];
		$userSexe						= $row[13];
		$userEmail					= $row[16];
		$userTel						= $row[18];
		$userMobile					= $row[19];
		$userBorn						= getReadableBornDate($row[20]);
		$userAddr1					= $row[21];
		$userCP							= $row[23];
		$userCity						= $row[24];

		$fieldArray = array(
			"classID" 			=> $classID,
			"userAdm" 			=> $userAdm,
			"userIdent" 		=> $userIdent,

			"userPassword"  => "",

			"userName" 			=> $userName,
			"userFname" 		=> $userFname,
			"userSexe" 			=> $userSexe,
			"userEmail" 		=> $userEmail,
			"userTel"				=> $userTel,
			"userMobile" 		=> $userMobile,
			"userBorn"			=> $userBorn,
			"userAddr1"			=> $userAddr1,
			"userCP"				=> $userCP,
			"userCity"			=> $userCity
		);

		// Champs de rubriques:
		// 2: numéro INE
		// 4: Adresse études
		// 5: CP études
		// 6: ville études
		// 7: Lieu de naissance
		// 8: Année d'entrée
		// 10: Code barre
		// 11: Prix scolarité
		// 12: Mode de règlement
		// 13: Commentaire
		// 15: Diplôme

		$rubriquesArray = array(
			2 	=> 'userINE',
			4 	=> 'userAddrStudy',
			5 	=> 'userCPStudy',
			6 	=> 'userCityStudy',
			7 	=> 'userBornCity',
			8 	=> 'userEnteringYear',
			10 	=> 'userBareCode',
			11 	=> 'userPriceScolarity',
			12 	=> 'userPayingMode',
			13 	=> 'userComment',
			15 	=> 'userDiploma'
		);

		foreach ($rubriquesArray as $key => $value) {

			$query2 = "SELECT `_valeur` FROM `rubrique_data` WHERE `_IDrubrique` = '".$key."' AND `_IDdata` = '".$userID."' ";
			$result2 = mysqli_query($mysql_link, $query2);
			// Si il y a une valeur pour la rubrique
			if (mysqli_num_rows($result2) > 0)
			{
				while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
					$$value = $row2[0];
					$rubriqueArrayFields = array($value => $$value);
					$fieldArray = array_merge($fieldArray, $rubriqueArrayFields);
				}
			}
			// Sinon, on affiche un champ vide
			else
			{
				$rubriqueArrayFields = array($value => "");
				$fieldArray = array_merge($fieldArray, $rubriqueArrayFields);
			}


		}



	}


	// on vérifie si la photo existe
	$photo = 'ged_thumbnail.php?action=userImage&fileID='.base64_encode($userID);

}


else
{
	// $userFormation 			= '';
	// $classID						= '';
	// $userAdm						= '';
	// $userIdent					= '';
	// $userName						= '';
	// $userFname					= '';
	// $userSexe						= '';
	// $userEmail					= '';
	// $userTel						= '';
	// $userMobile					= '';
	// $userBorn						= '';
	// $userAddr1					= '';
	// $userCP							= '';
	// $userCity						= '';
  $userFormation = $classID = $userAdm = $userIdent = $userName = $userFname = $userSexe = $userEmail = $userTel = $userMobile = $userBorn = $userAddr1 = $userCP = $userCity = '';

	$fieldArray = array(
		"classID" 			=> "",
		"userAdm" 			=> "",
		"userIdent" 		=> "",

		"userPassword"  => "",

		"userName" 			=> "",
		"userFname" 		=> "",
		"userSexe" 			=> "",
		"userEmail" 		=> "",
		"userTel"				=> "",
		"userMobile" 		=> "",
		"userBorn"			=> "",
		"userAddr1"			=> "",
		"userCP"				=> "",
		"userCity"			=> ""
	);

	$rubriquesArray = array(
		2 	=> 'userINE',
		4 	=> 'userAddrStudy',
		5 	=> 'userCPStudy',
		6 	=> 'userCityStudy',
		7 	=> 'userBornCity',
		8 	=> 'userEnteringYear',
		10 	=> 'userBareCode',
		11 	=> 'userPriceScolarity',
		12 	=> 'userPayingMode',
		13 	=> 'userComment',
		15 	=> 'userDiploma'
	);

	foreach ($rubriquesArray as $key => $value) {
		$rubriqueArrayFields = array($value => '');
		$fieldArray = array_merge($fieldArray, $rubriqueArrayFields);
	}

	$photo = RESSOURCE_PATH['NO_PROFILE_PICTURE'];
}



$fieldArrayLabel = array(
	"classID" 						=> 'Promotion',
	"userAdm" 						=> 'Droits de l\'utilisateur',
	"userIdent" 					=> 'Identifiant',
	"userName" 						=> 'Nom',
	"userFname" 					=> 'Prénom',
	"userSexe" 						=> 'Sexe',
	"userEmail" 					=> 'Email',
	"userTel"							=> 'Téléphone',
	"userMobile" 					=> 'Mobile',
	"userBorn"						=> 'Date de naissance',
	"userAddr1"						=> 'Adresse',
	"userCP"							=> 'Code postal',
	"userCity"						=> 'Ville',

	"userINE"							=> 'Numéro INE',
	"userAddrStudy"				=> 'Adresse d\'études',
	"userCPStudy"					=> 'Code postal d\'études',
	"userCityStudy"				=> 'Ville d\'études',
	"userBornCity"				=> 'Lieu de naissance',
	"userEnteringYear"		=> 'Année d\'entrée',
	"userBareCode"				=> 'Code barre',
	"userPriceScolarity"	=> 'Prix de la scolarité',
	"userPayingMode"			=> 'Mode de payement',
	"userComment"					=> 'Commentaire',
	"userDiploma"					=> 'Diplôme',

	"userPassword"				=> 'Mot de passe'
);

$fieldArrayType = array(
	"classID" 						=> 'select',
	"userAdm" 						=> 'hidden',
	"userIdent" 					=> 'text',
	"userName" 						=> 'text',
	"userFname" 					=> 'text',
	"userSexe" 						=> 'select',
	"userEmail" 					=> 'email',
	"userTel"							=> 'text',
	"userMobile" 					=> 'text',
	"userBorn"						=> 'text',
	"userAddr1"						=> 'text',
	"userCP"							=> 'text',
	"userCity"						=> 'text',

	"userINE"							=> 'text',
	"userAddrStudy"				=> 'text',
	"userCPStudy"					=> 'text',
	"userCityStudy"				=> 'text',
	"userBornCity"				=> 'text',
	"userEnteringYear"		=> 'text',
	"userBareCode"				=> 'text',
	"userPriceScolarity"	=> 'text',
	"userPayingMode"			=> 'text',
	"userComment"					=> 'text',
	"userDiploma"					=> 'text',

	"userPassword"				=> 'password'
);

if ($userID == "" or $userID == 0)
{
	$fieldArrayRequired = array(
		"classID" 						=> '',
		"userAdm" 						=> '',
		"userIdent" 					=> 'required',
		"userName" 						=> 'required',
		"userFname" 					=> 'required',
		"userSexe" 						=> 'required',
		"userEmail" 					=> 'required',
		"userTel"							=> '',
		"userMobile" 					=> '',
		"userBorn"						=> 'required',
		"userAddr1"						=> '',
		"userCP"							=> '',
		"userCity"						=> '',
		"userPassword"				=> 'required',

		"userINE"							=> '',
		"userAddrStudy"				=> '',
		"userCPStudy"					=> '',
		"userCityStudy"				=> '',
		"userBornCity"				=> '',
		"userEnteringYear"		=> '',
		"userBareCode"				=> '',
		"userPriceScolarity"	=> '',
		"userPayingMode"			=> '',
		"userComment"					=> '',
		"userDiploma"					=> ''
	);
}
else
{
	$fieldArrayRequired = array(
		"classID" 						=> '',
		"userAdm" 						=> '',
		"userIdent" 					=> 'required',
		"userName" 						=> 'required',
		"userFname" 					=> 'required',
		"userSexe" 						=> 'required',
		"userEmail" 					=> 'required',
		"userTel"							=> '',
		"userMobile" 					=> '',
		"userBorn"						=> '',
		"userAddr1"						=> '',
		"userCP"							=> '',
		"userCity"						=> '',
		"userPassword"				=> '',

		"userINE"							=> '',
		"userAddrStudy"				=> '',
		"userCPStudy"					=> '',
		"userCityStudy"				=> '',
		"userBornCity"				=> '',
		"userEnteringYear"		=> '',
		"userBareCode"				=> '',
		"userPriceScolarity"	=> '',
		"userPayingMode"			=> '',
		"userComment"					=> '',
		"userDiploma"					=> ''
	);
}


$fieldArraySelect = array(
	"userSexe" 			=> array('H' => 'Homme', 'F' => 'Femme')
);



// Construction du tableau des classes
$classValuesArray = array();
$selected_option = $alreadyShown = "";

$query_annee = "SELECT `_IDclass`, `_ident` FROM `campus_classe` WHERE `_visible` = 'O' ORDER BY `campus_classe`.`_code` ASC ";
$result_annee = mysqli_query($mysql_link, $query_annee);
while ($row_annee = mysqli_fetch_array($result_annee, MYSQLI_NUM)) $classValuesArray[$row_annee[0]] = $row_annee[1];

$newFieldArraySelect = array("classID" => $classValuesArray);
$fieldArraySelect = array_merge($fieldArraySelect, $newFieldArraySelect);
?>




<?php if (isset($modif_status) && $modif_status == "success") { ?>
	<script>
		$(document).ready(function(){
			Toast.fire({
				icon: 'success',
				title: 'Modification enregistrées'
			});
		});
	</script>
<?php } ?>



<h1 class="h3 mb-4 text-gray-800"><?php print($msg->read($STUDENT_STUDENTACCOUNT)); ?></h1>


<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, false); ?></h6>
  </div>
  <div class="card-body">
    <div class="row">
			<div class="col-md-3" style="text-align: center;">
				<img src="<?php echo $photo; ?>" style="width: 150px; border-radius: 100%;">
				<div class="mt-2">
					<?php echo getUserNameByID($userID); ?>
				</div>

			</div>
			<div class="col-md-9">








				<div>
					<!-- <table class="width100"> -->
						<form action="index.php?item=38&cmde=account&action=submit&ID=<?php echo $userID; ?>" method="POST" enctype="multipart/form-data">

							<input type="hidden" name="userID" value="<?php echo $userID; ?>">
							<input type="hidden" name="Litem" value="<?php echo $Litem; ?>">

							<!-- Groupe -->
							<div class="form-group">
								<label for="IDgrp"><?php echo $msg->read($STUDENT_GROUP); ?></label>
								<select class="form-control custom-select" id="IDgrp" name="IDgrp" required onchange="checkFormation()">
									<option value="" disabled><?php echo $msg->read($STUDENT_CHOOSECATEGORY); ?></option>
									<?php
										// recherche des groupes
										$query = $db->parse("SELECT `_IDgrp`, `_ident` FROM `user_group` where `_visible` = 'O' AND `_lang` = ?s ", $_SESSION["lang"]);
										if ($_SESSION['CnxAdm'] != 255) $query .= $db->parse("AND `_IDgrp` = ?i ", $_SESSION['CnxGrp']);
										$query .= $db->parse("ORDER BY `_IDgrp` ASC ");
										foreach ($db->getAll($query) as $group_data)
										{
											if ($userFormation == $group_data->_IDgrp) $selected = 'selected'; else $selected = '';
                      echo '<option value="'.$group_data->_IDgrp.'" '.$selected.'>'.$msg->getTrad($group_data->_ident).'</option>';
										}
									?>
								</select>
							</div>


							<?php
								foreach ($fieldArray as $key => $value) {
									if ($fieldArrayType[$key] != 'hidden')
									{
										echo '<div class="form-group" id="tr_'.$key.'">';
											echo '<label for="'.$key.'">'.$fieldArrayLabel[$key].'</label>';
											if ($fieldArrayRequired[$key] == "required") $required = 'required'; else $required = '';
											if ($fieldArrayType[$key] == "text" or $fieldArrayType[$key] == "email" or $fieldArrayType[$key] == "password" or $fieldArrayType[$key] == "number")
											{
												echo '<input type="text" class="form-control" id="'.$key.'" name="'.$key.'" value="'.$value.'" '.$required.'>';
												// if ($required == "required") echo "<span style=\"color:#FF0000;\">*</span>";
												if ($key == 'userEmail') echo '<small id="errorMail" style="display: none;" class="form-text text-muted">Cet adresse mail est déjà utilisée</small>';
												if ($key == 'userIdent') echo '<small id="errorIdent" style="display: none;" class="form-text text-muted">Cet identifiant est déjà utilisé</small>';
											}
											elseif ($fieldArrayType[$key] == "select")
											{
												echo '<select class="form-control custom-select" id="'.$key.'" name="'.$key.'">';
													foreach ($fieldArraySelect[$key] as $key2 => $value2) {
														if ($value == $key2) $selected = "selected";
														else $selected = "";
														echo '<option value="'.$key2.'" '.$selected.'>'.$value2.'</option>';
													}
												echo '</select>';
											}
										echo '</div>';
									}
								}
							?>

							<div class="form-group">
								<label for="profile_picture">Image de profil</label>
								<div class="custom-file">

									<input type="file" name="UploadedFile" class="custom-file-input" id="profile_picture">
									<label class="custom-file-label" for="profile_picture" data-browse="Choisir">Choisissez un fichier</label>
								</div>
							</div>

							<script>
								// Affiche le nom du fichier dans la zone de choix de fichier quand upload
								$('#profile_picture').on('change',function(){
									//get the file name
									var fileName = $(this).val();
									fileName = fileName.replace('C:\\fakepath\\', " ");
									//replace the "Choose a file" label
									$(this).next('.custom-file-label').html(fileName);
								});
							</script>



							<div class="mt-3">
								<a href="index.php?item=<?php echo $Litem; ?>" class="btn btn-danger">Fermer</a>
								<input id="validate_btn" type="submit" class="btn btn-success" value="Valider">
							</div>

						</form>
					<!-- </table> -->
				</div>





			</div>


		</div>
  </div>
</div>



<script>
	// $('#IDgrp').change(function(){
	// 	checkFormation();
	// });

	//IPS HD 04/08/2020
	// checkFormation();

	// // Est-ce que l'on affiche le select de la promo au chargement de la page ?
	// $(document).ready(function(){
	// 	checkFormation();
	// });


	function checkFormation() {
		switch ($("#IDgrp").val()) {
			case '1': $("#tr_classID").show(); break;
			default:  $("#tr_classID").hide(); break;
		}
	}



  // On vérifie que l'adresse mail n'est pas déjà utilisé
	$('#userEmail').keyup(function() {
		var email = $('#userEmail').val();
		var emailLenght = $('#userEmail').val().length;
		$.ajax({
			url : 'include/fonction/ajax/user_new.php?action=checkMail',
			type : 'POST', // Le type de la requête HTTP, ici devenu POST
			data : 'email=' + email,

			dataType : 'html', // On désire recevoir du HTML
			success : function(code_html, statut){ // code_html contient le HTML renvoyé
				if (code_html == "success") {
					$("#errorMail").hide();
					$("#validate_btn").show();
				}
				else {
					$("#errorMail").show();
					$("#validate_btn").hide();
				}
			}
		});
	});

  // On vérifie que l'identifiant n'est pas déjà utilisé
	$('#userIdent').keyup(function() {
		var ident = $('#userIdent').val();
		$.ajax({
			url : 'include/fonction/ajax/user_new.php?action=checkIdent',
			type : 'POST', // Le type de la requête HTTP, ici devenu POST
			data : 'ident=' + ident,

			dataType : 'html', // On désire recevoir du HTML
			success : function(code_html, statut){ // code_html contient le HTML renvoyé
				if (code_html == "success") {
					$("#errorIdent").hide();
					$("#validate_btn").show();
				}
				else {
					$("#errorIdent").show();
					$("#validate_btn").hide();
				}
			}
		});
	});
</script>
