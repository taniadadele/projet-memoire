<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2008-2009 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : admin_import.php
 *		projet   : la page de gestion des imports dans la base de données
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 10/09/08
 *		modif    :
 */



$onglet = (	@$_POST["onglet"] )		// le n° de l'onglet
	? (int) $_POST["onglet"]
	: (int) @$_GET["onglet"] ;
$dv = (	@$_GET["dv"] );

$submit = @$_POST["valid_x"];			// bouton de validation


//$max_time = min(get_cfg_var("max_execution_time"), get_cfg_var("max_input_time"));

//---------------------------------------------------------------------------
// function get_center($centre, $lang, $nbrec = 0)
// {
// // $centre, nom du centre
// // $lang, langue utilisée
// // $nbrec, n° enregistrement
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	// lecture code centre
// 	$query  = "select _IDcentre from config_centre ";
// 	$query .= "where _ident = '$centre' AND _lang = '$lang' ";
// 	$query .= "limit 1";
//
// 	$return = mysqli_query($mysql_link, $query);
// 	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;
//
// 	// si le centre n'existe pas on le crée
// 	if ( $myrow[0] )
// 		$idcentre = $myrow[0];
// 	else
// 		if ( $centre != "" ) {
// 			$sql  = "insert into config_centre ";
// 			$sql .= "values('".sql_getunique("config_centre")."', '$centre', '', '', '', '', '', 'O', '$lang')";
//
// 			$idcentre = ( mysqli_query($mysql_link, $sql) ) ? mysqli_insert_id($mysql_link) : 0 ;
// 			}
// 		else
// 			$idcentre = 0;
//
// 	// on trace l'erreur
// 	if ( !$idcentre )
// 		mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ($nbrec) ".addslashes($query)."')");
//
// 	return $idcentre;
// }
//---------------------------------------------------------------------------
function getCodeIDClasse($codeclass) {
/***** Récupère les ID d'une classe à partir des codes *****/
	require $_SESSION["ROOTDIR"]."/globals.php";

	$valclaasID = "";
	$classpptab = explode(",",$codeclass);
	foreach($classpptab as $classcode)
	{
		$query  = "select _IDclass from campus_classe ";
		// $query .= "where _ident = '$classcode' ";
		$query .= "where _code = '$classcode' ";
		$query .= "limit 1";

		$return = mysqli_query($mysql_link, $query);
		$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;
		if ($valclaasID != "") $valclaasID .= " ";
		$valclaasID .= $myrow[0];
	}

	return $valclaasID;
}
//---------------------------------------------------------------------------
function getCodeIDClasseUnique($codeclass) {
/***** Récupère l'ID d'une classe à partir des codes *****/
	require $_SESSION["ROOTDIR"]."/globals.php";

	$query  = "select _IDclass from campus_classe ";
	$query .= "where _ident = '$codeclass' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return $myrow[0];

}
//---------------------------------------------------------------------------
function setIDClassePP($ID, $IDclass) {
/***** Récupère les ID d'une classe à partir des codes *****/
	require $_SESSION["ROOTDIR"]."/globals.php";


	$query  = "select _IDpp, _IDpp_2 from campus_classe ";
	$query .= "where _IDclass = '$IDclass' ";
	$query .= "limit 1";
	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	if ($myrow[0] == 0) {
		$query  = "update campus_classe set ";
		$query .= "_IDpp = '$ID' ";
		$query .= "where _IDclass = '$IDclass' ";
		$return = mysqli_query($mysql_link, $query);

	} else {
		if ($myrow[1] == 0) {
		$query  = "update campus_classe set ";
		$query .= "_IDpp_2 = '$ID' ";
		$query .= "where _IDclass = '$IDclass' ";
		$return = mysqli_query($mysql_link, $query);

		}
	}

}
//---------------------------------------------------------------------------
function getCodeIDmat($codemat) {
/***** Récupère les ID d'une matière à partir des codes *****/
	require $_SESSION["ROOTDIR"]."/globals.php";

	$query  = "select _IDmat from campus_data ";
	$query .= "where _code = '$codemat' AND _lang = 'fr' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return $myrow[0];
}
//---------------------------------------------------------------------------
function getCodeIDsalle($codesalle) {
/***** Récupère les ID d'une salle à partir des codes *****/
	require $_SESSION["ROOTDIR"]."/globals.php";
	$codesalle = rtrim($codesalle);
	$query  = "select _IDitem from edt_items ";
	$query .= "where _title = '$codesalle' AND _lang = 'fr' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	$IDsalle = $myrow[0];

	if ($IDsalle == "") {

	$IDitem = sql_getunique("edt_items");

	$query  = "insert into edt_items set ";
	$query .= "_IDitem = '$IDitem', ";
	$query .= "_IDcentre = '1', ";
	$query .= "_title = '$codesalle', ";
	$query .= "_lang = 'fr' ";

	$return = mysqli_query($mysql_link, $query);

	$query = str_replace("'fr' ", "'de' ", $query);
	$return = mysqli_query($mysql_link, $query);



	$IDsalle = $IDitem;

	}

	return $IDsalle;
}
//---------------------------------------------------------------------------
function getIDcodeProf($codeprof) {
/***** Récupère les ID du prof à partir de son code *****/
	require $_SESSION["ROOTDIR"]."/globals.php";

	$query  = "select _ID from user_id ";
	$query .= "where _code = '$codeprof' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return $myrow[0];
}
//---------------------------------------------------------------------------
function getIDcentreClass($codeclass) {
/***** Récupère les ID du prof à partir de son code *****/
	require $_SESSION["ROOTDIR"]."/globals.php";

	$query  = "select _IDcentre from campus_classe ";
	$query .= "where _code = '$codeclass' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return $myrow[0];
}
//---------------------------------------------------------------------------
function getuniqueedt($table) {


	require $_SESSION["ROOTDIR"]."/globals.php";


	$query  = "select _IDdata from $table ";
	$query .= "order by _IDdata desc ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	$newnum = ($myrow[0])+1;

	return $newnum;
}
//---------------------------------------------------------------------------
function getuniqueuserid($table) {


	require $_SESSION["ROOTDIR"]."/globals.php";


	$query  = "select _ID from $table ";
	$query .= "order by _ID desc ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	$newnum = ($myrow[0])+1;

	return $newnum;
}
//---------------------------------------------------------------------------
function get_group($group, $idcat, $lang, $nbrec = 0)
{
// $group, nom du groupe
// $idcat, catégorie du groupe
// $lang, langue utilisée
// $nbrec, n° enregistrement

	require $_SESSION["ROOTDIR"]."/globals.php";

	// lecture code classe
	$query  = "select _IDgrp from user_group ";
	$query .= "where _ident = '$group' AND _lang = '$lang' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	// si la classe n'existe pas on la crée
	if ( $myrow[0] )
		$idgroup = $myrow[0];
	else
		if ( $group != "" ) {
			$sql  = "insert into user_group ";
			$sql .= "values('".sql_getunique("user_group")."', '$group', '0000-00-00 00:00:00', '1024', '$idcat', 'O', '$lang')";

			$idgroup = ( mysqli_query($mysql_link, $sql) ) ? mysqli_insert_id($mysql_link) : 0 ;
			}
		else
			$idgroup = 0;

	// on trace l'erreur
	if ( !$idgroup )
		mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ($nbrec) ".addslashes($query)."')");

	return $idgroup;
}
//---------------------------------------------------------------------------
function get_campus_class($class, $idcentre, $nbrec = 0)
{
// $class, nom de la classe
// $idcentre, Id du centre
// $nbrec, n° enregistrement

	require $_SESSION["ROOTDIR"]."/globals.php";

	// erreur
	if ( !$idcentre )
		return 0;

	// lecture code classe
	$query  = "select _IDclass from campus_classe ";
	$query .= "where _ident = '$class' AND _IDcentre = '$idcentre' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	// si la classe n'existe pas on la crée
	if ( $myrow[0] )
		$idclass = $myrow[0];
	else
		if ( $class != "" ) {
			$query   = "insert into campus_classe ";
			$query  .= "values('', '0', '0', '255', '$idcentre', '$class', '', '0', '0', 'N', 'O', '', 'O')";

			$idclass = ( mysqli_query($mysql_link, $query) ) ? mysqli_insert_id($mysql_link) : 0 ;
			}
		else
			$idclass = 0;

	// on trace l'erreur
	if ( !$idclass )
		mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ($nbrec) ".addslashes($query)."')");

	return $idclass;
}
//---------------------------------------------------------------------------
function get_stage_secteur($secteur, $lang)
{
// $secteur, intitulé du secteur
// $lang, langue utilisée

	require $_SESSION["ROOTDIR"]."/globals.php";

	// erreur
	if ( !strlen($secteur) )
		return 0;

	// lecture code secteur
	$query  = "select _IDsecteur from stage_secteur ";
	$query .= "where _ident = '$secteur' AND _lang = '$lang' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	// si le secteur n'existe pas on le crée
	if ( $myrow[0] )
		$idsecteur = $myrow[0];
	else {
		// lecture code secteur
		$query  = "select _IDsecteur from stage_secteur ";
		$query .= "where _lang = '$lang' ";

		$return = mysqli_query($mysql_link, $query);
		$nbrow  = ( $return ) ? mysqli_num_rows($return) : 0 ;

		$query  = "insert into stage_secteur ";
		$query .= "values('', '$nbrow', '$secteur', 'N', 'O', '$lang')";

		$idsecteur = ( mysqli_query($mysql_link, $query) ) ? mysqli_insert_id($mysql_link) : 0 ;
		}


	return $idsecteur;
}
//---------------------------------------------------------------------------
function exist_student($name, $fname, $IDclass)
{
// $name, nom de l'élève
// $fname, prénom de l'élève
// $idclass, ID de la classe

	require $_SESSION["ROOTDIR"]."/globals.php";

	// lecture élève
	$query   = "select _ID from user_id ";
	$query  .= "where _name = '$name' AND _fname = '$fname' AND _IDclass = '$IDclass' ";
	$query  .= "limit 1";

	$return  = mysqli_query($mysql_link, $query);
	$myrow   = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return ( $myrow ) ? $myrow[0] : 0 ;
}
//---------------------------------------------------------------------------
function exist_parent($ideleve, $record)
{
// $ideleve, ID de l'élève
// $record, n° du champ

	require $_SESSION["ROOTDIR"]."/globals.php";

	// lecture utilisateur
	$query   = "select _IDtutor from user_tutors ";
	$query  .= "where _ID = '$ideleve' ";
	$query  .= "order by _index asc";

	$return  = mysqli_query($mysql_link, $query);
	$myrow   = ( @mysqli_data_seek($return, $record - 1) ) ? mysqli_fetch_row($return) : 0 ;

	return ( $myrow ) ? $myrow[0] : 0 ;
}
//---------------------------------------------------------------------------
function exist_user($name, $fname)
{
// $name, nom de l'utilisateur
// $fname, prénom de l'utilisateur

	require $_SESSION["ROOTDIR"]."/globals.php";

	// lecture utilisateur
	$query   = "select _ID from user_id ";
	$query  .= "where _name = '$name' AND _fname = '$fname' ";
	$query  .= "limit 1";

	$return  = mysqli_query($mysql_link, $query);
	$myrow   = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return ( $myrow ) ? $myrow[0] : 0 ;
}
//---------------------------------------------------------------------------
function import_student_xls($file)
{
	$preview = @$_POST["preview"];
	$IDcentre = @$_POST["IDcentre"];
	$lang = $_SESSION["lang"];
	require $_SESSION["ROOTDIR"]."/globals.php";
	$filetrad = file_get_contents($file, 'a+');
	//$filetrad = utf8_decode($filetrad);
	//Previsalisation
	if ($preview == "on"){
			print "<table border='1'>";
			$lignes = explode("\n",$filetrad);

			$taillelignes = sizeof($lignes);
			if ($taillelignes > 11) $taillelignes = 11;
			for($i=0; $i<$taillelignes; $i++)
					{
					print "<tr>";
					$cels = explode(";",$lignes[$i]);
					for($j=0; $j<sizeof($cels); $j++)
						{
						print "<td>".$cels[$j]."</td>";
						}
					print "</tr>";
					}


			print "</table>";
	}
	else {
	//Import

			$count = 0;
			$countde = 0;
			$countmaj = 0;
			$countmajde = 0;
			$lignes = explode("\n",$filetrad);
			$taillelignes = sizeof($lignes);
			if ($taillelignes > 11) $taillelignes = 11;
			for($i=1; $i<$taillelignes; $i++)
					{
					if (strpos($lignes[$i], ";") >0) {
					$cels = explode(";",$lignes[$i]);

					// lecture login deja existante
					$query  = "select _ID from user_id ";
					$query .= "where _ident = '$cels[3]' ";
					$query .= "limit 1";

					$return = mysqli_query($mysql_link, $query);
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;


					//On traite les valeurs
					$IDclass = getCodeIDClasse($cels[0]); //OK
					$sexe = $cels[6]; //OK
					if ($sexe == "w") $sexe = "F";
					if ($sexe == "m") $sexe = "H";
					$dateborn = substr($cels[5], 6, 4)."-".substr($cels[5], 3, 2)."-".substr($cels[5], 0, 2); //OK
					$adr2 = $cels[10];
					if ($adr2 == "Adr2") $adr2 = "";
					$datecreate = date("Y-m-d H:i:s");

					// si l'identifiant n'existe pas on le crée
					if ($myrow[0] == '') {
						$IDuser = getuniqueuserid("user_id");
						$query  = "insert into user_id set ";
						// $query .= "_ID = '$IDuser', ";
						$query .= "_ID = NULL, ";
						$query .= "_IDcentre = '1', ";
            if ($IDclass) $query .= "_IDclass = '$IDclass', ";
						else $query .= "_IDclass = NULL, ";
						$query .= "_create = NOW(), ";
						$query .= "_ident = '".trim($cels[3])."', ";
						$query .= "_passwd = MD5('$cels[4]'), ";
						$query .= "_name = '".trim(addslashes($cels[1]))."', ";
						$query .= "_fname = '".trim(addslashes($cels[2]))."', ";
						$query .= "_sexe = '".trim($sexe)."', ";
						$query .= "_born = '".trim($dateborn)."', ";
						$query .= "_adr1 = '".trim(addslashes($cels[9]))."', ";
						$query .= "_adr2 = '".trim(addslashes($adr2))."', ";
						$query .= "_cp = '".trim($cels[8])."', ";
						$query .= "_city = '".trim(addslashes($cels[7]))."', ";
						$query .= "_tel = '".trim(addslashes($cels[11]))."', ";
						$query .= "_mobile = '".trim(addslashes($cels[12]))."', ";
						$query .= "_email = '".trim($cels[13])."' ";


						//On cree l'id
						if (mysqli_query($mysql_link, $query)) $count++;
						else mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");
					}
					else {
					// si l'identifiant existe on la met à jour


						$query  = "update user_id set ";
						$query .= "_IDcentre = '1', ";
						if (!$IDclass) $query .= "_IDclass = NULL, ";
						else $query .= "_IDclass = '$IDclass', ";
						$query .= "_create = NOW(), ";
						$query .= "_name = '".stripslashes($cels[1])."', ";
						$query .= "_fname = '".stripslashes($cels[2])."', ";
						$query .= "_sexe = '$sexe', ";
						$query .= "_born = '$dateborn', ";
						$query .= "_adr1 = '".stripslashes($cels[9])."', ";
						$query .= "_adr2 = '".stripslashes($adr2)."', ";
						$query .= "_cp = '$cels[8]', ";
						$query .= "_city = '".stripslashes($cels[7])."', ";
						$query .= "_tel = '".stripslashes($cels[11])."', ";
						$query .= "_mobile = '".stripslashes($cels[12])."', ";
						$query .= "_email = '$cels[13]' ";
						$query .= "where _ID = '$myrow[0]' ;";

						//On met à jour
						if ( mysqli_query($mysql_link, $query) ) {
							$countmaj++;
							}
						else {
							mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");
							}
						}

					}
					}
			if ($count > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " + ID : ".$count."<br></div>";
				}
			if ($countmaj > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " # ID : ".$countmaj."<br></div>";
				}
			}



}
//---------------------------------------------------------------------------
function import_mat_xls($file)
{
	$preview = @$_POST["preview"];
	$IDcentre = @$_POST["IDcentre"];
	$lang = $_SESSION["lang"];
	require $_SESSION["ROOTDIR"]."/globals.php";
	$filetrad = file_get_contents($file, 'a+');
	//Previsalisation
	if ($preview == "on"){
			print "<table border='1'>";
			$lignes = explode("\n",$filetrad);

			$taillelignes = sizeof($lignes);
			if ($taillelignes > 11) $taillelignes = 11;
			for($i=0; $i<$taillelignes; $i++)
					{
					print "<tr>";
					$cels = explode(";",$lignes[$i]);
					for($j=0; $j<sizeof($cels); $j++)
						{
						print "<td>".$cels[$j]."</td>";
						}
					print "</tr>";
					}


			print "</table>";
	}
	else {
	//Import
			$count = 0;
			$countde = 0;
			$countmaj = 0;
			$countmajde = 0;
			//$filetrad = utf8_decode($filetrad);
			$lignes = explode("\n",$filetrad);
			$taillelignes = sizeof($lignes);
			for($i=1; $i<$taillelignes; $i++)
					{
					if (strpos($lignes[$i], ";") >0) {
					$cels = explode(";",$lignes[$i]);

					// lecture matiere deja existante
					$query  = "select _IDmat from campus_data ";
					$query .= "where _code = '$cels[0]' AND _lang = '$lang' ";
					$query .= "limit 1";

					$return = mysqli_query($mysql_link, $query);
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

					// si la matiere n'existe pas on la crée
					if ($myrow[0] == "") {
						$query  = "insert into campus_data values(";
						$query .= "'".sql_getunique("campus_data")."', ";
						$query .= "'0', ";
						$query .= "'4', ";
						$query .= "'255', ";
						$query .= "'', ";
						$query .= "'$cels[1]', ";	//Nom de la matiere
						$query .= "'', ";
						$query .= "10, ";
						$query .= "'N', ";
						$query .= "'N', ";
						$query .= "'', ";
						$query .= "'O', ";
						$query .= "'fr', ";
						$query .= "'$cels[2]', ";	//Couleur de la matiere
						$query .= "'0', ";
						$query .= "'$cels[0]', ";		//Code la matiere
						$query .= "'1' ";		//Code la matiere
						$query .= ")";

						//On cree la matiere en FR
						if ( mysqli_query($mysql_link, $query) )
							$count++;
						else
							mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");

						}

					else {
					// si la matiere existe on la met à jour

						$query  = "update campus_data set ";
						$query .= "_titre = '".$cels[1]."' ";					// droits
						$query .= "where _code = '$cels[0]' AND _lang = 'fr' ";

						//On cree la matiere en FR
						if ( mysqli_query($mysql_link, $query) )
							$countmaj++;
						else
							mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");

						}

					}
					}
			if ($count > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " + : ".$count."<br></div>";
				}
			if ($countmaj > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " # : ".$countmaj."<br></div>";
				}
			}



}
//---------------------------------------------------------------------------
function import_student_xml($file, $IDgroup)
{
// $file, fichier source
// $IDgroup, groupe des utilisateurs

	require $_SESSION["ROOTDIR"]."/globals.php";
	require_once $_SESSION["ROOTDIR"]."/lib/lib_import_xls.php";

	$XMLDoc = new DOMDocument();							// objet document
	$XMLDoc->load($file);

	// Test la Validite du fichier XML
	if ( $XMLDoc->documentElement->nodeName != "BEE_ELEVES" )		// vérifier la racine
		return 0;

	// récupération noeud <DONNEES><ELEVES>
//	$eleves = $XMLDoc->getElementsByTagName("DONNEES")->item(0)->getElementsByTagName("ELEVES");

	if ( $eleves->length == 0 )
		return 0;

	// initialisation
	$count  = 0;



	return $count;
}
//---------------------------------------------------------------------------
function import_classes_xls($file)
{
	$preview = @$_POST["preview"];
	$IDcentre = @$_POST["IDcentre"];
	$lang = $_SESSION["lang"];
	require $_SESSION["ROOTDIR"]."/globals.php";
	$filetrad = file_get_contents($file, 'a+');
	//Previsalisation
	if ($preview == "on"){
			print "<table border='1'>";
			$lignes = explode("\n",$filetrad);

			$taillelignes = sizeof($lignes);
			if ($taillelignes > 11) $taillelignes = 11;
			for($i=0; $i<$taillelignes; $i++)
					{
					print "<tr>";
					$cels = explode(";",$lignes[$i]);
					for($j=0; $j<sizeof($cels); $j++)
						{
						print "<td>".utf8_decode($cels[$j])."</td>";
						}
					print "</tr>";
					}


			print "</table>";
	}
	else {
	//Import
			$count = 0;
			$countde = 0;
			$countmaj = 0;
			$countmajde = 0;
			$filetrad = utf8_decode($filetrad);
			$lignes = explode("\n",$filetrad);
			$taillelignes = sizeof($lignes);
			for($i=1; $i<$taillelignes; $i++)
					{
					$cels = explode(";",$lignes[$i]);


					if (substr($cels[2], 0, 1) == "C") $centre = "1";
					if (substr($cels[2], 0, 1) == "L") $centre = "2";

					// lecture classe deja existante
					$query  = "select _IDclass from campus_classe ";
					$query .= "where _ident = '$cels[0]' AND _IDcentre = '$centre' ";
					$query .= "limit 1";

					$return = mysqli_query($mysql_link, $query);
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

										// si l'identifiant n'existe pas on le crée
					if ($myrow[0] == "" && $cels[0] != "") {
						$query  = "insert into campus_classe set ";
						$query .= "_ident = '$cels[0]', ";
						$query .= "_IDcentre = '$centre', ";
						$query .= "_IDgrpwr = '2', ";
						$query .= "_IDgrprd = '255', ";
						$query .= "_private = 'N', ";
						$query .= "_auto = 'N', ";
						$query .= "_code = '$cels[0]' ";


						//On cree la classe
						if ( mysqli_query($mysql_link, $query) )
							$count++;
						else
							mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");

						}

					else {
					// si l'identifiant existe on la met à jour

						$query  = "update campus_classe set ";
						$query .= "_ident = '$cels[0]', ";
						$query .= "_IDcentre = '$centre', ";
						$query .= "_code = '$cels[0]' ";

						$query .= "where _IDclass = '$myrow[0]' ";

						//On cree l'id
						if ( mysqli_query($mysql_link, $query) )
							$countmaj++;
						else
							mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");

						}

					}
			if ($count > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " + ID : ".$count."<br></div>";
				}
			if ($countmaj > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " # ID : ".$countmaj."<br></div>";
				}
			}
}
//---------------------------------------------------------------------------
function import_edt_xls($file)
{
	$preview = @$_POST["preview"];
	$datesup = @$_POST["datesup"];
	$IDcentre = @$_POST["IDcentre"];
	$lang = $_SESSION["lang"];
	require $_SESSION["ROOTDIR"]."/globals.php";
	$filetrad = file_get_contents($file, 'a+');
	$filetrad = utf8_decode($filetrad);
	if (substr($filetrad, 0, 1) == "?") $filetrad = substr($filetrad, 1);
	$lignes = explode("\n",$filetrad);
	$taillelignes = sizeof($lignes);
	$cels = explode(",",$lignes[0]);


	//Suppression des EDT après la première date
	if ($datesup != "") {
		list($jour, $mois, $annee) = explode('/', $datesup);
		$debutfichier = mktime(0, 0, 0, $mois, $jour, $annee);
		$numweek = date('W', $debutfichier);
		$numweek2 = $numweek + 1;
		$numday = (date('w', mktime(0, 0, 0, $mois, $jour, $annee)))-1;
		if ($numday == -1) $numday = 6;

		$query = " DELETE FROM edt_data WHERE ( _nosemaine >= '$numweek' and _jour >= $numday and _annee = $cels[1] ) OR ( _nosemaine >= '$numweek2' and _annee = $cels[1] ) OR _annee > $cels[1] ";
		$return = mysqli_query($mysql_link, $query);
		setParam("I_DV", "0", "0");
	}


	//Previsalisation
	if ($preview == "on"){
			print "<table border='1'>";



			if ($taillelignes > 11) $taillelignes = 11;
			for($i=0; $i<$taillelignes; $i++)
					{
					print "<tr>";
					$cels = explode(",",$lignes[$i]);
					for($j=0; $j<sizeof($cels); $j++)
						{
						print "<td>".utf8_decode($cels[$j])."</td>";
						}
					print "</tr>";
					}


			print "</table>";
	}
	else {
	//Import
			$count = 0;
			$countmaj = 0;
			$heuredouble = 0;

			for($i=0; $i<$taillelignes; $i++)
					{
					if (strpos($lignes[$i], ",") >0) {

					$cels = explode(",",$lignes[$i]);
					$classe =$cels[6];
					$grclasse = "";
					if (strcmp(substr($classe, -1), "F") === 0) {$classe = substr($classe, 0, -1); $grclasse = "1"; }
					if (strcmp(substr($classe, -1), "A")  === 0) {$classe = substr($classe, 0, -1); $grclasse = "2"; }

					$IDclass = getCodeIDClasseUnique($classe);
					$heuredebut = "";
					$heurefin = "";


					$IDprof = getIDcodeProf($cels[0]);
					$centre = getIDcentreClass($classe);
					$IDmat = getCodeIDmat($cels[5]);

					$IDsalle = getCodeIDsalle($cels[7]);


					switch ($cels[4]) {
    					case "1": $heuredebut = "07:50:00"; $heurefin = "08:35:00"; break;
    					case "2": $heuredebut = "08:35:00"; $heurefin = "09:20:00"; break;
    					case "3": $heuredebut = "09:40:00"; $heurefin = "10:25:00"; break;
    					case "4": $heuredebut = "10:25:00"; $heurefin = "11:10:00"; break;
    					case "5": $heuredebut = "11:30:00"; $heurefin = "12:15:00"; break;
    					case "6": $heuredebut = "12:15:00"; $heurefin = "13:00:00"; break;
    					case "7": $heuredebut = "13:00:00"; $heurefin = "13:50:00"; break;
    					case "8": $heuredebut = "13:50:00"; $heurefin = "14:35:00"; break;
    					case "9": $heuredebut = "14:35:00"; $heurefin = "15:20:00"; break;
    					case "10": $heuredebut = "15:25:00"; $heurefin = "16:10:00"; break;
    					case "11": $heuredebut = "16:15:00"; $heurefin = "17:00:00"; break;
    					case "12": $heuredebut = "17:00:00"; $heurefin = "17:45:00"; break;
						}



					$numweek = date('W', mktime(0, 0, 0, $cels[2], $cels[3], $cels[1]));
					$numday = (date('w', mktime(0, 0, 0, $cels[2], $cels[3], $cels[1])))-1;

					$IDclass = ";".$IDclass.";";
					$IDclass = str_replace(";;", ";", $IDclass);
					$IDclass = str_replace(";;", ";", $IDclass);
					$IDclass = str_replace(";;", ";", $IDclass);
					if ($IDclass == ";" ) $IDclass = "";

					if ($numday == -1) $numday = 6;
					$IDdata = getuniqueedt("edt_data");

					$query  = "insert into edt_data set ";
					$query .= "_IDdata = '$IDdata', ";
					$query .= "_IDedt = '3', ";
					$query .= "_IDcentre = '$centre', ";
					$query .= "_IDmat = '$IDmat', ";
					$query .= "_IDclass = '$IDclass', ";
					$query .= "_IDitem = '$IDsalle', ";
					$query .= "_ID = '$IDprof', ";
					$query .= "_group = '$grclasse', ";
					$query .= "_jour = '$numday', ";
					$query .= "_debut = '$heuredebut', ";
					$query .= "_fin = '$heurefin', ";
					$query .= "_nosemaine = '$numweek', ";
					$query .= "_etat = '1', ";
					$query .= "_annee = '$cels[1]' ";


					//On cree l'EDT

					if ($IDclass != ";" ) {
					mysqli_query($mysql_link, $query);
					$count++;
					}


					}

			}

			if ($count > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " + ID : ".$count."<br></div>";
				}
			if ($countmaj > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " # ID : ".$countmaj."<br></div>";
				}

		}
}
//---------------------------------------------------------------------------
function import_edt_fusion()
{
	global $doublon, $dv;
	$doublon = 0;
	$groupclass = "";
	$groupidsup = "";
	require $_SESSION["ROOTDIR"]."/globals.php";

	$queryparamlast = "";
	$paramlast = getParam("I_DV", "0");
	if ($dv != "on") $paramlast = $paramlast - 15000;
	if ($paramlast > 0) $queryparamlast = "WHERE _IDdata > $paramlast";
	$query  = "SELECT * FROM `edt_data` $queryparamlast ORDER BY `_nosemaine`, `_jour`, `_IDclass`, `_IDitem`, `_ID`, `_debut` ASC ";

	$result = mysqli_query($mysql_link, $query);

	$fusionmodif = array();
	$fusionsup = array();

	for($i=0; $i<mysqli_num_rows($result); $i++)	 {
    mysqli_data_seek($result, $i);
	$row = remove_magic_quotes(mysqli_fetch_row($result));
			$ligne = $row[3]."--".$row[4]."--".$row[5]."--".$row[6]."--".$row[9]."--".$row[13];

		if ($i+1<mysqli_num_rows($result)) {
			mysqli_data_seek($result, $i+1);
			$rows = remove_magic_quotes(mysqli_fetch_row($result));
			$lignes = $rows[3]."--".$rows[4]."--".$rows[5]."--".$rows[6]."--".$rows[9]."--".$rows[13];
			}


		if ( $ligne == $lignes && $row[15] != $rows[15] ) {
			if ($row[8] !== $rows[8]) { $supgroup = ", _group = ''"; } else { $supgroup = "";}
			if ($rows[11] > $row[11]) { $newheurefin = $rows[11]; } else { $newheurefin = $row[11]; }
			$fusionmodif[] = " UPDATE edt_data SET _fin = '$newheurefin'".$supgroup." WHERE _IDx = '$row[15]' ";
			$fusionsup[] = " DELETE FROM edt_data WHERE _IDx = '$rows[15]' ";
			$doublon++ ;
			}

	}

	 foreach($fusionmodif as $query)
		{
		$result = mysqli_query($mysql_link, $query);
		}

	foreach($fusionsup as $query)
		{
		$result = mysqli_query($mysql_link, $query);
		}

}
//---------------------------------------------------------------------------
function import_edt_fusionprof()
{
	global $doublon, $dv;
	$doublon = 0;
	$groupclass = "";
	$groupidsup = "";
	require $_SESSION["ROOTDIR"]."/globals.php";


	//FUSION PROF

	$queryparamlast = "";
	$paramlast = getParam("I_DV", "0");
	if ($dv != "on") $paramlast = $paramlast - 15000;
	if ($paramlast > 0) $queryparamlast = "WHERE _IDdata > $paramlast";

	$query  = "SELECT * FROM `edt_data` $queryparamlast ORDER BY `_nosemaine` ASC, `_jour` ASC, `_IDitem` ASC, `_debut` ASC, `_ID` ASC, `_IDclass` DESC  ";

	$result = mysqli_query($mysql_link, $query);
	$fusionmodif = array();
	$fusionsup = array();

	for($i=0; $i<mysqli_num_rows($result); $i++)	 {
    mysqli_data_seek($result, $i);
	$row = remove_magic_quotes(mysqli_fetch_row($result));

	if (strpos($groupidsup, ';'.$row[15].';')  === false ) {
		$groupclass = $row[4];
		$query2  = "SELECT _IDclass, _group, _IDx FROM `edt_data` WHERE _IDmat = '$row[3]' AND _IDitem = '$row[5]' AND _ID = '$row[6]' AND _jour = '$row[9]' AND _debut = '$row[10]'  AND _fin = '$row[11]' AND _nosemaine = '$row[13]'  AND _idx != '$row[15]' ORDER BY `_IDclass` ASC ";
		$result2 = mysqli_query($mysql_link, $query2);
		$row2    = ( $result2 ) ? remove_magic_quotes(mysqli_fetch_row($result2)) : 0 ;
		while ( $row2 ) {
			$groupidclass = explode(";", $row2[0]);
			foreach($groupidclass as $classeunique)
				{
				if (strpos($groupclass, $classeunique)  === false ) $groupclass .= ";".$classeunique.";";
				$groupclass = str_replace(";;", ";", $groupclass);
				$groupclass = str_replace(";;", ";", $groupclass);
				$groupclass = str_replace(";;", ";", $groupclass);
				}
				$groupidsup .= ";".$row2[2].";";
				$groupidsup = str_replace(";;", ";", $groupidsup);
				$grouplang = "";
				if ($row2[1] == $row[14]) { $grouplang = $row[14]; } else { $grouplang = ""; }
				$groupattribut = "";
				if (substr_count($groupclass, ";") > 2) { $groupattribut = "1"; } else { $groupattribut = ""; }
				$row2 = remove_magic_quotes(mysqli_fetch_row($result2));
			}
		if ($groupclass != "") $query3 = " UPDATE edt_data SET _IDclass = '$groupclass', _group = '$grouplang', _attribut = '$groupattribut' WHERE _IDx = '$row[15]' ";
		$groupclass = "";
		$result3 = mysqli_query($mysql_link, $query3);
		}

	}

	// on supprime les doublons

	$groupid = explode(";", $groupidsup);
	foreach($groupid as $idsup)
		{
		$querysup = " DELETE FROM edt_data WHERE _IDx = '$idsup' ";
		if ($idsup != "") { $result = mysqli_query($mysql_link, $querysup); $doublon++ ; }
		}

}
//---------------------------------------------------------------------------
function import_edt_group()
{

	global $doublon, $dv;
	$nbgroupe = 0;
	require $_SESSION["ROOTDIR"]."/globals.php";

	$queryparamlast = "";
	$paramlast = getParam("I_DV", "0");
	if ($dv != "on") $paramlast = $paramlast - 15000;
	if ($paramlast > 0) $queryparamlast = "AND _IDdata > $paramlast";

	$query  = "	SELECT _IDx FROM `edt_data` WHERE `_IDclass` LIKE '%;%;%;%' $queryparamlast ";
	$result = mysqli_query($mysql_link, $query);

	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
	while ( $row ) {
		$query3 = " UPDATE edt_data SET _attribut = '1' WHERE _IDx = '$row[0]' ";
		$result2 = mysqli_query($mysql_link, $query3);
		$nbgroupe++;

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}



	global $nbgroupe;
	$nbgroupe = 0;
	require $_SESSION["ROOTDIR"]."/globals.php";

	$queryparamlast = "";
	$paramlast = getParam("I_DV", "0");
	$paramlast = $paramlast - 15000;
	if ($paramlast > 0) $queryparamlast = "AND _IDdata > $paramlast";

	$query  = "SELECT _IDclass, _jour, _nosemaine, _debut, _IDx FROM `edt_data` WHERE _attribut = '0' $queryparamlast ";
	$result = mysqli_query($mysql_link, $query);


	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
	while ( $row ) {
		$classes = explode(";", $row[0]);
		$queryclass = "( ";
		foreach($classes as $classe)
			{
			if ($classe != "") $queryclass .= " _IDclass like  '%;$classe;%' AND";
			}
		$queryclass .= ")";
		$queryclass = str_replace("AND)", ")", $queryclass);
		$query2 = "SELECT * FROM `edt_data` WHERE $queryclass AND _jour = '$row[1]' AND _nosemaine = '$row[2]' AND _debut = '$row[3]' ";

		$return2 = mysqli_query($mysql_link, $query2);
		$nbrow  = ( $return2 ) ? mysqli_num_rows($return2) : 0 ;
		if ($nbrow > 1) {
			$query3 = " UPDATE edt_data SET _attribut = '1' WHERE _IDx = '$row[4]' ";
			$result2 = mysqli_query($mysql_link, $query3);
			$nbgroupe++;
			}

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	$query  = "select _IDdata from edt_data ";
	$query .= "order by _IDdata desc ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	//I_DV import ernière valeur
	setParam("I_DV", $myrow[0], "0");

}
//---------------------------------------------------------------------------
function import_prof_xls($file)
{
	$preview = @$_POST["preview"];
	$IDcentre = @$_POST["IDcentre"];
	$lang = $_SESSION["lang"];
	require $_SESSION["ROOTDIR"]."/globals.php";
	$filetrad = file_get_contents($file, 'a+');
	//$filetrad = utf8_decode($filetrad);
	//Previsalisation
	if ($preview == "on"){
			print "<table border='1'>";
			$lignes = explode("\n",$filetrad);

			$taillelignes = sizeof($lignes);
			if ($taillelignes > 11) $taillelignes = 11;
			for($i=0; $i<$taillelignes; $i++)
					{
					print "<tr>";
					$cels = explode(";",$lignes[$i]);
					for($j=0; $j<sizeof($cels); $j++)
						{
						print "<td>".$cels[$j]."</td>";
						}
					print "</tr>";
					}


			print "</table>";
	}
	else {
	//Import
			$count = 0;
			$countmaj = 0;
			$lignes = explode("\n",$filetrad);
			$taillelignes = sizeof($lignes);
			for($i=1; $i<$taillelignes; $i++)
					{
					if (strpos($lignes[$i], ";") >0) {
					$cels = explode(";",$lignes[$i]);
					// lecture login deja existante
					$query  = "select _ID from user_id ";
					$query .= "where _ident = '$cels[3]' ";
					$query .= "limit 1";

					$return = mysqli_query($mysql_link, $query);
					$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;


					//On traite les valeurs
					$sexe = $cels[4];
					if ($sexe == "w") $sexe = "F";
					if ($sexe == "m") $sexe = "H";
					//$dateborn = substr($cels[4], 6, 4)."-".substr($cels[4], 3, 2)."-".substr($cels[4], 0, 2);
					$datecreate = date("Y-m-d H:i:s");

					// si l'identifiant n'existe pas on le crée
					if ($myrow[0] == "") {

						$query  = "select _ID from user_id order by _ID desc limit 1";
						$return = mysqli_query($mysql_link, $query);
						$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;
						$IDprof = $myrow[0]+1;

						$query  = "insert into user_id set ";
						$query .= "_ID = '$IDprof', ";
						$query .= "_IDgrp = '2', ";
						$query .= "_create = '$datecreate', ";
						$query .= "_ident = '$cels[2]', ";
						$query .= "_passwd = '".md5($cels[3])."', ";
						$query .= "_name = '".mysqli_real_escape_string($cels[0])."', ";
						$query .= "_fname = '".mysqli_real_escape_string($cels[1])."', ";
						$query .= "_sexe = '$sexe', ";
						$query .= "_adr1 = '".mysqli_real_escape_string($cels[5])."', ";
						$query .= "_cp = '$cels[6]', ";
						$query .= "_city = '".mysqli_real_escape_string($cels[7])."', ";
						$query .= "_mobile = '$cels[8]', ";
						$query .= "_email = '$cels[9]', ";
						$query .= "_lang = 'fr' ";


						//On cree l'id
						if ( mysqli_query($mysql_link, $query) ) {
								$count++;

								$classpptab = explode(" ",$classpp);
								foreach($classpptab as $classpptabf)
								{
								if ($classpptabf != "") setIDClassePP($IDprof, $classpptabf) ;
								}
							}

						else
							mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");

						}

					else {
					// si l'identifiant existe on la met à jour

						$query  = "update user_id set ";
						$query .= "_create = '$datecreate', ";
						$query .= "_ident = '$cels[2]', ";
						$query .= "_name = '".mysqli_real_escape_string($cels[0])."', ";
						$query .= "_fname = '".mysqli_real_escape_string($cels[1])."', ";
						$query .= "_sexe = '$sexe', ";
						$query .= "_adr1 = '".mysqli_real_escape_string($cels[5])."', ";
						$query .= "_cp = '$cels[6]', ";
						$query .= "_city = '".mysqli_real_escape_string($cels[7])."' ";
						$query .= "_mobile = '$cels[8]', ";
						$query .= "_email = '$cels[9]' ";

						$query .= "where _ident = '$cels[2]' ";

						//On cree l'id
						if ( mysqli_query($mysql_link, $query) )
							$countmaj++;
						else
							mysqli_query($mysql_link, "insert into admin_log values ('', '0', 'SQL error: ".addslashes($query)."')");

						}

					}
					}
			if ($count > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " + ID : ".$count."<br></div>";
				}
			if ($countmaj > 0 ) {
				Print "<div class=\"alert alert-success\">";
				print " # ID : ".$countmaj."<br></div>";
				}
			}



}


?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($ADMIN_IMPORT)); ?><strong></strong>
	</div>
</div>

<div class="maincontent">

	<p style="margin-top:0px; margin-bottom:10px; text-align:justify;">
	<div class="alert alert-error">
	<?php if ($onglet != "5" ) print($msg->read($ADMIN_WARNINGUTF8CSV)); if ($onglet == "5" ) print($msg->read($ADMIN_WARNINGUTF8CCV));?>
	</div>
	</p>

	<?php

		$numweek = date('W', mktime(0, 0, 0, 7, 8, 2014));
	$numday = (date('w', mktime(0, 0, 0, 7, 8, 2014)))-1;


		$doublon = 0;
		// vérification des autorisations
		admSessionAccess();

		// l'utilisateur a validé
		if ( $submit ) {
			// la mise à jour de la DB peut prendre du temps => on supprime le tps max d'exécution des requêtes
			// attention : safe_mode doit être désactivé
			$safe_mode  = ini_get("safe_mode");
			$time_limit = ini_get("max_execution_time");

			if ( $safe_mode != "1" )
				set_time_limit(0);

			$table  = @$_POST["table"];
			$format = @$_POST["format"];
			$group  = @$_POST["IDgroup"];
			$file   = @$_FILES["UploadPJ"]["tmp_name"];



			for ($j = 0; $j < count($file); $j++)
				if ( @$file[$j] ) {
					switch ( $onglet ) {
						case 0 :	// login
							$sql = import_user_xls($file[$j]);
							break;

						case 1 :	// élèves
							$sql = import_student_xls($file[$j]);
							break;

						case 2 :	// matiere
							$sql = import_mat_xls($file[$j]);
							break;

						//case 3 :	// classes
						//	$sql = import_classes_xls($file[$j]);
						//	break;

						case 3 :	// Prof
							$sql = import_prof_xls($file[$j]);
							break;

						case 5 :	// EDT
							$sql = import_edt_xls($file[$j]);
							break;

						}

					if ( $sql < 0 )
						print("<p style=\"color: #ff0000;\">". $msg->read($ADMIN_ERROPEN) ."</p>");
					else {
						$Query  = "insert into admin_import ";
						$Query .= "values('', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$table', '".@$group."', '".date("Y-m-d H:i:s")."', '$sql', '$format')";

						if ( mysqli_query($mysql_link, $Query) )
							mysqli_query($mysql_link, "update admin_log set _IDimport = '".mysqli_insert_id($mysql_link)."' where _IDimport = '0'");
						}
					}

			// réinitialisation du tps max d'exécution des requêtes
			if ( $safe_mode != "1" )
				set_time_limit($time_limit);
			}	// endif submit

		// initialisation
		switch ( $onglet ) {
			case 0 :	// login
				$format = Array('xls');
				$table  = "user_id";
				break;

			case 1 :	// élèves
				$format = Array('xls', 'xml');
				$table  = "user_id";
				break;

			case 2 :	// matiere
				$format = Array('xls');
				$table  = "campus_data";
				break;

			case 3 :	// classes
				$format = Array('xls');
				$table  = "campus_classe";
				break;

			case 4 :	// Prof
				$format = Array('xls');
				$table  = "edt_data";
				break;

			case 5 :	// EDT
				$format = Array('xls');
				$table  = "user_id";
				break;

			case 51 :	// EDT FUSION
				import_edt_fusionprof();
				$table = "";
				break;


			case 52 :	// EDT FUSION
				import_edt_fusion();
				$table = "";
				break;

			case 53 :	// EDT FUSION
				import_edt_group();
				$table = "";
				break;

			}


			if ($doublon != 0) {
				print "<div class=\"alert alert-error\"><strong>";
				print ($msg->read($ADMIN_WARNINGDOUBLOON));
				print "$doublon</strong></div>";
				print "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1; URL=index.php?item=27&cmde=import&onglet=$onglet&dv=$dv\">";
			}

			if ($onglet == 51 && $doublon == 0) {
				print "<div class=\"alert alert-error\"><strong>";
				print ($msg->read($ADMIN_WARNINGDOUBLOON));
				print "$doublon</strong></div>";
				print "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1; URL=index.php?item=27&cmde=import&onglet=52&dv=$dv\">";
			}

			if ($onglet == 52 && $doublon == 0) {
				print "<div class=\"alert alert-error\"><strong>";
				print ($msg->read($ADMIN_WARNINGDOUBLOON));
				print "$doublon</strong></div>";
				print "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1; URL=index.php?item=27&cmde=import&onglet=53&dv=$dv\">";
			}

			if (isset($nbgroupe) && $nbgroupe != 0) {
				print "<div class=\"alert alert-error\"><strong>";
				print "Fusion groupe : $nbgroupe</strong></div>";
			}



	?>

	<form id="formulaire" action="index.php" method="post" enctype="multipart/form-data" accept-charset="UTF-8" >
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"    value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"    value=\"$cmde\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"table\"   value=\"$table\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"onglet\"  value=\"$onglet\" /></p>");
	?>

	<?php
		$label = "";
		$list  = explode(",", $msg->read($ADMIN_IMPORTMENU));

		if ( count($list))
			$label .= ( $onglet == 0 )
				? "<strong></strong>"
				: "" ;

		for ($i = 1; $i < count($list); $i++)
			$label .= ( $onglet == $i )
				? " <strong>$list[$i]</strong>"
				: " <a href=\"".myurlencode("index.php?item=$item&cmde=$cmde&onglet=$i")."\"  class=\"btn\" style=\"margin-left: 10px; margin-right: 10px;\" >$list[$i]</a>" ;

		print("
			<fieldset style=\"border:#cccccc solid 1px;\">
			<legend>$label</legend>");

			print("
		            <table class=\"width100\">
		              <tr class=\"align-center\" style=\"background-color:#c0c0c0;\">
		                <td style=\"width:60%;\">". $msg->read($ADMIN_FILE) ."</td>
		                <td style=\"width:20%;\"></td>
		                <td style=\"width:20%;\">");
		                if ($onglet > 10) print $msg->read($ADMIN_CENTRE);
		                Print ("</td>
		              </tr>

		              <tr>
			          <td>
					<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$FILESIZE\" />
					<input type=\"file\" name=\"UploadPJ[]\" style=\"font-size:9px; margin-bottom:5px;\" />
			          </td>
			          <td class=\"align-center\">");
					/*
					print("
					  	<label for=\"IDfmt\">

					  	<select id=\"IDfmt\" name=\"format\">");

					for ($i = 0; $i < count($format); $i++)
						print("<option value=\"$format[$i]\">$format[$i]</option>");

					print("
					  	</select>";

					*/
					print "</label>";

			print("
			          </td>
			          <td class=\"align-center\">");
		?>



				<?php
					// lecture des centres constitutifs
					if ($onglet > 10) {
					print "<select id=\"IDcentre\" name=\"IDcentre\"><option value=\"0\"></option>";
					$query  = "select _IDcentre, _ident from config_centre ";
					$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "order by _IDcentre";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {
						printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($IDcentre == $row[0]) ? "selected=\"selected\"" : "");

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
					print "</select>";
					}
				?>



			<?php


			print("
			          </td>
			          <td class=\"align-center\">");

					if ( $onglet == 10 ) {
						print("
						  	<label for=\"IDgroup\">
						  	<select id=\"IDgroup\" name=\"IDgroup\">");

							// affichage des matières
							$query  = "select _IDmat, _titre from campus_data ";
							$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
							$query .= "order by _titre";

							$return = mysqli_query($mysql_link, $query);
							$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

							while ( $myrow ) {
								print("<option value=\"$myrow[0]\">$myrow[1]</option>");

								$myrow = remove_magic_quotes(mysqli_fetch_row($return));
								}	// endwhile groupe

						print("
						  	</select>
						  	</label>");
						}

			print("
			          </td>
		              </tr>
		            </table>");
	?>
		<label for="preview"><input id="preview" name="preview" type="checkbox"> <?php print($msg->read($ADMIN_PREVIEW)); ?></label>

		<?php
		if ( $onglet == 5 ) {
		 print "&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"datesup\">".$msg->read($ADMIN_DELETEDT)." : <input id=\"datesup\" name=\"datesup\" type=\"input\"> jj/mm/aaaa </label> <hr/>";
		 print "<div style=\"margin: 10px; display:inline-block;\" ><a href=\"index.php?item=27&cmde=import&onglet=51\" class=\"btn\">";
		 print($msg->read($ADMIN_GODOUBLOON));
		 print "</a></div>";

		 print "<div style=\"margin: 10px; display:inline-block;\"><a href=\"index.php?item=27&cmde=import&onglet=51&dv=on\" class=\"btn\">";
		 print($msg->read($ADMIN_GODOUBLOON2));
		 print "</a></div>";
		 }
		?>
		<div>
			[ <?php print($msg->read($ADMIN_LOG)); ?> ]
			<span style="cursor: pointer;" onclick="$('log')._display.toggle(); return false;"><img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/updown.png" title="+" alt="+" /></span>
		</div>




		<div id="log" style="display:block;">
		<?php
			// lecture des logs d'import
			$query  = "select _ID, _IP, _date, _record, _ext, _IDgroup, _IDimport from admin_import ";
			$query .= "where _table = '$table' ";
			$query .= "order by _IDimport desc ";
			$query .= "limit 10";

			$return = mysqli_query($mysql_link, $query);
			$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

			$j      = 0;
			$text   = "";

			while ( $myrow ) {
				$bgcolor = ( $j++ % 2 ) ? "item" : "menu" ;

				$msg->isPlural = (bool) ( $myrow[3] > 1 );

				if ( $onglet == 3 ) {
					// affichage des matières
					$query  = "select _titre from campus_data ";
					$query .= "where _IDmat = '$myrow[5]' AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "limit 1";
					}
				else {
					// lecture du groupe
					$query  = "select _ident from user_group ";
					$query .= "where _IDgrp = '$myrow[5]' AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "limit 1";
					}

				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				$text  .= "<div class=\"$bgcolor\">";

				$text  .= "<a href=\"".$_SESSION["ROOTDIR"]."/admin_csv.php?sid=".$_SESSION["sessID"]."&amp;id=$myrow[6]\" onclick=\"window.open(this.href, '_blank'); return false;\">";
				$text  .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/post-in.gif\" title=\"". $msg->read($ADMIN_CSV) ."\" alt=\"". $msg->read($ADMIN_CSV) ."\" />";
				$text  .= "</a> ";

				$text  .= $msg->read($ADMIN_LASTIMPORT, Array(date2longfmt($myrow[2]), getUserNameByID($myrow[0]), _getHostName($myrow[1]), $row[0], strval($myrow[3]), $myrow[4]));

				$text  .= "</div>";

				$myrow  = mysqli_fetch_row($return);
				}

	            print("$text");



		?>
		</div>

		</fieldset>

            <table class="width100">
              <tr class="valign-bottom">
			<td style="width:10%;">
				<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($ADMIN_INPUTNEW)."\" />"); ?>
			</td>
			<td><?php print($msg->read($ADMIN_RUN)); ?></td>
              </tr>
            </table>
	</form>

</div>
