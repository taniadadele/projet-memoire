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
 *		module   : user_lost.php
 *		projet   : la page de récupération des mots de passe oubliés
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 18/04/07
 *		modif    :
 */

//!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
if ( is_file($_SESSION["ROOTDIR"]."/msg/mail.php") )
  require "msg/mail.php";
$msg_mail  = new TMessage("msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
$msg_mail->msg_mail_search  = $keywords_search;
$msg_mail->msg_mail_replace = $keywords_replace;


if ($_GET['page'] != "rstpswd")
{
  echo "<meta http-equiv=\"refresh\" content=\"0;URL=index.php\">";
}

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
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      // On construit le lien à envoyer
      $newSecretToken = genererMDP(25);

      $newSignature = "[".$newSecretToken."][".time()."]";

      // On met à jour le champ _signature de l'utilisateur avec le token et le timestamp actuel
      $update_query = "UPDATE `user_id` SET `_signature`= '".$newSignature."' WHERE `_ID` = '".$row[0]."' ";
      @mysqli_query($mysql_link, $update_query) or die('Erreur SQL !<br>'.$update_query.'<br>'.mysqli_error($mysql_link));

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
      // if (getParam("MAIL_DEV_MODE") != "") $destinataire = getParam("MAIL_DEV_MODE");

      // On envois le mail
      mail($destinataire, $subject, $body, $headers);
      echo "<meta http-equiv=\"refresh\" content=\"0;URL=index.php?validpwd=mail_send\">";
    }
    if (!isset($destinataire)) $error_ident = true;

  }



  function getUserIdFromToken ($token)
  {
    global $mysql_link;
    $toReturn = 0;
    $query = "SELECT `_ID` FROM `user_id` WHERE `_signature` LIKE '%[".$token."]%' ";
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $toReturn = $row[0];
    }
    return $toReturn;
  }


  function isTimedOutLink ($token)
  {
    global $mysql_link;
    $timeOutTimeMin = getParam('forgot_password_timeout');
    $timeOutTimeSec = $timeOutTimeMin * 60;

    // On récupère le timeout du lien:
    $query = "SELECT `_signature` FROM `user_id` WHERE `_ID` = '".getUserIdFromToken($token)."' ";
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
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
        @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
        echo "<meta http-equiv=\"refresh\" content=\"0;URL=index.php?validpwd=on\">";
      }
    }
  }

?>





<div class="container">
  <!-- Outer Row -->
  <div class="row justify-content-center">

    <div class="col-xl-10 col-lg-12 col-md-9">

      <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
          <!-- Nested Row within Card Body -->
          <div class="row">


            <?php
              $image = 'forgot_passwd.svg';
            ?>

            <div class="col-lg-6 d-none d-lg-block">
              <img src="images/ident/<?php echo $image; ?>" class="col-12" id="login_image" style="opacity: 0;">
            </div>


            <div class="col-lg-6">
              <div class="p-5">
                <div class="text-center">
                  <h1 class="h4 text-gray-900 mb-4"><?php echo $msg->read($USER_LOSTPWD); ?></h1>
                </div>
                <form class="user" id="forgotpwdform" action="index.php?item=1&page=rstpswd" method="post">

                  <?php
                    if (isset($_GET['validpwd']) && $_GET['validpwd'] == "on") echo '<div class="alert alert-success" style="text-align: center;"><strong>'. $msg->read($USER_PASSWORD_MODIF_VALID) .'</strong></div>';
                    if (isset($_GET['validpwd']) && $_GET['validpwd'] == "mail_send") echo '<div class="alert alert-success" style="text-align: center;"><strong>'. $msg->read($USER_PASSWORD_MODIF_MAIL) .'</strong></div>';
                    if (isset($loginMessage) && $loginMessage == "bad_ident") echo '<div class="alert alert-danger" style="text-align: center;"><strong>Erreur: </strong>Mauvais identifiant ou mot de passe</div>';
                  ?>



                  <?php
                    if (isset($error_ident) && $error_ident) echo "<div class=\"alert alert-danger\" style=\"text-align: center;\"><strong>". $msg->read($USER_PASSWORD_UNKNOWN_IDENT) ."</strong></div>";
                  ?>
                  <?php
                    if (!isset($_GET['token']))
                      require RESSOURCE_PATH['PAGES_FOLDER'].'ident/forgot_password_send_mail_form.php';
                    else
                      require RESSOURCE_PATH['PAGES_FOLDER'].'ident/forgot_password_new_password_form.php';
                  ?>

                </form>
                <hr>
                <?php if (getParam('showCreateAccountBtn')) { ?>
                  <div class="text-center">
                    <a class="small" href="index.php?item=1000"><?php echo $msg->read($USER_CREATEACCOUNT); ?></a>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>



<script>
// On met l'image au centre du bloc (avec une petite animation)
  $(document).ready(function(){
    setTimeout(function(){
      checkImgMargin();
      $('#login_image').hide().css('opacity', 1).fadeIn();
      $('#maintenance_title').fadeIn();
    }, 500);
  });

  $(document).on('click', function(){
    checkImgMargin();
  })

  function checkImgMargin() {
    var parentHeight = $('#login_image').parent().height();
    var imgHeight = $('#login_image').height();
    $('#login_image').css('margin-top', '0');
    $('#login_image').css('margin-top', ((parentHeight - imgHeight) / <?php if (getParam('OFFLINE')) echo '2.25'; else echo '2'; ?>));
  }
</script>








<div class="card form-signin" style="display: none;">
  <div class="card-body">
  	<form id="formulaire" action="index.php?item=1&page=rstpswd" method="post">
      <!-- Partie pour le logo -->
      <div class="align-center">
      <?php // Logo du drapeau ?>
      <?php // $msg->languageBanner("msg"); ?>
      <?php echo $my_logo ?><br />
      <?php print($msg->read($USER_LOGIN)); ?>
      </div>
      <br />

      <div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat; margin-bottom: 10px;">
      	<div style="text-align: center;">
      		<?php print($msg->read($USER_LOSTPWD)); ?>
      	</div>
      </div>
      <?php
        if (isset($_GET['token'])) echo "<center>Veuillez entrer un nouveau mot-de-passe</center>";
      ?>

  		<?php
  			if ( isset($myret) && $myret )
  				print("<p style=\"text-align: center; font-weight: bold; color: red;\">$myret</p>");
  		?>

      <?php
        if (isset($error_ident) && $error_ident) echo "<div class=\"alert alert-danger\" style=\"text-align: center;\"><strong>". $msg->read($USER_PASSWORD_UNKNOWN_IDENT) ."</strong></div>";
      ?>

      <table class="width100">
        <?php if (!isset($_GET['token']))
        {
          echo '
          <tr>
            <td class="align-left">
              <div class="form-group">
                <label for="ident">'.$msg->read($USER_USERID_OR_EMAIL).'</label>
                <input type="text" class="form-control" id="ident" name="ident" required>
              </div>
            </td>
          </tr>';
        }
        ?>

        <?php
          if (isset($_GET['token']))
          {
            if (!$timedout)
            {
              echo '
              <tr>
                <td class="align-left">
                  <!-- <label style="width: 100%\" for=\"newPass_1\"><input type=\"password\" id=\"newPass_1\" name=\"newPass_1\" size=\"20\" class=\"input-block-level\" placeholder=\"".$msg->read($USER_PASSWORD)."\" required /></label> -->

                  <div class="form-group">
                    <label for="newPass_1">'.$msg->read($USER_PASSWORD).'</label>
                    <input type="password" class="form-control" id="newPass_1" name="newPass_1" required>
                  </div>

                </td>
              </tr>
              ';
              echo '
              <tr>
                <td class="align-left">
                  <!-- <label style=\"width: 100%\" for=\"newPass_2\"><input type=\"password\" id=\"newPass_2\" name=\"newPass_2\" size=\"20\" class=\"input-block-level\" placeholder=\"".$msg->read($USER_PASSWORD)."\" required /></label> -->


                  <div class="form-group">
                    <label for="newPass_2">'.$msg->read($USER_PASSWORD).'</label>
                    <input type="password" class="form-control" id="newPass_2" name="newPass_2" required>
                  </div>

                </td>
              </tr>
              ';


              echo "<input type=\"hidden\" name=\"token\" value=\"".$_GET['token']."\">";
            }
            else
            {
              echo "
                <div class=\"alert alert-error\" style=\"text-align: center;\"><strong>".$msg->read($USER_TIMEOUT_LINK)."</strong></div>
              ";
            }
          }
        ?>
      </table>

  		<!-- <hr /> -->
      <div style="text-align: left; display: inline-block;">
        <a href="index.php" class="btn btn-danger btn-large" > < <?php print($msg->read($USER_BACK_BTN)); ?></a>
      </div>
  		<div style="text-align: right; display: inline-block; float: right;">
        <?php
          if (isset($_GET['token']))
          {
            if (!$timedout)
            {
              echo "<button class=\"btn btn-large\" type=\"button\" id=\"checkToValidate\">".$msg->read($USER_INPUTOK)."</button>";
            }
          }
          else
          {
            echo "<button class=\"btn btn-large\" type=\"submit\" name=\"valid\">".$msg->read($USER_INPUTOK)."</button>";
          }
        ?>

  		</div>
  	</form>
  </div>
</div>
<!-- <style>
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
</style>


<script>
  jQuery("#checkToValidate").on("click", function() {
    var firstPassword = jQuery("#newPass_1").val();
    var secondPassword = jQuery("#newPass_2").val();
    if (firstPassword == secondPassword && firstPassword != "")
    {
      jQuery("#formulaire").submit();
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


</script> -->
