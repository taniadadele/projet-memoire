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
 *		projet   : définition des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 23/03/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$message = Array(

"<strong>Liste des Forums</strong>",
"dossier racine",
"Les forums permettent de poster une annonce ou de vous exprimer sur un thème particulier.<br/>Un dossier regroupe des forums centrés sur des thèmes communs (ex : musique).",
"nouveau",
"pour créer un forum.",
"dossier parent",
"créer un nouveau dossier",
"Forums",
"<strong>Modérateur</strong>",
"<strong>en attente</strong>",
"<strong>message</strong>",
"fermer le dossier",
"ouvrir le dossier",
"dossier",
"modifier le forum",
"Suppression du dossier %1 ?",
"forum privé",
"Suppression du forum %1 ?",
"rechercher",
"<a href=\'%1\'>rechercher</a> un message dans les forums.",
"<strong>La charte des forums</strong>",
"Nous vous rappelons qu'avant de poster un message dans un forum, il est nécessaire de respecter quelques règles de bon goût et de civilité afin de préserver ces espaces d'échanges et de liberté d'expression.<br/>Ainsi,sont strictement interdits :",
"- les propos diffamatoires, calomnieux ou racistes.
- la propagande politique ou religieuse.
- les insultes ou propos injurieux.",
"Attention, n'oubliez pas qu'un mot écrit en majuscule sera interprété par les lecteurs comme étant crié, et peut par conséquent être mal ressenti. Usez-en donc avec précaution.<br/>Dans tous les cas, l'ironie ou l'humour seront toujours mieux appréciés que des propos agressifs.",
"<em><strong>De l'utilisation des smilies.</strong></em><br/>Pour faire connaître votre humeur ou votre état d'âme à vos interlocuteurs, vous pouvez employer à la fin de vos phrases des symboles connus sous le nom de smilies (émoticônes en français) dont nous donnons ci-dessous les plus connus.<br/>
:-( tristesse, :-) joie, :-D rire, ;-) clin d'oeil, :-&lt;déception, :-&gt; content, :-/scepticisme, indifférence :-|,:-|| colère, :-o choqué, 8-] Whaou !, :-X censuré.<br/>
Cool, non ? &gt;;-)",
"<em><strong>De l'utilité des bannières.</strong></em><br/>Signalées en début de sujet par des crochets, elles permettent aux lecteurs de repérer facilement un thème particulier dans toute la liste. Exemple : [voyage en Italie].<br/>N'hésitez pas à les employer, surtout si le sujet est récurrent.",
"<a href=\'%1\'>retourner au forum</a>",
"<strong>Gestion des Forums</strong><em><br/>Veuillez compléter le formulaire suivant pour paramétrer le forum</em>",
"modification",
"<strong>Statut :</strong>",
"<strong>Dossiers</strong>",
"<strong>Intitulé du forum</strong>",
"<strong>Fermer le forum</strong>",
"<strong>Rédacteurs</strong>",
"<strong>Lecteurs</strong>",
"aucun",
"<strong>Autorisations</strong>",
"<strong>Affichage</strong>",
"<strong>Messages privés</strong>",
"Pièce Jointe",
"Mise à jour",
"Suppression",
"Forum privé",
"Validation automatique",
"FAQ",
"egroup",
"chronologique",
"anti chronologique",
"interdit",
"post-it",
"email",
"<strong>Hébergement d'images</strong>",
"l'utilisateur doit être préalablement déclaré comme modérateur.",
"pour modifier un forum.",
"pour revenir aux forums.",
"<strong>Forum %1</strong>",
"Messages en attente : <strong>%1</strong>",
"Re: %1",
"suivant",
"précédent",
"<strong>Sujet :</strong>",
"<strong>Auteur :</strong>",
"<strong>Posté le :</strong>",
"<strong>Aucun message en attente de validation</strong>",
"<strong>Création d'un Forum</strong><em><br/>Pour créer votre forum veuillez compléter le formulaire suivant</em>",
"votre forum apparaîtra après validation de l'administrateur.<br/>Merci de votre participation",
"Demande de validation de forum",
"Vous vous trouvez dans un forum d'entraide pédagogique.<br/>Cet espace est destiné à recevoir des questions concernant des points de cours ou des exercices mal compris.<br/>Le modérateur a pour rôle de vous aider mais tous les _STUDENT sont encouragés à vous répondre.",
"forum pédagogique :",
"autre forum",
"<strong>Attention :</strong> l'intitulé doit être renseigné.",
"<strong>Intitulé</strong> (le nom du forum)",
"<strong>Description</strong> (optionnelle)",
"modifier",
"ajouter",
"pour %1 un forum.",
"<strong>Gestion des Forums</strong><em><br/>Pour créer un dossier veuillez compléter le formulaire suivant</em>",
"<strong>Attention :</strong> le dossier doit être renseigné.",
"<strong>Nom du dossier</strong>",
"<strong>Fermer le lien</strong>",
"créer",
"pour %1 un dossier.",
"<em>Veuillez compléter le formulaire ci-dessous pour poster un message</em>",
"import image",
"Il sera visible dès sa validation par le modérateur.",
"Votre message a été correctement posté.",
"Merci de votre participation.",
"<a href=\'%1\'>revenir au forum</a>",
"<strong>Attention : </strong>le sujet n'est pas renseigné. Veuillez compléter le champ de saisie ci-dessous.",
"<strong>Humeur :</strong>",
"<strong>Attention : </strong>le message n'est pas renseigné. Veuillez compléter le champ de saisie ci-dessous.",
"aide",
"description",
"<strong>Poster :</strong>",
"Message",
"Annonce",
"<strong>Inclure :</strong>",
"Signature",
"pour poster votre message.",
"Les réponses au message d'origine sont données par ordre chronologique. Pour les lire, il suffit de cliquer sur le sujet de la réponse.",
"pour répondre à ce message.",
", modifié le %1",
"Suppression de %1 ?",
"effacer le message",
"modifier le message",
"<strong>Forum :</strong>",
"<strong>messages :</strong> %1",
"Supprimer la pièce jointe ?",
"<strong>Taille :</strong> %1 octets",
"document en <a href=\'%1\' onmouseover=\'return overlib('%2');\' onmouseout=\'return nd();\' %3>Pièce Jointe</a>.",
"posté le",
"répondu le",
"Cacher le posteur",
"Afficher le posteur",
"Réduire",
"Déployer",
"modératrice",
"modérateur",
"aucun modérateur",
"Ce forum n'existe pas.<br/>Pour créer le forum <strong>%1</strong>, cliquez <a href=\'%2\'>ici</a>.",
"Pour connaître la charte de ce forum cliquez <a href=\'%1\'>ici</a>.",
"pour écrire un nouveau sujet.",
"[précédent]<strong>.</strong>[suivant]",
"<strong>Sujets</strong>",
"<strong>date</strong>",
"<strong>vus</strong>",
"<strong>réponses</strong>",
"<strong>Messages les plus récents</strong>",
"<a href=\'%1\'>Voir tous les messages</a> (%2)",
"<strong>Liste des Messages</strong>",
"<a href=\'%1\'>rechercher</a> un message dans ce forum.",
"insertion",
"par %1 %2",
"Janv.,Fév.,Mars,Avril,Mai,Juin,Juil.,Août,Sept.,Oct.,Nov.,Déc.",
"Envoi par email",
"s'abonner",
"se désabonner",
"créé le %1",
"<strong>Langue</strong>",
"%1 utilisateur%s abonné%s",
"<strong>Groupe :</strong>",
"<strong>Centre :</strong>",
"<strong>inscrit le :</strong>",
"fermer cette fenêtre",
"%1 à écrit :",
"Censurer %1 ?",
"<strong>Le contenu de ce message a été censuré</strong>",
"Multilingue",
"dernier post",
"RSS",
"téléchargée %1 fois",
"déplacer",
"aller au message n°",
"Enlever la censure ?",
"RAZ",
"ouvrir le forum",
"[nouveau]",
"[répondre]",
"[valider]",
"[annuler]"

);
//---------------------------------------------------------------------------
?>