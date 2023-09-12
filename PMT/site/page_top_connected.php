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


/*
 *		module   : page_top.php
 *		projet   : le bandeau du haut avec le logo de l'établissement
 *
 *		version  : 1.2
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 20/03/03 - par D. Laporte
 *		           log de déconnexion
 *                     18/10/03 - par D.Laporte
 *                     mise en place des post-it
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */
?>


<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion <?php if (getUserParam($_SESSION['CnxID'], 'menuShowMode') !== null && getUserParam($_SESSION['CnxID'], 'menuShowMode') == 'closed') echo 'toggled'; ?> d-print-none" id="accordionSidebar">
	<!-- Sidebar - Brand -->
	<a class="sidebar-brand d-flex align-items-center justify-content-center toggled" href="index.php">
		<img src="download/logos/<?php echo getLogoDir($_SESSION['CfgIdent']); ?>/logo_square.png" title="" alt="Logo" class="sidebar-brand-logo-square" style="/* width: 100%; */">
		<img src="download/logos/<?php echo getLogoDir($_SESSION['CfgIdent']); ?>/logo_large.png" title="" alt="Logo" class="sidebar-brand-logo-large" style="/* width: 100%; padding: 15px; */">
	</a>

	<!-- Divider -->
	<hr class="sidebar-divider my-0">

	<!-- Nav Item - Dashboard -->
	<li class="nav-item <?php if (!isset($item) || $item == '') echo 'active'; ?>">
		<a class="nav-link" href="index.php">
			<i class="fas fa-fw fa-home"></i>
			<span>Accueil</span></a>
	</li>

	<!-- Divider -->
	<hr class="sidebar-divider">




	<?php
		require_once "page_menu.php";
		if (getUserParam($_SESSION['CnxID'], 'menuShowMode') !== null && getUserParam($_SESSION['CnxID'], 'menuShowMode') == 'closed') $showSelected = false;
		else $showSelected = true;

		getMenu($idmenu_global, $showSelected);
	?>


	<!-- Divider -->
	<hr class="sidebar-divider d-none d-md-block">

	<!-- Sidebar Toggler (Sidebar) -->
	<div class="text-center d-none d-md-inline">
		<button class="rounded-circle border-0" id="sidebarToggle"></button>
	</div>

</ul>
<!-- End of Sidebar -->






<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

	<!-- Main Content -->
	<div id="content">

		<!-- Topbar -->
		<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow d-print-none">

			<!-- Sidebar Toggle (Topbar) -->
			<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
				<i class="fa fa-bars"></i>
			</button>


			<!-- Top shortcuts -->
			<div class="btn-toolbar" role="toolbar">
			  <div class="btn-group mr-2" role="group">
			    <a href="index.php?item=64" class="btn btn-secondary"><i class="far fa-sm fa-calendar-alt"></i>&nbsp;Emploi du temps</a>
			  </div>

			  <div class="btn-group mr-2" role="group">
			    <a href="index.php?item=60" class="btn btn-secondary"><i class="fas fa-sm fa-user-graduate"></i>&nbsp;Notes</a>
			  </div>
			  <div class="btn-group mr-2" role="group">
			    <a href="index.php?item=13" class="btn btn-secondary"><i class="fas fa-sm fa-book-open"></i>&nbsp;Devoirs</a>
			  </div>

				<div class="btn-group" role="group">
			    <a href="index.php?item=28&idmenu=521" class="btn btn-secondary"><i class="far fa-sm fa-folder-open"></i>&nbsp;Fichiers</a>
			  </div>
			</div>



			<!-- Topbar Navbar -->
			<ul class="navbar-nav ml-auto">

				<!-- Nav Item - Search Dropdown (Visible Only XS) -->
				<li class="nav-item dropdown no-arrow d-sm-none">
					<a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-search fa-fw"></i>
					</a>
					<!-- Dropdown - Messages -->
					<div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
						<form class="form-inline mr-auto w-100 navbar-search">
							<div class="input-group">
								<input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
								<div class="input-group-append">
									<button class="btn btn-primary" type="button">
										<i class="fas fa-search fa-sm"></i>
									</button>
								</div>
							</div>
						</form>
					</div>
				</li>





				<!-- Nav Item - Alerts -->
				<li class="nav-item dropdown no-arrow mx-1">
					<a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-bell fa-fw"></i>
						<!-- Counter - Alerts -->
						<span class="badge badge-danger badge-counter" id="notif_count"></span>
					</a>
					<!-- Dropdown - Alerts -->
					<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
						<h6 class="dropdown-header">
							Notifications
						</h6>
						<?php require('include/fonction/notifications.php'); ?>
						<!-- <a class="dropdown-item d-flex align-items-center" href="#">
							<div class="mr-3">
								<div class="icon-circle bg-primary">
									<i class="fas fa-file-alt text-white"></i>
								</div>
							</div>
							<div>
								<div class="small text-gray-500">December 12, 2019</div>
								<span class="font-weight-bold">A new monthly report is ready to download!</span>
							</div>
						</a>
						<a class="dropdown-item d-flex align-items-center" href="#">
							<div class="mr-3">
								<div class="icon-circle bg-success">
									<i class="fas fa-donate text-white"></i>
								</div>
							</div>
							<div>
								<div class="small text-gray-500">December 7, 2019</div>
								$290.29 has been deposited into your account!
							</div>
						</a>
						<a class="dropdown-item d-flex align-items-center" href="#">
							<div class="mr-3">
								<div class="icon-circle bg-warning">
									<i class="fas fa-exclamation-triangle text-white"></i>
								</div>
							</div>
							<div>
								<div class="small text-gray-500">December 2, 2019</div>
								Spending Alert: We've noticed unusually high spending for your account.
							</div>
						</a> -->
						<!-- <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a> -->
					</div>
				</li>




				<!-- Nav Item - Messages -->
				<li class="nav-item dropdown no-arrow mx-1">
					<a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-envelope fa-fw"></i>
						<!-- Counter - Messages -->
						<span class="badge badge-danger badge-counter" id="new_message_counter"></span>
					</a>
					<!-- Dropdown - Messages -->
					<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
						<h6 class="dropdown-header" id="newMessagesPopupContentTitle">
							Messagerie
						</h6>


						<!-- <a class="dropdown-item d-flex align-items-center" href="#">
							<div class="dropdown-list-image mr-3">
								<img class="rounded-circle" src="https://source.unsplash.com/fn_BT9fwg_E/60x60" alt="">
								<div class="status-indicator bg-success"></div>
							</div>
							<div class="font-weight-bold">
								<div class="text-truncate">Hi there! I am wondering if you can help me with a problem I've been having.</div>
								<div class="small text-gray-500">Emily Fowler · 58m</div>
							</div>
						</a>


						<a class="dropdown-item d-flex align-items-center" href="#">
							<div class="dropdown-list-image mr-3">
								<img class="rounded-circle" src="https://source.unsplash.com/AU4VPcFN4LE/60x60" alt="">
								<div class="status-indicator"></div>
							</div>
							<div>
								<div class="text-truncate">I have the photos that you ordered last month, how would you like them sent to you?</div>
								<div class="small text-gray-500">Jae Chun · 1d</div>
							</div>
						</a>
						<a class="dropdown-item d-flex align-items-center" href="#">
							<div class="dropdown-list-image mr-3">
								<img class="rounded-circle" src="https://source.unsplash.com/CS2uCrpNzJY/60x60" alt="">
								<div class="status-indicator bg-warning"></div>
							</div>
							<div>
								<div class="text-truncate">Last month's report looks great, I am very happy with the progress so far, keep up the good work!</div>
								<div class="small text-gray-500">Morgan Alvarez · 2d</div>
							</div>
						</a>
						<a class="dropdown-item d-flex align-items-center" href="#">
							<div class="dropdown-list-image mr-3">
								<img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="">
								<div class="status-indicator bg-success"></div>
							</div>
							<div>
								<div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</div>
								<div class="small text-gray-500">Chicken the Dog · 2w</div>
							</div>
						</a> -->
						<a class="dropdown-item text-center small text-gray-500" href="index.php?item=4&idmenu=493">Voir tous les messages</a>
					</div>
				</li>

				<div class="topbar-divider d-none d-sm-block"></div>

				<!-- Nav Item - User Information -->
				<li class="nav-item dropdown no-arrow">
					<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<?php
							// On récupère l'image de profile
							// $photo = "ged_thumbnail.php?action=userImage&fileID=".base64_encode($_SESSION['CnxID']);
							$photo = getUserPictureLink();
						?>
						<span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo (getUserNameByID($_SESSION['CnxID'])); ?></span>
						<img class="img-profile rounded-circle" src="<?php echo $photo; ?>">
					</a>
					<!-- Dropdown - User Information -->
					<div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
						<a class="dropdown-item" href="index.php?item=1&cmde=account&show=0&ID=9999999999&idmenu=488">
							<i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
							Mon compte
						</a>
						<!-- <a class="dropdown-item" href="#">
							<i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
							Settings
						</a> -->
						<!-- <a class="dropdown-item" href="#">
							<i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
							Activity Log
						</a> -->
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
							<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
							Se déconnecter
						</a>
					</div>
				</li>

			</ul>

		</nav>
		<!-- End of Topbar -->

		<!-- Notifications toasts -->
		<div id="toasts_popup" aria-live="polite" last_notif="0" aria-atomic="true" style="max-height: calc(100vh - (4.375rem + 1.5rem) - 20px); position: absolute; overflow-y: scroll; width: 350px; /* min-height: 200px; */ right: 20px; z-index: 99999999;"></div>

		<!-- Begin Page Content -->
		<div class="container-fluid">
