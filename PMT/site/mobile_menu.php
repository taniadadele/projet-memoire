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
 *		module   : mobile_menu.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    : 25/07/29 -> Thomas Dazy -> Actualisation de la version mobile
 */


  $edt_link_active = $home_link_active = $note_link_active = $devoirs_link_active = "";
  if (isset($currentPage)) {
    switch ($currentPage) {
      case 'edt':
      case 'ctn':
        $edt_link_active = "active";
        break;
      case 'note':
        $note_link_active = "active";
        break;
      case 'devoirs':
        $devoirs_link_active = "active";
        break;

      default:
        $home_link_active = "active";
        break;
    }
  }
  else $home_link_active = "active";

?>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <?php echo $my_logo; ?>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav">
      <a class="nav-item nav-link <?php echo $home_link_active; ?>    " style="padding: .5rem 1rem;" href="mobile.php">Accueil</a>
      <a class="nav-item nav-link <?php echo $edt_link_active; ?>     " style="padding: .5rem 1rem;" href="mobile_edt.php">EDT</a>
      <?php if ($_SESSION['CnxGrp'] == 1) { ?>
        <a class="nav-item nav-link <?php echo $note_link_active; ?>    " style="padding: .5rem 1rem;" href="mobile_note.php">Notes</a>
        <a class="nav-item nav-link <?php echo $devoirs_link_active; ?> " style="padding: .5rem 1rem;" href="mobile_devoir.php">Devoirs</a>
      <?php } ?>
      <a class="nav-item nav-link alert alert-danger" style="border-radius: 5px; padding: .5rem 1rem; border-color: #dc3545 !important;" href="index_mobile.php?item=-1">Déconnexion</a>
    </div>
  </div>
</nav>
<div id="mainContent">
