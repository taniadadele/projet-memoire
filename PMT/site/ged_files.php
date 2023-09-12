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
 *		module   : ged_files.php
 *		projet   : la page de gestion des fichiers de l'utilisateur
 *
 *		version  : 1.1
 *		auteur   : Thomas Dazy
 *		creation : 28/12/2018
 *
 */

//
$ID        = ( @$_POST["ID"] )		// ID de l'utilisateur
	? (int) $_POST["ID"]
	: (int) @$_GET["ID"] ;


$submit    = ( @$_POST["submit"] )		// bouton de validation
	? $_POST["submit"]
	: @$_GET["submit"] ;
?>


<?php

if ((!isset($_GET['folderSearch']) || $_GET['folderSearch'] == "") && isset($_GET['path'])) $_SESSION['folder_path'] = $_GET['path'];
else if (isset($_GET['folderSearch'])) $_SESSION['folder_path'] = $_GET['folderSearch'];

if (isset($_GET['fileSearch'])) $_SESSION['selected_file'] = $_GET['fileSearch'];


if (isset($_SESSION['folder_path']) && $_SESSION['folder_path'] == 0) unset($_SESSION['folder_path']);




// Toujours vérifier si le répertoire utilisateur est existant
if(!is_dir("download/ged/files/".$ID))
{
  // Sinon on le crée
	mkdir("download/ged/files/".$ID, 0777, true);
  fopen("download/ged/files/".$ID."/index.php", "w");
}

if ((isset($_GET["ID"]) && $_GET["ID"] === "9999999999") OR (!isset($_GET["ID"]) || $_GET["ID"] == "")) $ID = $_SESSION["CnxID"];


// Ajouter un fichier
if (isset($_GET['action']) && $_GET['action'] == "addFolder")
{
	$folderName = "-".$_GET['element_name'];

	$query  = "SELECT _ext ";
	$query .= "FROM images ";
	$query .= "WHERE _type = 'ged' ";
	$query .= "AND _ID = '".$_SESSION["CnxID"]."' ";
	$query .= "AND _title = '$folderName' ";
	$query .= "AND _parent = '".$_GET['path']."' ";
	$result = mysqli_query($mysql_link, $query);
	if(mysqli_num_rows($result) == 0) {
		$query  = "INSERT INTO images ";
		$query .= "VALUES (NULL, 'ged', '".$_SESSION["CnxID"]."', '".$folderName."', '0', '', NOW(), 'fol', '', '0', '".$_GET['path']."') ";
	}
	$result = mysqli_query($mysql_link, $query);
}

// Renommer un fichier
if (isset($_GET['action']) && $_GET['action'] == "renameFolder")
{
	$query  = "SELECT _ext ";
	$query .= "FROM images ";
	$query .= "WHERE _ID = ".$_SESSION["CnxID"]." ";
	$query .= "AND _IDimage = '".$_GET['element_id']."' ";
	$result = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$extension_element = $row[0];
	}
	if ($extension_element == "fol") $new_name = "-".$_GET['element_name'];
	else $new_name = $_GET['element_name'].".".$extension_element;

	$query = "UPDATE `images` SET `_title` = '".$new_name."' WHERE `_IDimage` = '".$_GET['element_id']."' ";
	$result = mysqli_query($mysql_link, $query);
}

// Déplacer un fichier
if (isset($_GET['action']) && $_GET['action'] == "moveFile")
{
	$element_id = $_GET['element_id'];
	$parent_id = $_GET['parent_id'];
	$query = "UPDATE `images` SET `_parent` = '".$parent_id."' WHERE `_IDimage` = '".$element_id."' ";
	$result = mysqli_query($mysql_link, $query);
}

// Partager un fichier
if (isset($_GET['action']) && $_GET['action'] == "shareFile")
{
	$element_id = $_POST['element_id'];
	$shareArray = array();
	if (isset($_POST['UserTags'])) foreach ($_POST['UserTags'] as $key => $value) array_push($shareArray, "U_".$value);
	if (isset($_POST['ClassTags'])) foreach ($_POST['ClassTags'] as $key => $value) array_push($shareArray, "C_".$value);
	if (isset($_POST['GroupTags'])) foreach ($_POST['GroupTags'] as $key => $value) array_push($shareArray, "G_".$value);
	$query = "UPDATE `images` SET `_share` = '".json_encode($shareArray)."' WHERE `_IDimage` = '".$element_id."' ";
	$result = mysqli_query($mysql_link, $query);
}
?>

<script src="script/sweetalert2.min.js"></script>

<?php
	if (isset($_GET['cmde'])) $cmde = $_GET['cmde'];
	elseif (isset($_POST['cmde'])) $cmde = $_POST['cmde'];
	else $cmde = '';
	if (isset($_SESSION['folder_path'])) $folder_path = $_SESSION['folder_path'];
	else $folder_path = '';
	$page_breadcrumbs = create_breadcrumbs($folder_path, $_SESSION["CnxID"], $cmde);
?>

<?php
	$current_page = 'my_files';
	$page_title = $msg->read($USER_MY_FILES);
?>

<?php include(RESSOURCE_PATH['PAGES_FOLDER'].'ged/page_top.php'); ?>




			<?php
			if(($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) or 1 == 1)
			{
				?>
				<table class="width100" style="width: 100%; margin-top: 30px; border-top: 1px solid #ccc">
					<tr>
						<td>
							<!-- blueimp Gallery styles -->
							<link rel="stylesheet" href="css/fileupload/blueimp/blueimp-gallery.min.css">
							<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
							<link rel="stylesheet" href="css/fileupload/jquery.fileupload.css">
							<link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui.css">
							<!-- CSS adjustments for browsers with JavaScript disabled -->
							<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-noscript.css"></noscript>
							<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui-noscript.css"></noscript>

							<div style="background-color: white; padding: 10px;">
								<!-- The file upload form used as target for the file upload widget -->
								<form id="fileupload" action="index.php" method="POST" enctype="multipart/form-data">
									<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
									<div class="row fileupload-buttonbar" style="margin: 10px 7px 10px 5px;">
										<div style="width: 100%;">
											<!-- The fileinput-button span is used to style the file input field as button -->
											<div style="width: 100%;">
											    <div style="float: right;">
														<button type="button" class="btn btn-primary" onclick="add_folder();">
															<i class="fa fa-plus"></i>&nbsp;<?php echo $msg->read($USER_ADDFOLDER); ?>
														</button>
														<button type="button" id="removeMultipleButton" onclick="removeMultiple();" class="btn btn-primary delete" title="<?php echo $msg->read($USER_DELETE); ?>">
															<i class="fas fa-trash"></i>
														</button>
										        <input type="checkbox" class="toggle">
											    </div>

											    <div style="overflow: hidden;">
														<span class="btn btn-default fileinput-button" style="width: calc(100% - 30px); border: grey 3px dashed; line-height: 28px;">
															<i class="glyphicon glyphicon-plus"></i>
															<span><?php echo $msg->read($USER_ADDFILE); ?></span>
															<input type="file" name="files[]" multiple onchange="ValidateSize(this)">
														</span>
											    </div>
											</div>
											<!-- The global file processing state -->
											<span class="fileupload-process"></span>
										</div>
										<!-- The global progress state -->
										<div class="col-lg-5 fileupload-progress" style="display: none;">
											<!-- The global progress bar -->
											<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
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

							<?php include(RESSOURCE_PATH['PAGES_FOLDER'].'ged/templates/user_ged.php'); ?>

						</td>
					</tr>
				</table>
				<?php
			}
			?>



			<script>
				// Ce qui se passe quand jquery upload est prêt
				function fileListReady() {

				}
			</script>



			<style>
				#selectedAfterSearch > td{
						background-color: rgba(10, 10, 10, 0.3);
				}
				.tooltip {
					position: relative;
					display: inline-block;
					opacity: 1;
				}
				/* Tooltip text */
				.tooltip .tooltiptext {
					visibility: hidden;
				}
				.tooltiptext {
					width: 250px !important;
				}
				/* Show the tooltip text when you mouse over the tooltip container */
				.tooltip:hover .tooltiptext {
					visibility: visible;
				}
				.tooltiptext:hover .tooltiptext {
					visibility: visible;
				}
				.tooltip .tooltiptext {
					width: 230px;
					top: 100%;
					left: 50%;
					margin-left: -220px; /* Use half of the width (120/2 = 60), to center the tooltip */
				}
				.tooltipcontent {
					background-color: #999999;
					height: auto;
					width: -webkit-fill-availible;
				}
				.tooltip .tooltiptext::after {
					content: " ";
					position: absolute;
					bottom: 100%;  /* At the top of the tooltip */
					left: 87%;
					margin-left: -5px;
					border-width: 5px;
					border-style: solid;
					border-color: transparent transparent #999999 transparent;
				}


				.tooltiptext-folder {
					width: 115px !important;
					margin-left: -98px !important;
				}
			</style>

			<script type="text/javascript">

				$("#selectSearch").change(function(){
					$('#searchForm').submit();
				});
			</script>

			<script type="text/javascript">

				function rename_file(id_file)
				{
					var name_file = $("#rename_"+id_file).attr('data-name');
					var form_label = "<?php echo $msg->read($USER_CURRENT_FILE_NAME); ?>";
					var form_input_content = name_file;
					var form_button = "<?php echo $msg->read($USER_RENAME); ?>";
					$("#action").val("renameFolder");
					$("#new_name_of_element").attr("value", form_input_content);
					$("#id_of_renamed_element").attr("value", id_file);
					$("#modal_title").html("<?php echo $msg->read($USER_RENAME); ?>");
					$("#modal_submit_button").html(form_button);
					$("#modal_form_label").html(form_label+":&nbsp;");

					modal_action_show();
				}

				function add_folder()
				{
					var modal_title = "<?php echo $msg->read($USER_ADDFOLDER); ?>";
					var form_label = "<?php echo $msg->read($USER_CURRENT_FILE_NAME); ?>";
					var form_input_content = "";
					var form_button = "<?php echo $msg->read($USER_ADDFOLDER); ?>";
					$("#action").val("addFolder");
					$("#new_name_of_element").attr("placeholder", form_input_content);
					$("#modal_title").html(modal_title);
					$("#modal_submit_button").html(form_button);
					$("#modal_form_label").html(form_label+":&nbsp;");

					modal_action_show();
				}

				function move_file(id_file) {
					$("#modal_move_file").modal('show');
					showFileListForMove(id_file, "<?php if (isset($_SESSION['folder_path'])) echo $_SESSION['folder_path']; ?>");
				}

				function showFileListForMove(idOfFileToMove, currentPath) {
					showFileListForMoveBreadCrumbs(idOfFileToMove, currentPath);
					showFileListForMoveDestinationInTitle(idOfFileToMove, currentPath);
					$.ajax({
						url : 'ged_ajax.php?action=getFolderList',
						type : 'POST', // Le type de la requête HTTP, ici devenu POST
						data : 'idOfFileToMove=' + idOfFileToMove + '&currentPath=' + currentPath + '&userID=' + <?php echo $_SESSION["CnxID"]; ?>,
						dataType : 'html', // On désire recevoir du HTML
						success : function(code_html, statut){ // code_html contient le HTML renvoyé
							$("#table_of_file_to_move_from").html(code_html);
						}
					});
					$("#id_of_moved_element").attr("value", idOfFileToMove);
					$("#id_of_parent_folder").attr("value", currentPath);
				}

				function showFileListForMoveBreadCrumbs(idOfFileToMove, currentPath) {
					$.ajax({
						url : 'ged_ajax.php?action=getBreadcrumb',
						type : 'POST', // Le type de la requête HTTP, ici devenu POST
						data : 'currentPath=' + currentPath + '&userID=' + <?php echo $_SESSION["CnxID"]; ?> + '&idOfFileToMove=' + idOfFileToMove,
						dataType : 'html', // On désire recevoir du HTML
						success : function(code_html, statut){ // code_html contient le HTML renvoyé
							$("#table_of_file_to_move_current_file").html(code_html);
						}
					});
				}

				function showFileListForMoveDestinationInTitle(idOfFileToMove, currentPath) {
					$.ajax({
						url : 'ged_ajax.php?action=getTitleFolderName',
						type : 'POST', // Le type de la requête HTTP, ici devenu POST
						data : 'idOfFileToMove=' + currentPath + '&userID=' + <?php echo $_SESSION["CnxID"]; ?>,
						dataType : 'html', // On désire recevoir du HTML
						success : function(code_html, statut){ // code_html contient le HTML renvoyé
							$("#modal_move_location").html(code_html);
						}
					});
				}

				function getNumberOfElementToRemove(id_file) {
					$.ajax({
						url : 'ged_ajax.php?action=getNumberOfFilesInFolderToRemove',
						type : 'POST', // Le type de la requête HTTP, ici devenu POST
						data : 'idFolder=' + id_file + '&userID=' + <?php echo $_SESSION["CnxID"]; ?>,
						dataType : 'html', // On désire recevoir du HTML
						success : function(code_html, statut){ // code_html contient le HTML renvoyé
							return code_html;
						}
					});
				}

				function removeConfirm(id_file) {
					var remove_text = $('#remove_' + id_file).attr("data-remove-text");
					if (remove_text == "cet élément" || remove_text == "1 dossier") var remove_text_confirm = "Cet élément à bien été supprimé !";
					else var remove_text_confirm = remove_text + " on bien été supprimés !";
					if (remove_text == "cet élément") var remove_text_abort = "Cet élément n'à pas été supprimé !";
					else var remove_text_abort = remove_text + " n'on pas été supprimés !";
					swal({
						title: "Attention",
						text: "Vous allez supprimer " + remove_text + " de façon définitive!\n Êtes-vous sûr ?",
						icon: "warning",
						buttons: ["Annuler", true],
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {
							removeConfirmed(id_file);
							swal(remove_text_confirm, {
								icon: "success",
							});

						} else {
							swal(remove_text_abort);
						}
					});
				}


				function removeConfirmed(id_file) {
					// $('#javascriptHelperCalledTwice').html("0");
					$("#remove_" + id_file).attr("type", "");
					$("#remove_" + id_file).addClass("delete");
					$("#remove_" + id_file).click();
				}
				function removeMultipleConfirmed() {
					$(".remove_button").addClass("delete");
					$("#removeMultipleButton").click();
				}

				function removeMultiple() {
					swal({
						title: "Attention",
						text: "Vous allez supprimer ces éléments de façon définitive!\n Êtes-vous sûr ?",
						icon: "warning",
						buttons: ["Annuler", true],
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {
							removeMultipleConfirmed();
							swal("Les éléments à bien été supprimés !", {
								icon: "success",
							});
						} else {
							swal("Les éléments n'à pas été supprimés !");
						}
					});
				}


				function share_file(id_file) {
					$("#modal_share_file").modal('show');
					showSharedFile(id_file);
					$("#id_of_shared_element").attr("value", id_file);
				}

				function showSharedFile(idOfCurrentFile) {
					$.ajax({
						url : 'ged_ajax.php?action=getShareForm',
						type : 'POST', // Le type de la requête HTTP, ici devenu POST
						data : 'idOfFileToShare=' + idOfCurrentFile + '&userID=' + <?php echo $_SESSION["CnxID"]; ?>,

						dataType : 'html', // On désire recevoir du HTML
						success : function(code_html, statut){ // code_html contient le HTML renvoyé
							$("#shareForm").html(code_html);
						}

					});
				}

				// ---------------------------------------------------------------------------
				// Fonction: Redirige vers la rédaction de mail avec dans le corps du message
				//					 un lien de partage du fichier
				// IN:		   L'ID du fichier (INT)
				// OUT: 		 redirection
				// ---------------------------------------------------------------------------
				function share_mail_file(id_file) {
					var new_link = "?item=4&cmde=post&id_file=" + id_file;
					window.location.replace(new_link);
				}

				// ---------------------------------------------------------------------------
				// Fonction: Donne à l'utilisateur le lien de partage du fichier
				// IN:		   L'ID du fichier (INT)
				// OUT: 		 Lien (TEXT) sous forme de popup
				// ---------------------------------------------------------------------------
				function share_link_file(id_file) {
					$.ajax({
						url : 'ged_ajax.php?action=getShareLink',
						type : 'POST', // Le type de la requête HTTP, ici devenu POST
						data : 'id_file=' + id_file,

						dataType : 'html', // On désire recevoir du HTML
						success : function(code_html, statut){ // code_html contient le HTML renvoyé
							$('#modal_share_file_link').modal('show');
							$("#share_link").val(code_html);
							$("#share_link").attr('value', code_html);
						}

					});
				}

				// ---------------------------------------------------------------------------
				// Fonction: Copie le lien de partage
				// ---------------------------------------------------------------------------
				function copy_link_to_clipbord() {
					var copyText = document.getElementById("share_link");

						/* Select the text field */
						copyText.select();
						copyText.setSelectionRange(0, 99999); /*For mobile devices*/

						/* Copy the text inside the text field */
						document.execCommand("copy");

						/* Alert the copied text */
						console.log("Lien de partage copié");
				}


				function modal_action_hide() {
					$('#modal_action').modal('hide');
				}
				function modal_action_show() {
					$('#modal_action').modal('show');
				}


				function ValidateSize(file) {
					var FileSize = file.files[0].size / 1024 / 1024;
					// Taille maximum autorisée en mb:
					if (FileSize > 10) {
						alert('Taille maximum autorisée: 10 MB');
						$(file).val('');
					} else {

					}
				}


				$('#fileupload').bind('fileuploadstop', function (e) {
					console.log('Uploads finished');
						location.reload(); // refresh page
					});


			</script>






			<!-- Adaptable modal -->
			<!-- <div class="modal_background" id ="modal_background" style="position: absolute; z-index: 9000; top: 0; left: 0; width: 100vw; height: 98.8vh; background-color: rgba(0, 0, 0, 0.4); display: none" onclick="modal_action_hide();"></div> -->
			<div class="modal fade" id ="modal_action" tabindex="-1" aria-hidden="true" style="/* position: absolute; z-index: 9001; top: 50%; left: 50%; -ms-transform: translateX(-50%) translateY(-50%); -webkit-transform: translate(-50%,-50%); transform: translate(-50%,-50%); display: none; */">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<form id="modal_form" action="" method="GET">
							<div class="modal-header">
				        <h5 class="modal-title" id="modal_title">Titre</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>

							<div class="modal-body">

								<input type="hidden" name="item" value="28" />
								<input type="hidden" name="action" id="action" value="" />
								<div class="form-group">
									<label for="new_name_of_element" id="modal_form_label">Email address</label>
									<input type="text" class="form-control" name="element_name" id="new_name_of_element" aria-describedby="emailHelp" required>
								</div>
								<input id="path_current_folder" type="hidden" name="path" value="<?php if (isset($_SESSION['folder_path'])) echo $_SESSION['folder_path']; ?>">
								<input id="id_of_renamed_element" name="element_id" hidden value="">
							</div>
							<div class="modal-footer">
								<button id="modal_submit_button" type="submit" class="btn btn-success"></button>
							</div>
						</form>
					</div>
				</div>
			</div>



			<!-- Move modal -->
			<div class="modal fade" id ="modal_move_file" tabindex="-1" aria-hidden="true" style=" /* position: absolute; z-index: 9001; top: 50%; left: 50%; -ms-transform: translateX(-50%) translateY(-50%); -webkit-transform: translate(-50%,-50%); transform: translate(-50%,-50%); display: none; */">

				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<form id="modal_move_form" action="index.php" method="GET">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_move_title">Déplacer vers:&nbsp;<span id="modal_move_location"></span></h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<input type="hidden" name="item" value="28" />
								<input type="hidden" name="path" value="<?php echo $_SESSION['folder_path']; ?>" />
								<input type="hidden" name="action" value="moveFile" />
								<table class="table" id="table_of_file_to_move_current_file"></table>
								<table class="width100" id="table_of_file_to_move_from"></table>
								<table class="width100">
									<tr>
										<td style="text-align: right;">
											<input id="id_of_moved_element" name="element_id" hidden value="">
											<input id="id_of_parent_folder" name="parent_id" hidden value="">

										</td>
									</tr>
								</table>
							</div>
							<div class="modal-footer">
								<button id="modal_submit_button" style="margin-top: 5px;" type="submit" class="btn btn-success">Déplacer</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Share modal -->
			<div class="modal fade" id ="modal_share_file" tabindex="-1" aria-hidden="true">
				<form id="modal_share_form" action="index.php?item=28&path=<?php echo $_SESSION['folder_path']; ?>&action=shareFile" method="post">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_share_title">Partager avec:</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<table id="shareForm">

								</table>
								<input id="id_of_shared_element" name="element_id" hidden value="">
							</div>
							<div class="modal-footer">
								<button id="modal_submit_button" type="submit" class="btn btn-success">Enregistrer</button>
							</div>
						</div>
					</div>
				</form>
			</div>



			<!-- Share LINK modal -->
			<div class="modal fade" id ="modal_share_file_link" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Lien de partage:</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<input type="text" id="share_link" class="form-control" style="/* width: 90%; overflow: auto; */">
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary" onclick="copy_link_to_clipbord()">Copier</button>
						</div>
					</div>
				</div>
			</div>




  </div>
</div>


<link href="/css/file.css" rel="stylesheet">
<!-- <link rel="stylesheet" href="css/blueimp/blueimp-gallery.min.css" /> -->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="css/fileupload/jquery.fileupload.css" />
<link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui.css" />
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-noscript.css" /></noscript>
<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui-noscript.css" /></noscript>









<script>

function getImageThumbnail (fileID) {
	// var fileID = $(".imageThumbnail"+id_file).attr('data-name');
	$.ajax({
		url : 'ged_ajax.php?action=getShareForm',
		type : 'POST', // Le type de la requête HTTP, ici devenu POST
		data : 'fileID=' + fileID,

		dataType : 'html', // On désire recevoir du HTML
		success : function(code_html, statut){ // code_html contient le HTML renvoyé
			$(".imageThumbnail").html(code_html);
		}

	});
}


</script>
