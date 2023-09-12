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
 *		module   : page_banner.php
 *		projet   : entête document html
 *
 *		version  : 2.0
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 12/01/03 - par D. Laporte
 *		           lecture du fichier de configuration de l'intranet
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


//---------------------------------------------------------------------------
// Déjà appelée sur index.php
// require_once "page_session.php";
//---------------------------------------------------------------------------
?>

<!-- Anciens Feuille impression -->
<!-- <link rel="stylesheet" type="text/css" media="print" href="css/print.css" /> -->

<!-- Anciens RSS -->
<!-- <link rel="alternate" type="application/rss+xml" href="<?php echo $_SESSION["ROOTDIR"]; ?>/rss.php" /> -->



<!DOCTYPE html>
<html lang="fr">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo $_SESSION["CfgTitle"]; ?>">
  <meta name="author" content="IP-Solutions">
	<meta name="keywords" content="<?php echo $row[20]; ?>">
	<meta name="rating" content="education">
	<meta name="revisit-after" content="10 days">

  <title><?php echo $_SESSION["CfgTitle"]; ?></title>

  <!-- Custom fonts for this template-->
	<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

	<!-- CSS Customs -->
	<link href="css/custom.css" rel="stylesheet" type="text/css">

	<!-- jQuery -->
	<script src="js/jQuery_3.5.1.js"></script>

	<!-- Favicon -->
	<link rel="icon" type="image/png" href="<?php echo $_SESSION["ROOTDIR"]; ?>/download/logos/<?php echo getLogoDir($_SESSION['CfgIdent']); ?>/favicon.png" />

  <!-- Style pour la customisation du thème (ex: couleurs, polices...) -->
  <?php if (file_exists($_SESSION["ROOTDIR"].'/download/logos/'.getLogoDir($_SESSION['CfgIdent']).'/theme_custom.css')) { ?>
    <link href="<?php echo $_SESSION["ROOTDIR"]; ?>/download/logos/<?php echo getLogoDir($_SESSION['CfgIdent']); ?>/theme_custom.css" rel="stylesheet">
  <?php } ?>


	<!-- Sweetalert2 (https://sweetalert2.github.io/) -->
	<script src="js/sweetalert2.min.js"></script>
	<script>
  	const Toast = Swal.mixin({
  	  toast: true,
  	  position: 'top-end',
  	  showConfirmButton: false,
  	  timer: 3000,
  	  timerProgressBar: true,
  	  onOpen: (toast) => {
  	    toast.addEventListener('mouseenter', Swal.stopTimer)
  	    toast.addEventListener('mouseleave', Swal.resumeTimer)
  	  }
  	});
	</script>



</head>

<body id="page-top" class="<?php if (!isUserConnected()) echo 'bg-gradient-primary'; ?>">
	<!-- Page Wrapper -->
  <div id="wrapper">
