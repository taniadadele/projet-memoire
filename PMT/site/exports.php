<?php

	session_start();
// 	error_reporting(E_ALL);
// ini_set("display_errors", 1);


	include_once('php/exports/xlsxwriter.class.php');
	require_once "php/functions.php";

	if (isset($_GET['item'])) $item = (int) $_GET['item'];
	else $item = (int) $_POST['item'];

	if (isset($_GET['cmde'])) $cmde = $_GET['cmde'];
	else $cmde = $_POST['cmde'];
	if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
	{


		// On construit le nom du fichier
		switch ($item) {
			case '1':  if ($cmde == '') $filename = 'users_export.xlsx'; 							break;
			case '13': if ($cmde == '') $filename = 'cahier_de_texte_export.xlsx'; 		break;
			case '29': if ($cmde == '') $filename = 'edt_export.xlsx'; 								break;
			case '38': if ($cmde == '') $filename = 'student_export.xlsx'; 						break;
			case '61': if ($cmde == '') $filename = 'bank_export.xlsx'; 							break;
			case '62': if ($cmde == '') $filename = 'stage_hours.xlsx'; 							break;
			case '63': if ($cmde == '') $filename = 'absent_export.xlsx'; 						break;
			case '65': if ($cmde == '') $filename = 'rattrapage_list.xlsx'; 					break;
			case '69': if ($cmde == '') $filename = 'syllabus_export.xlsx'; 					break;
			case '70': if ($cmde == '') $filename = 'exams_export.xlsx'; 							break;
			default: $filename = 'export.xlsx'; break;
		}



		//---------------------------------------------------------------------------
		require_once $_SESSION['CFGDIR'].'/config.php';
		//---------------------------------------------------------------------------
		require_once 'include/TMessage.php';
		require_once 'include/sqltools.php';
		require_once 'include/session.php';
		require_once 'include/config.php';
		require_once 'include/fonction.php';
		//---------------------------------------------------------------------------
		$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
		//---------------------------------------------------------------------------
		//---------------------------------------------------------------------------
		function remove_magic_quotes($array)
		{
			/*
			 * fonction :	nettoyage des \ dans une chaîne
			 * in :		$array : tableau de valeurs
			 */

			// On n'exécute la boucle que si nécessaire
			if ( $array AND get_magic_quotes_gpc() == 1 )
				foreach($array as $key => $val) {
					// Si c'est un array, recursion de la fonction, sinon suppression des slashes
					if ( is_array($val) )
						remove_magic_quotes($array[$key]);
					else
						if ( is_string($val) )
							$array[$key] = stripslashes($val);
					}

			return $array;
		}
		//---------------------------------------------------------------------------

		// Si on est sur l'export des rattrapages et que l'on a sélectionné un élève, on met son nom dans le nom du fichier
		if ($item == 65 && isset($_GET['IDeleve']) && $_GET['IDeleve'] != 0) $filename = "rattrapage_".getUserNameByID($_GET['IDeleve']).'.xlsx';


		// On ne télécharge pas de fichier si on veux créer un zip d'export
		if (!isset($_GET['tofolder']) || $_GET['tofolder'] == '')
		{
			header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
		}





		if ($item == '38')
		{
			$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
				? (int) $_POST["IDcentre"]
				: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
			$visu     = ( @$_POST["visu"] )			// mode de visualisation des élèves (O:Valide, N:Ancien élève, A:En attente, D:Démissionaire, E:Exclus)
				? $_POST["visu"]
				: (@$_GET["visu"] ? $_GET["visu"] : "O") ;
			$IDsel    = ( strlen(@$_POST["IDsel"]) )		// Identifiant de la classe sélectionnée
				? (int) $_POST["IDsel"]
				: (int) (strlen(@$_GET["IDsel"]) ? $_GET["IDsel"] : $_SESSION["STUDENT_IDsel"]) ;
			$_SESSION["STUDENT_IDsel"] = $IDsel;
			$IDpromo  = ( @$_POST["IDpromo"] )			// Identifiant de la promotion
				? (int) $_POST["IDpromo"]
				: (int) @$_GET["IDpromo"] ;
			$IDres    = ( @$_POST["IDres"] )			// Identifiant de la ressource
				? (int) $_POST["IDres"]
				: (int) @$_GET["IDres"] ;
			$IDmat    = ( @$_POST["IDmat"] )			// Identifiant de la matière sélectionnée
				? (int) $_POST["IDmat"]
				: (int) @$_GET["IDmat"] ;
			$IDalpha  = ( isset($_GET["IDalpha"]) )			// Recherche
				? $_GET["IDalpha"]
				: $_SESSION["STUDENT_IDalpha"] ;
			if(isset($_GET["IDalpha"]) && $_GET["IDalpha"] == "") { // RAZ recherche
				$IDalpha = "";
			}
			$_SESSION["STUDENT_IDalpha"] = $IDalpha;
			$recuseralpha  = ( @$_GET["recuseralpha"] )			// recherche user
				? $_GET["recuseralpha"]
				: "A" ;
			$regime   = ( strlen(@$_POST["regime"]) )		// régime de l'élève
				? $_POST["regime"]
				: @$_GET["regime"] ;
			$gender   = ( strlen(@$_POST["gender"]) )		// sexe  de l'élève
				? $_POST["gender"]
				: $_SESSION["STUDENT_gender"] ;
			$_SESSION["STUDENT_gender"] = $gender;
			$delegue  = ( strlen(@$_POST["delegue"]) )	// élève délégué ?
				? $_POST["delegue"]
				: @$_GET["delegue"] ;
			$bourse   = ( strlen(@$_POST["bourse"]) )		// élève boursier ?
				? $_POST["bourse"]
				: @$_GET["bourse"] ;

			$skpage   = ( @$_GET["skpage"] )			// n° de la page affichée
				? (int) $_GET["skpage"]
				: 1 ;
			$skshow   = ( @$_GET["skshow"] )			// n° du flash info
				? (int) $_GET["skshow"]
				: 1 ;
			$IDeleve   = ( @$_GET["IDeleve"] )
				? (int) $_GET["IDeleve"]
				: 1 ;


			if ($recuseralpha == "on" ) { $recuseralpha = "like '$IDalpha%'"; } else {$recuseralpha = ">= '$IDalpha'"; }
			$Query  = "select distinctrow ";
			$Query .= ( $IDpromo )
				? "user_id._ID, user_promos._IDclass, "
				: "user_id._ID, user_id._IDclass, " ;
			$Query .= "_name, _fname, _sexe, _born, _adr1, _adr2, _cp, _city, _tel, _regime, _bourse, _email, _IDgrp, user_id._delegue, user_id._lang, user_id._numen, user_id._adm " ;
			$Query .= ( $IDpromo )
				? "from user_id, campus_classe, user_promos "
				: "from user_id, campus_classe " ;
			$Query .= "where user_id._visible = '$visu' ";
			$Query .= "AND (campus_classe._IDcentre = '$IDcentre' AND campus_classe._IDclass = user_id._IDclass) ";
			$Query .= ( $IDsel )
				? ($IDpromo ? "AND (campus_classe._IDcentre = '$IDcentre' AND campus_classe._IDclass = user_id._IDclass) AND user_promos._IDclass = '$IDsel' " : "AND user_id._IDclass = '$IDsel' ")
				: "" ;
			$Query .= ( $IDpromo )
				? "AND (user_promos._date = '$IDpromo-00-00' AND user_promos._IDeleve = user_id._ID) "
				: "" ;

			// $Query .= ( strlen($IDalpha) ) ? "AND user_id._name $recuseralpha " : "" ;


			$Query .= "AND _adm != '-1' ";
			$Query .= ( $gender ) ? "AND user_id._sexe = '$gender' " : "" ;
			$Query .= ( $bourse ) ? "AND user_id._bourse = '$bourse' " : "" ;
			$Query .= ( $regime ) ? "AND user_id._regime = '$regime' " : "" ;
			$Query .= ( $delegue ) ? "AND user_id._delegue = '$delegue' " : "" ;

			// Champ de recherche
			if ($IDalpha != "") $Query .= "AND (_name LIKE '%".$IDalpha."%' OR _fname LIKE '%".$IDalpha."%') ";

			$Query .= "order by user_id._name asc, user_id._name asc";

			$rows = array(
				array('Promotion','Nom','Prénom','Date','Email', 'Adresse 1', 'Adresse 2', 'Code postal', 'Ville', 'Adresse d\'études', 'Code postal d\'études', 'Ville d\'études')
			);

			$result = mysql_query($Query, $mysql_link);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				// affichage des groupes
				for ($idx = 0; $idx < @count($groupe[0]); $idx++)
					if ( $groupe[0][$idx] == $row[1] ) break;
				$date = substr($row[5], 8, 2)."/".substr($row[5], 5, 2)."/".substr($row[5], 0, 4);

				// On récupère l'adresse d'étude
				$query_study = "SELECT _valeur FROM rubrique_data WHERE _IDrubrique = 4 AND _IDdata = '".$row[0]."' ";
				$result_study = mysql_query($query_study, $mysql_link);
				while ($row_study = mysql_fetch_array($result_study, MYSQL_NUM)) $study_addr = $row_study[0];
				// On récupère le code postal d'étude
				$query_study = "SELECT _valeur FROM rubrique_data WHERE _IDrubrique = 5 AND _IDdata = '".$row[0]."' ";
				$result_study = mysql_query($query_study, $mysql_link);
				while ($row_study = mysql_fetch_array($result_study, MYSQL_NUM)) $study_cp = $row_study[0];
				// On récupère la ville d'étude
				$query_study = "SELECT _valeur FROM rubrique_data WHERE _IDrubrique = 6 AND _IDdata = '".$row[0]."' ";
				$result_study = mysql_query($query_study, $mysql_link);
				while ($row_study = mysql_fetch_array($result_study, MYSQL_NUM)) $study_city = $row_study[0];


				array_push($rows, array(getClassNameByClassID($row[1]), $row[2], $row[3], $date, $row[13], $row[6], $row[7], $row[8], $row[9], $study_addr, $study_cp, $study_city));
			}
		}

	/*------------------------------------------------------------------------------\\
	||------------------------------------------------------------------------------||
	\\------------------------------------------------------------------------------*/
		// Si page liste des comptes
		if ($item == "1" && $cmde == "")
		{



			$IDcentre = ( strlen(@$_POST["IDcentre"]) )	// ID du centre
				? (int) $_POST["IDcentre"]
				: (int) (strlen(@$_GET["IDcentre"]) ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
			$IDalpha  = ( isset($_GET["IDalpha"]) )			// ordre alphabétique
				? $_GET["IDalpha"]
				: $_SESSION["USER_IDalpha"] ;
			if(isset($_GET["IDalpha"]) && $_GET["IDalpha"] == "") { // RAZ recherche
				$IDalpha = "";
			}
			$_SESSION["USER_IDalpha"] = $IDalpha;
			$recuseralpha  = ( @$_GET["recuseralpha"] )			// recherche user
				? $_GET["recuseralpha"]
				: "A" ;
			$visu     = ( @$_POST["visu"] )			// type de visualisation
				? (int) $_POST["visu"]
				: (int) @$_GET["visu"] ;
			$IDsel    = ( $visu )					// catégorie
				? (int) $_SESSION["CnxClass"]
				: (int) (strlen(@$_POST["IDsel"]) ? $_POST["IDsel"]: @$_SESSION["USER_IDsel"] );
			$_SESSION["USER_IDsel"] = $IDsel;
			$sort     = ( strlen(@$_POST["sort"]) )		// filtre affichage sur les dates de connexion
				? $_POST["sort"]
				: @$_SESSION["USER_sort"] ;
			$_SESSION["USER_sort"] = $sort;
			$sortadm  = ( strlen(@$_POST["sortadm"]) )	// filtre affichage sur les droits utilisateur
				? $_POST["sortadm"]
				: @$_GET["sortadm"] ;
			$mylang   = ( strlen(@$_POST["mylang"]) )		// filtre affichage sur la langue
				? $_POST["mylang"]
				: @$_GET["mylang"] ;

			$ID       = ( @$_POST["ID"] )				// ID de l'utilisateur
				? (int) $_POST["ID"]
				: (int) @$_GET["ID"] ;
			$authuser = ( @$_POST["authuser"] )			// validation des comptes utilisateurs
				? (int) $_POST["authuser"]
				: (int) @$_GET["authuser"] ;

			$skpage   = ( @$_GET["skpage"] )			// n° de la page affichée
				? (int) $_GET["skpage"]
				: 1 ;
			$skshow   = ( @$_GET["skshow"] )			// n° du flash info
				? (int) $_GET["skshow"]
				: 1 ;


			// Select du type de compte
			if (isset($_POST['accountType'])) $accountType = $_POST['accountType'];
			elseif (isset($_GET['accountType'])) $accountType = $_GET['accountType'];
			else $accountType = "all";
			$statusID = 0;
			switch ($accountType) {
				case 'all':
					$IDsel    = 0;
					$statusID = "all";
					break;

				// Pour les élèves
				case 'E_all':
					$IDsel    = 1;
					$statusID = "all";
					break;
				case 'E_0':
					$IDsel    = 1;
					$statusID = 0;
					break;
				case 'E_1':
					$IDsel    = 1;
					$statusID = 1;
					break;
				case 'E_-2':
					$IDsel    = 1;
					$statusID = -2;
					break;
				case 'E_-3':
					$IDsel    = 1;
					$statusID = -3;
					break;

				// Pour les enseignants
				case 'P_all':
					$IDsel    = 2;
					$statusID = "all";
					break;
				case 'P_0':
					$IDsel    = 2;
					$statusID = 0;
					break;
				case 'P_1':
					$IDsel    = 2;
					$statusID = 1;
					break;
				case 'P_-2':
					$IDsel    = 2;
					$statusID = -2;
					break;

				// Pour les administratifs
				case 'A':
					$IDsel    = 4;
					$statusID = "all";
					break;

				// Au cas ou
				default:
					$IDsel    = 0;
					$statusID = "all";
					break;

			}


			// on classe par ordre alphabétique
			if ($recuseralpha == "on" ) { $recuseralpha = "like '$IDalpha%'"; } else {$recuseralpha = ">= '$IDalpha'"; }

			$Query  = "select distinctrow ";
			$Query .= "user_id._ID, user_id._cnx, user_id._title, user_id._fonction, user_id._sexe, user_id._adm, user_id._IDgrp, user_id._lastcnx, user_id._create, user_id._delay, user_id._lang, user_id._email, user_id._IDcentre, user_id._tel, ";
			$Query .= "user_id._mobile, user_id._IDclass, user_id._name, user_id._ident, user_id._fname, user_id._adr1, user_id._adr2, user_id._cp, user_id._city ";
			$Query .= "from user_id, user_group " ;
			$Query .= "where (user_id._IDgrp = user_group._IDgrp ";
			$Query .= "OR user_id._IDgrp = 0) ";
			$Query .= ( $authuser ) ? "AND user_id._create = '0000-00-00 00:00:00' " : "AND user_id._create != '0000-00-00 00:00:00' " ;
			$Query .= ( $IDsel ) ? "AND user_id._IDgrp = '$IDsel' " : "" ;
			if ($statusID != "all" or $statusID == "0") $Query .= "AND user_id._adm = '".$statusID."' ";
			$Query .= ( $IDcentre ) ? "AND (user_id._IDcentre = '$IDcentre' OR user_id._centre & pow(2, $IDcentre - 1)) " : "" ;

			// Recherche
			$Query .= ( strlen($IDalpha) ) ? "AND (user_id._name LIKE '%$IDalpha%' OR user_id._fname LIKE '%$IDalpha%' OR user_id._email LIKE '%$IDalpha%') " : "" ;

			$Query .= "AND _adm != '-1' ";

			$Query .= ( $mylang ) ? "AND user_id._lang = '$mylang' " : "" ;
			$Query .= ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) ? "" : "AND user_id._adm " ;
			// $Query .= ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) ? "AND user_group._IDcat " : "AND user_group._IDcat > 1 " ;

			$Query .= "AND user_id._visible = 'O' " ;

			// Un prof ne peux voir que les élèves:
			if ($_SESSION['CnxGrp'] == 2) $Query .= "AND user_id._IDgrp = '1' ";

			switch ( $sortadm ) {
				case 1 :
					$Query .= "AND user_id._adm = '0' ";
					break;
				case 2 :
					$Query .= "AND user_id._adm = '1' ";
					break;
				case 3 :
					$Query .= "AND (user_id._adm & 2) AND user_id._adm != '255' ";
					break;
				case 5 :
					$Query .= "AND (user_id._adm & 4) AND user_id._adm != '255' ";
					break;
				case 9 :
					$Query .= "AND (user_id._adm & 8) AND user_id._adm != '255' ";
					break;
				case 256 :
					$Query .= "AND user_id._adm = '255' ";
					break;
				default :
					break;
				}

			switch ( $sort ) {
				case 1 :
					$Query .= "order by user_id._ID, user_id._name ";
					break;
				case 2 :
					$Query .= "order by user_id._ID desc, user_id._name ";
					break;
				case 3 :
					$Query .= "order by user_id._lastcnx desc, user_id._name ";
					break;
				case 4 :
					$Query .= "order by user_id._lastcnx, user_id._name ";
					break;
				default :
					$Query .= "order by user_id._name ";
					break;
				}

			// if ($skpage != "all") $limit_first = ($skpage - 1) * getParam('MAXPAGE');
			// if ($skpage != "all") $Query .= "LIMIT ".$limit_first.", ".getParam('MAXPAGE');

			$rows = array(
				array('Groupe','Nom','Prénom','Classe','Email', 'Mobile', 'Téléphone', 'Identifiant', 'Adresse 1', 'Adresse 2', 'Code postal', 'Ville')
			);

			$result = mysql_query($Query, $mysql_link);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				array_push($rows, array(getUserGroupByID($row[0]), $row[16], $row[18], getClassNameByUserID($row[0]), $row[11], $row[14], $row[13], $row[17], $row[19], $row[20], $row[21], $row[22]));
			}
		}

		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		// Liste des examens
		if ($item == "70" && $cmde == "")
		{

			$showNext = addslashes($_GET['shownext']);

			$rows = array(
				array('Nom de l\'examen', 'Année', 'Pôle', 'Matière', 'Type', 'Note max', 'Coef', 'Date')
			);

			$query = "SELECT campus_examens.* FROM `campus_examens` LEFT JOIN `edt_data` edt ON (edt._ID_examen = campus_examens._ID_exam) WHERE ";
      if ($_SESSION['CnxGrp'] == 1) $query .= "campus_examens._ID_pma IN (SELECT _ID_pma FROM pole_mat_annee WHERE _ID_year = (SELECT _code FROM campus_classe WHERE _IDclass = '".$_SESSION['CnxClass']."')) ";

      // Si on veux voir que les examens à venir
      if ($showNext)
      {
        // Si on est la dernière semaine de l'année alors on s'assure de pas avoir 1 comme numéro de semaine...
        if (date('m') == 12) $weekNumber = date('W', mktime(0, 0, 0, 12, 28, $year));
        else $weekNumber = date('W');
        if ($_SESSION['CnxGrp'] == 1) $query .= 'AND ';
        $query .= "((edt._annee > '".date('Y')."' OR (edt._annee = '".date('Y')."' AND edt._nosemaine > '".$weekNumber."' OR (edt._annee = '".date('Y')."' AND edt._nosemaine = '".$weekNumber."' AND edt._jour >= '".date('N')."' ))) OR edt._annee IS NULL) ";
      }
      elseif ($_SESSION['CnxGrp'] != 1) $query .= '1 ';

			$result = mysql_query($query);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				$query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_pma` = ".$row[6]." ";
				$result2 = mysql_query($query2, $mysql_link);
				$row2   = remove_magic_quotes(mysql_fetch_row($result2));
				$annee = getNiveauNameByNumNiveau($row2[1]);

				$query3 = "SELECT `_name` FROM `pole` WHERE `_ID` = ".$row2[2]." ";
				$result3 = mysql_query($query3, $mysql_link);
				$row3   = remove_magic_quotes(mysql_fetch_row($result3));
				$poleName = $row3[0];


				// $query4 = "SELECT `_titre` FROM `campus_data` WHERE `_IDmat` = ".$row2[3]." ";
				// $result4 = mysql_query($query4, $mysql_link);
				// $row4   = remove_magic_quotes(mysql_fetch_row($result4));
				// $matiereName = $row4[0];

				$examType = getParam('type-examen');
				$examType = json_decode($examType, TRUE);
				if ($row[5] == "O") $oral = " (ORAL)";
				else $oral = "";

				// Affiche la date de l'examen (va la chercher dans l'edt) et sinon "à définir"
				$query5 = "SELECT * FROM `edt_data` WHERE `_ID_examen` = '".$row[0]."' ORDER BY `_jour` ASC, `_nosemaine` ASC, `_annee`  LIMIT 1 ";
				$result5 = mysql_query($query5, $mysql_link);
				$isdateexam = false;

				while ($row5 = mysql_fetch_array($result5, MYSQL_NUM)) {
					// Création de la date
					$anneeDate = $row5[16];
					$dateBase = $anneeDate."-01-01";

					$date = strtotime($dateBase);
					$daysToAdd = ($row5[13] - 1) * 7;

					$date = strtotime("+".$daysToAdd." day", $date);

					$date = strtotime("+".$row5[9]." day", $date);
					$date = strtotime("-1 day", $date);

					$dateToShow = getDayNameByDayNumber($row5[9])." ".date('d', $date)." ".getMonthNameByMonthNumber(date('m', $date))." ".date('Y', $date);
					$isdateexam = true;
				}


				array_push($rows, array($row[2], $annee, $poleName, getMatNameByIdMat($row2[3]), $examType[$row[1]].$oral, $row[4], $row[3], $dateToShow));
			}
		}

		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		// Liste des syllabus
		if ($item == "69" && $cmde == "")
		{

			$idSyllabus = $_GET['idsyllabus'];

		  if ($_GET['submit'] == "del")
		  {
		  	$query = "DELETE FROM `campus_syllabus` WHERE `_IDSyllabus` = '".$idSyllabus."' ";
		  	@mysql_query($query, $mysql_link) or die('Erreur SQL !<br>'.$query.'<br>'.mysql_error());
		  }
		  $IDpole = "";
		  $IDpromotion = "";
		  $IDmatiere = "";

		 if ($_GET['IDpole'] != "") $IDpole = addslashes($_GET['IDpole']);
		 else $IDpole = 0;

		 if ($_GET['IDpromotion'] != "") $IDpromotion = addslashes($_GET['IDpromotion']);
		 else $IDpromotion = 0;

		 if ($_GET['IDmatiere'] != "") $IDmatiere = addslashes($_GET['IDmatiere']);
		 else $IDmatiere = 0;

			$query  = "SELECT s.* ";
			$query .= "FROM `campus_syllabus` as s JOIN `pole_mat_annee` as p on s.`_IDPMA` = p.`_ID_PMA` WHERE `_IDPMA` != '' ";


			if ($IDpromotion != 0 or $IDpole != 0 or $IDmatiere != 0)
			{
				$querySearch = "";
				$querySearch2 = "";

				$query2 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE ";
				if ($IDpromotion != 0) $querySearch .= "`_ID_year` = '".$IDpromotion."' ";
				if ($IDpole != 0)
				{
					if ($querySearch != "") $querySearch .= "AND ";
					$querySearch .= "`_ID_pole` = '".$IDpole."' ";
				}
				if ($IDmatiere != 0)
				{
					if ($querySearch != "") $querySearch .= "AND ";
					$querySearch .= "`_ID_matiere` = '".$IDmatiere."' ";
				}
				$query2 .= $querySearch;


				$result2 = mysql_query($query2);
				while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
					if($querySearch2 != "") $querySearch2 .= "OR ";
					$querySearch2 .= "`_IDPMA` = '".$row2[0]."' ";
				}
				$query .= "AND ".$querySearch2."";
			}

			$query .= "ORDER BY p.`_ID_year` ASC, p.`_ID_pole` ASC, p.`_ID_matiere` ASC ";

			$rows = array(
				array('Année', 'Pôle', 'Matière', 'Professeurs', 'P1', 'P2', 'PT')
			);

			$result = mysql_query($query);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				$query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_pma` = ".$row[1]." ";
				$result2 = mysql_query($query2, $mysql_link);
				$row2   = remove_magic_quotes(mysql_fetch_row($result2));
				$annee = getNiveauNameByNumNiveau($row2[1]);

				$query3 = "SELECT `_name` FROM `pole` WHERE `_ID` = ".$row2[2]." ";
				$result3 = mysql_query($query3, $mysql_link);
				$row3   = remove_magic_quotes(mysql_fetch_row($result3));
				$poleName = $row3[0];

				// Liste des profs:
				$profArray = explode(";", $row[5]);
				$profListe = "";
				foreach ($profArray as $key => $idProf) {
					if ($idProf != "")
					{
						if ($profListe != "") $profListe .= " - ";
						$profListe .= getUserNameByID($idProf);
					}
				}
				array_push($rows, array($annee, $poleName, getMatNameByIdMat($row2[3]), $profListe, $row[6], $row[7], $row[8]));
			}
		}

		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		// Si liste EDT
		if ($item == "29" && $cmde == "")
		{
			$sortOrder_request  = $_GET['sortOrder'];
		  $sortBy_request     = $_GET['sortBy'];
		  $IDpromotion        = $_GET['idpromotion'];
		  $IDmatiere          = $_GET['idmatiere'];
		  $IDpole             = $_GET['idpole'];
		  $IDprof             = $_GET['idprof'];
		  $date_1             = str_replace(".", "/", $_GET['date_1']);
		  $date_2             = str_replace(".", "/", $_GET['date_2']);
		  $time_1             = $_GET['time_1'];
		  $time_2             = $_GET['time_2'];
		  $status             = $_GET['status'];
		  $lessonstatus       = $_GET['lessonStatus'];
			if (isset($_GET['type_UV'])) $type_UV = addslashes(stripslashes($_GET['type_UV']));
		  else $type_UV = "";



			if ($lessonStatus == 0) $lessonStatus = "";

			$query = "SELECT * FROM `edt_data` WHERE `_visible` = 'O' ";

			if ($IDpromotion != "0") $query .= "AND `_IDclass` LIKE '%;".$IDpromotion.";%' ";
			if ($IDmatiere != "0") $query .= "AND `_IDmat` = '".$IDmatiere."' ";
			if ($IDprof != "0" && $_SESSION['CnxAdm'] == 255) $query .= "AND (`_ID` = '".$IDprof."' OR `_IDrmpl` = '".$IDprof."') ";

			if ($IDpole != "0")
			{
				$query2 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_pole` = '".$IDpole."' ";
				$result2 = mysql_query($query2, $mysql_link);
				$compteur = 0;
				while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
					if ($compteur == 0) $query .= "AND (";
					// La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
					// "))"

					$query3 = "SELECT `_ID_exam` FROM `campus_examens` WHERE `_ID_pma` = '".$row2[0]."' ";
					$result3 = mysql_query($query3, $mysql_link);
					$compteur2 = 0;
					while ($row3 = mysql_fetch_array($result3, MYSQL_NUM)) {
						if (substr($query, -3) != "OR " and substr($query, -5) != "AND (") $query .= "OR ";
						// La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
						// "))"
						$query .= "`_ID_examen` = '".$row3[0]."' ";
						$compteur2++;
					}
					if (substr($query, -3) != "OR " and substr($query, -5) != "AND (") $query .= "OR ";
					// La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
					// "))"
					$query .= "`_ID_pma` = '".$row2[0]."' ";
					$compteur++;
				}
				$query .= ")";
			}

			// La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
			// "))"

			if ($lessonStatus == "" || $lessonStatus == 0) {
				// Création de la partie de la date de la requête
				$query .= "AND ((`_debut` >= '".$time_1.":00' AND `_debut` <= '".$time_2.":00') ";


				$query .= "AND (`_fin` >= '".$time_1.":00' AND `_fin` <= '".$time_2.":00') ";

				// La ligne suivante sert uniquement pour la coloration syntaxique de mon éditeur, elle n'es pas utile au code...
				// ")"


				$startx = js2PhpTime($date_1." ".$time_1);
				$endx = js2PhpTime($date_2." ".$time_2);

				$startDay = date("N", $startx) - 1;
				$endDay = date("N", $endx) - 1;

				if(date("Y", $startx) == date("Y", $endx) && date("W", $startx) <= date("W", $endx)) { // Cas normal
					$query .= "AND ((`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` = '".date("W", $startx)."' AND `_jour` >= '".$startDay."') OR (`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` > '".date("W", $startx)."')) ";
					$query .= "AND ((`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` = '".date("W", $endx)."' AND `_jour` <= '".$endDay."') OR (`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` < '".date("W", $endx)."')) ";
				} else if(date("Y", $startx) == date("Y", $endx) && date("W", $startx) > date("W", $endx)) { // Cas date en fin d'année mais semaine 01 avec année de début
					$query .= "AND ((`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` = '".date("W", $startx)."' AND `_jour` >= '".$startDay."') OR (`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` > '".date("W", $startx)."')) ";
					$query .= "AND ((`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` = '52' AND `_jour` <= '".$endDay."') OR (`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` < '52')) ";
				} else if(date("Y", $startx) != date("Y", $endx)) { // Cas date chevauchant 2 années
					$query .= "AND ((`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` = '".date("W", $startx)."' AND `_jour` >= '".$startDay."') OR (`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` > '".date("W", $startx)."')) ";
					$query .= "OR ((`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` = '".date("W", $endx)."' AND `_jour` <= '".$endDay."') OR (`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` < '".date("W", $endx)."')) ";
				} else { // Cas autre
					$query .= "AND ((`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` = '".date("W", $startx)."' AND `_jour` >= '".$startDay."') OR (`_annee` >= '".date("Y", $startx)."' AND `_nosemaine` > '".date("W", $startx)."')) ";
					$query .= "AND ((`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` = '".date("W", $endx)."' AND `_jour` <= '".$endDay."') OR (`_annee` <= '".date("Y", $endx)."' AND `_nosemaine` < '".date("W", $endx)."')) ";
				}
			}
			$connexionID = $_SESSION["CnxID"];

			if ($sortOrder_request == "asc") $sortOrder = "asc";
			else $sortOrder = "desc";

			if ($sortBy_request == "pole") $sortBy = "_ID_pma";
			elseif ($sortBy_request == "mat") $sortBy = "_IDmat";
			elseif ($sortBy_request == "classe") $sortBy = "_IDclass";
			elseif ($sortBy_request == "prof") $sortBy = "_ID";
			elseif ($sortBy_request == "room") $sortBy = "_IDitem";
			elseif ($sortBy_request == "time") $sortBy = "_debut";
			elseif ($sortBy_request == "date" || $sortBy_request == 0) $sortBy = "_jour ".$sortOrder.", _nosemaine ".$sortOrder.", _annee ";
			elseif ($sortBy_request == "state")
			{
				// Lorsque l'on tri sur l'état, on doit faire un tri très spécifique pour avoir dans la liste des résultats d'abord les 1 et 5 puis les 4
				$sortBy = "
					(
						CASE _etat

							WHEN '1'
							THEN 1

							WHEN '5'
							THEN 2

							WHEN '4'
							THEN 3
						END
					)
				";
			}

			// Exclusion des matières de types indisponible
			$query .= "AND `_IDmat` in (SELECT `_IDmat` FROM `campus_data` WHERE `_type` != '2') ";
			if ($lessonStatus == "" || $lessonStatus == 0) $query .= ") ";

			// Si on est professeur
			if ($_SESSION["CnxAdm"] != 255 && $_SESSION['CnxGrp'] == 2) $query .= "AND (`_ID` = '".$connexionID."' OR `_IDrmpl` = '".$connexionID."') ";
			// Si on est étudiant
			if ($_SESSION['CnxGrp'] == 1 && !getParam('edt_visible_par_tous')) $query .= "AND `_IDclass` LIKE '%;".$_SESSION['CnxClass'].";%' ";


			if ($lessonStatus == "" || $lessonStatus == 0)
			{
				// Si on veut voir les cours "Programmés ou en attente"
				if ($status == 0)
				{
					if ($_SESSION['CnxAdm'] == 255) $query .= "AND (`_etat` = '1' OR `_etat` = '3' OR `_etat` = '4' OR `_etat` = '5') ";
					else $query .= "AND (`_etat` = '1' OR `_etat` = '4' OR `_etat` = '5') ";
				}

				// Si on veut voir les cours "Programmés"
				elseif ($status == 1) $query .= "AND (`_etat` = '1' OR `_etat` = '5') ";

				// Si on veut voir les cours "En attente"
				elseif ($status == 2)
				{
					if ($_SESSION['CnxAdm'] == 255) $query .= "AND (`_etat` = '4' OR `_etat` = '3') ";
					else $query .= "AND `_etat` = '4' ";
				}

				// Si on veut voir les cours "Refusés" (il faut être admin)
				elseif ($status == 3 && $_SESSION['CnxAdm'] == 255) $query .= "AND `_etat` = '6' ";
			}
			else
			{
				switch ($lessonStatus) {
					case 'accepted':
						$status = 5;
						break;
					case 'refused':
						$status = 6;
						break;
					case 'waiting':
						$status = 4;
						break;
				}
				$query .= "AND `_etat` = '".$status."' ";
			}

			// Tri sur le type:
			if ($type_UV != 0 && $type_UV != "")
			{
				switch ($type_UV) {
					case '1':
						$query .= "AND `_ID_examen` = 0 AND `_ID_pma` != 0 AND _IDmat != 123 AND _IDmat != 122 ";
						break;
					case '2':
						$query .= "AND `_ID_examen` != 0 ";
						break;
					case '3':
						$query .= "AND _IDmat = 122 AND _ID_pma =  0 ";

					default:
						// code...
						break;
				}
			}

			if ($sortOrder_request != "") $query .= "ORDER BY ".$sortBy." ".$sortOrder." ";
			else $query .= "ORDER BY `_annee`, `_nosemaine`, `_jour`, `_debut` ASC ";


			  $result = mysql_query($query, $mysql_link);

				$rows = array(
					array('Date de début', 'Date de fin', 'Pôle', 'Matière', 'Classes', 'Enseignant', 'Salle')
				);

			  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

			    $listeClassesToShow = "";
			    $listeClassesAlreadyShown = ";";
			    $listeClasses = $row[4];
			    $listeClasses = explode(";", $listeClasses);

			    foreach ($listeClasses as $key => $value) {
			      if ($value != 0 and strpos($listeClassesAlreadyShown, $value) === false)
			      {
			        $listeClassesAlreadyShown .= $value.";";
			        if ($listeClassesToShow != "") $listeClassesToShow .= " - ";
			        $listeClassesToShow .= "".getClassNameByClassID($value);
			      }
			    }

			    // Création de la date
			    $anneeDate = $row[16];
			    $dateBase = $anneeDate."-01-01";
			    $date = strtotime($dateBase);
			    $daysToAdd = ($row[13] - 1) * 7;
			    $date = strtotime("+".$daysToAdd." day", $date);
			    $date = strtotime("+".$row[9]." day", $date);
			    $date = strtotime("-1 day", $date);
			    $heure_debut = substr($row[10], 0, 2);
			    $heure_fin = substr($row[11], 0, 2);

			    $minutes_debut = substr($row[10], 3, 2);
			    $minutes_fin = substr($row[11], 3, 2);

					// Correction de la date quand passage à l'année suivante
	        // Si les jours ne correspondent pas, alors on corrige:
	        if (($row[9] + 1) != date("N", $date)) $dayNumber = date('d', $date) - 1;
	        else $dayNumber = date('d', $date);

			    $ID_examen = $row[23];
			    $ID_PMA = $row[24];

			    $matiereName = getMatNameByIdMat($row[3]);

			    if ($ID_examen != 0) $matiereToShow = "UV - ".$matiereName;
			    elseif (getMatTypeByMatID($row[3]) == 3 && $row[22] != "") $matiereToShow = "Agenda - ".$row[22];
					elseif (getMatTypeByMatID($row[3]) == 3)$matiereToShow = "Agenda";
			    else $matiereToShow = $matiereName;

			    if ($ID_examen != 0) $poleName = getPoleNameByIdPole(getPoleIDByUVID($ID_examen));
			    else $poleName = getPoleNameByIdPole(getPoleIDByPMAID($ID_PMA));

					if ($row[18] != "" && $row[18] != 0) $nameRmpl = " - ".getUserNameByID($row[18]);
					else $nameRmpl = "";

					$date_debut = $dayNumber."/".date('m', $date)."/".date('Y', $date)." ".$heure_debut.":".$minutes_debut.":00";
					$date_fin = $dayNumber."/".date('m', $date)."/".date('Y', $date)." ".$heure_fin.":".$minutes_fin.":00";
			    array_push($rows, array($date_debut, $date_fin, $poleName, $matiereToShow, $listeClassesToShow, getUserNameByID($row[6]).$nameRmpl, getRoomNameByID($row[5])));
			  }
		}

		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		// Si cahier de texte
		if ($item == "13" && $cmde == "")
		{

			$IDcentre = ( @$_POST["IDcentre"] )				// Identifiant du centre
				? (int) $_POST["IDcentre"]
				: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
			$IDclass  = ( strlen(@$_POST["IDclass"]) )		// sélection de la classe
				? (int) $_POST["IDclass"]
				: (int) (@$_GET["IDclass"] ? $_GET["IDclass"] : $_SESSION["CnxClass"]) ;
			$IDgroup  = ( @$_POST["IDgroup"] )				// sélection du e-groupe
				? (int) $_POST["IDgroup"]
				: (int) @$_GET["IDgroup"] ;
			$IDmat    = ( @$_POST["IDmat"] )				// ID de la matière
				? (int) $_POST["IDmat"]
				: (int) @$_GET["IDmat"] ;
			$IDitem   = (int) @$_GET["IDitem"];				// ID de l'item du CTN
			$year     = ( @$_POST["year"] )				// ID de la matière
				? (int) $_POST["year"]
				: (int) @$_GET["year"] ;

			if(isset($_GET["month"]))
			{
				$month = @$_GET["month"];
			}
			else
			{
				$month = date("m");
			}
			if(isset($_GET["day"]))
			{
				$day = @$_GET["day"];
			}
			else
			{
				$day = date("d");
			}

			if(strlen($month) == 1 && $month != 0)
			{
				$month = (int) "0".$month;
			}

			if(strlen($day) == 1 && $day != 0)
			{
				$day = (int) "0".$day;
			}

			$skpage   = ( @$_GET["skpage"] )				// n° de la page affichée
				? (int) $_GET["skpage"]
				: 1 ;
			$skshow   = ( @$_GET["skshow"] )				// n° du flash info
				? (int) $_GET["skshow"]
				: 1 ;
			$week 	  = @$_GET["week"];
	// echo $year;
			// lecture de la base de données
			$query  = "select distinctrow ";
			$query .= "ctn_items._IDitem, ctn_items._title, ctn_items._date, ctn_items._delay, ctn_items._ID, ctn_items._visible, ctn_items._IP, ctn_items._IDmat, ctn_items._IDmat, ";
			$query .= "ctn._IDgrprd, ctn._IDgroup, ctn_items._type, ctn_items._IDctn, ctn_items._IDcours, edt_data._debut, edt_data._fin, ctn_items._texte, ctn_items._devoirs, ctn_items._observ ";
			$query .= "from ctn, ctn_items, edt_data ";
			if($month != 0 && $day != 0 && $week == "on")
			{
				$tmp = get_lundi_dimanche_from_week(date("W", mktime(0, 0, 0, $month, $day, $year))-1, $year);
				$query .= "where (ctn_items._date >= '$tmp[0] 00:00:00' AND ctn_items._date <= '$tmp[1] 23:59:59') ";
			}
			else if($month == 0 && $day == 0)
			{
				$query .= "where (ctn_items._date >= '$year-01-01 00:00:00' AND ctn_items._date <= '$year-12-31 23:59:59') ";
			}
			else if($month != 0 && $day == 0)
			{
				$query .= "where (ctn_items._date >= '$year-$month-01 00:00:00' AND ctn_items._date <= '$year-$month-31 23:59:59') ";
			}
			else if($month == 0 && $day != 0)
			{
				$query .= "where (ctn_items._date LIKE '$year-%-$day%' OR ctn_items._date LIKE '$year-%-$day%') ";
			}
			else if($month != 0 && $day != 0)
			{
				$query .= "where ctn_items._date LIKE '$year-$month-$day%' ";
			}
			//
			$query .= ($IDclass != 0) ? "AND ctn_items._IDctn LIKE '%;$IDclass;%' " : "";
			$query .= "AND edt_data._IDx = ctn_items._IDcours " ;
			$query .= ( $IDmat ) ? "AND ctn_items._IDmat = '$IDmat' " : "" ;
			$query .= ( $_SESSION["CnxAdm"] == 255 OR ($auth[6] == "O" AND ($auth[1] AND pow(2, $_SESSION["CnxGrp"] - 1))) )
				? ""
				: "AND (ctn_items._visible = 'O' OR ctn_items._ID = '".$_SESSION["CnxID"]."') " ;
			$query .= "order by ctn_items._date desc";

			$rows = array(
				array('Date', 'Horraires', 'Matière', 'Contenu', 'Plan de travail', 'Classes')
			);

			$result = mysql_query($query, $mysql_link);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
					$nom_class = "";
					foreach(explode(";", $row[12]) as $val)
					{
						if($val != "")
						{
							// Nom de la classe
							$queryclass  = "select _ident from campus_classe ";
							$queryclass .= "where _IDclass = ".$val;

							$resultclass = mysql_query($queryclass);
							$rowsclass    = ( $resultclass ) ? mysql_fetch_row($resultclass) : 0 ;

							if($rowsclass[0] != "")
							{
								$nom_class .= htmlentities($rowsclass[0], ENT_NOQUOTES, "iso-8859-1")."/";
							}
						}
					}
					$time = date("H:i", strtotime($row[14]))."-".date("H:i", strtotime($row[15]));
					$newDate = date("d/m/Y", strtotime($row[2]));


					array_push($rows, array($newDate, $time, getMatNameByIdMat($row[8]), $row[16], $row[17], substr($nom_class, 0, strlen($nom_class)-1)));



			}

			// echo "bonjour";

		}




		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		// Si export bancaire
		if ($item == 61 && $cmde == '')
		{
			if ($_SESSION['CnxAdm'] == 255)
			{
				if (isset($_GET['search_text'])) $search_text = addslashes($_GET['search_text']);
				else $search_text = '';
				if (isset($_GET['IDeleve'])) $IDeleve = addslashes($_GET['IDeleve']);
				else $IDeleve = 0;
				if (isset($_GET['IDclass'])) $IDclass = addslashes($_GET['IDclass']);
			  else $IDclass = 0;
				if (isset($_GET['triField'])) $triField = addslashes($_GET['triField']);
			  else $triField = 'date';

				if (isset($_GET['date_1'])) $date_1 = addslashes($_GET['date_1']);
				else $date_1 = '1/'.date('m/Y');

				if (isset($_GET['date_2'])) $date_2 = addslashes($_GET['date_2']);
				else $date_2 = '1/'.date('m/Y', strtotime('next year'));

				$query = "SELECT bank._ID, bank._date, bank._libele, bank._price, bank._IDeleve, bank._attr FROM bank_data bank INNER JOIN `user_id` user WHERE bank._IDeleve = user._ID ";

	      // if ($year != 0) $query .= "AND YEAR(_date) = '".$year."' ";
	      $date_1_query = date('Y-m-d', strtotime($date_1));
	      $date_2_query = date('Y-m-d', strtotime($date_2));
	      $query .= "AND bank._date >= '".$date_1_query."' ";
	      $query .= "AND bank._date <= '".$date_2_query."' ";

	      if ($IDeleve != 0) $query .= "AND bank._IDeleve = '".$IDeleve."' ";
				if ($IDclass != 0) $query .= "AND bank._IDeleve IN (SELECT _ID FROM user_id WHERE _IDclass = '".$IDclass."') ";
	      if ($search_text != "") $query .= "AND bank._libele LIKE '%".$search_text."%' ";

				if ($triField == 'date') $query .= 'ORDER BY bank._date ASC ';
	      elseif ($triField == 'student') $query .= 'ORDER BY user._name ASC ';
				elseif ($triField == 'promo') $query .= 'ORDER BY user._IDclass DESC ';

	      $currentStudent = 0;
	      $currentAmount = 0;

				$rows = array(
					array('Date', 'Intitulé', 'Élève', 'Promotion', 'Montant')
				);

	      $result = mysql_query($query);
				$numRows = mysql_num_rows($result);
	      $count = 0;
	      while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
					$count++;
					if ($currentStudent == 0) $currentStudent = $row[4];
	        if ($row[4] != $currentStudent && $triField == 'student')
	        {
	          array_push($rows, array('', '', 'TOTAL', $currentAmount));
	          $currentAmount = 0;
	          $currentStudent = $row[4];
	        }
	        $currentAmount += $row[3];

					array_push($rows, array(date('d/m/Y', strtotime($row[1])), $row[2], getUserNameByID($row[4]), json_decode($row[5], true)['prom_name'], $row[3]));

					if ($count == $numRows)
	        {
						array_push($rows, array('', '', 'TOTAL', $currentAmount));
	        }
	      }
			}

		}




		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		// Si export heures de stage
		if ($item == 62 && $cmde == "")
		{
			if (isset($_GET['IDclass'])) $IDclass = addslashes($_GET['IDclass']);
			$list_periodes = json_decode(getParam('periodeList'), TRUE);
			$year = getParam('START_Y');

			$temp = array('Nom');
			foreach ($list_periodes as $key => $value) {
				array_push($temp, $value);
			}
			array_push($temp, 'Année');
			$rows = array(
				$temp
			);

			// On récupère tous les élèves de la promo
			$query = 'SELECT _ID, _name, _fname FROM user_id WHERE _adm = 1 AND _IDclass = '.$IDclass.' ';
			$result = mysql_query($query);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {




				$temp = array($row[1].' '.$row[2]);

				foreach ($list_periodes as $key => $value) {
					$hours_value = 0;
					$query_hours = 'SELECT _stage_hours FROM notes_text WHERE _period = '.$key.' AND _IDeleve = '.$row[0].' AND _year = '.$year.' LIMIT 1 ';
					$result_hours = mysql_query($query_hours);
					while ($row_hours = mysql_fetch_array($result_hours, MYSQL_NUM)) {
						$hours_value = $row_hours[0];
					}
					array_push($temp, $hours_value);
				}
				$hours_value = 0;
				$query_hours = 'SELECT _stage_hours FROM notes_text WHERE _period = 0 AND _IDeleve = '.$row[0].' AND _year = '.$year.' LIMIT 1 ';
				$result_hours = mysql_query($query_hours);
				while ($row_hours = mysql_fetch_array($result_hours, MYSQL_NUM)) {
					$hours_value = $row_hours[0];
				}
				array_push($temp, $hours_value);

				array_push($rows, $temp);
			}
		}









		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		if ($item == 63 && $cmde == '') {
			$temp = array('cbmotif', 'IDcentre', 'IDeleve', 'IDclass', 'IDgroup', 'IDgroupclass', 'IDgrp', 'name', 'year', 'IDmotif', 'IDmotif_all', 'abs_idmat', 'type', 'typeabs', 'IDalpha', 'etat', 'month', 'day', 'regroup', 'week', 'isok', 'email', 'sms', 'valid');
			foreach ($temp as $value) if (isset($_GET[$value])) $$value = $_GET[$value];
			$rows = array();

			// En-tête du tableau
			$table_header = array();
			if($IDclass != 0) $table_header[] = '';
			else $table_header[] = 'Élève';
			$table_header[] = 'Promotion';
			$table_header[] = 'Matière';
			$table_header[] = 'Raison';
			$table_header[] = 'Commentaire';
			$table_header[] = 'Date début';
			$table_header[] = 'Date fin';
			$table_header[] = 'Abs justifiée ?';

			array_push($rows, $table_header);

			// Constitution de la partie de la requête pour les groupe
			if ($IDgrp != "")
			{
				$IDgroupeleve = "";
				$querya  = 'select * from groupe WHERE _IDgrp = '.$IDgrp;
				$resulta = mysql_query($querya, $mysql_link);
				$rowa    = mysql_fetch_row($resulta);
				$nb  = mysql_affected_rows($mysql_link);
				if($nb) $IDgroupeleve .= $rowa[0].'/';
				if($IDgroupeleve != '')
				{
					$IDgroupeleve = substr($IDgroupeleve, 0, strlen($IDgroupeleve)-1);
					$IDgroupeleve = str_replace("/", "' OR user_id._ID = '", $IDgroupeleve);
				}
			}

			// affichage des absences
			$query  = "select distinctrow ";
			$query .= "user_id._name, user_id._fname, user_id._ID, user_id._IDclass, user_id._email, user_id._tel, ";
			$query .= "absent_items._email, absent_items._sms, absent_items._ID, absent_items._IP, absent_items._IDdata, absent_items._start, absent_items._IDctn, absent_items._texte, absent_items._IDitem, absent_items._IDmod, absent_items._isok, ";
			$query .= "campus_classe._ident, absent_items._end, absent_items._date, absent_items._valid, absent_items._file, absent_items._IDabs ";
			$query .= "from user_id, absent_items, campus_classe, absent_data ";
			$query .= ( !$type ) ? ", ctn_items " : " " ;
			if($month != 0 && $day != 0 && $week == "on")
			{
				$tmp = get_semaine_from_week(date("W", mktime(0, 0, 0, $month, $day, $year))-1, $year);
				$query .= "where ((absent_items._start <= '$tmp[0] 23:59:59' AND absent_items._end >= '$tmp[0] 00:00:00') OR ";
				$query .= "(absent_items._start <= '$tmp[1] 23:59:59' AND absent_items._end >= '$tmp[1] 00:00:00') OR ";
				$query .= "(absent_items._start <= '$tmp[2] 23:59:59' AND absent_items._end >= '$tmp[2] 00:00:00') OR ";
				$query .= "(absent_items._start <= '$tmp[3] 23:59:59' AND absent_items._end >= '$tmp[3] 00:00:00') OR ";
				$query .= "(absent_items._start <= '$tmp[4] 23:59:59' AND absent_items._end >= '$tmp[4] 00:00:00') OR ";
				$query .= "(absent_items._start <= '$tmp[5] 23:59:59' AND absent_items._end >= '$tmp[5] 00:00:00') OR ";
				$query .= "(absent_items._start <= '$tmp[6] 23:59:59' AND absent_items._end >= '$tmp[6] 00:00:00')) ";
			}
			elseif($month == 0 && $day == 0) // Année
				$query .= "where (absent_items._start >= '$year-01-01 00:00:00' AND absent_items._start <= '$year-12-31 23:59:59') ";
			elseif($month != 0 && $day == 0) // Mois
				$query .= "where (absent_items._start >= '$year-$month-01 00:00:00' AND absent_items._start <= '$year-$month-31 23:59:59') ";
			elseif($month == 0 && $day != 0) // date sans mois
				$query .= "where (absent_items._start LIKE '$year-%-$day%' OR absent_items._end LIKE '$year-%-$day%') ";
			elseif($month != 0 && $day != 0) // Jour
				$query .= "where (absent_items._start <= '$year-$month-$day 23:59:59' AND absent_items._end >= '$year-$month-$day 00:00:00') ";

			$query .= "AND user_id._ID = absent_items._IDabs ";
			$query .= "AND absent_data._IDdata = absent_items._IDdata ";
			$query .= ( !$type ) ? "AND absent_items._IDctn " : "" ;
			$query .= ( $name ) ? "AND user_id._name like '$name%' " : "" ;
			if ($_SESSION['CnxGrp'] > 1 && $IDclass) $query .= "AND user_id._IDclass = '$IDclass' ";
			elseif ($_SESSION['CnxGrp'] <= 1) $query .= "AND user_id._IDclass = '".$_SESSION["CnxClass"]."' ";

			$query .= "AND user_id._IDclass = campus_classe._IDclass ";
			$query .= "AND campus_classe._IDcentre = '$IDcentre' ";

			// Si mode filtre groupe
			if($IDgrp) $query .= "AND (user_id._ID = '$IDgroupeleve') ";
			else $query .= ( $IDeleve ) ? "AND user_id._ID = '$IDeleve' " : "" ;

			$query .= ( $IDalpha ) ? "AND CONCAT(user_id._name, ' ', user_id._fname) LIKE '%$IDalpha%' " : "" ;
			$query .= "AND user_id._IDgrp = '1' ";
			$query .= ( $IDmotif ) ? "AND absent_items._IDdata = '$IDmotif' " : "" ;
			$query .= ( $abs_idmat ) ? "AND ctn_items._IDmat = '$abs_idmat' " : "" ;


			if ($type != "2")
			{
				$query .= ( $type == 0 ) ? "AND absent_items._IDctn != '-1' " : "" ;
				$query .= ( $type == 1 ) ? "AND absent_items._IDctn LIKE '-1' " : "" ;
				$query .= ( $type == 0 ) ? "AND absent_items._IDctn = ctn_items._IDitem " : "" ;
			}

			$query .= ( $typeabs == "ABS" ) ? "AND absent_data._texte NOT LIKE '%[NC]%' " : "" ;
			$query .= ( $typeabs == "NC" ) ? "AND absent_data._texte LIKE '%[NC]%' " : "" ;
			$query .= ( $etat == "O" ) ? "AND absent_items._valid = 'O' " : "" ;
			$query .= ( $etat == "N" ) ? "AND absent_items._valid = 'N' " : "" ;
			$query .= "ORDER BY absent_items._start DESC";

			$result = mysql_query($query, $mysql_link);

			$okall  = ( $isok) ? "checked=\"checked\"" : '' ;
			$chkall = ( $email ) ? "checked=\"checked\"" : '' ;
			$smsall = ( $sms ) ? "checked=\"checked\"" : '' ;

			$count = $same_ligne = $last_motif = 0;
			$count_user = 1;
			$last_justif = $last_date = $last_user = '';

			while ($row = mysql_fetch_row($result)) {
				if($row[2] != $last_user || $row[10] != $last_motif || $row[20] != $last_justif || date("d/m/Y", strtotime($row[11])) != $last_date)
				{
					$count_user = 1;
					$same_ligne++;
				}
				$rdonly = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $auth[0] OR $_SESSION["CnxGrp"] == 4 OR ($_SESSION["CnxGrp"] == 2 AND $_SESSION["CnxID"] == $row[8])) ? "" : "readonly=\"readonly\"" ;
				$disbl = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $auth[0] OR $_SESSION["CnxGrp"] == 4 OR ($_SESSION["CnxGrp"] == 2 AND $_SESSION["CnxID"] == $row[8])) ? "" : "disabled=\"disabled\"" ;
				// autorisation de rentrer en cours
				if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
				{
					if ($row[20] == 'O') $valid = '1';
					else $valid = '0';
				}
				else $valid = '';
				$valid  = ($_SESSION["CnxGrp"] == 1) ? '' : $valid;

				// recherche du cours
				$return = mysql_query("select campus_data._titre, ctn_items._note, ctn_items._IDcours from campus_data, ctn_items where ctn_items._IDitem = '$row[12]' AND campus_data._IDmat = ctn_items._IDmat AND campus_data._lang = '".$_SESSION["lang"]."' limit 1", $mysql_link);
				$cours  = ( $return ) ? remove_magic_quotes(mysql_fetch_row($return)) : 0 ;

				// recherche du prof
				$return = mysql_query("select user_id._ID from user_id, edt_data WHERE edt_data._ID = user_id._ID AND edt_data._IDx = $cours[2] limit 1", $mysql_link);
				$prof  = ( $return ) ? remove_magic_quotes(mysql_fetch_row($return)) : 0 ;

				// Nouvelle ligne
				$temp_row = array();

				// Le nom de l'élève
				$temp_row[] = $row[0].' '.$row[1];
				// La promotion
				$temp_row[] = $row[17];
				// La matière
				$temp_row[] = $cours[0];

				// Motif
				// liste des motifs de retard
				$querya  = "select _IDdata, _texte from absent_data ";
				$querya .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' AND _IDdata = '".$row[10]."' ";
				$querya .= "order by _texte LIMIT 1";
				$resulta = mysql_query($querya, $mysql_link);
				while ($rowa = mysql_fetch_row($resulta)) {
					$temp_row[] = $rowa[1];
				}

				// Le commentaire
				$temp_row[] = $row[13];

				// La date de début
				$temp_row[] = date('d/m/Y H:i', strtotime($row[11]));

				// La date de fin
				$temp_row[] = date('d/m/Y H:i', strtotime($row[18]));
				// Justifiée/non justifiée
				if($row[20] == "N") $temp_row[] = 'Non justifiée';
				else if($row[20] == "O")	$temp_row[] = 'Justifiée';

				array_push($rows, $temp_row);

				$count++;
				$count_user++;
				$last_user = $row[2];
				$last_motif = $row[10];
				$last_justif = $row[20];
				$last_date = date('d/m/Y', strtotime($row[11]));
			}

		}


		/*------------------------------------------------------------------------------\\
		||------------------------------------------------------------------------------||
		\\------------------------------------------------------------------------------*/
		// Si export rattrapage
		if ($item == 65 && $cmde == "")
		{
			if (isset($_GET['IDclass'])) $IDclass = addslashes($_GET['IDclass']);
			else $IDclass = 0;
			if (isset($_GET['IDuv'])) $IDuv = addslashes($_GET['IDuv']);
			else $IDuv = 0;
			if (isset($_GET['IDeleve'])) $IDeleve = addslashes($_GET['IDeleve']);
			else $IDeleve = 0;
			$list_periodes = json_decode(getParam('periodeList'), TRUE);
			$year = getParam('START_Y');

			// Vérification si étudiant:
			if ($_SESSION['CnxAdm'] != 255) {
			  $IDeleve = $_SESSION['CnxID'];
			  $IDclass = getUserClassIDByUserID($IDeleve);
			}

			$listStudentClass = array();
			$query = 'SELECT _ID, _name, _fname FROM user_id WHERE _adm = 1 AND _IDclass != 0 ';
			if ($IDclass != 0) $query .= "AND _IDclass = '".$IDclass."' ";
			$result = mysql_query($query);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			  $listStudentClass[$row[0]] = $row[1].' '.$row[2];
			}

			$rows = array(
				array('Nom', 'Note', 'Note rattrapage')
			);
			$compteur = 1;
			$merge_cell = array();



			switch ($_GET['elem']) {

				case 'exam':
					$query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 1 OR _type = 2) AND _year = '".$year."' ";
					$isMatAgv = false;
					break;
				case 'certif':
					$query = "SELECT _IDmat, _IDdata FROM notes_data WHERE _IDmat > 100000 AND (`_IDmat` - 100000) IN (SELECT _ID_exam FROM campus_examens WHERE _type = 3 OR _type = 4) AND _year = '".$year."' ";
					$isMatAgv = false;
					break;

				default:
				case 'mat':
					$query = "SELECT _IDmat, _IDdata FROM notes_data WHERE (( `_IDmat` > 100000 AND (`_IDmat` - 100000) NOT IN (SELECT _ID_exam FROM campus_examens WHERE _type != 1)) OR `_IDmat` < 100000) AND _year = '".$year."' ";
					$isMatAgv = true;
					break;
			}


			if ($IDclass != 0) $query .= "AND _IDclass = '".$IDclass."' ";
			if ($IDuv != 0) $query .= "AND _IDmat = '".($IDuv + 100000)."' ";       // Si on ne veux qu'un seul exam
			$query .= "GROUP BY _IDmat";
			$result = mysql_query($query);
			$oldMat = 0;

			while ($row = mysql_fetch_array($result, MYSQL_NUM))
			{
				if (!$isMatAgv) $uvID = $row[0] - 100000;
				else $uvID = $row[0];
				if (!$isMatAgv && isUVRattrapage($uvID)) continue;                                  // On ne veux pas les examens de type rattrapage
				elseif ($isMatAgv && $uvID > 100000) continue;
				if ($IDeleve == 0)
				{
					$header = '';
					if (!$isMatAgv) $toPush = getUVNameByID($uvID);
					else $toPush = getPoleNameByIdPole(getPoleIDByPMAID($uvID)).' - '.getMatNameByIdMat(getMatIDByPMAID($uvID));
					$header = array($toPush, $toPush, $toPush);

					$oldMat = $uvID;


					foreach ($listStudentClass as $studentID => $studentName) {
						$note = 0;
						$periodeList = json_decode(getParam('periodeList'), TRUE);
						$tempMat = '';
						foreach ($periodeList as $key => $value) {

							// On fait la moyenne sur la période
							$query_temp = "SELECT `_IDdata` FROM `notes_data` WHERE `_IDdata` != ".$row[1]." AND `_IDmat` = '".$row[0]."' ";
							$result_temp = mysql_query($query_temp);
							while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
							{
								$tempMat .= " OR `_IDdata` = '".$row_temp[0]."' ";
							}
						}
						if (!$isMatAgv) {
							$query_note = "SELECT `_value` FROM `notes_items` WHERE `_IDdata` = '".$row[1]."' AND `_IDeleve` = '".$studentID."' AND _value != '' ";
							$result_note = mysql_query($query_note);
							while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
							{
								$note = $row_note[0];
							}
						}
						else {
							$notes_total = 0;
							$notes_coef = 0;
							$temp_query = "SELECT `_total`, `_coef`, `_IDdata` FROM `notes_data` WHERE (`_IDdata` = '".$row[1]."' ".$tempMat.")";
							$result_temp = mysql_query($temp_query);
							while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
							{
								$total = explode(';', $row_temp[0]);
								$coef = explode(';', $row_temp[1]);

								$query_note = "SELECT `_value`, `_index` FROM `notes_items` WHERE `_IDeleve` = '".$studentID."' AND`_IDdata` = '".$row_temp[2]."' AND `_value` != '' ";
								$result_note = mysql_query($query_note);
								while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
								{
									// On calcule la moyenne
									$notes_total += (($row_note[0] / $total[$row_note[1]]) * 20) * $coef[$row_note[1]];
									$notes_coef += $coef[$row_note[1]];
								}
							}
							$note = round($notes_total / $notes_coef, 1);
						}
						if (isset($notes_coef) && $notes_coef == 0) continue;
						if (!$isMatAgv) $uvRattrapageNote = getUVRattNote($uvID, $studentID);
						else $uvRattrapageNote = getPMARattNote($uvID, $studentID);
						if ($note >= getParam('note_min_rattrapage') && $note < getParam('note_max_rattrapage')) {
							if (is_array($header)) {
								array_push($rows, $header);
								$header = '';
							}
							array_push($rows, array($studentName, $note, round($uvRattrapageNote, 1)));
						}

						unset($note, $uvRattrapageNote);
					}
				}
				else
				{
					$periodeList = json_decode(getParam('periodeList'), TRUE);
					$tempMat = '';
					foreach ($periodeList as $key => $value) {
						// On fait la moyenne sur la période
						$query_temp = "SELECT `_IDdata` FROM `notes_data` WHERE `_IDdata` != ".$row[1]." AND `_IDmat` = '".$row[0]."' ";
						$result_temp = mysql_query($query_temp);
						while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
						{
							$tempMat .= " OR `_IDdata` = '".$row_temp[0]."' ";
						}
					}
					if (!$isMatAgv) {
						$query_note = "SELECT `_value` FROM `notes_items` WHERE `_IDdata` = '".$row[1]."' AND `_IDeleve` = '".$studentID."' AND _value != '' ";
						$result_note = mysql_query($query_note);
						while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
						{
							$note = $row_note[0];
						}
					}
					else {
						$notes_total = 0;
						$notes_coef = 0;
						$temp_query = "SELECT `_total`, `_coef`, `_IDdata` FROM `notes_data` WHERE (`_IDdata` = '".$row[1]."' ".$tempMat.")";
						$result_temp = mysql_query($temp_query);
						while ($row_temp = mysql_fetch_array($result_temp, MYSQL_NUM))
						{
							$total = explode(';', $row_temp[0]);
							$coef = explode(';', $row_temp[1]);

							$query_note = "SELECT `_value`, `_index` FROM `notes_items` WHERE `_IDeleve` = '".$IDeleve."' AND`_IDdata` = '".$row_temp[2]."' AND `_value` != '' ";
							$result_note = mysql_query($query_note);
							while ($row_note = mysql_fetch_array($result_note, MYSQL_NUM))
							{
								// On calcule la moyenne
								$notes_total += (($row_note[0] / $total[$row_note[1]]) * 20) * $coef[$row_note[1]];
								$notes_coef += $coef[$row_note[1]];
							}
						}
						$note = round($notes_total / $notes_coef, 1);
					}
					if (isset($notes_coef) && $notes_coef == 0) continue;

					$student_name = getUserNameByID($IDeleve);
					$filename = "rattrapage_".$student_name.'.xlsx';



					$uvRattrapageNote = getUVRattNote($uvID, $IDeleve);
					if ($note < getParam('note_min_rattrapage') || $note >= getParam('note_max_rattrapage')) continue;
					if ($note >= getParam('note_min_rattrapage') && $note < getParam('note_max_rattrapage')) {
						if (is_array($header)) {
							array_push($rows, $header);
							$header = '';
						}

						if (!$isMatAgv) $toPush = getUVNameByID($uvID);
						else $toPush = getPoleNameByIdPole(getPoleIDByPMAID($uvID)).' - '.getMatNameByIdMat(getMatIDByPMAID($uvID));
						array_push($rows, array($toPush, $note, round($uvRattrapageNote, 1)));
					}
					unset($note, $uvRattrapageNote);
				}
			}
		}


	}

// echo '<pre>';
// print_r($rows);
// echo '</pre>';
	$writer = new XLSXWriter();
	$writer->setAuthor('FLE');
	foreach($rows as $row)
		$writer->writeSheetRow('Sheet1', $row);


	if ($item == 65 && $cmde == "")
	{
		foreach ($merge_cell as $key => $value) {
			$writer->markMergedCell('Sheet1', $start_row = $value['start_row'], $start_col = $value['start_col'], $end_row = $value['end_row'], $end_col = $value['end_col']);
		}

	}

	if (isset($_GET['tofolder']) && $_GET['tofolder'] != '')
	{
	  // Si on est en phase d'export alors on enregistre le fichier plutôt que de le télécharger
		$writer->writeToFile("tmp/".$_GET['tofolder']."/".$filename);
	  // file_put_contents("tmp/export/bulletin_".$year.".pdf", $output);
	}
	else
	{
	  $writer->writeToStdOut();
	}

	// $writer->writeToFile('example.xlsx');
	// echo $writer->writeToString();
	exit(0);



?>
