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
 *		module   : config.php
 *		projet   : définition des mnémoniques des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 05/04/2007
 *		modif    :
 *
 */

//---------------------------------------------------------------------------
static	$CONFIG_CONFIG               = 0;
static	$CONFIG_TUNE                 = 1;
static	$CONFIG_DATABASE             = 2;
static	$CONFIG_MENU                 = 3;
static	$CONFIG_SECURITY             = 4;
static	$CONFIG_PERSISTENT           = 5;
static	$CONFIG_CREATACCOUNT         = 6;
static	$CONFIG_BYUSER               = 7;
static	$CONFIG_VALIDITY             = 8;
static	$CONFIG_ACCOUNT              = 9;
static	$CONFIG_FILTER               = 10;
static	$CONFIG_DEBUG                = 11;
static	$CONFIG_DEMO                 = 12;
static	$CONFIG_MULTIPLE             = 13;
static	$CONFIG_PASSWD               = 14;
static	$CONFIG_CHAR                 = 15;
static	$CONFIG_DELAY                = 16;
static	$CONFIG_MINUTES              = 17;
static	$CONFIG_LINK                 = 18;
static	$CONFIG_DAYS                 = 19;
static	$CONFIG_DATA                 = 20;
static	$CONFIG_SIZE                 = 21;
static	$CONFIG_QUOTAS               = 22;
static	$CONFIG_LOGS                 = 23;
static	$CONFIG_STATS                = 24;
static	$CONFIG_POSTIT               = 25;
static	$CONFIG_WEEKS                = 26;
static	$CONFIG_SHOW                 = 27;
static	$CONFIG_MYMENU               = 28;
static	$CONFIG_MENUTYPE             = 29;
static	$CONFIG_SPACE                = 30;
static	$CONFIG_PIXEL                = 31;
static	$CONFIG_HOMEPAGE             = 32;
static	$CONFIG_FLASH                = 33;
static	$CONFIG_NBDATA               = 34;
static	$CONFIG_NBPAGE               = 35;
static	$CONFIG_LASTMSG              = 36;
static	$CONFIG_STYLE                = 37;
static	$CONFIG_DOC                  = 38;
static	$CONFIG_MODIFY               = 39;
static	$CONFIG_CREAT                = 40;
static	$CONFIG_EXT                  = 41;
static	$CONFIG_ADDOC                = 42;
static	$CONFIG_VALIDATE             = 43;
static	$CONFIG_GOHOME               = 44;
static	$CONFIG_DBACONFIG            = 45;
static	$CONFIG_CENTERS              = 46;
static	$CONFIG_VISIBLE              = 47;
static	$CONFIG_INVISIBLE            = 48;
static	$CONFIG_DELETE               = 49;
static	$CONFIG_ADDRECORD            = 50;
static	$CONFIG_GROUPS               = 51;
static	$CONFIG_EXTAND               = 52;
static	$CONFIG_LIMITED              = 53;
static	$CONFIG_CLASS                = 54;
static	$CONFIG_MATTER               = 55;
static	$CONFIG_FORMFEED             = 56;
static	$CONFIG_TITLE                = 57;
static	$CONFIG_TEXT                 = 58;
static	$CONFIG_LOGIN                = 59;
static	$CONFIG_NOPERM               = 60;
static	$CONFIG_ERRCONF              = 61;
static	$CONFIG_OKSTEP1              = 62;
static	$CONFIG_OPTION               = 63;
static	$CONFIG_CHOOSEMENU           = 64;
static	$CONFIG_NEWIDENT             = 65;
static	$CONFIG_SUBMENU              = 66;
static	$CONFIG_NOSHOWLINK           = 67;
static	$CONFIG_SHOWLINK             = 68;
static	$CONFIG_MYTUNING             = 69;
static	$CONFIG_ERRNAME              = 70;
static	$CONFIG_IMAGE                = 71;
static	$CONFIG_NAME                 = 72;
static	$CONFIG_MANDATORY            = 73;
static	$CONFIG_MAXSIZE              = 74;
static	$CONFIG_LOGO                 = 75;
static	$CONFIG_MAXSZREGION          = 76;
static	$CONFIG_REGION               = 77;
static	$CONFIG_COLOR                = 78;
static	$CONFIG_ERRADDRESS           = 79;
static	$CONFIG_ADDRESS              = 80;
static	$CONFIG_ERRTEL               = 81;
static	$CONFIG_TEL                  = 82;
static	$CONFIG_FAX                  = 83;
static	$CONFIG_WEBSITE              = 84;
static	$CONFIG_EMAIL                = 85;
static	$CONFIG_TYPE                 = 86;
static	$CONFIG_NEXT                 = 87;
static	$CONFIG_GETCENTER            = 88;
static	$CONFIG_CENTERTYPE           = 89;
static	$CONFIG_TERMINATE            = 90;
static	$CONFIG_SEND                 = 91;
static	$CONFIG_ERRCREATE            = 92;
static	$CONFIG_TUNING               = 93;
static	$CONFIG_CHOOSECONFIG         = 94;
static	$CONFIG_THEME                = 95;
static	$CONFIG_APPLY                = 96;
static	$CONFIG_PUCE                 = 97;
static	$CONFIG_BKG                  = 98;
static	$CONFIG_APPLYPAGE            = 99;
static	$CONFIG_MYTITLE              = 100;
static	$CONFIG_MYCOLOR              = 101;
static	$CONFIG_MYADDRESS            = 102;
static	$CONFIG_MYLOGO               = 103;
static	$CONFIG_MAIN                 = 104;
static	$CONFIG_LEFT                 = 105;
static	$CONFIG_CENTER               = 106;
static	$CONFIG_RIGHT                = 107;
static	$CONFIG_WINTITLE             = 108;
static	$CONFIG_HOMEMSG              = 109;
static	$CONFIG_CONNECTION           = 110;
static	$CONFIG_HELP                 = 111;
static	$CONFIG_LINKUPDT             = 112;
static	$CONFIG_LINKNEW              = 113;
static	$CONFIG_INSERT               = 114;
static	$CONFIG_MODIFICATION         = 115;
static	$CONFIG_MANAGEMENT           = 116;
static	$CONFIG_STATUS               = 117;
static	$CONFIG_AUTHOR               = 118;
static	$CONFIG_LIST                 = 119;
static	$CONFIG_ERRIDENT             = 120;
static	$CONFIG_IDENT                = 121;
static	$CONFIG_ERRURL               = 122;
static	$CONFIG_URL                  = 123;
static	$CONFIG_ACCESS               = 124;
static	$CONFIG_PERMS                = 125;
static	$CONFIG_LINKCLOSE            = 126;
static	$CONFIG_ANONYMOUS            = 127;
static	$CONFIG_ADDLINK              = 128;
static	$CONFIG_PREV                 = 129;
static	$CONFIG_PUBLIC               = 130;
static	$CONFIG_ISVALID              = 131;
static	$CONFIG_SECONDARY            = 132;
static	$CONFIG_LANGLIST             = 133;
static	$CONFIG_LOADIMAGE            = 134;
static	$CONFIG_ADDMENU              = 135;
static	$CONFIG_UPDTMENU             = 136;
static	$CONFIG_NEWMENU              = 137;
static	$CONFIG_DESCRIPTION          = 138;
static	$CONFIG_ALIGN                = 139;
static	$CONFIG_DISPLAY              = 140;
static	$CONFIG_CLOSEMENU            = 141;
static	$CONFIG_MARQUEE              = 142;
static	$CONFIG_WELCOMEPAGE          = 143;
static	$CONFIG_PICTURE              = 144;
static	$CONFIG_CONTENT              = 145;
static	$CONFIG_SMS                  = 146;
static	$CONFIG_SMSPROVIDER          = 147;
static	$CONFIG_WEBMASTER            = 148;
static	$CONFIG_INSTALL              = 149;
static	$CONFIG_UNINSTALL            = 150;
static	$CONFIG_P2P                  = 151;
static	$CONFIG_KEY                  = 152;
static	$CONFIG_CONNECT              = 153;
static	$CONFIG_GETKEY               = 154;
static	$CONFIG_SHARING              = 155;
static	$CONFIG_MAINTENANCE          = 156;
static	$CONFIG_CHOOSECENTER         = 157;
static	$CONFIG_DENIED               = 158;
static	$CONFIG_SCREENING            = 159;
static	$CONFIG_FROM                 = 160;
static	$CONFIG_TO                   = 161;
static	$CONFIG_DATE                 = 162;
static	$CONFIG_CHARSET              = 163;
static	$CONFIG_CUSTOM               = 164;
static	$CONFIG_BACKOFFICE           = 165;
static	$CONFIG_SORTITEM             = 166;
static	$CONFIG_ACTIVATE             = 167;
static	$CONFIG_DEACTIVATE           = 168;
static	$CONFIG_IMGSIZE              = 169;
static	$CONFIG_KBYTE                = 170;
static	$CONFIG_SERVER               = 171;
static	$CONFIG_CONFIGP2P            = 172;
static	$CONFIG_CONFIGMENUS          = 173;
static	$CONFIG_CONFIGKWORDS         = 174;
static	$CONFIG_KEYWORDS             = 175;
static	$CONFIG_SYNCHRO              = 176;
static	$CONFIG_RUNSYNCHRO           = 177;
static	$CONFIG_MYDOC                = 178;
static	$CONFIG_DIRECTORY            = 179;
static	$CONFIG_WEBCRAWLER           = 180;
static	$CONFIG_ZIPCODE              = 181;
static	$CONFIG_TIMEZONE             = 182;
static	$CONFIG_ARCHBIT              = 183;
static	$CONFIG_BITS                 = 184;
static	$CONFIG_INPUTNEW             = 185;
static	$CONFIG_INPUTREPLY           = 186;
static	$CONFIG_INPUTOK              = 187;
static	$CONFIG_INPUTCANCEL          = 188;
static	$CONFIG_MYCOLORBAND          = 189;
static	$CONFIG_EDT                  = 190;
static	$CONFIG_VAC                  = 191;
static	$CONFIG_CONFSEM              = 192;
static	$CONFIG_CONFVAC              = 193;
static	$CONFIG_COLOR                = 194;
static	$CONFIG_SEMAINE              = 195;
static	$CONFIG_AU                   = 196;
static	$CONFIG_MATCLASS             = 197;
static	$CONFIG_MATPROF              = 198;
static	$CONFIG_MOTIF                = 199;
static	$CONFIG_NCTEXT               = 200;
static	$CONFIG_NOMCENTRE            = 201;
static	$CONFIG_NOMGROUPE            = 202;
static	$CONFIG_NOMCODE              = 203;
static	$CONFIG_NOMCLASS             = 204;
static	$CONFIG_NOMMATIERE           = 205;
static	$CONFIG_NOMCOULEUR           = 206;
static	$CONFIG_NOMMOTIF             = 207;
static	$CONFIG_SALLES               = 208;
static	$CONFIG_NOMSALLE             = 209;
static	$CONFIG_POLE                 = 210;
static	$CONFIG_GRADUATION_YEAR      = 211;
static	$CONFIG_POLE_NAME            = 212;
static  $CONFIG_MATBYCLASS           = 213;
static  $CONFIG_PARAMETERS           = 214;
static  $CONFIG_VALIDATION_BUTTON    = 215;
static  $CONFIG_ERRORPARAMUPDATE     = 216;
static  $CONFIG_SUCCESSPARAMUPDATE   = 217;
static  $CONFIG_CODE                 = 218;
static  $CONFIG_VALUE                = 219;
static  $CONFIG_COMMENT              = 220;
static  $CONFIG_EMPTY_CACHE_BUTTON   = 221;
static  $CONFIG_SUCCESS_CACHE_CLEAR  = 222;
static  $CONFIG_ERROR_CACHE_CLEAR    = 223;
static  $CONFIG_CONFPERIODES         = 224;
static  $CONFIG_NEWCONFIG            = 225;
static  $CONFIG_RENAMECONFIG         = 226;
static  $CONFIG_CONFIGNAME           = 227;
static  $CONFIG_CITY                 = 228;
static  $CONFIG_CHOOSEFILE           = 229;
static  $CONFIG_BROWSE               = 230;
static  $CONFIG_USEASDEFAULT         = 231;
static  $CONFIG_LOGOLARGE            = 232;
static  $CONFIG_LOGOSQUARE           = 233;
static  $CONFIG_CURRENTCONFIG        = 234;
static  $CONFIG_SIGNATURE            = 235;
static  $CONFIG_FAVICON              = 236;
static  $CONFIG_FAVICON_HELP         = 237;
static  $CONFIG_REMOVECONFIG         = 238;
static  $CONFIG_SUREQUESTION         = 239;
static	$CONFIG_ACTIVATED            = 240;
static	$CONFIG_DEACTIVATED          = 241;
static  $CONFIG_LOGOLARGEDARK        = 242;
static  $CONFIG_LOGOSQUAREDARK       = 243;
static  $CONFIG_FONTAWESOMEREQUIRED  = 244;
static  $CONFIG_BACK                 = 245;
static  $CONFIG_CSS_THEME            = 246;
//---------------------------------------------------------------------------
?>
