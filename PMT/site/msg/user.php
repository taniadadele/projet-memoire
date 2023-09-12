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
 *		module   : user.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 21/03/2007
 *		modif    :
 *
 */

//---------------------------------------------------------------------------
static	$USER_ACL                  = 0;
static	$USER_CHOOSECENTER         = 1;
static	$USER_CHOOSECATEGORY       = 2;
static	$USER_GROUP                = 3;
static	$USER_CONNECTED            = 4;
static	$USER_DISCONNECT           = 5;
static	$USER_LASTCONNECT          = 6;
static	$USER_CLOSEWINDOW          = 7;
static	$USER_CHOOSECLASS          = 8;
static	$USER_ALLCLASSES           = 9;
static	$USER_ISCHS                = 10;
static	$USER_MOBILE               = 11;
static	$USER_GIVELOGIN            = 12;
static	$USER_VALIDATE             = 13;
static	$USER_ALLCATEGORY          = 14;
static	$USER_LOGIN                = 15;
static	$USER_CANTREACH            = 16;
static	$USER_DOINSTALL            = 17;
static	$USER_CREATEACCOUNT        = 18;
static	$USER_NOPERM               = 19;
static	$USER_ACCOUNTCLOSE         = 20;
static	$USER_NOTVALID             = 21;
static	$USER_PASSWORD             = 22;
static	$USER_USERID               = 23;
static	$USER_CLICKHERE            = 24;
static	$USER_USERACCOUNT          = 25;
static	$USER_ERRINPUT             = 26;
static	$USER_BADEMAIL             = 27;
static	$USER_BADID                = 28;
static	$USER_THANX                = 29;
static	$USER_WARNING              = 30;
static	$USER_CHS                  = 31;
static	$USER_NOLIMIT              = 32;
static	$USER_DELAY                = 33;
static	$USER_PICTURE              = 34;
static	$USER_UPDATEOK             = 35;
static	$USER_BACK                 = 36;
static	$USER_SESSION              = 37;
static	$USER_DISCONNECTED         = 38;
static	$USER_TIMEOUT              = 39;
static	$USER_ADDRECORD            = 40;
static	$USER_CHOOSEPERIOD         = 41;
static	$USER_STUDENTSTATUS        = 42;
static	$USER_GRANTHOLDER          = 43;
static	$USER_FFU                  = 44;
static	$USER_PORTFOLIO            = 45;
static	$USER_B2I                  = 46;
static	$USER_STAGE                = 47;
static	$USER_ABSENT               = 48;
static	$USER_MYCLASS              = 49;
static	$USER_NAME                 = 50;
static	$USER_MYDATE               = 51;
static	$USER_POSTIT               = 52;
static	$USER_REPRESENTATIVE       = 53;
static	$USER_NEXT                 = 54;
static	$USER_STATUS               = 55;
static	$USER_PREV                 = 56;
static	$USER_SKIN                 = 57;
static	$USER_SKINTEXT             = 58;
static	$USER_MENU                 = 59;
static	$USER_PUCE                 = 60;
static	$USER_BGROUND              = 61;
static	$USER_TUNING               = 62;
static	$USER_WORKSPACE            = 63;
static	$USER_BLOLG                = 64;
static	$USER_MAIL                 = 65;
static	$USER_DIARY                = 66;
static	$USER_ADDRESSBOOK          = 67;
static	$USER_CONFIRM              = 68;
static	$USER_GOBACK               = 69;
static	$USER_STUDENTACCOUNT       = 70;
static	$USER_ERRINPUT             = 71;
static	$USER_FNAME                = 72;
static	$USER_SEX                  = 73;
static	$USER_CENTER               = 74;
static	$USER_VALIDATION           = 75;
static	$USER_BORN                 = 76;
static	$USER_DATE                 = 77;
static	$USER_ADDRESS              = 78;
static	$USER_CITY                 = 79;
static	$USER_PHONE                = 80;
static	$USER_NOEMAILFOUND         = 81;
static	$USER_EMAIL                = 82;
static	$USER_REGIME               = 83;
static	$USER_BOURSE               = 84;
static	$USER_DELEGUE              = 85;
static	$USER_YES                  = 86;
static	$USER_NO                   = 87;
static	$USER_NEVERCONNECT         = 88;
static	$USER_BAKTOLIST            = 89;
static	$USER_ADDRECORD            = 90;
static	$USER_ROLE                 = 91;
static	$USER_ALLCENTER            = 92;
static	$USER_ACCOUNTOPEN          = 93;
static	$USER_ACCOUNTCLOSE         = 94;
static	$USER_LASTCONNEXION        = 95;
static	$USER_TITLE                = 96;
static	$USER_FUNCTION             = 97;
static	$USER_VALIDITY             = 98;
static	$USER_WAITVALIDATION       = 99;
static	$USER_UPDATE               = 100;
static	$USER_RIGHTS               = 101;
static	$USER_INSCRIPTION          = 102;
static	$USER_CNX                  = 103;
static	$USER_CONNECTO             = 104;
static	$USER_NBMSG                = 105;
static	$USER_NBRESOURCE           = 106;
static	$USER_NBCNX                = 107;
static	$USER_MINPASSWD            = 108;
static	$USER_NOPASSWD             = 109;
static	$USER_SHOW                 = 110;
static	$USER_HIDE                 = 111;
static	$USER_SIGNATURE            = 112;
static	$USER_AVATAR               = 113;
static	$USER_ACTIVITY             = 114;
static	$USER_CATEGORY             = 115;
static	$USER_GRANTED              = 116;
static	$USER_BLACKLIST            = 117;
static	$USER_REFRESH              = 118;
static	$USER_LIST1                = 119;
static	$USER_LIST2                = 120;
static	$USER_DELSTUDENT           = 121;
static	$USER_SUBJECT              = 122;
static	$USER_TEXTBOOK             = 123;
static	$USER_LASTACCESS           = 124;
static	$USER_LOGOUT               = 125;
static	$USER_LASTCLICK            = 126;
static	$USER_USRTIME1             = 127;
static	$USER_USRTIME2             = 128;
static	$USER_MODIFICATION         = 129;
static	$USER_NEWRECORD            = 130;
static	$USER_MANDATORY            = 131;
static	$USER_MALE                 = 132;
static	$USER_FEMALE               = 133;
static	$USER_ANONYMOUS            = 134;
static	$USER_MORE                 = 135;
static	$USER_LIST3                = 136;
static	$USER_YEARMONTH            = 137;
static	$USER_MONTHDAYS            = 138;
static	$USER_DAYSHOUR             = 139;
static	$USER_HOURMIN              = 140;
static	$USER_MINSEC               = 141;
static	$USER_SECONDS              = 142;
static	$USER_LANG                 = 143;
static	$USER_CREATACCOUNT         = 144;
static	$USER_WAITOPEN             = 145;
static	$USER_AUTOVAL              = 146;
static	$USER_AUTODEL              = 147;
static	$USER_SENDPWD              = 148;
static	$USER_BODY                 = 149;
static	$USER_LOSTPWD              = 150;
static	$USER_NOSOUCY              = 151;
static	$USER_SENDEMAIL            = 152;
static	$USER_BURNOUT              = 153;
static	$USER_DELUSER              = 154;
static	$USER_CHANGECLASS          = 155;
static	$USER_CHANGECENTER         = 156;
static	$USER_TEL                  = 157;
static	$USER_CV                   = 158;
static	$USER_OFFER                = 159;
static	$USER_MYNAME               = 160;
static	$USER_GRANT                = 161;
static	$USER_DENIED               = 162;
static	$USER_FILTER               = 163;
static	$USER_PASWDMANAGEMENT      = 164;
static	$USER_FORMAT               = 165;
static	$USER_PASSWDSET            = 166;
static	$USER_GROUPS               = 167;
static	$USER_EMPTYPASSWD          = 168;
static	$USER_ACCOUNTUPDATE        = 169;
static	$USER_GENERATE             = 170;
static	$USER_NOTVALIDATE          = 171;
static	$USER_SELECTDATE           = 172;
static	$USER_SELECTRIGHT          = 173;
static	$USER_AWAITING             = 174;
static	$USER_AWAITBODYTEXT        = 175;
static	$USER_NBCONNECT            = 176;
static	$USER_SUBJECTS             = 177;
static	$USER_SELECTLANG           = 178;
static	$USER_COLUMNS              = 179;
static	$USER_MYTITLE              = 180;
static	$USER_RESTORE              = 181;
static	$USER_DEFAULTVALUES        = 182;
static	$USER_LOSTPASSWD           = 183;
static	$USER_NOIDENTFOUND         = 184;
static	$USER_SENDMAILSUCCESS      = 185;
static	$USER_SENDMAILFAILED       = 186;
static	$USER_WELCOME              = 187;
static	$USER_CSV                  = 188;
static	$USER_SCHOOL               = 189;
static	$USER_ZIPCODE              = 190;
static	$USER_GETKEY               = 191;
static	$USER_NOTVALIDSCHOOL       = 192;
static	$USER_IDENT                = 193;
static	$USER_INPUTNEW             = 194;
static	$USER_INPUTREPLY           = 195;
static	$USER_INPUTOK              = 196;
static	$USER_INPUTCANCEL          = 197;
static	$USER_CODE                 = 198;
static	$USER_ADMIN                = 199;
static	$USER_CLASS                = 200;
static	$USER_OPTIONS              = 201;
static	$USER_COURS                = 202;
static	$USER_ADDFILE              = 203;
static	$USER_CANCEL               = 204;
static	$USER_DELETE               = 205;
static	$USER_RETURNLIST           = 206;
static	$USER_SELECT_YOUR_CIVILITY = 207;
static	$USER_STUDENT              = 208;
static	$USER_TEACHER              = 209;
static	$USER_SELECT_ACCOUNT_TYPE  = 210;
static	$USER_PROMOTION            = 211;
static	$USER_SEE_GED              = 212;
static	$USER_EDITINFOS            = 213;
static	$USER_PSWD_OLD             = 214;
static	$USER_PSWD_NEW1            = 215;
static	$USER_PSWD_NEW2            = 216;
static	$USER_EDITINFOS_OK         = 217;
static	$USER_PWSD_NOTSAME         = 218;
static	$USER_TIMEOUT_LINK         = 219;
static	$USER_BACK_BTN             = 220;
static	$USER_PASSWORD_MODIF_VALID = 221;
static	$USER_USERID_OR_EMAIL      = 222;
static	$USER_FORGOT_PASSWD        = 223;
static	$USER_PASSWORD_MODIF_MAIL  = 224;
static	$USER_PASSWORD_UNKNOWN_IDENT= 225;
static	$USER_USERID_NO_MAIL       = 226;
static  $USER_COPIES               = 227;
static  $USER_CONNECTION           = 228;

//---------------------------------------------------------------------------
?>
