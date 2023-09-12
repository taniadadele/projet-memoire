<?php
/***** Recherche absence veille ou Vendredi *****/
function getLastAbs($IDetu, $DateAbs)
{
	global $mysql_link;
	$date = $DateAbs;
	$today = date('Y-m-d', strtotime($DateAbs));

	if(date("N", strtotime($DateAbs)) == "1")
	{
		$now = date('Y-m-d', strtotime('-3 day', strtotime($DateAbs)));
	}
	else
	{
		$now = date('Y-m-d', strtotime('-1 day', strtotime($DateAbs)));
	}

	$queryabs  = "SELECT _IDitem ";
	$queryabs .= "FROM absent_items ";
	$queryabs .= "WHERE absent_items._IDabs = $IDetu ";
	$queryabs .= "AND (absent_items._start <= '$now 23:59:59' AND absent_items._end >= '$now 00:00:00') ";
	$queryabs .= "AND (absent_items._start NOT LIKE '$today%' AND absent_items._end NOT LIKE '$today%') ";
	$queryabs .= "AND absent_items._IDctn = '-1' ";

	$resultabs = mysqli_query($mysql_link, $queryabs);
	$rowabs    = ( $resultabs ) ? remove_magic_quotes(mysqli_fetch_row($resultabs)) : 0 ;
	$return = 0;

	while ($rowabs)
	{
		$return = $rowabs[0];
		$rowabs = remove_magic_quotes(mysqli_fetch_row($resultabs));
	}
	return $return;
}



// ---------------------------------------------------------------------------
// Fonction: Donne la raison d'une absence en fonction de l'ID_data
// IN:		   ID data (INT)
// OUT: 		 Raison de l'absence (TEXT)
// ---------------------------------------------------------------------------
function getReasonByID($IDdata)
{
	global $mysql_link;
	$query = "SELECT _texte FROM absent_data WHERE _IDdata = '".$IDdata."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	  return $row[0];
	}
}
?>
