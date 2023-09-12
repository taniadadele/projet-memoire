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
 *		module   : ged_files_search.php
 *		projet   : la page de gestion des fichiers PARTAGéS avec l'utilisateur
 *
 *		version  : 1.1
 *		auteur   : Thomas Dazy
 *		creation : 28/12/2018
 *
 */

?>


<?php
	$_SESSION['folder_path'] = $_GET['path'];


	if (isset($_POST['userID']) && $_POST['userID'] != "") $userID = $_POST['userID'];
	elseif (isset($_GET['userID']) && $_GET['userID'] != "") $userID = $_GET['userID'];
	else $userID = $_SESSION['CnxID'];

	if ($_SESSION['CnxGrp'] <= 1) $userID = $_SESSION["CnxID"];



	if (isset($_POST['specific']) && $_POST['specific'] != "") $specific = $_POST['specific'];
	elseif (isset($_GET['specific']) && $_GET['specific'] != "") $specific = $_GET['specific'];
	else $specific = "";

	if ($_SESSION['CnxGrp'] <= 1) $specific = "";

	if ($specific == "userFiles") $pathBase = "user_ged";
	else $pathBase = "ged";


	// Toujours vérifier si le répertoire utilisateur est existant
	if(!is_dir("download/".$pathBase."/files/".$ID))
	{
    // Sinon on le crée
		mkdir("download/".$pathBase."/files/".$ID, 0777, true);
    fopen("download/".$pathBase."/files/".$ID."/index.php", "w");
	}

	// initialisation
	$_ID = ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
		? ($ID ? $ID : $_SESSION["CnxID"])
		: $_SESSION["CnxID"] ;

	if ($_GET["ID"] === "9999999999" OR $_GET["ID"] == "") $ID = $_SESSION["CnxID"];
	if ($_SESSION["CnxGrp"] > 1) $_ID = $ID;

?>




<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<?php if ($specific != "userFiles") { ?>
		<div style="text-align: center;">
			<?php print($msg->read($USER_MY_FILES)); ?>
		</div>
	<?php } ?>

<?php if ($specific == "userFiles") { ?>

	<div class="alert alert-error" style="text-align: center; padding: 10px;">

		<div style="float: left; ">

			<a class="btn" style="color: black; margin-bottom: 10px; margin-top: -4%;" href="index.php?item=28&cmde=userFiles&userID=<?php echo $userID; ?>">
				<i class="fa fa-chevron-left"></i> Retour à la GED
			</a>
		</div>
		<div style="float: center;">
			<?php echo $msg->read($USER_FILES_OF_THE_USER)."&nbsp;<strong>".getUserNameByID($userID)."</strong>"; ?>
		</div>
	</div>
<?php } ?>

	<div style="overflow: hidden; width: 100%;">
		<div style="float: right;">
			<form class="form-search" action="index.php?item=28&cmde=search&specific=<?php echo $specific; ?>" method="POST" id="searchForm">
				<div class="input-append">

					<input type="hidden" name="userID" value="<?php echo $userID; ?>">
					<input type="hidden" name="specific" value="<?php echo $specific; ?>">


					<input class="span2 input-medium search-query" id="appendedInputButton" type="text" name="searchField" autocomplete="off" value="<?php echo stripslashes($_POST['searchField']); ?>">
					<button class="btn" type="submit"><?php echo $msg->read($USER_RESEARCH); ?></button>
				</div>
			</form>
		</div>

		<?php if ($specific != "userFiles") { ?>
			<div style="overflow: hidden; min-height: 30px;">
				<div class="btn-group">
					<a href="index.php?item=28" class="btn">Mes fichiers</a>
					<a href="index.php?item=28&cmde=shared" class="btn">Fichiers Partagés</a>
				</div>
			</div>

		<?php } ?>
</div>
</div>

<div class="maincontent" id="main_my_files">


	<table class="table table-striped" style="padding: 10px;">

		<?php

			if ($specific != "userFiles")
			{
				$groupOfUser = getGroupsByIDuser($_SESSION["CnxID"]);

				$groupeSelector = "";
				foreach ($groupOfUser as $key => $value) {
					$groupeSelector .= " or ";
					$groupeSelector .= "_share LIKE '%G_".$key."%'";
				}

				$searchField = $_POST['searchField'];

				$query  = "SELECT * FROM `images` ";
				$query .= "WHERE `_title` LIKE '%".$searchField."%' ";
				$query .= "AND `_ext` != 'fol' AND (";
				$query .= "`_ID` = '".$_SESSION["CnxID"]."' OR ";
				$query .= "`_share` LIKE '%C_".$_SESSION['CnxClass']."%' OR ";
				$query .= "`_share` LIKE '%U_".$_SESSION["CnxID"]."%' ";
				$query .= $groupeSelector.") ";
				$query .= "AND `_type` = 'ged' ";
				$query .= " ORDER BY `_ID` ASC";
			}
			else
			{
				$searchField = $_POST['searchField'];

				$query  = "SELECT * FROM `images` ";
				$query .= "WHERE `_title` LIKE '%".$searchField."%' ";
				$query .= "AND `_ext` != 'fol' ";
				$query .= "AND `_ID` = '".$userID."' ";
				$query .= "AND `_type` = 'user' ";
				$query .= " ORDER BY `_ID` ASC";
			}
			// echo $query;


			$result = mysqli_query($mysql_link, $query);

			if (mysqli_num_rows($result) == 0) echo "Aucuns résultats";

			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
				$NEWthumbnailUrl = "download/".$pathBase."/files/".$row[2]."/thumbnail/".$row[0].".".$row[7];

				// Si on est pas dans la ged administrative d'un utilisateur
				if ($specific != "userFiles")
				{
					if(file_exists($NEWthumbnailUrl)) $thumbnail_url = "ged_thumbnail.php?fileID=".base64_encode($row[0]);
					else $thumbnail_url = "images/filetype/".strtolower($row[7]).".png";
				}
				// Sinon
				else
				{
					if(file_exists($NEWthumbnailUrl)) $thumbnail_url = "ged_thumbnail.php?action=user_ged&fileID=".base64_encode($row[0]);
					else $thumbnail_url = "images/filetype/".strtolower($row[7]).".png";
				}


				$download_url = "download_ged.php?image=".base64_encode("download_".$row[0].".".$row[7]);
				if($row[10] != "") $parentLink = "&folderSearch=".$row[10];
				else $parentLink = "";
				if ($specific != "userFiles") $show_url = "index.php?item=28&fileSearch=".$row[0].$parentLink;
				else
				{
					$show_url = "index.php?item=28&cmde=userFiles&userID=".$userID."&fileSearch=".$row[0].$parentLink;
				}
		?>

				<tr class="tr_class_<?php echo $row[2]; ?>">
					<td style="text-align: center; width: 100px;">
						<span>
							<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><img src="<?php echo $thumbnail_url; ?>" style="height: 60px; text-align: center;"></a>
						</span>
					</td>
					<td>
						<a href="<?php echo $download_url; ?>" title="<?php echo $row[3]; ?>"><?php echo $row[3]; ?></a>
					</td>
					<td>
						<?php if ($row[2] == $userID) { ?>
							<a href="<?php echo $show_url; ?>">
								<button class="btn btn-default" type="button" title="<?php echo $msg->read($USER_SHOW_IN_PARENT); ?>">
									<i class="fa fa-eye" aria-hidden="true"></i>
								</button>
							</a>
						<?php } elseif ($specific != "userFiles") { ?>
							<span class="badge"><i class="fa fa-user">&nbsp;<?php echo getUserNameByID($row[2]); ?></i></span>
						<?php } ?>
					</td>
					<td>
						<span class="size"><?php echo number_format($row[9] , 0, ',', '.')." KB"; ?></span>
					</td>
				</tr>
		<?php } ?>
	</table>

</div>

<!-- <script src="script/jquery.min.vupload.js"></script> -->

<!-- Placer le JS ICI -->


</div>
