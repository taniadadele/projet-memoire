<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2003-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : rm.php
 *		projet   : efface un répertoire et son contenu
 *
 *		version  : 1.0
 *		auteur   : Marco
 *		creation : 23/05/05
 *		modif    :  
 */


//---------------------------------------------------------------------------
function rm($dirname)
{
	// Sanity check
	if ( !file_exists($dirname) )
		return false;

	// Simple delete for a file
	if ( is_file($dirname) )
		return unlink($dirname);

	// Loop through the folder
	$dir = dir($dirname);

	while ( ($entry = $dir->read()) != false ) {
		// Skip pointers
		if ( $entry == '.' || $entry == '..' )
			continue;

		// Recurse
		rm("$dirname/$entry");
		}

	// Clean up
	$dir->close();

	return rmdir($dirname);
}
//---------------------------------------------------------------------------
?>