<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : forbidden.php
 *		projet   : la page de redirection en cas de problème
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 14/01/07
 *		modif    :
 */
?>



<!-- <!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> -->


<?php
	require "msg/forbidden.php";
	require_once "include/TMessage.php";

	$mylang = ( @$_GET["lang"] ) ? $_GET["lang"] : @$_SESSION["lang"] ;

	$msg   = new TMessage($_SESSION["ROOTDIR"]."/msg/$mylang/forbidden.php");
?>

<div class="text-center">
  <div class="error mx-auto" data-text="404">404</div>
  <p class="lead text-gray-800 mb-5"><?php echo $msg->read($FORBIDDEN_NOTEXIST); ?></p>
  <p class="text-gray-500 mb-0"><?php echo $msg->read($FORBIDDEN_CONTACT_ADMIN); ?></p>
  <a href="index.php"><i class="fas fa-arrow-left"></i>&nbsp;<?php echo $msg->read($FORBIDDEN_BACK_HOME); ?></a>
</div>
