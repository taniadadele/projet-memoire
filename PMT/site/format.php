<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : format.php
 *		projet   : page de présentation des différents formats disponibles des documents
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */
?>


<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<?php require "page_banner.php"; ?>

<body style="background-color:#FFFFFF; margin:5px;">

<?php
	require "msg/format.php";
	require_once "include/TMessage.php";

	$_msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/format.php");
?>

<table class="width100" style="border: 1px solid black">
	<tr>
	  <td class="align-center" style="width:20%;background-color:<?php print($_SESSION["CfgColor"]); ?>;">
		<span style=" color:#FFFFFF;"><?php print($_msg->read($FORMAT_ICON)); ?></span>
	  </td>
	  <td class="align-center" style="width:80%;background-color:<?php print($_SESSION["CfgColor"]); ?>;">
		<span style=" color:#FFFFFF;"><?php print($_msg->read($FORMAT_TYPE)); ?></span>
	  </td>
	</tr>
              
	<?php
		// affichage des formats de ressources
		$query  = "select _ext, _mime from config_mime ";
		$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
		$query .= "order by _mime";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
				
		while ( $row ) {
			print("
				<tr>
					<td class=\"align-center valign-top\">
						<img src=\"".$_SESSION["ROOTDIR"]."/images/mime/$row[0].gif\" title=\"$row[0]\" alt=\"$row[0]\" />
					</td>
					<td class=\"valign-top align-left\">$row[1] &nbsp;</td>
				</tr>
				");
      
			$row  = mysqli_fetch_row($result);
			}
	?>
</table>

<p style="text-align:center;">
[<a href="#" onclick="window.close(); return false;"><?php print($_msg->read($FORMAT_CLOSE)); ?></a>]
</p>

</body>
</html>