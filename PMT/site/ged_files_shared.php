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
 *		module   : ged_files_shared.php
 *		projet   : la page de gestion des fichiers PARTAGéS avec l'utilisateur
 *
 *		version  : 1.1
 *		auteur   : Thomas Dazy
 *		creation : 28/12/2018
 *
 */

?>


<?php
	if (isset($_GET['path'])) $_SESSION['folder_path'] = $_GET['path'];

	// initialisation
	// if ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) {
	// 	if (!isset($ID)) $ID =
	// }
	// $_ID = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
	// 	? ($ID ? $ID : $_SESSION["CnxID"])
	// 	: $_SESSION["CnxID"] ;
	//
	// if ($_GET["ID"] === "9999999999" OR $_GET["ID"] == "") $ID = $_SESSION["CnxID"];
	// if ($_SESSION["CnxGrp"] > 1) $_ID = $ID;

	$ID = $_SESSION['CnxID'];


	// Toujours vérifier si le répertoire utilisateur est existant
	if(!is_dir("download/ged/files/".$ID))
	{
    // Sinon on le crée
		mkdir("download/ged/files/".$ID, 0777, true);
    fopen("download/ged/files/".$ID."/index.php", "w");
	}


	// if ( $submit == "Valider" ) {
  //   // pour éviter les injections SQL
  //   $pwd       = str_replace(" ", "-", $pwd);
  //   $titre     = addslashes($titre);
  //   $fonction  = str_replace("\n", "<br/>", addslashes($fonction));
  //   $signature = str_replace("\n", "<br/>", addslashes($signature));
	//
  //   // les matières enseignées
  //   $idmat    = " ";
  //   for ($i = 0; $i < count($cbox); $i++)
  //     $idmat .= ( @$cbox[$i] )  ? "$cbox[$i] " : "" ;
	//
  //   $Query     = "update user_id ";
  //   $Query    .= "set _IDmat = '$idmat' ";
  //   $Query    .= "where _ID = '$_ID' ";
  //   $Query    .= "limit 1";
	//
  //   if ( !mysqli_query($mysql_link, $Query) )
  //     sql_error($mysql_link);
  //   else
  //     $_SESSION["CnxPasswd"] = $pwd;
	//
  //   // fichier à transférer
  //   $file = @$_FILES["UploadedFile"]["tmp_name"];
	//
  //   if ( $file AND authfile(@$_FILES["UploadedFile"]["name"]) ) {
  //     require_once "include/gallery.php";
	//
  //     $dest = ( getAccess() == 1 ) ? "$DOWNLOAD/photo/eleves" : "$DOWNLOAD/photo" ;
	//
  //     // création de la vignette
  //     vignette("$file|".@$_FILES["UploadedFile"]["name"], $dest, "$_ID.gif", $srcWidth, $srcHeight);
  //   }
	// }

	// lecture du compte
	// if ( ($result = mysqli_query($mysql_link, "select * from user_id where _ID = '$_ID' limit 1")) ) {
	// 	$row = remove_magic_quotes(mysqli_fetch_row($result));
	//
	// 	if ( $row ) {
	// 		// photos élèves et personnel ne sont pas dans le même répertoire
	// 		$image  = ( getAccess($row[1]) == 1 )
	// 			? "$DOWNLOAD/photo/eleves/$row[0].gif"
	// 			: "$DOWNLOAD/photo/$row[0].gif" ;
	//
	// 		// on vérifie si la photo existe
	// 		$photo  = ( file_exists($image) )
	// 			? $image
	// 			: $_SESSION["ROOTDIR"]."/css/themes/".$_SESSION["CfgTheme"]."/images/0.gif" ;
	//
	// 		// lectures de données à modifier
	// 		$class     = $row[3];
	// 		$pwd       = $row[10];
	// 		if($pwd != "")
	// 		{
	// 			$pwd   = "*****";
	// 		}
	// 		else
	// 		{
	// 			$pwd   = "";
	// 		}
	// 		$titre     = $row[14];
	// 		$fonction  = str_replace("<br/>", "\n", $row[15]);
	// 		$email     = $row[16];
	// 		$tel       = $row[18];
	// 		$mobile    = $row[19];
	// 		$adr1      = $row[21];
	// 		$adr2      = $row[22];
	// 		$zip       = $row[23];
	// 		$city      = $row[24];
	// 		$signature = str_replace("<br/>", "\n", $row[25]);
	// 		$avatar    = $row[30];
	//
	// 		switch ( $row[13] ) {
	// 			case "H" :
	//       	     		"<i class=\"fa fa-male\" style=\"color: black\"></i>";
	//             		break;
	//             	case "F" :
	//             		$sex = "<i class=\"fa fa-female\" style=\"color: black\"></i>";
	// 	           		break;
	// 	           	default :
	//             		$sex = "<i class=\"fa fa-question\" style=\"color: black\"></i>";
	//             		break;
	//             	}
	//
	// 		// lecture de la station émettrice
	// 		$poste     = _getHostName($row[29]);
	//
	// 		// lecture du groupe
	// 		$query     = "select _ident from user_group ";
	// 		$query    .= "where _IDgrp = '$row[1]' AND _lang = '".$_SESSION["lang"]."' ";
	// 		$query    .= "limit 1";
	//
	// 		$result    = mysqli_query($mysql_link, $query);
	// 		$data      = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
	//
	// 		// modification du compte
	// 		if ($row[1] == "1") {$linkupdate = "index.php?item=38&cmde=account&ID=$row[0]";} else {$linkupdate = "index.php?item=1&visu=0&cmde=new&ID=$row[0]";}
	// 		$update = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
	// 			? "<a href=\"".myurlencode($linkupdate)."\" class=\"icon-pencil\"></a>"
	// 			: "" ;
	//
	// 		}
	// 	}


?>

<script>
// jQuery( document ).ready(function() {
// 	jQuery.ajax({
// 		<?php
// 		if ( getAccess($row[1]) == 2 ) echo "url: 'getInfos.php?IDuser=$ID&type=3',";
// 		?>
// 		<?php
// 		if ( getAccess($row[1]) == 1 ) echo "url: 'getInfos.php?IDclass=$row[3]&IDuser=$ID&type=2',";
// 		?>
// 		success  : function(data) {
// 			jQuery("#divtr_<?php echo $ID; ?>").html(data);
// 		}
// 	});
//
// 	return false;
// });
</script>



<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
<style>
/* .swal-text {
	text-align: center;
} */
</style>







<?php
	$current_page = 'shared_files';
	$page_title = 'Fichiers partagés avec moi';
?>

<?php include(RESSOURCE_PATH['PAGES_FOLDER'].'ged/page_top.php'); ?>




	<?php

	// Afichage des éléments partagés à ma classe
		// echo create_breadcrumbs($_SESSION['folder_path'], $_SESSION["CnxID"]);
		// CnxClass
		$groupOfUser = getGroupsByIDuser($_SESSION["CnxID"]);

		$groupeSelector = "";
		foreach ($groupOfUser as $key => $value) {
			$groupeSelector .= " or ";
			$groupeSelector .= "_share LIKE '%G_".$key."%'";
		}
		$query  = "SELECT * FROM images WHERE ";
		$query .= "_share LIKE '%C_".$_SESSION['CnxClass']."%' ";
		// $query .= $groupeSelector;
		$query .= " ORDER BY _ID ASC ";
		// echo $query;
		$result = mysqli_query($mysql_link, $query);

		$fileOwner = "";


if (mysqli_num_rows($result) > 0) echo "<div><span style=\"font-size: 20px;\"><i class=\"fa fa-graduation-cap\"></i>&nbsp;Partagé avec la classe ".getClassNameByClassID($_SESSION['CnxClass'])." :</span></div>";
?>
	<table class="table table-striped" style="padding: 10px;">
		<?php
			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
				$NEWthumbnailUrl = "download/ged/files/".$row[2]."/thumbnail/".$row[0].".".$row[7];

				if(file_exists($NEWthumbnailUrl)) $thumbnail_url = "ged_thumbnail.php?fileID=".base64_encode($row[0]);

				else $thumbnail_url = "images/filetype/".strtolower($row[7]).".png";

				$download_url = "download_ged.php?image=".base64_encode("download_".$row[0].".".$row[7]);

				if ($fileOwner != $row[2])
				{
					$fileOwner = $row[2];
					// echo $fileOwner."---".$row[2];
				?>

					<tr>
						<td style="text-align: center; width: 100px;">
							<span>
								<a href="#" style="font-size: 0px;" onclick="showFolderContent(<?php echo $row[2]; ?>, 'class');" title="Partagé par: <?php echo getUserNameByID($row[2]); ?>"><img src="images/arrow-right.png" style="height: 60px; text-align: center;" id="image_class_<?php echo $row[2]; ?>"></a>

							</span>
						</td>
						<td>
							<a href="#" onclick="showFolderContent(<?php echo $row[2]; ?>, 'class');">Partagé par: <span class="badge"><i class="fa fa-user">&nbsp;<?php echo getUserNameByID($row[2]); ?></i></span></a>
						</td>
						<td></td>
					</tr>
				<?php
				}
		?>

				<tr class="tr_class_<?php echo $row[2]; ?>" style="display: none;">
					<td style="text-align: center; width: 100px;">
						<span>
							<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><img src="<?php echo $thumbnail_url; ?>" style="height: 60px; text-align: center;"></a>
						</span>
					</td>
					<td>
						<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><?php echo $row[3]; ?></a>
					</td>
					<td>
						<span class="size"><?php echo number_format($row[9] , 0, ',', '.')." KB"; ?></span>
					</td>

				</tr>
		<?php } ?>
	</table>




	<?php
	// Afichage des éléments partagés à mes groupes
	$groupOfUser = getGroupsByIDuser($_SESSION["CnxID"]);

	foreach ($groupOfUser as $key => $value) {

		echo "<div><span style=\"font-size: 20px;\"><i class=\"fa fa-users\"></i>&nbsp;Partagé avec le groupe ".getNomByIDgrp($key)." :</span></div>";
		$query  = "SELECT * FROM images WHERE ";
		$query .= "_share LIKE '%G_".$key."%'";
		$query .= " ORDER BY _ID ASC ";
		$result = mysqli_query($mysql_link, $query);
		$fileOwner = "";
	?>

	<table class="table table-striped" style="padding: 10px;">
		<?php
			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
				$NEWthumbnailUrl = "download/ged/files/".$row[2]."/thumbnail/".$row[0].".".$row[7];

				if(file_exists($NEWthumbnailUrl)) $thumbnail_url = "ged_thumbnail.php?fileID=".base64_encode($row[0]);
				else $thumbnail_url = "images/filetype/".strtolower($row[7]).".png";

				$download_url = "download_ged.php?image=".base64_encode("download_".$row[0].".".$row[7]);
				$javascriptSelector = $key."-".$row[2];
				if ($fileOwner != $row[2])
				{
					$fileOwner = $row[2];
				?>
					<tr>
						<td style="text-align: center; width: 100px;">
							<span>
								<a href="#" style="font-size: 0px;" onclick="showFolderContent('<?php echo $javascriptSelector; ?>', 'group');" title="Partagé par: <?php echo getUserNameByID($row[2]); ?>"><img src="images/arrow-right.png" style="height: 60px; text-align: center;" id="image_group_<?php echo $javascriptSelector; ?>"></a>
							</span>
						</td>
						<td>
							<a href="#" onclick="showFolderContent('<?php echo $javascriptSelector; ?>', 'group');">Partagé par: <span class="badge"><i class="fa fa-user">&nbsp;<?php echo getUserNameByID($row[2]); ?></i></span></a>
						</td>
						<td></td>
					</tr>
				<?php
				}
		?>

				<tr class="tr_group_<?php echo $javascriptSelector; ?>" style="display: none;">
					<td style="text-align: center; width: 100px;">
						<span>
							<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><img src="<?php echo $thumbnail_url; ?>" style="height: 60px; text-align: center;"></a>
						</span>
					</td>
					<td>
						<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><?php echo $row[3]; ?></a>
					</td>
					<td>
						<span class="size"><?php echo number_format($row[9] , 0, ',', '.')." KB"; ?></span>
					</td>

				</tr>
		<?php } ?>
	</table>
<?php } ?>





	<?php

	// Afichage des éléments partagés avec moi
		// echo create_breadcrumbs($_SESSION['folder_path'], $_SESSION["CnxID"]);
		// CnxClass
		$groupOfUser = getGroupsByIDuser($_SESSION["CnxID"]);

		$groupeSelector = "";
		foreach ($groupOfUser as $key => $value) {
			$groupeSelector .= " or ";
			$groupeSelector .= "_share LIKE '%G_".$key."%'";
		}
		$query  = "SELECT * FROM images WHERE ";
		$query .= "_share LIKE '%U_".$_SESSION["CnxID"]."%' ";
		$query .= " ORDER BY _ID ASC ";
		// echo $query;
		$result = mysqli_query($mysql_link, $query);

		$fileOwner = "";
	?>

	<?php
	if (mysqli_num_rows($result) > 0) echo "<div><span style=\"font-size: 20px;\"><i class=\"fa fa-user\"></i>&nbsp;Partagé avec moi :</span></div>";
	?>

<table class="table table-striped" style="padding: 10px;">
	<?php
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$NEWthumbnailUrl = "download/ged/files/".$row[2]."/thumbnail/".$row[0].".".$row[7];

			if(file_exists($NEWthumbnailUrl)) $thumbnail_url = "ged_thumbnail.php?fileID=".base64_encode($row[0]);
			else $thumbnail_url = "images/filetype/".strtolower($row[7]).".png";

			$download_url = "download_ged.php?image=".base64_encode("download_".$row[0].".".$row[7]);

			if ($fileOwner != $row[2])
			{
				$fileOwner = $row[2];
				// echo $fileOwner."---".$row[2];
			?>

				<tr>
					<td style="text-align: center; width: 100px;">
						<span>
							<a href="#" style="font-size: 0px;" onclick="showFolderContent(<?php echo $row[2]; ?>, 'user');" title="Partagé par: <?php echo getUserNameByID($row[2]); ?>"><img src="images/arrow-right.png" style="height: 60px; text-align: center;" id="image_user_<?php echo $row[2]; ?>"></a>

						</span>
					</td>
					<td>
						<a href="#" onclick="showFolderContent(<?php echo $row[2]; ?>, 'user');">Partagé par: <span class="badge"><i class="fa fa-user">&nbsp;<?php echo getUserNameByID($row[2]); ?></i></span></a>
					</td>
					<td></td>
				</tr>
			<?php
			}
	?>

			<tr class="tr_user_<?php echo $row[2]; ?>" style="display: none;">
				<td style="text-align: center; width: 100px;">
					<span>
						<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><img src="<?php echo $thumbnail_url; ?>" style="height: 60px; text-align: center;"></a>
					</span>
				</td>
				<td>
					<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><?php echo $row[3]; ?></a>
				</td>
				<td>
					<span class="size"><?php echo number_format($row[9] , 0, ',', '.')." KB"; ?></span>
				</td>

			</tr>
	<?php } ?>
</table>




  </div>
</div>





<style>
.rotated {
  transform: rotate(90deg);
  -ms-transform: rotate(90deg); /* IE 9 */
  -moz-transform: rotate(90deg); /* Firefox */
  -webkit-transform: rotate(90deg); /* Safari and Chrome */
  -o-transform: rotate(90deg); /* Opera */
}
</style>



<script src="script/jquery.min.vupload.js"></script>


<script type="text/javascript">
	function showFolderContent(idContent, type)
	{
		if ($('.tr_' + type + '_' + idContent).css("display") == "none") {
			$('.tr_' + type + '_' + idContent).fadeIn();
			$('#image_' + type + '_' + idContent).addClass("rotated");
		}
		else {
			$('.tr_' + type + '_' + idContent).fadeOut();
			$('#image_' + type + '_' + idContent).removeClass("rotated");
		}
	}
</script>
