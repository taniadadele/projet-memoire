<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2008 by Dominique Laporte(C-E-D@wanadoo.fr)

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
   along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/


/*
 *		projet   : définiton des variables de configuration de l'intranet
 *
 *		version  : 14.0
 *		auteur   : laporte
 *		creation : 4/10/02
 *		22/10/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *					  passage en PHP 7 et maj du thème
 */

//---------------------------------------------------------------------------


static	$SERVER      = "database";				// nom du serveur MySQL
static	$USER        = "root";				// nom de l'utilisateur MySQL
static	$PASSWD      = "root";				// mot de passe de l'utilisateur MySQL
static	$DATABASE    = "test";			  // nom de la base de données MySQL
static	$SERVPORT    = 3306;				  // port du serveur MySQL (laisser à 0 pour prendre la valeur par défaut)

static	$DOWNLOAD    = "download";			// répertoire des documents des utilisateurs
static	$LANG        = "fr";				// langue utilisée
static	$PERSISTENT  = 0;			// connexion persistante à la base de données
static	$TIMELOG     = 5184000;				// durée de stockage des logs : 60 jours (0 si illimité)
static	$TIMESTAT    = 864000;				// durée de stockage des stats : 31 jours (0 si illimité)
static	$TIMELIMIT   = 1800;				// temps d'expiration des pages : 30 minutes
static	$TIMEFORUM   = 86400;					// nouveaux messages des forums : 24 heures
static	$TIMERSS     = 604800;					// temps d'expiration des news rss : 7 jours
static	$TIMELINK    = 172800;				// temps d'expiration des liens par mail: 2 jours
static	$TIMEREFRESH = 10;				// temps de rafraichissement des images du bandeau (secondes)
static	$TIMETMP     = 600;				// délais des fichiers temporaires avant suppression (secondes)
static	$FILESIZE    = 1024000;				// taille max des fichiers à transférer
static	$DEBUG       = 0;				// mode débogage (1 pour activer)
static	$DEMO        = 0;				// mode démo (1 pour activer)
static	$CHARSET     = "utf-8";				// encodage des caractères
static	$WYSIWYG     = 1;					// éditeur wysiwyg (1 pour activer par défaut)
static	$MAINTENANCE = 0;					// mode maintenance (1 pour activer)
static	$USERPWD     = 3;			// longueur des mots de passe (0 pour mdp vide)
static	$IPFILTER    = 0;				// filtrage des adresses IP (1 pour activer)
// static	$MAXPAGE     = 50;				// nombre max de données par page de présentation
static	$MAXSHOW     = 9;				// nombre max de pages accessibles
static	$IMGWIDTH    = 700;					// largeur max des images (en pixels)
static	$MAXIMGWDTH  = 400;					// largeur max des vignettes (en pixels)
static	$MAXIMGHGTH  = 400;					// hauteur max des vignettes (en pixels)
static	$IMGBYLINE   = 3;					// nombre de vignettes par ligne
static	$IMGMAXLINE  = 6;					// nombre max de ligne de vignettes
static	$IMGUPLOAD   = "galerie/upload";		// répertoire pour import d'images
static	$MAXSTAR     = 5;					// nombre max d'étoiles pour les stats de fréquentation
static	$HITBYSTAR   = 100;					// nombre de hit par étoile
static	$MAXRECENT   = 5;				// nombre max de messages récents affichés dans les forums
static	$FLASH       = "Général";				// flash info par défaut (si vide : flash personnalisé download/spip/templates/<lang>/custom.htm)
static	$SERVICE     = "";					// service au démarrage (si vide : flash info par défaut)
// static	$IDENT       = "élève[_ID:0]";		// ID pour attribution automatique de login (élève + n° de connexion)
static	$IDENT       = "[_name:4][_fname:4]";		// ID pour attribution automatique de login (4 lettres du nom + 4 lettres du prénom)
static	$AUTOPASSWD  = "[_born:0]";				// mot de passe pour attribution automatique de login (date de naissance aaaa-mm-jj)
//static	$AUTOLOGIN   = "id=visiteur&pwd=visiteur";			// ID et mot de passe pour connexion automatique
static	$AUTOLOGIN   = "";			// ID et mot de passe pour connexion automatique
static  $MODETHEME   = "intranet";	// portal ou intranet
static  $BTNLEFT     = 1;	// affichage boutons gauche
static	$AUTOVAL     = 0;					// validation automatique des comptes créés par les utilisateurs (1 pour activer)
static	$AUTODEL     = 0;					// suppression automatique des comptes non visités au bout de x heures (0 pour désactiver)
static	$VERSION     = "14.0";				// version de l'intranet
static	$CONFIG      = "config.txt";				// fichier de configuration de l'intranet utilisé
static	$CREDITS     = "credits.txt";			// fichier de la liste des contributeurs
//static	$GEOLOC      = 10753;					// ID inscription geo-loc.com pour la localisation géographique des internautes
static	$GEOLOC      = 0;					// ID inscription geo-loc.com pour la localisation géographique des internautes
static	$MAXPOST     = 3628800;				// délai max avant l'effaçage des post-it en attentes : 6 semaines (0 si illimité)
static	$SHOWPOST    = 0;					// pour visualiser les post-it en attentes (1 pour activer)
static	$HDQUOTAS    = 1024000;			// quotas disque pour les Espaces de Travail Partagés
static	$AUTHUSER    = "1:11";			// pour autoriser les utilisateurs à créer leur compte (1 pour activer)
static	$ACOUNTIME   = "0";			// durée de validité des comptes (0000-00-00 00:00:00 pour validité illimitée)
static	$ROOTSYS     = "";					// chemin absolu pour le dépôt des fichiers (pb e-smith)
static	$HDRSPACING  = 10;					// séparation barre d'accueil et la page (en pixels)
static	$TBLSPACING  = 10;			// séparation entre les tableaux des menus (en pixels)
static	$MENUSKIN    = "1";			// placement des menus (0 : 2 colonnes, 1 : gauche, 2 : droite)
// static	$WEBSITE     = "http://promethee.eu.org";	// site internet Prométhée
static	$WEBSITE     = "http://promethee-solutions.fr/ent-libre/";	// site internet Prométhée
// static	$SMSPROVIDER = "sms@SMStoB.com";			// fournisseur service SMS
static	$SMSPROVIDER = "";			// fournisseur service SMS
static	$SMSPWD      = "";			// mot de passe au service SMS
static	$CHMOD       = 0775;			// permission par défaut pour transfert de fichiers
static	$TIMEZONE    = "Europe/Paris";			// fuseau horaire

// ---------------------------------------------------------------------------
// Fonction: Partie des paramètres qui traitent de la connexion au server de
//           traitement des copies
// ---------------------------------------------------------------------------
static  $COPIES_REMOTE_FILE = "";
static  $COPIES_FTP_SERVER  = "";
static  $COPIES_FTP_USER    = "";
static  $COPIES_FTP_PASS    = '';
static  $COPIES_CURL_SCRIPT = "";
//---------------------------------------------------------------------------
?>
