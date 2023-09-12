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
 *		module   : nl2br.php
 *		projet   : primitive php du même nom
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 18/12/03
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */
//---------------------------------------------------------------------------
function nltobr($text)
{
	/*
	 * fonction :	pour remédier au bug sur certains navigateurs
	 * in :		texte avec saut de ligne
	 * out :		texte formatté html
	 */

//	return str_replace("\n", "<br/>", $text);
	return str_replace("<br>", "<br/>", nl2br($text));
}
//---------------------------------------------------------------------------
?>