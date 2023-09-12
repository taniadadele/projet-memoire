<?php
// /*-----------------------------------------------------------------------*
//    Copyright (c) 2007 by Dominique Laporte(C-E-D@wanadoo.fr)
//
//    This file is part of Prométhée.
//
//    Prométhée is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Prométhée is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
//  *-----------------------------------------------------------------------*/
//
//
// /*
//  *		module   : user_lost.htm
//  *		projet   : la page de récupération des mots de passe oubliés
//  *
//  *		version  : 1.0
//  *		auteur   : laporte
//  *		creation : 18/04/07
//  *		modif    :
//  */
//
// //!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
// if ( is_file($_SESSION["ROOTDIR"]."/msg/mail.php") )
//   require "msg/mail.php";
// $msg_mail  = new TMessage("msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
// $msg_mail->msg_mail_search  = $keywords_search;
// $msg_mail->msg_mail_replace = $keywords_replace;

// if ( is_file($_SESSION["ROOTDIR"]."/msg/mail.php") )
//   require "msg/mail.php";
// $msg_mail  = new TMessage("msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
// $msg_mail->msg_mail_search  = $keywords_search;
// $msg_mail->msg_mail_replace = $keywords_replace;
// print_r($msg_mail);
//
// require("page_session.php");
$currentPage = "index";
include("mobile_banner.php");
// $_SESSION["ROOTDIR"] = ( @$ROOTDIR != "" ) ? $ROOTDIR : "." ;

if ( is_file($_SESSION["ROOTDIR"]."/msg/user.php") )
  require "msg/user.php";

$msg  = new TMessage("msg/".$_SESSION["lang"]."/user.php", $_SESSION["ROOTDIR"]);
$msg->msg_search  = $keywords_search;
$msg->msg_replace = $keywords_replace;




if ( is_file($_SESSION["ROOTDIR"]."/msg/mail.php") )
  require "msg/mail.php";

$msg_mail  = new TMessage("msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
$msg_mail->msg_mail_search  = $keywords_search;
$msg_mail->msg_mail_replace = $keywords_replace;

//


  $ident  = trim(@$_POST["ident"]);	// ID de l'utilisateur

  $submit = @$_POST["valid_x"];		// bouton de validation

  function genererMDP ($longueur = 8){
      // initialiser la variable $mdp
      $mdp = "";

      // Définir tout les caractères possibles dans le mot de passe,
      // Il est possible de rajouter des voyelles ou bien des caractères spéciaux
      $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

      // obtenir le nombre de caractères dans la chaîne précédente
      // cette valeur sera utilisé plus tard
      $longueurMax = strlen($possible);

      if ($longueur > $longueurMax) {
          $longueur = $longueurMax;
      }

      // initialiser le compteur
      $i = 0;

      // ajouter un caractère aléatoire à $mdp jusqu'à ce que $longueur soit atteint
      while ($i < $longueur) {
          // prendre un caractère aléatoire
          $caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);

          // vérifier si le caractère est déjà utilisé dans $mdp
          if (!strstr($mdp, $caractere)) {
              // Si non, ajouter le caractère à $mdp et augmenter le compteur
              $mdp .= $caractere;
              $i++;
          }
      }
      // retourner le résultat final
      return $mdp;
  }

  // Si un identifiant est passé:
  if (isset($_POST['ident']) && !isset($_POST['newPass']))
  {
    $query = "SELECT `_ID`, `_email`, `_lastcnx` FROM `user_id` WHERE (`_ident` = '".trim($_POST['ident'])."' OR `_email` = '".addslashes(trim($_POST['ident']))."') LIMIT 1 ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      // On construit le lien à envoyer
      $newSecretToken = genererMDP(25);
      $newSignature = "[".$newSecretToken."][".time()."]";

      // On met à jour le champ _signature de l'utilisateur avec le token et le timestamp actuel
      $update_query = "UPDATE `user_id` SET `_signature`= '".$newSignature."' WHERE `_ID` = '".$row[0]."' ";
      @mysql_query($update_query, $mysql_link) or die('Erreur SQL !<br>'.$update_query.'<br>'.mysql_error());


      // Création du mail

      // Headers

      $headers = "From: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
      $headers .= "Reply-To: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
      $headers .= $msg_mail->read($MAIL_HEADERS_MIME);
      $headers .= $msg_mail->read($MAIL_HEADERS_CONTENT_HTML);

      // Corps
      $body .= $msg_mail->read($MAIL_FORGOT_PASSWD_BODY, "https://www.".$_SESSION['CfgWeb']."/index.php?item=1&token=".$newSecretToken."&page=rstpswd");

      // On ajoute la signature au mail:
      $body .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER);
      $body .= $_SESSION["CfgAdr"] . "<br>";
      if (getParam('showLinkToSiteMail')) $body .= "<a href=\"https://www.".$_SESSION['CfgWeb']."\">".$_SESSION["CfgWeb"]."</a>";

      // Objet du mail:
      $subject = $_SESSION['CfgIdent']." - ".$msg->read($USER_FORGOT_PASSWD);

      // On récupère le destinataire:
      $destinataire = $row[1];

      // Si on est en dev, alors tu envois le mail au compte mail de dev plutôt qu'à la personne concernée
      if (getParam("MAIL_DEV_MODE") != "") $destinataire = getParam("MAIL_DEV_MODE");

      // On envois le mail
      // echo "MAIL: ".$body;
      mail($destinataire, $subject, $body, $headers);
      echo "<meta http-equiv=\"refresh\" content=\"0;URL=index_mobile.php?validpwd=mail_send\">";
    }
    if (!isset($destinataire)) $error_ident = true;

  }



  function getUserIdFromToken ($token)
  {
    $toReturn = 0;
    $query = "SELECT `_ID` FROM `user_id` WHERE `_signature` LIKE '%[".$token."]%' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $toReturn = $row[0];
    }
    return $toReturn;
  }


  function isTimedOutLink ($token)
  {
    $timeOutTimeMin = getParam('forgot_password_timeout');
    $timeOutTimeSec = $timeOutTimeMin * 60;

    // On récupère le timeout du lien:
    $query = "SELECT `_signature` FROM `user_id` WHERE `_ID` = '".getUserIdFromToken($token)."' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $temp = explode("][", $row[0]);
      $timeOutLinkTime = substr($temp[1], 0, -1);
    }
    if ($timeOutLinkTime + $timeOutTimeSec >= time()) return false;
    else return true;
  }

  if (isset($_GET['token']) && !isset($_POST['newPass_1']))
  {
    $token = $_GET['token'];
    // Si on a pas dépassé le temps de timeout du lien
    if (!isTimedOutLink($token))
    {
      $timedout = false;
    }
    else
    {
      $timedout = true;
    }
  }

  if (isset($_POST['newPass_1']))
  {

    $token = $_POST['token'];
    if (!isTimedOutLink($token))
    {

      if ($_POST['newPass_1'] == $_POST['newPass_2'])
      {
        $userID = getUserIdFromToken($token);
        $newPassword = md5($_POST['newPass_1']);
        $query = "UPDATE `user_id` SET `_passwd` = '".$newPassword."', `_signature` = '' WHERE `_ID` = '".$userID."' ";
        @mysql_query($query, $mysql_link) or die('Erreur SQL !<br>'.$query.'<br>'.mysql_error());
        echo "<meta http-equiv=\"refresh\" content=\"0;URL=index.php?validpwd=on\">";
      }
    }
  }

echo "<div class=\"maincontent\">";
	echo "<form id=\"formulaire\" action=\"mobile_user_lost.php\" method=\"post\" class=\"form-signin\">";
    echo "<div class=\"align-center\" style=\"width: 100%; text-align: center; margin-bottom: 15px;\">";
    // Logo du drapeau
    // $msg->languageBanner("msg");
    echo $my_logo."<br />";
    print($msg->read($USER_LOGIN));
    echo "</div>";
    echo "<br />";

    echo "<div class=\"maintitle\" style=\"background-image: url('".$_SESSION["CfgHeader"]."'); background-repeat: repeat; margin-bottom: 10px;\">";
    	echo "<div style=\"text-align: center;\">";
    		print($msg->read($USER_LOSTPWD));
    	echo "</div>";
    echo "</div>";

      if (isset($_GET['token'])) echo "<center>Veuillez entrer un nouveau mot-de-passe</center>";



		if ( $myret )
			print("<p style=\"text-align: center; font-weight: bold; color: red;\">$myret</p>");


    if ($error_ident) echo "<div class=\"alert alert-danger\" style=\"text-align: center;\"><strong>". $msg->read($USER_PASSWORD_UNKNOWN_IDENT) ."</strong></div>";

    echo "<table class=\"width100\" style=\"width: 100%;\">";
      if (!isset($_GET['token']))
      {
        echo "
        <tr>
          <td>
            <label style=\"width: 100%\" for=\"ident\"><input type=\"text\" id=\"ident\" name=\"ident\" class=\"form-control\" placeholder=\"".$msg->read($USER_USERID_OR_EMAIL)."\" required /></label>
          </td>
        </tr>";
      }


        if (isset($_GET['token']))
        {
          if (!$timedout)
          {
            echo "
            <tr>
              <td>
                <label style=\"width: 100%\" for=\"newPass_1\"><input type=\"password\" id=\"newPass_1\" name=\"newPass_1\" size=\"20\" class=\"form-control\" placeholder=\"".$msg->read($USER_PASSWORD)."\" required /></label>
              </td>
            </tr>
            ";
            echo "
            <tr>
              <td>
                <label style=\"width: 100%\" for=\"newPass_2\"><input type=\"password\" id=\"newPass_2\" name=\"newPass_2\" size=\"20\" class=\"form-control\" placeholder=\"".$msg->read($USER_PASSWORD)."\" required /></label>
              </td>
            </tr>
            ";


            echo "<input type=\"hidden\" name=\"token\" value=\"".$_GET['token']."\">";
          }
          else
          {
            echo "
              <div class=\"alert alert-error\" style=\"text-align: center;\"><strong>".$msg->read($USER_TIMEOUT_LINK)."</strong></div>
            ";
          }
        }

    echo "</table>";

    echo "<div style=\"text-align: left; display: inline-block;\">";
      echo "<a href=\"index.php\" class=\"btn btn-danger btn-large\" > ".$msg->read($USER_BACK_BTN)."</a>";
    echo "</div>";
		echo "<div style=\"text-align: right; display: inline-block; float: right;\">";

      if (isset($_GET['token']))
      {
        if ($timedout == "" || $timedout == false)
        {
          echo "<button class=\"btn btn-primary\" type=\"button\" id=\"checkToValidate\">".$msg->read($USER_INPUTOK)."</button>";
        }
      }
      else
      {
        echo "<button class=\"btn btn-primary\" type=\"submit\" name=\"valid\">".$msg->read($USER_INPUTOK)."</button>";
      }
		echo "</div>";
	echo "</form>";
echo "</div>";
?>


<style>
  body {
    background-image: url('images/background-login.jpg');
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
  }

  body::before {
    content: "";
    display: block;
    position: absolute;
    z-index: -1;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(0,0,0,0.1);
  }

  .maincontent {
  	width: 90%;

  	position: absolute;
  	top: 50%;
  	left: 50%;
  	-ms-transform: translateX(-50%) translateY(-50%);
  	-webkit-transform: translate(-50%,-50%);
  	transform: translate(-50%,-50%);
  	background-color: white;

  	border-radius: 10px;

  	padding: 33px 55px 33px 55px;
  	box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
  	-moz-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
  	-webkit-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
  	-o-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
  	-ms-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
  }
</style>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script>

  $("#checkToValidate").on("click", function() {
    var firstPassword = $("#newPass_1").val();
    var secondPassword = $("#newPass_2").val();

    if (firstPassword == secondPassword && firstPassword != "")
    {
      $("#formulaire").submit();
    }
    else if (firstPassword != "")
    {
      alert("Les mots de passes ne correspondent pas...");
    }
    else
    {
      alert("Vous devez rentrer un mot de passe");
    }
  });


</script>
<?php include("mobile_footer.php"); ?>
