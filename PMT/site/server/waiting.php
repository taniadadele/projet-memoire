<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007-2008 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : waiting.php
 *		projet   : liste des plate-formes P2P en attente de validation
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 9/12/07
 *		modif    : 
 */

?>


<?php
function serverWaiting()
{
/*
 * fonction :	vérifie les plate-formes en atente
 * out :		nbr de plate-formes en attente
 */
	require $_SESSION["CFGDIR"]."/config.php";
	require $_SESSION["ROOTDIR"]."/globals.php";

	require $_SESSION["ROOTDIR"]."/msg/server.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/server.php");

	$query  = "select _IDdata from p2p_data ";
	$query .= "where _visible = 'N'";

	$result = mysqli_query($mysql_link, $query);
	$return = ( $result ) ? mysqli_affected_rows($mysql_link) : 0 ;

	$msg->isPlural = (bool) ( $return > 1 );

	if ( $_SESSION["CnxAdm"] == 255 AND $return ) {
		print("
			<div class=\"boxtitle\" style=\"padding:2px; text-align:center;\">
				". $msg->read($SERVER_WARNING) ."
			</div>
			");

		print("
		      <div class=\"boxcontent\" style=\"text-align:center; margin:1px; padding:2px; background-color:".$_SESSION["CfgBgcolor"]."\">
				". $msg->read($SERVER_WAITVALIDATION, Array(strval($return), "index.php?item=101")) ."<br/>
				<img src=\"".$_SESSION["ROOTDIR"]."/images/warning.png\" title=\"\" alt =\"\" />
		      </div>
			");

		print("<p style=\"margin-top: 0px; margin-bottom: $TBLSPACING px;\"></p>");
		}

	return $return;
}
?>
