<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2020 by IP-Solutions(contact@ip-solutions.fr)

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
 *		module   : dbconfig.php
 *
 *		version  : 2.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    : Thomas Dazy (contact@thomasdazy.fr) - 08/09/2020 passage en PHP 7
 */

// Connexion à la db
$rootdir = str_replace('/php', '/', __DIR__);
$rootdir = str_replace('\php', '\\', $rootdir);
require_once($rootdir.'config.php');
require_once($rootdir."include/sqltools.php");
require_once($rootdir."include/class/loader.php");
if (!isset($mysql_link) || empty($mysql_link)) $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

// BDD
$db = new SafeMySQL(array(
	'host' 	=> $SERVER,
	'user'	=> $USER,
	'pass'	=> $PASSWD,
	'db'=> $DATABASE)
);
?>
