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
 *		module   : humour.php
 *		projet   : affichage de la blague du jour et de la citation
 *
 *		version  : 2.0
 *		auteur   : laporte
 *		creation : 18/01/03
 *		modif    : 18/09/05 - D. Laporte
 *                     ajout des citations
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


$IDdata = @$_GET["IDdata"];
?>

<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<?php require "page_banner.php"; ?>

<body style="background-color:#FFFFFF; margin:5px;">

<?php
	require "msg/humour.php";
	require_once "include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/humour.php");

	require_once "include/nl2br.php";

	// combien y a-t-il de blagues à 2 balles dans le fichier
	mysqli_query($mysql_link, "select _IDitem from humour_items where _IDdata < '$IDdata' order by _IDitem asc");
	$total = mysqli_affected_rows($mysql_link);

	// combien y a-t-il de blagues à 2 balles dans le fichier
	mysqli_query($mysql_link, "select _IDitem from humour_items where _IDdata = '$IDdata'");

	// n° ID de la blague à 2 balles
	$item   = (date("z") + 1) % mysqli_affected_rows($mysql_link) + $total;

	// lecture de la blague à 2 balles
	$result = mysqli_query($mysql_link, "select _text from humour_items where _IDitem = '$item' AND _IDdata = '$IDdata'");
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// affichage de la blague à 2 balles : ouf !!
	// ou de la citation
	print("<p>".nltobr($row[0])."</p>");
?>

<p style="text-align:center;">
[<a href="#" onclick="window.close(); return false;"><?php print($msg->read($HUMOUR_CLOSEWINDOW)); ?></a>]
</p>

</body>
</html>