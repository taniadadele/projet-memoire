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
?>

<?php
/*
 *		module   : spip.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 08/04/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$SPIP_LIST                 = 0;
static	$SPIP_DISCLAIMER           = 1;
static	$SPIP_NEWPUBLI             = 2;
static	$SPIP_NEXTPREV             = 3;
static	$SPIP_VALIDATION           = 4;
static	$SPIP_ARTICLE              = 5;
static	$SPIP_WRITING              = 6;
static	$SPIP_PRIVATE              = 7;
static	$SPIP_DELETE               = 8;
static	$SPIP_UPDATE               = 9;
static	$SPIP_PARAM                = 10;
static	$SPIP_SEARCH               = 11;
static	$SPIP_ADD                  = 12;
static	$SPIP_INSERTION            = 13;
static	$SPIP_MODIFICATION         = 14;
static	$SPIP_AUTHOR               = 15;
static	$SPIP_IDENT                = 16;
static	$SPIP_THEME                = 17;
static	$SPIP_TEXT                 = 18;
static	$SPIP_GETCLASS             = 19;
static	$SPIP_ADDANNOUNCE          = 20;
static	$SPIP_BACK2CAMPUS          = 21;
static	$SPIP_FORMFEED             = 22;
static	$SPIP_UPDTLINK             = 23;
static	$SPIP_NEWLINK              = 24;
static	$SPIP_ERRIDENT             = 25;
static	$SPIP_WEBSITE              = 26;
static	$SPIP_ERRURL               = 27;
static	$SPIP_URL                  = 28;
static	$SPIP_LANGUAGE             = 29;
static	$SPIP_LICENSE              = 30;
static	$SPIP_ADDLINK              = 31;
static	$SPIP_GOBACK               = 32;
static	$SPIP_MORE                 = 33;
static	$SPIP_POLL                 = 34;
static	$SPIP_THEN                 = 35;
static	$SPIP_PREV                 = 36;
static	$SPIP_NEXT                 = 37;
static	$SPIP_INPUTON              = 38;
static	$SPIP_INPUTOFF             = 39;
static	$SPIP_INVISIBLE            = 40;
static	$SPIP_VISIBLE              = 41;
static	$SPIP_ADDTOPIC             = 42;
static	$SPIP_TOPICTITLE           = 43;
static	$SPIP_BKGIMAGE             = 44;
static	$SPIP_SOUND                = 45;
static	$SPIP_LOOP                 = 46;
static	$SPIP_YES                  = 47;
static	$SPIP_NO                   = 48;
static	$SPIP_NBARTICLE            = 49;
static	$SPIP_IMAGE                = 50;
static	$SPIP_IMAGETITLE           = 51;
static	$SPIP_DESCRIPTION          = 52;
static	$SPIP_ADDSMILE             = 53;
static	$SPIP_UPDTANNOUNCE         = 54;
static	$SPIP_ATTACHEMENT          = 55;
static	$SPIP_ATTCHDOC             = 56;
static	$SPIP_DOCTITLE             = 57;
static	$SPIP_DELATTACH            = 58;
static	$SPIP_OPENATTACH           = 59;
static	$SPIP_MANAGEMENT           = 60;
static	$SPIP_STATUS               = 61;
static	$SPIP_PUBLICATION          = 62;
static	$SPIP_CLOSE                = 63;
static	$SPIP_MODO                 = 64;
static	$SPIP_WRITER               = 65;
static	$SPIP_READER               = 66;
static	$SPIP_NONE                 = 67;
static	$SPIP_PERMS                = 68;
static	$SPIP_ATTACHMENT           = 69;
static	$SPIP_PRIVATE              = 70;
static	$SPIP_UPDTPUBLI            = 71;
static	$SPIP_QUIT                 = 72;
static	$SPIP_FORMFEED             = 73;
static	$SPIP_THANX                = 74;
static	$SPIP_TITLE                = 75;
static	$SPIP_MYDESCRIPTION        = 76;
static	$SPIP_TEMPLATE             = 77;
static	$SPIP_GOBACK               = 78;
static	$SPIP_FORMITEM             = 79;
static	$SPIP_DELETE               = 80;
static	$SPIP_ERRTITLE             = 81;
static	$SPIP_ERRTITLE2            = 82;
static	$SPIP_ERRTEXT              = 83;
static	$SPIP_TOPICTITLE           = 84;
static	$SPIP_VISUALIZE            = 85;
static	$SPIP_NEWTOPIC             = 86;
static	$SPIP_UPDTOPIC             = 87;
static	$SPIP_ALIGN                = 88;
static	$SPIP_LEFT                 = 89;
static	$SPIP_CENTER               = 90;
static	$SPIP_RIGHT                = 91;
static	$SPIP_BACKGROUND           = 92;
static	$SPIP_ITEMTITLE            = 93;
static	$SPIP_CREATITEM            = 94;
static	$SPIP_UPDTITEM             = 95;
static	$SPIP_DELITEM              = 96;
static	$SPIP_COLOR                = 97;
static	$SPIP_SWAPBASIC            = 98;
static	$SPIP_SWAPADVANCED         = 99;
static	$SPIP_ITEMTEXT             = 100;
static	$SPIP_TYPO                 = 101;
static	$SPIP_HELP                 = 102;
static	$SPIP_VALID                = 103;
static	$SPIP_QUIT                 = 104;
static	$SPIP_SUMMARY              = 105;
static	$SPIP_ATTACHED             = 106;
static	$SPIP_DECLARE              = 107;
static	$SPIP_BYTE                 = 108;
static	$SPIP_CREATBY              = 109;
static	$SPIP_NOTE                 = 110;
static	$SPIP_POSTBY               = 111;
static	$SPIP_DELANNOUNCE          = 112;
static	$SPIP_UPDTANNOUNCE         = 113;
static	$SPIP_DELLINK              = 114;
static	$SPIP_HIT                  = 115;
static	$SPIP_NBHIT                = 116;
static	$SPIP_COMMENT              = 117;
static	$SPIP_NEWANNOUNCE          = 118;
static	$SPIP_MODIFYLINK           = 119;
static	$SPIP_ERASE                = 120;
static	$SPIP_NOSHOWITEM           = 121;
static	$SPIP_SHOWITEM             = 122;
static	$SPIP_NOSHOW               = 123;
static	$SPIP_SHOW                 = 124;
static	$SPIP_ATTACHOK             = 125;
static	$SPIP_ATTACHNOTOK          = 126;
static	$SPIP_ADDPUBLI             = 127;
static	$SPIP_UPDTBY               = 128;
static	$SPIP_HITBY                = 129;
static	$SPIP_ADDBY                = 130;
static	$SPIP_BASIC                = 131;
static	$SPIP_ADVANCED             = 132;
static	$SPIP_VALIDATE             = 133;
static	$SPIP_DOWNLOAD             = 134;
static	$SPIP_SUBJECT              = 135;
static	$SPIP_TOPARTICLE           = 136;
static	$SPIP_DOCMANAGEMENT        = 137;
static	$SPIP_DIR                  = 138;
static	$SPIP_CLOSEDIR             = 139;
static	$SPIP_CREATDIR             = 140;
static	$SPIP_UPDATEDIR            = 141;
static	$SPIP_NEWDIR               = 142;
static	$SPIP_ROOTDIR              = 143;
static	$SPIP_PARENTDIR            = 144;
static	$SPIP_CLOSING              = 145;
static	$SPIP_OPENING              = 146;
static	$SPIP_NBDIR                = 147;
static	$SPIP_NBFILE               = 148;
static	$SPIP_INPUTNEW             = 149;
static	$SPIP_INPUTREPLY           = 150;
static	$SPIP_INPUTOK              = 151;
static	$SPIP_INPUTCANCEL          = 152;
//---------------------------------------------------------------------------
?>