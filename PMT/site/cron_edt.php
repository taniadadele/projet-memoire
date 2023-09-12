<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 Thomas Dazy (contact@thomasdazy.fr)

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

  // Page qui envois les mails au différents profs avec le contenu de la semaine à venir
  require_once "config.php";
  require_once "include/sqltools.php";
  require_once "include/fonction.php";
  $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

  //!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
  require_once "include/TMessage.php";
  require "msg/mail.php";
  $msg_mail  = new TMessage("msg/fr/mail.php");
  $msg_mail->msg_mail_search  = $keywords_search;
  $msg_mail->msg_mail_replace = $keywords_replace;

  // On récupère l'adresse URL du site
  $web = $_SERVER['SERVER_NAME'];

  // On récupère le nom
  $result = mysqli_query($mysql_link, "SELECT _ident FROM config_centre WHERE _IDcentre = 1 ");
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $CfgTitle = $row[0];

  // On récupère l'adresse
  $result = mysqli_query($mysql_link, "SELECT _adresse FROM config WHERE _visible = 'O' LIMIT 1 ");
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $CfgAddr = $row[0];

  // Si on doit envoyer les mail aujourd'hui
  if (getParam("sendWeeklyRecap") == date('N'))
  {
    // On met en place un compteur pour le retour
    $compteur = 0;
    // On stoque les mails pour le retour
    $emails_adresses = "";

    $query = "SELECT `_email` FROM `user_id` WHERE `_IDgrp` >= '2' AND `_adm` >= 1 ";
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      // On récupère la liste de l'EDT pour chaque prof
      $msg_edt = sendEdtList($row[0]);
      $msg = "";
      $msg .= $msg_edt;

      // On construit le footer du message
      $msg .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER);
      $msg .= $CfgTitle . "<br>";
      $msg .= $CfgAddr.'<br>';
      if (getParam('showLinkToSiteMail')) $msg .= $web;

      // On construit le HEADER
      $headers = "From: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
      $headers .= "Reply-To: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
      $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
      $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);
      if (getParam('email_copie_mail_hebdo')) $headers .= "Cc: ".getParam('email_copie_mail_hebdo')." \r\n";
      $headers .= "\r\n";

      // Si on est en dev, alors on n'envois le mail qu'à la personne qui a mis son mail dans le champ MAIL_DEV_MODE
      if (getParam("MAIL_DEV_MODE") == "") $toProf = trim($row[0]);
      else $toProf = trim(getParam("MAIL_DEV_MODE"));

      // Si on a des éléments dans le tableau contenant les cours de la semaine
      if ($msg_edt != false)
      {
        // Alors on envois le mail
        $subject = $msg_mail->read($MAIL_COURSES_OF_WEEK_SUBJ);
        mail($toProf, $subject, $msg, $headers);
        $emails_adresses .= ";".$toProf;
        $compteur++;
      }
    }
    // setParam('temp_mail_sent', $compteur);

    // On envois un mail pour confirmer l'envoi (si une adresse est définie pour le retour)
    if (getParam('sendConfirmCronMail') != "")
    {
      $to = getParam('sendConfirmCronMail');
      $subject = '[Prométhée] Confirmation d\'envoi de mails';
      $message = 'Les mails ont bien été envoyés: nb de mails envoyes: '.$compteur.'<br>À:<br>';
      $list_emails = explode(';', $emails_adresses);
      foreach ($list_emails as $key => $value) {
        $message .= $value."<br>";
      }

      $message .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER);
      $message .= $CfgTitle . "<br>";
      $message .= $CfgAddr.'<br>';
      if (getParam('showLinkToSiteMail')) $message .= $web;

      $headers = "From: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
      $headers .= "Reply-To: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
      $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
      $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);
      $headers .= "\r\n";

      mail($to, $subject, $message, $headers);
    }
    setParam('temp_mail_sent', $compteur);
    // On affiche le résultat (logs OVH)
    echo "OK, ".$compteur." mails envoyés";

    // On log les envois de mail
    $query = "INSERT INTO `mail_log`(`_id`, `_date`, `_type`, `_dest_count`, `_dest`) VALUES (NULL, NOW(), '1', '".$compteur."', '".$emails_adresses."') ";
    mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }
  // Si ce n'est pas le bon jour pour envoyer les mail, on l'affiche (logs OVH)
  else echo "Pas le bon jour";




  echo "<br><hr><br>";



  // ----------------------------------------------------------------------------
  // Envois des mails de cours en attente
  // ----------------------------------------------------------------------------
  if (getParam('autoSendConfirmMailDaily'))
  {
    $compteur = 0;
    $listSentEmailAdress = array();
    $query = "SELECT `_ID`, `_email` FROM `user_id` WHERE `_IDgrp` >= '2' AND `_adm` >= 1 ";
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $userID = $row[0];
      $userEmail = $row[1];


      // Pour chaques professeurs,
      $compteurOfEventNumber = 0;
      $query2 = "SELECT `_IDx` FROM `edt_data` WHERE `_ID` = '".$userID."' AND `_visible` = 'O' AND `_etat` = '3' ";
      $result2 = mysqli_query($mysql_link, $query2);
      while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
        // On compte le nombre d'évènements qu'ils on en attente
        $compteurOfEventNumber++;


        $sql  = "UPDATE `edt_data` SET `_etat` = '4' ";
        $sql .= "WHERE `_IDx` = ".$row2[0]." ";
        if(mysqli_query($mysql_link, $sql)==false)
        {
          echo "error";
        }
        else
        {
          echo "success";
        }
      }


      // S'il on au moins un évènement en attente
      if ($compteurOfEventNumber != 0)
      {
        // Alors on leurs envoie un mail
        // $subject = "[Prométhée] Cours en attente";
        $subject = getParam('pendingLessonsMailSubject');

        // Headers
        $headers = "From: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
        $headers .= "Reply-To: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
        $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
        $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);
        $headers .= "\r\n";

        // Message du mail
        $texte = "Bonjour, vous avez ".$compteurOfEventNumber." cours en attente de validation. Merci de <a href=\"http://".$web."/index.php?item=29&action=confirmEvents\">cliquer ici</a> (depuis une ordinateur, pas un téléphone) pour confirmer vos disponibilités.";

        $texte .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER);
        $texte .= $CfgTitle . "<br>";
        $texte .= $CfgAddr.'<br>';
        if (getParam('showLinkToSiteMail')) $texte .= $web;

        if (getParam("MAIL_DEV_MODE") != "") $userEmail = getParam("MAIL_DEV_MODE");
        mail($userEmail, $subject, $texte, $headers);
        $compteur++;
        $listSentEmailAdress[] = $userEmail;
      }
    }
    // ----------------------------------------------------------------------------


    // Test pour l'envoi de mails
    $subject = '[Prométhée] [EDT_WAITING] Confirmation d\'envoi de mails';
    $message = 'Les mails de demande de confirmation d\'évènements en attente ont bien été envoyés:<br>nb de mails envoyes: '.$compteur.'<br>À:<br>';
    $list_emails = explode(';', $emails_adresses);
    foreach ($listSentEmailAdress as $key => $value) {
      $message .= $value."<br>";
    }
    $message .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER);
    $message .= $CfgTitle . "<br>";
    $message .= $CfgAddr.'<br>';
    if (getParam('showLinkToSiteMail')) $message .= $web;

    $headers = "From: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
    $headers .= "Reply-To: ".str_replace(" ", "-", $CfgTitle)." <no-reply@".$web.">\r\n";
    $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
    $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);
    $headers .= "\r\n";


    echo "<br>OK".$compteur." mails envoyés";
    // mail($to, $subject, $message, $headers);



    // On log les envois de mail
    $listEmailSent = ";";
    foreach ($listSentEmailAdress as $key => $value) {
      if (strpos($listEmailSent, ';'.$value.';') !== true)
      $listEmailSent .= $value.";";
    }
    $query = "INSERT INTO `mail_log`(`_id`, `_date`, `_type`, `_dest_count`, `_dest`) VALUES (NULL, NOW(), '2', '".$compteur."', '".$listEmailSent."') ";
    mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

  }


  // ----------------------------------------------------------------------------
  // On vide le répertoire tmp/user_data qui est utilisé pour télécharger les données d'un user
  // ----------------------------------------------------------------------------
  if (file_exists('tmp/user_data'))
  {
    foreach (scandir('tmp/user_data') as $key => $value) {
      unlink('tmp/user_data/'.$value);
    }
    rmdir('tmp/user_data');
  }
  // ----------------------------------------------------------------------------






?>
