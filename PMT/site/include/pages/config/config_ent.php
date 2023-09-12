<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)

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
 *		module   : config_ent.php
 *		projet   : paramétrage de l'interface intranet
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 25/01/03
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */

 // On vérifie que l'on soit bien un super-administrateur
 if ($_SESSION['CnxAdm'] != 255) header('Location: index.php');

$IDconf = ( @$_POST["IDconf"] )		// ID de la configuration
	? (int) $_POST["IDconf"]
	: (int) (@$_GET["IDconf"] ? $_GET["IDconf"] : $_SESSION["CfgID"]) ;
$IDmime = ( @$_POST["IDmime"] )		// ID du mime
	? (int) $_POST["IDmime"]
	: (int) @$_GET["IDmime"] ;
$mime   = @$_POST["mime"];			// description mime
$ext    = @$_POST["ext"];			// extension mime
$newdoc = (int) @$_GET["newdoc"];		// ajout d'un document

$submit = @$_POST["submit"];			// bouton de validation

if (isset($_GET['admin'])) $showAdmin = $_GET['admin'];

if (isset($showAdmin) && $showAdmin == "on") $showAdmin = "";
else $showAdmin = "display: none;";
?>


<?php
	// l'utilisateur a validé
	switch ( $submit ) {
		case "Valider" :
			require_once "include/rm.php";
			require_once "include/config.php";

			// connexion persistante
			$persist = ( @$_POST["persist"] ) ? 1 : 0 ;
			// création de comptes
			$cb      = @$_POST["cbgrp"];			// groupes utilisateurs
			$cbgrp   = 0;
			for ($i = 0; $i < count($cb); $i++)
				$cbgrp += (int) @$cb[$i];

			$authusr = ( @$_POST["authusr"] ) ? 1 : 0 ;

			// validité des comptes
			$account = (int) @$_POST["account"];

			// filtrage IP
			$filtre  = ( @$_POST["filtre"] ) ? 1 : 0 ;

			// mode Debug
			$debug   = ( @$_POST["debug"] ) ? 1 : 0 ;

			// mode Demo
			$demo    = ( @$_POST["demo"] ) ? 1 : 0 ;

			// longueur des mdp
			$usrpwd  = (int) @$_POST["usrpwd"];

			// expiration des pages
			$delay   = @$_POST["delay"] * 60;

			// taille des fichiers
			$size    = @$_POST["size"] * 1000;

			// taille du quotas utilisateur
			$hdsz    = @$_POST["hdsz"] * 1000;

			// durée des logs
			$log     = @$_POST["log"] * 24 * 3600;

			// durée des stats
			$stats   = @$_POST["stats"] * 24 * 3600;

			// durée des liens par mail
			$link    = @$_POST["link"] * 24 * 3600;

			// durée des post-it
			$postit  = @$_POST["postit"] * 7 * 24 * 3600;

			//---- mise à jour du fichier de configuration ----
			writeconfigfile("config.ini", "config.php",
				$SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $_POST["charset"],
				$DOWNLOAD, $persist, $filtre, $debug, $demo, $usrpwd, $delay, $size, $hdsz,
				$log, $stats, @$_POST["data"], @$_POST["page"], @$_POST["recent"], $link, @$_POST["flash"], "$authusr:$cbgrp",
				@$_POST["spacing"], @$_POST["menu"], $account, $postit, @$_POST["sms"], @$_POST["smspwd"],
				@$_POST["timezone"]);

			//---- mise à jour des documents autorisés ----
			if ( mysqli_query($mysql_link, "update config_mime set _visible = 'N'") ) {
				$cb = @$_POST["cb"];
				for ($i = 0; $i < count($cb); $i++)
					if ( strlen($cb[$i]) )
						mysqli_query($mysql_link, "update config_mime set _visible = 'O' where _IDmime = '".$cb[$i]."'");
				}

			//---- mise à jour des langues autorisées ----
			$list = Array();
			$cb   = @$_POST["cblang"];

			// ouverture du répertoire des langues
			$myDir = @opendir("msg");

			// lecture des répertoires
			while ( $entry = @readdir($myDir) )
				if ( is_dir("msg/$entry") AND strlen($entry) == 2 )
					array_push($list, $entry);

			// fermeture du répertoire
			@closedir($myDir);

			for ($i = 0; $i < count($list); $i++)
				if ( !@in_array($list[$i], $cb) AND $list[$i] != $LANG ) {
					// attention aux mises à jour
					if ( is_dir("msg/#".$list[$i]) )
						rm("msg/#".$list[$i]);

					@rename("msg/".$list[$i], "msg/#".$list[$i]);
					}

			for ($i = 0; $i < count($cb); $i++)
				if ( strlen($cb[$i]) )
					@rename("msg/#".$cb[$i], "msg/".$cb[$i]);

			print("<script type=\"text/javascript\"> window.location.replace('index.php?item=$item&IDconf=$IDconf', '_self'); </script>");
			break;

		default :
			// création d'un document
			if ( $submit == $msg->read($CONFIG_CREAT) ) {
				$query  = "insert into config_mime ";
				$query .= "values('', '$ext', '".addslashes($mime)."', 'O', '".$_SESSION["lang"]."')";

				mysqli_query($mysql_link, $query);
				}

			// modification d'un document
			if ( $submit == $msg->read($CONFIG_MODIFY) ) {
				$query  = "update config_mime ";
				$query .= "set _ext = '$ext', _mime = '".addslashes($mime)."' ";
				$query .= "where _IDmime = '$IDmime' ";
				$query .= "limit 1";

				mysqli_query($mysql_link, $query);
				}
			break;
		}

	// initialisation
	$persist  = $PERSISTENT;
	$account  = $ACOUNTIME;
	$filtre   = $IPFILTER;
	$debug    = $DEBUG;
	$demo     = $DEMO;
	$charset  = $CHARSET;
	$usrpwd   = $USERPWD;
	$delay    = (int) $TIMELIMIT / 60;
	$size     = (int) $FILESIZE  / 1000;
	$hdsz     = (int) $HDQUOTAS  / 1000;
	$log      = (int) $TIMELOG   / (24 * 3600);
	$stats    = (int) $TIMESTAT  / (24 * 3600);
	$spacing  = $TBLSPACING;
	$menu     = $MENUSKIN;
	$data     = $MAXPAGE;
	$page     = $MAXSHOW;
	$recent   = $MAXRECENT;
	$link     = (int) $TIMELINK / (24 * 3600);
	$mail     = $MAIL;
	$flash    = $FLASH;
	$postit   = (int) $MAXPOST  / (7 * 24 * 3600);
	$sms      = $SMSPROVIDER;
	$smspwd   = $SMSPWD;
	$timezone = $TIMEZONE;

	list($authusr, $cbgrp) = explode(":", $AUTHUSER);
?>







<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($CONFIG_CONFIG); ?></h1>


<form id="formulaire" action="index.php" method="post">
  <?php
    $temp = array('item', 'IDconf', 'IDmime');
    foreach ($temp as $value) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
  ?>
  <input type="hidden" name="submit" value="Valider">

  <?php include("include/config_menu_top.php"); ?>

  <div class="card shadow mb-4">
    <div class="card-header">
      <ul class="nav nav-pills card-header-pills" id="myTab" role="tablist">
        <li class="nav-item" role="presentation"><a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true"><?php echo str_replace(".", "", $msg->read($CONFIG_SECURITY)); ?></a></li>
        <li class="nav-item" role="presentation"><a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><?php echo str_replace(".", "", $msg->read($CONFIG_DATA)); ?></a></li>
        <li class="nav-item" role="presentation"><a class="nav-link" id="tab5-tab" data-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="false">Gestion</a></li>
        <li class="nav-item" role="presentation"><a class="nav-link" id="tab6-tab" data-toggle="tab" href="#tab6" role="tab" aria-controls="tab6" aria-selected="false">Paramètres administrateur</a></li>
        <li class="nav-item" role="presentation"><a class="nav-link" id="tab7-tab" data-toggle="tab" href="#tab7" role="tab" aria-controls="tab7" aria-selected="false">Changement d'année</a></li>
      </ul>
    </div>
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="tab1" role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/ent/config_security.php'); ?></div>
        <div class="tab-pane fade" id="tab2" role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/ent/config_data.php'); ?></div>
        <div class="tab-pane fade" id="tab5" role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/ent/config_gestion.php'); ?></div>
        <div class="tab-pane fade" id="tab6" role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/ent/params/config_param_admin.php'); ?></div>
        <div class="tab-pane fade" id="tab7" role="tabpanel"><?php include(RESSOURCE_PATH['PAGES_FOLDER'].'config/ent/config_change_year.php'); ?></div>
      </div>

      <div class="mt-3">

        <button type="submit" class="btn btn-success"><?php echo $msg->read($CONFIG_INPUTOK); ?></button>
        <a href="index.php" class="btn btn-danger"><?php echo $msg->read($CONFIG_INPUTCANCEL); ?></a>
      </div>
    </div>
  </div>



</form>









<script>
$('.updateParam').click(function () {
	// On récupère le code du paramètre, sa valeur et le commentaire
	var paramCode = $(this).attr("paramCode");
	var inputValue = $("#input_value_" + paramCode).val();
	var inputComment = $("#input_comment_" + paramCode).val();

	$.ajax({
		url : 'include/fonction/ajax/updateParam.php?action=updateParam',
		type : 'POST', // Le type de la requête HTTP, ici devenu POST
		data : 'paramCode=' + paramCode + '&paramValue=' + inputValue + '&paramComment=' + inputComment,

		dataType : 'html', // On désire recevoir du HTML
		success : function(code_html, statut){ // code_html contient le HTML renvoyé
			// alert(code_html);
			if (code_html == "success") {
        Toast.fire({
          icon: 'success',
          title: '<?php echo $msg->read($CONFIG_SUCCESSPARAMUPDATE); ?>'
        });
			}
			else {
        Toast.fire({
          icon: 'danger',
          title: '<?php echo $msg->read($CONFIG_ERRORPARAMUPDATE); ?>'
        });
			}
		}
	});
});

$("#emptyCacheButton").click(function() {

	$.ajax({
		url : 'include/fonction/ajax/clearcache.php?action=clearcache',
		type : 'POST', // Le type de la requête HTTP, ici devenu POST
		data : '',
		dataType : 'html', // On désire recevoir du HTML
		success : function(code_html, statut){ // code_html contient le HTML renvoyé

			if (code_html == "success") {
        Toast.fire({
          icon: 'danger',
          title: '<?php echo $msg->read($CONFIG_SUCCESS_CACHE_CLEAR); ?>'
        });
			}
			else {
        Toast.fire({
          icon: 'danger',
          title: 'Error',
          text: '<?php echo $msg->read($CONFIG_ERROR_CACHE_CLEAR); ?>'
        });
			}
		}
	});

});


$("#sendMailDisponibilityEdt").click(function() {

	$.ajax({
		url : 'include/fonction/ajax/updateWorkflow.php?action=sendRequest',
		type : 'POST', // Le type de la requête HTTP, ici devenu POST
		data : '',
		dataType : 'html', // On désire recevoir du HTML
		success : function(code_html, statut){ // code_html contient le HTML renvoyé
			getNumberOfPeoplesToSendConfirmationMailTo();
      Toast.fire({
        icon: 'success',
        title: 'Envoi réussi'
      });
		}
	});
});

function getNumberOfPeoplesToSendConfirmationMailTo()
{
	$.ajax({
		url : 'include/fonction/ajax/fonction.php?action=getNumberOfTeachersToSendMailTo',
		type : 'POST', // Le type de la requête HTTP, ici devenu POST
		data : '',
		dataType : 'html', // On désire recevoir du HTML
		success : function(code_html, statut){ // code_html contient le HTML renvoyé
			$("#countOfPeoplesToSendMessageTo").html(code_html);
		}
	});
}

$(document).ready(function() {
  getNumberOfPeoplesToSendConfirmationMailTo();
});


$("#reassignPromotionByGraduationYear").click(function() {

$.ajax({
	url : 'include/fonction/ajax/fonction.php?action=reassignPromotionByGraduationYear',
	type : 'POST', // Le type de la requête HTTP, ici devenu POST
	data : '',
	dataType : 'html', // On désire recevoir du HTML
	success : function(code_html, statut){ // code_html contient le HTML renvoyé
    Toast.fire({
      icon: 'success',
      title: 'Calcul réussi'
    });

	}
});


});





</script>


<script>
  // Quand on clique sur le bouton "passage d'année"
	$('#changeYear').on('click', function(){
		if (confirm('Cette opération est dangereuse et définitive, êtes-vous sûr ?'))
		{
			if (confirm('Êtes-vous réellement sûr'))
			{
				$.ajax({
				  url : 'include/fonction/ajax/changeYear.php',             // L'URL de la page appelée
				  type : 'POST',                                            // Le type de la requête HTTP, ici devenu POST
				  data : '#',																			          // Les données
				  // async: true,                                           // Lancer l'appel en asynchrone
				  dataType : 'html',                                        // On désire recevoir du HTML
				  success : function(code_html, statut){                    // Fonction appelée si succes
				    alert('Opération réussie');
						$('#changeYear').hide();
				  }
				});
			}
		}
	})
</script>
