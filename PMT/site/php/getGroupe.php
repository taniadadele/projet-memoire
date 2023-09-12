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
<option value="0"></option>
<?php
session_start();
include_once("dbconfig.php");
include_once("functions.php");

if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
{
	$IDclass = $_GET["IDclass"];
	$sy = $_GET["sy"];
	$ey = $_GET["ey"];
	$sw = $_GET["sw"];
	$ew = $_GET["ew"];
	$startday = $_GET["sd"];
	$endday = $_GET["ed"];

	$db = new DBConnection();
	$db->getConnection();


		$query  = "select distinctrow user_id._IDclass, campus_data._titre, groupe._IDmat, groupe._IDprof ";
		$query .= "from groupe, campus_classe, campus_data, user_id  ";
		$query .= "WHERE groupe._IDeleve = user_id._ID ";
		$query .= "AND groupe._IDmat = campus_data._IDmat ";
		$query .= "AND user_id._IDclass = $IDclass ";
		$query .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";
		$handle2 = mysqli_query($mysql_link, $query);

		while ($row2 = mysqli_fetch_object($handle2))
		{
			$txt_classe = "";
			foreach(explode(";", $row2->_IDclass) as $val)
			{
				if($val != "")
				{
					$query3  = "SELECT distinctrow campus_classe._ident ";
					$query3 .= "FROM campus_classe ";
					$query3 .= "WHERE campus_classe._IDclass = $val ";
					$handle3 = mysqli_query($mysql_link, $query3);
					while ($row3 = mysqli_fetch_object($handle3))
					{
						$txt_classe .= $row3->_ident." ";
					}
				}
			}

			$queryuser  = "select distinctrow _ID, _name, _fname ";
			$queryuser .= "from user_id ";
			$queryuser .= "WHERE _ID = ".$row2->_IDprof." ";
			$queryuser .= "order by _name";
			$handleuser = mysqli_query($mysql_link, $queryuser);
			while ($rowuser = mysqli_fetch_object($handleuser))
			{
				$txt_user = $rowuser->_name." ".$rowuser->_fname;
			}

			print("<option value=\";".$row2->_IDclass.";\" orgens=\"".$row2->_IDprof."\" orgmat=\"".$row2->_IDmat."\">".utf8_encode($txt_classe)." | ".utf8_encode($row2->_titre)." > ".utf8_encode($txt_user)."</option>");
		}
}

?>
