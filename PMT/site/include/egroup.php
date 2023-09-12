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
 *		module   : egroup.php
 *		projet   : utilitaires e-groupes
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 12/09/09
 *		modif    : 
 */


//---------------------------------------------------------------------------
function getServiceNumber($string)
{
/*
 * fonction :	renvoi le n° du service d'une url
 * in :		$string, chaine de variables
 * out :		n° de service, 0 sinon
 */

	$list = explode("&", $string);

	for ($i = 0; $i < count($list); $i++) {
		@list($item, $value) = explode("=", $list[$i]);

		if ( $item == "item" )
			return (int) $value;
		}

	return 0;
}
//---------------------------------------------------------------------------
function isegroupsRegistered($IDdata)
{
/*
 * fonction :	test si un membre est déjà enregistré
 * in :		$IDdata : identifiant du egroupe
 * out :		-1000 si pas enregistré, le statut sinon
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	$query  = "select _access from egroup_user ";
	$query .= "where _IDdata = '$IDdata' ";
	$query .= "AND _ID = '".$_SESSION["CnxID"]."' ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	return ( $row ) ? $row[0] : -1000 ;
}
//---------------------------------------------------------------------------
?>
