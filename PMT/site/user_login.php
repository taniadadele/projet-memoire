<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007 by Dominique Laporte(C-E-D@wanadoo.fr)

   This file is part of PromÃ©thÃ©e.

   PromÃ©thÃ©e is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   PromÃ©thÃ©e is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PromÃ©thÃ©e.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/

/*
 *		module   : user_login.php
 *		projet   : la page de login
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 21/10/2019
 *							 Refonte du système de connexion pour inclure une utilisation de cookie
 */


if (isset($_POST['pathToGo']) && $_POST['pathToGo'] != "") $_SESSION['pathToGo'] = $_POST['pathToGo'];

// remarque : login automatique si les variables ID utilisateur $id et mot de passe $pwd sont renseignÃ©es (par l'URL)
$id  = trim(@$_GET["id"]);		// ID utilisateur
$pwd = trim(@$_GET["pwd"]);		// mot de passe

// login automatique Ã  partir de config.ini (mode portail)
if ( strlen($AUTOLOGIN) AND $id == "" AND $pwd == "" )
	list($lbl1, $id, $lbl2, $pwd) = preg_split("/[=&]/", $AUTOLOGIN);
?>

<?php require_once "user_ident.php"; ?>
