# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Hôte: localhost (MySQL 5.7.26)
# Base de données: promethee
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Affichage de la table absent
# ------------------------------------------------------------

DROP TABLE IF EXISTS `absent`;

CREATE TABLE `absent` (
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDgrp` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_template` varchar(20) NOT NULL,
  `_sms` varchar(20) NOT NULL,
  `_display` enum('D','W','M') NOT NULL DEFAULT 'D',
  `_autoval` enum('O','N') NOT NULL DEFAULT 'O',
  `_email` enum('-','P','E') NOT NULL DEFAULT '-',
  UNIQUE KEY `_key` (`_IDcentre`,`_IDgrp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des absences';

LOCK TABLES `absent` WRITE;
/*!40000 ALTER TABLE `absent` DISABLE KEYS */;

INSERT INTO `absent` (`_IDcentre`, `_IDgrp`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_template`, `_sms`, `_display`, `_autoval`, `_email`)
VALUES
	(1,1,0,8,31,'absence.html','absence.txt','D','N','-');

/*!40000 ALTER TABLE `absent` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table absent_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `absent_data`;

CREATE TABLE `absent_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_texte` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'N',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDdata`,`_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des motifs des absences';

LOCK TABLES `absent_data` WRITE;
/*!40000 ALTER TABLE `absent_data` DISABLE KEYS */;

INSERT INTO `absent_data` (`_IDdata`, `_texte`, `_visible`, `_lang`)
VALUES
	(2,'Retard','O','fr'),
	(3,'Décès','O','fr'),
	(4,'Sortie','O','fr'),
	(6,'Convocation externe','O','fr'),
	(7,'Raisons Familiales','O','fr'),
	(9,'Dispensé','O','fr'),
	(10,'Abs: Mail famille','O','fr'),
	(11,'Problème de transport','O','fr'),
	(12,'RDV médical','O','fr'),
	(13,'Certificat Médical','O','fr'),
	(14,'Stage [NC]','O','fr'),
	(16,'Autre','O','fr'),
	(23,'Abandon','O','fr'),
	(24,'Inconnu','O','fr'),
	(25,'Raisons Professionnelles','O','fr');

/*!40000 ALTER TABLE `absent_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table absent_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `absent_items`;

CREATE TABLE `absent_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDctn` int(11) NOT NULL DEFAULT '0',
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_IDgrp` int(10) unsigned DEFAULT NULL,
  `_IDabs` int(10) unsigned DEFAULT NULL,
  `_start` datetime DEFAULT NULL,
  `_end` datetime DEFAULT NULL,
  `_texte` tinytext NOT NULL,
  `_comment` varchar(255) DEFAULT NULL,
  `_display` enum('O','N') NOT NULL DEFAULT 'O',
  `_calendar` enum('O','N') NOT NULL DEFAULT 'N',
  `_delay` enum('0','1') NOT NULL DEFAULT '0',
  `_email` text,
  `_sms` text,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_isok` datetime DEFAULT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'N',
  `_valid` enum('O','N','A') NOT NULL DEFAULT 'A',
  `_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`_IDitem`),
  KEY `_IDdata` (`_IDdata`),
  KEY `absent_items IDabs index` (`_IDabs`),
  CONSTRAINT `absent_items IDabs to user_id ID` FOREIGN KEY (`_IDabs`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `absent_items IDdata to absent_data IDdata` FOREIGN KEY (`_IDdata`) REFERENCES `absent_data` (`_IDdata`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des annonces des absences';



# Affichage de la table absent_motif
# ------------------------------------------------------------

DROP TABLE IF EXISTS `absent_motif`;

CREATE TABLE `absent_motif` (
  `_IDmotif` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_text` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  UNIQUE KEY `_key` (`_IDmotif`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des motifs des passages à l''infirmerie';

LOCK TABLES `absent_motif` WRITE;
/*!40000 ALTER TABLE `absent_motif` DISABLE KEYS */;

INSERT INTO `absent_motif` (`_IDmotif`, `_text`, `_lang`)
VALUES
	(1,'Inconnu','fr'),
	(2,'Soins','fr'),
	(3,'Repos infirmerie','fr'),
	(4,'Retour au domicile','fr'),
	(5,'Passage abusif','fr'),
	(6,'Non présentation','fr');

/*!40000 ALTER TABLE `absent_motif` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table absent_sick
# ------------------------------------------------------------

DROP TABLE IF EXISTS `absent_sick`;

CREATE TABLE `absent_sick` (
  `_IDsick` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDclass` int(10) unsigned NOT NULL,
  `_IDeleve` int(10) unsigned NOT NULL,
  `_IDexp` int(10) unsigned NOT NULL,
  `_IPexp` bigint(20) NOT NULL DEFAULT '0',
  `_start` datetime DEFAULT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_end` datetime DEFAULT NULL,
  `_IDmotif` int(10) unsigned NOT NULL,
  `_text` tinytext NOT NULL,
  PRIMARY KEY (`_IDsick`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des passages à l''infirmerie';



# Affichage de la table admin_backup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_backup`;

CREATE TABLE `admin_backup` (
  `_IDbackup` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_version` varchar(12) NOT NULL,
  `_create` datetime DEFAULT NULL,
  `_update` datetime DEFAULT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_table` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_IDbackup`),
  UNIQUE KEY `_key` (`_create`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des sauvegardes de la base de données';



# Affichage de la table admin_import
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_import`;

CREATE TABLE `admin_import` (
  `_IDimport` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_table` varchar(20) NOT NULL,
  `_IDgroup` int(10) unsigned NOT NULL,
  `_date` datetime DEFAULT NULL,
  `_record` int(10) unsigned NOT NULL,
  `_ext` varchar(3) NOT NULL,
  PRIMARY KEY (`_IDimport`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des sauvegardes de la base de données';



# Affichage de la table admin_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_log`;

CREATE TABLE `admin_log` (
  `_IDlog` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDimport` int(10) unsigned NOT NULL,
  `_text` mediumtext NOT NULL,
  PRIMARY KEY (`_IDlog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des logs d''erreurs des imports';



# Affichage de la table bank_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `bank_data`;

CREATE TABLE `bank_data` (
  `_ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la donnée',
  `_date` date NOT NULL,
  `_libele` longtext NOT NULL,
  `_price` float NOT NULL,
  `_IDeleve` int(11) NOT NULL,
  `_status` int(11) NOT NULL DEFAULT '1',
  `_attr` longtext,
  PRIMARY KEY (`_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des opérations bancaires';



# Affichage de la table calendar_events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `calendar_events`;

CREATE TABLE `calendar_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `user_id` int(11) unsigned zerofill DEFAULT NULL,
  `pma_id` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `prof_id_1` int(11) unsigned DEFAULT NULL,
  `prof_id_2` int(11) unsigned DEFAULT NULL,
  `attr` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calendar_events user_id index` (`user_id`),
  KEY `calendar_events pma_id index` (`pma_id`),
  KEY `calendar_events prof_id_1 index` (`prof_id_1`),
  KEY `calendar_events prof_id_2 index` (`prof_id_2`),
  KEY `calendar_events exam_id index` (`exam_id`),
  CONSTRAINT `calendar_events exam_id to campus_examens _ID_exam` FOREIGN KEY (`exam_id`) REFERENCES `campus_examens` (`_ID_exam`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `calendar_events pma_id to pole_mat_annee _ID_pma` FOREIGN KEY (`pma_id`) REFERENCES `pole_mat_annee` (`_ID_pma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `calendar_events prof_id_1 to user_id _ID` FOREIGN KEY (`prof_id_1`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `calendar_events prof_id_2 to user_id _ID` FOREIGN KEY (`prof_id_2`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `calendar_events user_id to user_id _ID` FOREIGN KEY (`user_id`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table campus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus`;

CREATE TABLE `campus` (
  `_IDcampus` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_table` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDcampus`),
  UNIQUE KEY `_key` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des e-campus';

LOCK TABLES `campus` WRITE;
/*!40000 ALTER TABLE `campus` DISABLE KEYS */;

INSERT INTO `campus` (`_IDcampus`, `_ident`, `_table`, `_visible`, `_lang`)
VALUES
	(5,'matières','campus_data','O','fr'),
	(6,'classes','campus_classe','N','fr');

/*!40000 ALTER TABLE `campus` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table campus_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_access`;

CREATE TABLE `campus_access` (
  `_IDaccess` int(11) NOT NULL,
  `_ident` varchar(20) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDaccess`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='table des droits d''accès des utilisateurs pour les e-campus';

LOCK TABLES `campus_access` WRITE;
/*!40000 ALTER TABLE `campus_access` DISABLE KEYS */;

INSERT INTO `campus_access` (`_IDaccess`, `_ident`, `_lang`)
VALUES
	(-1,'On standby','en'),
	(0,'Banished','en'),
	(1,'Reader','en'),
	(3,'Member','en'),
	(7,'Moderator','en'),
	(-1,'En espera','es'),
	(0,'Proscritos','es'),
	(1,'Lector','es'),
	(3,'Miembros','es'),
	(7,'Moderador','es'),
	(-1,'En attente','fr'),
	(0,'Bannis','fr'),
	(1,'Lecteur','fr'),
	(3,'Membre','fr'),
	(7,'Modérateur','fr');

/*!40000 ALTER TABLE `campus_access` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table campus_classe
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_classe`;

CREATE TABLE `campus_classe` (
  `_IDclass` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDcentre` int(10) unsigned NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_text` tinytext NOT NULL,
  `_IDpp` int(10) unsigned DEFAULT NULL COMMENT 'ID du prof principal',
  `_IDpp_2` int(10) unsigned DEFAULT NULL COMMENT 'ID du second prof principal',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_auto` enum('O','N') NOT NULL DEFAULT 'O',
  `_valid` datetime DEFAULT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_code` varchar(200) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  `_graduation_year` int(4) NOT NULL COMMENT 'Année d''obtention du diplôme',
  PRIMARY KEY (`_IDclass`),
  KEY `IDcentre index` (`_IDcentre`),
  CONSTRAINT `campus_classe IDcentre to config_centre IDcentre` FOREIGN KEY (`_IDcentre`) REFERENCES `config_centre` (`_IDcentre`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des classes du campus';

LOCK TABLES `campus_classe` WRITE;
/*!40000 ALTER TABLE `campus_classe` DISABLE KEYS */;

INSERT INTO `campus_classe` (`_IDclass`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_IDcentre`, `_ident`, `_text`, `_IDpp`, `_IDpp_2`, `_private`, `_auto`, `_valid`, `_visible`, `_code`, `_lang`, `_graduation_year`)
VALUES
	(1,0,255,255,1,'Promotion 2021','',NULL,NULL,'N','N',NULL,'O','1','fr',2021);

/*!40000 ALTER TABLE `campus_classe` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table campus_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_data`;

CREATE TABLE `campus_data` (
  `_IDmat` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_ident` varchar(5) NOT NULL,
  `_titre` varchar(40) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_seuil` int(10) unsigned NOT NULL DEFAULT '10',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_auto` enum('O','N') NOT NULL DEFAULT 'O',
  `_valid` datetime DEFAULT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  `_color` varchar(10) NOT NULL DEFAULT 'aaaaaa' COMMENT 'La couleur de la matière en hexadecimal',
  `_option` int(1) NOT NULL DEFAULT '0',
  `_code` varchar(200) NOT NULL,
  `_type` int(11) NOT NULL DEFAULT '1' COMMENT 'Type de matière (1 = matière, 2 = Indisponible, 3 = Agenda)',
  PRIMARY KEY (`_IDmat`,`_lang`,`_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des matières enseignées';

LOCK TABLES `campus_data` WRITE;
/*!40000 ALTER TABLE `campus_data` DISABLE KEYS */;

INSERT INTO `campus_data` (`_IDmat`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_ident`, `_titre`, `_texte`, `_seuil`, `_private`, `_auto`, `_valid`, `_visible`, `_lang`, `_color`, `_option`, `_code`, `_type`)
VALUES
	(5,0,4,255,'','Économie','Bienvenue sur la page Français.',10,'N','N',NULL,'O','fr','7d8c89',0,'ECO',1),
	(6,0,4,255,'','Anglais','Bienvenue sur la page Anglais.',10,'N','N',NULL,'O','fr','be2929',0,'ANGL',1);

/*!40000 ALTER TABLE `campus_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table campus_download
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_download`;

CREATE TABLE `campus_download` (
  `_IDwork` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmat` int(10) unsigned NOT NULL,
  `_IDroot` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` text NOT NULL,
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_titre` varchar(80) NOT NULL,
  `_texte` text NOT NULL,
  `_open` datetime NOT NULL,
  `_close` datetime NOT NULL,
  `_start` datetime NOT NULL,
  `_end` datetime NOT NULL,
  UNIQUE KEY `_key` (`_IDwork`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la gestion des téléchargements des travaux dans le ';



# Affichage de la table campus_examens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_examens`;

CREATE TABLE `campus_examens` (
  `_ID_exam` int(11) NOT NULL AUTO_INCREMENT,
  `_type` int(11) NOT NULL,
  `_nom` longtext NOT NULL,
  `_coef` int(11) NOT NULL,
  `_note_max` int(11) NOT NULL,
  `_oral` enum('O','N','') NOT NULL DEFAULT 'N',
  `_ID_pma` int(11) DEFAULT NULL,
  `_ID_parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`_ID_exam`),
  KEY `campus_examens ID_pma index` (`_ID_pma`),
  CONSTRAINT `campus_examens ID_pma to pole_mat_annee ID_pma` FOREIGN KEY (`_ID_pma`) REFERENCES `pole_mat_annee` (`_ID_pma`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table campus_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_items`;

CREATE TABLE `campus_items` (
  `_IDmat` int(11) NOT NULL,
  `_IDmenu` int(10) unsigned NOT NULL,
  `_IDcentre` int(10) unsigned NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  UNIQUE KEY `_key` (`_IDmat`,`_IDmenu`,`_IDcentre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des liens affichés dans le campus';



# Affichage de la table campus_root
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_root`;

CREATE TABLE `campus_root` (
  `_IDroot` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDparent` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL,
  `_titre` varchar(40) NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL,
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDroot`),
  UNIQUE KEY `_key` (`_IDparent`,`_IDmat`,`_titre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des répertoires des travaux téléchargés';



# Affichage de la table campus_syllabus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_syllabus`;

CREATE TABLE `campus_syllabus` (
  `_IDSyllabus` int(10) NOT NULL AUTO_INCREMENT,
  `_IDPMA` int(10) NOT NULL COMMENT 'ID correspondant dans la table de liaison pôle matières année',
  `_objectifs` longtext NOT NULL,
  `_programme` longtext NOT NULL,
  `_visible` enum('O','N') NOT NULL,
  `_idUser` longtext NOT NULL COMMENT 'ID des profs liés au syllabus',
  `_periode_1` text NOT NULL,
  `_periode_2` text NOT NULL,
  `_periode_total` text NOT NULL,
  PRIMARY KEY (`_IDSyllabus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table campus_syllabus_archive
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_syllabus_archive`;

CREATE TABLE `campus_syllabus_archive` (
  `_IDx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la donnée',
  `_year` int(11) NOT NULL COMMENT 'Année de la donnée',
  `_IDpma` int(11) NOT NULL COMMENT 'ID du PMA',
  `_IDprof` longtext NOT NULL COMMENT 'ID des profs entourés de '';''',
  `_periode_1` text NOT NULL COMMENT 'Durée de la période 1',
  `_periode_2` text NOT NULL COMMENT 'Durée de la période 2',
  `_periode_tot` text NOT NULL COMMENT 'Durée de la période totale',
  PRIMARY KEY (`_IDx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table campus_upload
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_upload`;

CREATE TABLE `campus_upload` (
  `_IDupload` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDwork` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_file` varchar(80) NOT NULL,
  `_text` varchar(80) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_type` enum('E','C','R') NOT NULL DEFAULT 'R',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  UNIQUE KEY `_key` (`_IDupload`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de dépôt des travaux dans le campus';



# Affichage de la table campus_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campus_user`;

CREATE TABLE `campus_user` (
  `_IDuser` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmat` int(11) NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_access` int(11) NOT NULL DEFAULT '-1',
  `_date` datetime NOT NULL,
  `_valid` datetime NOT NULL,
  `_lastcnx` datetime NOT NULL,
  `_cnx` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDuser`),
  UNIQUE KEY `_IDmat` (`_IDmat`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des utilisateurs du campus';



# Affichage de la table ccn
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ccn`;

CREATE TABLE `ccn` (
  `_IDcentre` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_email` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `_rss` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`_IDcentre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table du cahier de correspondance numérique';



# Affichage de la table ccn_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ccn_items`;

CREATE TABLE `ccn_items` (
  `_IDmsg` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_ack` datetime NOT NULL,
  `_IDparent` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDdest` int(11) NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_text` mediumtext NOT NULL,
  `_email` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `_priority` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_IDmsg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des messages du cahier de correspondance numérique';



# Affichage de la table chat
# ------------------------------------------------------------

DROP TABLE IF EXISTS `chat`;

CREATE TABLE `chat` (
  `_IDchat` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_start` time NOT NULL,
  `_end` time NOT NULL,
  `_title` varchar(40) NOT NULL,
  `_refresh` int(10) unsigned NOT NULL DEFAULT '1',
  `_maxmsg` int(10) unsigned NOT NULL DEFAULT '15',
  `_maxsize` int(10) unsigned NOT NULL DEFAULT '10',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDchat`),
  UNIQUE KEY `_key` (`_title`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la gestion du chat';

LOCK TABLES `chat` WRITE;
/*!40000 ALTER TABLE `chat` DISABLE KEYS */;

INSERT INTO `chat` (`_IDchat`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_start`, `_end`, `_title`, `_refresh`, `_maxmsg`, `_maxsize`, `_visible`, `_lang`)
VALUES
	(1,0,255,255,'00:00:00','00:00:00','Prométhée',1,15,10,'O','fr'),
	(2,0,255,255,'00:00:00','00:00:00','Prométhée',1,15,10,'O','en'),
	(3,0,255,255,'00:00:00','00:00:00','::Général',1,15,10,'O','fr'),
	(4,0,255,255,'00:00:00','00:00:00','Prométhée',1,15,10,'O','es');

/*!40000 ALTER TABLE `chat` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table chs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `chs`;

CREATE TABLE `chs` (
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_warnmodo` enum('O','N') NOT NULL DEFAULT 'N',
  `_warnuser` enum('O','N') NOT NULL DEFAULT 'N',
  UNIQUE KEY `_key` (`_IDcentre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la gestion du Cahier Hygiène et Sécurité';

LOCK TABLES `chs` WRITE;
/*!40000 ALTER TABLE `chs` DISABLE KEYS */;

INSERT INTO `chs` (`_IDcentre`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_warnmodo`, `_warnuser`)
VALUES
	(1,0,30,30,'N','N');

/*!40000 ALTER TABLE `chs` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table chs_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `chs_items`;

CREATE TABLE `chs_items` (
  `_IDitems` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_ID1` int(10) unsigned NOT NULL,
  `_IP1` bigint(20) NOT NULL DEFAULT '0',
  `_date1` datetime DEFAULT NULL,
  `_titre` varchar(40) NOT NULL,
  `_note1` tinytext NOT NULL,
  `_ID2` int(10) unsigned NOT NULL,
  `_IP2` bigint(20) NOT NULL DEFAULT '0',
  `_date2` datetime DEFAULT NULL,
  `_todo` date DEFAULT NULL,
  `_note2` tinytext NOT NULL,
  `_priority` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`_IDitems`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table du Cahier Hygiène et Sécurité';

LOCK TABLES `chs_items` WRITE;
/*!40000 ALTER TABLE `chs_items` DISABLE KEYS */;

INSERT INTO `chs_items` (`_IDitems`, `_IDcentre`, `_ID1`, `_IP1`, `_date1`, `_titre`, `_note1`, `_ID2`, `_IP2`, `_date2`, `_todo`, `_note2`, `_priority`)
VALUES
	(1,1,9,-4,'2013-09-06 14:03:34','ma note','blabla mon observ',0,0,'2013-09-06 14:03:34','0000-00-00','','');

/*!40000 ALTER TABLE `chs_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table class_mat_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `class_mat_user`;

CREATE TABLE `class_mat_user` (
  `_IDRclass` int(10) unsigned NOT NULL,
  `_IDRmat` int(10) unsigned DEFAULT NULL,
  `_IDRuser` int(10) unsigned DEFAULT NULL,
  `_attr` longtext,
  KEY `class_mat_user IDRclass to campus_classe IDclass` (`_IDRclass`),
  KEY `class_mat_user IDRmat index` (`_IDRmat`),
  KEY `class_mat_user IDRuser index` (`_IDRuser`),
  CONSTRAINT `class_mat_user IDRclass to campus_classe IDclass` FOREIGN KEY (`_IDRclass`) REFERENCES `campus_classe` (`_IDclass`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `class_mat_user IDRmat to campus_data IDmat` FOREIGN KEY (`_IDRmat`) REFERENCES `campus_data` (`_IDmat`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `class_mat_user IDRuser to user_id _ID` FOREIGN KEY (`_IDRuser`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `_IDconf` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(60) NOT NULL DEFAULT '' COMMENT 'Le code de la configuration (Ex: DEMO)',
  `_title` varchar(60) NOT NULL DEFAULT '' COMMENT 'Le nom de la configuration et titre des fenêtres (Ex: Démonstration)',
  `_texte` varchar(60) NOT NULL DEFAULT '' COMMENT 'Message d''accueil',
  `_crawler` varchar(6) DEFAULT NULL,
  `_tdcolor` varchar(6) DEFAULT NULL,
  `_align` enum('G','C','D') NOT NULL DEFAULT 'C',
  `_login` varchar(500) DEFAULT NULL COMMENT 'Message de connexion',
  `_nologin` varchar(500) DEFAULT NULL COMMENT 'Message de maintenance',
  `_IDtheme` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'L''ID du thème',
  `_bgcolor` enum('O','N') NOT NULL DEFAULT 'N',
  `_puce` varchar(20) DEFAULT NULL,
  `_fond` varchar(20) DEFAULT NULL,
  `_page` enum('O','N') NOT NULL DEFAULT 'N',
  `_header` varchar(20) DEFAULT NULL,
  `_adresse` varchar(60) DEFAULT NULL COMMENT 'L''adresse de l''établissement',
  `_cp` varchar(6) DEFAULT NULL COMMENT 'Le code postal de l''établissement',
  `_ville` varchar(20) DEFAULT NULL COMMENT 'La ville de l''établissement',
  `_tel` varchar(20) DEFAULT NULL COMMENT 'Le numéro de téléphone de l''établissement',
  `_fax` varchar(20) DEFAULT NULL COMMENT 'Le numéro de FAX de l''établissement',
  `_web` varchar(80) DEFAULT NULL COMMENT 'Le site web de l''établissement',
  `_email` varchar(60) DEFAULT NULL COMMENT 'L''Email de l''administrateur du site',
  `_logo1` enum('O','N') NOT NULL DEFAULT 'N' COMMENT 'Est-ce qu''il y a un logo de site ?',
  `_logo2` enum('O','N') NOT NULL DEFAULT 'O' COMMENT 'Est-ce qu''il y a un logo de région ?',
  `_webmaster` varchar(40) DEFAULT NULL COMMENT 'Le nom de l''administrateur du site',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O' COMMENT 'Est-ce que la config est utilisée comme configuration principale ? (seul une ligne à ''O'')',
  `_lang` varchar(2) DEFAULT 'fr' COMMENT 'La langue de la configuration',
  `_bandeau` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDconf`),
  UNIQUE KEY `_key` (`_ident`,`_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table de la configuration de l''intranet';

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;

INSERT INTO `config` (`_IDconf`, `_ident`, `_title`, `_texte`, `_crawler`, `_tdcolor`, `_align`, `_login`, `_nologin`, `_IDtheme`, `_bgcolor`, `_puce`, `_fond`, `_page`, `_header`, `_adresse`, `_cp`, `_ville`, `_tel`, `_fax`, `_web`, `_email`, `_logo1`, `_logo2`, `_webmaster`, `_visible`, `_lang`, `_bandeau`)
VALUES
	(10,'Mon centre','Prométhée','Bienvenue sur votre intranet',NULL,NULL,'C','Veuillez taper votre adresse mail et votre mot de passe pour vous connecter.','Le site est provisoirement inaccessible pour cause de maintenance.\r\nMerci de bien vouloir revenir plus tard.',0,'N',NULL,NULL,'N',NULL,'','','','','','https://www.promethee-solutions.fr/ent-libre/','contact@ip-solutions.fr','N','O',NULL,'O','fr',0);

/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_centre
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_centre`;

CREATE TABLE `config_centre` (
  `_IDcentre` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_adresse` varchar(60) NOT NULL,
  `_tel` varchar(20) NOT NULL,
  `_fax` varchar(20) NOT NULL,
  `_web` varchar(60) NOT NULL,
  `_email` varchar(60) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  `_semaines` text NOT NULL,
  `_vacances` text NOT NULL,
  PRIMARY KEY (`_IDcentre`,`_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des centres de formation';

LOCK TABLES `config_centre` WRITE;
/*!40000 ALTER TABLE `config_centre` DISABLE KEYS */;

INSERT INTO `config_centre` (`_IDcentre`, `_ident`, `_adresse`, `_tel`, `_fax`, `_web`, `_email`, `_visible`, `_lang`, `_semaines`, `_vacances`)
VALUES
	(1,'Prométhée','','','','','','O','fr','[1,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null]','{\"start\":[\"17/10/2020\",\"19/12/2020\",\"20/02/2021\",\"24/04/2021\",\"12/05/2021\"],\"end\":[\"02/11/2020\",\"04/01/2021\",\"08/03/2021\",\"10/05/2021\",\"17/05/2021\"]}');

/*!40000 ALTER TABLE `config_centre` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_charset
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_charset`;

CREATE TABLE `config_charset` (
  `_IDchar` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_charset` varchar(20) NOT NULL,
  PRIMARY KEY (`_IDchar`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de l''encodage des caractères';

LOCK TABLES `config_charset` WRITE;
/*!40000 ALTER TABLE `config_charset` DISABLE KEYS */;

INSERT INTO `config_charset` (`_IDchar`, `_charset`)
VALUES
	(1,'utf-8'),
	(2,'utf-16'),
	(3,'iso-8859-1'),
	(4,'iso-8859-15'),
	(5,'windows-1252');

/*!40000 ALTER TABLE `config_charset` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_database
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_database`;

CREATE TABLE `config_database` (
  `_IDconf` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_version` varchar(12) NOT NULL,
  `_IPv6` varchar(24) NOT NULL,
  `_date` datetime DEFAULT NULL,
  `_table` int(10) unsigned NOT NULL,
  `_retcode` int(10) unsigned NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDconf`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de configuration de la base de données';

LOCK TABLES `config_database` WRITE;
/*!40000 ALTER TABLE `config_database` DISABLE KEYS */;

INSERT INTO `config_database` (`_IDconf`, `_version`, `_IPv6`, `_date`, `_table`, `_retcode`, `_lang`)
VALUES
	(1,'12.0','127.0.0.1','2013-07-15 09:44:27',232,0,'fr');

/*!40000 ALTER TABLE `config_database` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_def
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_def`;

CREATE TABLE `config_def` (
  `_IDdef` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcentre` int(10) unsigned NOT NULL,
  `_ident` varchar(20) NOT NULL,
  `_text` varchar(40) NOT NULL DEFAULT '' COMMENT 'Mettre tout en minuscule!',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDdef`),
  KEY `_IDcentre index` (`_IDcentre`),
  CONSTRAINT `IDcentre to config_centre` FOREIGN KEY (`_IDcentre`) REFERENCES `config_centre` (`_IDcentre`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table de configuration des définitions des mot-clefs';

LOCK TABLES `config_def` WRITE;
/*!40000 ALTER TABLE `config_def` DISABLE KEYS */;

INSERT INTO `config_def` (`_IDdef`, `_IDcentre`, `_ident`, `_text`, `_lang`)
VALUES
	(109,1,'_STUDENT','étudiant','fr'),
	(110,1,'_COURSE','cours','fr'),
	(111,1,'_CLASS','classe','fr'),
	(123,1,'_PLACE','salle','fr'),
	(124,1,'_TEACHER','professeur','fr'),
	(125,1,'_CENTER','centre','fr'),
	(126,1,'_USER','utilisateur','fr'),
	(127,1,'_SAVE','enregistrer','fr'),
	(128,1,'_BACK','retour','fr'),
	(129,1,'_RESULT','résultat','fr');

/*!40000 ALTER TABLE `config_def` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_logo
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_logo`;

CREATE TABLE `config_logo` (
  `_IDlogo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDlogo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des logos';



# Affichage de la table config_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_menu`;

CREATE TABLE `config_menu` (
  `_IDmenu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(20) NOT NULL,
  `_text` tinytext NOT NULL,
  `_order` int(11) NOT NULL,
  `_marquee` enum('O','N') NOT NULL DEFAULT 'N',
  `_img` varchar(20) DEFAULT NULL,
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '255',
  `_anonyme` enum('O','N') NOT NULL DEFAULT 'O',
  `_backoffice` varchar(80) NOT NULL,
  `_sort` enum('O','N') NOT NULL DEFAULT 'O',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_activate` enum('O','N') NOT NULL DEFAULT 'O',
  `_table` varchar(20) NOT NULL DEFAULT 'config_submenu',
  `_type` int(10) unsigned NOT NULL DEFAULT '0',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDmenu`,`_lang`),
  UNIQUE KEY `_key` (`_ident`,`_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des menus de la page d''accueil';

LOCK TABLES `config_menu` WRITE;
/*!40000 ALTER TABLE `config_menu` DISABLE KEYS */;

INSERT INTO `config_menu` (`_IDmenu`, `_ident`, `_text`, `_order`, `_marquee`, `_img`, `_IDgrprd`, `_anonyme`, `_backoffice`, `_sort`, `_visible`, `_activate`, `_table`, `_type`, `_lang`)
VALUES
	(22,'Messagerie','',5,'N','fa-inbox',31,'N','','N','O','O','config_submenu',0,'fr'),
	(28,'','',0,'N','default.png',255,'O','','O','O','O','config_submenu',0,''),
	(29,'Planning','',1,'N','fa-calendar',31,'O','','O','O','O','config_submenu',0,'fr'),
	(33,'Administration','',6,'N','fa-cog',8,'O','','N','O','O','config_submenu',0,'fr'),
	(34,'Mon espace','',2,'N','fa-briefcase',31,'O','','O','O','O','config_submenu',0,'fr'),
	(35,'Gestion','',4,'N','fa-building',11,'O','','N','O','O','config_submenu',0,'fr'),
	(36,'Notes / Examens','',3,'N','fa-user-graduate',255,'O','','O','O','O','config_submenu',0,'fr');

/*!40000 ALTER TABLE `config_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_mime
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_mime`;

CREATE TABLE `config_mime` (
  `_IDmime` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ext` varchar(5) NOT NULL,
  `_mime` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDmime`),
  UNIQUE KEY `_key` (`_ext`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des fichiers autorisés';



# Affichage de la table config_submenu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_submenu`;

CREATE TABLE `config_submenu` (
  `_IDsubmenu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmenu` int(10) unsigned NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_link` varchar(255) NOT NULL,
  `_url` enum('O','N') NOT NULL DEFAULT 'O',
  `_order` int(11) NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '255',
  `_anonyme` enum('O','N') NOT NULL DEFAULT 'N',
  `_backoffice` enum('O','N') NOT NULL DEFAULT 'N',
  `_image` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_type` int(10) unsigned NOT NULL DEFAULT '0',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDsubmenu`),
  KEY `config_submenu IDmenu index` (`_IDmenu`),
  CONSTRAINT `config_submenu IDmenu to config_menu IDmenu` FOREIGN KEY (`_IDmenu`) REFERENCES `config_menu` (`_IDmenu`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des sous menus';

LOCK TABLES `config_submenu` WRITE;
/*!40000 ALTER TABLE `config_submenu` DISABLE KEYS */;

INSERT INTO `config_submenu` (`_IDsubmenu`, `_IDmenu`, `_ident`, `_link`, `_url`, `_order`, `_IDgrprd`, `_anonyme`, `_backoffice`, `_image`, `_visible`, `_type`, `_lang`)
VALUES
	(189,35,'Liste des absences','item=63','O',4,31,'N','O','','O',0,'fr'),
	(190,29,'Emplois du temps','item=64','O',1,31,'N','N','','O',0,'fr'),
	(216,33,'Accès à l\'ENT','item=21&cmde=access','O',2,255,'N','N','','N',0,'fr'),
	(217,33,'Configuration','item=21','O',3,255,'N','N','','O',0,'fr'),
	(219,33,'Logs de connexion','item=92','O',31,255,'N','N','','O',0,'fr'),
	(220,33,'Logs IP','item=92&cmde=ip','O',32,255,'N','N','','O',0,'fr'),
	(234,35,'Liste des _CLASSs','item=9&amp;cmde=class','O',103,10,'N','N','','O',0,'fr'),
	(240,34,'Cahier de Texte','item=13','O',2,255,'N','O','','O',0,'fr'),
	(262,34,'Mes absences','item=63','O',4,1,'N','N','','O',2,'fr'),
	(472,36,'Saisie des notes','item=60&cmde=post','O',11,24,'N','N','','O',0,'fr'),
	(475,33,'Liste des comptes','item=1','O',41,10,'N','N','','O',0,'fr'),
	(476,35,'Liste des _STUDENTs','item=38','O',2,10,'N','N','','O',0,'fr'),
	(477,33,'Statistiques','item=7','O',34,0,'N','N','','O',0,'fr'),
	(493,22,'Mes messages','item=4','O',493,31,'N','N','','O',0,'fr'),
	(497,36,'Liste des notes','item=60','O',10,26,'N','N','','O',0,'fr'),
	(498,22,'Nouveau message','item=4&cmde=post','O',498,31,'N','N','','O',0,'fr'),
	(499,22,'Liste de diffusion','item=11','O',499,31,'N','N','','O',0,'fr'),
	(500,34,'Syllabus','item=69','O',3,11,'N','N','','O',0,'fr'),
	(501,36,'Liste des examens','item=70','O',12,11,'N','N','','O',0,'fr'),
	(502,29,'Liste de l\'EDT','item=29','O',500,2,'N','N','','O',0,'fr'),
	(504,33,'Texte des emails','item=112&cmde=gestion&tradlang=fr&filemodule=mail','O',51,255,'N','N','','O',0,'fr'),
	(506,36,'Import des copies','item=60&cmde=barcode','O',13,24,'N','N','','O',0,'fr'),
	(510,33,'Logs mails','item=92&cmde=mail','O',33,255,'N','N','','O',0,'fr'),
	(511,35,'Liste des heures de stage','item=62','O',102,8,'N','N','','O',0,'fr'),
	(512,35,'Liste des rattrapages','item=65','O',105,31,'N','N','','O',0,'fr'),
	(517,33,'Logs','','O',30,255,'N','N','','O',1,'fr'),
	(518,33,'Utilisateurs','','O',40,255,'N','N','','O',1,'fr'),
	(519,33,'L\'ENT','','O',1,255,'N','N','','N',1,'fr'),
	(520,33,'Traductions','','O',50,255,'N','N','','O',1,'fr'),
	(521,34,'Mes fichiers','item=28','O',6,255,'N','N','','O',0,'fr'),
	(522,35,'Liste des syllabus','item=69','O',3,11,'N','N','','O',0,'fr'),
	(523,34,'Mes notes','item=60','O',5,1,'N','N','','O',0,'fr'),
	(524,35,'LISTES','','O',1,255,'N','N','','O',1,'fr');

/*!40000 ALTER TABLE `config_submenu` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_theme
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_theme`;

CREATE TABLE `config_theme` (
  `_IDtheme` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_theme` varchar(10) NOT NULL,
  `_color` varchar(7) NOT NULL,
  `_bgcolor` varchar(7) NOT NULL,
  PRIMARY KEY (`_IDtheme`),
  UNIQUE KEY `_key` (`_theme`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des habillages (skins) de l''intranet';

LOCK TABLES `config_theme` WRITE;
/*!40000 ALTER TABLE `config_theme` DISABLE KEYS */;

INSERT INTO `config_theme` (`_IDtheme`, `_theme`, `_color`, `_bgcolor`)
VALUES
	(2,'green','#009000','#CCFFCC'),
	(3,'blue','#006699','#CCCCFF'),
	(4,'red','#D01C1C','#FFCCFF'),
	(5,'brown','#720A02','#FFCC66'),
	(6,'pistachio','#125C65','#FFFF99'),
	(7,'orange','#FF9900','#FFCC99'),
	(8,'custom','#FF9900','#FFCC99'),
	(1,'default','#000000','#FFFFFF');

/*!40000 ALTER TABLE `config_theme` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table config_timezone
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_timezone`;

CREATE TABLE `config_timezone` (
  `_IDzone` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_timezone` varchar(40) NOT NULL,
  PRIMARY KEY (`_IDzone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des fuseaux horaires';

LOCK TABLES `config_timezone` WRITE;
/*!40000 ALTER TABLE `config_timezone` DISABLE KEYS */;

INSERT INTO `config_timezone` (`_IDzone`, `_timezone`)
VALUES
	(1,'Europe/Amsterdam'),
	(2,'Europe/Berlin'),
	(3,'Europe/Chisinau'),
	(4,'Europe/Helsinki'),
	(5,'Europe/Kiev'),
	(6,'Europe/Madrid'),
	(7,'Europe/Moscow'),
	(8,'Europe/Prague'),
	(9,'Europe/Sarajevo'),
	(10,'Europe/Tallinn'),
	(11,'Europe/Vatican'),
	(12,'Europe/Zagreb'),
	(13,'Europe/Andorra'),
	(14,'Europe/Bratislava'),
	(15,'Europe/Copenhagen'),
	(16,'Europe/Isle_of_Man'),
	(17,'Europe/Lisbon'),
	(18,'Europe/Malta'),
	(19,'Europe/Nicosia'),
	(20,'Europe/Riga'),
	(21,'Europe/Simferopol'),
	(22,'Europe/Tirane'),
	(23,'Europe/Vienna'),
	(24,'Europe/Zaporozhye'),
	(25,'Europe/Athens'),
	(26,'Europe/Brussels'),
	(27,'Europe/Dublin'),
	(28,'Europe/Istanbul'),
	(29,'Europe/Ljubljana'),
	(30,'Europe/Mariehamn'),
	(31,'Europe/Oslo'),
	(32,'Europe/Rome'),
	(33,'Europe/Skopje'),
	(34,'Europe/Tiraspol'),
	(35,'Europe/Vilnius'),
	(36,'Europe/Zurich'),
	(37,'Europe/Belfast'),
	(38,'Europe/Bucharest'),
	(39,'Europe/Gibraltar'),
	(40,'Europe/Jersey'),
	(41,'Europe/London'),
	(42,'Europe/Minsk'),
	(43,'Europe/Paris'),
	(44,'Europe/Samara'),
	(45,'Europe/Sofia'),
	(46,'Europe/Uzhgorod'),
	(47,'Europe/Volgograd'),
	(48,'Europe/Belgrade'),
	(49,'Europe/Budapest'),
	(50,'Europe/Guernsey'),
	(51,'Europe/Kaliningrad'),
	(52,'Europe/Luxembourg'),
	(53,'Europe/Monaco'),
	(54,'Europe/Podgorica'),
	(55,'Europe/San_Marino'),
	(56,'Europe/Stockholm'),
	(57,'Europe/Vaduz'),
	(58,'Europe/Warsaw'),
	(59,'Africa/Abidjan'),
	(60,'Africa/Asmera'),
	(61,'Africa/Blantyre'),
	(62,'Africa/Ceuta'),
	(63,'Africa/Douala'),
	(64,'Africa/Johannesburg'),
	(65,'Africa/Lagos'),
	(66,'Africa/Lusaka'),
	(67,'Africa/Mogadishu'),
	(68,'Africa/Nouakchott'),
	(69,'Africa/Tripoli'),
	(70,'Africa/Accra'),
	(71,'Africa/Bamako'),
	(72,'Africa/Brazzaville'),
	(73,'Africa/Conakry'),
	(74,'Africa/El_Aaiun'),
	(75,'Africa/Kampala'),
	(76,'Africa/Libreville'),
	(77,'Africa/Malabo'),
	(78,'Africa/Monrovia'),
	(79,'Africa/Ouagadougou'),
	(80,'Africa/Tunis'),
	(81,'Africa/Addis_Ababa'),
	(82,'Africa/Bangui'),
	(83,'Africa/Bujumbura'),
	(84,'Africa/Dakar'),
	(85,'Africa/Freetown'),
	(86,'Africa/Khartoum'),
	(87,'Africa/Lome'),
	(88,'Africa/Maputo'),
	(89,'Africa/Nairobi'),
	(90,'Africa/Porto-Novo'),
	(91,'Africa/Windhoek'),
	(92,'Africa/Algiers'),
	(93,'Africa/Banjul'),
	(94,'Africa/Cairo'),
	(95,'Africa/Dar_es_Salaam'),
	(96,'Africa/Gaborone'),
	(97,'Africa/Kigali'),
	(98,'Africa/Luanda'),
	(99,'Africa/Maseru'),
	(100,'Africa/Ndjamena'),
	(101,'Africa/Sao_Tome'),
	(102,'Africa/Asmara'),
	(103,'Africa/Bissau'),
	(104,'Africa/Casablanca'),
	(105,'Africa/Djibouti'),
	(106,'Africa/Harare'),
	(107,'Africa/Kinshasa'),
	(108,'Africa/Lubumbashi'),
	(109,'Africa/Mbabane'),
	(110,'Africa/Niamey'),
	(111,'Africa/Timbuktu'),
	(112,'America/Adak'),
	(113,'America/Argentina/Buenos_Aires'),
	(114,'America/Argentina/La_Rioja'),
	(115,'America/Argentina/San_Luis'),
	(116,'America/Atikokan'),
	(117,'America/Belem'),
	(118,'America/Boise'),
	(119,'America/Caracas'),
	(120,'America/Chihuahua'),
	(121,'America/Curacao'),
	(122,'America/Detroit'),
	(123,'America/Ensenada'),
	(124,'America/Goose_Bay'),
	(125,'America/Guayaquil'),
	(126,'America/Indiana/Indianapolis'),
	(127,'America/Indiana/Vevay'),
	(128,'America/Iqaluit'),
	(129,'America/Kentucky/Monticello'),
	(130,'America/Louisville'),
	(131,'America/Martinique'),
	(132,'America/Merida'),
	(133,'America/Montevideo'),
	(134,'America/Nipigon'),
	(135,'America/North_Dakota/New_Salem'),
	(136,'America/Phoenix'),
	(137,'America/Puerto_Rico'),
	(138,'America/Resolute'),
	(139,'America/Santiago'),
	(140,'America/St_Barthelemy'),
	(141,'America/St_Vincent'),
	(142,'America/Tijuana'),
	(143,'America/Whitehorse'),
	(144,'America/Anchorage'),
	(145,'America/Argentina/Catamarca'),
	(146,'America/Argentina/Mendoza'),
	(147,'America/Argentina/Tucuman'),
	(148,'America/Atka'),
	(149,'America/Belize'),
	(150,'America/Buenos_Aires'),
	(151,'America/Catamarca'),
	(152,'America/Coral_Harbour'),
	(153,'America/Danmarkshavn'),
	(154,'America/Dominica'),
	(155,'America/Fort_Wayne'),
	(156,'America/Grand_Turk'),
	(157,'America/Guyana'),
	(158,'America/Indiana/Knox'),
	(159,'America/Indiana/Vincennes'),
	(160,'America/Jamaica'),
	(161,'America/Knox_IN'),
	(162,'America/Maceio'),
	(163,'America/Matamoros'),
	(164,'America/Mexico_City'),
	(165,'America/Montreal'),
	(166,'America/Nome'),
	(167,'America/Ojinaga'),
	(168,'America/Port-au-Prince'),
	(169,'America/Rainy_River'),
	(170,'America/Rio_Branco'),
	(171,'America/Santo_Domingo'),
	(172,'America/St_Johns'),
	(173,'America/Swift_Current'),
	(174,'America/Toronto'),
	(175,'America/Winnipeg'),
	(176,'America/Anguilla'),
	(177,'America/Argentina/ComodRivadavia'),
	(178,'America/Argentina/Rio_Gallegos'),
	(179,'America/Argentina/Ushuaia'),
	(180,'America/Bahia'),
	(181,'America/Blanc-Sablon'),
	(182,'America/Cambridge_Bay'),
	(183,'America/Cayenne'),
	(184,'America/Cordoba'),
	(185,'America/Dawson'),
	(186,'America/Edmonton'),
	(187,'America/Fortaleza'),
	(188,'America/Grenada'),
	(189,'America/Halifax'),
	(190,'America/Indiana/Marengo'),
	(191,'America/Indiana/Winamac'),
	(192,'America/Jujuy'),
	(193,'America/La_Paz'),
	(194,'America/Managua'),
	(195,'America/Mazatlan'),
	(196,'America/Miquelon'),
	(197,'America/Montserrat'),
	(198,'America/Noronha'),
	(199,'America/Panama'),
	(200,'America/Port_of_Spain'),
	(201,'America/Rankin_Inlet'),
	(202,'America/Rosario'),
	(203,'America/Sao_Paulo'),
	(204,'America/St_Kitts'),
	(205,'America/Tegucigalpa'),
	(206,'America/Tortola'),
	(207,'America/Yakutat'),
	(208,'America/Antigua'),
	(209,'America/Argentina/Cordoba'),
	(210,'America/Argentina/Salta'),
	(211,'America/Aruba'),
	(212,'America/Bahia_Banderas'),
	(213,'America/Boa_Vista'),
	(214,'America/Campo_Grande'),
	(215,'America/Cayman'),
	(216,'America/Costa_Rica'),
	(217,'America/Dawson_Creek'),
	(218,'America/Eirunepe'),
	(219,'America/Glace_Bay'),
	(220,'America/Guadeloupe'),
	(221,'America/Havana'),
	(222,'America/Indiana/Petersburg'),
	(223,'America/Indianapolis'),
	(224,'America/Juneau'),
	(225,'America/Lima'),
	(226,'America/Manaus'),
	(227,'America/Mendoza'),
	(228,'America/Moncton'),
	(229,'America/Nassau'),
	(230,'America/North_Dakota/Beulah'),
	(231,'America/Pangnirtung'),
	(232,'America/Porto_Acre'),
	(233,'America/Recife'),
	(234,'America/Santa_Isabel'),
	(235,'America/Scoresbysund'),
	(236,'America/St_Lucia'),
	(237,'America/Thule'),
	(238,'America/Vancouver'),
	(239,'America/Yellowknife'),
	(240,'America/Araguaina'),
	(241,'America/Argentina/Jujuy'),
	(242,'America/Argentina/San_Juan'),
	(243,'America/Asuncion'),
	(244,'America/Barbados'),
	(245,'America/Bogota'),
	(246,'America/Cancun'),
	(247,'America/Chicago'),
	(248,'America/Cuiaba'),
	(249,'America/Denver'),
	(250,'America/El_Salvador'),
	(251,'America/Godthab'),
	(252,'America/Guatemala'),
	(253,'America/Hermosillo'),
	(254,'America/Indiana/Tell_City'),
	(255,'America/Inuvik'),
	(256,'America/Kentucky/Louisville'),
	(257,'America/Los_Angeles'),
	(258,'America/Marigot'),
	(259,'America/Menominee'),
	(260,'America/Monterrey'),
	(261,'America/New_York'),
	(262,'America/North_Dakota/Center'),
	(263,'America/Paramaribo'),
	(264,'America/Porto_Velho'),
	(265,'America/Regina'),
	(266,'America/Santarem'),
	(267,'America/Shiprock'),
	(268,'America/St_Thomas'),
	(269,'America/Thunder_Bay'),
	(270,'America/Virgin'),
	(271,'Asia/Aden'),
	(272,'Asia/Aqtobe'),
	(273,'Asia/Baku'),
	(274,'Asia/Calcutta'),
	(275,'Asia/Dacca'),
	(276,'Asia/Dushanbe'),
	(277,'Asia/Hovd'),
	(278,'Asia/Jerusalem'),
	(279,'Asia/Kathmandu'),
	(280,'Asia/Kuching'),
	(281,'Asia/Makassar'),
	(282,'Asia/Novosibirsk'),
	(283,'Asia/Pyongyang'),
	(284,'Asia/Saigon'),
	(285,'Asia/Singapore'),
	(286,'Asia/Tel_Aviv'),
	(287,'Asia/Ulaanbaatar'),
	(288,'Asia/Yakutsk'),
	(289,'Asia/Almaty'),
	(290,'Asia/Ashgabat'),
	(291,'Asia/Bangkok'),
	(292,'Asia/Choibalsan'),
	(293,'Asia/Damascus'),
	(294,'Asia/Gaza'),
	(295,'Asia/Irkutsk'),
	(296,'Asia/Kabul'),
	(297,'Asia/Katmandu'),
	(298,'Asia/Kuwait'),
	(299,'Asia/Manila'),
	(300,'Asia/Omsk'),
	(301,'Asia/Qatar'),
	(302,'Asia/Sakhalin'),
	(303,'Asia/Taipei'),
	(304,'Asia/Thimbu'),
	(305,'Asia/Ulan_Bator'),
	(306,'Asia/Yekaterinburg'),
	(307,'Asia/Amman'),
	(308,'Asia/Ashkhabad'),
	(309,'Asia/Beirut'),
	(310,'Asia/Chongqing'),
	(311,'Asia/Dhaka'),
	(312,'Asia/Harbin'),
	(313,'Asia/Istanbul'),
	(314,'Asia/Kamchatka'),
	(315,'Asia/Kolkata'),
	(316,'Asia/Macao'),
	(317,'Asia/Muscat'),
	(318,'Asia/Oral'),
	(319,'Asia/Qyzylorda'),
	(320,'Asia/Samarkand'),
	(321,'Asia/Tashkent'),
	(322,'Asia/Thimphu'),
	(323,'Asia/Urumqi'),
	(324,'Asia/Yerevan'),
	(325,'Asia/Anadyr'),
	(326,'Asia/Baghdad'),
	(327,'Asia/Bishkek'),
	(328,'Asia/Chungking'),
	(329,'Asia/Dili'),
	(330,'Asia/Ho_Chi_Minh'),
	(331,'Asia/Jakarta'),
	(332,'Asia/Karachi'),
	(333,'Asia/Krasnoyarsk'),
	(334,'Asia/Macau'),
	(335,'Asia/Nicosia'),
	(336,'Asia/Phnom_Penh'),
	(337,'Asia/Rangoon'),
	(338,'Asia/Seoul'),
	(339,'Asia/Tbilisi'),
	(340,'Asia/Tokyo'),
	(341,'Asia/Vientiane'),
	(342,'Asia/Aqtau'),
	(343,'Asia/Bahrain'),
	(344,'Asia/Brunei'),
	(345,'Asia/Colombo'),
	(346,'Asia/Dubai'),
	(347,'Asia/Hong_Kong'),
	(348,'Asia/Jayapura'),
	(349,'Asia/Kashgar'),
	(350,'Asia/Kuala_Lumpur'),
	(351,'Asia/Magadan'),
	(352,'Asia/Novokuznetsk'),
	(353,'Asia/Pontianak'),
	(354,'Asia/Riyadh'),
	(355,'Asia/Shanghai'),
	(356,'Asia/Tehran'),
	(357,'Asia/Ujung_Pandang'),
	(358,'Asia/Vladivostok'),
	(359,'Atlantic/Azores'),
	(360,'Atlantic/Faroe'),
	(361,'Atlantic/St_Helena'),
	(362,'Atlantic/Bermuda'),
	(363,'Atlantic/Jan_Mayen'),
	(364,'Atlantic/Stanley'),
	(365,'Atlantic/Canary'),
	(366,'Atlantic/Madeira'),
	(367,'Atlantic/Cape_Verde'),
	(368,'Atlantic/Reykjavik'),
	(369,'Atlantic/Faeroe'),
	(370,'Atlantic/South_Georgia'),
	(371,'Australia/ACT'),
	(372,'Australia/Currie'),
	(373,'Australia/Lindeman'),
	(374,'Australia/Perth'),
	(375,'Australia/Victoria'),
	(376,'Australia/Adelaide'),
	(377,'Australia/Darwin'),
	(378,'Australia/Lord_Howe'),
	(379,'Australia/Queensland'),
	(380,'Australia/West'),
	(381,'Australia/Brisbane'),
	(382,'Australia/Eucla'),
	(383,'Australia/Melbourne'),
	(384,'Australia/South'),
	(385,'Australia/Yancowinna'),
	(386,'Australia/Broken_Hill'),
	(387,'Australia/Hobart'),
	(388,'Australia/North'),
	(389,'Australia/Sydney'),
	(390,'Australia/Canberra'),
	(391,'Australia/LHI'),
	(392,'Australia/NSW'),
	(393,'Australia/Tasmania'),
	(394,'Indian/Antananarivo'),
	(395,'Indian/Kerguelen'),
	(396,'Indian/Reunion'),
	(397,'Indian/Chagos'),
	(398,'Indian/Mahe'),
	(399,'Indian/Christmas'),
	(400,'Indian/Maldives'),
	(401,'Indian/Cocos'),
	(402,'Indian/Mauritius'),
	(403,'Indian/Comoro'),
	(404,'Indian/Mayotte'),
	(405,'Pacific/Apia'),
	(406,'Pacific/Efate'),
	(407,'Pacific/Galapagos'),
	(408,'Pacific/Johnston'),
	(409,'Pacific/Marquesas'),
	(410,'Pacific/Noumea'),
	(411,'Pacific/Ponape'),
	(412,'Pacific/Tahiti'),
	(413,'Pacific/Wallis'),
	(414,'Pacific/Auckland'),
	(415,'Pacific/Enderbury'),
	(416,'Pacific/Gambier'),
	(417,'Pacific/Kiritimati'),
	(418,'Pacific/Midway'),
	(419,'Pacific/Pago_Pago'),
	(420,'Pacific/Port_Moresby'),
	(421,'Pacific/Tarawa'),
	(422,'Pacific/Yap'),
	(423,'Pacific/Chatham');

/*!40000 ALTER TABLE `config_timezone` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cours
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cours`;

CREATE TABLE `cours` (
  `_IDcours` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmat` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_titre` varchar(60) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_IDgrp` text NOT NULL,
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '255',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_PJ` int(10) unsigned NOT NULL DEFAULT '1',
  `_sort` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDcours`),
  UNIQUE KEY `_IDmat` (`_IDmat`,`_titre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des cours en ligne';



# Affichage de la table cours_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cours_data`;

CREATE TABLE `cours_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDparent` int(10) unsigned NOT NULL,
  `_IDcours` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_titre` varchar(40) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_info` datetime NOT NULL,
  `_open` datetime NOT NULL,
  `_max` enum('O','N') NOT NULL DEFAULT 'N',
  `_usability` int(10) unsigned NOT NULL DEFAULT '0',
  `_order` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_IDparent` (`_IDparent`,`_IDcours`,`_titre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des répertoires des items des cours';



# Affichage de la table cours_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cours_items`;

CREATE TABLE `cours_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_IDcours` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_titre` varchar(40) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_open` datetime NOT NULL,
  `_file` varchar(80) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_ver` varchar(10) NOT NULL DEFAULT '1.0',
  `_usability` int(10) unsigned NOT NULL DEFAULT '0',
  `_note` mediumtext NOT NULL,
  `_time` time NOT NULL,
  `_order` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des items par cours';

LOCK TABLES `cours_items` WRITE;
/*!40000 ALTER TABLE `cours_items` DISABLE KEYS */;

INSERT INTO `cours_items` (`_IDitem`, `_IDdata`, `_IDcours`, `_ID`, `_IP`, `_date`, `_titre`, `_texte`, `_open`, `_file`, `_size`, `_ver`, `_usability`, `_note`, `_time`, `_order`, `_visible`)
VALUES
	(1,0,0,9,-129,'2018-11-29 16:44:30','','','2018-11-29 16:44:30','LogoIPS.jpg',1551,'',0,'','00:00:00',0,'O');

/*!40000 ALTER TABLE `cours_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ctn
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ctn`;

CREATE TABLE `ctn` (
  `_IDctn` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDclass` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgroup` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_PJ` enum('O','N') NOT NULL DEFAULT 'N',
  `_diary` enum('O','N') NOT NULL DEFAULT 'N',
  `_currdate` enum('O','N') NOT NULL DEFAULT 'O',
  `_display` enum('D','W','M') NOT NULL DEFAULT 'D',
  `_limited` enum('O','N') NOT NULL DEFAULT 'N',
  `_common` enum('O','N') NOT NULL DEFAULT 'N',
  `_horaire` tinytext NOT NULL,
  `_font` int(10) unsigned NOT NULL DEFAULT '10',
  `_rss` enum('O','N') NOT NULL DEFAULT 'N',
  `_sndmail` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`_IDctn`),
  UNIQUE KEY `_IDclass` (`_IDclass`,`_IDgroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des cahiers de texte numériques';

LOCK TABLES `ctn` WRITE;
/*!40000 ALTER TABLE `ctn` DISABLE KEYS */;

INSERT INTO `ctn` (`_IDctn`, `_IDclass`, `_IDgroup`, `_IDcentre`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_month`, `_visible`, `_PJ`, `_diary`, `_currdate`, `_display`, `_limited`, `_common`, `_horaire`, `_font`, `_rss`, `_sndmail`)
VALUES
	(6,256,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(7,255,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(8,254,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(9,253,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(10,252,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(11,248,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(12,249,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(13,250,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(14,251,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(15,259,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(16,247,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(17,257,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N'),
	(18,258,0,1,0,0,27,8,'O','O','O','O','D','N','N','1:00,2:00,3:00,4:00',10,'N','N');

/*!40000 ALTER TABLE `ctn` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ctn_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ctn_data`;

CREATE TABLE `ctn_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDitem` int(10) unsigned NOT NULL,
  `_type` int(10) unsigned NOT NULL DEFAULT '0',
  `_todo` datetime NOT NULL,
  `_text` mediumtext NOT NULL,
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_key` (`_IDitem`,`_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des commentaires des PJ du cahier de texte';

LOCK TABLES `ctn_data` WRITE;
/*!40000 ALTER TABLE `ctn_data` DISABLE KEYS */;

INSERT INTO `ctn_data` (`_IDdata`, `_IDitem`, `_type`, `_todo`, `_text`)
VALUES
	(1,2,1,'0000-00-00 00:00:00',''),
	(2,2,2,'0000-00-00 00:00:00','');

/*!40000 ALTER TABLE `ctn_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ctn_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ctn_items`;

CREATE TABLE `ctn_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDctn` varchar(200) NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL DEFAULT '0',
  `_ID` int(10) unsigned NOT NULL,
  `_IP` text,
  `_date` datetime NOT NULL,
  `_delay` varchar(20) NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_raw` enum('O','N') NOT NULL DEFAULT 'O',
  `_note` mediumtext NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_type` int(11) NOT NULL DEFAULT '0' COMMENT '0 = Fait, 1 = A faire',
  `_IDcours` int(11) NOT NULL DEFAULT '0' COMMENT 'En relation avec l''edt',
  `_nosemaine` int(11) NOT NULL DEFAULT '0',
  `_devoirs` longtext NOT NULL,
  `_observ` longtext NOT NULL,
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des articles des cahiers de texte numériques';

LOCK TABLES `ctn_items` WRITE;
/*!40000 ALTER TABLE `ctn_items` DISABLE KEYS */;

INSERT INTO `ctn_items` (`_IDitem`, `_IDctn`, `_IDmat`, `_ID`, `_IP`, `_date`, `_delay`, `_title`, `_texte`, `_raw`, `_note`, `_visible`, `_type`, `_IDcours`, `_nosemaine`, `_devoirs`, `_observ`)
VALUES
	(116,';286;',122,3126,'192.168.1.1','2020-10-22 00:00:00','','chap.','<p>Contenu du cours</p>','N','','O',0,21,41,'<p>A faire avant le cours</p>',''),
	(537,';287;',122,1,'192.168.1.1','2020-10-21 00:00:00','','chap.','<p>Test</p>','N','','O',0,2,43,'<p>coucou</p>',''),
	(538,';287;',145,3173,'192.168.1.1','2020-10-21 00:00:00','','chap.','<p>Contenu du cours</p>','N','','O',0,55,43,'<p>A faire avant le cours</p>','');

/*!40000 ALTER TABLE `ctn_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ctn_pj
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ctn_pj`;

CREATE TABLE `ctn_pj` (
  `_IDpj` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDitem` int(10) unsigned NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_ext` varchar(5) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_type` int(10) unsigned NOT NULL DEFAULT '0',
  `_name` varchar(400) NOT NULL,
  PRIMARY KEY (`_IDpj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des PJ du cahier de texte';



# Affichage de la table ctn_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ctn_user`;

CREATE TABLE `ctn_user` (
  `_IDuser` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDclass` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL DEFAULT '0',
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_year` int(10) unsigned NOT NULL,
  `_file` varchar(80) NOT NULL,
  PRIMARY KEY (`_IDuser`),
  UNIQUE KEY `_key` (`_IDclass`,`_IDmat`,`_year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des cahiers de texte numériques personnels';



# Affichage de la table ctn_vu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ctn_vu`;

CREATE TABLE `ctn_vu` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL,
  `_date` datetime NOT NULL,
  UNIQUE KEY `_key` (`_IDitem`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de log des articles lus';



# Affichage de la table cursus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cursus`;

CREATE TABLE `cursus` (
  `_IDcursus` int(10) unsigned NOT NULL,
  `_titre` varchar(30) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDcursus`,`_lang`),
  UNIQUE KEY `_key` (`_titre`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des cursus';

LOCK TABLES `cursus` WRITE;
/*!40000 ALTER TABLE `cursus` DISABLE KEYS */;

INSERT INTO `cursus` (`_IDcursus`, `_titre`, `_texte`, `_visible`, `_lang`)
VALUES
	(1,'Initial training','','O','en'),
	(2,'Continuous training','','N','en'),
	(3,'Formation by Alternation','','N','en'),
	(1,'Formacion inicial','','O','es'),
	(2,'Formacion continua','','N','es'),
	(3,'Formacion por alternancia','','N','es'),
	(1,'Formation Initiale','','O','fr'),
	(2,'Formation Continue','','N','fr'),
	(3,'Formation par Alternance','','N','fr');

/*!40000 ALTER TABLE `cursus` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cursus_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cursus_data`;

CREATE TABLE `cursus_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcursus` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_key` (`_IDcursus`,`_IDmat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des modules des cursus';

LOCK TABLES `cursus_data` WRITE;
/*!40000 ALTER TABLE `cursus_data` DISABLE KEYS */;

INSERT INTO `cursus_data` (`_IDdata`, `_IDcursus`, `_IDmat`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_visible`)
VALUES
	(1,1,1,0,2,2,'O');

/*!40000 ALTER TABLE `cursus_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cursus_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cursus_items`;

CREATE TABLE `cursus_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_IDgrprd` text NOT NULL,
  `_titre` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_file` varchar(80) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_IDitem`),
  UNIQUE KEY `_key` (`_IDdata`,`_titre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des descriptions des modules des cursus';

LOCK TABLES `cursus_items` WRITE;
/*!40000 ALTER TABLE `cursus_items` DISABLE KEYS */;

INSERT INTO `cursus_items` (`_IDitem`, `_IDdata`, `_date`, `_ID`, `_IP`, `_IDgrprd`, `_titre`, `_texte`, `_file`, `_size`)
VALUES
	(1,1,'2013-09-06 13:59:26',9,-4,' 2 ','ref1','','receipt_2.pdf',1687);

/*!40000 ALTER TABLE `cursus_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv`;

CREATE TABLE `cv` (
  `_IDcv` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDuser` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_titre` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_dispo` varchar(80) NOT NULL,
  `_salaire` varchar(80) NOT NULL,
  `_IDposte` int(10) unsigned NOT NULL,
  `_IDlevel` int(10) unsigned NOT NULL,
  `_IDcontrat` int(10) unsigned NOT NULL,
  `_IDregion` int(10) unsigned NOT NULL,
  `_lieu` varchar(80) NOT NULL,
  `_divers` mediumtext NOT NULL,
  `_vus` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDcv`),
  UNIQUE KEY `_IDuser` (`_IDuser`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des CV';



# Affichage de la table cv_contrat
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_contrat`;

CREATE TABLE `cv_contrat` (
  `_IDcontrat` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDcontrat`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des catégories de contrats';

LOCK TABLES `cv_contrat` WRITE;
/*!40000 ALTER TABLE `cv_contrat` DISABLE KEYS */;

INSERT INTO `cv_contrat` (`_IDcontrat`, `_ident`, `_lang`)
VALUES
	(1,'Apprenticeship','en'),
	(2,'Full-time job','en'),
	(3,'Part-time job','en'),
	(4,'Freelance','en'),
	(5,'Practice teaching','en'),
	(6,'Aprendizaje','es'),
	(7,'Tiempo completo','es'),
	(8,'Trabajo por horas','es'),
	(9,'Freelance','es'),
	(10,'Práctica enseñando','es'),
	(11,'Apprentissage','fr'),
	(12,'CDD','fr'),
	(13,'CDI','fr'),
	(14,'Freelance','fr'),
	(15,'Stage','fr');

/*!40000 ALTER TABLE `cv_contrat` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_country
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_country`;

CREATE TABLE `cv_country` (
  `_IDcountry` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDcountry`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des pays';

LOCK TABLES `cv_country` WRITE;
/*!40000 ALTER TABLE `cv_country` DISABLE KEYS */;

INSERT INTO `cv_country` (`_IDcountry`, `_ident`, `_lang`)
VALUES
	(1,'Algeria','en'),
	(2,'Germany','en'),
	(3,'Argentina','en'),
	(4,'Australia','en'),
	(5,'Belgium','en'),
	(6,'Brazil','en'),
	(7,'Cameroun','en'),
	(8,'Canada','en'),
	(9,'China','en'),
	(10,'Ivory Coast','en'),
	(11,'Spain','en'),
	(12,'the United States','en'),
	(13,'Finland','en'),
	(14,'France','en'),
	(15,'Gabon','en'),
	(16,'Hungary','en'),
	(17,'Ireland','en'),
	(18,'Italy','en'),
	(19,'Japan','en'),
	(20,'Luxembourg','en'),
	(21,'Mali','en'),
	(22,'Morocco','en'),
	(23,'Norway','en'),
	(24,'the Netherlands','en'),
	(25,'Poland','en'),
	(26,'Romania','en'),
	(27,'United Kingdom','en'),
	(28,'Russia','en'),
	(29,'Senegal','en'),
	(30,'Sweden','en'),
	(31,'Switzerland','en'),
	(32,'Togo','en'),
	(33,'Tunisia','en'),
	(34,'Argelia','es'),
	(35,'Alemania','es'),
	(36,'Argentina','es'),
	(37,'Australia','es'),
	(38,'Bélgica','es'),
	(39,'Brasil','es'),
	(40,'Camerún','es'),
	(41,'Canadá','es'),
	(42,'China','es'),
	(43,'Costa de Marfil','es'),
	(44,'España','es'),
	(45,'los Estados Unidos','es'),
	(46,'Finlandia','es'),
	(47,'Francia','es'),
	(48,'Gabón','es'),
	(49,'Hungría','es'),
	(50,'Irlanda','es'),
	(51,'Italia','es'),
	(52,'Japón','es'),
	(53,'Luxemburgo','es'),
	(54,'Malí','es'),
	(55,'Marruecos','es'),
	(56,'Noruega','es'),
	(57,'los Países Bajos','es'),
	(58,'Polonia','es'),
	(59,'Rumania','es'),
	(60,'Reino Unido','es'),
	(61,'Rusia','es'),
	(62,'Senegal','es'),
	(63,'Suecia','es'),
	(64,'Suiza','es'),
	(65,'Togo','es'),
	(66,'Túnez','es'),
	(67,'Algérie','fr'),
	(68,'Allemagne','fr'),
	(69,'Argentine','fr'),
	(70,'Australie','fr'),
	(71,'Belgique','fr'),
	(72,'Bresil','fr'),
	(73,'Cameroun','fr'),
	(74,'Canada','fr'),
	(75,'Chine','fr'),
	(76,'Côte d\'Ivoire','fr'),
	(77,'Espagne','fr'),
	(78,'Etats Unis','fr'),
	(79,'Finlande','fr'),
	(80,'France','fr'),
	(81,'Gabon','fr'),
	(82,'Hongrie','fr'),
	(83,'Irlande','fr'),
	(84,'Italie','fr'),
	(85,'Japon','fr'),
	(86,'Luxembourg','fr'),
	(87,'Mali','fr'),
	(88,'Maroc','fr'),
	(89,'Norvege','fr'),
	(90,'Pays Bas','fr'),
	(91,'Pologne','fr'),
	(92,'Roumanie','fr'),
	(93,'Royaume uni','fr'),
	(94,'Russie','fr'),
	(95,'Sénégal','fr'),
	(96,'Suède','fr'),
	(97,'Suisse','fr'),
	(98,'Togo','fr'),
	(99,'Tunisie','fr');

/*!40000 ALTER TABLE `cv_country` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_degree
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_degree`;

CREATE TABLE `cv_degree` (
  `_IDdegree` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDdegree`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des catégories de diplômes';

LOCK TABLES `cv_degree` WRITE;
/*!40000 ALTER TABLE `cv_degree` DISABLE KEYS */;

INSERT INTO `cv_degree` (`_IDdegree`, `_ident`, `_lang`)
VALUES
	(1,'B.Sc.','en'),
	(2,'M.Sc.','en'),
	(3,'Ph.D.','en'),
	(4,'B.Sc.','es'),
	(5,'M.Sc.','es'),
	(6,'Ph.D.','es'),
	(7,'Agrégation','fr'),
	(8,'Baccalauréat','fr'),
	(9,'BEP','fr'),
	(10,'BTS','fr'),
	(11,'CAP','fr'),
	(12,'CAPES','fr'),
	(13,'DEA','fr'),
	(14,'DESS','fr'),
	(15,'DEST Cnam','fr'),
	(16,'DEUG','fr'),
	(17,'Diplôme d\'ingénieur','fr'),
	(18,'Diplôme école de commerce','fr'),
	(19,'Doctorat','fr'),
	(20,'DU','fr'),
	(21,'DUT','fr'),
	(22,'IUP','fr'),
	(23,'Licence','fr'),
	(24,'Magistère','fr'),
	(25,'Maitrise','fr'),
	(26,'MST','fr'),
	(27,'TOEIC','fr');

/*!40000 ALTER TABLE `cv_degree` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_exp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_exp`;

CREATE TABLE `cv_exp` (
  `_IDexp` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcv` int(10) unsigned NOT NULL,
  `_IDposte` int(10) unsigned NOT NULL,
  `_ident` tinytext NOT NULL,
  `_text` mediumtext NOT NULL,
  `_start` date NOT NULL,
  `_end` date NOT NULL,
  PRIMARY KEY (`_IDexp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des expériences professionnelles';



# Affichage de la table cv_form
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_form`;

CREATE TABLE `cv_form` (
  `_IDdegree` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcv` int(10) unsigned NOT NULL,
  `_year` int(10) unsigned NOT NULL,
  `_IDlevel` int(10) unsigned NOT NULL,
  `_text` mediumtext NOT NULL,
  PRIMARY KEY (`_IDdegree`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des diplômes';



# Affichage de la table cv_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_lang`;

CREATE TABLE `cv_lang` (
  `_IDlang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcv` int(10) unsigned NOT NULL,
  `_IDtype` int(10) unsigned NOT NULL,
  `_IDlevel` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_IDlang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des langues connues';

LOCK TABLES `cv_lang` WRITE;
/*!40000 ALTER TABLE `cv_lang` DISABLE KEYS */;

INSERT INTO `cv_lang` (`_IDlang`, `_IDcv`, `_IDtype`, `_IDlevel`)
VALUES
	(1,0,60,17);

/*!40000 ALTER TABLE `cv_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_langlevel
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_langlevel`;

CREATE TABLE `cv_langlevel` (
  `_IDlevel` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDlevel`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la maîtrise des langues';

LOCK TABLES `cv_langlevel` WRITE;
/*!40000 ALTER TABLE `cv_langlevel` DISABLE KEYS */;

INSERT INTO `cv_langlevel` (`_IDlevel`, `_ident`, `_lang`)
VALUES
	(1,'Basic','en'),
	(2,'Read','en'),
	(3,'Read, spoken','en'),
	(4,'Read, spoken, written','en'),
	(5,'Native','en'),
	(6,'Fluently','en'),
	(7,'Básico','es'),
	(8,'Leído','es'),
	(9,'Leído, hablado','es'),
	(10,'Leído, hablado, escrito','es'),
	(11,'Natural','es'),
	(12,'Fluido','es'),
	(13,'Notions','fr'),
	(14,'Lu','fr'),
	(15,'Lu, parlé','fr'),
	(16,'Lu, parlé, écrit','fr'),
	(17,'Langue maternelle','fr'),
	(18,'Parfaitement maîtrisé','fr');

/*!40000 ALTER TABLE `cv_langlevel` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_langtype
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_langtype`;

CREATE TABLE `cv_langtype` (
  `_IDtype` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDtype`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des langues';

LOCK TABLES `cv_langtype` WRITE;
/*!40000 ALTER TABLE `cv_langtype` DISABLE KEYS */;

INSERT INTO `cv_langtype` (`_IDtype`, `_ident`, `_lang`)
VALUES
	(1,'German','en'),
	(2,'English','en'),
	(3,'Arab','en'),
	(4,'Chinese','en'),
	(5,'Corse','en'),
	(6,'Danish','en'),
	(7,'Spanish','en'),
	(8,'Esperanto','en'),
	(9,'Finnish','en'),
	(10,'French','en'),
	(11,'Greek','en'),
	(12,'Hungarian','en'),
	(13,'Icelander','en'),
	(14,'Italian','en'),
	(15,'Japanese','en'),
	(16,'Laotian','en'),
	(17,'Netherlander','en'),
	(18,'Norwegian','en'),
	(19,'Pole','en'),
	(20,'Portuguese','en'),
	(21,'Roumanian','en'),
	(22,'Russian','en'),
	(23,'Swede','en'),
	(24,'Czechs','en'),
	(25,'Thailandais','en'),
	(26,'Turkish','en'),
	(27,'Vietnamese','en'),
	(28,'Yugoslavian','en'),
	(29,'Alemán','es'),
	(30,'Ingleses','es'),
	(31,'Arabe','es'),
	(32,'Chinos','es'),
	(33,'Corse','es'),
	(34,'Danés','es'),
	(35,'Españoles','es'),
	(36,'Esperanto','es'),
	(37,'Finlandés','es'),
	(38,'Franceses','es'),
	(39,'Griego','es'),
	(40,'Húngaro','es'),
	(41,'Icelander','es'),
	(42,'Italiano','es'),
	(43,'Japaneses','es'),
	(44,'Laosiano','es'),
	(45,'Netherlander','es'),
	(46,'Noruego','es'),
	(47,'Poste','es'),
	(48,'Portugueses','es'),
	(49,'Rumano','es'),
	(50,'Ruso','es'),
	(51,'Sueco','es'),
	(52,'Checos','es'),
	(53,'Thailandais','es'),
	(54,'Turco','es'),
	(55,'Vietnamitas','es'),
	(56,'Yugoslavo','es'),
	(57,'Allemand','fr'),
	(58,'Anglais','fr'),
	(59,'Arabe','fr'),
	(60,'Chinois','fr'),
	(61,'Corse','fr'),
	(62,'Danois','fr'),
	(63,'Espagnol','fr'),
	(64,'Esperanto','fr'),
	(65,'Finnois','fr'),
	(66,'Français','fr'),
	(67,'Grecque','fr'),
	(68,'Hongrois','fr'),
	(69,'Islandais','fr'),
	(70,'Italien','fr'),
	(71,'Japonais','fr'),
	(72,'Laotien','fr'),
	(73,'Néerlandais','fr'),
	(74,'Norvégien','fr'),
	(75,'Polonais','fr'),
	(76,'Portugais','fr'),
	(77,'Roumain','fr'),
	(78,'Russe','fr'),
	(79,'Suédois','fr'),
	(80,'Tcheques','fr'),
	(81,'Thailandais','fr'),
	(82,'Turque','fr'),
	(83,'Vietnamien','fr'),
	(84,'Yougoslave','fr');

/*!40000 ALTER TABLE `cv_langtype` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_level`;

CREATE TABLE `cv_level` (
  `_IDlevel` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDlevel`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des catégories de qualifications';

LOCK TABLES `cv_level` WRITE;
/*!40000 ALTER TABLE `cv_level` DISABLE KEYS */;

INSERT INTO `cv_level` (`_IDlevel`, `_ident`, `_lang`)
VALUES
	(1,'< 1 year','en'),
	(2,'1 to 2 years','en'),
	(3,'2 to 5 years','en'),
	(4,'5 to 10 years','en'),
	(5,'> 10 years','en'),
	(6,'< 1 año','es'),
	(7,'1 a 2 años','es'),
	(8,'2 a 5 años','es'),
	(9,'5 a 10 años','es'),
	(10,'> 10 años','es'),
	(11,'< 1 an','fr'),
	(12,'1 à 2 ans','fr'),
	(13,'2 à 5 ans','fr'),
	(14,'5 à 10 ans','fr'),
	(15,'> 10 ans','fr');

/*!40000 ALTER TABLE `cv_level` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_offre
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_offre`;

CREATE TABLE `cv_offre` (
  `_IDoffre` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDuser` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_IDsociete` int(10) unsigned NOT NULL,
  `_titre` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_dispo` varchar(80) NOT NULL,
  `_salaire` varchar(80) NOT NULL,
  `_IDposte` int(10) unsigned NOT NULL,
  `_IDlevel` int(10) unsigned NOT NULL,
  `_IDcontrat` int(10) unsigned NOT NULL,
  `_IDregion` int(10) unsigned NOT NULL,
  `_lieu` varchar(80) NOT NULL,
  `_IDdegree` int(10) unsigned NOT NULL,
  `_IDlangtype` int(10) unsigned NOT NULL,
  `_IDlanglvl` int(10) unsigned NOT NULL,
  `_vus` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDoffre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des offres d''emploi';

LOCK TABLES `cv_offre` WRITE;
/*!40000 ALTER TABLE `cv_offre` DISABLE KEYS */;

INSERT INTO `cv_offre` (`_IDoffre`, `_IDuser`, `_IP`, `_date`, `_IDsociete`, `_titre`, `_texte`, `_dispo`, `_salaire`, `_IDposte`, `_IDlevel`, `_IDcontrat`, `_IDregion`, `_lieu`, `_IDdegree`, `_IDlangtype`, `_IDlanglvl`, `_vus`, `_visible`, `_lang`)
VALUES
	(1,9,-4,'2013-09-10 09:28:33',2,'dsfdsf','dsfdfs','01/09/2013','300',10000,12,11,10102,'dsf',8,60,15,0,'N','fr');

/*!40000 ALTER TABLE `cv_offre` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_poste
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_poste`;

CREATE TABLE `cv_poste` (
  `_IDposte` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDposte`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des catégories de postes';

LOCK TABLES `cv_poste` WRITE;
/*!40000 ALTER TABLE `cv_poste` DISABLE KEYS */;

INSERT INTO `cv_poste` (`_IDposte`, `_ident`, `_lang`)
VALUES
	(10000,'Agriculture','en'),
	(10100,'- Manager','en'),
	(10200,'- Farm labourer','en'),
	(20000,'Engineering department','en'),
	(20100,'- project Leader','en'),
	(20101,'-- Leader junior project','en'),
	(20102,'-- Leader senior project','en'),
	(20400,'- Engineer study','en'),
	(20500,'- Engineer support','en'),
	(20800,'- Webmaster','en'),
	(30000,'Sales department','en'),
	(30200,'- Sales manager','en'),
	(60000,'Trainer','en'),
	(70000,'Journalist','en'),
	(80000,'Trainee','en'),
	(90000,'Translator','en'),
	(10000,'Agricultura','es'),
	(10100,'- Encargado','es'),
	(10200,'- Trabajador de granja','es'),
	(20000,'Departamento de ingeniería','es'),
	(20100,'- Líder de proyecto','es'),
	(20101,'-- Proyecto menor','es'),
	(20102,'-- Proyecto mayor','es'),
	(20400,'- Dirigir el estudio','es'),
	(20500,'- Dirigir la ayuda','es'),
	(20800,'- Webmaster','es'),
	(30000,'Departamento de las ventas','es'),
	(30200,'- Encargado de ventas','es'),
	(60000,'Amaestrador','es'),
	(70000,'Periodista','es'),
	(80000,'Aprendiz','es'),
	(90000,'Traductor','es'),
	(10000,'Agriculture','fr'),
	(10100,'- Chef d’exploitation','fr'),
	(10200,'- Ouvrier agricole','fr'),
	(20000,'Service Technique','fr'),
	(20100,'- Chef de projet','fr'),
	(20101,'-- Chef de projet junior','fr'),
	(20102,'-- Chef de projet senior','fr'),
	(20200,'- Consultant','fr'),
	(20300,'- Directeur technique','fr'),
	(20400,'- Ingénieur d\'étude','fr'),
	(20500,'- Ingénieur support','fr'),
	(20600,'- Responsable R & D','fr'),
	(20700,'- Technicien','fr'),
	(20701,'-- Technicien hotline','fr'),
	(20702,'-- Technicien maintenance','fr'),
	(20800,'- Webmaster','fr'),
	(30000,'Service Commercial','fr'),
	(30100,'- Commercial','fr'),
	(30200,'- Directeur commercial','fr'),
	(30300,'- Technico commercial','fr'),
	(40000,'Service Marketing','fr'),
	(40100,'- Responsable Marketing','fr'),
	(50000,'Service qualité','fr'),
	(50100,'- Assistant qualité','fr'),
	(50200,'- Responsable qualité','fr'),
	(60000,'Formateur','fr'),
	(70000,'Journaliste','fr'),
	(70100,'- Assistant(e) de direction','fr'),
	(80000,'Stagiaire','fr'),
	(90000,'Traducteur','fr');

/*!40000 ALTER TABLE `cv_poste` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_region
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_region`;

CREATE TABLE `cv_region` (
  `_IDregion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDregion`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des régions';

LOCK TABLES `cv_region` WRITE;
/*!40000 ALTER TABLE `cv_region` DISABLE KEYS */;

INSERT INTO `cv_region` (`_IDregion`, `_ident`, `_lang`)
VALUES
	(10000,'France','en'),
	(20000,'Foreign','en'),
	(10000,'Francia','es'),
	(20000,'Extranjero','es'),
	(10000,'France entière','fr'),
	(10100,'- France métropolitaine','fr'),
	(10101,'-- Alsace','fr'),
	(10102,'-- Auvergne','fr'),
	(10103,'-- Aquitaine','fr'),
	(10104,'-- Bourgogne','fr'),
	(10105,'-- Bretagne','fr'),
	(10106,'-- Centre','fr'),
	(10107,'-- Champagne-Ardenne','fr'),
	(10108,'-- Corse','fr'),
	(10109,'-- Franche-Comté','fr'),
	(10110,'-- Ile de France','fr'),
	(10111,'-- Languedoc-Roussillon','fr'),
	(10112,'-- Limousin','fr'),
	(10113,'-- Lorraine','fr'),
	(10114,'-- Midi-Pyrénées','fr'),
	(10115,'-- Nord-Pas-de-Calais','fr'),
	(10116,'-- Normandie','fr'),
	(10117,'-- Pays-de-Loire','fr'),
	(10118,'-- Picardie','fr'),
	(10119,'-- Poitou-Charentes','fr'),
	(10120,'-- PACA','fr'),
	(10121,'-- Rhône Alpes','fr'),
	(10200,'- DOM et TOM','fr'),
	(10201,'-- Guadeloupe','fr'),
	(10202,'-- Guyane','fr'),
	(10203,'-- Martinique','fr'),
	(10204,'-- Réunion','fr'),
	(10205,'-- St-Pierre et Miquelon','fr'),
	(20000,'Etranger','fr');

/*!40000 ALTER TABLE `cv_region` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_user`;

CREATE TABLE `cv_user` (
  `_IDsociete` int(10) unsigned NOT NULL,
  `_IDuser` int(10) unsigned NOT NULL,
  UNIQUE KEY `_IDsociete` (`_IDsociete`,`_IDuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des recruteurs';

LOCK TABLES `cv_user` WRITE;
/*!40000 ALTER TABLE `cv_user` DISABLE KEYS */;

INSERT INTO `cv_user` (`_IDsociete`, `_IDuser`)
VALUES
	(2,9);

/*!40000 ALTER TABLE `cv_user` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table cv_vu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cv_vu`;

CREATE TABLE `cv_vu` (
  `_IDitem` bigint(20) NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL,
  `_date` datetime NOT NULL,
  UNIQUE KEY `_key` (`_IDitem`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de log des CV ou des offres lus';



# Affichage de la table download
# ------------------------------------------------------------

DROP TABLE IF EXISTS `download`;

CREATE TABLE `download` (
  `_IDdown` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL,
  `_count` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  UNIQUE KEY `_key` (`_IDdown`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des ressources téléchargées';



# Affichage de la table download_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `download_data`;

CREATE TABLE `download_data` (
  `_IDdown` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_file` varchar(255) NOT NULL,
  `_count` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  PRIMARY KEY (`_IDdown`),
  UNIQUE KEY `_key` (`_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des ressources à télécharger';



# Affichage de la table download_tmp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `download_tmp`;

CREATE TABLE `download_tmp` (
  `_IDfile` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_file` varchar(255) NOT NULL,
  `_date` datetime NOT NULL,
  PRIMARY KEY (`_IDfile`),
  UNIQUE KEY `_key` (`_file`,`_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des fichiers temporaires';



# Affichage de la table edt
# ------------------------------------------------------------

DROP TABLE IF EXISTS `edt`;

CREATE TABLE `edt` (
  `_IDedt` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_titre` varchar(40) NOT NULL,
  `_IDweek` int(10) unsigned NOT NULL DEFAULT '0',
  `_horaire` tinytext NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDedt`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des listes des emplois du temps';

LOCK TABLES `edt` WRITE;
/*!40000 ALTER TABLE `edt` DISABLE KEYS */;

INSERT INTO `edt` (`_IDedt`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_titre`, `_IDweek`, `_horaire`, `_visible`, `_lang`)
VALUES
	(3,0,0,31,'_CLASS',127,'8:00;8:30;9:00;9:30;10:00;10:30;11:00;11:30;12:00;12:30;13:00;13:30;14:00;14:30;15:00;15:30;16:00;16:30;17:00;17:30;18:00;18:30;19:00;','O','fr'),
	(2,0,0,26,'_TEACHER',127,'8:00;8:30;9:00;9:30;10:00;10:30;11:00;11:30;12:00;12:30;13:00;13:30;14:00;14:30;15:00;15:30;16:00;16:30;17:00;17:30;18:00;18:30;19:00;','O','fr'),
	(1,0,0,26,'_PLACE',127,'8:00;8:30;9:00;9:30;10:00;10:30;11:00;11:30;12:00;12:30;13:00;13:30;14:00;14:30;15:00;15:30;16:00;16:30;17:00;17:30;18:00;18:30;19:00;','O','fr'),
	(4,0,0,31,'_STUDENT',127,'8:00;8:30;9:00;9:30;10:00;10:30;11:00;11:30;12:00;12:30;13:00;13:30;14:00;14:30;15:00;15:30;16:00;16:30;17:00;17:30;18:00;18:30;19:00;','O','fr');

/*!40000 ALTER TABLE `edt` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table edt_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `edt_data`;

CREATE TABLE `edt_data` (
  `_IDdata` int(10) unsigned NOT NULL,
  `_IDedt` int(10) unsigned NOT NULL,
  `_IDcentre` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL,
  `_IDclass` varchar(200) NOT NULL,
  `_IDitem` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_semaine` enum('0','1','2') NOT NULL DEFAULT '0',
  `_group` int(11) NOT NULL DEFAULT '0',
  `_jour` int(10) unsigned NOT NULL DEFAULT '0',
  `_debut` time NOT NULL,
  `_fin` time NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_nosemaine` int(10) NOT NULL DEFAULT '0',
  `_etat` int(10) DEFAULT '0' COMMENT '0 = normal, 1 = ajout, 2 = suppression',
  `_IDx` int(11) NOT NULL AUTO_INCREMENT,
  `_annee` int(11) NOT NULL,
  `_attribut` int(11) NOT NULL DEFAULT '0',
  `_IDrmpl` int(11) NOT NULL,
  `_MatRmpl` int(11) NOT NULL,
  `_plus` longtext NOT NULL,
  `_IDxParent` int(11) NOT NULL,
  `_text` longtext NOT NULL COMMENT 'Texte de l''évènement si la matière est agenda et que le type est ''Autre''',
  `_ID_examen` int(11) DEFAULT NULL COMMENT 'ID de l''examen, si vide alors pas un examen',
  `_ID_pma` int(11) DEFAULT NULL,
  PRIMARY KEY (`_IDx`),
  KEY `IDexamen` (`_ID_examen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des emplois du temps';

LOCK TABLES `edt_data` WRITE;
/*!40000 ALTER TABLE `edt_data` DISABLE KEYS */;

INSERT INTO `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, `_semaine`, `_group`, `_jour`, `_debut`, `_fin`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`, `_attribut`, `_IDrmpl`, `_MatRmpl`, `_plus`, `_IDxParent`, `_text`, `_ID_examen`, `_ID_pma`)
VALUES
	(0,3,1,2,';1;',0,5,'0',0,0,'09:15:00','11:00:00','O',44,1,1,2020,0,0,0,'0',0,'',0,197),
	(1,3,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',44,1,2,2020,0,0,0,'0',0,'',0,202),
	(2,3,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',44,1,3,2020,0,0,0,'0',0,'',0,213),
	(3,3,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',44,1,4,2020,0,0,0,'0',0,'',0,229),
	(4,3,1,4,';5;',6,4,'0',0,1,'11:15:00','13:00:00','O',44,1,5,2020,0,0,0,'0',0,'',0,210),
	(5,3,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',44,1,6,2020,0,0,0,'0',0,'',0,203),
	(6,3,1,1,';5;',6,7,'0',0,1,'15:15:00','17:00:00','O',44,1,7,2020,0,0,0,'0',0,'',0,216),
	(7,2,1,2,';1;',6,6,'0',0,3,'13:30:00','15:45:00','O',44,1,8,2020,0,0,0,'0',0,'',0,197),
	(8,2,1,1,';1;',6,4,'0',0,2,'10:45:00','13:30:00','O',44,1,9,2020,0,0,0,'0',0,'',0,212),
	(9,2,1,6,';2;',3,4,'0',0,3,'08:45:00','10:45:00','O',44,1,10,2020,0,0,0,'0',0,'',0,224),
	(10,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',44,1,11,2020,0,0,0,'0',0,'',0,212),
	(11,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',44,1,12,2020,0,0,0,'0',0,'',0,218),
	(12,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',44,1,13,2020,0,0,0,'0',0,'',0,231),
	(13,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',45,1,14,2020,0,0,0,'0',0,'',0,197),
	(14,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',46,1,15,2020,0,0,0,'',14,'',0,197),
	(15,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',47,1,16,2020,0,0,0,'',14,'',0,197),
	(16,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',48,1,17,2020,0,0,0,'',14,'',0,197),
	(17,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',49,1,18,2020,0,0,0,'',14,'',0,197),
	(18,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',50,1,19,2020,0,0,0,'',14,'',0,197),
	(19,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',51,1,20,2020,0,0,0,'',14,'',0,197),
	(20,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',52,1,21,2020,0,0,0,'',14,'',0,197),
	(21,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',53,1,22,2020,0,0,0,'',14,'',0,197),
	(22,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',1,1,23,2021,0,0,0,'',14,'',0,197),
	(23,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',2,1,24,2021,0,0,0,'',14,'',0,197),
	(24,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',3,1,25,2021,0,0,0,'',14,'',0,197),
	(25,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',4,1,26,2021,0,0,0,'',14,'',0,197),
	(26,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',5,1,27,2021,0,0,0,'',14,'',0,197),
	(27,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',6,1,28,2021,0,0,0,'',14,'',0,197),
	(28,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',7,1,29,2021,0,0,0,'',14,'',0,197),
	(29,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',8,1,30,2021,0,0,0,'',14,'',0,197),
	(30,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',9,1,31,2021,0,0,0,'',14,'',0,197),
	(31,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',10,1,32,2021,0,0,0,'',14,'',0,197),
	(32,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',11,1,33,2021,0,0,0,'',14,'',0,197),
	(33,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',12,1,34,2021,0,0,0,'',14,'',0,197),
	(34,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',13,1,35,2021,0,0,0,'',14,'',0,197),
	(35,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',14,1,36,2021,0,0,0,'',14,'',0,197),
	(36,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',15,1,37,2021,0,0,0,'',14,'',0,197),
	(37,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',16,1,38,2021,0,0,0,'',14,'',0,197),
	(38,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',17,1,39,2021,0,0,0,'',14,'',0,197),
	(39,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',18,1,40,2021,0,0,0,'',14,'',0,197),
	(40,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',19,1,41,2021,0,0,0,'',14,'',0,197),
	(41,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',20,1,42,2021,0,0,0,'',14,'',0,197),
	(42,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',21,1,43,2021,0,0,0,'',14,'',0,197),
	(43,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',22,1,44,2021,0,0,0,'',14,'',0,197),
	(44,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',23,1,45,2021,0,0,0,'',14,'',0,197),
	(45,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',24,1,46,2021,0,0,0,'',14,'',0,197),
	(46,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',25,1,47,2021,0,0,0,'',14,'',0,197),
	(47,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',26,1,48,2021,0,0,0,'',14,'',0,197),
	(48,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',27,1,49,2021,0,0,0,'',14,'',0,197),
	(49,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',28,1,50,2021,0,0,0,'',14,'',0,197),
	(50,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',29,1,51,2021,0,0,0,'',14,'',0,197),
	(51,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',30,1,52,2021,0,0,0,'',14,'',0,197),
	(52,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',31,1,53,2021,0,0,0,'',14,'',0,197),
	(53,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',32,1,54,2021,0,0,0,'',14,'',0,197),
	(54,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',33,1,55,2021,0,0,0,'',14,'',0,197),
	(55,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',34,1,56,2021,0,0,0,'',14,'',0,197),
	(56,2,1,2,';1;',1,5,'0',0,0,'09:15:00','11:00:00','O',35,1,57,2021,0,0,0,'',14,'',0,197),
	(57,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',45,1,58,2020,0,0,0,'0',0,'',0,202),
	(58,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',46,1,59,2020,0,0,0,'',58,'',0,202),
	(59,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',47,1,60,2020,0,0,0,'',58,'',0,202),
	(60,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',48,1,61,2020,0,0,0,'',58,'',0,202),
	(61,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',49,1,62,2020,0,0,0,'',58,'',0,202),
	(62,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',50,1,63,2020,0,0,0,'',58,'',0,202),
	(63,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',51,1,64,2020,0,0,0,'',58,'',0,202),
	(64,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',52,1,65,2020,0,0,0,'',58,'',0,202),
	(65,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',53,1,66,2020,0,0,0,'',58,'',0,202),
	(66,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',1,1,67,2021,0,0,0,'',58,'',0,202),
	(67,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',2,1,68,2021,0,0,0,'',58,'',0,202),
	(68,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',3,1,69,2021,0,0,0,'',58,'',0,202),
	(69,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',4,1,70,2021,0,0,0,'',58,'',0,202),
	(70,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',5,1,71,2021,0,0,0,'',58,'',0,202),
	(71,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',6,1,72,2021,0,0,0,'',58,'',0,202),
	(72,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',7,1,73,2021,0,0,0,'',58,'',0,202),
	(73,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',8,1,74,2021,0,0,0,'',58,'',0,202),
	(74,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',9,1,75,2021,0,0,0,'',58,'',0,202),
	(75,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',10,1,76,2021,0,0,0,'',58,'',0,202),
	(76,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',11,1,77,2021,0,0,0,'',58,'',0,202),
	(77,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',12,1,78,2021,0,0,0,'',58,'',0,202),
	(78,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',13,1,79,2021,0,0,0,'',58,'',0,202),
	(79,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',14,1,80,2021,0,0,0,'',58,'',0,202),
	(80,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',15,1,81,2021,0,0,0,'',58,'',0,202),
	(81,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',16,1,82,2021,0,0,0,'',58,'',0,202),
	(82,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',17,1,83,2021,0,0,0,'',58,'',0,202),
	(83,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',18,1,84,2021,0,0,0,'',58,'',0,202),
	(84,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',19,1,85,2021,0,0,0,'',58,'',0,202),
	(85,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',20,1,86,2021,0,0,0,'',58,'',0,202),
	(86,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',21,1,87,2021,0,0,0,'',58,'',0,202),
	(87,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',22,1,88,2021,0,0,0,'',58,'',0,202),
	(88,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',23,1,89,2021,0,0,0,'',58,'',0,202),
	(89,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',24,1,90,2021,0,0,0,'',58,'',0,202),
	(90,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',25,1,91,2021,0,0,0,'',58,'',0,202),
	(91,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',26,1,92,2021,0,0,0,'',58,'',0,202),
	(92,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',27,1,93,2021,0,0,0,'',58,'',0,202),
	(93,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',28,1,94,2021,0,0,0,'',58,'',0,202),
	(94,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',29,1,95,2021,0,0,0,'',58,'',0,202),
	(95,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',30,1,96,2021,0,0,0,'',58,'',0,202),
	(96,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',31,1,97,2021,0,0,0,'',58,'',0,202),
	(97,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',32,1,98,2021,0,0,0,'',58,'',0,202),
	(98,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',33,1,99,2021,0,0,0,'',58,'',0,202),
	(99,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',34,1,100,2021,0,0,0,'',58,'',0,202),
	(100,2,1,4,';1;',2,6,'0',0,0,'11:15:00','13:00:00','O',35,1,101,2021,0,0,0,'',58,'',0,202),
	(101,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',45,1,102,2020,0,0,0,'0',0,'',0,213),
	(102,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',46,1,103,2020,0,0,0,'',102,'',0,213),
	(103,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',47,1,104,2020,0,0,0,'',102,'',0,213),
	(104,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',48,1,105,2020,0,0,0,'',102,'',0,213),
	(105,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',49,1,106,2020,0,0,0,'',102,'',0,213),
	(106,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',50,1,107,2020,0,0,0,'',102,'',0,213),
	(107,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',51,1,108,2020,0,0,0,'',102,'',0,213),
	(108,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',52,1,109,2020,0,0,0,'',102,'',0,213),
	(109,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',53,1,110,2020,0,0,0,'',102,'',0,213),
	(110,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',1,1,111,2021,0,0,0,'',102,'',0,213),
	(111,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',2,1,112,2021,0,0,0,'',102,'',0,213),
	(112,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',3,1,113,2021,0,0,0,'',102,'',0,213),
	(113,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',4,1,114,2021,0,0,0,'',102,'',0,213),
	(114,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',5,1,115,2021,0,0,0,'',102,'',0,213),
	(115,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',6,1,116,2021,0,0,0,'',102,'',0,213),
	(116,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',7,1,117,2021,0,0,0,'',102,'',0,213),
	(117,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',8,1,118,2021,0,0,0,'',102,'',0,213),
	(118,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',9,1,119,2021,0,0,0,'',102,'',0,213),
	(119,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',10,1,120,2021,0,0,0,'',102,'',0,213),
	(120,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',11,1,121,2021,0,0,0,'',102,'',0,213),
	(121,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',12,1,122,2021,0,0,0,'',102,'',0,213),
	(122,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',13,1,123,2021,0,0,0,'',102,'',0,213),
	(123,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',14,1,124,2021,0,0,0,'',102,'',0,213),
	(124,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',15,1,125,2021,0,0,0,'',102,'',0,213),
	(125,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',16,1,126,2021,0,0,0,'',102,'',0,213),
	(126,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',17,1,127,2021,0,0,0,'',102,'',0,213),
	(127,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',18,1,128,2021,0,0,0,'',102,'',0,213),
	(128,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',19,1,129,2021,0,0,0,'',102,'',0,213),
	(129,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',20,1,130,2021,0,0,0,'',102,'',0,213),
	(130,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',21,1,131,2021,0,0,0,'',102,'',0,213),
	(131,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',22,1,132,2021,0,0,0,'',102,'',0,213),
	(132,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',23,1,133,2021,0,0,0,'',102,'',0,213),
	(133,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',24,1,134,2021,0,0,0,'',102,'',0,213),
	(134,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',25,1,135,2021,0,0,0,'',102,'',0,213),
	(135,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',26,1,136,2021,0,0,0,'',102,'',0,213),
	(136,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',27,1,137,2021,0,0,0,'',102,'',0,213),
	(137,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',28,1,138,2021,0,0,0,'',102,'',0,213),
	(138,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',29,1,139,2021,0,0,0,'',102,'',0,213),
	(139,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',30,1,140,2021,0,0,0,'',102,'',0,213),
	(140,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',31,1,141,2021,0,0,0,'',102,'',0,213),
	(141,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',32,1,142,2021,0,0,0,'',102,'',0,213),
	(142,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',33,1,143,2021,0,0,0,'',102,'',0,213),
	(143,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',34,1,144,2021,0,0,0,'',102,'',0,213),
	(144,2,1,1,';2;',3,7,'0',0,0,'14:30:00','18:15:00','O',35,1,145,2021,0,0,0,'',102,'',0,213),
	(145,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',45,1,146,2020,0,0,0,'0',0,'',0,229),
	(146,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',46,1,147,2020,0,0,0,'',146,'',0,229),
	(147,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',47,1,148,2020,0,0,0,'',146,'',0,229),
	(148,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',48,1,149,2020,0,0,0,'',146,'',0,229),
	(149,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',49,1,150,2020,0,0,0,'',146,'',0,229),
	(150,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',50,1,151,2020,0,0,0,'',146,'',0,229),
	(151,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',51,1,152,2020,0,0,0,'',146,'',0,229),
	(152,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',52,1,153,2020,0,0,0,'',146,'',0,229),
	(153,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',53,1,154,2020,0,0,0,'',146,'',0,229),
	(154,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',1,1,155,2021,0,0,0,'',146,'',0,229),
	(155,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',2,1,156,2021,0,0,0,'',146,'',0,229),
	(156,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',3,1,157,2021,0,0,0,'',146,'',0,229),
	(157,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',4,1,158,2021,0,0,0,'',146,'',0,229),
	(158,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',5,1,159,2021,0,0,0,'',146,'',0,229),
	(159,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',6,1,160,2021,0,0,0,'',146,'',0,229),
	(160,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',7,1,161,2021,0,0,0,'',146,'',0,229),
	(161,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',8,1,162,2021,0,0,0,'',146,'',0,229),
	(162,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',9,1,163,2021,0,0,0,'',146,'',0,229),
	(163,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',10,1,164,2021,0,0,0,'',146,'',0,229),
	(164,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',11,1,165,2021,0,0,0,'',146,'',0,229),
	(165,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',12,1,166,2021,0,0,0,'',146,'',0,229),
	(166,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',13,1,167,2021,0,0,0,'',146,'',0,229),
	(167,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',14,1,168,2021,0,0,0,'',146,'',0,229),
	(168,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',15,1,169,2021,0,0,0,'',146,'',0,229),
	(169,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',16,1,170,2021,0,0,0,'',146,'',0,229),
	(170,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',17,1,171,2021,0,0,0,'',146,'',0,229),
	(171,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',18,1,172,2021,0,0,0,'',146,'',0,229),
	(172,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',19,1,173,2021,0,0,0,'',146,'',0,229),
	(173,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',20,1,174,2021,0,0,0,'',146,'',0,229),
	(174,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',21,1,175,2021,0,0,0,'',146,'',0,229),
	(175,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',22,1,176,2021,0,0,0,'',146,'',0,229),
	(176,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',23,1,177,2021,0,0,0,'',146,'',0,229),
	(177,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',24,1,178,2021,0,0,0,'',146,'',0,229),
	(178,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',25,1,179,2021,0,0,0,'',146,'',0,229),
	(179,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',26,1,180,2021,0,0,0,'',146,'',0,229),
	(180,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',27,1,181,2021,0,0,0,'',146,'',0,229),
	(181,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',28,1,182,2021,0,0,0,'',146,'',0,229),
	(182,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',29,1,183,2021,0,0,0,'',146,'',0,229),
	(183,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',30,1,184,2021,0,0,0,'',146,'',0,229),
	(184,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',31,1,185,2021,0,0,0,'',146,'',0,229),
	(185,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',32,1,186,2021,0,0,0,'',146,'',0,229),
	(186,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',33,1,187,2021,0,0,0,'',146,'',0,229),
	(187,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',34,1,188,2021,0,0,0,'',146,'',0,229),
	(188,2,1,7,';4;',4,5,'0',0,1,'08:30:00','12:15:00','O',35,1,189,2021,0,0,0,'',146,'',0,229),
	(189,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',45,1,190,2020,0,0,0,'0',0,'',0,204),
	(190,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',46,1,191,2020,0,0,0,'',190,'',0,204),
	(191,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',47,1,192,2020,0,0,0,'',190,'',0,204),
	(192,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',48,1,193,2020,0,0,0,'',190,'',0,204),
	(193,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',49,1,194,2020,0,0,0,'',190,'',0,204),
	(194,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',50,1,195,2020,0,0,0,'',190,'',0,204),
	(195,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',51,1,196,2020,0,0,0,'',190,'',0,204),
	(196,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',52,1,197,2020,0,0,0,'',190,'',0,204),
	(197,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',53,1,198,2020,0,0,0,'',190,'',0,204),
	(198,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',1,1,199,2021,0,0,0,'',190,'',0,204),
	(199,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',2,1,200,2021,0,0,0,'',190,'',0,204),
	(200,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',3,1,201,2021,0,0,0,'',190,'',0,204),
	(201,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',4,1,202,2021,0,0,0,'',190,'',0,204),
	(202,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',5,1,203,2021,0,0,0,'',190,'',0,204),
	(203,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',6,1,204,2021,0,0,0,'',190,'',0,204),
	(204,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',7,1,205,2021,0,0,0,'',190,'',0,204),
	(205,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',8,1,206,2021,0,0,0,'',190,'',0,204),
	(206,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',9,1,207,2021,0,0,0,'',190,'',0,204),
	(207,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',10,1,208,2021,0,0,0,'',190,'',0,204),
	(208,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',11,1,209,2021,0,0,0,'',190,'',0,204),
	(209,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',12,1,210,2021,0,0,0,'',190,'',0,204),
	(210,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',13,1,211,2021,0,0,0,'',190,'',0,204),
	(211,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',14,1,212,2021,0,0,0,'',190,'',0,204),
	(212,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',15,1,213,2021,0,0,0,'',190,'',0,204),
	(213,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',16,1,214,2021,0,0,0,'',190,'',0,204),
	(214,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',17,1,215,2021,0,0,0,'',190,'',0,204),
	(215,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',18,1,216,2021,0,0,0,'',190,'',0,204),
	(216,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',19,1,217,2021,0,0,0,'',190,'',0,204),
	(217,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',20,1,218,2021,0,0,0,'',190,'',0,204),
	(218,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',21,1,219,2021,0,0,0,'',190,'',0,204),
	(219,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',22,1,220,2021,0,0,0,'',190,'',0,204),
	(220,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',23,1,221,2021,0,0,0,'',190,'',0,204),
	(221,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',24,1,222,2021,0,0,0,'',190,'',0,204),
	(222,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',25,1,223,2021,0,0,0,'',190,'',0,204),
	(223,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',26,1,224,2021,0,0,0,'',190,'',0,204),
	(224,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',27,1,225,2021,0,0,0,'',190,'',0,204),
	(225,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',28,1,226,2021,0,0,0,'',190,'',0,204),
	(226,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',29,1,227,2021,0,0,0,'',190,'',0,204),
	(227,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',30,1,228,2021,0,0,0,'',190,'',0,204),
	(228,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',31,1,229,2021,0,0,0,'',190,'',0,204),
	(229,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',32,1,230,2021,0,0,0,'',190,'',0,204),
	(230,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',33,1,231,2021,0,0,0,'',190,'',0,204),
	(231,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',34,1,232,2021,0,0,0,'',190,'',0,204),
	(232,2,1,4,';2;',6,4,'0',0,1,'11:15:00','13:00:00','O',35,1,233,2021,0,0,0,'',190,'',0,204),
	(233,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',45,1,234,2020,0,0,0,'0',0,'',0,216),
	(235,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',46,1,236,2020,0,0,0,'',234,'',0,216),
	(236,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',47,1,237,2020,0,0,0,'',234,'',0,216),
	(237,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',48,1,238,2020,0,0,0,'',234,'',0,216),
	(238,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',49,1,239,2020,0,0,0,'',234,'',0,216),
	(239,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',50,1,240,2020,0,0,0,'',234,'',0,216),
	(240,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',51,1,241,2020,0,0,0,'',234,'',0,216),
	(241,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',52,1,242,2020,0,0,0,'',234,'',0,216),
	(242,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',53,1,243,2020,0,0,0,'',234,'',0,216),
	(243,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',1,1,244,2021,0,0,0,'',234,'',0,216),
	(244,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',2,1,245,2021,0,0,0,'',234,'',0,216),
	(245,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',3,1,246,2021,0,0,0,'',234,'',0,216),
	(246,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',4,1,247,2021,0,0,0,'',234,'',0,216),
	(247,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',5,1,248,2021,0,0,0,'',234,'',0,216),
	(248,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',6,1,249,2021,0,0,0,'',234,'',0,216),
	(249,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',7,1,250,2021,0,0,0,'',234,'',0,216),
	(250,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',8,1,251,2021,0,0,0,'',234,'',0,216),
	(251,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',9,1,252,2021,0,0,0,'',234,'',0,216),
	(252,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',10,1,253,2021,0,0,0,'',234,'',0,216),
	(253,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',11,1,254,2021,0,0,0,'',234,'',0,216),
	(254,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',12,1,255,2021,0,0,0,'',234,'',0,216),
	(255,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',13,1,256,2021,0,0,0,'',234,'',0,216),
	(256,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',14,1,257,2021,0,0,0,'',234,'',0,216),
	(257,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',15,1,258,2021,0,0,0,'',234,'',0,216),
	(258,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',16,1,259,2021,0,0,0,'',234,'',0,216),
	(259,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',17,1,260,2021,0,0,0,'',234,'',0,216),
	(260,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',18,1,261,2021,0,0,0,'',234,'',0,216),
	(261,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',19,1,262,2021,0,0,0,'',234,'',0,216),
	(262,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',20,1,263,2021,0,0,0,'',234,'',0,216),
	(263,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',21,1,264,2021,0,0,0,'',234,'',0,216),
	(264,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',22,1,265,2021,0,0,0,'',234,'',0,216),
	(265,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',23,1,266,2021,0,0,0,'',234,'',0,216),
	(266,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',24,1,267,2021,0,0,0,'',234,'',0,216),
	(267,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',25,1,268,2021,0,0,0,'',234,'',0,216),
	(268,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',26,1,269,2021,0,0,0,'',234,'',0,216),
	(269,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',27,1,270,2021,0,0,0,'',234,'',0,216),
	(270,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',28,1,271,2021,0,0,0,'',234,'',0,216),
	(271,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',29,1,272,2021,0,0,0,'',234,'',0,216),
	(272,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',30,1,273,2021,0,0,0,'',234,'',0,216),
	(273,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',31,1,274,2021,0,0,0,'',234,'',0,216),
	(274,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',32,1,275,2021,0,0,0,'',234,'',0,216),
	(275,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',33,1,276,2021,0,0,0,'',234,'',0,216),
	(276,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',34,1,277,2021,0,0,0,'',234,'',0,216),
	(277,2,1,1,';5;',4,7,'0',0,1,'15:15:00','17:00:00','O',35,1,278,2021,0,0,0,'',234,'',0,216),
	(278,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',45,1,279,2020,0,0,0,'0',0,'',0,212),
	(279,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',46,1,280,2020,0,0,0,'',279,'',0,212),
	(280,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',47,1,281,2020,0,0,0,'',279,'',0,212),
	(281,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',48,1,282,2020,0,0,0,'',279,'',0,212),
	(282,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',49,1,283,2020,0,0,0,'',279,'',0,212),
	(283,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',50,1,284,2020,0,0,0,'',279,'',0,212),
	(284,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',51,1,285,2020,0,0,0,'',279,'',0,212),
	(285,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',52,1,286,2020,0,0,0,'',279,'',0,212),
	(286,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',53,1,287,2020,0,0,0,'',279,'',0,212),
	(287,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',1,1,288,2021,0,0,0,'',279,'',0,212),
	(288,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',2,1,289,2021,0,0,0,'',279,'',0,212),
	(289,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',3,1,290,2021,0,0,0,'',279,'',0,212),
	(290,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',4,1,291,2021,0,0,0,'',279,'',0,212),
	(291,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',5,1,292,2021,0,0,0,'',279,'',0,212),
	(292,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',6,1,293,2021,0,0,0,'',279,'',0,212),
	(293,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',7,1,294,2021,0,0,0,'',279,'',0,212),
	(294,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',8,1,295,2021,0,0,0,'',279,'',0,212),
	(295,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',9,1,296,2021,0,0,0,'',279,'',0,212),
	(296,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',10,1,297,2021,0,0,0,'',279,'',0,212),
	(297,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',11,1,298,2021,0,0,0,'',279,'',0,212),
	(298,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',12,1,299,2021,0,0,0,'',279,'',0,212),
	(299,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',13,1,300,2021,0,0,0,'',279,'',0,212),
	(300,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',14,1,301,2021,0,0,0,'',279,'',0,212),
	(301,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',15,1,302,2021,0,0,0,'',279,'',0,212),
	(302,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',16,1,303,2021,0,0,0,'',279,'',0,212),
	(303,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',17,1,304,2021,0,0,0,'',279,'',0,212),
	(304,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',18,1,305,2021,0,0,0,'',279,'',0,212),
	(305,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',19,1,306,2021,0,0,0,'',279,'',0,212),
	(306,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',20,1,307,2021,0,0,0,'',279,'',0,212),
	(307,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',21,1,308,2021,0,0,0,'',279,'',0,212),
	(308,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',22,1,309,2021,0,0,0,'',279,'',0,212),
	(309,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',23,1,310,2021,0,0,0,'',279,'',0,212),
	(310,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',24,1,311,2021,0,0,0,'',279,'',0,212),
	(311,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',25,1,312,2021,0,0,0,'',279,'',0,212),
	(312,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',26,1,313,2021,0,0,0,'',279,'',0,212),
	(313,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',27,1,314,2021,0,0,0,'',279,'',0,212),
	(314,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',28,1,315,2021,0,0,0,'',279,'',0,212),
	(315,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',29,1,316,2021,0,0,0,'',279,'',0,212),
	(316,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',30,1,317,2021,0,0,0,'',279,'',0,212),
	(317,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',31,1,318,2021,0,0,0,'',279,'',0,212),
	(318,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',32,1,319,2021,0,0,0,'',279,'',0,212),
	(319,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',33,1,320,2021,0,0,0,'',279,'',0,212),
	(320,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',34,1,321,2021,0,0,0,'',279,'',0,212),
	(321,2,1,1,';1;',2,4,'0',0,2,'10:45:00','13:30:00','O',35,1,322,2021,0,0,0,'',279,'',0,212),
	(322,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',45,1,323,2020,0,0,0,'0',0,'',0,203),
	(323,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',46,1,324,2020,0,0,0,'',323,'',0,203),
	(324,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',47,1,325,2020,0,0,0,'',323,'',0,203),
	(325,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',48,1,326,2020,0,0,0,'',323,'',0,203),
	(326,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',49,1,327,2020,0,0,0,'',323,'',0,203),
	(327,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',50,1,328,2020,0,0,0,'',323,'',0,203),
	(328,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',51,1,329,2020,0,0,0,'',323,'',0,203),
	(329,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',52,1,330,2020,0,0,0,'',323,'',0,203),
	(330,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',53,1,331,2020,0,0,0,'',323,'',0,203),
	(331,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',1,1,332,2021,0,0,0,'',323,'',0,203),
	(332,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',2,1,333,2021,0,0,0,'',323,'',0,203),
	(333,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',3,1,334,2021,0,0,0,'',323,'',0,203),
	(334,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',4,1,335,2021,0,0,0,'',323,'',0,203),
	(335,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',5,1,336,2021,0,0,0,'',323,'',0,203),
	(336,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',6,1,337,2021,0,0,0,'',323,'',0,203),
	(337,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',7,1,338,2021,0,0,0,'',323,'',0,203),
	(338,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',8,1,339,2021,0,0,0,'',323,'',0,203),
	(339,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',9,1,340,2021,0,0,0,'',323,'',0,203),
	(340,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',10,1,341,2021,0,0,0,'',323,'',0,203),
	(341,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',11,1,342,2021,0,0,0,'',323,'',0,203),
	(342,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',12,1,343,2021,0,0,0,'',323,'',0,203),
	(343,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',13,1,344,2021,0,0,0,'',323,'',0,203),
	(344,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',14,1,345,2021,0,0,0,'',323,'',0,203),
	(345,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',15,1,346,2021,0,0,0,'',323,'',0,203),
	(346,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',16,1,347,2021,0,0,0,'',323,'',0,203),
	(347,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',17,1,348,2021,0,0,0,'',323,'',0,203),
	(348,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',18,1,349,2021,0,0,0,'',323,'',0,203),
	(349,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',19,1,350,2021,0,0,0,'',323,'',0,203),
	(350,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',20,1,351,2021,0,0,0,'',323,'',0,203),
	(351,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',21,1,352,2021,0,0,0,'',323,'',0,203),
	(352,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',22,1,353,2021,0,0,0,'',323,'',0,203),
	(353,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',23,1,354,2021,0,0,0,'',323,'',0,203),
	(354,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',24,1,355,2021,0,0,0,'',323,'',0,203),
	(355,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',25,1,356,2021,0,0,0,'',323,'',0,203),
	(356,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',26,1,357,2021,0,0,0,'',323,'',0,203),
	(357,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',27,1,358,2021,0,0,0,'',323,'',0,203),
	(358,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',28,1,359,2021,0,0,0,'',323,'',0,203),
	(359,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',29,1,360,2021,0,0,0,'',323,'',0,203),
	(360,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',30,1,361,2021,0,0,0,'',323,'',0,203),
	(361,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',31,1,362,2021,0,0,0,'',323,'',0,203),
	(362,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',32,1,363,2021,0,0,0,'',323,'',0,203),
	(363,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',33,1,364,2021,0,0,0,'',323,'',0,203),
	(364,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',34,1,365,2021,0,0,0,'',323,'',0,203),
	(365,2,1,6,';1;',4,5,'0',0,2,'14:15:00','18:00:00','O',35,1,366,2021,0,0,0,'',323,'',0,203),
	(366,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',45,1,367,2020,0,0,0,'0',0,'',0,205),
	(367,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',46,1,368,2020,0,0,0,'',367,'',0,205),
	(368,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',47,1,369,2020,0,0,0,'',367,'',0,205),
	(369,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',48,1,370,2020,0,0,0,'',367,'',0,205),
	(370,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',49,1,371,2020,0,0,0,'',367,'',0,205),
	(371,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',50,1,372,2020,0,0,0,'',367,'',0,205),
	(372,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',51,1,373,2020,0,0,0,'',367,'',0,205),
	(373,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',52,1,374,2020,0,0,0,'',367,'',0,205),
	(374,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',53,1,375,2020,0,0,0,'',367,'',0,205),
	(375,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',1,1,376,2021,0,0,0,'',367,'',0,205),
	(376,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',2,1,377,2021,0,0,0,'',367,'',0,205),
	(377,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',3,1,378,2021,0,0,0,'',367,'',0,205),
	(378,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',4,1,379,2021,0,0,0,'',367,'',0,205),
	(379,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',5,1,380,2021,0,0,0,'',367,'',0,205),
	(380,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',6,1,381,2021,0,0,0,'',367,'',0,205),
	(381,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',7,1,382,2021,0,0,0,'',367,'',0,205),
	(382,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',8,1,383,2021,0,0,0,'',367,'',0,205),
	(383,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',9,1,384,2021,0,0,0,'',367,'',0,205),
	(384,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',10,1,385,2021,0,0,0,'',367,'',0,205),
	(385,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',11,1,386,2021,0,0,0,'',367,'',0,205),
	(386,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',12,1,387,2021,0,0,0,'',367,'',0,205),
	(387,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',13,1,388,2021,0,0,0,'',367,'',0,205),
	(388,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',14,1,389,2021,0,0,0,'',367,'',0,205),
	(389,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',15,1,390,2021,0,0,0,'',367,'',0,205),
	(390,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',16,1,391,2021,0,0,0,'',367,'',0,205),
	(391,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',17,1,392,2021,0,0,0,'',367,'',0,205),
	(392,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',18,1,393,2021,0,0,0,'',367,'',0,205),
	(393,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',19,1,394,2021,0,0,0,'',367,'',0,205),
	(394,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',20,1,395,2021,0,0,0,'',367,'',0,205),
	(395,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',21,1,396,2021,0,0,0,'',367,'',0,205),
	(396,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',22,1,397,2021,0,0,0,'',367,'',0,205),
	(397,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',23,1,398,2021,0,0,0,'',367,'',0,205),
	(398,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',24,1,399,2021,0,0,0,'',367,'',0,205),
	(399,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',25,1,400,2021,0,0,0,'',367,'',0,205),
	(400,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',26,1,401,2021,0,0,0,'',367,'',0,205),
	(401,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',27,1,402,2021,0,0,0,'',367,'',0,205),
	(402,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',28,1,403,2021,0,0,0,'',367,'',0,205),
	(403,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',29,1,404,2021,0,0,0,'',367,'',0,205),
	(404,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',30,1,405,2021,0,0,0,'',367,'',0,205),
	(405,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',31,1,406,2021,0,0,0,'',367,'',0,205),
	(406,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',32,1,407,2021,0,0,0,'',367,'',0,205),
	(407,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',33,1,408,2021,0,0,0,'',367,'',0,205),
	(408,2,1,6,';2;',6,4,'0',0,3,'08:45:00','10:45:00','O',34,1,409,2021,0,0,0,'',367,'',0,205),
	(409,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',45,1,410,2020,0,0,0,'0',0,'',0,197),
	(410,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',46,1,411,2020,0,0,0,'',410,'',0,197),
	(411,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',47,1,412,2020,0,0,0,'',410,'',0,197),
	(412,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',48,1,413,2020,0,0,0,'',410,'',0,197),
	(413,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',49,1,414,2020,0,0,0,'',410,'',0,197),
	(414,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',50,1,415,2020,0,0,0,'',410,'',0,197),
	(415,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',51,1,416,2020,0,0,0,'',410,'',0,197),
	(416,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',52,1,417,2020,0,0,0,'',410,'',0,197),
	(417,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',53,1,418,2020,0,0,0,'',410,'',0,197),
	(418,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',1,1,419,2021,0,0,0,'',410,'',0,197),
	(419,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',2,1,420,2021,0,0,0,'',410,'',0,197),
	(420,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',3,1,421,2021,0,0,0,'',410,'',0,197),
	(421,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',4,1,422,2021,0,0,0,'',410,'',0,197),
	(422,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',5,1,423,2021,0,0,0,'',410,'',0,197),
	(423,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',6,1,424,2021,0,0,0,'',410,'',0,197),
	(424,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',7,1,425,2021,0,0,0,'',410,'',0,197),
	(425,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',8,1,426,2021,0,0,0,'',410,'',0,197),
	(426,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',9,1,427,2021,0,0,0,'',410,'',0,197),
	(427,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',10,1,428,2021,0,0,0,'',410,'',0,197),
	(428,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',11,1,429,2021,0,0,0,'',410,'',0,197),
	(429,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',12,1,430,2021,0,0,0,'',410,'',0,197),
	(430,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',13,1,431,2021,0,0,0,'',410,'',0,197),
	(431,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',14,1,432,2021,0,0,0,'',410,'',0,197),
	(432,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',15,1,433,2021,0,0,0,'',410,'',0,197),
	(433,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',16,1,434,2021,0,0,0,'',410,'',0,197),
	(434,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',17,1,435,2021,0,0,0,'',410,'',0,197),
	(435,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',18,1,436,2021,0,0,0,'',410,'',0,197),
	(436,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',19,1,437,2021,0,0,0,'',410,'',0,197),
	(437,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',20,1,438,2021,0,0,0,'',410,'',0,197),
	(438,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',21,1,439,2021,0,0,0,'',410,'',0,197),
	(439,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',22,1,440,2021,0,0,0,'',410,'',0,197),
	(440,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',23,1,441,2021,0,0,0,'',410,'',0,197),
	(441,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',24,1,442,2021,0,0,0,'',410,'',0,197),
	(442,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',25,1,443,2021,0,0,0,'',410,'',0,197),
	(443,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',26,1,444,2021,0,0,0,'',410,'',0,197),
	(444,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',27,1,445,2021,0,0,0,'',410,'',0,197),
	(445,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',28,1,446,2021,0,0,0,'',410,'',0,197),
	(446,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',29,1,447,2021,0,0,0,'',410,'',0,197),
	(447,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',30,1,448,2021,0,0,0,'',410,'',0,197),
	(448,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',31,1,449,2021,0,0,0,'',410,'',0,197),
	(449,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',32,1,450,2021,0,0,0,'',410,'',0,197),
	(450,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',33,1,451,2021,0,0,0,'',410,'',0,197),
	(451,2,1,2,';1;',1,6,'0',0,3,'13:30:00','15:45:00','O',34,1,452,2021,0,0,0,'',410,'',0,197),
	(452,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',45,1,453,2020,0,0,0,'0',0,'',0,218),
	(453,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',46,1,454,2020,0,0,0,'',453,'',0,218),
	(454,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',47,1,455,2020,0,0,0,'',453,'',0,218),
	(455,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',48,1,456,2020,0,0,0,'',453,'',0,218),
	(456,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',49,1,457,2020,0,0,0,'',453,'',0,218),
	(457,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',50,1,458,2020,0,0,0,'',453,'',0,218),
	(458,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',51,1,459,2020,0,0,0,'',453,'',0,218),
	(459,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',52,1,460,2020,0,0,0,'',453,'',0,218),
	(460,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',53,1,461,2021,0,0,0,'',453,'',0,218),
	(461,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',1,1,462,2021,0,0,0,'',453,'',0,218),
	(462,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',2,1,463,2021,0,0,0,'',453,'',0,218),
	(463,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',3,1,464,2021,0,0,0,'',453,'',0,218),
	(464,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',4,1,465,2021,0,0,0,'',453,'',0,218),
	(465,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',5,1,466,2021,0,0,0,'',453,'',0,218),
	(466,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',6,1,467,2021,0,0,0,'',453,'',0,218),
	(467,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',7,1,468,2021,0,0,0,'',453,'',0,218),
	(468,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',8,1,469,2021,0,0,0,'',453,'',0,218),
	(469,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',9,1,470,2021,0,0,0,'',453,'',0,218),
	(470,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',10,1,471,2021,0,0,0,'',453,'',0,218),
	(471,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',11,1,472,2021,0,0,0,'',453,'',0,218),
	(472,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',12,1,473,2021,0,0,0,'',453,'',0,218),
	(473,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',13,1,474,2021,0,0,0,'',453,'',0,218),
	(474,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',14,1,475,2021,0,0,0,'',453,'',0,218),
	(475,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',15,1,476,2021,0,0,0,'',453,'',0,218),
	(476,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',16,1,477,2021,0,0,0,'',453,'',0,218),
	(477,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',17,1,478,2021,0,0,0,'',453,'',0,218),
	(478,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',18,1,479,2021,0,0,0,'',453,'',0,218),
	(479,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',19,1,480,2021,0,0,0,'',453,'',0,218),
	(480,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',20,1,481,2021,0,0,0,'',453,'',0,218),
	(481,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',21,1,482,2021,0,0,0,'',453,'',0,218),
	(482,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',22,1,483,2021,0,0,0,'',453,'',0,218),
	(483,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',23,1,484,2021,0,0,0,'',453,'',0,218),
	(484,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',24,1,485,2021,0,0,0,'',453,'',0,218),
	(485,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',25,1,486,2021,0,0,0,'',453,'',0,218),
	(486,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',26,1,487,2021,0,0,0,'',453,'',0,218),
	(487,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',27,1,488,2021,0,0,0,'',453,'',0,218),
	(488,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',28,1,489,2021,0,0,0,'',453,'',0,218),
	(489,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',29,1,490,2021,0,0,0,'',453,'',0,218),
	(490,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',30,1,491,2021,0,0,0,'',453,'',0,218),
	(491,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',31,1,492,2021,0,0,0,'',453,'',0,218),
	(492,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',32,1,493,2021,0,0,0,'',453,'',0,218),
	(493,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',33,1,494,2021,0,0,0,'',453,'',0,218),
	(494,2,1,5,';2;',3,4,'0',0,4,'09:30:00','12:45:00','O',34,1,495,2021,0,0,0,'',453,'',0,218),
	(495,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',45,1,496,2020,0,0,0,'0',0,'',0,212),
	(496,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',46,1,497,2020,0,0,0,'',496,'',0,212),
	(497,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',47,1,498,2020,0,0,0,'',496,'',0,212),
	(498,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',48,1,499,2020,0,0,0,'',496,'',0,212),
	(499,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',49,1,500,2020,0,0,0,'',496,'',0,212),
	(500,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',50,1,501,2020,0,0,0,'',496,'',0,212),
	(501,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',51,1,502,2020,0,0,0,'',496,'',0,212),
	(502,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',52,1,503,2020,0,0,0,'',496,'',0,212),
	(503,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',53,1,504,2021,0,0,0,'',496,'',0,212),
	(504,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',1,1,505,2021,0,0,0,'',496,'',0,212),
	(505,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',2,1,506,2021,0,0,0,'',496,'',0,212),
	(506,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',3,1,507,2021,0,0,0,'',496,'',0,212),
	(507,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',4,1,508,2021,0,0,0,'',496,'',0,212),
	(508,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',5,1,509,2021,0,0,0,'',496,'',0,212),
	(509,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',6,1,510,2021,0,0,0,'',496,'',0,212),
	(510,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',7,1,511,2021,0,0,0,'',496,'',0,212),
	(511,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',8,1,512,2021,0,0,0,'',496,'',0,212),
	(512,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',9,1,513,2021,0,0,0,'',496,'',0,212),
	(513,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',10,1,514,2021,0,0,0,'',496,'',0,212),
	(514,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',11,1,515,2021,0,0,0,'',496,'',0,212),
	(515,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',12,1,516,2021,0,0,0,'',496,'',0,212),
	(516,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',13,1,517,2021,0,0,0,'',496,'',0,212),
	(517,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',14,1,518,2021,0,0,0,'',496,'',0,212),
	(518,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',15,1,519,2021,0,0,0,'',496,'',0,212),
	(519,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',16,1,520,2021,0,0,0,'',496,'',0,212),
	(520,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',17,1,521,2021,0,0,0,'',496,'',0,212),
	(521,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',18,1,522,2021,0,0,0,'',496,'',0,212),
	(522,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',19,1,523,2021,0,0,0,'',496,'',0,212),
	(523,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',20,1,524,2021,0,0,0,'',496,'',0,212),
	(524,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',21,1,525,2021,0,0,0,'',496,'',0,212),
	(525,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',22,1,526,2021,0,0,0,'',496,'',0,212),
	(526,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',23,1,527,2021,0,0,0,'',496,'',0,212),
	(527,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',24,1,528,2021,0,0,0,'',496,'',0,212),
	(528,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',25,1,529,2021,0,0,0,'',496,'',0,212),
	(529,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',26,1,530,2021,0,0,0,'',496,'',0,212),
	(530,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',27,1,531,2021,0,0,0,'',496,'',0,212),
	(531,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',28,1,532,2021,0,0,0,'',496,'',0,212),
	(532,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',29,1,533,2021,0,0,0,'',496,'',0,212),
	(533,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',30,1,534,2021,0,0,0,'',496,'',0,212),
	(534,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',31,1,535,2021,0,0,0,'',496,'',0,212),
	(535,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',32,1,536,2021,0,0,0,'',496,'',0,212),
	(536,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',33,1,537,2021,0,0,0,'',496,'',0,212),
	(537,2,1,1,';1;',6,6,'0',0,4,'10:15:00','15:00:00','O',34,1,538,2021,0,0,0,'',496,'',0,212),
	(538,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',45,1,539,2020,0,0,0,'0',0,'',0,231),
	(539,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',46,1,540,2020,0,0,0,'',539,'',0,231),
	(540,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',47,1,541,2020,0,0,0,'',539,'',0,231),
	(541,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',48,1,542,2020,0,0,0,'',539,'',0,231),
	(542,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',49,1,543,2020,0,0,0,'',539,'',0,231),
	(543,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',50,1,544,2020,0,0,0,'',539,'',0,231),
	(544,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',51,1,545,2020,0,0,0,'',539,'',0,231),
	(545,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',52,1,546,2020,0,0,0,'',539,'',0,231),
	(546,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',53,1,547,2021,0,0,0,'',539,'',0,231),
	(547,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',1,1,548,2021,0,0,0,'',539,'',0,231),
	(548,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',2,1,549,2021,0,0,0,'',539,'',0,231),
	(549,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',3,1,550,2021,0,0,0,'',539,'',0,231),
	(550,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',4,1,551,2021,0,0,0,'',539,'',0,231),
	(551,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',5,1,552,2021,0,0,0,'',539,'',0,231),
	(552,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',6,1,553,2021,0,0,0,'',539,'',0,231),
	(553,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',7,1,554,2021,0,0,0,'',539,'',0,231),
	(554,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',8,1,555,2021,0,0,0,'',539,'',0,231),
	(555,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',9,1,556,2021,0,0,0,'',539,'',0,231),
	(556,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',10,1,557,2021,0,0,0,'',539,'',0,231),
	(557,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',11,1,558,2021,0,0,0,'',539,'',0,231),
	(558,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',12,1,559,2021,0,0,0,'',539,'',0,231),
	(559,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',13,1,560,2021,0,0,0,'',539,'',0,231),
	(560,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',14,1,561,2021,0,0,0,'',539,'',0,231),
	(561,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',15,1,562,2021,0,0,0,'',539,'',0,231),
	(562,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',16,1,563,2021,0,0,0,'',539,'',0,231),
	(563,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',17,1,564,2021,0,0,0,'',539,'',0,231),
	(564,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',18,1,565,2021,0,0,0,'',539,'',0,231),
	(565,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',19,1,566,2021,0,0,0,'',539,'',0,231),
	(566,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',20,1,567,2021,0,0,0,'',539,'',0,231),
	(567,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',21,1,568,2021,0,0,0,'',539,'',0,231),
	(568,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',22,1,569,2021,0,0,0,'',539,'',0,231),
	(569,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',23,1,570,2021,0,0,0,'',539,'',0,231),
	(570,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',24,1,571,2021,0,0,0,'',539,'',0,231),
	(571,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',25,1,572,2021,0,0,0,'',539,'',0,231),
	(572,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',26,1,573,2021,0,0,0,'',539,'',0,231),
	(573,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',27,1,574,2021,0,0,0,'',539,'',0,231),
	(574,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',28,1,575,2021,0,0,0,'',539,'',0,231),
	(575,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',29,1,576,2021,0,0,0,'',539,'',0,231),
	(576,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',30,1,577,2021,0,0,0,'',539,'',0,231),
	(577,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',31,1,578,2021,0,0,0,'',539,'',0,231),
	(578,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',32,1,579,2021,0,0,0,'',539,'',0,231),
	(579,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',33,1,580,2021,0,0,0,'',539,'',0,231),
	(580,2,1,7,';5;',2,4,'0',0,4,'14:00:00','16:15:00','O',34,1,581,2021,0,0,0,'',539,'',0,231),
	(581,2,1,122,';1;2;',0,0,'0',0,2,'08:15:00','09:30:00','O',44,1,583,2020,0,0,0,'0',0,'test',0,NULL),
	(582,2,1,122,';1;2;3;',6,5,'0',0,4,'06:00:00','08:45:00','O',44,1,584,2020,0,0,0,'0',0,'Test 2',0,NULL),
	(583,1,1,2,';1;',6,2,'0',0,2,'07:00:00','09:15:00','O',44,1,585,2020,0,5,0,'0',0,'',0,197);

/*!40000 ALTER TABLE `edt_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table edt_data_archive
# ------------------------------------------------------------

DROP TABLE IF EXISTS `edt_data_archive`;

CREATE TABLE `edt_data_archive` (
  `_IDdata` int(10) unsigned NOT NULL,
  `_IDedt` int(10) unsigned NOT NULL,
  `_IDcentre` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL,
  `_IDclass` varchar(200) NOT NULL,
  `_IDitem` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_semaine` enum('0','1','2') NOT NULL DEFAULT '0',
  `_group` enum('0','1','2') NOT NULL DEFAULT '0',
  `_jour` int(10) unsigned NOT NULL DEFAULT '0',
  `_debut` time NOT NULL,
  `_fin` time NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_nosemaine` int(10) NOT NULL DEFAULT '0',
  `_etat` int(10) DEFAULT '0' COMMENT '0 = normal, 1 = ajout, 2 = suppression',
  `_IDx` int(11) NOT NULL AUTO_INCREMENT,
  `_annee` int(11) NOT NULL,
  `_attribut` int(11) NOT NULL DEFAULT '0',
  `_IDrmpl` int(11) NOT NULL,
  `_MatRmpl` int(11) NOT NULL,
  `_plus` longtext NOT NULL,
  PRIMARY KEY (`_IDx`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des emplois du temps';



# Affichage de la table edt_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `edt_items`;

CREATE TABLE `edt_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_title` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDitem`),
  KEY `edt_items IDcentre index` (`_IDcentre`),
  CONSTRAINT `edt_items IDcentre to config_centre IDcentre` FOREIGN KEY (`_IDcentre`) REFERENCES `config_centre` (`_IDcentre`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des intitulés des salles';

LOCK TABLES `edt_items` WRITE;
/*!40000 ALTER TABLE `edt_items` DISABLE KEYS */;

INSERT INTO `edt_items` (`_IDitem`, `_IDcentre`, `_title`, `_lang`)
VALUES
	(1,1,'Salle 1','fr'),
	(2,1,'Salle 2','fr'),
	(3,1,'Salle 3','fr'),
	(4,1,'Salle 4','fr'),
	(5,1,'Salle 5','fr'),
	(6,1,'En ligne (visio)','fr');

/*!40000 ALTER TABLE `edt_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table edt_modif
# ------------------------------------------------------------

DROP TABLE IF EXISTS `edt_modif`;

CREATE TABLE `edt_modif` (
  `_ID` int(11) NOT NULL AUTO_INCREMENT,
  `_zone` varchar(200) NOT NULL,
  `_IDclass` varchar(200) NOT NULL,
  `_date` date NOT NULL,
  `_texte` text NOT NULL,
  PRIMARY KEY (`_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table egroup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `egroup`;

CREATE TABLE `egroup` (
  `_IDgroup` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDitem` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDparent` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '255',
  `_date` datetime NOT NULL,
  `_valid` datetime NOT NULL,
  `_ident` varchar(60) NOT NULL,
  `_comment` mediumtext NOT NULL,
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDgroup`,`_lang`),
  UNIQUE KEY `_lang` (`_lang`,`_IDparent`,`_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='liste des e-groupes';



# Affichage de la table egroup_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `egroup_access`;

CREATE TABLE `egroup_access` (
  `_IDaccess` int(11) NOT NULL,
  `_ident` varchar(20) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDaccess`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='table des droits d''accès des utilisateurs pour les e-groupes';



# Affichage de la table egroup_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `egroup_data`;

CREATE TABLE `egroup_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDgroup` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '255',
  `_date` datetime NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_valid` datetime NOT NULL,
  `_lastcnx` datetime NOT NULL,
  `_ident` varchar(60) NOT NULL,
  `_comment` mediumtext NOT NULL,
  `_email` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  `_space` int(10) unsigned NOT NULL DEFAULT '0',
  `_cnx` int(10) unsigned NOT NULL DEFAULT '0',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_auto` enum('O','N') NOT NULL DEFAULT 'O',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_IDmenu` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_lang` (`_lang`,`_IDdata`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Les e-groupes des utilisateurs';



# Affichage de la table egroup_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `egroup_items`;

CREATE TABLE `egroup_items` (
  `_IDitem` int(10) unsigned NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDitem`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Les type de e-groupes';



# Affichage de la table egroup_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `egroup_menu`;

CREATE TABLE `egroup_menu` (
  `_IDmenu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDitem` int(10) unsigned NOT NULL DEFAULT '1',
  `_ident` varchar(20) NOT NULL,
  `_text` tinytext NOT NULL,
  `_link` varchar(40) NOT NULL,
  `_url` enum('O','N') NOT NULL DEFAULT 'O',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '255',
  `_anonyme` enum('O','N') NOT NULL DEFAULT 'O',
  `_backoffice` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDmenu`,`_IDitem`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Les intitulés des modules des e-groupes';



# Affichage de la table egroup_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `egroup_user`;

CREATE TABLE `egroup_user` (
  `_IDuser` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_access` int(11) NOT NULL DEFAULT '-1',
  `_date` datetime NOT NULL,
  `_valid` datetime NOT NULL,
  `_lastcnx` datetime NOT NULL,
  `_invite` datetime NOT NULL,
  `_cnx` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDuser`),
  UNIQUE KEY `_IDdata` (`_IDdata`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Les utilisateurs des e-groupes';



# Affichage de la table email_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_address`;

CREATE TABLE `email_address` (
  `_IDadd` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_name` varchar(40) NOT NULL,
  `_fname` varchar(40) NOT NULL,
  `_sexe` enum('H','F','A') NOT NULL,
  `_titre` varchar(40) NOT NULL,
  `_fonction` tinytext NOT NULL,
  `_company` varchar(40) NOT NULL,
  `_adresse` varchar(60) NOT NULL,
  `_cp` varchar(5) NOT NULL,
  `_ville` varchar(40) NOT NULL,
  `_tel` varchar(20) NOT NULL,
  `_fax` varchar(20) NOT NULL,
  `_email` varchar(40) DEFAULT NULL,
  `_web` varchar(60) NOT NULL,
  PRIMARY KEY (`_IDadd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des carnet d''adresses';



# Affichage de la table flash
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash`;

CREATE TABLE `flash` (
  `_IDflash` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDroot` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDmod` int(11) NOT NULL DEFAULT '0',
  `_IDgrp` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_lock` enum('O','N') NOT NULL DEFAULT 'N',
  `_title` varchar(80) NOT NULL,
  `_align` enum('G','C','D') NOT NULL DEFAULT 'C',
  `_texte` tinytext NOT NULL,
  `_template` varchar(20) NOT NULL DEFAULT 'default.htm',
  `_type` enum('F','P','B','C') NOT NULL DEFAULT 'F',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_autoval` enum('O','N') NOT NULL DEFAULT 'O',
  `_poster` enum('O','N') NOT NULL DEFAULT 'O',
  `_PJ` int(10) unsigned NOT NULL DEFAULT '0',
  `_chrono` enum('O','N','S') NOT NULL DEFAULT 'O',
  `_create` enum('O','N') NOT NULL DEFAULT 'O',
  `_rss` enum('O','N') NOT NULL DEFAULT 'N',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDflash`),
  UNIQUE KEY `_key` (`_title`,`_type`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des publications par Internet';

LOCK TABLES `flash` WRITE;
/*!40000 ALTER TABLE `flash` DISABLE KEYS */;

INSERT INTO `flash` (`_IDflash`, `_IDroot`, `_IDmod`, `_IDgrp`, `_IDgrpwr`, `_IDgrprd`, `_date`, `_lock`, `_title`, `_align`, `_texte`, `_template`, `_type`, `_private`, `_visible`, `_autoval`, `_poster`, `_PJ`, `_chrono`, `_create`, `_rss`, `_lang`)
VALUES
	(31,0,0,1,0,11,'2002-09-01 00:00:00','N','Etudiants','C','','flash.htm','F','N','O','O','O',0,'O','O','N','fr'),
	(37,0,0,4,0,31,'2002-09-01 00:00:00','N','Administration','C','','flash.htm','F','N','O','O','O',1,'N','O','N','fr'),
	(44,0,0,0,0,31,'2002-09-01 00:00:00','N','Général','C','','flash.htm','F','N','O','O','O',1,'N','O','N','fr'),
	(45,0,0,2,0,31,'2002-09-01 00:00:00','N','Enseignants','C','','flash.htm','F','N','O','O','O',1,'N','O','N','fr');

/*!40000 ALTER TABLE `flash` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table flash_breve
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_breve`;

CREATE TABLE `flash_breve` (
  `_IDbreve` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDflash` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IDgrp` text NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_title` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_color` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  `_img` varchar(20) NOT NULL DEFAULT 'defaut.png',
  `_hit` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDbreve`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des brêves des publications par Internet';



# Affichage de la table flash_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_data`;

CREATE TABLE `flash_data` (
  `_IDinfos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDflash` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_modif` datetime DEFAULT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_title` varchar(80) NOT NULL,
  `_align` enum('G','C','D') NOT NULL DEFAULT 'C',
  `_color` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  `_img` varchar(5) DEFAULT NULL,
  `_snd` varchar(5) DEFAULT NULL,
  `_repeat` enum('O','N') NOT NULL DEFAULT 'O',
  `_hit` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDinfos`),
  UNIQUE KEY `_key` (`_IDflash`,`_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des rubriques des publications par Internet';

LOCK TABLES `flash_data` WRITE;
/*!40000 ALTER TABLE `flash_data` DISABLE KEYS */;

INSERT INTO `flash_data` (`_IDinfos`, `_IDflash`, `_date`, `_modif`, `_ID`, `_IP`, `_title`, `_align`, `_color`, `_img`, `_snd`, `_repeat`, `_hit`, `_visible`)
VALUES
	(1,44,'2013-07-16 13:54:53','2020-10-30 14:12:57',9,-129,'ENT','','','','','O',48830,'O'),
	(2,45,'2013-09-06 11:16:41','2013-09-09 11:18:46',9,-4,'Ma première rubrique','G','','','','O',14,'O'),
	(5,46,'2013-07-16 13:54:53','2014-06-04 16:09:36',1,1,'test de','','','','','O',1619,'O'),
	(6,0,'2014-06-04 16:25:32','2014-06-05 11:52:00',9,-4,'test de','','','','','O',0,'O'),
	(7,29,'2014-06-05 12:02:29','2014-06-05 12:24:58',9,-4,'Flash','','','','','O',18,'O'),
	(10,49,'2014-11-30 22:19:32','2015-02-22 23:38:06',61,-69,'ENT','','','','','O',5826,'O'),
	(11,0,'2018-11-28 16:24:24','2018-11-28 16:24:24',9,-129,'#menu_item:','','','','','O',0,'O');

/*!40000 ALTER TABLE `flash_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table flash_default
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_default`;

CREATE TABLE `flash_default` (
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDflash` int(10) unsigned NOT NULL,
  `_IDgrp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `_lang` varchar(2) NOT NULL,
  UNIQUE KEY `_key` (`_IDcentre`,`_IDflash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des flash-infos par défaut';

LOCK TABLES `flash_default` WRITE;
/*!40000 ALTER TABLE `flash_default` DISABLE KEYS */;

INSERT INTO `flash_default` (`_IDcentre`, `_IDflash`, `_IDgrp`, `_lang`)
VALUES
	(1,30,0,'fr'),
	(1,44,0,'fr'),
	(1,46,0,'de'),
	(1,47,0,'de'),
	(1,29,0,'de'),
	(1,24,0,'de'),
	(1,16,0,'de'),
	(1,28,0,'de'),
	(1,37,0,'fr'),
	(1,43,0,'fr'),
	(1,48,0,'fr'),
	(2,48,0,'fr'),
	(1,39,0,'fr'),
	(1,49,0,'de'),
	(1,27,0,'de'),
	(1,21,0,'de'),
	(1,31,0,'fr');

/*!40000 ALTER TABLE `flash_default` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table flash_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_items`;

CREATE TABLE `flash_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDinfos` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_modif` datetime DEFAULT NULL,
  `_ID` text NOT NULL,
  `_IP` text NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_raw` enum('O','N') NOT NULL DEFAULT 'O',
  `_c` varchar(2) DEFAULT 'ON',
  `_e` varchar(2) DEFAULT 'ON',
  `_r` varchar(2) DEFAULT 'ON',
  `_color` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  `_img` varchar(5) NOT NULL,
  `_order` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDitem`),
  KEY `IDinfos index` (`_IDinfos`),
  CONSTRAINT `IDinfos to flash_data` FOREIGN KEY (`_IDinfos`) REFERENCES `flash_data` (`_IDinfos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des articles par rubriques des publications par Intern';

LOCK TABLES `flash_items` WRITE;
/*!40000 ALTER TABLE `flash_items` DISABLE KEYS */;

INSERT INTO `flash_items` (`_IDitem`, `_IDinfos`, `_date`, `_modif`, `_ID`, `_IP`, `_title`, `_texte`, `_raw`, `_c`, `_e`, `_r`, `_color`, `_img`, `_order`, `_visible`)
VALUES
	(3,1,'2013-09-26 14:31:21','2020-10-22 15:27:32','3200','192.168.1.1','Prométhée','<p><strong>Bienvenue sur Prom&eacute;th&eacute;e, votre espace num&eacute;rique de travail</strong></p>\r\n\r\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" style=\"width:100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"width:450px\"><img alt=\"\" src=\"download/flash/ENT-Environnement-Numerique-de-Travail.jpg\" style=\"width: 434px; height: 304px;\" /></td>\r\n			<td style=\"vertical-align: top; white-space: nowrap;\">\r\n			<p>Acc&eacute;dez &agrave; l&#39;ensemble de vos donn&eacute;es</p>\r\n\r\n			<ul style=\"margin-left:50px: float: right\">\r\n				<li>Emploi du temps</li>\r\n				<li>Notes</li>\r\n				<li>Examens &agrave; venir</li>\r\n				<li>Fichiers</li>\r\n				<li>Absences</li>\r\n				<li>Cahier de texte</li>\r\n				<li>Cours en ligne</li>\r\n				<li>Messagerie</li>\r\n				<li>Stages</li>\r\n			</ul>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>','N','','','','','',16,'O');

/*!40000 ALTER TABLE `flash_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table flash_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_link`;

CREATE TABLE `flash_link` (
  `_IDlink` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDbreve` int(10) unsigned NOT NULL,
  `_titre` varchar(80) NOT NULL,
  `_url` varchar(128) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_lang` varchar(6) NOT NULL,
  `_IDlicense` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDlink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des liens concernant les brêves des publications par I';



# Affichage de la table flash_pj
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_pj`;

CREATE TABLE `flash_pj` (
  `_IDpj` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDitem` int(10) unsigned NOT NULL,
  `_title` text NOT NULL,
  `_texte` varchar(80) NOT NULL,
  `_ext` varchar(5) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_res` varchar(20) NOT NULL,
  `_attach` enum('O','N') NOT NULL DEFAULT 'O',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDpj`),
  KEY `IDitem index` (`_IDitem`),
  CONSTRAINT `IDitem to flash_items` FOREIGN KEY (`_IDitem`) REFERENCES `flash_items` (`_IDitem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des PJ par rubrique des publications par Internet';



# Affichage de la table flash_root
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_root`;

CREATE TABLE `flash_root` (
  `_IDroot` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDparent` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL,
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDroot`),
  UNIQUE KEY `_key` (`_IDparent`,`_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des répertoires des flash-infos';



# Affichage de la table flash_vote
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flash_vote`;

CREATE TABLE `flash_vote` (
  `_IDvote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDbreve` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_vote` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDvote`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des votes concernant les brêves des publications par I';



# Affichage de la table forfait
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forfait`;

CREATE TABLE `forfait` (
  `_IDforfait` int(11) NOT NULL AUTO_INCREMENT,
  `_Nom` varchar(200) NOT NULL,
  PRIMARY KEY (`_IDforfait`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `forfait` WRITE;
/*!40000 ALTER TABLE `forfait` DISABLE KEYS */;

INSERT INTO `forfait` (`_IDforfait`, `_Nom`)
VALUES
	(1,'Forfait Cours particulier'),
	(2,'Forfait Groupe'),
	(3,'Forfait test');

/*!40000 ALTER TABLE `forfait` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table forfait_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forfait_log`;

CREATE TABLE `forfait_log` (
  `_IDlog` int(11) NOT NULL,
  `_IDuser` int(11) NOT NULL,
  `_IDforfait` int(11) NOT NULL,
  `_IDcours` int(11) NOT NULL,
  `_mvt` int(11) NOT NULL,
  `_solde` int(11) NOT NULL,
  `_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Affichage de la table forum
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forum`;

CREATE TABLE `forum` (
  `_IDroot` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDgroup` int(11) NOT NULL DEFAULT '0',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_title` varchar(40) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_i18n` enum('O','N') NOT NULL DEFAULT 'N',
  `_maximize` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDroot`),
  UNIQUE KEY `_key` (`_IDroot`,`_IDgroup`,`_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des répertoires des forums';



# Affichage de la table forum_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forum_data`;

CREATE TABLE `forum_data` (
  `_IDforum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDroot` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgroup` int(11) NOT NULL DEFAULT '0',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_access` datetime NOT NULL,
  `_title` varchar(40) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_PJ` int(10) unsigned NOT NULL DEFAULT '0',
  `_update` enum('O','N') NOT NULL DEFAULT 'N',
  `_erase` enum('O','N') NOT NULL DEFAULT 'N',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_email` enum('-','P','E') NOT NULL DEFAULT '-',
  `_showmode` enum('F','E') NOT NULL DEFAULT 'F',
  `_autoval` enum('O','N') NOT NULL DEFAULT 'N',
  `_chrono` enum('O','N','P') NOT NULL DEFAULT 'N',
  `_mailcp` enum('O','N') NOT NULL DEFAULT 'N',
  `_rss` enum('O','N') NOT NULL DEFAULT 'N',
  `_maximize` enum('O','N') NOT NULL DEFAULT 'N',
  `_image` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDforum`),
  UNIQUE KEY `_key` (`_IDgroup`,`_title`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des forums';

LOCK TABLES `forum_data` WRITE;
/*!40000 ALTER TABLE `forum_data` DISABLE KEYS */;

INSERT INTO `forum_data` (`_IDforum`, `_IDroot`, `_IDgroup`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_date`, `_access`, `_title`, `_texte`, `_visible`, `_PJ`, `_update`, `_erase`, `_private`, `_email`, `_showmode`, `_autoval`, `_chrono`, `_mailcp`, `_rss`, `_maximize`, `_image`, `_lang`)
VALUES
	(1,0,0,0,254,254,'2002-09-01 00:00:00','0000-00-00 00:00:00','Internal','This space is reserved to the personnel of the establishment. It is intended to receive your comments, remarks, reflexions, questions or even your small advertisements.','O',0,'N','N','N','-','F','N','N','N','N','N','http://www.imageshack.us/','en'),
	(2,0,0,0,255,255,'2002-09-01 00:00:00','0000-00-00 00:00:00','Stagiaires','This public space makes it possible to express you. It is intended to receive your comments, remarks, reflexions, questions or even your small advertisements.','O',0,'N','N','N','-','F','N','N','N','N','N','','en'),
	(3,0,0,0,254,254,'2002-09-01 00:00:00','0000-00-00 00:00:00','Interno','Se reserva este espacio para el personal del establecimiento. Se destina a recibir sus comentarios, notas, reflexiones, preguntas o anuncios.','O',0,'N','N','N','-','F','N','N','N','N','N','http://www.imageshack.us/','es'),
	(4,0,0,0,255,255,'2002-09-01 00:00:00','0000-00-00 00:00:00','Stagiaires','Este espacio publico le permite expresion se destina a recibir sus comentarios, notas, reflexiones, preguntas o anuncios.','O',0,'N','N','N','-','F','N','N','N','N','N','','es'),
	(5,0,0,0,254,254,'2002-09-01 00:00:00','0000-00-00 00:00:00','Interne','Cet espace est réservé au personnel de l\'établissement. Il est destiné à recevoir vos commentaires, remarques, réflexions, questions ou même vos petites annonces.','O',0,'N','N','N','-','F','N','N','N','N','N','http://www.imageshack.us/','fr'),
	(6,0,0,0,255,255,'2002-09-01 00:00:00','0000-00-00 00:00:00','Stagiaires','Cet espace public permet de vous exprimer. Il est destiné à recevoir vos commentaires, remarques, réflexions, questions ou même vos petites annonces.','O',0,'N','N','N','-','F','N','N','N','N','N','','fr');

/*!40000 ALTER TABLE `forum_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table forum_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forum_items`;

CREATE TABLE `forum_items` (
  `_IDmsg` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDforum` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_update` datetime NOT NULL,
  `_access` datetime NOT NULL,
  `_thread` int(10) unsigned NOT NULL DEFAULT '0',
  `_parent` int(10) unsigned NOT NULL DEFAULT '0',
  `_title` varchar(80) NOT NULL,
  `_IDsmile` int(10) unsigned NOT NULL DEFAULT '1',
  `_texte` mediumtext NOT NULL,
  `_post` int(10) unsigned NOT NULL DEFAULT '0',
  `_type` enum('M','P') NOT NULL DEFAULT 'M',
  `_sign` enum('O','N') NOT NULL DEFAULT 'O',
  `_censor` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDmsg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des messages des forums';



# Affichage de la table forum_list
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forum_list`;

CREATE TABLE `forum_list` (
  `_IDforum` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'N',
  UNIQUE KEY `_key` (`_IDforum`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des messages à envoyé par email';



# Affichage de la table forum_pj
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forum_pj`;

CREATE TABLE `forum_pj` (
  `_IDpj` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmsg` int(10) unsigned NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_ext` varchar(5) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_IDpj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des PJ des forums';



# Affichage de la table forum_vu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forum_vu`;

CREATE TABLE `forum_vu` (
  `_IDmsg` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL,
  `_date` datetime NOT NULL,
  UNIQUE KEY `_key` (`_IDmsg`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de log des messages lus';



# Affichage de la table ftp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ftp`;

CREATE TABLE `ftp` (
  `_IDftp` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_ident` varchar(40) NOT NULL,
  `_path` varchar(255) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_open` datetime NOT NULL,
  `_close` datetime NOT NULL,
  `_sort` enum('C','D') NOT NULL DEFAULT 'C',
  `_lock` int(10) unsigned NOT NULL DEFAULT '1',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDftp`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des serveurs ftp';

LOCK TABLES `ftp` WRITE;
/*!40000 ALTER TABLE `ftp` DISABLE KEYS */;

INSERT INTO `ftp` (`_IDftp`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_ident`, `_path`, `_texte`, `_open`, `_close`, `_sort`, `_lock`, `_visible`, `_lang`)
VALUES
	(1,0,30,31,'Intranet server','download/ftp/serveur','Various Documents for download.','0000-00-00 00:00:00','0000-00-00 00:00:00','C',1,'O','en'),
	(2,0,30,31,'servidor de Intranet','download/ftp/serveur','Varios documentos para descargar.','0000-00-00 00:00:00','0000-00-00 00:00:00','C',1,'O','es'),
	(1,0,30,31,'serveur Intranet','download/ftp/serveur','Documents divers en libre téléchargement.','0000-00-00 00:00:00','0000-00-00 00:00:00','C',1,'O','fr'),
	(2,0,2,11,'sujets CCF','download/ftp/ccf','Les sujets CCF de ce serveur sont accessibles sous certaines conditions.','0000-00-00 00:00:00','0000-00-00 00:00:00','C',1,'N','fr');

/*!40000 ALTER TABLE `ftp` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ftp_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ftp_data`;

CREATE TABLE `ftp_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDparent` int(10) unsigned NOT NULL,
  `_IDftp` int(10) unsigned NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_date` datetime NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_IDmod` int(10) unsigned NOT NULL,
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_open` datetime NOT NULL,
  `_close` datetime NOT NULL,
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_key` (`_IDparent`,`_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des répertoires ftp';



# Affichage de la table ftp_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ftp_items`;

CREATE TABLE `ftp_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_IDftp` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_ident` varchar(40) NOT NULL,
  `_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDmod` int(10) unsigned NOT NULL,
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDitem`),
  UNIQUE KEY `_key` (`_IDdata`,`_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des documents ftp';



# Affichage de la table ftp_note
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ftp_note`;

CREATE TABLE `ftp_note` (
  `_IDnote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_modify` datetime NOT NULL,
  `_path` varchar(255) NOT NULL,
  `_text` mediumtext NOT NULL,
  `_lock` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDnote`),
  UNIQUE KEY `_key` (`_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des notes sur les ressources ftp';



# Affichage de la table gallery
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gallery`;

CREATE TABLE `gallery` (
  `_IDgal` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_title` varchar(40) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_sort` int(10) unsigned NOT NULL DEFAULT '0',
  `_autoval` enum('O','N') NOT NULL DEFAULT 'O',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDgal`,`_lang`),
  UNIQUE KEY `_key` (`_title`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des thèmes des photothèques';

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;

INSERT INTO `gallery` (`_IDgal`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_date`, `_title`, `_texte`, `_sort`, `_autoval`, `_visible`, `_lang`)
VALUES
	(1,0,254,254,'2002-09-01 00:00:00','school Life','This topic is intended to visualize the pictures taken during PAE, PUS or other projects teaching.',0,'O','O','en'),
	(2,0,254,254,'2002-09-01 00:00:00','Pedagogy','This topic presents the pictures being used as support at the various teaching courses.',0,'O','O','en'),
	(3,0,254,254,'2002-09-01 00:00:00','e-group','This topic presents the pictures of the virtual groups.',0,'O','O','en'),
	(1,0,254,254,'2002-09-01 00:00:00','Vida escolar','Este tema se destina a visualizar las fotografias sacadas durante los PAE, PUS o otros progectos pedagogicos.',0,'O','O','es'),
	(2,0,254,254,'2002-09-01 00:00:00','Pedagogía','Este tema presenta las fotografias que sirven de soportes a las diferentes clases pedagogicas.',0,'O','O','es'),
	(3,0,254,254,'2002-09-01 00:00:00','e-grupo','Este tema presenta las fotografias de los grupos virtuales.',0,'O','O','es'),
	(1,0,254,254,'2002-09-01 00:00:00','Vie scolaire','Ce thème est destiné à visualiser les photos prises lors des PAE, PUS ou d\'autres projets pédagogiques.',0,'O','O','fr'),
	(2,0,254,254,'2002-09-01 00:00:00','Pédagogie','Ce thème présente les photos servant de support aux différents cours pédagogiques.',0,'O','O','fr'),
	(3,0,254,254,'2002-09-01 00:00:00','e-groupe','Ce thème présente les photos des groupes virtuels.',0,'O','O','fr');

/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table gallery_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_data`;

CREATE TABLE `gallery_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDgal` int(10) unsigned NOT NULL,
  `_IDroot` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgroup` int(11) NOT NULL DEFAULT '0',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_title` varchar(40) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_wiki` enum('O','N') NOT NULL DEFAULT 'O',
  `_file` enum('O','N') NOT NULL DEFAULT 'O',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_PJ` enum('O','N') NOT NULL DEFAULT 'N',
  `_imgwidth` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_key` (`_IDgal`,`_IDroot`,`_IDgroup`,`_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des photothèques';

LOCK TABLES `gallery_data` WRITE;
/*!40000 ALTER TABLE `gallery_data` DISABLE KEYS */;

INSERT INTO `gallery_data` (`_IDdata`, `_IDgal`, `_IDroot`, `_IDgroup`, `_IDmod`, `_IP`, `_IDgrpwr`, `_IDgrprd`, `_date`, `_title`, `_texte`, `_wiki`, `_file`, `_private`, `_PJ`, `_imgwidth`, `_visible`)
VALUES
	(1,1,0,0,9,-4,254,254,'2013-09-06 11:13:40','test image','','O','O','N','N',700,'O');

/*!40000 ALTER TABLE `gallery_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table gallery_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_items`;

CREATE TABLE `gallery_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_file` varchar(80) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_width` int(10) unsigned NOT NULL,
  `_height` int(10) unsigned NOT NULL,
  `_hit` int(10) unsigned NOT NULL,
  `_titre` varchar(40) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDitem`),
  UNIQUE KEY `_key` (`_IDdata`,`_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des images des photothèques';

LOCK TABLES `gallery_items` WRITE;
/*!40000 ALTER TABLE `gallery_items` DISABLE KEYS */;

INSERT INTO `gallery_items` (`_IDitem`, `_IDdata`, `_ID`, `_IP`, `_date`, `_file`, `_size`, `_width`, `_height`, `_hit`, `_titre`, `_texte`, `_visible`)
VALUES
	(1,1,9,-4,'2013-09-06 11:14:33','golf_metz.png',292886,600,300,5,'img1','','O'),
	(2,1,9,-4,'2013-09-10 16:50:07','provence.jpg',101563,640,480,3,'Provence','','O');

/*!40000 ALTER TABLE `gallery_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table gallery_pj
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_pj`;

CREATE TABLE `gallery_pj` (
  `_IDpj` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_file` varchar(40) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_IDpj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des PJ des galeries';



# Affichage de la table gallery_root
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_root`;

CREATE TABLE `gallery_root` (
  `_IDroot` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDparent` int(10) unsigned NOT NULL,
  `_IDgal` int(10) unsigned NOT NULL,
  `_IDgroup` int(11) NOT NULL DEFAULT '0',
  `_titre` varchar(40) NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDroot`),
  UNIQUE KEY `_key` (`_IDparent`,`_IDgal`,`_IDgroup`,`_titre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des répertoires des galeries';



# Affichage de la table groupe
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groupe`;

CREATE TABLE `groupe` (
  `_IDeleve` int(11) NOT NULL,
  `_IDmat` int(11) NOT NULL,
  `_IDprof` int(11) NOT NULL,
  `_IDgrp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Affichage de la table groupe_nom
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groupe_nom`;

CREATE TABLE `groupe_nom` (
  `_IDgrp` int(11) NOT NULL AUTO_INCREMENT,
  `_IDcentre` int(11) NOT NULL,
  `_nom` varchar(300) NOT NULL,
  `_commentaire` longtext NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_attribut` longtext NOT NULL,
  PRIMARY KEY (`_IDgrp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `groupe_nom` WRITE;
/*!40000 ALTER TABLE `groupe_nom` DISABLE KEYS */;

INSERT INTO `groupe_nom` (`_IDgrp`, `_IDcentre`, `_nom`, `_commentaire`, `_visible`, `_attribut`)
VALUES
	(1,1,'Groupe de test','Ceci est un groupe de test','O','');

/*!40000 ALTER TABLE `groupe_nom` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `_IDimage` int(11) NOT NULL AUTO_INCREMENT,
  `_type` varchar(100) NOT NULL,
  `_ID` int(10) unsigned DEFAULT NULL,
  `_title` varchar(200) NOT NULL,
  `_droits` int(11) NOT NULL,
  `_attr` longtext NOT NULL,
  `_date` datetime NOT NULL COMMENT 'Date et heure de l''upload',
  `_ext` text NOT NULL COMMENT 'Extension du fichier',
  `_share` text NOT NULL COMMENT 'Json contenant les attributs de partage du fichier/dossier',
  `_size` int(10) NOT NULL COMMENT 'Taille du fichier en mo',
  `_parent` text NOT NULL COMMENT 'ID de l''élément parent',
  PRIMARY KEY (`_IDimage`),
  KEY `images _ID index` (`_ID`),
  CONSTRAINT `images _ID to user_id _ID` FOREIGN KEY (`_ID`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table ip
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip`;

CREATE TABLE `ip` (
  `_IP` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcentre` int(10) unsigned NOT NULL,
  `_IPv6` varchar(23) NOT NULL,
  `_host` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IP`),
  UNIQUE KEY `_key` (`_IPv6`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des adresses IP des postes clients';

LOCK TABLES `ip` WRITE;
/*!40000 ALTER TABLE `ip` DISABLE KEYS */;

INSERT INTO `ip` (`_IP`, `_IDcentre`, `_IPv6`, `_host`, `_visible`)
VALUES
	(1,1,'127.0.0.1','localhost','O');

/*!40000 ALTER TABLE `ip` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ip_denied
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_denied`;

CREATE TABLE `ip_denied` (
  `_IP` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IPv6` varchar(23) NOT NULL,
  `_date` datetime NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IP`),
  UNIQUE KEY `_key` (`_IPv6`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des adresses IP en liste brûlée';



# Affichage de la table ip_logerr
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_logerr`;

CREATE TABLE `ip_logerr` (
  `_auto` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_adm` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `_name` varchar(80) NOT NULL,
  `_item` int(10) unsigned NOT NULL,
  `_cmde` varchar(20) NOT NULL,
  PRIMARY KEY (`_auto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des log des erreurs des autorisations sur les modules';



# Affichage de la table ip_remote
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_remote`;

CREATE TABLE `ip_remote` (
  `_IP` bigint(20) NOT NULL AUTO_INCREMENT,
  `_IPv6` varchar(23) NOT NULL,
  `_host` varchar(50) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IP`),
  UNIQUE KEY `_key` (`_IPv6`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des adresses IP des postes distants en extranet';



# Affichage de la table liaison
# ------------------------------------------------------------

DROP TABLE IF EXISTS `liaison`;

CREATE TABLE `liaison` (
  `_IDclass` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_PJ` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`_IDclass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des cahiers de liaison';

LOCK TABLES `liaison` WRITE;
/*!40000 ALTER TABLE `liaison` DISABLE KEYS */;

INSERT INTO `liaison` (`_IDclass`, `_IDcentre`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_visible`, `_PJ`)
VALUES
	(1,1,0,30,30,'O','O');

/*!40000 ALTER TABLE `liaison` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table liaison_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `liaison_data`;

CREATE TABLE `liaison_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDeleve` int(10) unsigned NOT NULL,
  `_period` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDclass` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_IDdata`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des cahiers de liaison élève';

LOCK TABLES `liaison_data` WRITE;
/*!40000 ALTER TABLE `liaison_data` DISABLE KEYS */;

INSERT INTO `liaison_data` (`_IDdata`, `_IDeleve`, `_period`, `_IDclass`)
VALUES
	(1,77,1,1);

/*!40000 ALTER TABLE `liaison_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table liaison_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `liaison_items`;

CREATE TABLE `liaison_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_IDparent` int(10) unsigned NOT NULL DEFAULT '0',
  `_ID` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_text1` mediumtext NOT NULL,
  `_text2` mediumtext NOT NULL,
  `_text3` mediumtext NOT NULL,
  `_raw` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des articles des cahiers de liaison';

LOCK TABLES `liaison_items` WRITE;
/*!40000 ALTER TABLE `liaison_items` DISABLE KEYS */;

INSERT INTO `liaison_items` (`_IDitem`, `_IDdata`, `_IDparent`, `_ID`, `_IDmat`, `_IP`, `_date`, `_text1`, `_text2`, `_text3`, `_raw`)
VALUES
	(1,1,0,9,0,-4,'2014-08-29 11:44:58','test','','','O');

/*!40000 ALTER TABLE `liaison_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table mail_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mail_log`;

CREATE TABLE `mail_log` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `_date` datetime NOT NULL,
  `_type` int(11) NOT NULL COMMENT '1 = récap des cours de la semaine, 2 = demande de confirmation de cours',
  `_dest_count` int(11) NOT NULL,
  `_dest` longtext NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table marquee
# ------------------------------------------------------------

DROP TABLE IF EXISTS `marquee`;

CREATE TABLE `marquee` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(10) NOT NULL,
  `_text` varchar(40) NOT NULL,
  `_item` int(10) unsigned NOT NULL DEFAULT '2',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDitem`),
  UNIQUE KEY `_key` (`_text`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des annonces défilantes';

LOCK TABLES `marquee` WRITE;
/*!40000 ALTER TABLE `marquee` DISABLE KEYS */;

INSERT INTO `marquee` (`_IDitem`, `_ident`, `_text`, `_item`, `_visible`, `_lang`)
VALUES
	(1,'forum','Forums',2,'O','en'),
	(2,'doc','Documents repository',2,'O','en'),
	(3,'flash','Informations-Flash',2,'O','en'),
	(4,'fil','Live News',2,'O','en'),
	(5,'galerie','Galleries',2,'O','en'),
	(6,'forum','Foros',2,'O','es'),
	(7,'doc','Documentos repositorio',2,'O','es'),
	(8,'flash','Flash-info',2,'O','es'),
	(9,'fil','Noticias ',2,'O','es'),
	(10,'galerie','Galerias',2,'O','es'),
	(11,'forum','Forums',2,'O','fr'),
	(12,'doc','Dépôt de documents',2,'O','fr'),
	(13,'flash','Flash-infos',2,'O','fr'),
	(14,'fil','FIL d\'informations',2,'O','fr'),
	(15,'galerie','Galeries',2,'O','fr');

/*!40000 ALTER TABLE `marquee` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table mat_forfait
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mat_forfait`;

CREATE TABLE `mat_forfait` (
  `_IDforfait` int(11) NOT NULL,
  `_IDmat` int(11) NOT NULL,
  UNIQUE KEY `_IDmat` (`_IDmat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `mat_forfait` WRITE;
/*!40000 ALTER TABLE `mat_forfait` DISABLE KEYS */;

INSERT INTO `mat_forfait` (`_IDforfait`, `_IDmat`)
VALUES
	(0,61),
	(0,1),
	(0,2),
	(0,3),
	(0,4),
	(0,65),
	(0,10),
	(0,8),
	(0,11),
	(0,12),
	(0,13),
	(0,14),
	(0,15),
	(0,123),
	(0,112),
	(0,25),
	(0,192),
	(0,148),
	(0,208),
	(0,78),
	(0,215),
	(0,180),
	(0,178),
	(0,173),
	(0,82),
	(0,83),
	(0,221),
	(0,84),
	(0,222),
	(0,85),
	(0,146),
	(0,185),
	(0,184),
	(0,195),
	(0,127),
	(0,124),
	(0,133),
	(0,155),
	(0,161),
	(0,162),
	(0,122),
	(0,99),
	(0,174),
	(0,225),
	(0,176),
	(0,137),
	(0,125),
	(0,126),
	(0,129),
	(0,136),
	(0,153),
	(0,167),
	(0,168),
	(0,169),
	(0,171),
	(0,172),
	(0,196),
	(0,188),
	(0,190),
	(0,138),
	(0,140),
	(0,183),
	(0,158),
	(0,141),
	(0,142),
	(0,220),
	(0,149),
	(0,150),
	(0,199),
	(0,200),
	(0,202),
	(0,205),
	(0,213),
	(0,214),
	(0,217),
	(0,134),
	(0,151),
	(0,181),
	(0,132),
	(0,182),
	(0,231),
	(0,233);

/*!40000 ALTER TABLE `mat_forfait` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `_IDcentre` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_period` enum('0','1','2') NOT NULL DEFAULT '0',
  `_month` int(10) unsigned NOT NULL DEFAULT '8',
  `_email` enum('-','P','E') NOT NULL DEFAULT '-',
  `_text` tinytext NOT NULL,
  `_decimal` int(10) unsigned NOT NULL DEFAULT '1',
  `_separator` varchar(1) NOT NULL DEFAULT '.',
  `_max` int(10) unsigned NOT NULL DEFAULT '10',
  `_display` varchar(5) NOT NULL DEFAULT '11000',
  `_font` int(10) unsigned NOT NULL DEFAULT '10',
  UNIQUE KEY `_key` (`_IDcentre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des bulletins';

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;

INSERT INTO `notes` (`_IDcentre`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_period`, `_month`, `_email`, `_text`, `_decimal`, `_separator`, `_max`, `_display`, `_font`)
VALUES
	(1,0,10,11,'1',8,'-','20',0,'.',10,'00001',10),
	(2,0,0,0,'0',0,'-','20',0,'.',10,'10000',10);

/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table notes_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes_data`;

CREATE TABLE `notes_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_year` int(10) unsigned NOT NULL,
  `_IDclass` int(10) unsigned NOT NULL,
  `_IDmat` int(100) unsigned NOT NULL COMMENT 'Si > 10000 alors c''est un UV sinon c''est un PMA',
  `_period` int(10) unsigned NOT NULL DEFAULT '1',
  `_type` tinytext NOT NULL,
  `_total` tinytext NOT NULL,
  `_coef` tinytext NOT NULL,
  `_visible` tinytext NOT NULL,
  `_lock` enum('O','N') NOT NULL DEFAULT 'N',
  `_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_key` (`_year`,`_IDclass`,`_IDmat`,`_period`),
  KEY `IDclass to campus_class` (`_IDclass`),
  CONSTRAINT `IDclass to campus_class` FOREIGN KEY (`_IDclass`) REFERENCES `campus_classe` (`_IDclass`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des bulletins élèves';



# Affichage de la table notes_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes_items`;

CREATE TABLE `notes_items` (
  `_IDitems` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` text NOT NULL,
  `_create` datetime DEFAULT NULL,
  `_update` datetime DEFAULT NULL,
  `_IDeleve` int(10) unsigned NOT NULL,
  `_index` int(10) unsigned NOT NULL DEFAULT '0',
  `_value` varchar(6) NOT NULL,
  PRIMARY KEY (`_IDitems`),
  UNIQUE KEY `_key` (`_IDdata`,`_IDeleve`,`_index`),
  KEY `IDeleve` (`_IDeleve`),
  KEY `IDdata notes_items` (`_IDdata`),
  CONSTRAINT `IDdata from notes_items to notes_data _IDdata` FOREIGN KEY (`_IDdata`) REFERENCES `notes_data` (`_IDdata`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `IDeleve from notes_items to user_id` FOREIGN KEY (`_IDeleve`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des notes';



# Affichage de la table notes_lock
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes_lock`;

CREATE TABLE `notes_lock` (
  `_IDlock` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_year` int(10) unsigned NOT NULL,
  `_IDclass` int(10) unsigned NOT NULL,
  `_period` int(10) unsigned NOT NULL DEFAULT '1',
  `_coef` tinytext NOT NULL,
  `_visible` tinytext NOT NULL,
  `_lock` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`_IDlock`),
  UNIQUE KEY `_key` (`_year`,`_IDclass`,`_period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des verroux';



# Affichage de la table notes_rattrapage
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes_rattrapage`;

CREATE TABLE `notes_rattrapage` (
  `_IDratt` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_IDeleve` int(11) unsigned DEFAULT NULL,
  `_year` int(11) DEFAULT NULL,
  `_period` int(11) DEFAULT NULL,
  `_IDpole` int(11) DEFAULT NULL COMMENT 'Si null alors c''est une matière avec une liaison PMA',
  `_ID_pma` int(11) DEFAULT NULL COMMENT 'Si null alors c''est un pole',
  `_matt_validation` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Est-ce que l''on force l''élève à passer la matière et à ne pas aller au rattrapage ?',
  PRIMARY KEY (`_IDratt`),
  KEY `IDeleve` (`_IDeleve`),
  KEY `IDpma` (`_ID_pma`),
  KEY `IDpole` (`_year`),
  KEY `Pole ID` (`_IDpole`),
  CONSTRAINT `Pole ID` FOREIGN KEY (`_IDpole`) REFERENCES `pole` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `User ID` FOREIGN KEY (`_IDeleve`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table notes_text
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes_text`;

CREATE TABLE `notes_text` (
  `_IDeleve` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_IDclass` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL DEFAULT '0',
  `_year` int(10) unsigned NOT NULL,
  `_period` int(10) unsigned NOT NULL DEFAULT '1',
  `_text` tinytext NOT NULL,
  `_lock` enum('O','N') NOT NULL DEFAULT 'N',
  `_stage_hours` int(11) NOT NULL COMMENT 'Nombre d''heures de stage',
  `_validated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Est-ce que le bulletin à été validé ?',
  `_open_doors_hours` int(11) NOT NULL DEFAULT '0' COMMENT 'Heures de portes ouvertes/',
  `_attr` longtext COMMENT 'Attributs supplémentaires',
  UNIQUE KEY `_key` (`_IDeleve`,`_IDclass`,`_IDmat`,`_year`,`_period`),
  KEY `_IDeleve` (`_IDeleve`),
  KEY `IDclass notes_text` (`_IDclass`),
  CONSTRAINT `IDclass from notes_text to campus_class IDclass` FOREIGN KEY (`_IDclass`) REFERENCES `campus_classe` (`_IDclass`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `IDeleve to user_id` FOREIGN KEY (`_IDeleve`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des appréciations';



# Affichage de la table notes_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes_type`;

CREATE TABLE `notes_type` (
  `_IDtype` int(10) unsigned NOT NULL,
  `_ident` varchar(6) NOT NULL,
  `_text` varchar(40) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  UNIQUE KEY `_key` (`_IDtype`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des types de controle';

LOCK TABLES `notes_type` WRITE;
/*!40000 ALTER TABLE `notes_type` DISABLE KEYS */;

INSERT INTO `notes_type` (`_IDtype`, `_ident`, `_text`, `_lang`)
VALUES
	(1,'DS','Devoir Surveillé','fr'),
	(2,'DM','Devoir Maison','fr'),
	(3,'TP','Travail Pratique','fr'),
	(4,'CC','Controle Connaisances','fr'),
	(5,'CO','Compréhension Orale','fr'),
	(6,'EO','Expression Orale','fr'),
	(7,'EE','Expression Ecrite','fr'),
	(8,'CE','Compréhension Ecrite','fr'),
	(9,'TD','Travail dirigé','fr');

/*!40000 ALTER TABLE `notes_type` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table parametre
# ------------------------------------------------------------

DROP TABLE IF EXISTS `parametre`;

CREATE TABLE `parametre` (
  `_code` varchar(200) NOT NULL,
  `_valeur` longtext NOT NULL,
  `_IDuser` int(10) NOT NULL DEFAULT '0',
  `_comm` longtext NOT NULL,
  PRIMARY KEY (`_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `parametre` WRITE;
/*!40000 ALTER TABLE `parametre` DISABLE KEYS */;

INSERT INTO `parametre` (`_code`, `_valeur`, `_IDuser`, `_comm`)
VALUES
	('START_Y','2021',0,'Année de début de l\'année scolaire actuelle'),
	('END_Y','2022',0,'Année de fin de l\'année scolaire actuelle'),
	('START_M','9',0,'Mois de début de l\'année scolaire actuelle'),
	('END_M','8',0,'Mois de fin de l\'année scolaire actuelle'),
	('START_D','1',0,''),
	('END_D','31',0,''),
	('I_DV','211436',0,''),
	('GROUPE_IDCENTRE','1',0,'0 = Groupe intercentre, 1 = Groupe restreint au centre'),
	('FORFAIT','0',0,'0 = Forfait désactivé, 1 = Forfait activé'),
	('adminAccountValidation','1',0,'Est-ce que l\'administrateur doit valider chaque compte après que le mail du compte à été validé ? (0 = non; 1 = oui)'),
	('adminRecieveMailWhenAccountCreated','0',0,'Est-ce que l\'administrateur reçois un mail à la création de chaque compte ? (0 = non; 1 = oui)'),
	('annee-niveau','{\n	\"1\": \"1<sup>ère</sup> année\",\n	\"2\": \"2<sup>ème</sup> année\",\n	\"3\": \"3<sup>ème</sup> année\",\n	\"4\": \"4<sup>ème</sup> année\",\n	\"5\": \"5<sup>ème</sup> année\",\n	\"6\": \"6<sup>ème</sup> année\",\n	\"7\": \"7<sup>ème</sup> année\",\n	\"8\": \"8<sup>ème</sup> année\",\n	\"9\": \"9<sup>ème</sup> année\",\n	\"10\": \"10<sup>ème</sup> année\",\n	\"11\": \"11<sup>ème</sup> année\",\n	\"12\": \"12<sup>ème</sup> année\",\n	\"13\": \"13<sup>ème</sup> année\",\n	\"14\": \"14<sup>ème</sup> année\",\n	\"15\": \"15<sup>ème</sup> année\"\n}',0,'Nom de l\'année en fonction de son niveau'),
	('showCenter','0',0,'Est-ce que l\'on montre le choix du centre ? (0 = non; 1 = oui)'),
	('type-examen','{\r\n	\"1\": \"examen classique\",\r\n	\"2\": \"examen de rattrapage\",\r\n	\"3\": \"certificat\",\r\n	\"4\": \"certificat de rattrapage\"\r\n}',0,'Les différents types d\'examens en plus de l\'oral ou non'),
	('type-matiere','{\n	\"1\": \"Cours\",\n	\"2\": \"Indisponible\",\n	\"3\": \"Autre\"\n}',0,'Différents type possible de matières (pour EDT)'),
	('nbElemPerPage','25',0,'Nombre d\'éléments par page (pour pagination)'),
	('MAIL_DEV_MODE','',0,'Si ce champ contient quelque chose, alors les mails d\'alerte seront envoyés uniquement à cette adresse'),
	('sendWeeklyRecap','4',0,'Jour ou on envois le mail de récap hebdomadaire, 0 = aucun, 1 = Lundi, 2 = Mardi...'),
	('showCreateAccountBtn','0',0,'Est-ce que l\'on affiche le bouton \"Créer un compte\" sur la page de connexion ?'),
	('forgot_password_timeout','60',0,'Temps en min avant que le lien de mot de passe oublié envoyé par mail n\'expire'),
	('edtDebug','0',0,'Débug de l\'edt'),
	('paramGestionList','{\n \"START_Y\",\n \"END_Y\",\n \"START_M\",\n \"adminAccountValidation\",\n \"sendWeeklyRecap\",\n \"titre_etablissement\",\n \"certificat_scol_nom_directeur\",\n \"certificat_scol_titre_directeur\",\n \"periode_courante\",\n \"sendConfirmCronMail\",\n \"email_copie_mail_hebdo\",\n\"note_min_rattrapage\",\n\"note_max_rattrapage\"\n}',0,'Liste des paramètres à montrer dans l\'onglet \'Gestion\''),
	('plageHoraire','{\n	\"1\": {\n		\"lib\":\"H1\",\n		\"start\":\"09:15\",\n		\"end\":\"11:00\"\n	},\n\n	\"2\": {\n		\"lib\":\"H2\",\n		\"start\":\"11:15\",\n		\"end\":\"13:00\"\n	},\n\n	\"3\": {\n		\"lib\":\"H3\",\n		\"start\":\"14:30\",\n		\"end\":\"16:15\"\n	},\n\n	\"4\": {\n		\"lib\":\"H4\",\n		\"start\":\"16:30\",\n		\"end\":\"18:15\"\n	}\n}',0,''),
	('periodeList','{\n	\"1\": \"Sem 1\",\n	\"2\": \"Sem 2\"\n}',0,'liste des périodes pour les filtres (Semestre 1/2 ou Trimestre 1/2/3 etc...)'),
	('importCopies','1',0,'Est-ce que l\'on active la fonction d\'import des copies (activer cette fonction n\'affichera pas forcément le menu)'),
	('isCurrentlyWorking','0',0,'Est-ce que l\'import de copie est en cours de traitement ?'),
	('MAXPAGE','25',0,'nombre max de données par page de présentation'),
	('nbrAnneeAvantDiplome','5',0,'Nombre d\'années avant l\'obtention du diplôme'),
	('certificat_scol_logo','logo01_logo_text_right.png',0,'Nom de l\'image de logo affichée sur le certificat de scolarite, l\'image est dans download/logo/NOM_DU_CENTRE/xxxxx.xxx'),
	('certificat_scol_signature','signature.png',0,'Nom de l\'image de signature affichée sur le certificat de scolarite, l\'image est dans download/logo/NOM_DU_CENTRE/xxxxx.xxx'),
	('certificat_scol_nom_directeur','Albert Einstein',0,'Nom du directeur/directrice affiché sur le certificat de scolarité'),
	('certificat_scol_texte_h','Je soussigné, __DIRECTOR_NAME__, __TITRE_DIR__ de la __TITRE_SCHOOL__, certifie que __USER_NAME__, né le __USER_BIRTH__ à __USER_BIRTH_PLACE__ est inscrit dans notre établissement pour l’année universitaire __CURRENT_STUDY_YEAR__.<br><br>Pour servir et valoir ce que de droit.<br><br>Fait à __SCHOOL_CITY__, le __DATE__',0,'Texte affiché sur le certificat de scolarité, celui-ce comprendra des codes spécifiques:\r\n__DIRECTOR_NAME__ : le nom du directeur/directrice,\r\n__DATE__ : la date actuelle,\r\n__TITRE_DIR__ : le titre du directeur/directrice,\r\n__TITRE_SCHOOL__ : le titre de l\'établissement,\r\n__USER_NAME__ : le nom de l\'élève concerné,\r\n__USER_BIRTH__ : la date de naissance de l\'utilisateur concerné,\r\n__USER_BIRTH_PLACE__ : le lieu de naissance de l\'utilisateur concerné,\r\n__CURRENT_STUDY_YEAR__ : années d\'études actuelles (ex: \'2018-2019\'),\r\n__SCHOOL_CITY__ : le nom de la ville de l\'établissement,\r\n__USER_PROMOTION__ : la classe de l\'utilisateur concerné'),
	('certificat_scol_titre_directeur','Directeur',0,'Titre du directeur/directrice'),
	('titre_etablissement','Prométhée Academy',0,'Titre complet de l\'établissement'),
	('certificat_scol_show','1',0,'Est-ce que l\'on affiche le bouton pour générer le certificat de scolarité (1 = oui, 0 = non)'),
	('certificat_scol_texte_f','Je soussigné, __DIRECTOR_NAME__, __TITRE_DIR__ de la __TITRE_SCHOOL__, certifie que __USER_NAME__, née le __USER_BIRTH__ à __USER_BIRTH_PLACE__ est inscrite dans notre établissement pour l’année universitaire __CURRENT_STUDY_YEAR__.<br><br>Pour servir et valoir ce que de droit.<br><br>Fait à __SCHOOL_CITY__, le __DATE__',0,'Texte affiché sur le certificat de scolarité, celui-ce comprendra des codes spécifiques:\r\n__DIRECTOR_NAME__ : le nom du directeur/directrice,\r\n__DATE__ : la date actuelle,\r\n__TITRE_DIR__ : le titre du directeur/directrice,\r\n__TITRE_SCHOOL__ : le titre de l\'établissement,\r\n__USER_NAME__ : le nom de l\'élève concerné,\r\n__USER_BIRTH__ : la date de naissance de l\'utilisateur concerné,\r\n__USER_BIRTH_PLACE__ : le lieu de naissance de l\'utilisateur concerné,\r\n__CURRENT_STUDY_YEAR__ : années d\'études actuelles (ex: \'2018-2019\'),\r\n__SCHOOL_CITY__ : le nom de la ville de l\'établissement,\r\n__USER_PROMOTION__ : la classe de l\'utilisateur concerné'),
	('certificat_scol_texte_footer','IP-Solutions - Prométhée Solutions',0,'Texte au bas de page des certificats de scolarités'),
	('note_min_rattrapage','0',0,'Note minimum pour passer au rattrapage (incluse)'),
	('note_max_rattrapage','10',0,'Note maximum pour passer au rattrapage (exclue)'),
	('feuille_suivi_pedagogique_show','0',0,'Est-ce que l\'on affiche le bouton de feuille de suivi sur la page du cahier de texte'),
	('edt_visible_par_tous','0',0,'Est-ce que tous les emplois du temps sont visibles par tous ? (1 = oui; 2 = non)\n(un étudiant ne verra pas l\'EDT d\'un prof)'),
	('periode_courante','1',0,'Période actuelle (cf liste des périodes)'),
	('temp_mail_sent','8',0,''),
	('afficherMoyenneParPeriodeBulletin','1',0,'Est-ce que l\'on affiche la moyenne par matière par période en vue annuelle sur le bulletin ?'),
	('absence_show_bulletin','1',0,'Est-ce que l\'on montre les absences sur les bulletins'),
	('bulletin_footer','IP-Solutions - Prométhée Solutions',0,'Texte du pied de page des bulletins'),
	('bulletin_logo','logo01_logo_text_right_bulletin.png',0,'Nom du fichier image du logo du bulletin'),
	('periodeDates','{\"1_start\":\"2020-09-01\",\"1_end\":\"2020-12-31\",\"2_start\":\"2021-01-01\",\"2_end\":\"2021-06-30\"}',0,'Liste des dates définissant les différentes périodes'),
	('sendConfirmCronMail','',0,'Email auquel on envois la confirmation d\'envoi des crons avec le détail'),
	('autoTellTeacherAndStudents','0',0,'Lors de la création d\'un évènement sur l\'EDT, cocher automatiquement la case \'Prévenir l\'intervenant\' et \'Prévenir les étudiants\''),
	('autoSendConfirmMailDaily','0',0,'Envoyer quotidiennement les mails de demande de confirmation de cours (0 = non; 1 = oui)'),
	('email_copie_mail_hebdo','',0,'Mail en copie des mails hebdomadaires'),
	('student_see_pending_lessons','0',0,'Est-ce que les étudiants voient les cours en attente (0 = non, 1 = oui)'),
	('can_teachers_modify_grades','0',0,'Est-ce que les intervenants peuvent renseigner/modifier les notes (0 = non, 1 = oui)'),
	('periodeFilterDefaultEdt','1',0,'Est-ce que l\'on doit utiliser la date de fin de période pour le filtre de l\'EDT en mode liste (1 = oui, 0 = non)'),
	('certificat_study_year','Année universitaire',0,''),
	('certificat_scolarship_bill','Reçu frais de scolarité',0,''),
	('certificat_nom_tresorier','M.Elon Musk',0,''),
	('certificat_image_bill_signature','signature_tresorier.png',0,''),
	('afficherDetailNoteParPeriodeBulletin','1',0,'Est-ce que l\'on affiche le détail des notes par matière par période en vue annuelle sur le bulletin ?'),
	('afficherCompteurNotesMatBulletin','1',0,'Est-ce que l\'on affiche le compteur de notes pour chaque matière sur le bulletin ?'),
	('afficherMoyenneClasseBulletin','1',0,'Est-ce que l\'on affiche la moyenne de la classe sur le bulletin ?'),
	('afficherCheckOuTextRattrapageBulletin','text',0,'Est-ce que l\'on affiche un check ou du texte pour indiquer si rattrapage sur le bulletin ? (check/text/valide).'),
	('afficherSeparationPolesBulletin','1',0,'Est-ce que l\'on affiche les poles par groupe sur le bulletin ?'),
	('afficherProfsBulletin','1',0,'Est-ce que l\'on affiche les profs des matières sur le bulletin ?'),
	('afficherDetailNoteAllPeriodeBulletin','0',0,'Est-ce que l\'on affiche le détail des notes en vue toute les périodes ?'),
	('afficherAppreciationBulletin','1',0,'Est-ce que l\'on affiche la case apréciation sur le remplissage des bulletins ?'),
	('afficherHeureStageQue1AnneeBulletin','0',0,'Est-ce que l\'on affiche la case heure de stage (uniquement pour les 1ère années sur le remplissage des bulletins ?'),
	('afficherQueLaPeriodeTotaleBulletin','0',0,'Est-ce que l\'on affiche que la période totale sur les bulletins'),
	('afficherTypeNoteBulletin','1',0,'Est-ce que l\'on affiche le type de note sur le bulletin (et l\'impression) ?'),
	('texteLabelColonneNoteRattrapage','Note rattrapage',0,'Libellé de la colonne de note de rattrapage'),
	('afficherNotesMoinsDeDixEnRougeBulletin','0',0,'Est-ce que l\'on affiche les notes < 10 en rouge sur le bulletin'),
	('texteLabelColonneRattrapage','Rattrapage',0,''),
	('afficherNomPolesBleuBulletin','0',0,'Est-ce que l\'on affiche le nom des poles en bleu sur le bulletin'),
	('activateForceValidateMatPoleFunction','0',0,'Est-ce que l\'on active la possibilité de forcer la validation d\'une matière/pole sur le bulletin'),
	('addSpaceTopTableBulletinExport','0',0,'Est-ce que l\'on ajoute un espace au dessus du tableau des notes sur l\'export de l\'EDT'),
	('addSpaceBottomHeaderBulletinExport','1',0,'Est-ce que l\'on ajoute un espace en dessous de l\'en-tête de l\'export des bulletins'),
	('intituleRemarqueBulletin','Conseil de classe',0,'Intitulé affiché pour la case de remarques sur le bulletin'),
	('afficherMoyennePolesBleuBulletin','0',0,'Est-ce que la moyenne des pôles est en bleue sur les bulletins ?'),
	('afficherGrisLigneMoyenneBulletin','0',0,'Est-ce que l\'on affiche la ligne de moyenne en gris sur les bulletins ?'),
	('afficherGrisColonnesEleveBulletin','1',0,'Est-ce que l\'on affiche en grisé les colonnes qui concernent l\'élève ?'),
	('centreCity','1',0,'1 = Lyon, 2 = Paris, 3 = Bretagne'),
	('canValidateSignatureBulletin','1',0,'Est-ce que l\'on peux valider un bulletin (boîte à cocher) et on ajoute la signature dessus ?'),
	('bulletinSignature','signature_bulletins.png',0,'Nom du fichier image contenant la signature'),
	('afficherRattrapagePolesBulletin','0',0,'Est-ce que l\'on affiche la vérification de rattrapage par pôles sur le bulletin ?'),
	('afficherOneLetterProfNamePrintBulletin','1',0,'Est-ce que l\'on affiche qu\'une lettre du prénom du prof sur l\'impression du bulletin ?'),
	('lockNewAccountValidationAdmin','0',0,'Est-ce que l\'on bloque la possibilité à l\'admin de valider un nouveau compte ?'),
	('affichageProfsPointsSuspensionBulletin','1',0,'Est-ce que l\'on affiche des \'...\' après les noms des profs quand il y en a plusieurs mais un seul d\'affiché ?'),
	('tailleAffichageNomProfsBulletin','11',0,'Quelle taille utiliser pour afficher le nom des profs sur le bulletin (pas impression)'),
	('calculMoyenneGeneraleNotesOuPolesBulletin','notes',0,'Est-ce que l\'on doit utiliser les notes pour calculer la moyenne générale d\'un élève ou bien la moyenne des pôles ? (\'notes\' ou \'poles\')'),
	('showOnlyTotalPeriodGeneral','0',0,'Est-ce que l\'on affiche qu\'une période partout dans l\'interface ou est-ce que l\'on précise les valeurs pour chaque période (1 = seulement période totale, 0 = toutes les périodes) (Attention: ce param ne supprime pas la gestion des périodes mais simplement la vue de celles-ci, faites attention à définir les dates de la période 1 comme étant toute l\'année et ne changez pas ce paramètre en cours d\'année!!!)'),
	('modeAbsentTableBulletin','2',0,'Le mode d\'affichage voulu: 1: Détail des absences, 2: Nb de journée/demi-journée d\'absence (justifiées et injustifiées)'),
	('modeCertificatTableBulletin','2',0,'Le mode d\'affichage voulu: 1: affichage vertical, 2: affichage horizontal'),
	('modePoleAndHistoryTableBulletin','2',0,'Le mode d\'affichage voulu: 1: affichage horizontal, 2: affichage vertical'),
	('afficherSur20MoyenneBulletin','0',0,'Est-ce que l\'on affiche le \'/20\' sur la moyenne des bulletins'),
	('forceWidthColonneBulletin','0',0,'Est-ce que l\'on force la largeur des colonnes Matières et Notes sur le bulletin ?'),
	('dateBasculeAnnee','01/09/2021',0,'Date de la basculle d\'une année à l\'autre (format: JJ/MM/YYYY)'),
	('showLinkToSiteMail','1',0,'Est-ce que l\'on montre le lien vers l\'ent en fin de mail ?'),
	('pendingLessonsMailSubject','[Prométhée] Cours en attente',0,'Sujet des mails envoyés pour rappel de cours en attente'),
	('liste_rattrapage_show_mat','0',0,'Est-ce que l\'on affiche le bouton \"Matière\" sur la liste des étudiants en rattrapage'),
	('liste_rattrapage_show_exam','1',0,'Est-ce que l\'on affiche le bouton \"Examen\" sur la liste des étudiants en rattrapage'),
	('showPrevNextButtonPagination','1',0,'Est-ce que l\'on affiche les boutons \'Précédents\' et \'Suivants\' dans les paginations ?'),
	('showAnneeInPMASelectList','0',0,'Est-ce que l\'on montre l\'année à chaque éléments dans la liste déroulante pour sélectionner le pma ?'),
	('showReloadButtonEDT','1',0,'Est-ce que l\'on affiche le bouton d\'actualisation sur l\'EDT ?'),
	('mobile_edt_absence_gestion','0',0,'Est-ce que l\'on peux gérer les absences sur la version mobile ?'),
	('mobile_edt_cours_gestion','1',0,'Est-ce que l\'on peux gérer les cours sur la version mobile ?'),
	('postit_list_show_acquitement','1',0,'Est-ce que l\'on affiche la date d\'acquitement du message dans la liste des messages'),
	('certificat_scol_show_titre_etablissement_before_signature','1',0,'Est-ce que l\'on remet le nom de l\'établissement au dessus de la signature sur le certificat de scolarité ?'),
	('afficherExamensDansBulletin','1',0,'Est-ce que l\'on affiche les examens (type UV) dans le bulletin ?'),
	('postit_can_write_to_myself','1',0,'Est-ce que j\'ai le droit de m\'écrire un message à moi-même ?'),
	('afficherHeuresSalonBulletin','1',0,'Est-ce que l\'on affiche le champ du nombre d\'heures de portes ouvertes/salons sur le bulletin ?'),
	('afficherRadioPassageRedoublementBulletin','1',0,'Est-ce que l\'on affiche les boutons radio pour passer à l\'année suppérieure ou redoubler ?'),
	('libelleRattrapageListeExamens','{\"normal\":\"Examen initial\",\"rattrapage\":\"Rattrapage\"}',0,'Quel est le libelle des rattrapage dans le select des examens (Rattrapages/2nd session...)'),
	('numberDaysWeekViewEDT','6',0,'Nombre de jours à afficher en vue semaine sur l\'EDT'),
	('afficherBoutonFeuilleEmargement','0',0,'Est-ce que l\'on affiche le bouton pour obtenir la feuille d\'émargement ?'),
	('showYearIntitule','1',0,'Est-ce que l\'on affiche les intitulés d\'année (1ère année, 2ème année...) ?'),
	('show_demo_idents_login','0',0,'Est-ce que l\'on affiche les identifiants par défaut sur la page de connexion (mode démo)'),
	('show_default_admin_login','1',0,'Est-ce que l\'on affiche les identifiants par défaut du compte admin sur la page de connexion (zip)');

/*!40000 ALTER TABLE `parametre` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table parametre_archive
# ------------------------------------------------------------

DROP TABLE IF EXISTS `parametre_archive`;

CREATE TABLE `parametre_archive` (
  `_annee` int(11) NOT NULL,
  `_code` varchar(200) NOT NULL,
  `_valeur` longtext NOT NULL,
  `_IDuser` int(10) NOT NULL,
  `_comm` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Affichage de la table pfolio
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pfolio`;

CREATE TABLE `pfolio` (
  `_IDskill` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDuv` int(10) unsigned NOT NULL,
  `_order` int(10) unsigned NOT NULL,
  `_ident` varchar(255) NOT NULL,
  `_min` int(10) unsigned NOT NULL DEFAULT '50',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDskill`),
  UNIQUE KEY `_IDuv` (`_IDuv`,`_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des domaines de compétences';

LOCK TABLES `pfolio` WRITE;
/*!40000 ALTER TABLE `pfolio` DISABLE KEYS */;

INSERT INTO `pfolio` (`_IDskill`, `_IDuv`, `_order`, `_ident`, `_min`, `_visible`)
VALUES
	(1,1,1,'S\'approprier un environnement informatique de travail',50,'O'),
	(2,1,2,'Adopter une attitude responsable',50,'O'),
	(3,1,3,'Créer, produire, traiter, exploiterdes données',50,'O'),
	(4,1,4,'S\'informer, se documenter',50,'O'),
	(5,1,5,'Communiquer, échanger',50,'O'),
	(6,2,1,'S\'approprier un environnement informatique de travail',50,'O'),
	(7,2,2,'Adopter une attitude responsable',50,'O'),
	(8,2,3,'Créer, produire, traiter, exploiterdes données',50,'O'),
	(9,2,4,'S\'informer, se documenter',50,'O'),
	(10,2,5,'Communiquer, échanger',50,'O'),
	(11,3,1,'S\'approprier un environnement informatique de travail',50,'O'),
	(12,3,2,'Adopter une attitude responsable',50,'O'),
	(13,3,3,'Créer, produire, traiter, exploiter des données',50,'O'),
	(14,3,4,'S\'informer, se documenter',50,'O'),
	(15,3,5,'Communiquer, échanger',50,'O'),
	(16,4,1,'Compétence A1 : Tenir compte du caractère évolutif des TIC',50,'O'),
	(17,4,2,'Compétence A2 : Intégrer la dimension éthique et le respect de la déontologie',50,'O'),
	(18,4,3,'Compétence B1 : S\'approprier son environnement de travail',50,'O'),
	(19,4,4,'Compétence B2 : Rechercher l\'information',50,'O'),
	(20,4,5,'Compétence B3 : Sauvegarder, sécuriser, archiver ses données en local et en réseau  filaire ou sans fil',50,'O'),
	(21,4,6,'Compétence B4 : Réaliser des documents destinés à être imprimés',50,'O'),
	(22,4,7,'Compétence B5 : Réaliser la présentation de ses travaux en présentiel et en ligne',50,'O'),
	(23,4,8,'Compétence B6 : Échanger et communiquer à distance',50,'O'),
	(24,4,9,'Compétence B7 : Mener des projets en travail collaboratif à distance',50,'O'),
	(25,5,1,'Compétence A1 : Maîtrise de l\'environnement numérique professionnel',50,'O'),
	(26,5,2,'Compétence A2 : Développement des compétences pour la formation tout au long de la vie',50,'O'),
	(27,5,3,'Compétence A3 : Responsabilité professionnelle dans le cadre du système éducatif',50,'O'),
	(28,5,4,'Compétence B1 : Travail en réseau avec l\'utilisation des outils de travail collaboratif',50,'O'),
	(29,5,5,'Compétence B2 : Conception et préparation de contenus d\'enseignement et de situations d\'apprentissage',50,'O'),
	(30,5,6,'Compétence B3 : Mise en oeuvre pédagogique en présentiel et à distance',50,'O'),
	(31,5,7,'Compétence B4 : Mise en œuvre de démarches d\'évaluation',50,'O'),
	(32,6,1,'Compétence A : Problématiques et enjeux liés aux TIC dans les activités juridiques et judiciaires',50,'O'),
	(33,6,2,'Compétence B1 : La recherche et l\'utilisation des ressources d\'information et de documentation juridique',50,'O'),
	(34,6,3,'Compétence B2 : Sécurité',50,'O'),
	(35,6,4,'Compétence B3 : Responsabilité professionnelle liée aux activités numériques',50,'O'),
	(36,6,5,'Compétence B4 : Le travail collaboratif en réseau',50,'O'),
	(37,6,6,'Compétence B5 : Les échanges numériques entre acteurs judiciaires ou juridiques et services offerts aux citoyens',50,'O'),
	(38,6,7,'Compétence B6 : Traitement de l\'information juridique',50,'O'),
	(39,7,1,'Compétence A1 : L’information en santé, documentation',50,'O'),
	(40,7,2,'Compétence A2 : L’information en santé, juridique',50,'O'),
	(41,7,3,'Compétence A3 : L’information en santé, sécurité',50,'O'),
	(42,7,4,'Compétence B1 : Travail collaboratif en santé',50,'O'),
	(43,7,5,'Compétence B2 : Systèmes d’information',50,'O'),
	(44,8,1,'Compétence A1 : Problématique et enjeux liés aux aspects juridiques en contexte professionnel',50,'O'),
	(45,8,2,'Compétence A2 : La sécurité de l’information et des systèmes d’information',50,'O'),
	(46,8,3,'Compétence B1 : Standards, normes techniques et interopérabilité',50,'O'),
	(47,8,4,'Compétence B2 : Environnement numérique et ingénierie collaborative',50,'O'),
	(48,9,1,'Compétence D1 : Environnement informatique',50,'O'),
	(49,9,2,'Compétence D2 : Attitude citoyenne',50,'O'),
	(50,9,3,'Compétence D3 : Traitement et Production',50,'O'),
	(51,9,4,'Compétence D4 : Recherche de l’information',50,'O'),
	(52,9,5,'Compétence D5 : Communication',50,'O'),
	(53,10,1,'Compétence A1 : Connaître et utiliser un équipement informatique et ses logiciels',50,'O'),
	(54,10,2,'Compétence A2 : Naviguer sur Internet',50,'O'),
	(55,10,3,'Compétence A3 : Communiquer avec Internet',50,'O'),
	(56,10,4,'Compétence A4 : Créer et exploiter un document numérique',50,'O'),
	(57,10,5,'Compétence A5 : Connaître les règles élémentaires du droit et du bon usage sur Internet',50,'O'),
	(58,11,1,'Compétence M1 : Bases de l\'ordinateur et d\'Internet',50,'O'),
	(59,11,2,'Compétence M2 : Traitement de texte et Tableur',50,'O'),
	(60,11,3,'Compétence M3 : Messagerie et forum',50,'O'),
	(61,11,4,'Compétence M4 : Navigation sur la Toile',50,'O'),
	(62,11,5,'Compétence M5 : Sécurité des systèmes d’information',50,'O'),
	(63,11,6,'Compétence M6 : Protection des données personnelles et environnement juridiques',50,'O'),
	(64,11,7,'Compétence M7 : Administration électronique',50,'O'),
	(65,12,1,'Compétence M1 : Publication sur le Web',50,'O'),
	(66,12,2,'Compétence M2 : Messagerie et forum',50,'O'),
	(67,12,3,'Compétence M3 : Navigation sur la Toile',50,'O'),
	(68,12,4,'Compétence M4 : Sécurité des systèmes d’information',50,'O'),
	(69,12,5,'Compétence M5 : Protection des données personnelles et environnement juridique',50,'O'),
	(70,12,6,'Compétence M6 : Administration électronique',50,'O'),
	(71,13,1,'Compétence A1 : Naviguer sur Internet',50,'O'),
	(72,13,2,'Compétence A2 : Communiquer avec Internet',50,'O'),
	(73,13,3,'Compétence A3 : Rechercher sur Internet',50,'O');

/*!40000 ALTER TABLE `pfolio` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table pfolio_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pfolio_data`;

CREATE TABLE `pfolio_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDskill` int(10) unsigned NOT NULL,
  `_order` int(10) unsigned NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_option` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDdata`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des intitulés des compétences';



# Affichage de la table pfolio_eval
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pfolio_eval`;

CREATE TABLE `pfolio_eval` (
  `_IDeval` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(80) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDeval`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des modalités de positionnement';



# Affichage de la table pfolio_formation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pfolio_formation`;

CREATE TABLE `pfolio_formation` (
  `_IDform` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(255) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDform`,`_lang`),
  UNIQUE KEY `_ident` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des formations';



# Affichage de la table pfolio_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pfolio_items`;

CREATE TABLE `pfolio_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IDlevel` int(10) unsigned NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL,
  `_IDmat` int(10) unsigned NOT NULL,
  `_IP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table du portefeuille de compétences';



# Affichage de la table pfolio_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pfolio_level`;

CREATE TABLE `pfolio_level` (
  `_IDlevel` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDeval` int(10) unsigned NOT NULL DEFAULT '0',
  `_color` varchar(7) NOT NULL,
  `_ident` varchar(10) NOT NULL,
  `_texte` varchar(255) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDlevel`,`_lang`),
  UNIQUE KEY `_IDlevel` (`_IDlevel`,`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des niveaux de positionnement des compétences';



# Affichage de la table pfolio_uv
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pfolio_uv`;

CREATE TABLE `pfolio_uv` (
  `_IDuv` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDform` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDmat` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_ident` varchar(255) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_IDgrp` text NOT NULL,
  `_IDeval` int(10) unsigned NOT NULL DEFAULT '1',
  `_color` enum('O','N') NOT NULL DEFAULT 'N',
  `_scale` enum('O','N') NOT NULL DEFAULT 'N',
  `_autoeval` enum('O','N') NOT NULL DEFAULT 'N',
  `_min` int(10) unsigned NOT NULL DEFAULT '50',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDuv`),
  UNIQUE KEY `_IDform` (`_IDform`,`_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des Unités de Valeur';



# Affichage de la table pole
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pole`;

CREATE TABLE `pole` (
  `_ID` int(11) NOT NULL AUTO_INCREMENT,
  `_name` text NOT NULL,
  `_lang` text NOT NULL,
  `_attr` text NOT NULL,
  `_visible` text NOT NULL COMMENT 'N = non; O = oui',
  PRIMARY KEY (`_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `pole` WRITE;
/*!40000 ALTER TABLE `pole` DISABLE KEYS */;

INSERT INTO `pole` (`_ID`, `_name`, `_lang`, `_attr`, `_visible`)
VALUES
	(1,'Commerce','fr','','N'),
	(2,'Gestion','fr','','N'),
	(3,'Langue','fr','','N');

/*!40000 ALTER TABLE `pole` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table pole_mat_annee
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pole_mat_annee`;

CREATE TABLE `pole_mat_annee` (
  `_ID_pma` int(11) NOT NULL AUTO_INCREMENT,
  `_ID_year` int(11) NOT NULL,
  `_ID_pole` int(11) NOT NULL,
  `_ID_matiere` int(11) unsigned DEFAULT NULL,
  `_pma_coef` int(11) NOT NULL,
  PRIMARY KEY (`_ID_pma`),
  KEY `ID_pole INDEX` (`_ID_year`),
  KEY `ID_pole to pole` (`_ID_pole`),
  KEY `ID_matiere INDEX` (`_ID_matiere`),
  CONSTRAINT `ID_pole to pole` FOREIGN KEY (`_ID_pole`) REFERENCES `pole` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pole_mat_annee ID_matiere to campus_data IDmat` FOREIGN KEY (`_ID_matiere`) REFERENCES `campus_data` (`_IDmat`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `pole_mat_annee` WRITE;
/*!40000 ALTER TABLE `pole_mat_annee` DISABLE KEYS */;

INSERT INTO `pole_mat_annee` (`_ID_pma`, `_ID_year`, `_ID_pole`, `_ID_matiere`, `_pma_coef`)
VALUES
	(203,1,1,6,0),
	(205,2,1,6,0),
	(207,3,1,6,0),
	(209,4,1,6,0),
	(211,5,1,6,0),
	(217,1,2,5,0),
	(218,2,2,5,0),
	(219,3,2,5,0),
	(220,4,2,5,0),
	(221,5,2,5,0),
	(222,1,3,6,0),
	(224,2,3,6,0),
	(226,3,3,6,0),
	(228,4,3,6,0),
	(230,5,3,6,0);

/*!40000 ALTER TABLE `pole_mat_annee` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table postit
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postit`;

CREATE TABLE `postit` (
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrppj` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `_key` (`_IDcentre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la gestion des messages instantannés';



# Affichage de la table postit_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postit_address`;

CREATE TABLE `postit_address` (
  `_IDlidi` int(10) unsigned NOT NULL,
  `_ID` varchar(200) NOT NULL,
  `_type` varchar(200) NOT NULL,
  KEY `IDlidi INDEX` (`_IDlidi`),
  CONSTRAINT `IDlidi to postit_lidi` FOREIGN KEY (`_IDlidi`) REFERENCES `postit_lidi` (`_IDlidi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des carnets d''adresses de la messagerie instantannée';



# Affichage de la table postit_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postit_data`;

CREATE TABLE `postit_data` (
  `_IDdata` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_IDparent` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_ident` varchar(40) NOT NULL,
  PRIMARY KEY (`_IDdata`),
  UNIQUE KEY `_key` (`_IDparent`,`_ident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des répertoires des post-it';



# Affichage de la table postit_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postit_items`;

CREATE TABLE `postit_items` (
  `_IDpost` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(11) unsigned DEFAULT '0',
  `_IDdst` int(11) NOT NULL,
  `_IDexp` int(10) unsigned NOT NULL,
  `_IP` text NOT NULL,
  `_date` datetime NOT NULL,
  `_type` int(10) unsigned NOT NULL DEFAULT '1',
  `_titre` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_priority` int(10) unsigned NOT NULL DEFAULT '0',
  `_sign` enum('O','N') NOT NULL DEFAULT 'N',
  `_vu` datetime DEFAULT NULL,
  `_ack` datetime DEFAULT NULL,
  `_deldst` enum('O','N') NOT NULL DEFAULT 'N',
  `_delexp` enum('O','N') NOT NULL DEFAULT 'N',
  `_timer` datetime NOT NULL,
  PRIMARY KEY (`_IDpost`),
  KEY `IDdata index` (`_IDdata`),
  CONSTRAINT `IDdata to postit_data` FOREIGN KEY (`_IDdata`) REFERENCES `postit_data` (`_IDdata`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des messages instantannés';



# Affichage de la table postit_lidi
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postit_lidi`;

CREATE TABLE `postit_lidi` (
  `_IDlidi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_nom` varchar(40) NOT NULL,
  `_AR` enum('O','N') NOT NULL DEFAULT 'N',
  `_public` enum('O','N','M') NOT NULL DEFAULT 'N',
  `_email` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`_IDlidi`),
  UNIQUE KEY `_key` (`_ID`,`_nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des listes de diffusion de la messagerie instantannée';



# Affichage de la table postit_pj
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postit_pj`;

CREATE TABLE `postit_pj` (
  `_IDpj` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDpost` int(10) unsigned NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_ext` varchar(5) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_hit` datetime NOT NULL,
  PRIMARY KEY (`_IDpj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des PJ de la messagerie instantannée';



# Affichage de la table postit_quotas
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postit_quotas`;

CREATE TABLE `postit_quotas` (
  `_IDuser` int(11) NOT NULL,
  `_maxsize` bigint(20) unsigned NOT NULL DEFAULT '1024000',
  `_size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `_delay` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `_key` (`_IDuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la gestion des quotas';



# Affichage de la table reservation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reservation`;

CREATE TABLE `reservation` (
  `_IDres` int(10) unsigned NOT NULL,
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_titre` varchar(30) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_email` enum('-','P','E') NOT NULL DEFAULT '-',
  `_autoval` enum('O','N') NOT NULL DEFAULT 'O',
  `_weekly` enum('O','N') NOT NULL DEFAULT 'O',
  `_IDweek` int(10) unsigned NOT NULL DEFAULT '0',
  `_horaire` tinytext NOT NULL,
  `_maximize` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDres`,`_IDcentre`,`_lang`),
  UNIQUE KEY `_key` (`_IDcentre`,`_titre`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table du menu des réservations';



# Affichage de la table reservation_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reservation_data`;

CREATE TABLE `reservation_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcentre` text NOT NULL,
  `_IDres` int(10) unsigned NOT NULL,
  `_titre` varchar(30) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_ident` varchar(30) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDdata`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des articles des réservations';



# Affichage de la table reservation_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reservation_items`;

CREATE TABLE `reservation_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_parent` int(10) unsigned NOT NULL DEFAULT '0',
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_update` datetime DEFAULT NULL,
  `_erase` datetime DEFAULT NULL,
  `_valid` datetime DEFAULT NULL,
  `_priority` enum('H','B') NOT NULL DEFAULT 'B',
  `_start` datetime DEFAULT NULL,
  `_end` datetime DEFAULT NULL,
  `_comment` tinytext NOT NULL,
  `_note` tinytext NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des dates de réservation';



# Affichage de la table reset
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reset`;

CREATE TABLE `reset` (
  `_IDitem` int(10) unsigned NOT NULL,
  `_table` mediumtext NOT NULL,
  UNIQUE KEY `_key` (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des tables à vider';



# Affichage de la table reset_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reset_log`;

CREATE TABLE `reset_log` (
  `_IDitem` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime DEFAULT NULL,
  `_IDcentre` int(10) unsigned NOT NULL,
  `_numrows` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `_key` (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des logs des tables à vider';



# Affichage de la table resource
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource`;

CREATE TABLE `resource` (
  `_IDres` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_titre` varchar(30) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_IDlicense` int(10) unsigned NOT NULL DEFAULT '11',
  `_color` enum('O','N') NOT NULL DEFAULT 'N',
  `_internal` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDres`,`_lang`),
  UNIQUE KEY `_key` (`_titre`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des ressources';



# Affichage de la table resource_bag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_bag`;

CREATE TABLE `resource_bag` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_file` varchar(255) NOT NULL,
  PRIMARY KEY (`_IDitem`),
  UNIQUE KEY `_key` (`_ID`,`_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table du cartable électronique';



# Affichage de la table resource_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_data`;

CREATE TABLE `resource_data` (
  `_IDcat` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDres` int(10) unsigned NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_nom` varchar(30) NOT NULL,
  `_texte` tinytext NOT NULL,
  `_email` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `_lock` int(10) unsigned NOT NULL DEFAULT '1',
  `_rss` enum('O','N') NOT NULL DEFAULT 'N',
  `_PJ` int(10) unsigned NOT NULL DEFAULT '1',
  `_share` enum('O','N') NOT NULL DEFAULT 'O',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDcat`),
  UNIQUE KEY `_key` (`_IDres`,`_nom`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des catégories de ressources';



# Affichage de la table resource_function
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_function`;

CREATE TABLE `resource_function` (
  `_IDfunc` int(10) unsigned NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDfunc`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des types de ressources';



# Affichage de la table resource_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_items`;

CREATE TABLE `resource_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDroot` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgroup` int(11) NOT NULL DEFAULT '0',
  `_IDlicense` int(10) unsigned NOT NULL DEFAULT '1',
  `_date` datetime NOT NULL,
  `_modify` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IDgrprd` text NOT NULL,
  `_IDcentre` text NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_IDcat` int(10) unsigned NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_file` varchar(80) NOT NULL,
  `_size` int(10) unsigned NOT NULL,
  `_texte` tinytext NOT NULL,
  `_note` mediumtext NOT NULL,
  `_ver` varchar(10) NOT NULL DEFAULT '1.0',
  `_count` int(10) unsigned NOT NULL DEFAULT '0',
  `_level` int(11) NOT NULL DEFAULT '0',
  `_valid` datetime NOT NULL,
  `_comment` enum('O','N') NOT NULL DEFAULT 'O',
  `_lock` int(11) NOT NULL DEFAULT '0',
  `_usability` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des articles des ressources';



# Affichage de la table resource_license
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_license`;

CREATE TABLE `resource_license` (
  `_IDlicense` int(10) unsigned NOT NULL,
  `_titre` varchar(20) NOT NULL,
  `_texte` varchar(80) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDlicense`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des licences des ressources';



# Affichage de la table resource_online
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_online`;

CREATE TABLE `resource_online` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDlicense` int(10) unsigned NOT NULL DEFAULT '1',
  `_date` datetime NOT NULL,
  `_update` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_IDgrprd` text NOT NULL,
  `_IDtype` text NOT NULL,
  `_IDcat` text NOT NULL,
  `_IDfunc` text NOT NULL,
  `_title` varchar(80) NOT NULL,
  `_texte` mediumtext NOT NULL,
  `_author` tinytext NOT NULL,
  `_tags` tinytext NOT NULL,
  `_url` varchar(128) NOT NULL,
  `_lang` varchar(6) NOT NULL,
  `_break` enum('O','N') NOT NULL DEFAULT 'N',
  `_count` int(10) unsigned NOT NULL DEFAULT '0',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des articles des ressources';



# Affichage de la table resource_post
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_post`;

CREATE TABLE `resource_post` (
  `_IDmsg` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDitem` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_texte` mediumtext NOT NULL,
  PRIMARY KEY (`_IDmsg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des commentaires des ressources';



# Affichage de la table resource_root
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_root`;

CREATE TABLE `resource_root` (
  `_IDroot` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDparent` int(10) unsigned NOT NULL,
  `_IDcentre` int(11) NOT NULL DEFAULT '1',
  `_IDgroup` int(11) NOT NULL DEFAULT '0',
  `_IDcat` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_titre` varchar(40) NOT NULL,
  `_IDmod` int(10) unsigned NOT NULL,
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_private` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDroot`),
  UNIQUE KEY `_key` (`_IDparent`,`_IDcat`,`_IDgroup`,`_titre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des répertoires des ressources';



# Affichage de la table resource_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource_type`;

CREATE TABLE `resource_type` (
  `_IDtype` int(10) unsigned NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDtype`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des types de ressources';



# Affichage de la table retenu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `retenu`;

CREATE TABLE `retenu` (
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDmod` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrpwr` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDgrprd` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDflash` int(10) unsigned NOT NULL DEFAULT '0',
  `_start` varchar(10) NOT NULL DEFAULT '13h30',
  `_template` varchar(20) NOT NULL,
  PRIMARY KEY (`_IDcentre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la gestion des consignes';

LOCK TABLES `retenu` WRITE;
/*!40000 ALTER TABLE `retenu` DISABLE KEYS */;

INSERT INTO `retenu` (`_IDcentre`, `_IDmod`, `_IDgrpwr`, `_IDgrprd`, `_IDflash`, `_start`, `_template`)
VALUES
	(1,0,30,30,0,'13h30','consigne.html');

/*!40000 ALTER TABLE `retenu` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table retenu_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `retenu_data`;

CREATE TABLE `retenu_data` (
  `_IDdata` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDcentre` int(10) unsigned NOT NULL,
  `_IDeleve` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_motif` mediumtext NOT NULL,
  `_devoir` mediumtext NOT NULL,
  `_delay` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDroom` int(10) unsigned NOT NULL DEFAULT '0',
  `_todo` datetime NOT NULL,
  `_IDsalle` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDdata`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des consignes';

LOCK TABLES `retenu_data` WRITE;
/*!40000 ALTER TABLE `retenu_data` DISABLE KEYS */;

INSERT INTO `retenu_data` (`_IDdata`, `_IDcentre`, `_IDeleve`, `_ID`, `_IP`, `_date`, `_motif`, `_devoir`, `_delay`, `_IDroom`, `_todo`, `_IDsalle`)
VALUES
	(1,1,14,9,-4,'2013-09-09 16:49:28','le motif','taff à faire',1,6,'0000-00-00 00:00:00',0);

/*!40000 ALTER TABLE `retenu_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table retenu_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `retenu_items`;

CREATE TABLE `retenu_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDdata` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_date` datetime NOT NULL,
  `_comment` mediumtext NOT NULL,
  `_email` datetime NOT NULL,
  `_status` enum('0','1','2','3') NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des pointages de consigne';



# Affichage de la table rss
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rss`;

CREATE TABLE `rss` (
  `_IDflux` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_title` tinytext NOT NULL,
  `_url` tinytext NOT NULL,
  `_text` mediumtext NOT NULL,
  `_admin` varchar(80) NOT NULL,
  `_category` varchar(80) NOT NULL,
  `_date` datetime NOT NULL,
  `_ttl` int(10) unsigned NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDflux`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des flux rss';



# Affichage de la table rss_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rss_items`;

CREATE TABLE `rss_items` (
  `_IDitem` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDflux` int(10) unsigned NOT NULL,
  `_title` tinytext NOT NULL,
  `_url` tinytext NOT NULL,
  `_text` mediumtext NOT NULL,
  `_author` varchar(80) NOT NULL,
  `_category` varchar(80) NOT NULL,
  `_date` datetime NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDitem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des articles des flux rss';

LOCK TABLES `rss_items` WRITE;
/*!40000 ALTER TABLE `rss_items` DISABLE KEYS */;

INSERT INTO `rss_items` (`_IDitem`, `_IDflux`, `_title`, `_url`, `_text`, `_author`, `_category`, `_date`, `_lang`)
VALUES
	(6,0,'Pré rentrée','','<p>Nous vous attendons&nbsp;pour la pr&eacute; rentr&eacute;e qui se d&eacute;roulera le mardi 17/09/2019 &agrave; partir de 9h15.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>','Pallot, Sylvain','FIL d\'information','2019-09-11 11:32:32','fr');

/*!40000 ALTER TABLE `rss_items` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table rubrique
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rubrique`;

CREATE TABLE `rubrique` (
  `_IDrubrique` int(10) NOT NULL,
  `_libelle` varchar(100) NOT NULL,
  `_type` varchar(100) NOT NULL,
  `_attr` longtext NOT NULL,
  `_table` varchar(100) NOT NULL,
  `_lang` varchar(5) NOT NULL,
  `_ordre` int(10) NOT NULL,
  UNIQUE KEY `_IDrubrique` (`_IDrubrique`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `rubrique` WRITE;
/*!40000 ALTER TABLE `rubrique` DISABLE KEYS */;

INSERT INTO `rubrique` (`_IDrubrique`, `_libelle`, `_type`, `_attr`, `_table`, `_lang`, `_ordre`)
VALUES
	(1,'Cours particulier','liste','','user','FR',0),
	(2,'Numéro INE','chaine','','user_eleve','FR',0),
	(15,'Diplôme','chaine','','user_new_formateur','FR',0),
	(4,'Adresse études','chaine','','user_new_eleve','FR',0),
	(5,'Code postal études','chaine','','user_new_eleve','FR',0),
	(6,'Ville études','chaine','','user_new_eleve','FR',0),
	(7,'Lieu de naissance','chaine','','user_new','FR',0),
	(8,'Année d\'entrée','chaine','','user_eleve','FR',0),
	(10,'Code barre','chaine','','user_eleve','FR',0),
	(11,'Prix scolarité','chaine','','user_eleve','FR',0),
	(12,'Mode de règlement','chaine','','user_eleve','FR',0),
	(13,'Commentaire','chaine','','user','FR',0),
	(14,'Type de compte','liste','','user_new_type','FR',0);

/*!40000 ALTER TABLE `rubrique` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table rubrique_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rubrique_data`;

CREATE TABLE `rubrique_data` (
  `_IDrubrique` int(10) NOT NULL,
  `_IDdata` int(10) NOT NULL,
  `_valeur` longtext NOT NULL,
  PRIMARY KEY (`_IDrubrique`,`_IDdata`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `rubrique_data` WRITE;
/*!40000 ALTER TABLE `rubrique_data` DISABLE KEYS */;

INSERT INTO `rubrique_data` (`_IDrubrique`, `_IDdata`, `_valeur`)
VALUES
	(2,10,''),
	(4,10,''),
	(5,10,''),
	(6,10,''),
	(7,10,''),
	(8,10,''),
	(10,10,''),
	(11,10,''),
	(12,10,''),
	(13,10,''),
	(15,10,'');

/*!40000 ALTER TABLE `rubrique_data` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table rubrique_valeur
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rubrique_valeur`;

CREATE TABLE `rubrique_valeur` (
  `_IDrubrique` int(10) NOT NULL,
  `_code` varchar(100) NOT NULL,
  `_valeur` varchar(200) NOT NULL,
  `_attr` longtext NOT NULL,
  `_lang` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `rubrique_valeur` WRITE;
/*!40000 ALTER TABLE `rubrique_valeur` DISABLE KEYS */;

INSERT INTO `rubrique_valeur` (`_IDrubrique`, `_code`, `_valeur`, `_attr`, `_lang`)
VALUES
	(1,'O','Oui','','FR'),
	(1,'N','Non','','FR'),
	(14,'formateur','Formateur','','FR'),
	(14,'eleve','Élève','','FR');

/*!40000 ALTER TABLE `rubrique_valeur` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table smileys
# ------------------------------------------------------------

DROP TABLE IF EXISTS `smileys`;

CREATE TABLE `smileys` (
  `_IDsmile` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_type` enum('H','T') NOT NULL,
  `_ident` varchar(20) NOT NULL,
  `_code` varchar(10) NOT NULL,
  PRIMARY KEY (`_IDsmile`),
  UNIQUE KEY `_key` (`_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des smileys';

LOCK TABLES `smileys` WRITE;
/*!40000 ALTER TABLE `smileys` DISABLE KEYS */;

INSERT INTO `smileys` (`_IDsmile`, `_type`, `_ident`, `_code`)
VALUES
	(1,'H','note','[!]'),
	(2,'H','oui','[yes]'),
	(3,'H','non','[no]'),
	(4,'H','attention','[!!]'),
	(5,'H','question','[?:(]'),
	(6,'H','idee','[!:)]'),
	(7,'H','sourire','[:)]'),
	(8,'H','surpris','[:o]'),
	(9,'H','mecontent','[:((]'),
	(10,'T','mefiance','[>:]'),
	(11,'T','censure','[:X]'),
	(12,'T','hum','[>:(]'),
	(13,'T','humpf','[<:((]'),
	(14,'T','heureux','[:))]'),
	(15,'T','help','[:||]'),
	(16,'T','clindoeil','[;)]'),
	(17,'T','bye','[;>>]'),
	(18,'T','bravo','[;>]'),
	(19,'T','mad','[:(]'),
	(20,'T','avocat','[cit]'),
	(21,'T','langue','[:P]'),
	(22,'T','bof','[:/]'),
	(23,'T','soleil','[8))]'),
	(24,'T','rigole','[:D]'),
	(25,'T','pinte','[glou]'),
	(26,'T','pleure','[cry]'),
	(27,'T','raf','[raf]');

/*!40000 ALTER TABLE `smileys` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table stat_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stat_log`;

CREATE TABLE `stat_log` (
  `_date` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_ident` varchar(40) NOT NULL,
  `_IPv6` varchar(23) NOT NULL,
  `_action` enum('C','D','E','X') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des logs de connexion à l''intranet';



# Affichage de la table stat_page
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stat_page`;

CREATE TABLE `stat_page` (
  `_date` datetime NOT NULL,
  `_ident` varchar(80) NOT NULL,
  `_IDgrp` int(10) unsigned NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_IP` text NOT NULL,
  `_attr` varchar(2500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des statistiques par page';



# Affichage de la table user_acl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_acl`;

CREATE TABLE `user_acl` (
  `_IDacl` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(20) NOT NULL,
  `_IDident` int(11) NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_date` datetime NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`_IDacl`),
  UNIQUE KEY `_key` (`_ident`,`_IDident`,`_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des ACL (Access Control List)';



# Affichage de la table user_admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_admin`;

CREATE TABLE `user_admin` (
  `_adm` int(3) NOT NULL,
  `_ident` varchar(20) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_adm`,`_lang`),
  UNIQUE KEY `_key` (`_ident`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des droits utilisateurs';

LOCK TABLES `user_admin` WRITE;
/*!40000 ALTER TABLE `user_admin` DISABLE KEYS */;

INSERT INTO `user_admin` (`_adm`, `_ident`, `_lang`)
VALUES
	(0,'Bannished','en'),
	(1,'User','en'),
	(2,'Member','en'),
	(4,'Moderator','en'),
	(8,'Manager','en'),
	(255,'Administrator','en'),
	(0,'Verbannt','de'),
	(1,'Benutzer','de'),
	(2,'Mitglied','de'),
	(4,'Moderator','de'),
	(8,'Manager','de'),
	(255,'Administrator','de'),
	(0,'Bannis','fr'),
	(1,'Utilisateur','fr'),
	(2,'Membre','fr'),
	(4,'Modérateur','fr'),
	(8,'Gestionnaire','fr'),
	(255,'Administrateur','fr');

/*!40000 ALTER TABLE `user_admin` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_category`;

CREATE TABLE `user_category` (
  `_IDcat` int(10) unsigned NOT NULL,
  `_ident` varchar(20) NOT NULL,
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDcat`,`_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des catégories d''utilisateurs';

LOCK TABLES `user_category` WRITE;
/*!40000 ALTER TABLE `user_category` DISABLE KEYS */;

INSERT INTO `user_category` (`_IDcat`, `_ident`, `_lang`)
VALUES
	(1,'Student','en'),
	(2,'Staff','en'),
	(3,'Outside','en'),
	(1,'Estudiante','es'),
	(2,'Personal','es'),
	(3,'Exterior','es'),
	(1,'Apprenant','fr'),
	(2,'Personnel','fr'),
	(3,'Extérieur','fr');

/*!40000 ALTER TABLE `user_category` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_config`;

CREATE TABLE `user_config` (
  `_IDconf` int(10) unsigned NOT NULL,
  `_IDtheme` int(10) unsigned NOT NULL DEFAULT '1',
  `_menu` int(10) unsigned NOT NULL DEFAULT '0',
  `_puce` varchar(20) NOT NULL,
  `_fond` varchar(20) NOT NULL,
  `_header` varchar(20) NOT NULL,
  `_service` tinytext NOT NULL,
  PRIMARY KEY (`_IDconf`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table de la personnalisation de l''intranet';

LOCK TABLES `user_config` WRITE;
/*!40000 ALTER TABLE `user_config` DISABLE KEYS */;

INSERT INTO `user_config` (`_IDconf`, `_IDtheme`, `_menu`, `_puce`, `_fond`, `_header`, `_service`)
VALUES
	(1,1,1,'arrow.gif','backstripes.gif','blurred.jpg',' 255 256 257 258 259 260 261 262 263 264 265 266 267 '),
	(9,1,1,'dash.gif','blur.gif','blurred.jpg',' 255 256 257 258 259 260 261 262 263 264 265 266 267 '),
	(11,1,0,'spot3.gif','woof.jpg','streak4.gif',' 256 257 258 259 260 261 262 263 264 265 266 267 ');

/*!40000 ALTER TABLE `user_config` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_cookie
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_cookie`;

CREATE TABLE `user_cookie` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `_token` text NOT NULL,
  `_userID` int(11) NOT NULL,
  `_timeOut` int(20) NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table user_denied
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_denied`;

CREATE TABLE `user_denied` (
  `_IDcentre` int(10) unsigned NOT NULL,
  `_IDgrp` int(10) unsigned NOT NULL,
  `_dstart` date NOT NULL,
  `_dend` date NOT NULL,
  `_hstart` time NOT NULL,
  `_hend` time NOT NULL,
  UNIQUE KEY `_key` (`_IDcentre`,`_IDgrp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des droits d''accès à l''intranet';

LOCK TABLES `user_denied` WRITE;
/*!40000 ALTER TABLE `user_denied` DISABLE KEYS */;

INSERT INTO `user_denied` (`_IDcentre`, `_IDgrp`, `_dstart`, `_dend`, `_hstart`, `_hend`)
VALUES
	(2,1,'0000-00-00','0000-00-00','00:00:00','00:00:00');

/*!40000 ALTER TABLE `user_denied` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_forfait
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_forfait`;

CREATE TABLE `user_forfait` (
  `_IDeleve` int(11) NOT NULL,
  `_IDforfait` int(11) NOT NULL,
  `_solde` int(11) NOT NULL,
  PRIMARY KEY (`_IDeleve`,`_IDforfait`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Affichage de la table user_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_group`;

CREATE TABLE `user_group` (
  `_IDgrp` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ident` varchar(40) NOT NULL,
  `_delay` datetime DEFAULT NULL,
  `_hdquotas` bigint(20) unsigned NOT NULL DEFAULT '1024000',
  `_IDcat` int(10) unsigned NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_lang` varchar(2) NOT NULL,
  PRIMARY KEY (`_IDgrp`,`_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des groupes utilisateurs';

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;

INSERT INTO `user_group` (`_IDgrp`, `_ident`, `_delay`, `_hdquotas`, `_IDcat`, `_visible`, `_lang`)
VALUES
	(1,'_STUDENT',NULL,1024000,1,'O','fr'),
	(2,'_TEACHER',NULL,1024000,2,'O','fr'),
	(4,'Administration',NULL,1024000,2,'O','fr');

/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_id
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_id`;

CREATE TABLE `user_id` (
  `_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDgrp` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDcentre` int(10) unsigned NOT NULL DEFAULT '1',
  `_IDclass` int(10) unsigned DEFAULT NULL,
  `_numen` varchar(20) DEFAULT NULL,
  `_create` datetime NOT NULL DEFAULT '2002-09-01 00:00:00',
  `_date` datetime DEFAULT NULL,
  `_lastcnx` datetime DEFAULT NULL,
  `_adm` int(3) NOT NULL DEFAULT '1',
  `_ident` varchar(20) NOT NULL,
  `_passwd` varchar(200) NOT NULL,
  `_name` varchar(40) NOT NULL,
  `_fname` varchar(40) NOT NULL,
  `_sexe` enum('H','F','A') NOT NULL,
  `_title` varchar(60) DEFAULT NULL,
  `_fonction` tinytext,
  `_email` varchar(40) DEFAULT NULL,
  `_work` varchar(20) DEFAULT NULL,
  `_tel` varchar(20) DEFAULT NULL,
  `_mobile` varchar(20) DEFAULT NULL,
  `_born` date DEFAULT NULL,
  `_adr1` tinytext NOT NULL,
  `_adr2` tinytext NOT NULL,
  `_cp` varchar(10) DEFAULT NULL,
  `_city` varchar(40) DEFAULT NULL,
  `_signature` varchar(255) DEFAULT NULL,
  `_msg` int(10) unsigned NOT NULL DEFAULT '0',
  `_res` int(10) unsigned NOT NULL DEFAULT '0',
  `_cnx` int(10) unsigned NOT NULL DEFAULT '0',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  `_avatar` int(10) unsigned NOT NULL DEFAULT '0',
  `_persistent` enum('O','N') NOT NULL DEFAULT 'N',
  `_chs` enum('O','N') NOT NULL DEFAULT 'N',
  `_regime` enum('E','I','D','C') NOT NULL DEFAULT 'E',
  `_bourse` enum('O','N') NOT NULL DEFAULT 'N',
  `_delegue` enum('O','N') NOT NULL DEFAULT 'N',
  `_visible` enum('O','N','A','D','E') NOT NULL DEFAULT 'O',
  `_delay` datetime DEFAULT NULL,
  `_centre` bigint(20) unsigned NOT NULL DEFAULT '0',
  `_IDmat` varchar(255) DEFAULT NULL,
  `_lang` varchar(2) NOT NULL DEFAULT 'fr',
  `_IDtuteur1` int(10) unsigned NOT NULL DEFAULT '0',
  `_IDtuteur2` int(10) unsigned NOT NULL DEFAULT '0',
  `_code` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`_ID`),
  UNIQUE KEY `_key` (`_ident`,`_passwd`),
  KEY `user_id IDclass index` (`_IDclass`),
  CONSTRAINT `user_id IDclass to campus_classe IDclass` FOREIGN KEY (`_IDclass`) REFERENCES `campus_classe` (`_IDclass`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table des identifiants de connexion des utilisateurs à l''int';

LOCK TABLES `user_id` WRITE;
/*!40000 ALTER TABLE `user_id` DISABLE KEYS */;

INSERT INTO `user_id` (`_ID`, `_IDgrp`, `_IDcentre`, `_IDclass`, `_numen`, `_create`, `_date`, `_lastcnx`, `_adm`, `_ident`, `_passwd`, `_name`, `_fname`, `_sexe`, `_title`, `_fonction`, `_email`, `_work`, `_tel`, `_mobile`, `_born`, `_adr1`, `_adr2`, `_cp`, `_city`, `_signature`, `_msg`, `_res`, `_cnx`, `_IP`, `_avatar`, `_persistent`, `_chs`, `_regime`, `_bourse`, `_delegue`, `_visible`, `_delay`, `_centre`, `_IDmat`, `_lang`, `_IDtuteur1`, `_IDtuteur2`, `_code`)
VALUES
	(1,4,1,NULL,NULL,'2002-09-01 00:00:00','2020-10-30 09:26:35','2020-10-30 09:26:35',255,'admin','21232f297a57a5a743894a0e4a801fc3','Administrateur','Administrateur','A',NULL,NULL,'admin@promethee.fr',NULL,NULL,NULL,NULL,'','',NULL,NULL,NULL,0,0,0,0,0,'N','N','E','N','N','O',NULL,0,NULL,'fr',0,0,NULL);

/*!40000 ALTER TABLE `user_id` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_log`;

CREATE TABLE `user_log` (
  `_ID` int(11) NOT NULL AUTO_INCREMENT,
  `_IDuser` int(11) unsigned DEFAULT NULL,
  `_year` int(11) DEFAULT NULL COMMENT 'L''année du log',
  `_niveau` int(11) DEFAULT '0' COMMENT 'Le niveau (1ère année...) de l''élève correspondant à l''année',
  `_attr` longtext,
  PRIMARY KEY (`_ID`),
  KEY `user_log IDuser index` (`_IDuser`),
  CONSTRAINT `user_log IDuser to user_id ID` FOREIGN KEY (`_IDuser`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table user_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_menu`;

CREATE TABLE `user_menu` (
  `_ID` int(10) unsigned NOT NULL,
  `_IDmenu` int(10) unsigned NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  UNIQUE KEY `_key` (`_ID`,`_IDmenu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des menus personnalisés des utilisateurs';

LOCK TABLES `user_menu` WRITE;
/*!40000 ALTER TABLE `user_menu` DISABLE KEYS */;

INSERT INTO `user_menu` (`_ID`, `_IDmenu`, `_visible`)
VALUES
	(1,1,'O'),
	(9,2,'O'),
	(9,15,'N'),
	(11,15,'N'),
	(11,6,'O'),
	(9,6,'O'),
	(10,15,'O'),
	(11,2,'O'),
	(16,15,'N'),
	(10,2,'O'),
	(13,15,'N'),
	(10,17,'O'),
	(24,6,'O'),
	(91,6,'N'),
	(82,6,'O'),
	(45,2,'O'),
	(103,2,'O'),
	(41,2,'O'),
	(41,6,'N'),
	(82,2,'O'),
	(15,6,'N'),
	(15,2,'N'),
	(97,2,'O'),
	(80,2,'N'),
	(24,2,'O'),
	(2615,2,'N'),
	(69,2,'O'),
	(69,6,'O'),
	(27,2,'N'),
	(2618,2,'N'),
	(100,6,'O'),
	(2619,2,'N'),
	(13,2,'N'),
	(48,2,'N'),
	(13,6,'O'),
	(9,24,'O');

/*!40000 ALTER TABLE `user_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_parametre
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_parametre`;

CREATE TABLE `user_parametre` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_IDuser` int(11) unsigned NOT NULL COMMENT 'ID de l''utilisateur',
  `_code` varchar(200) DEFAULT NULL COMMENT 'Le code permettant d''identifier le paramètre',
  `_valeur` longtext COMMENT 'La valeur du paramètre',
  `_comm` longtext COMMENT 'La description du paramètre',
  PRIMARY KEY (`_id`),
  KEY `UserID Key` (`_IDuser`),
  CONSTRAINT `UserID to user_id` FOREIGN KEY (`_IDuser`) REFERENCES `user_id` (`_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table user_promos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_promos`;

CREATE TABLE `user_promos` (
  `_IDeleve` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_IDclass` int(10) unsigned NOT NULL,
  `_date` date NOT NULL,
  `_delegue` enum('O','N') NOT NULL DEFAULT 'N',
  UNIQUE KEY `_key` (`_IDeleve`,`_IDclass`,`_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des promotions';

LOCK TABLES `user_promos` WRITE;
/*!40000 ALTER TABLE `user_promos` DISABLE KEYS */;

INSERT INTO `user_promos` (`_IDeleve`, `_IDclass`, `_date`, `_delegue`)
VALUES
	(1,0,'2015-00-00',''),
	(2,0,'2015-00-00',''),
	(3,0,'2015-00-00',''),
	(4,0,'2015-00-00',''),
	(5,0,'2015-00-00','');

/*!40000 ALTER TABLE `user_promos` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table user_session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_session`;

CREATE TABLE `user_session` (
  `_IDsess` varchar(10) NOT NULL,
  `_lastaction` datetime NOT NULL,
  `_ID` int(10) unsigned NOT NULL,
  `_visible` enum('O','N') NOT NULL DEFAULT 'O',
  `_anonyme` enum('O','N') NOT NULL DEFAULT 'O',
  `_action` enum('C','D') NOT NULL DEFAULT 'C',
  `_IP` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`_IDsess`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des sessions de connexion des utilisateurs à l''intrane';



# Affichage de la table user_tutors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_tutors`;

CREATE TABLE `user_tutors` (
  `_index` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_ID` int(10) unsigned NOT NULL,
  `_IDtutor` int(10) unsigned NOT NULL,
  PRIMARY KEY (`_index`),
  UNIQUE KEY `_key` (`_ID`,`_IDtutor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table des tuteurs';

LOCK TABLES `user_tutors` WRITE;
/*!40000 ALTER TABLE `user_tutors` DISABLE KEYS */;

INSERT INTO `user_tutors` (`_index`, `_ID`, `_IDtutor`)
VALUES
	(1,2657,2650);

/*!40000 ALTER TABLE `user_tutors` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
