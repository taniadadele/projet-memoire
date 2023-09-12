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
 *		module   : stage.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 09/04/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$STAGE_CLOSEWINDOW          = 0;
static	$STAGE_RESULTS              = 1;
static	$STAGE_FOUND                = 2;
static	$STAGE_NEXTPREV             = 3;
static	$STAGE_COMPANY              = 4;
static	$STAGE_CITY                 = 5;
static	$STAGE_ACTIVITY             = 6;
static	$STAGE_NL                   = 7;
static	$STAGE_MANAGEMENT           = 8;
static	$STAGE_STATUS               = 9;
static	$STAGE_MENU                 = 10;
static	$STAGE_CLOSEMENU            = 11;
static	$STAGE_MODO                 = 12;
static	$STAGE_WRITER               = 13;
static	$STAGE_READER               = 14;
static	$STAGE_NONE                 = 15;
static	$STAGE_DECLARE              = 16;
static	$STAGE_UPDTANNOUNCE         = 17;
static	$STAGE_QUIT                 = 18;
static	$STAGE_FORMFEED             = 19;
static	$STAGE_SECTOR               = 20;
static	$STAGE_COMPNAME             = 21;
static	$STAGE_COMPLAW              = 22;
static	$STAGE_COMPTYPE             = 23;
static	$STAGE_ADDRESS              = 24;
static	$STAGE_ERRZIP               = 25;
static	$STAGE_ZIP                  = 26;
static	$STAGE_TEL                  = 27;
static	$STAGE_FAX                  = 28;
static	$STAGE_DIRECTOR             = 29;
static	$STAGE_MANAGER              = 30;
static	$STAGE_COMPSIZE             = 31;
static	$STAGE_EMPLOYEES            = 32;
static	$STAGE_MAXWORK              = 33;
static	$STAGE_MINOR                = 34;
static	$STAGE_YES                  = 35;
static	$STAGE_NO                   = 36;
static	$STAGE_GIRL                 = 37;
static	$STAGE_HOSTED               = 38;
static	$STAGE_CANEAT               = 39;
static	$STAGE_ISOK                 = 40;
static	$STAGE_COMMENT              = 41;
static	$STAGE_MENATWORK            = 42;
static	$STAGE_UHT                  = 43;
static	$STAGE_MORTGAGE             = 44;
static	$STAGE_SURFACE              = 45;
static	$STAGE_SAU                  = 46;
static	$STAGE_SFP                  = 47;
static	$STAGE_TL                   = 48;
static	$STAGE_RESPONSABILITIES     = 49;
static	$STAGE_VAT                  = 50;
static	$STAGE_COMPTA               = 51;
static	$STAGE_SA                   = 52;
static	$STAGE_ANIMAL               = 53;
static	$STAGE_CATEGORY             = 54;
static	$STAGE_RACE                 = 55;
static	$STAGE_FEMALE               = 56;
static	$STAGE_FUTURE               = 57;
static	$STAGE_UPRA                 = 58;
static	$STAGE_PERF                 = 59;
static	$STAGE_QUALITY              = 60;
static	$STAGE_LEISURE              = 61;
static	$STAGE_COMPETITION          = 62;
static	$STAGE_ELEVAGE              = 63;
static	$STAGE_ADDPRODUCT           = 64;
static	$STAGE_VEGETAL              = 65;
static	$STAGE_AUTO                 = 66;
static	$STAGE_SOLD                 = 67;
static	$STAGE_COMPLEMENT           = 68;
static	$STAGE_SIZE                 = 69;
static	$STAGE_ADD                  = 70;
static	$STAGE_STUDACTIVITY         = 71;
static	$STAGE_SERVICES             = 72;
static	$STAGE_ACCUEIL              = 73;
static	$STAGE_ANIMATION            = 74;
static	$STAGE_RESTO                = 75;
static	$STAGE_OTHER                = 76;
static	$STAGE_NEGOCIATE            = 77;
static	$STAGE_VALID                = 78;
static	$STAGE_LINK                 = 79;
static	$STAGE_NAME                 = 80;
static	$STAGE_MYCOMPANY            = 81;
static	$STAGE_WORKDONE             = 82;
static	$STAGE_COMMENT2             = 83;
static	$STAGE_SEARCH               = 84;
static	$STAGE_COUNTY               = 85;
static	$STAGE_NUMBERS              = 86;
static	$STAGE_FROMTO               = 87;
static	$STAGE_ERASE                = 88;
static	$STAGE_LOCATE               = 89;
static	$STAGE_LUNCHSEARCH          = 90;
static	$STAGE_VISIT                = 91;
static	$STAGE_CHOOSECENTER         = 92;
static	$STAGE_CHOOSECLASS          = 93;
static	$STAGE_ALLCLASS             = 94;
static	$STAGE_NEVCONVENTION        = 95;
static	$STAGE_CREATSTAGE           = 96;
static	$STAGE_PERIOD               = 97;
static	$STAGE_NEXT                 = 98;
static	$STAGE_PREV                 = 99;
static	$STAGE_STUDENT              = 100;
static	$STAGE_LOCATION             = 101;
static	$STAGE_START                = 102;
static	$STAGE_END                  = 103;
static	$STAGE_VISIT                = 104;
static	$STAGE_CANCEL               = 105;
static	$STAGE_CONFIRM              = 106;
static	$STAGE_FILE                 = 107;
static	$STAGE_STAGEPERIOD          = 108;
static	$STAGE_MODIFY               = 109;
static	$STAGE_NBFILE               = 110;
static	$STAGE_BACK2LIST            = 111;
static	$STAGE_ATTRIB               = 112;
static	$STAGE_BACK2SEARCH          = 113;
static	$STAGE_MODIFICATION         = 114;
static	$STAGE_COMMENT3             = 115;
static	$STAGE_PHONE                = 116;
static	$STAGE_RDV                  = 117;
static	$STAGE_VISITEDBY            = 118;
static	$STAGE_CREATBY              = 119;
static	$STAGE_BLACKLISTON          = 120;
static	$STAGE_BLACKLISTOFF         = 121;
static	$STAGE_DELFROMLIST          = 122;
static	$STAGE_NEWSECTOR            = 123;
static	$STAGE_ERASING              = 124;
static	$STAGE_ERRCOMPNAME          = 125;
static	$STAGE_ERRCITY              = 126;
static	$STAGE_ERRDIRECTOR          = 127;
static	$STAGE_DELPROD              = 128;
static	$STAGE_DELCAT               = 129;
static	$STAGE_DELAT                = 130;
static	$STAGE_ADDCMDE              = 131;
static	$STAGE_INSERT               = 132;
static	$STAGE_EMAIL                = 133;
static	$STAGE_WEBSITE              = 134;
static	$STAGE_CVSHOW               = 135;
static	$STAGE_DISCLAIMER           = 136;
static	$STAGE_DATE                 = 137;
static	$STAGE_JOB                  = 138;
static	$STAGE_REGION               = 139;
static	$STAGE_CONTRACT             = 140;
static	$STAGE_HIT                  = 141;
static	$STAGE_CREATCV              = 142;
static	$STAGE_INPUTCV              = 143;
static	$STAGE_COORDINATES          = 144;
static	$STAGE_CAREER               = 145;
static	$STAGE_POSITION             = 146;
static	$STAGE_NAMES                = 147;
static	$STAGE_BORN                 = 148;
static	$STAGE_ADDRESS              = 149;
static	$STAGE_COUNTRY              = 150;
static	$STAGE_SEX                  = 151;
static	$STAGE_EXP                  = 152;
static	$STAGE_DIPLOMA              = 153;
static	$STAGE_LANG                 = 154;
static	$STAGE_OTHER                = 155;
static	$STAGE_AVAILIBILITY         = 156;
static	$STAGE_LOCATION             = 157;
static	$STAGE_SALARY               = 158;
static	$STAGE_PROFILE              = 159;
static	$STAGE_TEXT                 = 160;
static	$STAGE_STATUS               = 161;
static	$STAGE_EXPERIENCE           = 162;
static	$STAGE_UPDATE               = 163;
static	$STAGE_VALIDATE             = 164;
static	$STAGE_QUIT                 = 165;
static	$STAGE_NIL                  = 166;
static	$STAGE_MANDATORY            = 167;
static	$STAGE_BADEMAIL             = 168;
static	$STAGE_BADNAME              = 169;
static	$STAGE_BADDATE              = 170;
static	$STAGE_COMPANYLOC           = 171;
static	$STAGE_TASK                 = 172;
static	$STAGE_START                = 173;
static	$STAGE_END                  = 174;
static	$STAGE_GETTING              = 175;
static	$STAGE_DETAIL               = 176;
static	$STAGE_LANGUAGE             = 177;
static	$STAGE_LEVEL                = 178;
static	$STAGE_FORMAT               = 179;
static	$STAGE_DELETE               = 180;
static	$STAGE_UPDATING             = 181;
static	$STAGE_PUBLISHCV            = 182;
static	$STAGE_SINCE                = 183;
static	$STAGE_HELP                 = 184;
static	$STAGE_BY                   = 185;
static	$STAGE_CVNUMBER             = 186;
static	$STAGE_HOWOLD               = 187;
static	$STAGE_BACKTOLIST           = 188;
static	$STAGE_EXPERTISE            = 189;
static	$STAGE_LISTOFFER            = 190;
static	$STAGE_CREATOFFER           = 191;
static	$STAGE_INPUTOFFER           = 192;
static	$STAGE_PUBLISHOFFER         = 193;
static	$STAGE_DESCOMPANY           = 194;
static	$STAGE_BADCOMPNAME          = 195;
static	$STAGE_BADADDRESS           = 196;
static	$STAGE_DISCLAIMER2          = 197;
static	$STAGE_OFFERNUMBER          = 198;
static	$STAGE_BACKTOFFER           = 199;
static	$STAGE_NEWOFFER             = 200;
static	$STAGE_AUTHORIZATION        = 201;
static	$STAGE_RSS                  = 202;
static	$STAGE_FNAME                = 203;
static	$STAGE_PICTURE              = 204;
static	$STAGE_CLASS                = 205;
static	$STAGE_INPUTNEW             = 206;
static	$STAGE_INPUTREPLY           = 207;
static	$STAGE_INPUTOK              = 208;
static	$STAGE_INPUTCANCEL          = 209;
//---------------------------------------------------------------------------
?>