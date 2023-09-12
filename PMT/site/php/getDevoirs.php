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
 *		module   : getContenu.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */
?>

<?php
session_start();
include_once("dbconfig.php");
include_once("functions.php");
if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
{
	$IDx = $_GET["IDx"];
	$sd = $_GET["sd"];

	// $db = new DBConnection();
	// $db->getConnection();

	// Verification si quelque chose à faire
	$nbdev = 0;
	$textdev = "";
	//if($generique == "off")
	//{
		$sql2 = "select * from ctn_items where _type = 0 and _IDcours = ".$IDx;
		$handle2 = mysqli_query($mysql_link, $sql2);
		while ($row2 = mysqli_fetch_object($handle2))
		{
			$nbdev++;
			$textdev = $row2->_devoirs;
		}
	//}

	echo $textdev;
}


?>
