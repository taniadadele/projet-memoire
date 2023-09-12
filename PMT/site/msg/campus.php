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
 *		module   : campus.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 27/03/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$CAMPUS_FORMLINK             = 0;
static	$CAMPUS_UPDATELINK           = 1;
static	$CAMPUS_NEWLINK              = 2;
static	$CAMPUS_INSERT               = 3;
static	$CAMPUS_MODIFICATION         = 4;
static	$CAMPUS_STATUS               = 5;
static	$CAMPUS_AUTHOR               = 6;
static	$CAMPUS_ERRIDENT             = 7;
static	$CAMPUS_SITENAME             = 8;
static	$CAMPUS_DESCRIPTION          = 9;
static	$CAMPUS_ERRURL               = 10;
static	$CAMPUS_URL                  = 11;
static	$CAMPUS_LANG                 = 12;
static	$CAMPUS_LICENSE              = 13;
static	$CAMPUS_LINKUPDATE           = 14;
static	$CAMPUS_LINKINSERT           = 15;
static	$CAMPUS_BACK2CAMPUS          = 16;
static	$CAMPUS_ALLTHEMES            = 17;
static	$CAMPUS_ADDANNOUNCE          = 18;
static	$CAMPUS_VALIDATE             = 19;
static	$CAMPUS_CLASSLIST            = 20;
static	$CAMPUS_CHOOSECENTER         = 21;
static	$CAMPUS_CHOOSECAT            = 22;
static	$CAMPUS_CLASS                = 23;
static	$CAMPUS_PRIMTEACHER          = 24;
static	$CAMPUS_COORDTEACHER         = 25;
static	$CAMPUS_NUMBERS              = 26;
static	$CAMPUS_TOTAL                = 27;
static	$CAMPUS_ISOK                 = 28;
static	$CAMPUS_CHOOSECOURSE         = 29;
static	$CAMPUS_HOME                 = 30;
static	$CAMPUS_INVISIBLE            = 31;
static	$CAMPUS_VISIBLE              = 32;
static	$CAMPUS_REPOSITORY           = 33;
static	$CAMPUS_DOWNLOAD             = 34;
static	$CAMPUS_DOC                  = 35;
static	$CAMPUS_ONLINE               = 36;
static	$CAMPUS_UPLOAD               = 37;
static	$CAMPUS_LOCATION             = 38;
static	$CAMPUS_ADDOC                = 39;
static	$CAMPUS_ERRSIZE              = 40;
static	$CAMPUS_ERRLOC               = 41;
static	$CAMPUS_ERRFILETYPE          = 42;
static	$CAMPUS_VALID                = 43;
static	$CAMPUS_UPLOADTEXT           = 44;
static	$CAMPUS_UPLOADWORK           = 45;
static	$CAMPUS_WORKS                = 46;
static	$CAMPUS_DONE                 = 47;
static	$CAMPUS_SIZE                 = 48;
static	$CAMPUS_DELWORK              = 49;
static	$CAMPUS_BACK                 = 50;
static	$CAMPUS_MANAGEMENT           = 51;
static	$CAMPUS_IDENT                = 52;
static	$CAMPUS_CLOSE                = 53;
static	$CAMPUS_MODO                 = 54;
static	$CAMPUS_WRITER               = 55;
static	$CAMPUS_READER               = 56;
static	$CAMPUS_DECLARE              = 57;
static	$CAMPUS_UPDATECAMPUS         = 58;
static	$CAMPUS_QUIT                 = 59;
static	$CAMPUS_MODOF                = 60;
static	$CAMPUS_MODOM                = 61;
static	$CAMPUS_NOMODO               = 62;
static	$CAMPUS_LINKLIST             = 63;
static	$CAMPUS_ROOTDIR              = 64;
static	$CAMPUS_DISCLAIMER           = 65;
static	$CAMPUS_ADDIR                = 66;
static	$CAMPUS_NEWDIR               = 67;
static	$CAMPUS_PARENTDIR            = 68;
static	$CAMPUS_DELDIR               = 69;
static	$CAMPUS_UPDATELINK           = 70;
static	$CAMPUS_CLOSEDIR             = 71;
static	$CAMPUS_OPENDIR              = 72;
static	$CAMPUS_DELLINK              = 73;
static	$CAMPUS_ADDLINK              = 74;
static	$CAMPUS_WORKMANAGEMENT       = 75;
static	$CAMPUS_DIR                  = 76;
static	$CAMPUS_DIRCLOSE             = 77;
static	$CAMPUS_MODIFY               = 78;
static	$CAMPUS_CREATE               = 79;
static	$CAMPUS_APPEND               = 80;
static	$CAMPUS_DIRACTION            = 81;
static	$CAMPUS_BACK2WORK            = 82;
static	$CAMPUS_FORMUPLOAD           = 83;
static	$CAMPUS_WORKMODIFY           = 84;
static	$CAMPUS_WORKNEW              = 85;
static	$CAMPUS_NOFILE               = 86;
static	$CAMPUS_UPLOADNAME           = 87;
static	$CAMPUS_FILESIZE             = 88;
static	$CAMPUS_ERRFILE              = 89;
static	$CAMPUS_NOPERM               = 90;
static	$CAMPUS_EEXIST               = 91;
static	$CAMPUS_WARNING              = 92;
static	$CAMPUS_CLICKHERE            = 93;
static	$CAMPUS_UPLOADATE            = 94;
static	$CAMPUS_BEGINUPLOAD          = 95;
static	$CAMPUS_ENDUPLOAD            = 96;
static	$CAMPUS_FMTDATE              = 97;
static	$CAMPUS_BEGIN                = 98;
static	$CAMPUS_END                  = 99;
static	$CAMPUS_TARGET               = 100;
static	$CAMPUS_UPDTACTION           = 101;
static	$CAMPUS_ECAMPUS              = 102;
static	$CAMPUS_MENU                 = 103;
static	$CAMPUS_OPTIONS              = 104;
static	$CAMPUS_WORKLIST             = 105;
static	$CAMPUS_LISTBELOW            = 106;
static	$CAMPUS_ALLCLASS             = 107;
static	$CAMPUS_CREATEDIR            = 108;
static	$CAMPUS_CREATEWORK           = 109;
static	$CAMPUS_WRKLABEL             = 110;
static	$CAMPUS_REPLABEL             = 111;
static	$CAMPUS_HITLABEL             = 112;
static	$CAMPUS_DNELABEL             = 113;
static	$CAMPUS_UPDATEWORK           = 114;
static	$CAMPUS_DELWORK              = 115;
static	$CAMPUS_NOLIMIT              = 116;
static	$CAMPUS_CONFIG               = 117;
static	$CAMPUS_EXIT                 = 118;
static	$CAMPUS_BYTE                 = 119;
static	$CAMPUS_CORRECTION           = 120;
static	$CAMPUS_TRANSFERT            = 121;
static	$CAMPUS_ADDACTION            = 122;
static	$CAMPUS_ERASE                = 123;
static	$CAMPUS_CHOOSECLASS          = 124;
static	$CAMPUS_NBDIR                = 125;
static	$CAMPUS_NBFILE               = 126;
static	$CAMPUS_GETLICENSE           = 127;
static	$CAMPUS_LIMIT                = 128;
static	$CAMPUS_AUTHORIZATION        = 129;
static	$CAMPUS_PRIVATE              = 130;
static	$CAMPUS_ERROR                = 131;
static	$CAMPUS_ACCESSBY             = 132;
static	$CAMPUS_MANDATORY            = 133;
static	$CAMPUS_HELP                 = 134;
static	$CAMPUS_ISPRIVATE            = 135;
static	$CAMPUS_YES                  = 136;
static	$CAMPUS_NO                   = 137;
static	$CAMPUS_INSCRIPTION          = 138;
static	$CAMPUS_VALIDITY             = 139;
static	$CAMPUS_AUTO                 = 140;
static	$CAMPUS_IMAGE                = 141;
static	$CAMPUS_NEW                  = 142;
static	$CAMPUS_MEMBERACCESS         = 143;
static	$CAMPUS_REGISTER             = 144;
static	$CAMPUS_WAITING              = 145;
static	$CAMPUS_UNREGISTER           = 146;
static	$CAMPUS_INVITATION           = 147;
static	$CAMPUS_MEMBERLIST           = 148;
static	$CAMPUS_NAME                 = 149;
static	$CAMPUS_REGSINCE             = 150;
static	$CAMPUS_LASTACCESS           = 151;
static	$CAMPUS_PUBLISHCV            = 152;
static	$CAMPUS_ACCESSCV             = 153;
static	$CAMPUS_NBMEMBER             = 154;
static	$CAMPUS_ALLSTATUS            = 155;
static	$CAMPUS_OWNER                = 156;
static	$CAMPUS_VALUPDATE            = 157;
static	$CAMPUS_SUBJECT              = 158;
static	$CAMPUS_REQUEST              = 159;
static	$CAMPUS_SUBJECT2             = 160;
static	$CAMPUS_REQUEST2             = 161;
static	$CAMPUS_CENTER               = 162;
static	$CAMPUS_PROMPT               = 163;
static	$CAMPUS_REVOKE               = 164;
static	$CAMPUS_SUBSCRIPTION         = 165;
static	$CAMPUS_DELATTACH            = 166;
static	$CAMPUS_SORTBY               = 167;
static	$CAMPUS_SORTLIST             = 168;
static	$CAMPUS_WORKDONE             = 169;
static	$CAMPUS_LOCK                 = 170;
static	$CAMPUS_UNLOCK               = 171;
static	$CAMPUS_AUTHREADING          = 172;
static	$CAMPUS_RESET                = 173;
static	$CAMPUS_INPUTNEW             = 174;
static	$CAMPUS_INPUTREPLY           = 175;
static	$CAMPUS_INPUTOK              = 176;
static	$CAMPUS_INPUTCANCEL          = 177;
//---------------------------------------------------------------------------
?>