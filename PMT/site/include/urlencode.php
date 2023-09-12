<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : urlencode.php
 *		projet   : formattage des url pour XHTML 1.0 strict
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 14/08/06
 *		modif    :
 */


//---------------------------------------------------------------------------
function stripaccent($string, $hexa = false)
{
/*
 * fonction :	remplace les caractères accentués dans une châine
 * in :		$string : chaîne de caractère accentuée, $hexa : transformation en code hexa
 * out :		chaîne de caractère sans accent
 */

	// les caractères de remplacement
	$accent = Array('à','ä','â','é','è','ë','ê','ï','î','ö','ô','ù','ü','û','ç');
	$filtre = ( $hexa )
		? Array('%E0','%E4','%E2','%E9','%E8','%EB','%EA','%EF','%EE','%F6','%F4','%F9','%FC','%FB','%E7')
		: Array('a','a','a','e','e','e','e','i','i','o','o','u','u','u','c') ;

	// renvoie de la nouvelle chaîne
	return str_replace($accent, $filtre, $string);
}
//---------------------------------------------------------------------------
function stripspecialcar($string)
{
/*
 * fonction :	remplace les caractères spéciaux dans une châine
 * in :		$string : chaîne de caractère
 * out :		chaîne de caractère valide
 */

	// les caractères de remplacement
	$special = Array('\'','"',' ','?','!','@','>','<');

	// renvoie de la nouvelle chaîne
	return str_replace($special, "_", $string);
}
//---------------------------------------------------------------------------
function myurlencode($url)
{
	// si le lien est une url externe valide => pas de modification
	return ( strncmp($url, "http://", 7) AND strncmp($url, "https://", 8) )
		? stripaccent( str_replace(Array(' ', '&'), Array('%20', '&amp;'), trim($url)) )
		: str_replace(Array(' ', '&'), Array('%20', '&amp;'), $url) ;
}
//---------------------------------------------------------------------------
function myurldecode($url)
{
	return str_replace(Array('%20', '&amp;'), Array(' ', '&'), $url);
}
//---------------------------------------------------------------------------
?>
