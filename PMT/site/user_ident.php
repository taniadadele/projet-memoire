<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)

   This file is part of PromÃ©thÃ©e.

   PromÃ©thÃ©e is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   PromÃ©thÃ©e is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PromÃ©thÃ©e.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/

/*
 *		module   : user_ident.php
 *		projet   : la page d'identification
 *
 *		version  : 1.3
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 20/03/03 - par D. Laporte
 *                     champ de saisie en hidden (sÃ©curitÃ©)
 *		           26/11/05 - par D. Laporte
 *                     mode maintenance
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *							 21/10/2019 - par Thomas Dazy (contact@thomasdazy.fr)
 *							 		 Refonte du système de connexion pour inclure une utilisation de cookie et l'ajout de logs
 *					     29/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7 et maj du thème
 */



  // ---------------------------------------------------------------------------
  // Pour le débug
  // ---------------------------------------------------------------------------
  // On vérifie que le dossier log/année/mois/jour existe sinon on le crée
  if (!is_dir('log/'.date('Y'))) mkdir('log/'.date('Y'));
  if (!is_dir('log/'.date('Y').'/'.date('m'))) mkdir('log/'.date('Y').'/'.date('m'));
  if (!is_dir('log/'.date('Y').'/'.date('m').'/'.date('d'))) mkdir('log/'.date('Y').'/'.date('m').'/'.date('d'));

  $myfile = fopen('log/'.date('Y').'/'.date('m').'/'.date('d').'/'.date('Y-m-d H-i-s')." log.txt", "w") or die("Unable to open file!");
  $txt = "Identification\n";
  // fwrite($myfile, $txt);
  //
  // $txt = "DATE: ".date('Y-m-d H:i:s')."\n";
  // $txt .= "ID USER: ".@$_SESSION['LOGS_ID']."\n";
  // $_SESSION['LOGS_ID'] = '';
  // $txt .= "\n";
  // fwrite($myfile, $txt);
  //
  // $txt = "Headers:\n";
  // foreach (getallheaders() as $key => $value) {
  //   $txt .= $key." - ".$value."\n";
  // }
  // $txt .= "\n";
  // fwrite($myfile, $txt);
  //
  // $txt = "SESSION:\n";
  // foreach ($_SESSION as $key => $value) {
  //   if (substr($key, 0, 5) != 'POST_' && !is_array($value)) $txt .= $key." - ".$value."\n";
  //   elseif (substr($key, 0, 5) != 'POST_')$txt .= $key." - ".print_r($value)."\n";
  // }
  // $txt .= "\n";
  // fwrite($myfile, $txt);
  //
  // $txt = "POST:\n";
  // foreach ($_SESSION as $key => $value) {
  //   if (substr($key, 0, 5) == 'POST_') $txt .= $key." - ".$value."\n";
  //   if (substr($key, 0, 5) == 'POST_') $_SESSION[$key] = '';
  // }
  // $txt .= "\n";
  // fwrite($myfile, $txt);
  //
  //
  // fclose($myfile);
 // ---------------------------------------------------------------------------

// remarque : login automatique si les variables ID utilisateur $id et mot de passe $pwd sont renseignÃ©es
$id       = ( @$_POST["id"] )		// ID utilisateur
	? $_POST["id"]
	: @$id ;

$pwd      = ( @$_POST["pwd"] )	// mot de passe
	? $_POST["pwd"]
	: @$pwd ;


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
              // switch ($item) {
              //   case 'resetpwdrequest':
              //   case 'resetpwdform':
              //     $image = 'forgot_passwd.svg';
              //     break;
              //   case 'signup': $image = 'welcome.svg'; break;
              //   default: $image = 'login.svg'; break;
              // }

              $image = 'login.svg';
            ?>

            <div class="col-lg-6 d-none d-lg-block">
              <img src="images/ident/<?php echo $image; ?>" class="col-12" id="login_image" style="opacity: 0;">
            </div>


            <div class="col-lg-6">
              <div class="p-5">
                <div class="text-center">
                  <h1 class="h4 text-gray-900 mb-4">Bienvenue</h1>
                </div>
                <form class="user" id="loginform" action="index.php" method="post">

                  <?php
                    if (isset($_GET['validpwd']) && $_GET['validpwd'] == "on") echo '<div class="alert alert-success" style="text-align: center;"><strong>'. $msg->read($USER_PASSWORD_MODIF_VALID) .'</strong></div>';
                    if (isset($_GET['validpwd']) && $_GET['validpwd'] == "mail_send") echo '<div class="alert alert-success" style="text-align: center;"><strong>'. $msg->read($USER_PASSWORD_MODIF_MAIL) .'</strong></div>';
                    if (isset($loginMessage) && $loginMessage == "bad_ident") echo '<div class="alert alert-danger" style="text-align: center;"><strong>Erreur: </strong>Mauvais identifiant ou mot de passe</div>';
                  ?>



                  <?php
            				$path = "";
            				foreach ($_GET as $key => $value) {
            					if ($key != "item") $path .= "||1".$key."||2".$value;
            					else
            					{
            						if ($_GET[$key] != -1) $path .= "||1".$key."||2".$value;
            					}
            				}

            			?>

            			<input type="hidden" name="pathToGo" value="<?php echo $path; ?>">

                  <?php
                    require RESSOURCE_PATH['PAGES_FOLDER'].'ident/login_form.php';
                  ?>

                </form>
                <hr>
                <div class="text-center">
                  <a class="small" href="index.php?item=1&page=rstpswd"><?php echo $msg->read($USER_LOSTPASSWD); ?></a>
                </div>
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
