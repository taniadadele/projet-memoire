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
 *		module   : ctn.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 30/03/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$CTN_CTN                  = 0;
static	$CTN_DISCLAIMER           = 1;
static	$CTN_CHOOSECENTER         = 2;
static	$CTN_CHOOSECLASS          = 3;
static	$CTN_NEWCTN               = 4;
static	$CTN_VALIDATE             = 5;
static	$CTN_NEXTPREV             = 6;
static	$CTN_DATE                 = 7;
static	$CTN_DELAY                = 8;
static	$CTN_ABS                  = 9;
static	$CTN_HIT                  = 10;
static	$CTN_NOSHOW               = 11;
static	$CTN_SHOW                 = 12;
static	$CTN_CONFIRM              = 13;
static	$CTN_DELETE               = 14;
static	$CTN_UPDATE               = 15;
static	$CTN_PREV                 = 16;
static	$CTN_NEXT                 = 17;
static	$CTN_MANAGEMENT           = 18;
static	$CTN_MODIFICATION         = 19;
static	$CTN_STATUS               = 20;
static	$CTN_CENTER               = 21;
static	$CTN_CLASS                = 22;
static	$CTN_CLOSE                = 23;
static	$CTN_MODO                 = 24;
static	$CTN_WRITER               = 25;
static	$CTN_READER               = 26;
static	$CTN_NONE                 = 27;
static	$CTN_PERMS                = 28;
static	$CTN_ATTACHMENT           = 29;
static	$CTN_DECLARE              = 30;
static	$CTN_UPDATECTN            = 31;
static	$CTN_GOBACK               = 32;
static	$CTN_MYCTN                = 33;
static	$CTN_FORMFEED             = 34;
static	$CTN_RECORD               = 35;
static	$CTN_THANX                = 36;
static	$CTN_BACK                 = 37;
static	$CTN_CHAPTER              = 38;
static	$CTN_COURSE               = 39;
static	$CTN_AUTHOR               = 40;
static	$CTN_ERRDATE              = 41;
static	$CTN_MYDATE               = 42;
static	$CTN_MYDELAY              = 43;
static	$CTN_ERRIDENT             = 44;
static	$CTN_IDENT                = 45;
static	$CTN_BASIC                = 46;
static	$CTN_ADVANCED             = 47;
static	$CTN_TEXT                 = 48;
static	$CTN_HELP                 = 49;
static	$CTN_DOC                  = 50;
static	$CTN_DELATTACH            = 51;
static	$CTN_ATTACHEDOC           = 52;
static	$CTN_ATTACHED             = 53;
static	$CTN_DESCRIPTION          = 54;
static	$CTN_ABSENT               = 55;
static	$CTN_DELABSENT            = 56;
static	$CTN_NOTES                = 57;
static	$CTN_VALIDINPUT           = 58;
static	$CTN_VISUALIZE            = 59;
static	$CTN_SHOWDESC             = 60;
static	$CTN_EMAILED              = 61;
static	$CTN_INPUT                = 62;
static	$CTN_GETCLASS             = 63;
static	$CTN_GETMATTER            = 64;
static	$CTN_ERASE                = 65;
static	$CTN_SIZE                 = 66;
static	$CTN_GETABSENT            = 67;
static	$CTN_MODIFY               = 68;
static	$CTN_MOTIF                = 69;
static	$CTN_DOCUMENT             = 70;
static	$CTN_AT                   = 71;
static	$CTN_SCHOOLYEAR           = 72;
static	$CTN_MONTH                = 73;
static	$CTN_MONTHFULL            = 74;
static	$CTN_DAYS                 = 75;
static	$CTN_DAYSFULL             = 76;
static	$CTN_TODO                 = 77;
static	$CTN_FORNEXT              = 78;
static	$CTN_LIST                 = 79;
static	$CTN_LISTEVENTS           = 80;
static	$CTN_DELYEAR              = 81;
static	$CTN_WEEKOF               = 82;
static	$CTN_TODAY                = 83;
static	$CTN_POSTED               = 84;
static	$CTN_WEEK                 = 85;
static	$CTN_DISPLAY              = 86;
static	$CTN_DISPLAYLIST          = 87;
static	$CTN_DIARY                = 88;
static	$CTN_CHOOSEYEAR           = 89;
static	$CTN_PROGRESS             = 90;
static	$CTN_DIRECTACCESS         = 91;
static	$CTN_COPY                 = 92;
static	$CTN_CURRDATE             = 93;
static	$CTN_ISOK                 = 94;
static	$CTN_LIMITED              = 95;
static	$CTN_COMMON               = 96;
static	$CTN_PROGRESSION          = 97;
static	$CTN_TIMETABLE            = 98;
static	$CTN_HOUR                 = 99;
static	$CTN_PRINT		        = 100;
static	$CTN_FROMTO		        = 101;
static	$CTN_SCHOOLYEARFULL	  = 102;
static	$CTN_GETDOC		        = 103;
static	$CTN_SECTION		  = 104;
static	$CTN_VIEWS			  = 105;
static	$CTN_HOURPDF		  = 106;
static	$CTN_DELAYPDF		  = 107;
static	$CTN_AUTHORPDF		  = 108;
static	$CTN_FROM			  = 109;
static	$CTN_TO			  = 110;
static	$CTN_PRINTING             = 111;
static	$CTN_FONTSIZE             = 112;
static	$CTN_RSS                  = 113;
static	$CTN_IMPORT               = 114;
static	$CTN_SNDMAIL	        = 115;
static	$CTN_INPUTNEW             = 116;
static	$CTN_INPUTREPLY           = 117;
static	$CTN_INPUTOK              = 118;
static	$CTN_INPUTCANCEL          = 119;
static	$CTN_TYPE                 = 120;
static	$CTN_COCHEDECOCHE         = 121;
static	$CTN_LISTEDEVOIRS         = 122;
static	$CTN_BEFORE         = 123;
static	$CTN_AFTER                = 124;
static	$CTN_MONTH                = 125;
static	$CTN_NONAUT                = 126;
static	$CTN_DEVOIRS               = 127;
static	$CTN_CONTENU               = 128;
static	$CTN_CDT 		   = 129;
static	$CTN_OBSERV 		      = 130;
static	$CTN_PARPROMO 		      = 131;
static	$CTN_PARMAT 		      = 132;
static	$CTN_MATIERE 		      = 133;

//---------------------------------------------------------------------------
?>
