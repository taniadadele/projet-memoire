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
 *		module   : ged_specific_user_files.php
 *		projet   : la page de gestion des fichiers d'un utilisateur
 *
 *		version  : 1.1
 *		auteur   : Thomas Dazy
 *		creation : 20/03/2019
 *
 */

?>


<?php
	/***************************************************/
	/* On établis les variables principales de la page */
	/***************************************************/

	// On récupère l'ID de l'utilisateur dont on veut voir les fichiers
	if ($_GET['userID'] != "") $_SESSION['GED_USER_ID'] = $_GET['userID'];
	$userID = $_SESSION['GED_USER_ID'];

	// Les fichiers serons stoqués dans la base dans la table `images` sous le type:
	$typeDB = "user";
	$_SESSION['typeDB'] = $typeDB;

	// Les fichiers seront enregistrés dans le dossier:
	$folderGED = "user_ged";
	$_SESSION['FOLDER_GED'] = $folderGED;

	$cmde = $_GET['cmde'];

	$urlUser = "index.php?item=".$_GET['item']."&cmde=".$cmde."&userID=".$userID;

	// echo "USERID: ".$userID."<br>";
	// echo "typeDB: ".$typeDB."<br>";
	// echo "folderGED: ".$folderGED."<br>";
	// echo "url: ".$urlUser."<br>";


	if ($_GET['folderSearch'] == "")
	{
		$_SESSION['folder_path'] = $_GET['path'];
	}
	else {
		$_SESSION['folder_path'] = $_GET['folderSearch'];

	}

	$_SESSION['selected_file'] = $_GET['fileSearch'];


	// Toujours vérifier si le répertoire utilisateur est existant
	if(!is_dir("download/".$folderGED."/files/".$userID))
	{
    // Sinon on le crée
		mkdir("download/".$folderGED."/files/".$userID, 0777, true);
    fopen("download/".$folderGED."/files/".$userID."/index.php", "w");
	}

	// initialisation
	$_ID = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
		? ($ID ? $ID : $_SESSION["CnxID"])
		: $_SESSION["CnxID"] ;

	if ($_GET["ID"] === "9999999999" OR $_GET["ID"] == "") $ID = $_SESSION["CnxID"];
	if ($_SESSION["CnxGrp"] > 1) $_ID = $ID;


// addFolder
if ($_GET['action'] == "addFolder" and $_GET['element_name'] != "")
{
	$folderName = "-".$_GET['element_name'];

	$query  = "SELECT _ext ";
	$query .= "FROM images ";
	$query .= "WHERE _type = '$typeDB' ";
	$query .= "AND _ID = '$userID' ";
	$query .= "AND _title = '$folderName' ";
	$query .= "AND _parent = '".$_GET['path']."' ";
	$result = mysqli_query($mysql_link, $query);
	if(mysqli_num_rows($result) == 0) {
		$query  = "INSERT INTO images ";
		$query .= "VALUES (NULL, '".$typeDB."', '".$userID."', '".$folderName."', '0', '', NOW(), 'fol', '', '0', '".$_GET['path']."') ";
		$result = mysqli_query($mysql_link, $query);
	}
}




if ($_GET['action'] == "renameFolder")
{
	$query  = "SELECT _ext ";
	$query .= "FROM images ";
	$query .= "WHERE _ID = ".$userID." ";
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




if ($_GET['action'] == "moveFile")
{
	$element_id = $_GET['element_id'];
	$parent_id = $_GET['parent_id'];
	$query = "UPDATE `images` SET `_parent` = '".$parent_id."' WHERE `_IDimage` = '".$element_id."' ";
	$result = mysqli_query($mysql_link, $query);
}


?>

<?php // I am not sure if we need this, but too scared to delete. ?>
<script>
jQuery( document ).ready(function() {
	jQuery.ajax({
		<?php
		if ( getAccess($row[1]) == 2 ) echo "url: 'getInfos.php?IDuser=$ID&type=3',";
		?>
		<?php
		if ( getAccess($row[1]) == 1 ) echo "url: 'getInfos.php?IDclass=$row[3]&IDuser=$ID&type=2',";
		?>
		success  : function(data) {
			jQuery("#divtr_<?php echo $ID; ?>").html(data);
		}
	});

	return false;
});
</script>





<script src="script/sweetalert2.min.js"></script>
<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
<style>
	.swal-text {
		text-align: center;
	}
</style>






<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">


	<div class="alert alert-error" style="text-align: center; padding: 10px;">

		<div style="float: left; ">
			<a class="btn" style="color: black; margin-bottom: 10px; margin-top: -6%;" href="index.php?item=1&cmde=account&show=0&ID=<?php echo $userID; ?>">
				<i class="fa fa-chevron-left"></i> Retour
			</a>
		</div>
		<div style="float: center;">
	  	<?php echo $msg->read($USER_FILES_OF_THE_USER)." <strong>".getUserNameByID($userID)."</strong>"; ?>
		</div>
	</div>



</div>

<div class="maincontent" id="main_my_files" style="margin-top: 0px;">





	<div style="overflow: hidden; width: 100%;">
			<div style="float: right;">
				<form class="form-search" action="index.php?item=28&cmde=search&specific=userFiles&userID=<?php echo $userID; ?>" method="POST" id="searchForm">


					<div class="input-append">
						<input class="span2 input-medium search-query" id="appendedInputButton" type="text" name="searchField" autocomplete="off">
						<button class="btn" type="submit"><?php echo $msg->read($USER_RESEARCH); ?></button>
					</div>

				</form>
			</div>




			<div style="overflow: hidden; min-height: 30px;">
				<?php
					echo create_breadcrumbs($_SESSION['folder_path'], $userID, $cmde);
				?>
			</div>
	</div>



	<?php
	if(($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) or 1 == 1)
	{
		?>
		<table class="width100" style="/*margin-top: 60px;*/ margin-top: 30px; border-top: 1px solid #ccc">
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

					<div style="background-color: white; padding: 10px;">
						<!-- The file upload form used as target for the file upload widget -->
						<form id="fileupload" action="index.php" method="POST" enctype="multipart/form-data">
							<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
							<div class="row fileupload-buttonbar" style="margin: 10px 7px 10px 5px; text-align: right;">

								<div class="col-lg-7">
									<!-- The fileinput-button span is used to style the file input field as button -->





									<div style="overflow: hidden; width: 100%;">
									    <div style="float: right; height: 42px; padding-top: calc((42px - 30px) / 2);">
												<button type="button" style="margin-left: 10px;" class="btn btn-default" onclick="add_folder();">
													<i class="fa fa-plus"></i>&nbsp;<?php echo $msg->read($USER_ADDFOLDER); ?>
												</button>
												<button type="button" style="margin-left: 10px;" id="removeMultipleButton" onclick="removeMultiple();" class="btn btn-default delete" title="<?php echo $msg->read($USER_DELETE); ?>">
													<i class="fa fa-trash-o"></i>

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
								<div class="col-lg-5 fileupload-progress fade" style="display: none;">
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
						<tr class="template-download fade" {% if (file.backgroundColor != "0"){ %} id="selectedAfterSearch" {% } %} >
							<td style="text-align: center; width: 100px;">
								<span class="preview">
									{% if (file.thumbnailUrl) { %}
										<a href="{%=file.url%}" title="{%=file.name%}"><img src="{%=file.thumbnailUrl%}" style="height: 60px; text-align: center;"></a>
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
								<span class="size">{%=file.size%}</span>

							</td>
							<td>
								<span class="date">{%=file.dateTime%}</span>

							</td>

							<td style="text-align: center;">
								{% if (file.shared_with_user != "0") { %}
									<span class="badge"><i class="fa fa-user">&nbsp;{%=file.shared_with_user%}</i></span>
								{% } %}
								{% if (file.shared_with_class != "0") { %}
									<span class="badge"><i class="fa fa-graduation-cap">&nbsp;{%=file.shared_with_class%}</i></span>
								{% } %}
								{% if (file.shared_with_group != "0") { %}
									<span class="badge"><i class="fa fa-users">&nbsp;{%=file.shared_with_group%}</i></span>
								{% } %}
							</td>

							{% if (file.deleteUrl != "doNotDisplay") { %}
							<td style="text-align: right;">
								{% if (file.deleteUrl) { %}



<div class="tooltip">
<div class="tooltipbutton">
<button class="btn btn-default" id="more_options_{%=file.id%}" onclick="show_options({%=file.id%});" type="button">
	<i class="fa fa-ellipsis-h" aria-hidden="true"></i>
</button>
</div>

	<span class="tooltiptext">
	<span class="tooltipcontent">



										<button class="btn btn-default" id="move_{%=file.id%}" onclick="move_file({%=file.id%});" data-name="{%=file.name_without_extension%}" type="button" title="<?php echo $msg->read($USER_MOVE_ELEMENT); ?>">
											<i class="fa fa-arrows" aria-hidden="true"></i>
										</button>

										<button class="btn btn-default" id="rename_{%=file.id%}" onclick="rename_file({%=file.id%});" data-name="{%=file.name_without_extension%}" type="button" title="<?php echo $msg->read($USER_RENAME); ?>">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
										</button>
</span>
</span>
</div>


									<span  style="display:none;"></span>

									<button style="margin-left: 10px;" id="remove_{%=file.id%}" type="button" data-remove-text="{%=file.numberOfFilesToRemove%}" onclick="removeConfirm({%=file.id%});" class="btn btn-default remove_button" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %} title="<?php echo $msg->read($USER_DELETE); ?>">
										<i class="fa fa-trash-o"></i>
									</button>

									<input type="checkbox" name="delete" value="1" class="toggle">
								{% } else { %}
									<button class="btn btn-warning cancel">
										<i class="glyphicon glyphicon-ban-circle"></i>
										<span><?php echo $msg->read($USER_CANCEL); ?></span>
									</button>
								{% } %}
							</td>
							{% } else { %}
							<td></td>
							{% } %}
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
					<script src="js/bootstrap.min.js"></script>
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
					var numfiche = <?php echo $userID; ?>;
					var typefiche = "<?php echo $folderGED; ?>";
					var urlupload = "<?php echo substr($_SERVER["HTTP_REFERER"], 0, strpos($_SERVER["HTTP_REFERER"], "index.php")); ?>";
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
					  /* width: 120px; */
					  background-color: #999999;
					  color: #fff;
					  text-align: center;
					  padding: 5px 0;
					  border-radius: 6px;

					  /* Position the tooltip text - see examples below! */
					  position: absolute;
					  z-index: 1;
					}

					/* Show the tooltip text when you mouse over the tooltip container */
					.tooltip:hover .tooltiptext {
					  visibility: visible;
					}
					.tooltiptext:hover .tooltiptext {
					  visibility: visible;
					}
					.tooltip .tooltiptext {
					  width: 100px;
					  top: 100%;
					  left: 50%;
					  margin-left: -50px; /* Use half of the width (120/2 = 60), to center the tooltip */
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
					  left: 50%;
					  margin-left: -5px;
					  border-width: 5px;
					  border-style: solid;
					  border-color: transparent transparent #999999 transparent;
						/* text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); */
					}



					</style>

					<script type="text/javascript">

						$("#selectSearch").change(function(){
							$('#searchForm').submit();
							// alert('Selected value: ' + $(this).val());
						});
					</script>

					<script type="text/javascript">

					function rename_file(id_file)
					{
						var name_file = $("#rename_"+id_file).attr('data-name');

						var form_label = "<?php echo $msg->read($USER_CURRENT_FILE_NAME); ?>";
						var form_input_content = name_file;
						var form_button = "<?php echo $msg->read($USER_RENAME); ?>";
						// var form_action = "<?php echo $urlUser; ?>&path=<?php echo $_SESSION['folder_path']; ?>&action=renameFolder"
						$("#action").val("renameFolder");

						$("#new_name_of_element").attr("value", form_input_content);
						$("#id_of_renamed_element").attr("value", id_file);
						$("#modal_title").html("<?php echo $msg->read($USER_RENAME); ?>");
						$("#modal_submit_button").html(form_button);
						// $("#modal_form").attr("action", form_action);
						$("#modal_form_label").html(form_label+":&nbsp;");

						modal_action_show();
					}

					function add_folder()
					{
						var modal_title = "<?php echo $msg->read($USER_ADDFOLDER); ?>";
						var form_label = "<?php echo $msg->read($USER_CURRENT_FILE_NAME); ?>";
						var form_input_content = "";
						var form_button = "<?php echo $msg->read($USER_ADDFOLDER); ?>";
						// var form_action = "<?php echo $urlUser; ?>&path=<?php echo $_SESSION['folder_path']; ?>&action=addFolder"
						$("#action").val("addFolder");

						$("#new_name_of_element").attr("placeholder", form_input_content);
						$("#modal_title").html(modal_title);
						$("#modal_submit_button").html(form_button);
						// $("#modal_form").attr("action", form_action);
						$("#modal_form_label").html(form_label+":&nbsp;");

						modal_action_show();
					}

					function move_file(id_file) {
						$("#modal_move_file").fadeIn();
						$(".modal_background").fadeIn();
						showFileListForMove(id_file, "<?php echo $_SESSION['folder_path']; ?>");

					}

					function showFileListForMove(idOfFileToMove, currentPath) {
						showFileListForMoveBreadCrumbs(idOfFileToMove, currentPath);
						showFileListForMoveDestinationInTitle(idOfFileToMove, currentPath);
						$.ajax({
							url : 'ged_ajax.php?action=getFolderList',
							type : 'POST', // Le type de la requête HTTP, ici devenu POST
							data : 'idOfFileToMove=' + idOfFileToMove + '&currentPath=' + currentPath + '&userID=' + <?php echo $userID; ?> +"&type=user",

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
							data : 'currentPath=' + currentPath + '&userID=' + <?php echo $userID; ?> + '&idOfFileToMove=' + idOfFileToMove,

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
							data : 'idOfFileToMove=' + currentPath + '&userID=' + <?php echo $userID; ?>,

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
							data : 'idFolder=' + id_file + '&userID=' + <?php echo $userID; ?>,

							dataType : 'html', // On désire recevoir du HTML
							success : function(code_html, statut){ // code_html contient le HTML renvoyé

								// $('#remove_' + id_file).attr("data-remove-text", code_html);
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
						$(".modal_background").fadeIn();
						$("#modal_share_file").fadeIn();
						showSharedFile(id_file);
						$("#id_of_shared_element").attr("value", id_file);
					}

					function showSharedFile(idOfCurrentFile) {
						$.ajax({
							url : 'ged_ajax.php?action=getShareForm',
							type : 'POST', // Le type de la requête HTTP, ici devenu POST
							data : 'idOfFileToShare=' + idOfCurrentFile + '&userID=' + <?php echo $userID; ?>,

							dataType : 'html', // On désire recevoir du HTML
							success : function(code_html, statut){ // code_html contient le HTML renvoyé
								$("#shareForm").html(code_html);
							}

						});
					}


					function modal_action_hide() {
						$(".modal_background").fadeOut();
						$("#modal_action").fadeOut();
						$("#modal_move_file").fadeOut();
						$("#modal_share_file").fadeOut();
						// $("#modal_background").fadeOut();
						$("#modal_remove_file").fadeOut();


					}
					function modal_action_show() {
						$(".modal_background").fadeIn();
						$("#modal_action").fadeIn();
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

					// Function qui reload la page lors d'un upload
					$('#fileupload').bind('fileuploadstop', function (e) {
						console.log('Uploads finished');
							location.reload(); // refresh page
							});


					</script>




				<!-- Adaptable modal -->
				<div class="modal_background" id ="modal_background" style="position: absolute; z-index: 9000; top: 0; left: 0; width: 100vw; height: 98.8vh; background-color: rgba(0, 0, 0, 0.4); display: none" onclick="modal_action_hide();"></div>
				<div class="well" id ="modal_action" style="position: absolute; z-index: 9001; top: 50%; left: 50%; -ms-transform: translateX(-50%) translateY(-50%); -webkit-transform: translate(-50%,-50%); transform: translate(-50%,-50%); display: none;">
					<form id="modal_form" action="" method="GET">
						<input type="hidden" name="item" value="28" />
						<input type="hidden" name="cmde" value="userFiles" />
						<input type="hidden" name="userID" value="<?php echo $userID; ?>" />
						<input type="hidden" name="action" id="action" value="" />

						<fieldset>
							<legend id="modal_title"></legend>
							<label id="modal_form_label"></label>

							<input id="path_current_folder" type="hidden" name="path" value="<?php echo $_SESSION['folder_path']; ?>">

							<input id="id_of_renamed_element" name="element_id" hidden value="">
							<input id="new_name_of_element" name="element_name" type="text" class="input-medium search-query" required>
							<button id="modal_submit_button" type="submit" class="btn"></button>
						</fieldset>
					</form>
		    </div>

				<!-- Move modal -->
				<div class="well" id ="modal_move_file" style="position: absolute; z-index: 9001; top: 50%; left: 50%; -ms-transform: translateX(-50%) translateY(-50%); -webkit-transform: translate(-50%,-50%); transform: translate(-50%,-50%); display: none;">
					<form id="modal_move_form" action="index.php" method="GET">
						<input type="hidden" name="item" value="28" />
						<input type="hidden" name="path" value="<?php echo $_SESSION['folder_path']; ?>" />
						<input type="hidden" name="action" value="moveFile" />
						<input type="hidden" name="cmde" value="userFiles" />
						<input type="hidden" name="userID" value="<?php echo $userID; ?>" />

						<fieldset>
							<legend id="modal_move_title">Déplacer vers:&nbsp;<span id="modal_move_location"></span></legend>
							<table style="width: 600px; /*margin-top: 60px;*/ margin-top: 30px; border-top: 1px solid #ccc; margin-left: 10px; margin-right: 10px;" id="table_of_file_to_move_current_file"></table>
							<table class="width100" style="/*margin-top: 60px;*/ margin-top: 30px; border-top: 1px solid #ccc;" id="table_of_file_to_move_from"></table>
							<table class="width100" style="/*margin-top: 60px;*/ margin-top: 30px; border-top: 1px solid #ccc;">
								<tr>
									<td style="text-align: right;">
										<input id="id_of_moved_element" name="element_id" hidden value="">
										<input id="id_of_parent_folder" name="parent_id" hidden value="">
										<button id="modal_submit_button" style="margin-top: 5px;" type="submit" class="btn">Déplacer</button>
									</td>
								</tr>
							</table>
						</fieldset>
					</form>
		    </div>

				<!-- Share modal -->
				<div class="well" id ="modal_share_file" style="min-width: 300px; position: absolute; z-index: 9001; top: 50%; left: 50%; -ms-transform: translateX(-50%) translateY(-50%); -webkit-transform: translate(-50%,-50%); transform: translate(-50%,-50%); display: none;">
					<form id="modal_share_form" action="index.php?item=28&path=<?php echo $_SESSION['folder_path']; ?>&action=shareFile&cmde=userFiles&userID=<?php echo $userID; ?>" method="post">
						<fieldset>
							<legend id="modal_share_title">Partager avec:</legend>

							<table id="shareForm">

							</table>
							<input id="id_of_shared_element" name="element_id" hidden value="">

							<table class="width100" style="/*margin-top: 60px;*/ margin-top: 30px; border-top: 1px solid #ccc;">
								<tr>
									<td style="text-align: right;">
										<button id="modal_submit_button" style="margin-top: 5px;" type="submit" class="btn">Partager</button>
									</td>
								</tr>
							</table>

						</fieldset>
					</form>
		    </div>







				</td>
			</tr>
		</table>
		<?php
	}
	?>
</table>
</div>
</div>
