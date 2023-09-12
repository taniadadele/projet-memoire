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
 *		module   : forum.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 23/03/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$FORUM_FORUMLIST            = 0;
static	$FORUM_ROOTDIR              = 1;
static	$FORUM_DISCLAIMER           = 2;
static	$FORUM_NEW                  = 3;
static	$FORUM_FORUMCREATE          = 4;
static	$FORUM_PARENTDIR            = 5;
static	$FORUM_DIRCREATE            = 6;
static	$FORUM_FORUMS               = 7;
static	$FORUM_MODO                 = 8;
static	$FORUM_WAITING              = 9;
static	$FORUM_MESSAGE              = 10;
static	$FORUM_CLOSEDIR             = 11;
static	$FORUM_OPENDIR              = 12;
static	$FORUM_DIR                  = 13;
static	$FORUM_FORUMUPDATE          = 14;
static	$FORUM_DELDIR               = 15;
static	$FORUM_PRIVATE              = 16;
static	$FORUM_DELFORUM             = 17;
static	$FORUM_SEARCH               = 18;
static	$FORUM_GLOBALRESEARCH       = 19;
static	$FORUM_CHARTE               = 20;
static	$FORUM_CHARTEXT1            = 21;
static	$FORUM_CHARTEXT2            = 22;
static	$FORUM_CHARTEXT3            = 23;
static	$FORUM_CHARTSMILEY          = 24;
static	$FORUM_CHARTBANNER          = 25;
static	$FORUM_BACKTOFORUM          = 26;
static	$FORUM_MANAGEMENT           = 27;
static	$FORUM_MODIFICATION         = 28;
static	$FORUM_STATUS               = 29;
static	$FORUM_DIRECTORY            = 30;
static	$FORUM_IDENT                = 31;
static	$FORUM_CLOSEFORUM           = 32;
static	$FORUM_WRITER               = 33;
static	$FORUM_READER               = 34;
static	$FORUM_NONE                 = 35;
static	$FORUM_PERM                 = 36;
static	$FORUM_SHOW                 = 37;
static	$FORUM_PRIVATEMSG           = 38;
static	$FORUM_ATTACHMENT           = 39;
static	$FORUM_UPDATE               = 40;
static	$FORUM_DELETE               = 41;
static	$FORUM_ISPRIVATE            = 42;
static	$FORUM_AUTOVAL              = 43;
static	$FORUM_FAQ                  = 44;
static	$FORUM_EGROUP               = 45;
static	$FORUM_CHRONO               = 46;
static	$FORUM_CHRONOREV            = 47;
static	$FORUM_FORBIDDEN            = 48;
static	$FORUM_POSTIT               = 49;
static	$FORUM_EMAIL                = 50;
static	$FORUM_HOSTING              = 51;
static	$FORUM_DECLARE              = 52;
static	$FORUM_MODIFYFORUM          = 53;
static	$FORUM_BACKTOLIST           = 54;
static	$FORUM_MYFORUM              = 55;
static	$FORUM_MSGWAITING           = 56;
static	$FORUM_RE                   = 57;
static	$FORUM_NEXT                 = 58;
static	$FORUM_PREV                 = 59;
static	$FORUM_SUBJECT              = 60;
static	$FORUM_AUTHOR               = 61;
static	$FORUM_POSTED               = 62;
static	$FORUM_NOMSG                = 63;
static	$FORUM_NEWFORUM             = 64;
static	$FORUM_DELAY                = 65;
static	$FORUM_NEEDVALIDATION       = 66;
static	$FORUM_PEDAGO               = 67;
static	$FORUM_FORUMPEDAGO          = 68;
static	$FORUM_OTHER                = 69;
static	$FORUM_ERRIDENT             = 70;
static	$FORUM_FORUMIDENT           = 71;
static	$FORUM_DESCRIPTION          = 72;
static	$FORUM_MODIFY               = 73;
static	$FORUM_ADD                  = 74;
static	$FORUM_FORUMACTION          = 75;
static	$FORUM_FORUMANAGEMENT       = 76;
static	$FORUM_ERRDIR               = 77;
static	$FORUM_DIRNAME              = 78;
static	$FORUM_CLOSELINK            = 79;
static	$FORUM_CREATE               = 80;
static	$FORUM_DIRACTION            = 81;
static	$FORUM_FORMPOST             = 82;
static	$FORUM_IMPORT               = 83;
static	$FORUM_WARNING              = 84;
static	$FORUM_SENDOK               = 85;
static	$FORUM_THANX                = 86;
static	$FORUM_BACK                 = 87;
static	$FORUM_ERRSUBJECT           = 88;
static	$FORUM_HUMEUR               = 89;
static	$FORUM_ERRMESSAGE           = 90;
static	$FORUM_HELP                 = 91;
static	$FORUM_ATTDESCRIPTION       = 92;
static	$FORUM_POSTER               = 93;
static	$FORUM_MESSAGE              = 94;
static	$FORUM_ANNOUNCE             = 95;
static	$FORUM_INCLUDE              = 96;
static	$FORUM_SIGNATURE            = 97;
static	$FORUM_SENDMESSAGE          = 98;
static	$FORUM_CLICK                = 99;
static	$FORUM_REPLY                = 100;
static	$FORUM_MODIFIED             = 101;
static	$FORUM_ERASING              = 102;
static	$FORUM_DELMESSAGE           = 103;
static	$FORUM_UPDATEMESSAGE        = 104;
static	$FORUM_TITLE                = 105;
static	$FORUM_MESSAGES             = 106;
static	$FORUM_DELATTACHMENT        = 107;
static	$FORUM_SIZE                 = 108;
static	$FORUM_DOC                  = 109;
static	$FORUM_POSTEDON             = 110;
static	$FORUM_REPLIEDON            = 111;
static	$FORUM_HIDEPOSTER           = 112;
static	$FORUM_SHOWPOSTER           = 113;
static	$FORUM_CRUNCH               = 114;
static	$FORUM_DEPLOY               = 115;
static	$FORUM_MODOF                = 116;
static	$FORUM_MODOM                = 117;
static	$FORUM_NOMODO               = 118;
static	$FORUM_NOFORUM              = 119;
static	$FORUM_ACCESSCHART          = 120;
static	$FORUM_NEWSUBJECT           = 121;
static	$FORUM_NEXTPREV             = 122;
static	$FORUM_SUBJECTS             = 123;
static	$FORUM_DATE                 = 124;
static	$FORUM_HIT                  = 125;
static	$FORUM_REPLIES              = 126;
static	$FORUM_RECENTMSG            = 127;
static	$FORUM_SEEMSG               = 128;
static	$FORUM_MSGLIST              = 129;
static	$FORUM_FORUMSEARCH          = 130;
static	$FORUM_INSERT               = 131;
static	$FORUM_BY                   = 132;
static	$FORUM_MONTH                = 133;
static	$FORUM_MAILCOPY             = 134;
static	$FORUM_REGISTER             = 135;
static	$FORUM_UNREGISTER           = 136;
static	$FORUM_CREATBY              = 137;
static	$FORUM_LANG                 = 138;
static	$FORUM_REGUSERS             = 139;
static	$FORUM_GROUP                = 140;
static	$FORUM_CENTER               = 141;
static	$FORUM_REGISTERED           = 142;
static	$FORUM_CLOSEWINDOW          = 143;
static	$FORUM_WROTE                = 144;
static	$FORUM_CENSORING            = 145;
static	$FORUM_CENSOR               = 146;
static	$FORUM_MULTILINGUAL         = 147;
static	$FORUM_LASTPOST             = 148;
static	$FORUM_RSS                  = 149;
static	$FORUM_DOWNLOADED           = 150;
static	$FORUM_MOVE                 = 151;
static	$FORUM_GOTO                 = 152;
static	$FORUM_UNCENSOR             = 153;
static	$FORUM_RESET                = 154;
static	$FORUM_OPENFORUM            = 155;
static	$FORUM_INPUTNEW             = 156;
static	$FORUM_INPUTREPLY           = 157;
static	$FORUM_INPUTOK              = 158;
static	$FORUM_INPUTCANCEL          = 159;
//---------------------------------------------------------------------------
?>