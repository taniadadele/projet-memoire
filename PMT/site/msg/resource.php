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
 *		module   : resource.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 11/04/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$RESOURCE_RESTITLE             = 0;
static	$RESOURCE_AVAILFORMAT          = 1;
static	$RESOURCE_CHOOSESERVER         = 2;
static	$RESOURCE_ROOTDIR              = 3;
static	$RESOURCE_MODIFY               = 4;
static	$RESOURCE_CREAT                = 5;
static	$RESOURCE_ADDIR                = 6;
static	$RESOURCE_SIZE                 = 7;
static	$RESOURCE_PARENTDIR            = 8;
static	$RESOURCE_ADD                  = 9;
static	$RESOURCE_SENDCMD              = 10;
static	$RESOURCE_RENCMD               = 11;
static	$RESOURCE_ADDOC                = 12;
static	$RESOURCE_MANAGEMENT           = 13;
static	$RESOURCE_STATUS               = 14;
static	$RESOURCE_DIR                  = 15;
static	$RESOURCE_RESOURCE             = 16;
static	$RESOURCE_CLOSE                = 17;
static	$RESOURCE_MODO                 = 18;
static	$RESOURCE_WRITER               = 19;
static	$RESOURCE_READER               = 20;
static	$RESOURCE_NONE                 = 21;
static	$RESOURCE_DECLARE              = 22;
static	$RESOURCE_UPDTRES              = 23;
static	$RESOURCE_QUIT                 = 24;
static	$RESOURCE_FORMFEED             = 25;
static	$RESOURCE_MODIFICATION         = 26;
static	$RESOURCE_NEWRECORD            = 27;
static	$RESOURCE_TITLE                = 28;
static	$RESOURCE_URL                  = 29;
static	$RESOURCE_LANG                 = 30;
static	$RESOURCE_DESCRIPTION          = 31;
static	$RESOURCE_KWORD                = 32;
static	$RESOURCE_AUTHOR               = 33;
static	$RESOURCE_LICENSE              = 34;
static	$RESOURCE_CONTENT              = 35;
static	$RESOURCE_LEVEL                = 36;
static	$RESOURCE_MATTER               = 37;
static	$RESOURCE_RECORD               = 38;
static	$RESOURCE_DOCMANAGEMENT        = 39;
static	$RESOURCE_CLOSEDIR             = 40;
static	$RESOURCE_ACTION               = 41;
static	$RESOURCE_GOBACK               = 42;
static	$RESOURCE_ONLINE               = 43;
static	$RESOURCE_NEWDIR               = 44;
static	$RESOURCE_CHOOSERES            = 45;
static	$RESOURCE_ALLRESOURCE          = 46;
static	$RESOURCE_CHOOSELEVEL          = 47;
static	$RESOURCE_ALLLEVEL             = 48;
static	$RESOURCE_ADDRESOURCE          = 49;
static	$RESOURCE_LUNCHSEARCH          = 50;
static	$RESOURCE_ALLCONTENT           = 51;
static	$RESOURCE_HIT                  = 52;
static	$RESOURCE_MORE                 = 53;
static	$RESOURCE_CREATBY              = 54;
static	$RESOURCE_UPDTBY               = 55;
static	$RESOURCE_ERASE                = 56;
static	$RESOURCE_PREV                 = 57;
static	$RESOURCE_NEXT                 = 58;
static	$RESOURCE_FORMINPUT            = 59;
static	$RESOURCE_DELMSG               = 60;
static	$RESOURCE_TEXT                 = 61;
static	$RESOURCE_HELP                 = 62;
static	$RESOURCE_ADDANNOUNCE          = 63;
static	$RESOURCE_BACK2RES             = 64;
static	$RESOURCE_FORMRES              = 65;
static	$RESOURCE_UPDTRESOURCE         = 66;
static	$RESOURCE_NEWRESOURCE          = 67;
static	$RESOURCE_TRANSFERT            = 68;
static	$RESOURCE_INSERTION            = 69;
static	$RESOURCE_ERRFILE              = 70;
static	$RESOURCE_CATEGORY             = 71;
static	$RESOURCE_ERRIDENT             = 72;
static	$RESOURCE_IDENT                = 73;
static	$RESOURCE_MYDESCRIPTION        = 74;
static	$RESOURCE_ERRSIZE              = 75;
static	$RESOURCE_ERRUPLOAD            = 76;
static	$RESOURCE_ERRTYPE              = 77;
static	$RESOURCE_ERREXIST             = 78;
static	$RESOURCE_WARNING              = 79;
static	$RESOURCE_UPLOAD               = 80;
static	$RESOURCE_VERSION              = 81;
static	$RESOURCE_LEVELTYPE            = 82;
static	$RESOURCE_EXERCICE             = 83;
static	$RESOURCE_SHARE                = 84;
static	$RESOURCE_BACKTO               = 85;
static	$RESOURCE_FILE                 = 86;
static	$RESOURCE_NEXTPREV             = 87;
static	$RESOURCE_BACK                 = 88;
static	$RESOURCE_DOCUMENTS            = 89;
static	$RESOURCE_CHOOSECLASS          = 90;
static	$RESOURCE_CHOOSEGROUP          = 91;
static	$RESOURCE_ALLCLASS             = 92;
static	$RESOURCE_ALLGROUP             = 93;
static	$RESOURCE_NEW                  = 94;
static	$RESOURCE_DOCTITLE             = 95;
static	$RESOURCE_DATE                 = 96;
static	$RESOURCE_FORMAT               = 97;
static	$RESOURCE_UPDATING             = 98;
static	$RESOURCE_RESMODIFY            = 99;
static	$RESOURCE_CLOSING              = 100;
static	$RESOURCE_OPENING              = 101;
static	$RESOURCE_SEARCH               = 102;
static	$RESOURCE_DELDIR               = 103;
static	$RESOURCE_VER                  = 104;
static	$RESOURCE_RESIDENT             = 105;
static	$RESOURCE_COMMENT              = 106;
static	$RESOURCE_ERRENDIR             = 107;
static	$RESOURCE_ERRDELDIR            = 108;
static	$RESOURCE_ERRDELFILE           = 109;
static	$RESOURCE_ERRENFILE            = 110;
static	$RESOURCE_DELFILE              = 111;
static	$RESOURCE_KEYWORD              = 112;
static	$RESOURCE_FICHE                = 113;
static	$RESOURCE_ONLINEDBA            = 114;
static	$RESOURCE_NBDIR                = 115;
static	$RESOURCE_NBFILE               = 116;
static	$RESOURCE_CPFILE               = 117;
static	$RESOURCE_COPY                 = 118;
static	$RESOURCE_MVFILE               = 119;
static	$RESOURCE_MOVE                 = 120;
static	$RESOURCE_LASTDOWNLOAD         = 121;
static	$RESOURCE_CLOSEWINDOW          = 122;
static	$RESOURCE_VALIDATE             = 123;
static	$RESOURCE_NOTE                 = 124;
static	$RESOURCE_VALID                = 125;
static	$RESOURCE_SORT                 = 126;
static	$RESOURCE_CONNEXION            = 127;
static	$RESOURCE_AUTHORIZATION        = 128;
static	$RESOURCE_COMMENTS             = 129;
static	$RESOURCE_PRIORITY             = 130;
static	$RESOURCE_PRIORITYLEVEL        = 131;
static	$RESOURCE_INFORM               = 132;
static	$RESOURCE_INFORMSETUP          = 133;
static	$RESOURCE_SUBJECT              = 134;
static	$RESOURCE_OBJECT               = 135;
static	$RESOURCE_COLLABORATIVE        = 136;
static	$RESOURCE_LOCK                 = 137;
static	$RESOURCE_LOCKBY               = 138;
static	$RESOURCE_MYLOCK               = 139;
static	$RESOURCE_HOURS                = 140;
static	$RESOURCE_RSS                  = 141;
static	$RESOURCE_USABILITY            = 142;
static	$RESOURCE_ACCESSIBILITY        = 143;
static	$RESOURCE_SELECTION            = 144;
static	$RESOURCE_OR                   = 145;
static	$RESOURCE_MENU                 = 146;
static	$RESOURCE_ATTACHMENT           = 147;
static	$RESOURCE_PART                 = 148;
static	$RESOURCE_CENTER               = 149;
static	$RESOURCE_ALLCENTER            = 150;
static	$RESOURCE_SCHOOLBAG            = 151;
static	$RESOURCE_ADDSCHOOLBAG         = 152;
static	$RESOURCE_DOCINBAG             = 153;
static	$RESOURCE_WHATODO              = 154;
static	$RESOURCE_BAGACTION            = 155;
static	$RESOURCE_ZIPFILE              = 156;
static	$RESOURCE_IMPORTANCELEVEL      = 157;
static	$RESOURCE_COLORCODE            = 158;
static	$RESOURCE_DELETE               = 159;
static	$RESOURCE_FOLDERLIST           = 160;
static	$RESOURCE_PREFERENCES          = 161;
static	$RESOURCE_PRIVATE              = 162;
static	$RESOURCE_RESET                = 163;
static	$RESOURCE_MULTICENTER          = 164;
static	$RESOURCE_INPUTNEW             = 165;
static	$RESOURCE_INPUTREPLY           = 166;
static	$RESOURCE_INPUTOK              = 167;
static	$RESOURCE_INPUTCANCEL          = 168;
//---------------------------------------------------------------------------
?>