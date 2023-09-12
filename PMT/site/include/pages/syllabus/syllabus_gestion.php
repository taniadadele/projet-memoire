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
 *		module   : syllabus_gestion.php
 *		projet   : la page de modification/création de syllabus
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 01/03/2019
 */


if (isset($_GET['idsyllabus'])) $idSyllabus = $_GET['idsyllabus'];
$creation = 0;

if (isset($idSyllabus) && $idSyllabus != "" && $_SESSION['CnxAdm'] != 255)
{
	$query = "SELECT `_IDSyllabus` FROM `campus_syllabus` WHERE `_IDSyllabus` = '".$idSyllabus."' AND `_idUser` LIKE '%;".$_SESSION['CnxID'].";%' ";
	$result = mysqli_query($mysql_link, $query);
	$numRows = mysqli_num_rows($result);
	if ($numRows == 0) exit(0);
}

// Sur cette page, lors de la création d'un syllabus, j'insert d'abord une ligne vide dans la base de donnée qui sera remplie par la suite, cette ligne sert à avoir dès le début (avant l'import des données de l'utilisateur) l'id du syllabus pour la zone de GED
if ((isset($idSyllabus) && $idSyllabus == "") || !isset($idSyllabus))
{
	$creation = 1;

	$query = "INSERT INTO `campus_syllabus`(`_IDSyllabus`, `_IDPMA`, `_objectifs`, `_programme`, `_visible`, `_idUser`, `_periode_1`, `_periode_2`, `_periode_total`) VALUES (NULL, '0', '', '', 'O', '', '0', '0', '0')";
	@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

	$query2 = "SELECT LAST_INSERT_ID() FROM `campus_syllabus` WHERE 1";
	$result2 = mysqli_query($mysql_link, $query2);
	$row2   = remove_magic_quotes(mysqli_fetch_row($result2));
	while ($row2) {
		$idSyllabus = $row2[0];
		break;
	}
}

// Toujours vérifier si le répertoire du syllabus est existant
if(isset($idSyllabus) && !is_dir("download/syllabus/files/".$idSyllabus))
{
	mkdir("download/syllabus/files/".$idSyllabus, 0777, true);
	fopen("download/syllabus/files/".$idSyllabus."/index.php", "w");
}


if (isset($_POST['PMA']) && $_POST['PMA'] != "") $IDPMA = $_POST['PMA'];
else $IDPMA = "";

$temp_post = array('IDpole', 'IDpromotion', 'IDmatiere', 'periodeSyllabus_1', 'periodeSyllabus_2', 'periodeSyllabus_total');
foreach ($temp_post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

if (!isset($IDpromotion)) $IDpromotion = $_SESSION["CnxClass"];

$idPMA = $IDPMA;

// Si l'utilisateur a bien cliqué sur 'valider'
if (isset($_POST['isValidation']) && $_POST['isValidation'] == "1")
{
	// Si c'est une modification et non une création
	if ($idSyllabus != "")
	{
		// On récupère la liste des profs
		$listProfs = ";";
		if (isset($_POST['fieldProfesseur']))
			foreach ($_POST['fieldProfesseur'] as $key => $id) if ($id != 0) $listProfs .= $id.";";
		if (strpos($listProfs, ";".$_SESSION['CnxID'].";") !== false);
		elseif ($_SESSION['CnxAdm'] != 255) $listProfs .= $_SESSION['CnxID'].";";

		$query = "UPDATE `campus_syllabus` SET `_IDPMA` = '".$IDPMA."', `_objectifs` = '".addslashes(stripslashes($_POST['objectifsSyllabus_ckeditor']))."',`_programme` = '".addslashes(stripslashes($_POST['programmeSyllabus_ckeditor']))."',`_visible` = 'O',`_idUser` = '".$listProfs."', `_periode_1` = '".$periodeSyllabus_1."', `_periode_2` = '".$periodeSyllabus_2."', `_periode_total` = '".$periodeSyllabus_total."' WHERE `_IDSyllabus` = '".$idSyllabus."' ";
		if (@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link)) == 1) $success = 1;
		else $success = 2;
	}
}


if (isset($idSyllabus) && $idSyllabus != "" && $creation == 0)
{
	$query = "SELECT * FROM `campus_syllabus` WHERE `_IDSyllabus` = '".$idSyllabus."' ";
	$result = mysqli_query($mysql_link, $query);

	while ($row = mysqli_fetch_row($result)) {
		$idPMA                      = $row[1];
		$objectifsSyllabus_ckeditor = $row[2];
		$programmeSyllabus_ckeditor = $row[3];
		$visibleSyllabus            = $row[4];
		$profSyllabus               = $row[5];
		$periodeSyllabus_1          = $row[6];
		$periodeSyllabus_2          = $row[7];
		$periodeSyllabus_total      = $row[8];


		$query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_pma` = ".$idPMA." ";
		$result2 = mysqli_query($mysql_link, $query2);
		$row2   = remove_magic_quotes(mysqli_fetch_row($result2));
		$IDpromotion = $row2[1];

		$query3  = "SELECT `_ID` FROM `pole` WHERE `_ID` = ".$row2[2]." ";
		$result3 = mysqli_query($mysql_link, $query3);
		$row3    = remove_magic_quotes(mysqli_fetch_row($result3));
		$IDpole  = $row3[0];


		$query4    = "SELECT `_IDmat` FROM `campus_data` WHERE `_IDmat` = ".$row2[3]." ";
		$result4   = mysqli_query($mysql_link, $query4);
		$row4      = remove_magic_quotes(mysqli_fetch_row($result4));
		$IDmatiere = $row4[0];


		break;
	}
}
else {
	$idPMA = $objectifsSyllabus_ckeditor = $programmeSyllabus_ckeditor = $visibleSyllabus = $profSyllabus = $periodeSyllabus_1 = $periodeSyllabus_2 = $periodeSyllabus_total = '';
}

$submit   = @$_GET["submit"];
?>





	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
	  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($SYLLABUS_SYLLABUS); ?></h1>
	</div>

	<div class="card shadow mb-4">
	  <div class="card-header py-3">
	    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, true); ?><?php if (isset($idSyllabus) && $idSyllabus != '') echo 'Modification'; else echo 'Création'; ?></h6>
	  </div>
	  <div class="card-body">
			<form action="index.php?item=69&cmde=new&idsyllabus=<?php if (isset($idSyllabus)) echo $idSyllabus; ?>" method="POST" id="formulaire">
				<table style="width: 100%;">













					<tr>
						<td class="labelForData"><strong>La matière liée: </strong></td>
						<td>
							<?php echo showPMAList("PMA", $idPMA); ?>
						</td>
					</tr>



					<tr>
						<td class="labelForData"><strong>Les objectifs: </strong></td>
						<td>
							<textarea id="objectifsSyllabus_ckeditor" name="objectifsSyllabus_ckeditor"><?php echo $objectifsSyllabus_ckeditor; ?></textarea>
							<script>
							$(document).ready(function(){
								CKEDITOR.replace('objectifsSyllabus_ckeditor');
							})

							</script>

						</td>
					</tr>



					<tr style="border-top: 10px solid transparent;">
						<td class="labelForData"><strong>Le programme: </strong></td>
						<td>
							<textarea id="programmeSyllabus_ckeditor" name="programmeSyllabus_ckeditor"><?php echo $programmeSyllabus_ckeditor; ?></textarea>
							<script>
							$(document).ready(function(){
								CKEDITOR.replace('programmeSyllabus_ckeditor');
							})

							</script>
						</td>
					</tr>




					<tr style="border-top: 10px solid transparent;">
						<td class="labelForData"><strong>Les professeurs: </strong></td>
						<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery("#fieldProfesseur").tagit({
									autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=16", html: 'html'},
									allowDuplicates: false,
									singleField: false,
									fieldName: "fieldProfesseur[]"
								});

								<?php
								if ($_SESSION["CnxAdm"] != 255 AND $_SESSION["CnxGrp"] != 4) echo "jQuery(\"#fieldProfesseur\").tagit(\"createTag\", \"".getUserNameByID($_SESSION['CnxID'])."<span class='hidden'>".$_SESSION['CnxID']."</span>\");";
								?>

								$(".tagit-close").hide();
								// On affiche les matières dans les Tag-IT
								<?php
								if ($idSyllabus != "")
								{
									$profArray = explode(";", $profSyllabus);
									foreach ($profArray as $key => $id) {
										if ($id != "") echo "jQuery(\"#fieldProfesseur\").tagit(\"createTag\", \"".getUserNameByID($id)."<span class='hidden'>".$id."</span>\");";
									}

								}
								?>
							});
						</script>

						<td>
							<ul id="fieldProfesseur" name="fieldProfesseur" class="tagit ui-widget ui-widget-content ui-corner-all">
								<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable">
									<span class="tagit-label"></span>
								</li>
							</ul>
						</td>



					</tr>

					<?php
						$listPeriodes = json_decode(getParam('periodeList'), true);
						$periode_1_label = $listPeriodes[1];
						$periode_2_label = $listPeriodes[2];
					?>


					<tr style="border-top: 10px solid transparent;">
						<td class="labelForData"><strong><?php echo $periode_1_label; ?>: </strong></td>
						<td>
							<?php
								$periode_1_temp = explode(':', $periodeSyllabus_1);
							?>
							<select id="tempPeriode_1_h" class="custom-select col-1" onchange="changePeriode('1')">
								<?php
									for ($i = 0; $i <= 300; $i++) {
										if ($periode_1_temp[0] == $i) $selected = "selected";
										else $selected = "";
										echo "<option value=\"".$i."\" ".$selected.">".$i."</option>";
									}
								?>
							</select> heures
							<select id="tempPeriode_1_m" class="custom-select col-1" onchange="changePeriode('1')">
								<?php
								$i = 0;
								while ($i <= 45)
								{
									if ($periode_1_temp[1] == $i) $selected = "selected";
									else $selected = "";
									echo "<option value=\"".$i."\" ".$selected.">".$i."</option>";
									$i += 15;
								}

								?>
							</select> minutes
							<input type="hidden" id="periodeSyllabus_1" name="periodeSyllabus_1" value="<?php echo $periodeSyllabus_1; ?>" />
						</td>
					</tr>

					<tr style="border-top: 10px solid transparent;">
						<td class="labelForData"><strong><?php echo $periode_2_label; ?>: </strong></td>
						<td>
							<?php
								$periode_2_temp = explode(':', $periodeSyllabus_2);
							?>
							<select id="tempPeriode_2_h" class="custom-select col-1" onchange="changePeriode('2')">
								<?php
									for ($i = 0; $i <= 300; $i++) {
										if ($periode_2_temp[0] == $i) $selected = "selected";
										else $selected = "";
										echo "<option value=\"".$i."\" ".$selected.">".$i."</option>";
									}
								?>
							</select> heures
							<select id="tempPeriode_2_m" class="custom-select col-1" onchange="changePeriode('2')">
								<?php
								$i = 0;
								while ($i <= 45)
								{
									if ($periode_2_temp[1] == $i) $selected = "selected";
									else $selected = "";
									echo "<option value=\"".$i."\" ".$selected.">".$i."</option>";
									$i += 15;
								}

								?>
							</select> minutes
							<input type="hidden" id="periodeSyllabus_2" name="periodeSyllabus_2" value="<?php echo $periodeSyllabus_2; ?>" />
						</td>
					</tr>

					<tr style="border-top: 10px solid transparent;">
						<td class="labelForData"><strong>Période totale: </strong></td>
						<td>
							<?php
								$periode_tot_temp = explode(':', $periodeSyllabus_total);
							?>
							<select id="tempPeriode_total_h" class="custom-select col-1" onchange="changePeriode('total')">
								<?php
									for ($i = 0; $i <= 300; $i++) {
										if ($periode_tot_temp[0] == $i) $selected = "selected";
										else $selected = "";
										echo "<option value=\"".$i."\" ".$selected.">".$i."</option>";
									}
								?>
							</select> heures
							<select id="tempPeriode_total_m" class="custom-select col-1" onchange="changePeriode('total')">
								<?php
								$i = 0;
								while ($i <= 45)
								{
									if ($periode_tot_temp[1] == $i) $selected = "selected";
									else $selected = "";
									echo "<option value=\"".$i."\" ".$selected.">".$i."</option>";
									$i += 15;
								}
								?>
							</select> minutes
							<input type="hidden" id="periodeSyllabus_total" name="periodeSyllabus_total" value="<?php echo $periodeSyllabus_total; ?>" />
						</td>
					</tr>

					<script>
						function changePeriode(periodeName)
						{
							var hours = jQuery('#tempPeriode_' + periodeName + '_h').val();
							var minutes = jQuery('#tempPeriode_' + periodeName + '_m').val();
							jQuery('#periodeSyllabus_' + periodeName).val(hours + ':' + minutes);
						}
					</script>

				</table>
				<input type="hidden" name="isValidation" id="isValidation" value="0">
			</form>

			<?php
				$showGedScripts = true;
				$ged_num_fiche = $idSyllabus;
				$ged_type_fiche = 'syllabus';
			?>



			<!-- blueimp Gallery styles -->
			<link rel="stylesheet" href="css/fileupload/blueimp/blueimp-gallery.min.css">
			<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
			<link rel="stylesheet" href="css/fileupload/jquery.fileupload.css">
			<link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui.css">
			<!-- CSS adjustments for browsers with JavaScript disabled -->
			<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-noscript.css"></noscript>
			<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui-noscript.css"></noscript>

			<div style="background-color: white; padding: 10px">
				<!-- The file upload form used as target for the file upload widget -->
				<form id="fileupload" action="index.php" method="POST" enctype="multipart/form-data">
					<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
					<div class="row fileupload-buttonbar" style="margin-left: 5px">
						<div class="col-lg-12">

							<div style="display: inline-block; float: left; width: 85%">
								<!-- The fileinput-button span is used to style the file input field as button -->
								<span class="btn btn-default fileinput-button" style="width: 100%; border: grey 3px dashed; line-height: 28px;">
									<i class="glyphicon glyphicon-plus"></i>
									<span>Ajouter un fichier</span>
									<input type="file" name="files[]" multiple>
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
			<?php include('file_upload_template.php'); ?>




			<button type="submit" id="valid_btn" class="btn btn-success"><?php echo $msg->read($SYLLABUS_SAVE); ?></button>
			<a href="index.php?item=<?php echo $item; ?>" class="btn btn-danger"><i class="fas fa-chevron-left"></i>&nbsp;<?php echo $msg->read($SYLLABUS_BACK); ?></a>

	  </div>
	</div>






<script type="text/javascript">
	$('#valid_btn').click(function() {
		$('#isValidation').attr("value", "1");
		$('#formulaire').submit();
	});

	<?php if (isset($success) && $success == 1) { ?>
		Toast.fire({
			icon: 'success',
			title: 'Enregistré',
		});
	<?php } elseif (isset($success) && $success == 2) { ?>
		Toast.fire({
			icon: 'error',
			title: 'Erreur',
		});
	<?php } ?>

	// Function qui reload la page lors d'un upload
	$('#fileupload').bind('fileuploadstop', function (e) {
		console.log('Uploads finished');
			// location.reload(); // refresh page
			location.replace(location.href + "&idsyllabus=<?php if (isset($idSyllabus)) echo $idSyllabus; ?>")
	});
</script>




<script>
	// Ce qui se passe quand jquery upload est prêt
	function fileListReady() {

	}
</script>







<link href="/css/file.css" rel="stylesheet">
<!-- <link rel="stylesheet" href="css/blueimp/blueimp-gallery.min.css" /> -->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="css/fileupload/jquery.fileupload.css" />
<link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui.css" />
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-noscript.css" /></noscript>
<noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui-noscript.css" /></noscript>
