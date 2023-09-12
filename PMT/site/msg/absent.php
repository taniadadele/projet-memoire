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
 *		module   : absent.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 07/04/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$ABSENT_APPEND               = 0;
static	$ABSENT_UPDATE               = 1;
static	$ABSENT_STATUS               = 2;
static	$ABSENT_STUDENT              = 3;
static	$ABSENT_WEEK                 = 4;
static	$ABSENT_TIME                 = 5;
static	$ABSENT_MOTIF                = 6;
static	$ABSENT_NOTES                = 7;
static	$ABSENT_CLOSEWINDOW          = 8;
static	$ABSENT_SEXM                 = 9;
static	$ABSENT_SEXF                 = 10;
static	$ABSENT_FROMTO               = 11;
static	$ABSENT_MANAGEMENT           = 12;
static	$ABSENT_FAILED               = 13;
static	$ABSENT_MODIFICATION         = 14;
static	$ABSENT_CENTER               = 15;
static	$ABSENT_GROUPS               = 16;
static	$ABSENT_MODO                 = 17;
static	$ABSENT_WRITER               = 18;
static	$ABSENT_READER               = 19;
static	$ABSENT_NONE                 = 20;
static	$ABSENT_TEMPLATE             = 21;
static	$ABSENT_DECLARE              = 22;
static	$ABSENT_RECORD               = 23;
static	$ABSENT_GOHOME               = 24;
static	$ABSENT_FORMFEED             = 25;
static	$ABSENT_THANX                = 26;
static	$ABSENT_BACK2LIST            = 27;
static	$ABSENT_CHOOSECENTER         = 28;
static	$ABSENT_CHOOSECAT            = 29;
static	$ABSENT_AUTHOR               = 30;
static	$ABSENT_FROM                 = 31;
static	$ABSENT_TO                   = 32;
static	$ABSENT_ERRTEXT              = 33;
static	$ABSENT_ABSENT               = 34;
static	$ABSENT_MESSAGE              = 35;
static	$ABSENT_PRIORITY             = 36;
static	$ABSENT_SHOWHOME             = 37;
static	$ABSENT_SHOWCAL              = 38;
static	$ABSENT_GOBACK               = 39;
static	$ABSENT_LIST                 = 40;
static	$ABSENT_SUBJECT              = 41;
static	$ABSENT_CHOOSEGROUP          = 42;
static	$ABSENT_NEW                  = 43;
static	$ABSENT_NAME                 = 44;
static	$ABSENT_ALL                  = 45;
static	$ABSENT_CHOOSECLASS          = 46;
static	$ABSENT_ALL2                 = 47;
static	$ABSENT_SENDMAIL             = 48;
static	$ABSENT_SENT                 = 49;
static	$ABSENT_LIST2                = 50;
static	$ABSENT_ALLCAT               = 51;
static	$ABSENT_ADDABSENT            = 52;
static	$ABSENT_VISIBLE              = 53;
static	$ABSENT_INVISIBLE            = 54;
static	$ABSENT_DELETE               = 55;
static	$ABSENT_UPDT                 = 56;
static	$ABSENT_SENTBY               = 57;
static	$ABSENT_ARCHIVE              = 58;
static	$ABSENT_DELYEAR              = 59;
static	$ABSENT_MONTHFULL            = 60;
static	$ABSENT_MONTH                = 61;
static	$ABSENT_HOUR                 = 62;
static	$ABSENT_HELP                 = 63;
static	$ABSENT_COURSE               = 64;
static	$ABSENT_CLASS                = 65;
static	$ABSENT_DELAY                = 66;
static	$ABSENT_SENDSMS              = 67;
static	$ABSENT_SMS                  = 68;
static	$ABSENT_CERTIFY              = 69;
static	$ABSENT_ISOK                 = 70;
static	$ABSENT_TOTAL                = 71;
static	$ABSENT_HALFDAY              = 72;
static	$ABSENT_DAYS                 = 73;
static	$ABSENT_JUSTIFIED            = 74;
static	$ABSENT_NOTJUSTIFIED         = 75;
static	$ABSENT_DISPLAY              = 76;
static	$ABSENT_DISPLAYLIST          = 77;
static	$ABSENT_CURRWEEK             = 78;
static	$ABSENT_INFARMERY            = 79;
static	$ABSENT_STATYEAR             = 80;
static	$ABSENT_ADDVIEW              = 81;
static	$ABSENT_RECORDED             = 82;
static	$ABSENT_AUTHORIZATION        = 83;
static	$ABSENT_AUTOVAL              = 84;
static	$ABSENT_JUSTIFY              = 85;
static	$ABSENT_COMMENTS             = 86;
static	$ABSENT_RESERVED             = 87;
static	$ABSENT_WORKTODO             = 88;
static	$ABSENT_GRANTED              = 89;
static	$ABSENT_REJECTED             = 90;
static	$ABSENT_ATTACHED             = 91;
static	$ABSENT_DOWNLOAD             = 92;
static	$ABSENT_STATUSLIST           = 93;
static	$ABSENT_WARNING              = 94;
static	$ABSENT_WARNINGLIST          = 95;
static	$ABSENT_NOTE                 = 96;
static	$ABSENT_SUBJECT              = 97;
static	$ABSENT_PENDING              = 98;
static	$ABSENT_CONFIRM              = 99;
static	$ABSENT_MATTER               = 100;
static	$ABSENT_INPUTNEW             = 101;
static	$ABSENT_INPUTREPLY           = 102;
static	$ABSENT_INPUTOK              = 103;
static	$ABSENT_INPUTCANCEL          = 104;
static	$ABSENT_JUSTIFIER            = 105;
static	$ABSENT_DEJUSTIFIER          = 106;
static	$ABSENT_REEL                 = 107;
static	$ABSENT_PREVISU              = 108;
static	$ABSENT_ALLCLASS             = 109;
static	$ABSENT_ABSENCE              = 110;
static	$ABSENT_TTSEL                = 111;
static	$ABSENT_OU                   = 112;
static	$ABSENT_REGROUPEMENT         = 113;
static	$ABSENT_RETOURGEN            = 114;
static	$ABSENT_PERIODICITE          = 115;
static	$ABSENT_PERIODICITETEXT      = 116;
static	$ABSENT_ABSENCENC            = 117;
static	$ABSENT_NC                   = 118;
static	$ABSENT_AJOUT                = 119;
static	$ABSENT_MODIF                = 120;
static	$ABSENT_INFODUPLI            = 121;
static	$ABSENT_INFOEXTEND           = 122;
static	$ABSENT_INFOCOCHE            = 123;
//---------------------------------------------------------------------------
?>
