<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2009 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : notes.php
 *		projet   : fonctions utilitaires pour les bulletins de notes
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/12/09
 *		modif    :
 */


//---------------------------------------------------------------------------
function getLinesNumber($text)
{
/*
 * fonction :	calcul le nombre de saut de ligne dans un texte
 * in :		$text : le texte
 * out :		nombre de saut de ligne
 */

	$ln = explode("\n", $text);

	return ( $text != "" ) ? count ($ln) - 1 : 0 ;
}
//---------------------------------------------------------------------------
function computeMark($IDclass, $IDmat, $IDeleve, $year, $period)
{
/*
 * fonction :	calcul la moyenne de l'élève sur une matière
 * in :		$IDclass : Id de la classe, $IDmat : Id de la matière, $IDeleve : Id de l'élève, $year : année, $period : trimestre
 * out :		moyenne de l'élève
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	if ($_GET['IDcentre'] == "") $IDcentre = $_SESSION['CnxCentre'];
	else $IDcentre = $_GET['IDcentre'];
	// lecture des droits
	$Query  = "select _text from notes ";
	$Query .= "where _IDcentre = '".$IDcentre."' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$_total = $_coef = Array();

	// recherche de l'entête du tableau
	$Query  = "select _IDdata, _total, _coef from notes_data ";
	$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _period = '$period' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if ( mysqli_num_rows($result) ) {
		$_total   = explode(";", $row[1]);
		$_coef    = explode(";", $row[2]);
		}

	// initialisation
	$totcoef = $totpts = $value = 0;

	for ($i = 0; $i < count($_coef); $i++) {
		$Query  = "select _value from notes_items ";
		$Query .= "where _IDdata = '$row[0]' AND _IDeleve = '$IDeleve' AND _index = '$i' ";

		$return = mysqli_query($mysql_link, $Query);
		$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

		if ( $myrow[0] != "" ) {
			$value++;
			$totpts  += (float) ($_coef[$i] * $myrow[0]);
			$totcoef += (float) ($_coef[$i] * $_total[$i]);
			}
		}

	// Moyenne
	if ($value != 0 && $value != "") return ($auth[0] * $totpts / $totcoef);
	else return "";
	// return ( $value )
	// 	? (float) ($auth[0] * $totpts / $totcoef)
	// 	: "" ;
}
//---------------------------------------------------------------------------
function computeClass($IDclass, $IDmat, $year, $period)
{
/*
 * fonction :	calcul la moyenne de l'élève sur une matière
 * in :		$IDclass : Id de la classe, $IDmat : Id de la matière, $year : année, $period : trimestre
 * out :		moyenne de l'élève
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// initialisation
	$num = $tot = (float) 0;

	// affichage des élèves
	$query  = "select _ID from user_id ";
	$query .= "where _visible = 'O' AND _IDgrp = '1' ";
	$query .= "AND _IDclass = '$IDclass' ";

	$result = mysqli_query($mysql_link, $query);
	$row    = mysqli_fetch_row($result);

	while ( $row ) {
		$note = computeMark($IDclass, $IDmat, $row[0], $year, $period);

		if ( $note != "" ) {
			$num++;
			$tot += (float) $note;
			}

		$row = mysqli_fetch_row($result);
		}

	// moyenne élève
	return ( $num )
		? (float) ($tot / $num)
		: "" ;
}
//---------------------------------------------------------------------------
function computeRank($IDclass, $idmat, $year, $period)
{
/*
 * fonction :	calcul le rang des élèves sur un trimestre
 * in :		$IDclass : Id de la classe, $idmat : tableau des matières, $year : année, $period : trimestre
 * out :		tableau des notes par ordre décroissant
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	$rank = Array();

	// affichage des élèves
	$query  = "select _ID from user_id ";
	$query .= "where _visible = 'O' AND _IDgrp = '1' ";
	$query .= "AND _IDclass = '$IDclass' ";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$j = 0;
	while ( $row ) {
		$tot = $num = (float) 0;

		for ($i = 0; $i < count($idmat); $i++) {
			$note = computeMark($IDclass, $idmat[$i], $row[0], $year, $period);

			if ( $note != "" ) {
				$num++;
				$tot += (float) $note;
				}
			}

		if ( $num )
			$rank[$j] = $tot / $num;

		$j++;
		$row = mysqli_fetch_row($result);
		}

	@rsort($rank);

	return $rank;
}
//---------------------------------------------------------------------------
function createPDF(&$pdf, $IDcentre, $IDclass, $IDeleve, $year, $period)
{
/*
 * fonction :	création du pdf des bulletins de notes
 * in :		$IDclass : Id de la classe, $IDeleve : Id de l'élève, $year : année, $period : trimestre
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	require_once $_SESSION["ROOTDIR"]."/lib/fpdf.php";

	require $_SESSION["ROOTDIR"]."/msg/notes.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";
	require_once $_SESSION["ROOTDIR"]."/include/spip.php";
	require_once $_SESSION["ROOTDIR"]."/include/student.php";
	require_once $_SESSION["ROOTDIR"]."/include/calendar_tools.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/notes.php");

	// lecture configuration
	$Query  = "select _period, _decimal, _separator, _font from notes ";
	$Query .= "where _IDcentre = '$IDcentre' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// initialisation
	$TopMargin    = 10;
	$RightMargin  = 10;
	$LeftMargin   = 10;
	$BottomMargin = 10;
	$PageWidth    = 210;

	$fontsz       = $auth[3];
	$header1      = (int) ($fontsz * 1.5);

	$pdf->AddPage();
	$pdf->SetFont('Arial', '', $fontsz);
	$pdf->SetTopMargin($TopMargin);
	$pdf->SetRightMargin($RightMargin);
	$pdf->SetLeftMargin($LeftMargin);
	$pdf->SetAutoPageBreak(true, $BottomMargin);

	// Couleur des traits
	$pdf->SetDrawColor(0, 0, 0);

	//---- entête lycée
 	$header  = $_SESSION["CfgIdent"] . "\n";
 	$header .= str_replace(",", "\n", $_SESSION["CfgAdr"]);
	$header .= "\n";
	$header .= ( strlen($_SESSION["CfgTel"]) ) ? $msg->read($NOTES_TEL, $_SESSION["CfgTel"])." - " : "" ;
	$header .= ( strlen($_SESSION["CfgFax"]) ) ? $msg->read($NOTES_FAX, $_SESSION["CfgFax"]) : "" ;

	for ($i = getLinesNumber($header); $i < 6; $i++)
		$header .= "\n";

	$colWidth = ($PageWidth - $LeftMargin - $RightMargin) / 2;
	$pdf->SetX($LeftMargin);
	$pdf->MultiCell($colWidth, 3, $header, 1);

	//---- entête élève
	$query   = "select _name, _fname, _born, _regime from user_id ";
	$query  .= "where _ID = '$IDeleve' AND _IDgrp = '1' ";
	$query  .= "limit 1";

	$result  = mysqli_query($mysql_link, $query);
	$row     = remove_magic_quotes(mysqli_fetch_row($result));

	// affichage des classes
	$query  = "select _ident from campus_classe ";
	$query .= "where _IDclass = '$IDclass' ";

	$result = mysqli_query($mysql_link, $query);
	$myrow  = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$next   = $year + 1;

	$header  = "$row[0] $row[1]\n";
	$header .= $msg->read($NOTES_BORN, date2longfmt($row[2], "jma"))."\n";
	$header .= $msg->read($NOTES_REGIME, getStudentRegime($row[3]))."\n\n";
	$header .= $msg->read($NOTES_SCHOOLYEAR, "$year - $next")."\n";
	$header .= $msg->read($NOTES_SECTION, $myrow[0]);

	$pdf->SetLeftMargin($colWidth + $LeftMargin);
	$pdf->SetX($colWidth + $LeftMargin);
	$pdf->SetY($TopMargin);
	$pdf->MultiCell($colWidth, 3, $header, 1);

	//---- n° trimestre
	$list   = explode (",", $msg->read($NOTES_PERIODLIST));

	$colWidth = $PageWidth - $LeftMargin - $RightMargin;
	$text     = $msg->read($NOTES_QUATERMARK, $list[$auth[0]]." $period");

	$pdf->SetFont('Arial', 'B', $header1);
	$pdf->SetLeftMargin($LeftMargin);
	$pdf->SetX($LeftMargin);
	$pdf->Cell($colWidth , 5, $text, 0, 1, 'C');

	//---- prof principal
	$pdf->SetFont('Arial', '', $fontsz);

	$Query  = "select _IDpp from campus_classe ";
	$Query .= "where _IDclass = '$IDclass' ";
	$Query .= "limit 1";

	$return = mysqli_query($mysql_link, $Query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	$text   = ( getUserNameByID($myrow[0], false) != "??" )
		? $msg->read($NOTES_MAINTEACHER) ." ". getUserNameByID($myrow[0], false)
		: "" ;

	$pdf->Cell(190, 5, $text);

	//---- tableau de notes
	$width    = array(50, 15, 15, 110);		//Largeurs des colonnes
	$posy     = $pdf->GetY();			// position courante
	$colWidth = 0;

	$header = array(
		stripHTMLtags(str_replace('<br/>', '\n', $msg->read($NOTES_MATTER))),
		stripHTMLtags(str_replace('<br/>', '\n', $msg->read($NOTES_STUDENTHDR))),
		stripHTMLtags(str_replace('<br/>', '\n', $msg->read($NOTES_CLASSHDR))),
		stripHTMLtags(str_replace('<br/>', '\n', $msg->read($NOTES_COMMENTARY))));

	$pdf->SetFont('Arial', 'B', $fontsz);

	for ($i = 0; $i < count($header); $i++) {
		for ($j = getLinesNumber($header[$i]); $j < 2; $j++)
			$header[$i] .= "\n";

		$pdf->SetFillColor(224,235,255);
		$pdf->SetLeftMargin($LeftMargin + $colWidth);
		$pdf->SetX($LeftMargin + $colWidth);
		$pdf->SetY($posy);

		$pdf->MultiCell($width[$i], 3, $header[$i], 1, 'C', true);

		$colWidth += $width[$i];
		}

	// affichage des matières
	$Query  = "select distinctrow campus_data._IDmat, campus_data._titre, notes_data._IDdata ";
	$Query .= "from campus_data, notes_data ";
	$Query .= "where campus_data._lang = '".$_SESSION["lang"]."' ";
	$Query .= "AND notes_data._year = '$year' AND notes_data._IDclass = '$IDclass' AND notes_data._period = '$period' ";
	$Query .= "AND notes_data._IDmat = campus_data._IDmat ";
	$Query .= "order by campus_data._titre asc";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	// pour statistiques
	$rnum   = $rtot = Array('0', '0');

	$pdf->SetFont('Arial', '', $fontsz);

	while ( $row ) {
		$data   = array();	// initialisation

		// recherche enseignant de la matière
		$Query  = "select _ID from notes_items ";
		$Query .= "where _IDdata = '$row[2]' ";
		$Query .= "order by _IDitems ";
		$Query .= "limit 1";

		$return = mysqli_query($mysql_link, $Query);
		$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

		$data[0] = "$row[1]\n-- ". getUserNameByID($myrow[0], false);

		//---- calcul de la note
		$note = computeMark($IDclass, $row[0], $IDeleve, $year, $period);
		$fmt  = ( $note != "" ) ? number_format($note, $auth[1], $auth[2], ".") : "" ;

		$data[1] = $fmt;

		// statistiques
		if ( $note != "" ) {
			$rnum[0] += 1;
			$rtot[0] += (float) $note;
			}

		//---- calcul de la moyenne classe
		$note   = computeClass($IDclass, $row[0], $year, $period);
		$fmt  = ( $note != "" ) ? number_format($note, $auth[1], $auth[2], ".") : "" ;

		$data[2] = $fmt;

		// statistiques
		if ( $note != "" ) {
			$rnum[1] += 1;
			$rtot[1] += (float) $note;
			}

		//---- appréciation
		$Query  = "select _text from notes_text ";
		$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '$row[0]' AND _year = '$year' AND _period = '$period' ";
		$Query .= "limit 1";

		$return = mysqli_query($mysql_link, $Query);
		$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

		$data[3] = $myrow[0];

		$posy     = $pdf->GetY();			// position courante
		$colWidth = 0;

		for ($i = 0; $i < count($data); $i++) {
			for ($j = getLinesNumber($data[$i]); $j < 3; $j++)
				$data[$i] .= "\n";

			$pdf->SetLeftMargin($LeftMargin + $colWidth);
			$pdf->SetX($LeftMargin + $colWidth);
			$pdf->SetY($posy);

			$center = ( $i == 1 OR $i == 2 ) ? "C" : "L" ;
			$pdf->MultiCell($width[$i], 3, $data[$i], 1, $center);

			$colWidth += $width[$i];
			}

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	//---- moyenne générale
	$data    = array();	// réinitialisation

	$data[0] =  $msg->read($NOTES_GLOBALMEAN);

	$note    = ( $rnum[0] ) ? (float) ($rtot[0] / $rnum[0]) : "" ;
	$fmt     = ( $note != "" ) ? number_format($note, $auth[1], $auth[2], ".") : "" ;

	$data[1] = $fmt;

	$note    = ( $rnum[1] ) ? (float) ($rtot[1] / $rnum[1]) : "" ;
	$fmt     = ( $note != "" ) ? number_format($note, $auth[1], $auth[2], ".") : "" ;

	$data[2] = $fmt;

	$posy     = $pdf->GetY();			// position courante
	$colWidth = 0;

	for ($i = 0; $i < count($data); $i++) {
		$pdf->SetLeftMargin($LeftMargin + $colWidth);
		$pdf->SetX($LeftMargin + $colWidth);
		$pdf->SetY($posy);

		$bold   = ( $i ) ? "B" : "" ;
		$center = ( $i ) ? "C" : "L" ;

		$pdf->SetFont('Arial', $bold, $fontsz);
		$pdf->MultiCell($width[$i], 3, $data[$i], 0, $center);

		$colWidth += $width[$i];
		}

	//---- fin du document
	$col1 = $width[0] + $width[1] + $width[2];
	$col2 = $width[3];

	$pdf->SetFont('Arial', 'B', $fontsz);
	$pdf->SetX($LeftMargin);
	$pdf->Cell($col1, 5, stripHTMLtags($msg->read($NOTES_SCHOLARSHIP)));
	$pdf->Cell($col2, 5, stripHTMLtags($msg->read($NOTES_CLASSCOUNCIL)));
	$pdf->Ln();

	$posy = $pdf->GetY();			// position courante

	// retards et autres
	$pdf->SetFont('Arial', '', $fontsz);
	$pdf->SetX($LeftMargin);
	$pdf->Cell($width[0], 5, $msg->read($NOTES_LATENESS), 0, 0, 'R');
	$pdf->Cell($width[1], 5, getStudentOffence($IDeleve, $IDclass, 2), 0, 1, 'C');
	$pdf->SetX($LeftMargin);
	$pdf->Cell($width[0], 5, $msg->read($NOTES_ABSENCE), 0, 0, 'R');
	$pdf->Cell($width[1], 5, getStudentOffence($IDeleve, $IDclass), 0, 1, 'C');

	// appréciation conseil de classe
	$Query  = "select _text from notes_text ";
	$Query .= "where _IDeleve = '$IDeleve' AND _IDclass = '$IDclass' AND _IDmat = '0' AND _year = '$year' AND _period = '$period' ";
	$Query .= "limit 1";

	$return = mysqli_query($mysql_link, $Query);
	$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

	$text   = $myrow[0];
	for ($j = getLinesNumber($text); $j < 6; $j++)
		$text .= "\n";

	$pdf->SetLeftMargin($LeftMargin + $col1);
	$pdf->SetX($LeftMargin + $col1);
	$pdf->SetY($posy);
	$pdf->MultiCell($col2, 3, $text, 1);

	//---- signature
	$pdf->SetX($LeftMargin);
	$pdf->Cell($PageWidth, 5, $msg->read($NOTES_EDITED, date2longfmt(date("Y-m-d"), "jma")), 0, 1);
	$pdf->SetX($LeftMargin);
	$pdf->Cell($PageWidth, 5, $msg->read($NOTES_SIGNATURE));
}
//---------------------------------------------------------------------------
function exportPDF($IDcentre, $IDclass, $IDeleve, $year, $period)
{
/*
 * fonction :	création du pdf des bulletins de notes
 * in :		$IDclass : Id de la classe, $IDeleve : Id de l'élève, $year : année, $period : trimestre
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	require_once $_SESSION["ROOTDIR"]."/lib/fpdf.php";

	require $_SESSION["ROOTDIR"]."/msg/notes.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";
	require_once $_SESSION["ROOTDIR"]."/include/session.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/notes.php");

	$time_start = getmicrotime();

	// lecture des droits
	$Query  = "select _IDgrprd from notes ";
	$Query .= "where _IDcentre = '$IDcentre' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// vérification des autorisations
	whoami(@$_GET["sid"]);
	verifySessionAccess(0, $auth[0]);

	// préparation du document pdf
	$pdf = new FPDF('P','mm','A4');

	if ( $IDeleve )
		createPDF($pdf, $IDcentre, $IDclass, $IDeleve, $year, $period);
	else {
		// affichage des élèves
		$query  = "select _ID from user_id ";
		$query .= "where _visible = 'O' AND _IDgrp = '1' ";
		$query .= "AND _IDclass = '$IDclass' ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		while ( $row ) {
			createPDF($pdf, $IDcentre, $IDclass, $row[0], $year, $period);

			$row = mysqli_fetch_row($result);
			}
		}

	// enregistrement du document
	$file  = $_SESSION["ROOTDIR"]."/tmp/".SessionID().".pdf";
	$fname = ( $IDeleve )
		? $_SESSION["ROOTDIR"]."/tmp/bulletin_".$IDclass."_".$IDeleve."_".$year."_".$period.".pdf"
		: $_SESSION["ROOTDIR"]."/tmp/bulletin_".$IDclass."_".$year."_".$period.".pdf" ;

	$pdf->Output($file, 'F');

	// ouverture document
	$time = getmicrotime() - $time_start;

	print("
		<p style=\"text-align:center;\">
			". $msg->read($NOTES_GETDOC, Array(strval(number_format($time, 2)), myurlencode("index.php?file=$file&fname=$fname&tmp=1"))) ."
		</p>");
}
//---------------------------------------------------------------------------
function get_student($idclass, $idcentre, $name, $fname)
{
// $idclass, ID de la classe
// $idcentre, ID du centre
// $name, nom
// $fname, prénom

	require $_SESSION["ROOTDIR"]."/globals.php";

	$query  = "select _ID from user_id ";
	$query .= "where _IDclass = '$idclass' AND _IDcentre = '$idcentre' AND _name = '$name' AND _fname = '$fname' ";
	$query .= "limit 1";

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return (int) @$myrow[0];
}
//---------------------------------------------------------------------------
function get_type($ident)
{
// $ident, type d'évaluation

	require $_SESSION["ROOTDIR"]."/globals.php";

	$Query  = "select _IDtype from notes_type ";
	$Query .= "where _ident = '$ident' ";
	$Query .= "AND _lang = '".$_SESSION["lang"]."' ";
	$Query .= "limit 1";

	$return = mysqli_query($mysql_link, $Query);
	$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	return (int) @$myrow[0];
}
//---------------------------------------------------------------------------
function exist_data($year, $IDclass, $IDmat, $period)
{
// $year, année
// $IDclass, Identifiant de la classe
// $IDmat, Identifiant de la matière
// $period, trimestre

	require $_SESSION["ROOTDIR"]."/globals.php";

	// lecture des droits
	$Query  = "select _max, _text from notes ";
	$Query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// recherche de l'entête du tableau
	$Query  = "select _IDdata from notes_data ";
	$Query .= "where _year = '$year' AND _IDclass = '$IDclass' AND _IDmat = '$IDmat' AND _period = '$period' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if ( mysqli_num_rows($result) )
		return $row[0];

	$_type    = array_fill(0, "$auth[0]", "0");
	$_total   = array_fill(0, "$auth[0]", "$auth[1]");
	$_coef    = array_fill(0, "$auth[0]", "1");
	$_visible = array_fill(0, "$auth[0]", "O");

	$type = $total = $coef = $visible = "";
	for ($i = 0; $i < $auth[0]; $i++) {
		$type    .= $_type[$i].";";
		$total   .= $_total[$i].";";
		$coef    .= $_coef[$i].";";
		$visible .= $_visible[$i].";";
		}

	//---- nouveau bulletin élèves
	$Query  = "insert into notes_data ";
	$Query .= "values('', '$year', '$IDclass', '$IDmat', '$period', '$type', '$total', '$coef', '$visible', 'N', '0', '0', '')";

	mysqli_query($mysql_link, $Query);

	return mysqli_insert_id($mysql_link);
}
//---------------------------------------------------------------------------
function import_notes($IDcentre, $IDclass, $IDmat, $year, $file)
{
// $IDcentre, ID du centre
// $IDclass, Identifiant de la classe
// $IDmat, Identifiant de la matière
// $year, année
// $file, fichier source

	require $_SESSION["ROOTDIR"]."/globals.php";

	require_once $_SESSION["ROOTDIR"]."/lib/lib_import_xls.php";

	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP1251');
	$data->read($file["tmp_name"]);

	// initialisation
	$count  = $exist = 0;
	$date   = date("Y-m-d H:i:s");

	// lecture des colonnes
	$Query  = "select _max from notes ";
	$Query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$myrow  = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// parcours des onglets (1 onglet = 1 trimestre)
	for($sheet = 0; $sheet < count($data->sheets); $sheet++) {
			$type   = array();
			$max    = array();
			$coef   = array();

			$IDdata = exist_data($year, $IDclass, $IDmat, $sheet + 1);

			// on commence à l'indice 3 à cause du nom et du prénom
			for ($i = 3, $j = 0; strlen(trim(@$data->sheets[$sheet]['cells'][1][$i])); $i++, $j++) {
				$type[$j] = get_type(trim(@$data->sheets[$sheet]['cells'][1][$i]));
				$max[$j]  = trim(@$data->sheets[$sheet]['cells'][2][$i]);
				$coef[$j] = str_replace(",", ".", trim(@$data->sheets[$sheet]['cells'][3][$i]));
				}

			// modification du bulletin
			$_type = $_total = $_coef = "";
			for ($j = 0; $j < $myrow[0]; $j++) {
				$_type    .= ( @$type[$j] ) ? $type[$j].";" : "1;" ;
				$_total   .= ( @$max[$j] )  ? $max[$j].";"  : "20;" ;
				$_coef    .= ( @$coef[$j] ) ? $coef[$j].";" : "1;" ;
				}

			$Query  = "UPDATE notes_data ";
			$Query .= "SET _type = '$_type', _total = '$_total', _coef = '$_coef' ";
			$Query .= "where _IDdata = '$IDdata' ";
			$Query .= "limit 1";

			mysqli_query($mysql_link, $Query);

			// on commence à l'indice 2 à cause de l'en-tête dans la feuille de calcul
			for ($i = 4; $i <= $data->sheets[$sheet]['numRows']; $i++) {

				$name    = trim(addslashes(@$data->sheets[$sheet]['cells'][$i][1]));								// nom
				$fname   = trim(addslashes(@$data->sheets[$sheet]['cells'][$i][2]));								// prénom
				$IDeleve = get_student($IDclass, $IDcentre, $name, $fname);											// identifiant élève

				for ($j = 0; $j < sizeof($type); $j++) {
					$idx   = 3 + $j;
					$value = str_replace(",", ".", trim(addslashes(@$data->sheets[$sheet]['cells'][$i][$idx])));	// note

					if ( $value < 0 OR $value > $max[$j] OR !is_numeric($value) )
						$value = "";

					$Query  = "insert into notes_items ";
					$Query .= "values('', '$IDdata', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '$date', '$IDeleve', '$j', '$value')";

					if ( !mysqli_query($mysql_link, $Query) ) {
						// modification du bulletin
						$Query  = "UPDATE notes_items ";
						$Query .= "SET _ID = '".$_SESSION["CnxID"]."', _IP = '".$_SESSION["CnxIP"]."', _update = '$date', _value = '$value' ";
						$Query .= "where _IDdata = '$IDdata' AND _IDeleve = '$IDeleve' AND _index = '$j' ";
						$Query .= "limit 1";

						mysqli_query($mysql_link, $Query);
						}
					}	// endfor sizeof
				}	// endfor numrows
		}	// endfor sheet

	return $count;
}
//---------------------------------------------------------------------------
function reduceNoteSizeDecimal($note)
{
	$temp = explode('.', $note);
	if (isset($temp[1]) && $temp[1] != "")
	{
		return $temp[0].".".substr($temp[1], 0, 2);
	}
	else return $note;
}
?>
