<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2013 by IP-Solutions(contact@ip-solutions.fr)

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
 *		module   : dbconfig.devoir.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */
?>

<?php
class DBConnection{
	function getConnection(){
	include("config.php");
	  //change to your database server/user name/password
		mysqli_connect($SERVER, $USER, $PASSWD) or
         die("Could not connect: " . mysqli_error($mysql_link));
    //change to your database name
		mysqli_select_db($DATABASE) or
		     die("Could not select database: " . mysqli_error($mysql_link));
	}
}
?>
