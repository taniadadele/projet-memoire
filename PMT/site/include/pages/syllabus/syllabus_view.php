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
 *		module   : syllabus_visu.php
 *		projet   : la page de visualisation d'un syllabus
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 01/03/2019
 */

$idSyllabus = $_GET['idsyllabus'];

if ($idSyllabus != '')
{
	$query = "SELECT * FROM `campus_syllabus` WHERE `_IDSyllabus` = '".$idSyllabus."' ";
	$result = mysqli_query($mysql_link, $query);
	$row   = remove_magic_quotes(mysqli_fetch_row($result));

	while ( $row ) {
		$idPMA 								 = $row[1];
		$objectifsSyllabus 		 = $row[2];
		$programmeSyllabus 		 = $row[3];
		$visibleSyllabus 			 = $row[4];
		$profSyllabus 				 = $row[5];
		$periodeSyllabus_1 		 = $row[6];
		$periodeSyllabus_2 		 = $row[7];
		$periodeSyllabus_total = $row[8];

		$query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_pma` = ".$idPMA." ";
		$result2 = mysqli_query($mysql_link, $query2);
		$row2   = remove_magic_quotes(mysqli_fetch_row($result2));
		$IDpromotion = $row2[1];

		$query3 = "SELECT `_ID` FROM `pole` WHERE `_ID` = ".$row2[2]." ";
		$result3 = mysqli_query($mysql_link, $query3);
		$row3   = remove_magic_quotes(mysqli_fetch_row($result3));
		$IDpole = $row3[0];

		$query4 = "SELECT `_IDmat` FROM `campus_data` WHERE `_IDmat` = ".$row2[3]." ";
		$result4 = mysqli_query($mysql_link, $query4);
		$row4   = remove_magic_quotes(mysqli_fetch_row($result4));
		$IDmatiere = $row4[0];

		break;
	}
}

$listeAnnee = json_decode(getParam("annee-niveau"), TRUE);
?>

<?php
// On récupère le pole - mat - année
$query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_pma` = '".$idPMA."' ";
$result2 = mysqli_query($mysql_link, $query2);
$row = mysqli_fetch_row($result2);
$pole_mat_annee =  $listeAnnee[$row[1]]." - ".getPoleNameByIdPole($row[2])." - ".getMatNameByIdMat($row[3]);
?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($SYLLABUS_SYLLABUS); ?></h1>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, true); ?><?php echo $pole_mat_annee; ?></h6>
  </div>
  <div class="card-body">
		<h5>Les objectifs</h5>
		<p><?php echo $objectifsSyllabus; ?></p>

		<hr>
		<h5>Le programme</h5>
		<p><?php echo $programmeSyllabus; ?></p>

		<hr>
		<h5>Le(s) professeur(s)</h5>
		<?php
		if ($idSyllabus != '')
		{
			$profArray = explode(';', $profSyllabus);
			foreach ($profArray as $key => $id) if ($id != '') echo getUserPictureNameBox($id);
		}
		?>

		<hr>
		<h5>Fichiers liés</h5>
		<?php
			$query = 'SELECT _IDimage, _title, _size, _ext FROM images WHERE _type = "syllabus" AND _ID = '.$idSyllabus;
			$result = mysqli_query($mysql_link, $query);
		?>
		<table class="table table-striped table-bordered">
			<?php
			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
				$fileext  			= substr($row[1], strrpos($row[1], '.'));
				$download_link 	= 'download_ged.php?action=syllabus&image='.base64_encode('download_'.$row[0].$fileext).'&id=title_'.$row[0];
				$miniature 			= 'ged_thumbnail.php?action=syllabusImage&file_name='.$row[1].'&index='.$row[0].'&syllabusID='.$idSyllabus;
				$size 					= $msg->read($SYLLABUS_BYTE, number_format($row[2], 0, ',', ' ')) ;

				echo '<tr>';
					echo '<td style="width: 100px;">';
						echo '<a href="'.$download_link.'" class="overlib" target="_blank"><img src="'.$miniature.'" style="width: 100px;" title="'.$row[1].'" alt="'.$row[1].'" /></a>';
					echo '</td>';
					echo '<td style="vertical-align: middle; align-middle">';
						echo '<a href="'.$download_link.'" target="_blank">';
							echo $row[1];
							echo '<br>';
							echo '<p class="small mb-0">'.$size.'</p>';
						echo '</a>';
					echo '</td>';
				echo '</tr>';
			}
			?>
		</table>
  </div>
</div>
