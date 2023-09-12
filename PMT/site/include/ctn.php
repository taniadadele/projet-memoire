<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2010 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2010 by Jérémy CORNILLEAU (jeremy.cornilleau@gmail.com)
   Copyright (c) 2010 by Alexandre MAHE (alexandre.mahe@oxydia.com)

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
 *		module   : ctn.php
 *		projet   : fonctions utilitaires pour le cahier de texte numérique
 *
 *		version  : 1.0
 *		auteurs  : Alexandre MAHE - Jérémy CORNILLEAU
 *		creation : 16/05/2010
 *		modif    :
 */
?>


<?php
// //---------------------------------------------------------------------------
// function getLinesNumber($text)
// {
// /*
//  * fonction :	calcul le nombre de saut de ligne dans un texte
//  * in :		$text : le texte
//  * out :		nombre de saut de ligne
//  */
//
// 	$ln = explode("\n", $text);
//
// 	return ( $text != "" ) ? count ($ln) - 1 : 0 ;
// }

// //---------------------------------------------------------------------------
// function createPDF(&$pdf, $IDcentre, $IDclass, $IDgroup, $IDmat, $IDeleve, $date_begin, $date_end)
//  {
// /*
//  * fonction :	création du pdf des bulletins de notes
//  * in :		$IDclass : Id de la classe, $IDeleve : Id de l'élève, $period : trimestre
//  */
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	require_once $_SESSION["ROOTDIR"]."/lib/fpdf.php";
//
// 	require $_SESSION["ROOTDIR"]."/msg/ctn.php";
// 	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";
// 	require_once $_SESSION["ROOTDIR"]."/include/spip.php";
// 	require_once $_SESSION["ROOTDIR"]."/include/student.php";
// 	require_once $_SESSION["ROOTDIR"]."/include/calendar_tools.php";
//
// 	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/ctn.php");
//
// 	// lecture configuration
// 	// l'identifiant du cahier de texte : $IDctn
// 	// le modérateur du cahier de texte : $IDmod
// 	// la police d'affichage du texte par défaut : $auth[0]
// 	$Query  = "select _font, _IDctn, _IDmod from ctn ";
// 	$Query .= "where _IDclass = '$IDclass' ";
// 	// Si le cahier de texte dépend également d'un groupe
// 	$Query .= ( $IDgroup ) ? "and _IDgroup = '$IDgroup' " : "" ;
// 	$Query .= "limit 1";
//
// 	$result = mysqli_query($mysql_link, $Query) or die();
// 	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;
//
// 	// On sauvegarde les ID du ctn et du modérateur
// 	$IDctn = $auth[1];
// 	$IDmod = $auth[2];
//
// 	// initialisation d'une page de PDF
// 	$TopMargin    = 10;
// 	$RightMargin  = 10;
// 	$LeftMargin   = 10;
// 	$BottomMargin = 10;
// 	$PageWidth    = 210;
//
// 	// initialisation des polices
// 	$fontsz       = $auth[0]; // ctn._font
// 	$header1      = (int) ($fontsz * 1.5);
//
// 	// on charge la configuration dans l'objet FPDF
// 	$pdf->AddPage();
// 	$pdf->SetFont('Arial', '', $fontsz);
// 	$pdf->SetTopMargin($TopMargin);
// 	$pdf->SetRightMargin($RightMargin);
// 	$pdf->SetLeftMargin($LeftMargin);
// 	$pdf->SetAutoPageBreak(true, $BottomMargin);
//
// 	// Couleur des traits : noir
// 	$pdf->SetDrawColor(0, 0, 0);
//
// 	//---- entête de l'établissement : Nom et adresse
//  	$header  = str_replace(",", "\n", $_SESSION["CfgAdr"]);
//
// 	// On veut un cadre d'entête de 4 lignes
// 	for ($i = getLinesNumber($header); $i < 4; $i++)
// 		$header .= "\n";
//
// 	// On crée une cellule avec une bordure d'1 pixel, remplie avec header : Infos sur l'établissement
// 	$colWidth = ($PageWidth - $LeftMargin - $RightMargin) / 3;
// 	$pdf->SetX($LeftMargin);
// 	$pdf->MultiCell($colWidth, 5, $header, 1);
//
//
// 	//---- récupération du titre de la matière
// 	if ( $IDmat != 0 ) {
// 		$query   = "select _titre from campus_data where _IDmat = $IDmat AND _lang = '".$_SESSION["lang"]."' limit 1";
// 		$result  = mysqli_query($mysql_link, $query);
//
// 		$row     = remove_magic_quotes(mysqli_fetch_row($result));
//
// 		$matiere = $row[0];
// 	}
//
// 	//---- Récupération du nom de l'élève.
// 	$query   = "select _name, _fname from user_id ";
// 	$query  .= "where _ID = '$IDeleve' ";
// 	$query  .= "limit 1";
//
// 	$result  = mysqli_query($mysql_link, $query);
// 	$row     = remove_magic_quotes(mysqli_fetch_row($result));
//
//
// 	// affichage des classes
// 	$query  = "select _ident from campus_classe ";
// 	$query .= "where _IDclass = '$IDclass' ";
// 	$result = mysqli_query($mysql_link, $query);
// 	$myrow  = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
//
// 	// On affiche :
// 	// L'étudiant
// 	$header    = "$row[0] $row[1]\n";
//
// 	// La Filière
// 	$header   .= $msg->read($CTN_SECTION, $myrow[0])."\n";
//
// 	// On affiche la date en utilisant le bon format.
// 	$dateBegin = date2longfmt($date_begin, "jma");
// 	$header   .= ($date_begin == $date_end)?$dateBegin:$msg->read($CTN_FROMTO, Array($dateBegin, date2longfmt($date_end, "jma")));
//
// 	// Si une matière est précisée, on affiche son titre.
// 	if ( $IDmat != 0 )
// 		$header .= "\n".$matiere;
//
// 	// On veut un cadre d'entête de 5 lignes
// 	for ($i = getLinesNumber($header); $i < 5; $i++)
// 		$header .= "\n";
//
// 	// On crée une nouvelle cellule, à droite de la précédente, deux fois plus large.
// 	$pdf->SetLeftMargin($colWidth + $LeftMargin);
// 	$pdf->SetX($colWidth + $LeftMargin);
// 	$pdf->SetY($TopMargin);
// 	$pdf->MultiCell(2*$colWidth, 5, $header, 1);
//
// 	// Retour à la ligne
// 	$pdf->Ln(10);
//
// 	// récupération des articles du ctn
// 	$query  = "select _IDitem, _date, _delay, _title, _texte, _IDmat, _ID from ctn_items ";
// 	$query .= "where _IDctn = '$IDctn' ";
// 	// On affiche seulement les messages entre $date_debut (inclus) et $date_end+1(exclu) pour prendre en compte les heures et minutes du dernier jour
// 	$query .= "and _date < DATE_ADD('$date_end', INTERVAL 1 DAY) and _date >= '$date_begin' ";
// 	// On affiche seulement les messages visibles sauf si l'utilisateur est modérateur
// 	$query .= ($IDeleve != $IDmod) ? "and _visible = 'O' " : "" ;
// 	// On affiche seulement les enregistrements d'une matière donnée si celle-ci est spécifiée.
// 	$query .= ( $IDmat ) ? "and _IDmat = $IDmat " : "" ;
// 	$query .= "order by _date, _IDmat";
//
// 	$result = mysqli_query($mysql_link, $query) or die();
// 	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
//
// 	// S'il n'y a aucune enregistrement, on retourne une erreur.
// 	if ( !$row )
// //		die("Aucun enregistrement pour cette matière à cette date.");
// 		return;
//
// 	// On affichera un fond pour chaque ligne d'article.
// 	$pdf->SetFillColor(240,240,240);
//
// 	// Données
// 	$previousDate = "";
// 	$width        = $PageWidth - $LeftMargin - $RightMargin;
//
// 	// Pour tous les champs
// 	while($row) {
// 		// On stocke les informations récupérées dans des variables intelligibles.
// 		$idItem   = $row[0];	// pour les requête.
// 		$date     = date2longfmt($row[1], "jma");		// format Le Jour jj Mois aaaa
// //		$time     = date_format($datetime, 'H:i');	// format hh:mm
// 		$time     = date2longfmt($row[1], "hi");		// format hh:mm
// 		$titre    = html_entity_decode(stripHTMLtags(str_replace('<br/>', ' ', $row[3])));
// 		$article  = str_replace('\n', '<br/>', $row[4]);
// 		$article  = html_entity_decode(stripHTMLtags($article));
// 		$duree    = $row[2];
// 		$auteur   = getUserNameByID($row[6], false);
//
// 		// On récupère le nom de la matière
// 		$queryMatiere  = "select _titre from campus_data ";
// 		$queryMatiere .= "where _IDmat = '$row[5]' AND _lang = '".$_SESSION["lang"]."' ";
// 		$queryMatiere .= "limit 1";
//
// 		$resultMatiere = mysqli_query($mysql_link, $queryMatiere);
// 		$rowMatiere    = ( $resultMatiere ) ? remove_magic_quotes(mysqli_fetch_row($resultMatiere)) : 0 ;
//
// 		$matiere       = $rowMatiere[0];
//
// 		// On récupère le nombre de fois où l'article a été vus.
// 		$queryVus      = "select count(*) from ctn_vu ";
// 		$queryVus     .= "where _IDitem = $idItem ";
// 		$queryVus     .= "limit 1";
//
// 		$resultVus     = mysqli_query($mysql_link, $queryVus);
// 		$rowVus        = ( $resultVus ) ? remove_magic_quotes(mysqli_fetch_row($resultVus)) : 0 ;
//
// 		$vus           = $rowVus[0];
//
// 		// On se décale pour afficher une marge
// 		$pdf->SetX($LeftMargin);
// 		// Si c'est le premier élément d'un tableau
// 		if($previousDate != $date) {
// 			// Si ce n'est pas le premier tableau
// 			if($previousDate) {
// 				$pdf->SetX($LeftMargin);
// 				// On ferme le tableau précédent (on trace une ligne en dessous)
// 				$pdf->Cell($width,0,'','T');
// 				// On laisse un espace en dessous et on se décale de la marge
// 				$pdf->Ln(10);
// 				$pdf->SetX($LeftMargin);
// 			}
// 			// S'il y a plusieurs tableaux (plusieurs dates)
// 			if($date_begin != $date_end) {
// 				$texte = strtoupper($date);
// 				// On affiche un titre en majuscule.
// 				$pdf->Cell($width,1,$texte,'');
// 				// On revient à la ligne et on se décale de la marge
// 				$pdf->Ln(5);
// 				$pdf->SetX($LeftMargin);
// 			}
//
// 			// La couleur de la première ligne est blanche
// 			$fill=false;
//
// 			// On ouvre le tableau (on trace une ligne au-dessus) et on revient à la ligne
// 			$pdf->Cell($width,0,'','T');
// 		}
//
// 		// Si la case ne tient pas dans la suite de la page, on passe à la page suivante
// 		if($pdf->getY() > 275) {
// 			$pdf->SetX($LeftMargin);
// 			// On ferme le tableau précédent (on trace une ligne en dessous)
// 			$pdf->Cell($width,0,'','T');
// 			// On ajoute une page
// 			$pdf->AddPage();
// 			$pdf->SetX($LeftMargin);
// 			// On ouvre le nouveau tableau
// 			$pdf->Cell($width,0,'','T');
// 			}
//
// 		// On récupère les coordonnées -> début du tableau
// 		$pdf->SetX($LeftMargin);
// 		$x = $pdf->getX();
// 		$y = $pdf->getY();
//
// 		// Colonne de la matière (si elle n'est pas précisée) et de l'heure d'une largeur de 1/6 de la page
// 		$w      = (int)($width/6);
//
// 		$texte  = ($IDmat == 0)?$matiere."\n":"";
// 		$texte .= $msg->read($CTN_HOURPDF, $time);
//
// 		// On veut une cellule de 5 lignes
// 		for ($i = getLinesNumber($texte); $i < 5; $i++)
// 			$texte .= "\n";
// 		$pdf->MultiCell($w,5,$texte,'LR','L',$fill);
//
// 		// On se positionne bien
// 		$x += $w;
// 		$pdf->setXY($x, $y);
// 		// Colonne de l'article d'une largeur de la moitié de la page
// 		$w = 3*(int)($width/6);
//
// 		// Une cellule pour afficher le titre.
// 		// Affiche en gras
// 		$pdf->SetFont('Arial','B');
// 		$texte = $titre."\n";
// 		$pdf->MultiCell($w,5,$texte,'LR','L',$fill);
// 		//$pdf->Ln();
//
// 		// Enlève le gras
// 		$pdf->SetFont('');
//
// 		// On se positionne aux coordonnées de la deuxième cellule.
// 		$pdf->SetXY($x, $y+5);
//
// 		// Le texte doit être suffisamment grand
// 		for ($i = getLinesNumber($article); $i < 4; $i++)
// 			$article .= "\n";
// 		// Une cellule pour afficher les 200 premiers caractères du texte.
// 		$texte = substr($article, 0, 200)." ...";
//
// 		// On veut une cellule de 5 lignes
// 		for ($i = getLinesNumber($texte); $i < 2; $i++)
// 			$texte .= "\n";
// 		$pdf->MultiCell($w,4,$texte,'LR','L',$fill);
//
// 		// Remonter aux bonnes coordonnées.
// 		$x += $w;
// 		$pdf->SetXY($x, $y);
//
// 		// Colonne des informations sur l'article, sur le reste de la largeur de la page
// 		$w      = $width - $x + $LeftMargin;
//
// 		$texte  = $msg->read($CTN_DELAYPDF, $duree)."\n";
// 		$texte .= $msg->read($CTN_AUTHORPDF, $auteur)."\n";
// 		$texte .= $msg->read($CTN_VIEWS, $vus)."\n";
//
// 		// On veut une cellule de 5 lignes
// 		for ($i = getLinesNumber($texte); $i < 5; $i++)
// 			$texte .= "\n";
// 		$pdf->MultiCell($w,5,$texte,'LR','L',$fill);
//
// 		// On change de couleur.
// 		$fill=!$fill;
// 		// On enregistre la date comme date précédente.
// 		$previousDate = $date;
//
// 		$row = remove_magic_quotes(mysqli_fetch_row($result));
// 	}
//
// 	$pdf->SetX($LeftMargin);
// 	// On ferme le tableau précédent
// 	$pdf->Cell($width,0,'','T');
// 	// On laisse un espace en dessous
// 	$pdf->Ln(20);
// }
//
// //---------------------------------------------------------------------------
// function exportPDF($IDgroup, $IDcentre, $IDclass, $IDmat, $IDeleve, $date_begin=0, $date_end=0)
// {
// /*
//  * fonction :	création du pdf du cahier de texte (appel de createPDF) // temps de génération du pdf
//  * in :			$IDcentre : Id du centre, $IDclass : Id de la classe, $IDmat = Id de la matière, $IDeleve : Id de l'élève,
// 				$date_begin : date de début de l'export (0), $date_end : date de fin de l'export (0)
//  */
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	require_once $_SESSION["ROOTDIR"]."/lib/fpdf.php";
//
// 	require $_SESSION["ROOTDIR"]."/msg/ctn.php";
// 	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";
// 	require_once $_SESSION["ROOTDIR"]."/include/session.php";
//
// 	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/ctn.php");
//
// 	$time_start = getmicrotime();
//
// 	// lecture des droits
// 	$Query  = "select _IDgrprd from ctn ";
// 	$Query .= "where _IDcentre = '$IDcentre' ";
// 	$Query .= "limit 1";
//
// 	$result = mysqli_query($mysql_link, $Query);
// 	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;
//
// 	// vérification des autorisations
// 	whoami(@$_GET["sid"]);
// 	verifySessionAccess(0, $auth[0]);
//
// 	// préparation du document pdf
// 	$pdf = new FPDF('P','mm','A4');
//
// 	// véfication des dates
// 	if ($date_begin != 0 && $date_end != 0 && $date_begin > $date_end) {
// 		$temp       = $date_begin;
// 		$date_begin = $date_end;
// 		$date_end   = $temp;
// 	}
//
// 	createPDF($pdf, $IDcentre, $IDclass, $IDgroup, $IDmat, $IDeleve, $date_begin, $date_end);
//
// 	// enregistrement du document
// 	$file  = $_SESSION["ROOTDIR"]."/tmp/".SessionID().".pdf";
// 	$fname = ($date_begin == $date_end)
// 		? $_SESSION["ROOTDIR"]."/tmp/ctn_".$IDclass."_".$date_begin.".pdf"
// 		: $_SESSION["ROOTDIR"]."/tmp/ctn_".$IDclass."_".$date_begin."_".$date_end.".pdf" ;
//
// 	$pdf->Output($file, 'F');
//
// 	// ouverture document
// 	$time = getmicrotime() - $time_start;
//
// 	print("
// 		<p style=\"text-align:center;\">
// 			". $msg->read($CTN_GETDOC, Array(strval(number_format($time, 2)), myurlencode("index.php?file=$file&fname=$fname&tmp=1"))) ."
// 		</p>");
// }
// //---------------------------------------------------------------------------
// function get_campus_class($ident, $idcentre)
// {
// // $ident, nom de la classe
// // $idcentre, ID du centre
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	// lecture code classe
// 	$query  = "select _IDclass from campus_classe ";
// 	$query .= "where _ident = '$ident' AND _IDcentre = '$idcentre' ";
// 	$query .= "limit 1";
//
// 	$return = mysqli_query($mysql_link, $query);
// 	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;
//
// 	return (int) @$myrow[0];
// }
// //---------------------------------------------------------------------------
// function get_campus_data($ident, $lang)
// {
// // $ident, nom de la matière
// // $lang, code langue
//
// 	require $_SESSION["ROOTDIR"]."/globals.php";
//
// 	// lecture code matière
// 	$query  = "select _IDmat from campus_data ";
// 	$query .= "where _titre = '$ident' AND _lang = '$lang' ";
// 	$query .= "limit 1";
//
// 	$return = mysqli_query($mysql_link, $query);
// 	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;
//
// 	return (int) @$myrow[0];
// }


//---------------------------------------------------------------------------
function get_ctn($idclass, $idcentre)
{
// $idclass, ID de la classe
// $idcentre, ID du centre

	require $_SESSION["ROOTDIR"]."/globals.php";

	// lecture code CTN
	$query  = "select _IDctn from ctn ";
	$query .= "where _IDclass = '$idclass' AND _IDcentre = '$idcentre' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return (int) @$myrow[0];
}
//---------------------------------------------------------------------------


// function exist_item($idctn, $idmat, $date)
// {
// // $idctn, ID du CTN
// // $idmat, ID de la matière
// // $date,  date de l'item
//
// 	require "globals.php";
//
// 	// lecture CTN
// 	$query  = "select _IDitem from ctn_items ";
// 	$query .= "where _IDctn = '$idctn' AND _IDmat = '$idmat' AND _date = '$date' ";
// 	$query .= "limit 1";
//
// 	$return  = mysqli_query($mysql_link, $query);
// 	$myrow   = ( $return ) ? mysqli_fetch_row($return) : 0 ;
//
// 	return ( $myrow ) ? $myrow[0] : 0 ;
// }


//---------------------------------------------------------------------------
function import_ctn($IDcentre, $file)
{
// $IDcentre, ID du centre
// $file, fichier source

	require $_SESSION["ROOTDIR"]."/globals.php";

	require_once $_SESSION["ROOTDIR"]."/lib/lib_import_xls.php";

	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP1251');
	$data->read($file["tmp_name"]);

	// initialisation
	$count = $exist = 0;
	list($filename, $ext) = explode(".", $file["name"]);

	// parcours des onglets
	for($sheet = 0; $sheet < count($data->sheets); $sheet++) {
		// initialisation
		$IDclass = get_campus_class(trim($filename), $IDcentre);
		$IDmat   = get_campus_data(trim(addslashes($data->boundsheets[$sheet]['name'])), $_SESSION["lang"]);
		$IDctn   = get_ctn($IDclass, $IDcentre);

		$title   = trim(addslashes(@$data->sheets[$sheet]['cells'][2][5]));			// intitulé chapitre

		// si pas de ctn ouvert => enregistrement non valide
		if ( $IDctn ) {
			// on commence à l'indice 2 à cause de l'en-tête dans la feuille de calcul
//			for ($i = 2; $i <= $data->sheets[$sheet]['numRows'] AND strlen(trim(@$data->sheets[$sheet]['cells'][$i][1])); $i++) {
			for ($i = 2; $i <= $data->sheets[$sheet]['numRows']; $i++) {
//				$data->setColumnFormat(1, "d/m/Y");
//				$data->dateFormats[0xe];

				@list($day, $month, $year) = explode("/", trim(@$data->sheets[$sheet]['cells'][$i][1]));
				@list($hour, $min)         = preg_split("/[hH]/", trim(@$data->sheets[$sheet]['cells'][$i][2]));

				$min   = ( strlen($min) ) ? $min : "00" ;

				$todo  = "20$year-$month-$day $hour:$min";
				$delay = trim(addslashes(@$data->sheets[$sheet]['cells'][$i][3]));					// durée
				$title = ( strlen(trim(@$data->sheets[$sheet]['cells'][$i][4])) )						// intitulé
					? trim(addslashes(@$data->sheets[$sheet]['cells'][$i][4]))
					: $title ;
				$texte = trim(addslashes(@$data->sheets[$sheet]['cells'][$i][5]));					// texte

				// s'il existe un enregistrement => mise à jour
				$exist = exist_item($IDctn, $IDmat, "$todo");

				if ( $exist ) {
					$query  = "update ctn_items set ";
					$query .= "_delay = '$delay', ";	// durée
					$query .= "_title = '$title', ";	// intitulé
					$query .= "_texte = '$texte', ";	// texte
					$query .= "where _IDitem = '$exist' ";
					$query .= "limit 1";
					}
				else {
					$query  = "insert into ctn_items values(";
					$query .= "'', ";
					$query .= "'$IDctn', ";
					$query .= "'$IDmat', ";
					$query .= "'".$_SESSION["CnxID"]."', ";
					$query .= "'".$_SESSION["CnxIP"]."', ";
					$query .= "'$todo', ";
					$query .= "'$delay', ";
					$query .= "'$title', ";
					$query .= "'$texte', ";
					$query .= "'N', ";
					$query .= "'', ";
					$query .= "'O'";
					$query .= ")";
					}

				if ( mysqli_query($mysql_link, $query) )
					$count++;

				$exist = ( $exist ) ? $exist : mysqli_insert_id($mysql_link) ;

				$next  = trim(addslashes(@$data->sheets[$sheet]['cells'][$i][6]));					// à préparer
				$type  = strlen(trim(addslashes(@$data->sheets[$sheet]['cells'][$i][7]))) ? 2 : 1 ;			// controle

				if ( strlen($next) ) {
					@list($day, $month, $year) = explode("/", trim(@$data->sheets[$sheet]['cells'][$i][8]));
					@list($hour, $min)         = preg_split("/[hH]/", trim(@$data->sheets[$sheet]['cells'][$i][9]));

					$todo  = "20$year-$month-$day $hour:$min";

					$query = "insert into ctn_data values('', '$exist', '$type', '$todo', '$next')";

					mysqli_query($mysql_link, $query);
					}
				}	// endfor sheet
			}	// endif ctn
		}

	return $count;
}
//---------------------------------------------------------------------------
?>
