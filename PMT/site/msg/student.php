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
 *		module   : student.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 21/03/2007
 *		modif    :
 *
 */

//---------------------------------------------------------------------------
static	$STUDENT_ACL               = 0;
static	$STUDENT_CHOOSECENTER      = 1;
static	$STUDENT_CHOOSECATEGORY    = 2;
static	$STUDENT_GROUP             = 3;
static	$STUDENT_CONNECTED         = 4;
static	$STUDENT_DISCONNECT        = 5;
static	$STUDENT_LASTCONNECT       = 6;
static	$STUDENT_CLOSEWINDOW       = 7;
static	$STUDENT_CHOOSECLASS       = 8;
static	$STUDENT_ALLCLASSES        = 9;
static	$STUDENT_SWAP              = 10;
static	$STUDENT_SWAPAGAIN         = 11;
static	$STUDENT_GIVELOGIN         = 12;
static	$STUDENT_VALIDATE          = 13;
static	$STUDENT_ALLCATEGORY       = 14;
static	$STUDENT_LOGIN             = 15;
static	$STUDENT_CANTREACH         = 16;
static	$STUDENT_DOINSTALL         = 17;
static	$STUDENT_CREATEACCOUNT     = 18;
static	$STUDENT_NOPERM            = 19;
static	$STUDENT_ACCOUNTCLOSE      = 20;
static	$STUDENT_NOTVALID          = 21;
static	$STUDENT_PASSWORD          = 22;
static	$STUDENT_USERID            = 23;
static	$STUDENT_CLICKHERE         = 24;
static	$STUDENT_USERACCOUNT       = 25;
static	$STUDENT_ERRINPUT          = 26;
static	$STUDENT_BADEMAIL          = 27;
static	$STUDENT_BADID             = 28;
static	$STUDENT_THANX             = 29;
static	$STUDENT_WARNING           = 30;
static	$STUDENT_CHS               = 31;
static	$STUDENT_NOLIMIT           = 32;
static	$STUDENT_DELAY             = 33;
static	$STUDENT_PICTURE           = 34;
static	$STUDENT_UPDATEOK          = 35;
static	$STUDENT_BACK              = 36;
static	$STUDENT_SESSION           = 37;
static	$STUDENT_DISCONNECTED      = 38;
static	$STUDENT_TIMEOUT           = 39;
static	$STUDENT_ADDRECORD         = 40;
static	$STUDENT_CHOOSEPERIOD      = 41;
static	$STUDENT_STUDENTSTATUS     = 42;
static	$STUDENT_GRANTHOLDER       = 43;
static	$STUDENT_LOGIN             = 44;
static	$STUDENT_PORTFOLIO         = 45;
static	$STUDENT_B2I               = 46;
static	$STUDENT_STAGE             = 47;
static	$STUDENT_ABSENT            = 48;
static	$STUDENT_MYCLASS           = 49;
static	$STUDENT_NAME              = 50;
static	$STUDENT_MYDATE            = 51;
static	$STUDENT_POSTIT            = 52;
static	$STUDENT_REPRESENTATIVE    = 53;
static	$STUDENT_NEXT              = 54;
static	$STUDENT_STATUS            = 55;
static	$STUDENT_PREV              = 56;
static	$STUDENT_SKIN              = 57;
static	$STUDENT_SKINTEXT          = 58;
static	$STUDENT_MENU              = 59;
static	$STUDENT_PUCE              = 60;
static	$STUDENT_BGROUND           = 61;
static	$STUDENT_TUNING            = 62;
static	$STUDENT_WORKSPACE         = 63;
static	$STUDENT_BLOLG             = 64;
static	$STUDENT_MAIL              = 65;
static	$STUDENT_DIARY             = 66;
static	$STUDENT_ADDRESSBOOK       = 67;
static	$STUDENT_CONFIRM           = 68;
static	$STUDENT_GOBACK            = 69;
static	$STUDENT_STUDENTACCOUNT    = 70;
static	$STUDENT_ERRINPUT          = 71;
static	$STUDENT_FNAME             = 72;
static	$STUDENT_SEX               = 73;
static	$STUDENT_CENTER            = 74;
static	$STUDENT_VALIDATION        = 75;
static	$STUDENT_BORN              = 76;
static	$STUDENT_DATE              = 77;
static	$STUDENT_ADDRESS           = 78;
static	$STUDENT_CITY              = 79;
static	$STUDENT_PHONE             = 80;
static	$STUDENT_BADEMAIL          = 81;
static	$STUDENT_EMAIL             = 82;
static	$STUDENT_REGIME            = 83;
static	$STUDENT_BOURSE            = 84;
static	$STUDENT_DELEGUE           = 85;
static	$STUDENT_YES               = 86;
static	$STUDENT_NO                = 87;
static	$STUDENT_NEVERCONNECT      = 88;
static	$STUDENT_BAKTOLIST         = 89;
static	$STUDENT_ADDRECORD         = 90;
static	$STUDENT_ROLE              = 91;
static	$STUDENT_ALLCENTER         = 92;
static	$STUDENT_ACCOUNTOPEN       = 93;
static	$STUDENT_ACCOUNTCLOSE      = 94;
static	$STUDENT_LASTCONNEXION     = 95;
static	$STUDENT_TITLE             = 96;
static	$STUDENT_FUNCTION          = 97;
static	$STUDENT_VALIDITY          = 98;
static	$STUDENT_WAITVALIDATION    = 99;
static	$STUDENT_UPDATE            = 100;
static	$STUDENT_RIGHTS            = 101;
static	$STUDENT_INSCRIPTION       = 102;
static	$STUDENT_CNX               = 103;
static	$STUDENT_CONNECTO          = 104;
static	$STUDENT_NBMSG             = 105;
static	$STUDENT_NBRESOURCE        = 106;
static	$STUDENT_NBCNX             = 107;
static	$STUDENT_MINPASSWD         = 108;
static	$STUDENT_NOPASSWD          = 109;
static	$STUDENT_SHOW              = 110;
static	$STUDENT_HIDE              = 111;
static	$STUDENT_SIGNATURE         = 112;
static	$STUDENT_AVATAR            = 113;
static	$STUDENT_ACTIVITY          = 114;
static	$STUDENT_CATEGORY          = 115;
static	$STUDENT_GRANTED           = 116;
static	$STUDENT_BLACKLIST         = 117;
static	$STUDENT_REFRESH           = 118;
static	$STUDENT_LIST1             = 119;
static	$STUDENT_LIST2             = 120;
static	$STUDENT_DELSTUDENT        = 121;
static	$STUDENT_SUBJECT           = 122;
static	$STUDENT_TEXTBOOK          = 123;
static	$STUDENT_LASTACCESS        = 124;
static	$STUDENT_LOGOUT            = 125;
static	$STUDENT_LASTCLICK         = 126;
static	$STUDENT_USRTIME1          = 127;
static	$STUDENT_USRTIME2          = 128;
static	$STUDENT_MODIFICATION      = 129;
static	$STUDENT_NEWRECORD         = 130;
static	$STUDENT_MANDATORY         = 131;
static	$STUDENT_MALE              = 132;
static	$STUDENT_FEMALE            = 133;
static	$STUDENT_ANONYMOUS         = 134;
static	$STUDENT_MORE              = 135;
static	$STUDENT_LIST3             = 136;
static	$STUDENT_YEARMONTH         = 137;
static	$STUDENT_MONTHDAYS         = 138;
static	$STUDENT_DAYSHOUR          = 139;
static	$STUDENT_HOURMIN           = 140;
static	$STUDENT_MINSEC            = 141;
static	$STUDENT_SECONDS           = 142;
static	$STUDENT_LANG              = 143;
static	$STUDENT_CREATACCOUNT      = 144;
static	$STUDENT_WAITOPEN          = 145;
static	$STUDENT_AUTOVAL           = 146;
static	$STUDENT_AUTODEL           = 147;
static	$STUDENT_SENDPWD           = 148;
static	$STUDENT_BODY              = 149;
static	$STUDENT_LOSTPWD           = 150;
static	$STUDENT_NOSOUCY           = 151;
static	$STUDENT_SENDEMAIL         = 152;
static	$STUDENT_BURNOUT           = 153;
static	$STUDENT_DELUSER           = 154;
static	$STUDENT_CHANGECLASS       = 155;
static	$STUDENT_CHANGECENTER      = 156;
static	$STUDENT_TEL               = 157;
static	$STUDENT_CV                = 158;
static	$STUDENT_OFFER             = 159;
static	$STUDENT_MYNAME            = 160;
static	$STUDENT_GRANT             = 161;
static	$STUDENT_DENIED            = 162;
static	$STUDENT_FILTER            = 163;
static	$STUDENT_PASWDMANAGEMENT   = 164;
static	$STUDENT_FORMAT            = 165;
static	$STUDENT_PASSWDSET         = 166;
static	$STUDENT_GROUPS            = 167;
static	$STUDENT_EMPTYPASSWD       = 168;
static	$STUDENT_ACCOUNTUPDATE     = 169;
static	$STUDENT_GENERATE          = 170;
static	$STUDENT_NOTVALIDATE       = 171;
static	$STUDENT_SELECTDATE        = 172;
static	$STUDENT_SELECTRIGHT       = 173;
static	$STUDENT_AWAITING          = 174;
static	$STUDENT_AWAITBODYTEXT     = 175;
static	$STUDENT_NBCONNECT         = 176;
static	$STUDENT_SUBJECTS          = 177;
static	$STUDENT_YEARSOLD          = 178;
static	$STUDENT_DELETE            = 179;
static	$STUDENT_OPTIONS           = 180;
static	$STUDENT_USERSTATUS        = 181;
static	$STUDENT_STATLIST          = 182;
static	$STUDENT_CSV               = 183;
static	$STUDENT_NUMEN             = 184;
static	$STUDENT_ZIPCODE           = 185;
static	$STUDENT_MYCITY            = 186;
static	$STUDENT_PARENTLIST        = 187;
static	$STUDENT_MOBILE            = 188;
static	$STUDENT_PHONEWORK         = 189;
static	$STUDENT_TUTORS            = 190;
static	$STUDENT_INPUTNEW          = 191;
static	$STUDENT_INPUTREPLY        = 192;
static	$STUDENT_INPUTOK           = 193;
static	$STUDENT_INPUTCANCEL       = 194;
static	$STUDENT_VISIBLE           = 195;
static	$STUDENT_INVISIBLE         = 196;
static	$STUDENT_RETURNLIST        = 197;
static	$STUDENT_GRPNAME           = 198;
static	$STUDENT_LISTSTUDENT       = 199;
static	$STUDENT_INPUTOKNEW        = 200;
static	$STUDENT_INPUTOKTEXT       = 201;
static	$STUDENT_DELETEGRP         = 202;
static	$STUDENT_ETATGRP           = 203;
static	$STUDENT_ETATGRP_ALL       = 204;
static	$STUDENT_ETATGRP_V         = 205;
static	$STUDENT_ETATGRP_I         = 206;
static  $STUDENT_PROMOTION         = 207;
static  $STUDENT_ALLPROMOTION      = 208;
//---------------------------------------------------------------------------
?>
