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
?>

<?php
/*
 *		module   : smileys.php
 *		projet   : remplace les codes smileys par les images gif correspondantes
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 28/05/03
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


//---------------------------------------------------------------------------
function replace_smile($chaine)
{
/*
 * fonction :	remplace les codes smileys par les images gif correspondantes
 * in :		texte avec code smileys
 * out :		texte avec image gif des smileys
 */

	global $mysql_link;

	// on recherche les codes smiley pour les recencer
	$i = 0;
	for ( $token = strtok($chaine, "["); $token != ""; $token = strtok("[") ) {
		$pos = strpos($token, "]");
		if ( $pos )
			$found[$i++] = "[" . substr($token, 0, $pos + 1);
		}

	// on remplace les codes smiley
	$line = $chaine;
	for ( $j = 0; $j < $i; $j++ ) {
		$res   = mysqli_query($mysql_link, "select _ident from smileys where _code = '$found[$j]'");
		$smile = ( $res ) ? mysqli_fetch_row($res) : 0 ;

		if ( $smile )
			$line  = str_replace($found[$j], "<img src=\"".$_SESSION["ROOTDIR"]."/images/smiley/forum/$smile[0].gif\" title=\"\" alt=\"\" />", $line);
		}

	return $line;
}
//---------------------------------------------------------------------------
?>
