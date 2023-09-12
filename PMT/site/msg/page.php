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
 *		module   : page.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 14/03/2007
 *		modif    :
 *
 */

//---------------------------------------------------------------------------
static	$PAGE_BADTHEME             = 0;
static	$PAGE_BADCONF              = 1;
static	$PAGE_MONTH                = 2;
static	$PAGE_MONTHFULL            = 3;
static	$PAGE_TODAY                = 4;
static	$PAGE_FEST                 = 5;
static	$PAGE_IDEA                 = 6;
static	$PAGE_WELCOME              = 7;
static	$PAGE_SEND                 = 8;
static	$PAGE_QUICK                = 9;
static	$PAGE_MYKEYWORD            = 10;
static	$PAGE_TOPIC                = 11;
static	$PAGE_INFOFLASH            = 12;
static	$PAGE_RESOURCE             = 13;
static	$PAGE_FORUM                = 14;
static	$PAGE_GALLERY              = 15;
static	$PAGE_ARTICLE              = 16;
static	$PAGE_SENDSEARCH           = 17;
static	$PAGE_MAINTENANCE          = 18;
static	$PAGE_CONFIG               = 19;
static	$PAGE_IDENT                = 20;
static	$PAGE_LOGS                 = 21;
static	$PAGE_CURENTLY             = 22;
static	$PAGE_MEMBER               = 23;
static	$PAGE_ONLINE               = 24;
static	$PAGE_ANONYMOUS            = 25;
static	$PAGE_AND                  = 26;
static	$PAGE_WITH                 = 27;
static	$PAGE_GHOST                = 28;
static	$PAGE_IP                   = 29;
static	$PAGE_STATION              = 30;
static	$PAGE_BROWSER              = 31;
static	$PAGE_OS                   = 32;
static	$PAGE_CLOSEPOLL            = 33;
static	$PAGE_VERSION              = 34;
static	$PAGE_POLL                 = 35;
static	$PAGE_VOTED                = 36;
static	$PAGE_VOTE                 = 37;
static	$PAGE_RESULT               = 38;
static	$PAGE_NBPOLL               = 39;
static	$PAGE_TITLE1               = 40;
static	$PAGE_TITLE2               = 41;
static	$PAGE_INVISIBLE            = 42;
static	$PAGE_VISIBLE              = 43;
static	$PAGE_LASTCNX              = 44;
static	$PAGE_LOGOUT               = 45;
static	$PAGE_BACK                 = 46;
static	$PAGE_CATEGORY             = 47;
static	$PAGE_ITEM                 = 48;
static	$PAGE_THEME                = 49;
static	$PAGE_POSTIT               = 50;
static	$PAGE_EXP                  = 51;
static	$PAGE_BOOKING              = 52;
static	$PAGE_BY                   = 53;
static	$PAGE_FOUND                = 54;
static	$PAGE_NEXTPREV             = 55;
static	$PAGE_DATE                 = 56;
static	$PAGE_PREV                 = 57;
static	$PAGE_NEXT                 = 58;
static	$PAGE_RESEARCH             = 59;
static	$PAGE_ALLFLASH             = 60;
static	$PAGE_ALLARTICLE           = 61;
static	$PAGE_TITLE                = 62;
static	$PAGE_TEXT                 = 63;
static	$PAGE_DESCRIPTION          = 64;
static	$PAGE_MESSAGE              = 65;
static	$PAGE_SUBJECT              = 66;
static	$PAGE_GALLERYDESC          = 67;
static	$PAGE_IMAGEDESC            = 68;
static	$PAGE_EVENT                = 69;
static	$PAGE_DIARY                = 70;
static	$PAGE_TXT2SEARCH           = 71;
static	$PAGE_SEARCH               = 72;
static	$PAGE_LOOKINTO             = 73;
static	$PAGE_SORTBYDATE           = 74;
static	$PAGE_MYTEXT               = 75;
static	$PAGE_DOWN                 = 76;
static	$PAGE_UP                   = 77;
static	$PAGE_INCLUDE              = 78;
static	$PAGE_EXACTLY              = 79;
static	$PAGE_PRINT                = 80;
static	$PAGE_FIRSTMSG             = 81;
static	$PAGE_LUNCHSEARCH          = 82;
static	$PAGE_BACKTO               = 83;
static	$PAGE_AT                   = 84;
static	$PAGE_KEYWORD              = 85;
static	$PAGE_LASTMSG              = 86;
static	$PAGE_LASTDOC              = 87;
static	$PAGE_LASTNEWS             = 88;
static	$PAGE_ALLRESOURCE          = 89;
static	$PAGE_ALLFORUM             = 90;
static	$PAGE_ALLGALLERY           = 91;
static	$PAGE_ALLPOSTIT            = 92;
static	$PAGE_ALLBOOKING           = 93;
static	$PAGE_ADMIN                = 94;
static	$PAGE_ERRCONNECT           = 95;
static	$PAGE_ACCOUNTCLOSE         = 96;
static	$PAGE_PERMDENIED           = 97;
static	$PAGE_ACCOUNT              = 98;
static	$PAGE_PWDLOST              = 99;
static	$PAGE_POSTBY               = 100;
static	$PAGE_POSTAT               = 101;
static	$PAGE_CREATACCOUNT         = 102;
static	$PAGE_PORTAL               = 103;
static	$PAGE_MENUUP               = 104;
static	$PAGE_MENUDOWN             = 105;
static	$PAGE_LEFT                 = 106;
static	$PAGE_RIGHT                = 107;
static	$PAGE_BACKOFFICE           = 108;
static	$PAGE_LASTUPDATE           = 109;
static	$PAGE_LASTGALLERY          = 110;
static	$PAGE_ADDITEM              = 111;
static	$PAGE_DELITEM              = 112;
static	$PAGE_UPDATEITEM           = 113;
static	$PAGE_WEBMASTER            = 114;
static	$PAGE_FLASH                = 115;
static	$PAGE_COURSE               = 116;
static	$PAGE_SUPPORT              = 117;
static	$PAGE_SCHOOLBAG            = 118;
static	$PAGE_INPUTNEW             = 119;
static	$PAGE_INPUTREPLY           = 120;
static	$PAGE_INPUTOK              = 121;
static	$PAGE_INPUTCANCEL          = 122;
static	$PAGE_TOP                  = 123;
static	$PAGE_BTN_EDT              = 124;
static	$PAGE_BTN_NOTE             = 125;
static	$PAGE_BTN_DEVOIR           = 126;
static	$PAGE_BTN_MY_SHORTCUTS     = 127;
static	$PAGE_BTN_MY_FILES         = 128;
static	$PAGE_LISTING_HOURS        = 129;
//---------------------------------------------------------------------------
?>
