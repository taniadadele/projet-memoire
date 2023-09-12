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
 *		module   : user_account.php
 *		projet   : la page de gestion du compte utilisateur
 *
 *		version  : 1.2
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 23/12/02 - par D. Laporte
 *			     affichage du poste de connexion
 *
 *		           15/06/03 - par D. Laporte
 *			     affichage des avatars
 *
 *			     17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *					 08/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7
 */


$ID        = ( @$_POST["ID"] )		// ID de l'utilisateur
	? (int) $_POST["ID"]
	: (int) @$_GET["ID"] ;

if (!isset($ID)) $ID = $_SESSION['CnxID'];
$visu      = (int) @$_GET["visu"];



$show      = (int) @$_GET["show"];
$page      = ( @$_GET["page"] )
	? (int) @$_GET["page"]
	: 1 ;

$pwd       = trim(@$_POST["pwd"]);
$titre     = trim(@$_POST["titre"]);
$fonction  = trim(@$_POST["fonction"]);
$email     = trim(@$_POST["email"]);
$tel       = trim(@$_POST["tel"]);
$mobile    = trim(@$_POST["mobile"]);
$adr1      = trim(@$_POST["adr1"]);
$adr2      = trim(@$_POST["adr2"]);
$zip       = trim(@$_POST["zip"]);
$city      = trim(@$_POST["city"]);
$signature = trim(@$_POST["signature"]);
$avatar    = (int) @$_POST["avatar"];
$cbox      = @$_POST["cbox"];

$submit    = ( @$_POST["submit"] )		// bouton de validation
	? $_POST["submit"]
	: @$_GET["submit"];



?>


<?php
	// Toujours vérifier si le répertoire utilisateur est existant
	if(!is_dir("download/user/files/".$ID))
	{
		mkdir("download/user/files/".$ID, 0777, true);
		fopen("download/user/files/".$ID."/index.php", "w");
	}

	// initialisation
	$_ID = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
		? ($ID ? $ID : $_SESSION["CnxID"])
		: $_SESSION["CnxID"] ;

	if ((isset($_GET["ID"]) && $_GET["ID"] === "9999999999") || !isset($_GET['ID']) || $_GET['ID'] == '' || $_GET['ID'] == 0) $ID = $_SESSION["CnxID"];
	if ($_SESSION["CnxGrp"] > 1) $_ID = $ID;


	if ( $submit == "Valider" ) {
          	// pour éviter les injections SQL
          	$pwd       = str_replace(" ", "-", $pwd);

		$titre     = addslashes($titre);
		$fonction  = str_replace("\n", "<br/>", addslashes($fonction));
		$signature = str_replace("\n", "<br/>", addslashes($signature));

		// les matières enseignées
		$idmat    = " ";
		for ($i = 0; $i < count($cbox); $i++)
			$idmat .= ( @$cbox[$i] )  ? "$cbox[$i] " : "" ;

		$Query     = "update user_id ";
		$Query    .= "set _IDmat = '$idmat' ";
		$Query    .= "where _ID = '$_ID' ";
		$Query    .= "limit 1";

		if ( !mysqli_query($mysql_link, $Query) )
			sql_error($mysql_link);
		else
			$_SESSION["CnxPasswd"] = $pwd;

		// fichier à transférer
		$file = @$_FILES["UploadedFile"]["tmp_name"];

		if ( $file AND authfile(@$_FILES["UploadedFile"]["name"]) ) {
			require_once "include/gallery.php";

			$dest = ( getAccess() == 1 ) ? "$DOWNLOAD/photo/eleves" : "$DOWNLOAD/photo" ;


			// création de la vignette
			vignette("$file|".@$_FILES["UploadedFile"]["name"], $dest, "$_ID.gif", $srcWidth, $srcHeight);
			}
		}

	// lecture du compte
	if ( ($result = mysqli_query($mysql_link, "select * from user_id where _ID = '$_ID' limit 1")) ) {
		$row = remove_magic_quotes(mysqli_fetch_row($result));

		if ( $row ) {
			// photos élèves et personnel ne sont pas dans le même répertoire
			$image  = ( getAccess($row[1]) == 1 )
				? $DOWNLOAD."/photo/eleves/$row[0].gif"
				: $DOWNLOAD."/photo/$row[0].gif" ;

			// On récupère l'image de profil
			$photo = 'ged_thumbnail.php?action=userImage&fileID='.base64_encode($row[0]);

			// lectures de données à modifier
			$class     = $row[3];
			$pwd       = $row[10];
			if($pwd != '') $pwd   = '*****';
			else $pwd   = '';
			$titre     = $row[14];
			$fonction  = str_replace("<br/>", "\n", $row[15]);
			$email     = $row[16];
			$tel       = $row[18];
			$mobile    = $row[19];
			$adr1      = $row[21];
			$adr2      = $row[22];
			$zip       = $row[23];
			$city      = $row[24];
			$signature = str_replace("<br/>", "\n", $row[25]);
			$avatar    = $row[30];

			// modification du compte
			$linkupdate = "index.php?item=38&cmde=account&ID=$row[0]&Litem=1";
			if ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) $update = '&nbsp;<a href="'.myurlencode($linkupdate).'" class="link-unstyled"><i class="fas fa-pencil-alt"></i></a>'; else $update = '';
		}
	}
?>


<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, true); ?><?php if ($row[0] != $_SESSION['CnxID']) echo 'Compte de '.getUserNameByID($row[0]); else echo 'Mon compte'; ?></h6>
  </div>
  <div class="card-body">

		<div class="row">
			<div class="col-md-9">

				<table class="table table-striped">
					<tr>
						<th colspan="2" style="text-align: center;">INFORMATIONS</th>
					</tr>
					<tr>
						<td><?php echo $msg->read($USER_NAME); ?></td>
						<td><?php echo getUserNameByID($row[0]); ?></td>
					</tr>

					<tr style="display: none;"><td></td><td></td></tr>

					<tr>
						<td><?php echo $msg->read($USER_USERID); ?></td>
						<td><?php echo "$row[9]"; ?></td>
					</tr>

					<tr style="display: none;"><td></td><td></td></tr>

					<tr>
						<td><?php echo $msg->read($USER_EMAIL); ?></td>
						<td><?php echo "<a href='mailto:".$row[16]."'>".$row[16]."</a>"; ?></td>
					</tr>

					<tr style="display: none;"><td></td><td></td></tr>

					<tr>
						<td><?php echo $msg->read($USER_INSCRIPTION); ?></td>
						<td><?php echo date2longfmt($row[5]); ?></td>
					</tr>

					<tr style="display: none;"><td></td><td></td></tr>

					<?php if ($_SESSION['CnxGrp'] > 2) { ?>
						<tr>
							<td><?php echo $msg->read($USER_CNX); ?></td>
							<td><?php echo date2longfmt($row[7]); ?></td>
						</tr>

						<tr style="display: none;"><td></td><td></td></tr>
					<?php } ?>


					<?php if ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) { ?>
						<tr>
							<td>Droits :</td>
							<td>
								<ul>
									<?php
										$sql  = "select _adm, _ident from user_admin ";
										$sql .= "where _adm > 0 AND _adm != 255 ";
										$sql .= "AND _lang = '".$_SESSION["lang"]."' ";
										$sql .= "AND _adm = 1 "; // Restreindre affichage
										$sql .= "order by _adm";
										$res  = mysqli_query($mysql_link, $sql);
										while ($adm = mysqli_fetch_row($res)) echo '<li>'.$adm[1].'</li>';

										if($row[8] == 255) echo '<li>Admin</li>';
										else echo '<li>Admin</li>';
									?>
								</ul>
							</td>
						</tr>
					<?php } ?>

				</table>


			</div>
			<div class="col-md-3">
				<div class="text-center">
					<img alt="<?php echo getUserNameByID($row[0]); ?>" src="<?php echo $photo; ?>" class="rounded-circle img-responsive mt-2" width="128" height="128">
					<div class="mt-2">
						<?php echo getUserNameByID($row[0]).' '.$update; ?>
					</div>


					<?php if ($ID == $_SESSION['CnxID']) { ?>
						<a class="btn btn-secondary" href="#editInfos" role="button"  data-toggle="modal" style="margin-top: 10px;">
							<i class="fas fa-pencil-alt"></i> <?php echo $msg->read($USER_EDITINFOS); ?>
						</a>
					<?php } ?>

					<?php if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) { ?>
						<a class="btn btn-secondary" href="index.php?item=28&cmde=userFiles&userID=<?php echo $_ID; ?>" style="margin-top: 10px;">
							<i class="fa fa-folder"></i> <?php echo $msg->read($USER_SEE_GED); ?>
						</a>
					<?php } ?>

					<?php
						if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) $link_certificat = "?id=".$row[0];
						// Si l'utilisateur sélectionnné est bien un élève ou si l'utilisateur connecté est un élève (et vois donc son compte)
						if ($_SESSION['CnxGrp'] != 2 && getParam('certificat_scol_show') == 1)
						{
						?>
							<a href="user_certificat_scolarite.php<?php echo $link_certificat; ?>" target="_blank" class="btn btn-secondary" style="margin-top: 10px;">
								<i class="fa fa-file"></i> Générer le certificat de scolarité
							</a>
						<?php } ?>

						<?php if ((isUserTeacher($row[0]) || isUserAdmin($row[0])) && $_SESSION['CnxGrp'] > 1) { ?>
							<a href="?item=16&userID=<?php echo $row[0]; ?>" class="btn btn-secondary" style="margin-top: 10px;">
								<i class="fas fa-clock"></i> Voir le nombre d'heures effectuées
							</a>
						<?php } ?>

						<?php if (isUserStudent($row[0]) && $_SESSION['CnxAdm'] == 255) { // Si l'utilisateur sélectionnné est bien un élève ou si l'utilisateur connecté est un élève (et vois donc son compte) ?>
							<a href="#exportModal" role="button" class="btn btn-secondary" data-toggle="modal" style="margin-top: 10px;"><i class="fa fa-download" aria-hidden="true"></i>&nbsp;Exporter les données</a>
						<?php } ?>
				</div>
			</div>
		</div>
  </div>
</div>





<?php
// Si l'utilisateur sélectionnné est bien un élève ou si l'utilisateur connecté est un élève (et vois donc son compte)
if (isUserStudent($row[0]) && $_SESSION['CnxAdm'] == 255)
{
?>
	<!-- Modal -->
	<div id="exportModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalLabelExport" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Export des données</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
				</div>
				<div class="modal-body">
					<a href="download_eleve_data_admin.php?userID=<?php echo $row[0]; ?>" class="btn btn-primary"><i class="fas fa-file-archive" aria-hidden="true"></i>&nbsp;Télécharger toutes les données</a>
					<hr>
					<a href="exports.php?item=29&cmde=&sortOrder=0&sortBy=0&idpromotion=<?php echo getUserClassIDByUserID($row[0]); ?>&idmatiere=0&idpole=0&idprof=0&date_1=<?php echo '01.'.getParam('START_M').'.'.getParam('START_Y'); ?>&date_2=<?php echo '31.'.getParam('END_M').'.'.getParam('END_Y'); ?>&time_1=07:00&time_2=23:45&status=0&lessonStatus=0&currentPage=1&nbElemPerPage=&type_UV=0" class="btn btn-secondary"><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;Exporter l'emploi du temps</a>
					<br><br>
					<a href="exports.php?item=69&cmde=&idsyllabus=&IDpole=0&IDpromotion=<?php echo getClassNiveauByClassID(getUserClassIDByUserID($row[0])); ?>&IDmatiere=0" class="btn btn-secondary"><i class="fas fa-file" aria-hidden="true"></i>&nbsp;Exporter les syllabus</a>
					<hr>
					<?php
						$query_2 = "SELECT _year FROM user_log WHERE _IDuser = '".$row[0]."' AND _year < '".getParam('START_Y')."' ";
						$result_2 = mysqli_query($mysql_link, $query_2);
						while ($row_2 = mysqli_fetch_array($result_2, MYSQLI_NUM)) echo "<a href=\"notes_pdf.php?IDcentre=1&IDclass=282&IDeleve=".$row[0]."&year=".$row_2[0]."&period=0\" class=\"btn btn-secondary\"><i class=\"fas fa-graduation-cap\" aria-hidden=\"true\"></i>&nbsp;Exporter le bulletin de ".$row_2[0]."</a><br><br>";
					?>
					<a href="notes_pdf.php?IDcentre=1&IDclass=282&IDeleve=<?php echo $row[0]; ?>&year=<?php echo getParam('START_Y'); ?>&period=0" class="btn btn-secondary"><i class="fa fa-graduation-cap" aria-hidden="true"></i>&nbsp;Exporter le bulletin actuel</a>
					<hr>
					<a href="absent_export.php?IDeleve=<?php echo $row[0]; ?>&year=<?php echo getParam('START_Y'); ?>" class="btn btn-secondary"><i class="fa fa-sticky-note" aria-hidden="true"></i>&nbsp;Exporter les absences</a>
					<br><br>
					<a href="download_ged_admin.php?userID=<?php echo $row[0]; ?>" class="btn btn-secondary"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;Exporter les fichiers administratifs</a>
					<br><br>
					<a href="download_copies_admin.php?userID=<?php echo $row[0]; ?>" class="btn btn-secondary"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;Exporter les copies</a>

				</div>
			</div>
		</div>
	</div>
<?php
}
?>










<?php
// Modifier son compte
if (($ID == $_SESSION["CnxID"]) && ($row[16] != ""))
{
	?>

	<!-- Modal -->
	<div id="editInfos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><?php echo $msg->read($USER_EDITINFOS); ?></h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<label for="modif_email">Email: </label>
						<input type="email" class="form-control" id="modif_email" name="modif_email" value="<?php echo $row[16]; ?>">
					</div>

					<div class="form-group">
						<label for="modif_pwd_old"><?php echo $msg->read($USER_PSWD_OLD); ?></label>
						<input type="password" class="form-control" id="modif_pwd_old" name="modif_pwd_old">
					</div>

					<div class="form-group">
						<label for="modif_pwd_new1"><?php echo $msg->read($USER_PSWD_NEW1); ?></label>
						<input type="password" class="form-control" id="modif_pwd_new1" name="modif_pwd_new1">
					</div>

					<div class="form-group">
						<label for="modif_pwd_new2"><?php echo $msg->read($USER_PSWD_NEW2); ?></label>
						<input type="password" class="form-control" id="modif_pwd_new2" name="modif_pwd_new2">
					</div>



					<?php
						if ($_SESSION['CnxGrp'] == 1) {
							// On récupère l'adresse d'étude de l'utilisateur
							$result = mysqli_query($mysql_link, "SELECT _valeur FROM rubrique_data WHERE _IDrubrique = '4' AND _IDdata = '".$ID."' ");
							while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $study_addr = $row[0];

							$result = mysqli_query($mysql_link, "SELECT _valeur FROM rubrique_data WHERE _IDrubrique = '5' AND _IDdata = '".$ID."' ");
							while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $study_cp = $row[0];

							$result = mysqli_query($mysql_link, "SELECT _valeur FROM rubrique_data WHERE _IDrubrique = '6' AND _IDdata = '".$ID."' ");
							while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) $study_city = $row[0];

							$result = mysqli_query($mysql_link, "SELECT _mobile, _born FROM user_id WHERE _ID = '".$ID."' ");
							while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
								$study_phone = $row[0];
								$study_born	 = $row[1];
							}
					?>
						<hr>

						<div class="form-group">
							<label for="addr_modif_1">Adresse d'étude:</label>
							<input type="text" class="form-control" id="addr_modif_1" name="addr_modif_1" value="<?php echo $study_addr; ?>">
						</div>

						<div class="form-group">
							<label for="addr_modif_city">Ville:</label>
							<input type="text" class="form-control" id="addr_modif_city" name="addr_modif_city" value="<?php echo $study_city; ?>">
						</div>

						<div class="form-group">
							<label for="addr_modif_cp">Code postal:</label>
							<input type="text" class="form-control" id="addr_modif_cp" name="addr_modif_cp" value="<?php echo $study_cp; ?>">
						</div>

						<hr>

						<div class="form-group">
							<label for="phone_modif">Numéro de téléphone:</label>
							<input type="text" class="form-control" id="phone_modif" name="phone_modif" value="<?php echo $study_phone; ?>">
						</div>

						<div class="form-group">
							<label for="born_modif">Date de naissance:</label>
							<input type="text" class="form-control" id="born_modif" name="born_modif" value="<?php echo date('d/m/Y', strtotime($study_born)); ?>">
						</div>
					<?php } ?>



				</div>
				<div class="modal-footer">
					<button class="btn btn-danger" type="button" data-dismiss="modal" aria-hidden="true"><?php echo $msg->read($USER_INPUTCANCEL); ?></button>
					<button class="btn btn-success" type="button" id="modif_valid"><?php echo $msg->read($USER_INPUTOK); ?></button>
				</div>
			</div>
		</div>
	</div>





	<script>
		jQuery("#modif_valid").click(function() {
			if(jQuery("#modif_pwd_new1").val() == jQuery("#modif_pwd_new2").val())
			{
				jQuery.ajax({
					method: "POST",
					url: 'calcnbh.php?ID=<?php echo $ID; ?>',
					data: "modif_email="+jQuery("#modif_email").val()+"&modif_pwd_old="+jQuery("#modif_pwd_old").val()+"&modif_pwd_new1="+jQuery("#modif_pwd_new1").val()+"&modif_pwd_new2="+jQuery("#modif_pwd_new2").val() + "&addr_modif_1=" + jQuery('#addr_modif_1').val() + "&addr_modif_city=" + jQuery('#addr_modif_city').val() + "&addr_modif_cp=" + jQuery('#addr_modif_cp').val() + "&phone_modif=" + jQuery('#phone_modif').val() + "&born_modif=" + jQuery('#born_modif').val(),
					success  : function(data) {
						jQuery("#formncm").html('<a href="mailto:'+jQuery("#modif_email").val()+'">'+jQuery("#modif_email").val()+'</a>')
						jQuery('#editInfos').modal('hide');
						Toast.fire({
						  icon: 'success',
						  title: 'Enregistré',
							text: '<?php echo $msg->read($USER_EDITINFOS_OK); ?>'
						})
					}
				});
			}
			else
			{
				alert("<?php echo $msg->read($USER_PWSD_NOTSAME); ?>");
			}
		});
	</script>
	<?php
}
?>
