<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2003-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : calendar_tools.php
 *		projet   : utilitaires de gestions de dates
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 9/11/03
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


//---------------------------------------------------------------------------
function isBissextile($year)
{
	/*
	 * fonction :	détermination d'une année bissextile
	 * in :		l'année
	 * out :		28 ou 29
	 */

	// Depuis l'instauration du calendrier grégorien :
	//   1. Les années divisibles par 4 sont bissextiles, pas les autres.
	//   2. Exception : les années divisibles par 100 ne sont pas bissextiles.
	//   3. Exception à l'exception (!) : les années divisibles par 400 sont bissextiles.

	// année multiple de 4 et les 2 derniers chiffres soient différents de 0
	// sauf si l'année est divisible par 400
	if ( (($year % 4) == 0 AND ($year % 100)) OR ($year % 400) == 0 )
		return 29;
	else
		return 28;
}
//---------------------------------------------------------------------------
function getmaxdays($year, $month)
{
	/*
	 * fonction :	détermination du nombre de jours dans un mois
	 * in :		l'année et le mois
	 * out :		31, 30, 29 ou 28
	 */

	switch ( $month ) {
		case "1" :
		case "3" :
		case "5" :
		case "7" :
		case "8" :
		case "10" :
		case "12" :
			return 31;

		case "4" :
		case "6" :
		case "9" :
		case "11" :
			return 30;

		default :
			return isBissextile($year);
		}
}
//---------------------------------------------------------------------------
function roundup($minute)
{
	/*
	 * fonction :	arrondi à 5 minutes
	 * in :		le temps en minute
	 * out :		la valeur arrondie si possible sinon la valeur initiale
	 */

	for ($i = 0; $i < 60; $i += 5)
		if ( $minute <= $i )
			return $i;

	return $minute;
}
//---------------------------------------------------------------------------
function format2date($date = "")
{
	/*
	 * fonction :	formattage de la date
	 * in :		date au format jj/mm/aaaa hh:mm
	 * out :	le nouveau format aaaa-mm-jj hh:mm:ss
	 */

	// test de conformité de la date
	if ( $date == "" )
		$date = date("d/m/Y H:i");
	else
		if ( strpos($date, ":") == "" )
			$date .= date(" H:i");

	// traitement
	$day   = substr($date, 0, strpos($date, "/"));
	$month = substr($date, strpos($date,  "/") + 1, strrpos($date, "/") - strpos($date, "/") - 1);
	$year  = substr($date, strrpos($date, "/") + 1, strpos($date, " ") - strrpos($date, "/") - 1);
	$hour  = substr($date, strpos($date,  " ") + 1, strpos($date, ":") - strpos($date, " ") - 1);
	$min   = substr($date, strpos($date,  ":") + 1, strlen($date) - strpos($date, ":") - 1);

	if ( strlen($year) == 2 )
		$year = "20" . $year;

	return "$year-$month-$day $hour:$min:00";
}
//---------------------------------------------------------------------------
function date2shortfmt($date)
{
	/*
	 * fonction :	formattage de la date
	 * in :		date au format aaaa-mm-jj hh:mm:ss
	 * out :	le nouveau format jj/mm/aaaa hh:mm
	 */

	// test de conformité de la date
	if ( $date == "" )
		$date = date("Y-m-d H:i:s");
	else
		if ( strpos($date, ":") == "" )
			$date .= date(" H:i:s");

	// traitement
	$year  = substr($date, 0, strpos($date, "-"));
	$month = substr($date, strpos($date,  "-") + 1, strrpos($date, "-") - strpos($date, "-") - 1);
	$day   = substr($date, strrpos($date, "-") + 1, strpos($date, " ") - strrpos($date, "-") - 1);
	$hour  = substr($date, strpos($date,  " ") + 1, strpos($date, ":") - strpos($date, " ") - 1);
	$min   = substr($date, strpos($date,  ":") + 1, strrpos($date, ":") - strpos($date, ":") - 1);

	if ( strlen($year) == 2 )
		$year = "20" . $year;

	return "$day/$month/$year $hour:$min";
}
//---------------------------------------------------------------------------
function date2longfmt($date = "", $format = "")
{
	/*
	 * fonction : formattage de la date
	 * in :       date au format aaaa-mm-jj hh:mm:ss
	 * out :      le nouveau format <jour> jj <mois> aaaa hh:mm
	 */

	require $_SESSION["ROOTDIR"]."/msg/calendar.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/calendar.php");

	$mois = explode(",", $msg->read($CALENDAR_MONTH));
	$jour = explode(",", $msg->read($CALENDAR_DAYSFULL));

	// test de conformité de la date
	if ( $date == "0000-00-00 00:00:00" )
		return "-";

	if ( $date == "" )
		$date = date("Y-m-d H:i:s");
	else
		if ( strpos($date, ":") == false )
			$date .= date(" H:i:s");

	// traitement
	$year  = substr($date, 0, strpos($date, "-"));
	$month = substr($date, strpos($date,  "-") + 1, strrpos($date, "-") - strpos($date, "-") - 1);
	$day   = substr($date, strrpos($date, "-") + 1, strpos($date, " ")  - strrpos($date, "-") - 1);
	$hour  = substr($date, strpos($date,  " ") + 1, strpos($date, ":")  - strpos($date, " ") - 1);
	$min   = substr($date, strpos($date,  ":") + 1, strrpos($date, ":") - strpos($date, ":") - 1);

	// 1er jour du mois
	$idx_m = (int) strval($month) - 1;
	$idx_d = date("w", mktime(1, 1, 1, $month, $day, $year));

	if ( strlen($year) == 2 )
		$year = "20" . $year;

	switch ( $format ) {
		case "hi" :
			return "$hour:$min";
		case "ma" :
			return "$mois[$idx_m] $year";
		case "jm" :
			return "$jour[$idx_d] $day $mois[$idx_m]";
		case "jma" :
			return "$jour[$idx_d] $day $mois[$idx_m] $year";
		default :
			return "$jour[$idx_d] $day $mois[$idx_m] $year ".$msg->read($CALENDAR_AT)." $hour:$min";
		}
}
//---------------------------------------------------------------------------
function CalendarPopup($iddiv, $form, $format = 1)
{
	require $_SESSION["ROOTDIR"]."/msg/calendar.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/calendar.php");

	print "<script type=\"text/javascript\" src=\"./script/CalendarPopup.js\"></script>\n";
	print "<script type=\"text/javascript\">document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type=\"text/javascript\" ID=\"js18\">\n";
	print "var cal18 = new CalendarPopup('$iddiv');\n";

	print "cal18.setDayHeaders(".$msg->read($CALENDAR_DAYSUK).");\n";
	print "cal18.setMonthNames('".str_replace(",", "','", $msg->read($CALENDAR_MONTHFULL))."');\n";
	print "cal18.setTodayText(\"".$msg->read($CALENDAR_TODAY)."\");\n";

	if ( $format == "1" ) {
		print "cal18.showNavigationDropdowns();\n";
		print "cal18.setYearSelectStartOffset(15);\n";
		}

	print "</script>\n";
	print "<a href=\"#\" onclick=\"cal18.select($form,'anchor18$iddiv','yyyy-MM-dd'); return false;\" name='anchor18$iddiv' id='anchor18$iddiv'><img src=\"".$_SESSION["ROOTDIR"]."/images/calendar.gif\" title=\"\" alt=\"\" /></a>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div id=\"$iddiv\" style=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\"></div>\n";
}
//---------------------------------------------------------------------------
?>
