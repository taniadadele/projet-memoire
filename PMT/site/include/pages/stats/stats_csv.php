<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : stats_csv.php
 *		projet   : la page d'exportation des statistiques
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/01/07
 *		modif    : 
 */
?>


<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<?php require "page_banner.php"; ?>

<body style="background-color:#FFFFFF; margin:5px;">

<?php
	require "msg/stats.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/stats.php");

	$month  = (int) @$_GET["month"];

	$period = $TIMESTAT / (24 * 3600);
     	$today  = date("Y-m-d");

	$query  = "select _date, _ident, _IDgrp, _ID, _IP from stat_page ";
	$query .= ( $month ) ? "" : "where _date like '$today%' " ;
	$query .= "order by _date";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	// en-tête CSV
	$texte  = ( $month )
		? $msg->read($STATS_PERIOD, strval($period))
		: $msg->read($STATS_DAILY) ;

	print("<p>$texte</p>");
	print($msg->read($STATS_GROUP) ."<br/>");

	// données CSV
	while ( $row ) {
		// lecture service
		$start   = strpos($msg->read($STATS_STATLABEL), $row[1]) + strlen($row[1]) + 2; 
		$end     = strpos($msg->read($STATS_STATLABEL), ",", $start);
		$service = substr($msg->read($STATS_STATLABEL), $start, $end - $start);

		// lecture groupe
		$return  = mysqli_query($mysql_link, "select _ident from user_group where _IDgrp = '$row[2]' AND _lang = '".$_SESSION["lang"]."' limit 1");
		$myrow   = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

		print("$row[0], $service, $myrow[0], ".getUserNameByID($row[3], false).", "._getHostName($row[4], false)."<br/>");

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
?>

</body>
</html>
