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
 *		module   : postit.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 24/03/2007
 *		modif    :
 *
 */

//---------------------------------------------------------------------------
static	$POSTIT_MSGSYS               = 0;
static	$POSTIT_EXP                  = 1;
static	$POSTIT_POSTED               = 2;
static	$POSTIT_ACK                  = 3;
static	$POSTIT_FORMFEED             = 4;
static	$POSTIT_THANX                = 5;
static	$POSTIT_ERRDOWNLOAD          = 6;
static	$POSTIT_SENDFAIL             = 7;
static	$POSTIT_NODEST               = 8;
static	$POSTIT_GOBACK               = 9;
static	$POSTIT_CENTER               = 10;
static	$POSTIT_WARNING              = 11;
static	$POSTIT_ERRDEST              = 12;
static	$POSTIT_DEST                 = 13;
static	$POSTIT_CHOOSECAT            = 14;
static	$POSTIT_CHOOSEDEST           = 15;
static	$POSTIT_LIST                 = 16;
static	$POSTIT_CHOOSELIST           = 17;
static	$POSTIT_LIDI                 = 18;
static	$POSTIT_DELIST               = 19;
static	$POSTIT_UPDATELIST           = 20;
static	$POSTIT_ADDLIST              = 21;
static	$POSTIT_ERRSUBJECT           = 22;
static	$POSTIT_SUBJECT              = 23;
static	$POSTIT_ERRMSG               = 24;
static	$POSTIT_HELP                 = 25;
static	$POSTIT_ATTACHMENT           = 26;
static	$POSTIT_ADDATTACHMENT        = 27;
static	$POSTIT_INCLUDE              = 28;
static	$POSTIT_SIGNATURE            = 29;
static	$POSTIT_SENDPOST             = 30;
static	$POSTIT_QUIT                 = 31;
static	$POSTIT_FORMLIDI             = 32;
static	$POSTIT_STATUS               = 33;
static	$POSTIT_MODIFICATION         = 34;
static	$POSTIT_APPEND               = 35;
static	$POSTIT_ADDRESSBOOK          = 36;
static	$POSTIT_ERRNOLIST            = 37;
static	$POSTIT_NAMELIST             = 38;
static	$POSTIT_SENDACK              = 39;
static	$POSTIT_PUBLICLIST           = 40;
static	$POSTIT_CANUSE               = 41;
static	$POSTIT_SENDMAIL             = 42;
static	$POSTIT_LISTMEMBER           = 43;
static	$POSTIT_STUDENT              = 44;
static	$POSTIT_USER                 = 45;
static	$POSTIT_VALIDATE             = 46;
static	$POSTIT_POSTITLIST           = 47;
static	$POSTIT_DISCLAIMER           = 48;
static	$POSTIT_NEW                  = 49;
static	$POSTIT_NEXTPREV             = 50;
static	$POSTIT_MESSAGE              = 51;
static	$POSTIT_VISUALIZESND         = 52;
static	$POSTIT_VISUALIZE            = 53;
static	$POSTIT_VISUALIZERCV         = 54;
static	$POSTIT_EXPDST               = 55;
static	$POSTIT_MSGACK               = 56;
static	$POSTIT_ATTACHED             = 57;
static	$POSTIT_SENT                 = 58;
static	$POSTIT_RECEIVED             = 59;
static	$POSTIT_NOTVISUALIZED        = 60;
static	$POSTIT_HIT                  = 61;
static	$POSTIT_NOTACK               = 62;
static	$POSTIT_NEXT                 = 63;
static	$POSTIT_PREV                 = 64;
static	$POSTIT_SEARCH               = 65;
static	$POSTIT_WAITING              = 66;
static	$POSTIT_MANAGEMENT           = 67;
static	$POSTIT_FAIL                 = 68;
static	$POSTIT_WRITER               = 69;
static	$POSTIT_READER               = 70;
static	$POSTIT_QUOTAS               = 71;
static	$POSTIT_EXPIRE               = 72;
static	$POSTIT_RECORD               = 73;
static	$POSTIT_BACKHOME             = 74;
static	$POSTIT_SHOWPOSTIT           = 75;
static	$POSTIT_CLICK                = 76;
static	$POSTIT_INVISIBLE            = 77;
static	$POSTIT_VISIBLE              = 78;
static	$POSTIT_DOC                  = 79;
static	$POSTIT_NA                   = 80;
static	$POSTIT_SEND                 = 81;
static	$POSTIT_REPLY                = 82;
static	$POSTIT_BYTE                 = 83;
static	$POSTIT_ERASE                = 84;
static	$POSTIT_REPLIED              = 85;
static	$POSTIT_ATTDESCRIPTION       = 86;
static	$POSTIT_ATTNUMBER            = 87;
static	$POSTIT_CHOOSEGROUP          = 88;
static	$POSTIT_LISTAUTO             = 89;
static	$POSTIT_LISTPERSO            = 90;
static	$POSTIT_TOALL                = 91;
static	$POSTIT_WEEK                 = 92;
static	$POSTIT_MBYTE                = 93;
static	$POSTIT_ABSENT               = 94;
static	$POSTIT_URGENT               = 95;
static	$POSTIT_COMPANY              = 96;
static	$POSTIT_NAME                 = 97;
static	$POSTIT_ADDRESS              = 98;
static	$POSTIT_TEL                  = 99;
static	$POSTIT_EMAIL                = 100;
static	$POSTIT_SEX                  = 101;
static	$POSTIT_PERSON               = 102;
static	$POSTIT_LIST                 = 103;
static	$POSTIT_PRIORITY             = 104;
static	$POSTIT_PRIORITYLEVEL        = 105;
static	$POSTIT_ERRNAME              = 106;
static	$POSTIT_MSGPRIORITY          = 107;
static	$POSTIT_NBMSG                = 108;
static	$POSTIT_PARENTDIR            = 109;
static	$POSTIT_ROOTDIR              = 110;
static	$POSTIT_NEWDIR               = 111;
static	$POSTIT_CREATE               = 112;
static	$POSTIT_MOVE                 = 113;
static	$POSTIT_MOVINTO              = 114;
static	$POSTIT_OR                   = 115;
static	$POSTIT_CANCELMSG            = 116;
static	$POSTIT_FORWARD              = 117;
static	$POSTIT_FWD                  = 118;
static	$POSTIT_INPUTNEW             = 119;
static	$POSTIT_INPUTREPLY           = 120;
static	$POSTIT_INPUTOK              = 121;
static	$POSTIT_INPUTCANCEL          = 122;
static	$POSTIT_USER                 = 123;
static	$POSTIT_CLASS                = 124;
static	$POSTIT_GROUP	             = 125;
static	$POSTIT_TYPE	             = 126;
static	$POSTIT_TYPE1	             = 127;
static	$POSTIT_TYPE2	             = 128;
static	$POSTIT_TYPE3	             = 129;
static	$POSTIT_DIFF	             = 130;
static	$POSTIT_MAILREQUIRE          = 131;
static	$POSTIT_CLASSPROF            = 132;
static	$POSTIT_MATPROF              = 133;
static	$POSTIT_ADDFILE              = 134;
static	$POSTIT_CANCEL               = 135;
static	$POSTIT_DELETE               = 136;
static	$POSTIT_ADMIN                = 137;
static	$POSTIT_ENS                  = 138;
static	$POSTIT_COLLEGE              = 139;
static	$POSTIT_LYCEE                = 140;
static	$POSTIT_PREV                 = 141;
static	$POSTIT_NEXT                 = 142;
//---------------------------------------------------------------------------
?>
