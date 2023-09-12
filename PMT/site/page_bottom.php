<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : page_bottom.php
 *		projet   : le bandeau du bas avec les coordonnées de l'établissement
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */
?>
<?php if (1 == 0) { ?>
<?php
require_once "msg/page.php";

$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

$style  =  ( strlen($_SESSION["CfgTdcolor"]) )
	? "style=\"background-color:#".$_SESSION["CfgTdcolor"]."\""
	: "" ;


$file   = $_SESSION["ROOTDIR"]."/download/logos/".$_SESSION["CfgIdent"]."/logo02.jpg";

$footer  = "<strong>".$_SESSION["CfgIdent"]."</strong>";
// $footer .= $_SESSION["CfgAdr"]."<!-- <br /> -->";
$footer .= ( strlen($_SESSION["CfgEmail"]) ) ? "<span style=\"margin-left: 15px\"><i class=\"fa fa-envelope-square\"></i> <a href=\"mailto:".$_SESSION["CfgEmail"]."\">".$_SESSION["CfgEmail"]."</a></span>" : "";
$footer .= ( strlen($_SESSION["CfgTel"]) ) ? "<span style=\"margin-left: 15px\"><i class=\"fa fa-phone-square\"></i> ".$_SESSION["CfgTel"]."</span> " : "" ;
$footer .= ( strlen($_SESSION["CfgFax"]) ) ? "<span style=\"margin-left: 15px\"><i class=\"fa fa-fax\"></i> ".$_SESSION["CfgFax"]."</span> " : "" ;

if ( $_SESSION["CfgWeb"] ) {
	$href    = ( strstr($_SESSION["CfgWeb"], "http://") != "" )
		? $_SESSION["CfgWeb"]
		: "http://".$_SESSION["CfgWeb"] ;

	$footer .= "<i class=\"fa fa-globe\"></i> <a href=\"$href\" onclick=\"window.open(this.href,'_blank');return false;\">".$_SESSION["CfgWeb"]."</a>";
	}

if ( strlen($_SESSION["CfgAdmin"]) )
	$footer .= "<br/>". $msg->read($PAGE_WEBMASTER) . " <a href=\"mailto:".$_SESSION["CfgAdmin"]."\">".$_SESSION["CfgAdmin"]."</a>";
?>
<!--  class="noprint align-center" style="padding-left: <?php echo $width_left; ?>" -->
<footer style="position: fixed; bottom: 0; width: 100%;" class="d-print-none">
	<!-- style="width: -webkit-fill-available; height: 65px; position: unset;" -->
<div class="footer" style="width: -webkit-fill-available;">
	<center>
		<?php echo $footer; ?>
		<a href="#" onclick="popWin('apropos.php', '580', '600'); return false;">A propos</a>
	</center>
</div>
</footer>


<?php } ?>









</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php if (isUserConnected()) { ?>



	<?php
	$footer  = "<strong>".$_SESSION['CfgIdent']."</strong>";
	if (isset($_SESSION['CfgEmail']) && $_SESSION['CfgEmail']) $footer .= '&nbsp;<i class="fa fa-envelope"></i>&nbsp;<a href="mailto:'.$_SESSION['CfgEmail'].'">'.$_SESSION['CfgEmail'].'</a>';
	if (isset($_SESSION['CfgTel']) && $_SESSION['CfgTel']) $footer .= '&nbsp;<i class="fa fa-phone"></i>&nbsp;'.$_SESSION['CfgTel'];
	if (isset($_SESSION['CfgFax']) && $_SESSION['CfgFax']) $footer .= '&nbsp;<i class="fa fa-fax"></i>&nbsp;'.$_SESSION['CfgFax'];
	if (isset($_SESSION["CfgWeb"]) && $_SESSION["CfgWeb"]) {
		if (strpos($_SESSION['CfgWeb'], 'http://') !== false || strpos($_SESSION['CfgWeb'], 'https://') !== false) $href = $_SESSION['CfgWeb'];
		else $href = 'https://'.$_SESSION['CfgWeb'];
		$footer .= '&nbsp;<i class="fa fa-globe"></i>&nbsp;<a href="'.$href.'" onclick="window.open(this.href,\'_blank\');return false;">'.$_SESSION['CfgWeb'].'</a>';
	}
	if (isset($_SESSION['CfgAdmin']) && $_SESSION['CfgAdmin']) $footer .= '<br>'.$msg->read($PAGE_WEBMASTER).'<a href="mailto:'.$_SESSION['CfgAdmin'].'">'.$_SESSION['CfgAdmin'].'</a>';
	?>


	<!-- Footer -->
	<footer class="sticky-footer bg-white d-print-none">
		<div class="container my-auto">
			<div class="copyright text-center my-auto">
				<?php echo $footer; ?>
			</div>
		</div>
	</footer>
	<!-- End of Footer -->
<?php } ?>

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
	<i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Vous nous quittez ?</h5>
				<button class="close" type="button" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
		<div class="modal-body">Si vous voulez vraiment vous déconnecter, cliquer sur 'Oui... je veux partir' ci-dessous</div>
			<div class="modal-footer">
				<button class="btn btn-secondary" type="button" data-dismiss="modal">Non, rester</button>
				<a class="btn btn-primary" href="index.php?item=-1">Oui... je veux partir</a>
			</div>
		</div>
	</div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Datepicker - https://www.daterangepicker.com -->
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="js/daterangepicker/locale-<?php echo $_SESSION['lang']; ?>.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />

<!-- Timepicker - https://github.com/jonthornton/jquery-timepicker -->
<script type="text/javascript" src="js/jquery.timepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.timepicker.min.css" />

<!-- Bootstrap select - https://developer.snapappointments.com/bootstrap-select/ -->
<script type="text/javascript" src="js/bootstrap-select/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-select.min.css" />

<!-- CKEditor -->
<script type="text/javascript" src="js/ckeditor/ckeditor4/ckeditor.js"></script>

<!-- jQuery Validator -->
<script src="js/jquery.validate.min.js"></script>

<!-- Fênetres de popup -->
<script src="js/popup.js" type="text/javascript" charset="utf-8"></script>

<!-- jQuery UI -->
<script src="js/jquery-ui.pmt_14.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery.ui.autocomplete.html.js" type="text/javascript" charset="utf-8"></script>

<!-- Bootstrap colorpicker (https://github.com/itsjavi/bootstrap-colorpicker) -->
<script src="js/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-colorpicker/bootstrap-colorpicker.min.css" />


<!-- Pour la GED -->
<?php if ($item == '28' || isset($showGedScripts) && $showGedScripts) { ?>
	<script src="js/fileupload/vendor/jquery.ui.widget.js"></script>
	<script src="js/fileupload/blueimp/tmpl.min.js"></script>
	<script src="js/fileupload/blueimp/load-image.all.min.js"></script>
	<script src="js/fileupload/blueimp/canvas-to-blob.min.js"></script>
	<script src="js/fileupload/blueimp/jquery.blueimp-gallery.min.js"></script>
	<script src="js/fileupload/jquery.iframe-transport.js"></script>
	<script src="js/fileupload/jquery.fileupload.js"></script>
	<script src="js/fileupload/jquery.fileupload-process.js"></script>
	<script src="js/fileupload/jquery.fileupload-image.js"></script>
	<script src="js/fileupload/jquery.fileupload-audio.js"></script>
	<script src="js/fileupload/jquery.fileupload-video.js"></script>
	<script src="js/fileupload/jquery.fileupload-validate.js"></script>
	<script src="js/fileupload/jquery.fileupload-ui.js"></script>
	<script>
		var numfiche = "<?php if (isset($ged_num_fiche) && $ged_num_fiche != '') echo $ged_num_fiche; elseif (isset($ID)) echo $ID; else echo ''; ?>";
		var typefiche = "<?php if (isset($ged_type_fiche) && $ged_type_fiche != '') echo $ged_type_fiche; else echo 'ged'; ?>";
		var urlupload = "<?php if (isset($_SERVER["HTTP_REFERER"])) echo substr($_SERVER["HTTP_REFERER"], 0, strpos($_SERVER["HTTP_REFERER"], "index.php")); else echo ''; ?>";
	</script>
	<script src="js/fileupload/file_upload.js"></script>
<?php } ?>

<link href="<?php echo $_SESSION["ROOTDIR"]; ?>/css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
<link href="<?php echo $_SESSION["ROOTDIR"]; ?>/css/jquery-ui.min.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="script/jquery.desoform.js"></script>

<!-- Tag-it -->
<script src="js/tag-it/tag-it.pmt_14.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.tag-it.css" />

<script type="text/javascript" src="js/functions.js"></script>
</body>

</html>


<div style="display: none;" id="newMessagesPopupContent_hidden">
	<?php include_once 'include/postit.php';	// Si on a pas le fichier de la fonction ci-dessous inclus ?>
	<?php echo getNewMessagesPopupElements(); ?>
</div>


<script>
	$(document).ready(function(){
		// Quand on change l'état d'affichage du menu on le sauvegarde
		$('#sidebarToggle').click(function(){
			if ($('#accordionSidebar').hasClass('toggled')) var menuState = 'closed';
			else var menuState = 'opened';
			$.ajax({
				url : 'include/ajax/changeMenuShowMode.php?action=saveMenuShowMode',
				type : 'GET',
				data : 'menuShowMode=' + menuState,
				async: true,
				dataType : 'html',
				success : function(code_html, statut){
					console.log('Etat du menu enregistré');
				}
			});
		});


		// On active les popover bootstrap
		$(function () {
			$('[data-toggle="popover"]').popover();
		})

		// On active les tooltips bootstrap
		$(function () {
			$('[data-toggle="tooltip"]').tooltip();
		})
	});


	$('#fileupload').on('fileuploadfinished', function(){
		fileListReady();
	});


	// On affiche le nbr de nouveaux messages en haut et on met les nouveaux messages dans le popup
	$(document).ready(function(){
		var count = $('#new_messages_counter_hidden').attr('message_count');
		if ($('#new_messages_counter_hidden').attr('message_count')) count = '';
		$('#new_message_counter').html(count);

		var popup_content = $('#newMessagesPopupContent_hidden').html();

		$('#newMessagesPopupContentTitle').after(popup_content);
	});


	$(document).ready(function(){
		var notif_count = $('#notif_count_hidden').attr('notif_count');
		if (notif_count == 0) notif_count = '';
		$('#notif_count').html(notif_count);

	})
</script>
