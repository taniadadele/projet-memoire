<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 by Thomas Dazy (contact@thomasdazy.fr)

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
 *		module   : notes_barcode.htm
 *		projet   : Page d'import des copies pour traitement en lot
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 10/07/19
 */

?>

<?php
  $elementsToGetInPost = array('validate', 'IDmatiere', 'IDuv');
  foreach ($elementsToGetInPost as $value)
    if (isset($_POST[$value])) $$value = $_POST[$value]; else $$value = '';


  if (isset($_GET['action'])) $action = $_GET['action'];
  else $action = "";

  // La fonction suivante à été déplacée dans la page include/fonction/ajax/user_new.php car dans cette page il n'y a pas de contrôle de session
  // if ($action == "finishedLoading") setParam('isCurrentlyWorking', '2');

  // ---------------------------------------------------------------------------
  // Fonction: Génère le répertoire de l'utilisateur lors de l'arrivée sur la
  //           page si celui-ci n'en a pas dans le dossier
  //           download/copies/files/id_de_l'utilisateur
  // ---------------------------------------------------------------------------
  $CnxID = $_SESSION['CnxID'];
  // On crée le répertoire de l'utilisateur s'il n'existe pas déjà
  if (!file_exists("$DOWNLOAD/copies/files/$CnxID")) {
    mkdir("$DOWNLOAD/copies/files/$CnxID", 0777, true);
    mkdir("$DOWNLOAD/copies/files/$CnxID/thumbnail", 0777, true);
  }
  // On crée le fichier index.htm à la racine de ce même répertoire s'il n'existe pas déjà
  $fh = fopen("$DOWNLOAD/copies/files/$CnxID/index.htm", 'w');
  $fh = fopen("$DOWNLOAD/copies/files/$CnxID/thumbnail/index.htm", 'w');
  // ---------------------------------------------------------------------------

  $error = $upload = "";

  if ($validate == "true" && getParam('isCurrentlyWorking') == 0)
  {
    if (isset($IDmatiere) && $IDmatiere != "" && $IDmatiere != 0) $fileName = '0_'.$IDmatiere.'.pdf';
    elseif (isset($IDuv) && $IDuv != "" && $IDuv != 0) $fileName = $IDuv.'_0.pdf';
    else $error = 'nouvorpma';

    // ---------------------------------------------------------------------------
    // Fonction: On récupère la copie qui à été envoyée
    // ---------------------------------------------------------------------------
    $query = "SELECT _IDimage, _title, _ext FROM images WHERE _ID = '".$_SESSION['CnxID']."' AND `_type` = 'copies' LIMIT 1 ";
    $result = mysqli_query($mysql_link, $query);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $fileLocaton = $DOWNLOAD."/copies/files/".$_SESSION['CnxID']."/".$row[0].".".$row[2];
    }
    // ---------------------------------------------------------------------------

    if ($error == "")
    {
      // ---------------------------------------------------------------------------
      // Fonction: Connexion en CURL pour lancer le traitement des différents
      //           fichiers envoyés
      // ---------------------------------------------------------------------------
      $CnxID = $_SESSION['CnxID'];

      $file = $fileLocaton;
      $remote_file = $COPIES_REMOTE_FILE.$fileName;
      $ftp_server = $COPIES_FTP_SERVER;
      $ftp_user_name = $COPIES_FTP_USER;
      $ftp_user_pass = $COPIES_FTP_PASS;

      // Mise en place d'une connexion basique
      $conn_id = ftp_connect($ftp_server);

      // Identification avec un nom d'utilisateur et un mot de passe
      $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
      ftp_pasv( $conn_id, true );
      // Charge un fichier
      if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
       $upload = "success";
       setParam('isCurrentlyWorking', 1);
      } else {
       $upload = "error";
      }
      // Fermeture de la connexion
      ftp_close($conn_id);
      // ---------------------------------------------------------------------------



      // ---------------------------------------------------------------------------
      // Fonction: Connexion en CURL en asynchrone pour lancer le traitement
      //           des différents fichiers envoyés
      // ---------------------------------------------------------------------------
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $COPIES_CURL_SCRIPT."?IDmatiere=$IDmatiere&IDuv=$IDuv&userID=$userID");
      curl_setopt($ch, CURLOPT_USERAGENT, 'api');
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch,  CURLOPT_RETURNTRANSFER, false);
      curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
      curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
      curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
      curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
      $data = curl_exec($ch);
      curl_close($ch);



    }
  }
  elseif (getParam('isCurrentlyWorking') == 1)
  {
    $error = "alreadyWorking";
  }
  elseif (getParam('isCurrentlyWorking') == 2)
  {
    $error = "finishedWorking";
    $upload = "finishedWorking";
  }

?>

<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
    <b>
  		<?php print($msg->read($NOTES_IMPORT_TITLE)); ?>
    </b>
	</div>
</div>

<hr />


  <div class="maincontent">

  <?php

    switch ($error) {
      case 'filetype':
        $errorTxt = "Le fichier transmis n'est pas un fichier PDF";
        break;
      case 'nouvorpma':
        $errorTxt = "Aucuns UV ou matières n'a été renseigné";
        break;
      case 'alreadyWorking':
        $errorTxt = "Un traitement est déjà en cours, merci de patienter";
        break;
      case 'finishedWorking':
        $errorTxt = "Un traitement est terminé.";
        break;
      default:
        $errorTxt = "";
        break;
    }

    if ($errorTxt != "" && $upload != "finishedWorking")
    {
      echo "
        <div class=\"alert alert-danger\" style=\"text-align: center;\">
          <strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".$errorTxt."
        </div>
      ";
    }
    if ($upload == "success")
    {
      echo "
        <div class=\"alert alert-success\" style=\"text-align: center;\">
          <strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;Le fichier a bien été envoyé<br>Vous recevrez une notification quand le traitement sera terminé
        </div>
      ";
    }
    elseif ($upload == "error")
    {
      echo "
        <div class=\"alert alert-danger\" style=\"text-align: center;\">
          <strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;Il y a eu une erreur lors de l'upload du fichier
        </div>
      ";
    }
    elseif ($upload == "finishedWorking")
    {
      echo "
        <div class=\"alert alert-success\" style=\"text-align: center;\">
          <strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".$errorTxt."
        </div>
      ";
    }
    if ($_GET['action'] == "confirm" && getParam('isCurrentlyWorking') == 0)
    {
      echo "
        <div class=\"alert alert-success\" style=\"text-align: center;\">
          <strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;Envoi réussi
        </div>
      ";
    }
  ?>
<?php if (getParam('isCurrentlyWorking') == 0) { ?>

    <table class="width100">
      <tr>
        <td colspan="2"><h3>1. Choisissez une matière/UV</h3></td>
      </tr>
      <tr><td colspan="2"><hr></td></tr>
      <form id="formulaire" action="" method="post" enctype="multipart/form-data">
        <tr id="tr_IDmatiere">
          <td class="align-right" style="width: 15%;">
            <?php print($msg->read($NOTES_CHOOSEMATTER)); ?>
          </td>
          <td>
            <div style="width: 50%;">
              <?php if ($IDuv == "") echo showPMAList("IDmatiere", $IDmatiere); ?>
            </div>
          </td>
        </tr>

        <tr id="tr_IDuv">
          <td class="align-right" style="width: 15%;">
            <span id="or_IDuv"><strong>ou </strong></span>choisissez un UV :
          </td>
          <td style="width: 85%; padding-top: 20px;">
            <label for="IDuv">
              <select id="IDuv" name="IDuv">
                <option value=""></option>
                <?php
                  $query  = "SELECT `_ID_exam`, `_nom` FROM `campus_examens` ";
                  $result = mysqli_query($mysql_link, $query);
                  $row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
                  while ( $row ) {
                    $select = ( $IDuv == $row[0] ) ? "selected=\"selected\"" : "" ;
                    print("<option value=\"$row[0]\" $select>$row[1]</option>");
                    $row = remove_magic_quotes(mysqli_fetch_row($result));
                  }
                ?>
              </select>
            </label>
          </td>
        </tr>

        <tr class="upoadTitle">
          <td colspan="2" ><div style="margin-top: 40px;"><h3>2. Importez un fichier</h3></div></td>
        </tr>
        <tr class="upoadTitle"><td colspan="2"><hr></td></tr>


        <tr>
          <td colspan="2">
            <input type="hidden" name="validate" value="true">
      </form>


            <div id="upload_div" style="opacity: 0;">


            <table class="width100" style="margin-top: 60px; border-top: 1px solid #ccc">
            	<tr>
            		<td>
            			<!-- Bootstrap styles -->
            			<link rel="stylesheet" href="css/bootstrap.min.css">
            			<!-- blueimp Gallery styles -->
            			<link rel="stylesheet" href="css/blueimp-gallery.min.css">
            			<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
            			<link rel="stylesheet" href="css/jquery.fileupload.css">
            			<link rel="stylesheet" href="css/jquery.fileupload-ui.css">
            			<!-- CSS adjustments for browsers with JavaScript disabled -->
            			<noscript><link rel="stylesheet" href="css/jquery.fileupload-noscript.css"></noscript>
            			<noscript><link rel="stylesheet" href="css/jquery.fileupload-ui-noscript.css"></noscript>

            			<div style="background-color: white; padding: 10px">
            				<!-- The file upload form used as target for the file upload widget -->
            				<form id="fileupload" action="index.php" method="POST" enctype="multipart/form-data">
            					<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
            					<div class="row fileupload-buttonbar" style="margin-left: 5px">
            						<div class="col-lg-7">

            							<div style="display: inline-block; float: left; width: 85%">
            								<!-- The fileinput-button span is used to style the file input field as button -->
            								<span class="btn btn-default fileinput-button" style="width: 100%; border: grey 3px dashed; line-height: 28px;">
            									<i class="glyphicon glyphicon-plus"></i>
            									<span>Ajouter un fichier</span>
            									<input type="file" name="files">
            								</span>
            								<button type="reset" class="btn btn-warning cancel" style="display: none;">
            									<i class="glyphicon glyphicon-ban-circle"></i>
            									<span>Annuler</span>
            								</button>
            							</div>

            							<div style="display: inline-block; float: right; margin-right: 10px;">
            								<button type="button" class="btn btn-default delete" style="margin-left: 10px;">
            									<i class="fa fa-trash-o"></i>
            									<!-- <span>Supprimer</span> -->
            								</button>

            								<input type="checkbox" class="toggle">
            							</div>
            <br><br>
            							<!-- The global file processing state -->
            							<span class="fileupload-process"></span>
            						</div>
            						<!-- The global progress state -->
            						<div class="col-lg-5 fileupload-progress fade">
            							<!-- The global progress bar -->
            							<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
            								<div class="progress-bar progress-bar-success" style="width:0%;"></div>
            							</div>
            							<!-- The extended global progress state -->
            							<div class="progress-extended">&nbsp;</div>
            						</div>
            					</div>
            					<!-- The table listing the files available for upload/download -->
            					<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
            				</form>

            			</div>
            			<!-- The blueimp Gallery widget -->
            			<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
            				<div class="slides"></div>
            				<h3 class="title"></h3>
            				<a class="prev"></a>
            				<a class="next"></a>
            				<a class="close">×</a>
            				<a class="play-pause"></a>
            				<ol class="indicator"></ol>
            			</div>
            			<!-- The template to display files available for upload -->
            			<script id="template-upload" type="text/x-tmpl">
            			{% for (var i=0, file; file=o.files[i]; i++) { %}
            				<tr class="template-upload fade">
            					<td>
            						<span class="preview">
            							{% if (file.thumbnailUrl) { %}
            								<a href="{%=file.url%}" title="{%=file.name%}"><img src="{%=file.thumbnailUrl%}"></a>
            							{% } %}
            						</span>
            					</td>
            					<td>
            						<p class="name">{%=file.name%}</p>
            						<strong class="error text-danger"></strong>
            					</td>
            					<td>
            						<p class="size">Processing...</p>
            						<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            					</td>
            					<td>
            						{% if (!i && !o.options.autoUpload) { %}
            							<button class="btn btn-primary start" disabled>
            								<i class="glyphicon glyphicon-upload"></i>
            								<span>Start</span>
            							</button>
            						{% } %}
            						{% if (!i) { %}
            							<button class="btn btn-warning cancel">
            								<i class="glyphicon glyphicon-ban-circle"></i>
            								<span>Cancel</span>
            							</button>
            						{% } %}
            					</td>
            				</tr>
            			{% } %}
            			</script>
            			<!-- The template to display files available for download -->
            			<script id="template-download" type="text/x-tmpl">
            			{% for (var i=0, file; file=o.files[i]; i++) { %}
            				<tr class="template-download fade">
            					<td>
            						<span class="preview">
            							{% if (file.thumbnailUrl) { %}
            								<a href="{%=file.url%}" title="{%=file.name%}"><img src="{%=file.thumbnailUrl%}" style="max-height: 70px;"></a>
            							{% } %}
            						</span>
            					</td>
            					<td>
            						<p class="name">
            							{% if (file.url) { %}
            								<a href="{%=file.url%}" title="{%=file.name%}">{%=file.name%}</a>
            							{% } else { %}
            								<span>{%=file.name%}</span>
            							{% } %}
            						</p>
            						{% if (file.error) { %}
            							<div><span class="label label-danger">Error</span> {%=file.error%}</div>
            						{% } %}
            					</td>
            					<td>
            						<span class="size">{%=o.formatFileSize(file.size)%}</span>
            					</td>
            					<td style="text-align: right;">
            						{% if (file.deleteUrl) { %}
            							<button class="btn btn-default delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
            								<i class="fa fa-trash-o"></i>
            							</button>
            							<input type="checkbox" name="delete" value="1" class="toggle">
            						{% } else { %}
            							<button class="btn btn-warning cancel">
            								<i class="glyphicon glyphicon-ban-circle"></i>
            								<span>Annuler</span>
            							</button>
            						{% } %}
            					</td>
            				</tr>
            			{% } %}
            			</script>
            			<script src="script/jquery.min.vupload.js"></script>
            			<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
            			<script src="js/vendor/jquery.ui.widget.js"></script>
            			<!-- The Templates plugin is included to render the upload/download listings -->
            			<script src="js/tmpl.min.js"></script>
            			<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
            			<script src="js/load-image.all.min.js"></script>
            			<!-- The Canvas to Blob plugin is included for image resizing functionality -->
            			<script src="js/canvas-to-blob.min.js"></script>
            			<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
            			<!-- Ne pas inclure car conflit avec le menu de sélection de liaison pôle matière année. -->
            			<!-- <script src="js/bootstrap.min.js"></script> -->
            			<!-- blueimp Gallery script -->
            			<script src="js/jquery.blueimp-gallery.min.js"></script>
            			<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
            			<script src="js/jquery.iframe-transport.js"></script>
            			<!-- The basic File Upload plugin -->
            			<script src="js/jquery.fileupload.js"></script>
            			<!-- The File Upload processing plugin -->
            			<script src="js/jquery.fileupload-process.js"></script>
            			<!-- The File Upload image preview & resize plugin -->
            			<script src="js/jquery.fileupload-image.js"></script>
            			<!-- The File Upload audio preview plugin -->
            			<script src="js/jquery.fileupload-audio.js"></script>
            			<!-- The File Upload video preview plugin -->
            			<script src="js/jquery.fileupload-video.js"></script>
            			<!-- The File Upload validation plugin -->
            			<script src="js/jquery.fileupload-validate.js"></script>
            			<!-- The File Upload user interface plugin -->
            			<script src="js/jquery.fileupload-ui.js"></script>
            			<!-- The main application script -->
            			<script>
            			var numfiche  = <?php echo $_SESSION['CnxID'] ?>;
            			var typefiche = "copies";
            			var urlupload = "<?php echo substr($_SERVER["HTTP_REFERER"], 0, strpos($_SERVER["HTTP_REFERER"], "index.php")); ?>";
                  var maxNumberOfFiles = 1;
            			</script>
            			<script src="js/images.js"></script>
            			<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
            			<!--[if (gte IE 8)&(lt IE 10)]>
            			<script src="js/cors/jquery.xdr-transport.js"></script>
            			<![endif]-->
            			<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery-ui.min.js"></script>
            			<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/tag-it.pmt.js"></script>
            			<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery.ui.autocomplete.html.js"></script>
            			<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery.desoform.js"></script>
            		</td>
            	</tr>
            </table>



            </div>






          </td>
        </tr>
        <tr>
          <td></td>
          <td id="sendButton"><button type="button" id="toValidate" class="btn btn-success">Envoyer</button></td>
        </tr>



    </table>

  <?php }


  else
  {
    if (getParam('isCurrentlyWorking') == 2)
    { ?>

      <div style="width: 100%; text-align: center;">
        <a href="index.php?item=60&cmde=barcode" class="btn btn-default">Cliquez ici pour voir les résultats</a>

      </div>


      <?php
    }
  }


  ?>


  </div>

<script>
  jQuery("#IDmatiere").on("change", function() {
    checkToShowHideSelects();
  });
  function selectPMAChanged() {
    checkToShowHideSelects();
  }
  jQuery("#IDuv").on("change", function() {
    checkToShowHideSelects();
  });

  function checkToShowHideSelects() {
    var matiereSelected = jQuery("#IDmatiere").val();
    var UVSelected = jQuery("#IDuv").val();

    jQuery("#tr_IDmatiere").show();
    jQuery("#tr_IDuv").show();
    jQuery("#or_IDuv").show();
    jQuery("#upload_div").css('opacity', 0);
    jQuery(".upoadTitle").hide();
    jQuery("#sendButton").hide();

    if (matiereSelected != 0 && matiereSelected != null)
    {
      jQuery("#IDuv").val(0);
      jQuery("#tr_IDuv").hide();
      jQuery("#tr_IDmatiere").show();
      jQuery("#upload_div").css('opacity', 1);
      jQuery(".upoadTitle").show();
      jQuery("#sendButton").show();
    }
    else if (UVSelected != 0 && UVSelected != null)
    {
      jQuery("#IDmatiere").val(0);
      // jQuery("#IDmatiere").hide();
      jQuery("#tr_IDmatiere").hide();
      jQuery("#tr_IDuv").show();
      jQuery("#or_IDuv").hide();
      jQuery("#upload_div").css('opacity', 1);
      jQuery(".upoadTitle").show();
      jQuery("#sendButton").show();
    }
  }

  jQuery(document).ready(function() {
    checkToShowHideSelects();
  });



  jQuery("#toValidate").on("click", function() {
    var matiereSelected = jQuery("#IDmatiere").val();
    var UVSelected = jQuery("#IDuv").val();
    if (matiereSelected == 0 && UVSelected == 0)
    {
      alert("Vous devez sélectionner une matière ou un UV");
    }
    else
    {
      jQuery("#formulaire").submit();
    }
  })

  // On limite le nombre d'upload à 1
  $('#fileupload').fileupload({
      maxNumberOfFiles: 1
  });
</script>
