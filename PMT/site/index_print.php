<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : index.php
 *		projet   : affichage des pages en fonction du menu
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 4/10/02
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */

// début de session
session_start();
error_reporting(0);
//error_reporting(E_ALL);
ini_set('arg_separator.output', '&amp;');

// Vérification mobile
$user_agent = $_SERVER["HTTP_USER_AGENT"];
if(strpos($user_agent, "Android") || strpos($user_agent, "BlackBerry") || strpos($user_agent, "SymbianOS") || strpos($user_agent, "iPhone") || strpos($user_agent, "iPad") || strpos($user_agent, "Windows Phone"))
{
	header("Location: index_mobile.php");
}

// config
if(isset($_GET["config"]) && $_GET["config"] == "on")
{
	$_SESSION["config"] = "on";
}
else if(isset($_GET["config"]) && $_GET["config"] == "off")
{
	$_SESSION["config"] = "off";
}
else if(!isset($_SESSION["config"]))
{
	$_SESSION["config"] = "";
}

$IDgroupclass  = ( isset($_POST["IDgroupclass"]) ) ? $_POST["IDgroupclass"] : @$_SESSION["IDgroupclass"] ;

// variables d'environnement pour l'hébergement de sites
$_SESSION["CFGDIR"] = ( @$CFGDIR != "" ) ? $CFGDIR : "." ;
$_SESSION["ROOTDIR"] = ( @$ROOTDIR != "" ) ? $ROOTDIR : "." ;

//---- compteur des téléchargements ----//
if ( @$_SESSION["CnxID"] AND @$_SESSION["CnxIP"] AND @$_GET["file"] ) {
	// petite protection
	if ( strstr($_GET["file"], "../") OR strstr($_GET["file"], "config.php") ) {
		require_once "include/download.php";

		logDownloadError($_GET["file"]);
		}
	else {
		// lancement du téléchargement
		Header("Location: ".$_GET["file"]);

		require_once "include/download.php";

		// mise à jour des fichiers de log
		logDownloadFile(@$_GET["fname"] ? $_GET["fname"] : $_GET["file"]);
		if ( @$_GET["tmp"] )
			logTmpFile($_GET["file"]);

		exit;
		}
	}
else {
	// suppression des fichiers temporaires
	require_once "include/download.php";

	deleteTmpFile($TIMETMP);
	}

$item = ( @$_GET["item"] )		// item des menus
	? (int) $_GET["item"]
	: (int) @$_POST["item"] ;

$cmde = ( @$_POST["cmde"] )		// option sur les items du menu
	? $_POST["cmde"]
	: @$_GET["cmde"] ;


require("page_session.php");
?>


<!DOCTYPE html>
<?php
//	require_once "page_session.php";

	// lecture de la configuration
	require "page_banner.php";

	// les pages par défaut
	$page        = "forbidden.php";
	$page_left   = "page_gauche.php";
	$page_right  = "page_droit.php";

//	$width_left  = "15%";
//	$width_right = "15%";

	$width_right = "0px";

	$force_left  = false;
	$force_right = true;

	$errconnect  = "";

	// vérification de la session
	if ( @$_SESSION["sessID"] )
		$_SESSION["sessID"] = updateSessionID($_SESSION["sessID"], @$_GET["ghost"]);

	if ( @$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]) )
	{
		$width_left  = "345px";
	}
	else
	{
		$width_left  = "0px";
	}

	//---- l'utilisateur est identifié
	if ( @$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]) ) {

		// vérification de l'adresse IP
		if ( @$_SESSION["CnxIP"] ) {

			// dehors les bannis !
			if ( $_SESSION["CnxAdm"] ) {
				// si la longueur minimale du mot de passe autorisée est incorrecte
				// et que les utilisateurs ne sont pas des Anonymes
				// => on oblige les utilisateurs à saisir un mot de passe
				if ( $USERPWD > strlen($_SESSION["CnxPasswd"]) AND $_SESSION["CnxSex"] != "A" ) {
					$item = 1;
					$cmde = "account";
					}

				switch ( $item ) {
					case '-1' :	// déconnexion
						// déconnexion CAS :
						$_SESSION["logout"] = true;

						// log de déconnexion
						updateSessionID($_SESSION["sessID"]);

						$lastaction = date("Y-m-d H:i:s");

						if ( !mysqli_query($mysql_link, "insert into stat_log values('$lastaction', '".$_SESSION["CnxID"]."', '', '".@$_SERVER["REMOTE_ADDR"]."', 'D')") )
							sql_error($mysql_link);

						// on efface la session (sécurisation / browser)
						$_SESSION["sessID"]     = eraseSessionID($_SESSION["sessID"]);

						$_SESSION["CnxID"]      = "";		// ID utilisateur
						$_SESSION["CnxIP"]      = "";		// @IP de l'utilisateur
						$_SESSION["CnxAdm"]     = "";		// Droits de connexion de l'utilisateur
						$_SESSION["CnxName"]    = "";		// Nom de connexion de l'utilisateur
						$_SESSION["CnxGrp"]     = "";		// Groupe de connexion de l'utilisateur
						$_SESSION["CnxSex"]     = "";		// Sexe de l'utilisateur (A pour une connexion Anonyme)
						$_SESSION["CnxSign"]    = "";		// Signature des mails
						$_SESSION["CnxPers"]    = "";		// connexion persistante pour l'utilisateur
						$_SESSION["CnxCentre"]  = "";		// centre de formation
						$_SESSION["CnxPasswd"]  = "";		// mot de passe (vérification si vide)
						$_SESSION["CampusName"] = "";		// le e-campus
						$_SESSION["CnxClass"]   = "";		// classe de l'élève
						$_SESSION["egroup"]     = "";

						$page_left = $page_right = "";

						$page = "user_login.php";
						break;

					case '1' :	// gestion des connexions
						switch ( $cmde ) {
							case 'account' :	// compte utilisateur
								// accès interdit aux anonymes
								if ( $_SESSION["CnxSex"] != "A" )
									$page = "user_$cmde.htm";
								break;
							case 'acl' :	// gestion des ACL
								// accès réservé au personnel
								if ( getAccess() == 2 )
									$page = "user_$cmde.htm";
								break;
							case 'new' :	// accès réservé au Big Chef (sauf si droit accordé aux utilisateurs)
								list($iscreat, $nil) = explode(":", $AUTHUSER);

								if ( $_SESSION["CnxAdm"] == 255 OR $iscreat )
									$page = "user_$cmde.htm";
								break;
							case 'passwd' :	// gestion mots de passe
							case 'gestion' :	// gestion des utilisateurs
								// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "user_$cmde.htm";
								break;
							case 'skin' :	// pesonnalisation
							case 'lost' :	// mot de passe perdu
								$page = "user_$cmde.htm";
								break;
							default :		// liste des utilisateurs
								$page = "user_visu.php";
								break;
							}
						break;
					case '2' :	// accès réservé au Big Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							switch ( $cmde ) {
								case 'gestion' :	// gestion du menu principal
								case 'submenu' :	// gestion des sous menus
									$page = "page_$cmde.htm";
									break;
								default :
									$page = "page_menu.htm";
									break;
								}
						break;
					case '3' :	// forum
						switch ( $cmde ) {
							// on filtre par mesure de sécurité
							case 'charte' :	// la charte des forums
							case 'post'   :	// envoi d'un post dans un forum
							case 'show'  :	// visualisation du message d'un forum
							case 'visu'  :	// liste des messages dans un forum
								$page = "forum_$cmde.htm";
								break;
							case 'moderer' :	// accès réservé aux modérateurs
							case 'new' :
								if ( $_SESSION["CnxAdm"] & 4 )
									$page = "forum_$cmde.htm";
								break;
							case 'newdir' :
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "forum_$cmde.htm";
								break;
							default :		// liste des forums
								$page = "forum.htm";
								break;
							}
						break;
					case '4' :	// messagerie instantannée (post-it)
						// accès interdit aux connexions anonymes
						if ( $_SESSION["CnxSex"] != "A" )
							switch ( $cmde ) {
								case 'gestion' :	// réservé au big chef
									if ( $_SESSION["CnxAdm"] == 255 )
										$page = "postit_$cmde.htm";
									break;
								case 'post' :	// création d'un nouveau post-it
								case 'visu' :	// visualisation des post-it
								case 'search' :	// recherche d'un message dans les post-it
								case 'lidi' :	// pour gestion des listes de diffusion
									$page = "postit_$cmde.htm";
									break;
								default :
									$page = "postit.php";
									break;
								}
						break;
					case '5' :	// galeries
						switch ( $cmde ) {
							// on filtre par mesure de sécurité
							case 'theme' :	// création d'un nouveau thème
							case 'new' :	// création d'une nouvelle galerie
							case 'visu' :	// visualisation des vignettes de la galerie
							case 'show' :	// visualisation des images de la galerie
							case 'upload' :	// upload d'images dans une galerie
							case 'search' :	// recherche dans les galeries
							case 'gestion' :
								$page = "gallery_$cmde.htm";
								break;
							case 'newdir' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "gallery_$cmde.htm";
								break;
							default :
								$page = "gallery.htm";
								break;
							}
						break;
					case '6' :	// Publications par Internet
						switch ( $cmde ) {
							case 'visu' :		// visualisation des articles de la publi
							case 'post' :		// création d'un nouvel article
								$force_right = true;
								$width_right = "25%";
								$page_right  = ( $cmde == "visu" ) ? "spip_content.htm" : "spip_edit.htm" ;
							case 'new' :		// création d'une nouvelle publi
							case 'add' :		// création d'une dépêche
							case 'addlink' :		// ajout d'un lien sur une dépêche
							case 'search' :		// recherche dans les publis
								$page = "spip_$cmde.htm";
								break;
							case 'newdir' :		// les répertoires des publis
							case 'gestion' :		// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "spip_$cmde.htm";
								break;
							default :
								$page = "spip.htm";
								break;
							}
						break;
					case '7' :	// statistiques
						switch ( $cmde ) {
							case 'dba' :	// liste des bases de données installées
							case 'items' :	// liste des articles des auteurs
								$page = "stats_$cmde.htm";
								break;
							default :
								$page = "stats.htm";
								break;
							}
						break;
					case '8' :	// agenda
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "agenda_$cmde.php";
								break;
							case 'new' :	// création d'une nouvel agenda
							case 'post' :	// enregistrement d'une annonce dans un agenda
							case 'search' :	// recherche dans les agendas
							case 'tuning' :	// paramétrage des agendas personnels
								$page = "agenda_$cmde.htm";
								break;
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "agenda_gestion.htm";
								break;
							default :
								$page = "agenda.htm";
								break;
							}
						break;
					case '9' :	// accès au campus virtuel
						switch ( $cmde ) {
							case 'user' :
							case 'tuning' :
							case 'newdir' :
							case 'gestion' :	// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "campus_$cmde.htm";
								break;
							case 'class' :
							case 'visu' :
							case 'breve' :
							case 'work' :
							case 'upload' :
							case 'download' :
							case 'link' :
							case 'addlink' :
							case 'cursus' :
							case 'addcursus' :
								$page = "campus_$cmde.htm";
								break;
							default :
								$page = "campus_visu.php";
								break;
							}
						break;
					case '10' :	// réservations
						// accès réservé au personnel de l'établissement
						if ( getAccess() == 2 )
							switch ( $cmde ) {
								case 'gestion' :	// accès réservé au Big Chef
									if ( $_SESSION["CnxAdm"] == 255 )
										$page = "reservation_$cmde.htm";
									break;
								case 'new' :	// ajout ressource
								case 'post' :	// effectuer une demande de réservation
									$page = "reservation_$cmde.htm";
									break;
								default :		// accès aux réservations
									$page = "reservation.htm";
									break;
								}
						break;
					case '11' :	// messagerie externe
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "email_$cmde.htm";
								break;
							case 'new' :		// ajouter un contact
							case 'lidi' :		// annuaire
							case 'address' :		// carnet d'adresse
								$page = "email_$cmde.htm";
								break;
							default :
								$page = "email.php";
								break;
							}
						break;
					case '12' :	// cahier de liaison
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "liaison_$cmde.php";
								break;
							case 'post' :
							case 'gestion' :		// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "liaison_$cmde.htm";
								break;
							case 'show' :
								$page = "liaison_$cmde.htm";
								break;
							default :
								$page = "liaison.htm";
								break;
							}
						break;
					case '13' :	// cahier de texte numérique (CTN)
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "ctn_$cmde.php";
								break;
							case 'user' :
							case 'post' :
							case 'post_add' :
							case 'gestion' :		// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "ctn_$cmde.htm";
								break;
							case 'show' :
							case 'agenda' :
								$page = "ctn_$cmde.htm";
								break;
							default :
								$page = "ctn.php";
								break;
							}
						break;
					case '113' :	// Devoirs V12
								$page = "ctn_v12.htm";
						break;
					case '14' :	// chat
						switch ( $cmde ) {
							case 'gestion' :		// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "chat_$cmde.htm";
								break;
							default :
								$page = "chat.htm";
								break;
							}
						break;
					case '15' :	// FIL d'informations en continue
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "fil_$cmde.htm";
								break;
							case 'post' :		// saisie d'un fil
								// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "fil_$cmde.htm";
								break;
							case 'note' :		// commentaires des annonces
							case 'visu' :		// annonces importantes
							case 'wall' :		// mur d'annonces pour les e-groupes
								$page = "fil_$cmde.htm";
								break;
							default :
								$page = "fil.htm";
								break;
							}
						break;
					case '16' :	// e-portfolio
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "pfolio_$cmde.php";
								break;
							case 'new' :
							case 'gestion' :	// accès réservé au Big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "pfolio_$cmde.htm";
								break;
							case 'stat' :
							case 'visu' :
								$page = "pfolio_$cmde.htm";
								break;
							default :
								$page = "pfolio.htm";
								break;
							}
						break;
					case '17' :	// e-groupes
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "egroup_$cmde.php";
								break;
							case 'gestion' :	// accès réservé au Big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "egroup_$cmde.htm";
								break;
							case 'new' :
							case 'user' :
							case 'show' :
							case 'visu' :
								$page = "egroup_$cmde.htm";
								break;
							default :
								$page = "egroup.htm";
								break;
							}
						break;
					case '18' :	// message du jour
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "motd_$cmde.htm";
								break;
							case 'post' :	// envoi message
							case 'visu' :	// visualisation message
								$page = "motd_$cmde.htm";
								break;
							default :		// liste des messages envoyé
								$page = "motd.htm";
								break;
							}
						break;
					case '19' :	// saisie des contenus d'information
						switch ( $cmde ) {
							case 'post' :	// saisie des contenus
								$page = "cms_$cmde.htm";
								break;
							case 'gestion' :	// gestion des contenus
								// accès réservé aux modérateurs ou aux gestionnaires
								if ( $_SESSION["CnxAdm"] & (4+8) )
									$page = "cms_$cmde.htm";
								break;
							default :		// visualisation des contenus
								$page = "cms.htm";
								break;
							}
						break;
					case '20' :	// saisie des flash infos
						// accès réservé aux modérateurs ou aux gestionnaires
						if ( $_SESSION["CnxAdm"] & (4+8) )
							switch ( $cmde ) {
								case 'post' :	// saisie des flash infos
								case 'gestion' :	// gestion des flash infos
									$page = "flash_$cmde.htm";
									break;
								default :		// flash info Prométhée
									$page = "flash_promethee.htm";
									break;
								}
						break;
					case '21' :	// configuration intranet
						switch ( $cmde ) {
							case 'p2p' :	// gestion du partage des ressources
							case 'dba' :	// gestion de la base de données
							case 'logo' :	// gestion des bandeaux
							case 'skin' :	// gestion des revêtements
							case 'menu' :	// gestion des menus
							case 'menuordre' :	// gestion ordre des menus
							case 'kwords' :	// gestion des mot-clefs
							case 'edt' :	// gestion de l'edt
							case 'access' :	// gestion des accès
							case 'usrmenu' :	// gestion des menus utilisateurs
								// accès réservé au Grand Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "config_$cmde.htm";
								break;
							case 'submenu' :	// gestion des sous menus
								// accès réservé au Grand Chef ou aux gestionnaires
								if ( $_SESSION["CnxAdm"] == 255 OR ($_SESSION["CnxAdm"] & 8) )
									$page = "config_$cmde.htm";
								break;
							default :		// config générale
								// accès réservé au Grand Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "config_ent.php";
								break;
							}
						break;
					case '22' :	// gestion des liens
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "link_$cmde.htm";
								break;
							case 'new' :	// nouveaux liens
								$page = "link_$cmde.htm";
								break;
							default :
								$page = "link.htm";
								break;
							}
						break;
					case '24' :	// gestion des IP en liste brûlée
						// accès réservé au Big Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							$page = "log_ip.php";
						break;
					case '25' :	// webmail
						// accès réservé au Big Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							switch ( $cmde ) {
								case 'post' :
									$page = "webmail_$cmde.htm";
									break;
								default :
									$page = "webmail.htm";
									break;
								}
						break;
					case '26' :	// filtrage IP
						// accès réservé au Big Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							switch ( $cmde ) {
								case 'filter' :
									$page = "ip_$cmde.htm";
									break;
								default :
									break;
								}
						break;
					case '27' :	// accès réservé au Big Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							switch ( $cmde ) {
								case 'backup' :	// backup/restore
								case 'import' :	// import des données
								case 'reset' :	// RAZ des tables
								case 'csv' :	// export logs d'erreurs
									$page = "admin_$cmde.htm";
									break;
								default :
									break;
								}
						break;
					case '28' :	// GED - Mes fichiers
								$page = "ged_files.php";
						break;
					case '30' :	// gestion des infos défilantes
						// accès réservé au Big Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							$page = "marquee_gestion.htm";
						break;
					case '31' :	// ressources pédagos & administratives
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "resource_$cmde.htm";
								break;
							case 'new' :	// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "resource_$cmde.htm";
								break;
							case 'newdir' :
							case 'upload' :
							case 'post' :
							case 'visu' :
							case 'bag' :
							case 'online' :	// ressources en ligne
								$page = "resource_$cmde.htm";
								break;
							default :
								$page = "resource.htm";
								break;
							}
						break;
					case '32' :	// ressources ftp
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "ftp_$cmde.htm";
								break;
							default :
								$page = "resource_ftp.htm";
								break;
							}
						break;
					case '33' :	// cours en ligne
						switch ( $cmde ) {
							case 'show' :	// suivi par élève
								$page = "uc.htm";
								break;
							case 'new' :
							case 'tuning' :
							case 'monitor' :
							case 'gestion' :	// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "cours_$cmde.htm";
								break;
							case 'visu' :
								$page = "cours_$cmde.htm";
								break;
							default :
								$page = "cours.htm";
								break;
							}
						break;
					case '34' :	// documents collaboratifs (wiki)
						switch ( $cmde ) {
							case 'gestion' :	// backoffice
							case 'visu' :	// visualisation
								$page = "wiki_$cmde.htm";
								break;
							default :		// liste des documents
								$page = "wiki.htm";
								break;
							}
						break;
					case '35' :	// liste des référentiels
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "cursus_$cmde.htm";
								break;
							case 'new' :	// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "cursus_$cmde.htm";
								break;
							default :
								$page = "cursus.htm";
								break;
							}
						break;
					case '38' :	// gestion des listes élèves
						switch ( $cmde ) {
							case 'gestion' :	// gestion des élèves
								// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "student_$cmde.htm";
								break;
							case 'account' :	// accès réservé au Gestionnaire
								if ( $_SESSION["CnxAdm"] & 8 )
									$page = "student_$cmde.htm";
								break;
							case 'parent' :	// liste des parents
								$page = "student_$cmde.htm";
								break;
							case 'groupe' :	// liste des groupes
								$page = "student_$cmde.htm";
								break;
							default :		// liste des élèves
								$page = "student_visu.php";
								break;
							}
						break;
					case '39' :	// gestion des incidents
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "support_$cmde.htm";
								break;
							case 'visu' :
							case 'post' :
								$page = "support_$cmde.htm";
								break;
							default :
								$page = "support.htm";
								break;
							}
						break;
					case '40' :	// gestion des stages
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "stage_$cmde.php";
								break;
							// on filtre par mesure de sécurité
							case 'gestion' :	// gestion du menu des stages
								// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "stage_gestion.htm";
								break;
							case 'post' :	// dépôts de CV
							case 'new' :	// dépôts offre emploi
							case 'demande' :	// visualisation des CV
							case 'offre' :	// visualisation des offres
							case 'cv' :		// liste CV
							case 'pro' :	// liste offres stage, emploi, collaboration
							case 'visu' :	// visualisation de recherche d'un stage
							case 'search' :	// recherche d'un stage
							case 'find' :	// résultat de recherche d'un stage
								$page = "stage_$cmde.htm";
								break;
							case 'link' :	// fiche de liaison
							case 'visit' :	// visites de stage
								// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "stage_$cmde.htm";
								break;
							default :
								// accès réservé au personnel de l'établissement
								if ( getAccess() == 2 )
									$page = "stage_lieu.htm";
								break;
							}
						break;
					case '45' :	// Espace de Travail Partagé
						switch ( $cmde ) {
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "etp_gestion.htm";
								break;
							case 'visu' :
							case 'tuning' :
							case 'access' :
								$page = "etp_$cmde.htm";
								break;
							default :
								$page = "etp.htm";
								break;
							}
						break;
					case '50' :	// accès au quizz
						switch ( $cmde ) {
							// on filtre par mesure de sécurité
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "quizz_gestion.htm";
								break;
							case 'visu' :
							case 'show' :
							case 'new' :
							case 'newdir' :
							case 'upload' :
							case 'download' :
								$page = "quizz_$cmde.htm";
								break;
							default :
								$page = "quizz.htm";
								break;
							}
						break;
					case '60' :	// bulletins de notes
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "notes_$cmde.php";
								break;
							case 'show' :
							case 'view' :
							case 'visu' :
							case 'post' :	// accès réservé au personnel
								//if ( getAccess() == 2 )
									$page = "notes_$cmde.htm";
							case 'gestion' :	// accès réservé au Big Chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "notes_$cmde.htm";
								break;
							default :
								$page = "notes.php";
								break;
							}
						break;
					case '61' :	// notes CCF
						$page = "uc.htm";
						break;
					case '62' :	// résultats examens
						switch ( $cmde ) {
							case 'gestion' :	// gestion des résultats
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "exam_$cmde.htm";
							case 'new' :	// saisie des résultats
								if ( getAccess() == 2 )
									$page = "exam_$cmde.htm";
								break;
							case 'visu' :	// visualisation des résultats
								$page = "exam_$cmde.htm";
								break;
							default :		// visualisation des résultats
								$page = "exam.htm";
								break;
							}
						break;
					case '63' :	// abscences
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "absent_$cmde.php";
								break;
							case 'visu' :
							case 'show' :
							case 'new' :
							case 'print' :
							case 'total'  :
								$page = "absent_$cmde.htm";
								break;
							case 'sick' :	// résevé au personnel
								if ( getAccess() == 2 )
									$page = "absent_$cmde.htm";
							case 'gestion' :	// résevé au big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "absent_$cmde.htm";
								break;
							default :
								break;
							}
						break;
					case '64' :	// emplois du temps
						switch ( $cmde ) {
							case 'post'  :
							case 'gestion' :	// interdit aux élèves
								if ( getAccess() == 2 )
									$page = "edt_$cmde.htm";
								break;
							default :
								$page = "edt.php";
								break;
							}
						break;
					case '65' :	// boîte à idées
						switch ( $cmde ) {
							case 'gestion' :	// réservé au big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "suggest_$cmde.htm";
								break;
							default :
								break;
							}
						break;
					case '66' :	// Carnet à points
						switch ( $cmde ) {
							case 'mvc' :	// modèle vue controleur
								require "carnet_$cmde.php";
								break;
							case 'gestion' :	// réservé au big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "carnet_$cmde.htm";
								break;
							case 'post'  :	// interdit aux élèves
								if ( getAccess() == 2 )
									$page = "carnet_$cmde.htm";
								break;
							case 'visu'  :	// le détail par élève
								$page = "carnet_$cmde.htm";
								break;
							default :
								$page = "carnet.htm";
								break;
							}
						break;
					case '67' :	// Consignes
						switch ( $cmde ) {
							case 'gestion' :	// réservé au big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "retenu_$cmde.htm";
								break;
							case 'post'  :	// interdit aux élèves
								if ( getAccess() == 2 )
									$page = "retenu_$cmde.htm";
								break;
							case 'visu'  :	// le détail par élève
								$page = "retenu_$cmde.htm";
								break;
							default :
								$page = "retenu.htm";
								break;
							}
						break;
					case '68' :	// Correspondances
						switch ( $cmde ) {
							case 'gestion' :	// réservé au big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "ccn_$cmde.htm";
								break;
							case 'post'  :	// message
								$page = "ccn_$cmde.htm";
								break;
							case 'visu'  :	// le détail par élève
								$page = "ccn_$cmde.htm";
								break;
							default :		// liste des ccn
								$page = "ccn.htm";
								break;
							}
						break;
					case '91' :	// moteur de recherche
						switch ( $cmde ) {
							case 'find'   :
							case 'search' :	// commandes de recherche
								$page = "page_$cmde.htm";
								break;
							default :
								break;
							}
						break;
					case '92' :	// logs de connexion
						// accès réservé au Grand Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							switch ( $cmde ) {
								case 'csv' :	// export csv
									$page = "log_$cmde.htm";
									break;
								default :
									$page = "log.php";
									break;
								}
						break;
					case '99' :	// sondage
						switch ( $cmde ) {
							case 'visu' :
								$page = "vote_$cmde.htm";
								break;
							case 'new' :
							case 'del' :
							case 'open' :
							case 'close' :
							case 'gestion' :	// gestion des sondages
								$page = "vote_gestion.htm";
								break;
							default :		// vote + liste des sondages
								$page = "vote.htm";
								break;
							}
						break;
					case '100' :	// demande d'identification (autologin)
						$page = "user_ident.php";
						break;
					case '101' :	// validation des plate-formes P2P
						// accès réservé au Grand Chef
						if ( $_SESSION["CnxAdm"] == 255 )
							$page = "server/valid.htm";
						break;
					case '111' :	// Roundcube
						$page = "mail.php";
						break;
					case '112' :	// Traduction V13
						switch ( $cmde ) {
							case 'gestion' :	// réservé au big chef
								if ( $_SESSION["CnxAdm"] == 255 )
									$page = "traduction_$cmde.htm";
								break;
							case 'post'  :	// interdit aux élèves
								if ( getAccess() == 2 )
									$page = "traduction_$cmde.htm";
								break;
							case 'visu'  :	// le détail par élève
								$page = "traduction_$cmde.htm";
								break;
							default :
								$page = "traduction.htm";
								break;
							}
						break;
					case '1000' :	// création de compte
						list($iscreat, $nil) = explode(":", $AUTHUSER);

						if ( $iscreat )
							$page = "user_new.php";
						break;
					default :	// page d'accueil
						$_SESSION["CampusName"] = "";

						// configuration des items visibles par l'admin ou les gestionnaires
						if ( $_SESSION["CnxAdm"] == 255 OR ($_SESSION["CnxAdm"] & 8) )
							switch ( $cmde ) {
								case "setmenu" :
									setMenuVisible($_GET["ident"], $_GET["access"]);
									break;
								case "setsubmenu" :
									setSubmenuVisible($_GET["ident"], $_GET["access"]);
									break;
								case "setordermenu" :
									setUserMenuOrder($_GET["ident"], $_GET["order"]);
									break;
								case "setorderitem" :
									setUserItemOrder($_GET["ident"], $_GET["order"]);
									break;
								case "delitem" :
									// suppression PJ
									$query  = "select _IDsubmenu from config_submenu ";
									$query .= "where _IDsubmenu = '".@$_GET["iditem"]."' OR _IDmenu = '-".@$_GET["iditem"]."' ";

									$result = mysqli_query($mysql_link, $query);
									$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

									while ( $row ) {
										$ret = mysqli_query($mysql_link, "select _IDpj, _ext from flash_pj where _IDitem = '$row[0]'");
										$sql = ( $ret ) ? mysqli_fetch_row($ret) : 0 ;

										while ( $sql ) {
											unlink("$DOWNLOAD/flash/$sql[0].$sql[1]");
											$sql = mysqli_fetch_row($ret);
											}

										mysqli_query($mysql_link, "delete from flash_pj where _IDitem = '$row[0]'");

										$row = mysqli_fetch_row($result);
										}

									// suppression articles
									$query  = "delete from config_submenu ";
									$query .= "where _IDsubmenu = '".@$_GET["iditem"]."' OR _IDmenu = '-".@$_GET["iditem"]."' ";

									mysqli_query($mysql_link, $query);
									break;
								default :
									break;
								}

						// configuration des menus par les utilisateurs
						if ( $_SESSION["CnxSex"] != "A" )
							switch ( $cmde ) {
								case "setusermenu" :
									setUserMenu($_GET["ident"], $_GET["access"]);
									break;
								default :
									break;
								}

						// la page d'accueil
						$page = getService($item);
//die($page);
						break;
					} // end switch ($item)
				} // endif utilisateur autorisé
			else {
				// compte fermé
				$Query  = "select _email from user_id ";
				$Query .= "where _email != '' AND _adm = '255' ";
				$Query .= "order by _ID asc limit 1";

				$result = mysqli_query($mysql_link, $Query);
				$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

				$email  = ( $row ) ? "<a href=\"mailto:$row[0]\">". $message->read($PAGE_ADMIN) ."</a>" : $message->read($PAGE_ADMIN) ;

				$errconnect = $message->read($PAGE_ACCOUNTCLOSE, $email);
				} // endif $_SESSION["CnxAdm"]
			} // endif IPFILTER
		else
			$errconnect = $message->read($PAGE_PERMDENIED);
		} // endif utilisateur non identifié
	//---- l'utilisateur n'est pas identifié (ou timeout de session)
	else {
		// RAZ si timeout
		$_SESSION["CnxID"]  = "";		// ID utilisateur
		$_SESSION["CnxAdm"] = "";		// Droits de connexion de l'utilisateur
		$_SESSION["egroup"] = "";

		$page_left = $page_right = "";

		switch ( $item ) {
			case '1000' :	// création de compte
				list($iscreat, $nil) = explode(":", $AUTHUSER);

				if ( $iscreat )
					$page = "user_new.php";
				break;
			case '1' :		// mot de passe perdu
				$page = "user_lost.php";
				break;
			default :		// demande d'identification
				$page = "user_login.php";
				break;
			}
		}

/*$style = ( $_SESSION["CfgPage"] == "O" )
	? "style=\"background-image: url('".$_SESSION["CfgFond"]."'); background-repeat:repeat;\""
	: "" ;*/

//$style = "style=\"padding-top: 60px;\"";
if($MODETHEME == "portal")
{
	$style = "style=\"padding-top: 20px; background-color: #EFEFEF\"";
}



//---------------------------------------------------------------------------
function makeSlide()
{
	/*
	 * fonction :	images défilantes dans le bandeau
	 */

	require "globals.php";

	// iniitalisation
	$slide = "download/logos/". rawurlencode($_SESSION["CfgIdent"]) ."/logo01.jpg";

	// lecture de la base de données des logos du bandeau
	$Query  = "select _IDlogo from config_logo ";
	$Query .= "where _visible = 'O' ";
	$Query .= "order by _IDlogo";

	$result = mysqli_query($mysql_link, $Query);

	if ( mysqli_num_rows($result) ) {
		while ( ($row = mysqli_fetch_row($result)) != 0 )
			$slide .= ";download/logos/". rawurlencode($_SESSION["CfgIdent"]) ."/logo01-$row[0].jpg";

		print("
			onload='init_slider();
			RunSlideShow(\"header\", \"blendimage\", \"$slide\", $TIMEREFRESH);'");
		}
}
//---------------------------------------------------------------------------


?>


<body onLoad="window.print()" class=wide left>

<?php
if($MODETHEME == "portal")
{
	echo "<div class=\"page\" style=\"background-color: #FFFFFF; margin-left: 5%; margin-right: 5%; padding: 10px\">";
}
?>

<!-- haut de page -->

<!-- milieu de page -->
<?php
$style = ( $page_left AND $_SESSION["CfgPage"] == "N" )
	? "background-image: url('".$_SESSION["CfgFond"]."'); background-repeat:repeat;"
	: "" ;
?>

<div class="container-fluid">
	<!-- partie gauche : <?php //print("$page_left"); ?> -->
	<?php

			$width_left  = "0px";

	?>

	<?php
//		$isOk = (bool) ( $page_right != "" OR $force_right OR $MENUSKIN == 0 OR $MENUSKIN == 2 );
		if ( $force_right OR $MENUSKIN == 0 OR $MENUSKIN == 2 )
			$margin_right = "10px";
		else {
			$width_right  = "0px";
			$margin_right = "0px";
			}

		print("<div style=\"width:$width_right; float:right;\">");
		if ( $MENUSKIN == 2 AND $page_left ) require "$page_left";
		print("</div>");
	 ?>

	<!-- partie centrale : <?php print("$page"); ?> -->
	<?php
		require_once "motd.php";
		require_once "include/postit.php";

		if($item == "-1")
		{
			print("<div>");
			print("<div>");
		}
		else
		{
			print("<style>
			@media screen {
			.width-left {
				margin-left: $width_left;
			}
			.decal-left {
				margin-left: $margin_left;
			}
			}
			</style>");
			print("<div style=\"margin-right:$width_right;\" class=\"width-left\">");
			print("<div style=\"margin-right:$margin_right;\" class=\"decal-left\">");
		}

		// a-t-on reçu un post-it ?
		newMail();

		// message of the day
		motd((int) @$_GET["IDitem"]);

		if ( file_exists($_SESSION["ROOTDIR"]."/$page") ) {
			if ( $errconnect != "" )
				print("
					<div style=\"text-align:center; margin-bottom:10px;\">
					<span style=\"color:#FF0000\">". $message->read($PAGE_ERRCONNECT) ."</span> : $errconnect
					</div>");

			// sélection du fichier langue
			$path = $_SESSION["ROOTDIR"] . "/";
			$file = explode(" ", str_replace(Array($path, '/', '_', '.'), Array('', ' ', ' ', ' '), $page));
//			$file = explode(" ", str_replace(Array('/', '_', '.'), Array(' ', ' ', ' '), $page));

			if ( is_file($_SESSION["ROOTDIR"]."/msg/$file[0].php") )
				require "msg/$file[0].php";

			$msg  = new TMessage("msg/".$_SESSION["lang"]."/$file[0].php", $_SESSION["ROOTDIR"]);
			$msg->msg_search  = $keywords_search;
			$msg->msg_replace = $keywords_replace;

			// log des stats
			logSession($page);

			// Dérivation de pages
		if(file_exists("derivation/page/".$page))
		{
			$page = "derivation/page/".$page;
		}


			// affichage page
			require "$page";
			}
		else {
			// accès refusé
			require "forbidden.php";

			// log des stats
			logSession("forbidden.php");
			}

		print("</div>");
		print("</div>");
	?>

<!-- bas de page -->
</div>

</body>
</html>
