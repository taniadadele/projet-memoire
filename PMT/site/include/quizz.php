<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2010 by Dominique Laporte(C-E-D@wanadoo.fr)

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
?>

<?php
/*
 *		module   : quizz.php
 *		projet   : fonctions utilitaires des quizz
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 27/02/10
 *		modif    : 
 */


//---------------------------------------------------------------------------
function import_quizz($file, $IDdata, $IDroot)
{
/*
 * fonction :	enregistre une réponse à une question dans un quizz
 * in :		$IDquizz, identifiant du quizz
 *			$IDtext, identifiant question
 *			$iditem, identifiant réponse
 *			$text, réponse
 *			$index, index de la réponse
 * out :		0 si erreur, 1 sinon
 */
	require $_SESSION["ROOTDIR"]."/globals.php";
	require_once $_SESSION["ROOTDIR"]."/lib/lib_import_xls.php";

	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP1251');
	$data->read($file);

	for ($j = 0; $j < sizeof($data->sheets); $j++) {
		// initialisation
		$count = $index = 0;

		// initialisation des champs
		$date   = date("Y-m-d H:i:s");
		$title  = trim(addslashes(@$data->sheets[$j]['cells'][1][4]));

		// groupes autorisés
		$grpid  = " " . $_SESSION["CnxGrp"] ." ";

		// et on insére un nouvel exercice dans la base de données
		$Query  = "insert into quizz ";
		$Query .= "values('', '$IDdata', '$IDroot', '$date', '".$_SESSION["CnxID"]."', '$grpid', '".$_SESSION["CnxIP"]."', '$title', '', '0', 'N', 'N', 'O', 'N', 'N', '', '', 'N')";

		$IDquizz = ( mysqli_query($mysql_link, $Query) )
			? mysqli_insert_id($mysql_link)
			: 0 ;

		// on commence à l'indice 2 à cause de l'en-tête dans la feuille de calcul
		for ($i = 2; $i <= $data->sheets[$j]['numRows']; $i++) {
			$type  = trim(@$data->sheets[$j]['cells'][$i][3]);

			if ( $type != "" ) {
				switch ( $type ) {
					case 'R' : $type = 0; break;
					case 'C' : $type = 1; break;
					case 'S' : $type = 2; break;
					default :  $type = 3; break;
					}

				$texte  = trim(addslashes(@$data->sheets[$j]['cells'][$i][4]));				// texte

				$query  = "insert into quizz_data values(";
				$query .= "'', ";
				$query .= "'$IDquizz', ";
				$query .= "'$texte', ";
				$query .= "'". trim(@$data->sheets[$j]['cells'][$i][5]) ."', ";				// image
				$query .= "'$type'";
				$query .= ")";

				if ( strlen($texte) )
					$iddata = ( mysqli_query($mysql_link, $query) )
						? mysqli_insert_id($mysql_link)
						: 0 ;

				$index  = 0;
				}
			else {
				$texte  = trim(addslashes(@$data->sheets[$j]['cells'][$i][4]));				// question

				$query  = "insert into quizz_items values(";
				$query .= "'$index', ";
				$query .= "'$iddata', ";
				$query .= "'$texte', ";
				$query .= "'". trim(@$data->sheets[$j]['cells'][$i][2]) ."'";				// point
				$query .= ")";

				if ( strlen($texte) )
					if ( mysqli_query($mysql_link, $query) ) {
						$index++;
						$count++;
						}
				}
			}
		}

	return $count;
}
//---------------------------------------------------------------------------
function random_quizz($IDquizz, $total = 0)
{
/*
 * fonction :	génère un quizz aléatoire
 * in :		$IDquizz, identifiant du quizz
 *			$total, nombre de questions
 * out :		les questions aléatoires
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// lecture du nombre de questions
	$res   = mysqli_query($mysql_link, "select _IDdata from quizz_data where _IDquizz = '$IDquizz'");
	$count = ( $res ) ? mysqli_affected_rows($mysql_link) : 0 ;

	$rand  = array();

	if ( $total AND count($rand) < $total ) {
		while ( count($rand) < $total ) {
			$r = mt_rand(1, $count);

			if ( !in_array($r, $rand) )
				$rand[] = $r;
			}
		}
	else
		for ($i = 1; $i <= $count; $i++)
			$rand[] = $i;

	@sort($rand);

	$return = "$rand[0]";
	for ($i = 1; $i < count($rand); $i++)
		$return .= ";$rand[$i]";

	return $return;
}
//---------------------------------------------------------------------------
function insert_quizz($IDquizz, $IDtext, $iditem, $text = "", $index = 0)
{
/*
 * fonction :	enregistre une réponse à une question dans un quizz
 * in :		$IDquizz, identifiant du quizz
 *			$IDtext, identifiant question
 *			$iditem, identifiant réponse
 *			$text, réponse
 *			$index, index de la réponse
 * out :		0 si erreur, 1 sinon
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// initialisation
	$date   = date("Y-m-d H:i:s");

	//----- on enregistre la réponse
	$Query  = "insert into quizz_vote ";
	$Query .= "values('$IDquizz', '$IDtext', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$index', '$iditem', '$text', '$date', '1')";

	return ( mysqli_query($mysql_link, $Query) );
}
//---------------------------------------------------------------------------
function update_quizz($IDquizz, $IDtext, $iditem, $text = "", $index = 0)
{
/*
 * fonction :	modifie une réponse à une question dans un quizz
 * in :		$IDquizz, identifiant du quizz
 *			$IDtext, identifiant question
 *			$iditem, identifiant réponse
 *			$text, réponse
 *			$index, index de la réponse
 * out :		0 si erreur, 1 sinon
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// initialisation
	$date   = date("Y-m-d H:i:s");

	//----- mise à jour de la réponse
	$Query  = "update quizz_vote ";
	$Query .= "set _IP = '".$_SESSION["CnxIP"]."', _Iditem = '$iditem', _text = '$text', _date = '$date', _hit = _hit + 1 ";
	$Query .= "where _IDquizz = '$IDquizz' AND _IDdata = '$IDtext' AND _ID = '".$_SESSION["CnxID"]."' AND _index = '$index' ";
	$Query .= "limit 1";

	if ( !mysqli_query($mysql_link, $Query) )
		sql_error($mysql_link);
}
//---------------------------------------------------------------------------
function compute_quizz($IDdata, $IDitem, $type = 0, $text = "")
{
/*
 * fonction :	calcule le nombre de points attribué à une question dans un quizz
 * in :		$IDdata, identifiant du quizz
 *			$IDitem, identifiant question
 *			$type, type de question (radio, liste, ...)
 *			$text, réponse
 * out :		0 si erreur, 1 sinon
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	switch ( $type ) {
		case 0 :
		case 1 :
			if ( $IDitem ) {
				for ($k = 0; pow(2, $k) <= $IDitem; $k++)
					if ( pow(2, $k) & $IDitem ) {
						$query   = "select _pts from quizz_items ";
						$query  .= "where _IDdata = '$IDdata' ";
						$query  .= "AND _IDitem = '$k' ";
						$query  .= "limit 1";

						$return  = mysqli_query($mysql_link, $query);
						$pts     = ( $return ) ? mysqli_fetch_row($return) : 0 ;

						return $pts[0];
						}
				}
			break;

		case 2 :
			// réponse de l'utilisateur
			$iditem = $IDitem - 1;

			$query  = "select _pts, _texte from quizz_items ";
			$query .= "where _IDdata = '$IDdata' AND _IDitem = '$iditem' ";
			$query .= "limit 1";

			$return = mysqli_query($mysql_link, $query);
			$pts    = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			return ( $pts[1] == $text )
				? (int) $pts[0]
				: 0 ;
			break;

		default :
			// réponse de l'utilisateur
			$iditem = $IDitem - 1;

			$query  = "select _pts, _texte from quizz_items ";
			$query .= "where _IDdata = '$IDdata' AND _IDitem = '$iditem' ";
			$query .= "limit 1";

			$return = mysqli_query($mysql_link, $query);
			$pts    = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			return ( strtolower(trim($pts[1])) == strtolower(trim($text)) )
				? (int) $pts[0]
				: 0 ;
			break;
		}

	return 0;
}
//---------------------------------------------------------------------------
?>