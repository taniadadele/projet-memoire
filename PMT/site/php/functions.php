<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2013 by IP-Solutions(contact@ip-solutions.fr)

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
 *		module   : functions.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */
date_default_timezone_set('GMT');


function js2PhpTime($jsdate){
  if(preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches)==1){
    $ret = mktime($matches[4], $matches[5], 0, $matches[2], $matches[1], $matches[3]);
    //echo $matches[4] ."-". $matches[5] ."-". 0  ."-". $matches[1] ."-". $matches[2] ."-". $matches[3];
  }else if(preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches)==1){
    $ret = mktime(0, 0, 0, $matches[2], $matches[1], $matches[3]);
    //echo 0 ."-". 0 ."-". 0 ."-". $matches[1] ."-". $matches[2] ."-". $matches[3];
  }
  return @$ret;
}

function php2JsTime($phpDate){
    //echo $phpDate;
    //return "/Date(" . $phpDate*1000 . ")/";
    return date("m/d/Y H:i", $phpDate);
}

function php2MySqlTime($phpDate){
    return date("Y-m-d H:i:s", $phpDate);
}

function mySql2PhpTime($sqlDate){
    $arr = date_parse($sqlDate);
    return mktime($arr["hour"],$arr["minute"],$arr["second"],$arr["month"],$arr["day"],$arr["year"]);

}

function DateCalendarToPmt($st, $et)
{
	$debut = js2PhpTime($st);
	$fin = js2PhpTime($et);
	$res = Array();

	// Jour
	$res["jour"] = intval(date("N", $debut)) - 1;

	// Année
	$res["annee"] = intval(date("Y", $debut));

	// Délais
	$start  = date('Y-m-d H:i:s', js2PhpTime($st));
	$end    = date('Y-m-d H:i:s', js2PhpTime($et));
	$d_start    = new DateTime($start);
	$d_end      = new DateTime($end);
	$diff = $d_start->diff($d_end);
	$duree = $diff->format('%H').":".$diff->format('%I');
	$res["delais"] = $duree;

	// Heure
	$heure = date("H", $debut);
	$minute = date("i", $debut);

	$heure = (intval($heure) - 8) * 2;

	if($minute == "30")
	{
		$heure++;
	}

	$res["heure"] = $heure;

	return $res;
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

/***** Mise a jour cellule classes absentes *****/
function setClassAbs($date, $texte)
{
	$query  = "SELECT _ID ";
	$query .= "FROM edt_modif ";
	$query .= "WHERE _zone = 'absclasse' ";
	$query .= "AND _date = '$date' ";
	$query .= "LIMIT 1 ";

	$result = mysqli_query($mysql_link, $query);
	$rows    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if($rows) // si donnée existantes
	{
		$query  = "UPDATE edt_modif SET _texte = '$texte' WHERE _ID = ".$rows[0];
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
	}
	else // sinon on crée
	{
		$query  = "INSERT INTO edt_modif VALUES('', 'absclasse', '0', '$date', '$texte') ";
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
	}
}

/***** Mise a jour cellule d'une zone *****/
function setZoneAbs($date, $texte, $IDclass)
{
	$query  = "SELECT _ID ";
	$query .= "FROM edt_modif ";
	$query .= "WHERE _zone = 'zone' ";
	$query .= "AND _IDclass = '$IDclass' ";
	$query .= "AND _date = '$date' ";
	$query .= "LIMIT 1 ";

	$result = mysqli_query($mysql_link, $query);
	$rows    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	if($rows) // si donnée existantes
	{
		$query  = "UPDATE edt_modif SET _texte = '$texte' WHERE _ID = ".$rows[0];
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
	}
	else // sinon on crée
	{
		$query  = "INSERT INTO edt_modif VALUES('', 'zone', '$IDclass', '$date', '$texte') ";
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
	}
}


function getStartAndEndDate($week, $year) {
  $dto = new DateTime();
  $dto->setISODate($year, $week);
  $ret['week_start'] = $dto->format('Y-m-d');
  $dto->modify('+6 days');
  $ret['week_end'] = $dto->format('Y-m-d');
  return $ret;
}

function getIDclassByID($idUser) {
  $query  = "SELECT _IDclass ";
  $query .= "FROM user_id ";
  $query .= "WHERE _ID = ".$idUser." ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    return $row[0];
  }

}

?>
