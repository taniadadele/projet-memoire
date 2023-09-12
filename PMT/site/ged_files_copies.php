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
 *		projet   : la page de gestion des copies de l'utilisateur
 *
 *		version  : 1.1
 *		auteur   : Thomas Dazy
 *		creation : 01/07/2019
 *
 */


if (isset($_GET['idUV'])) $idUV = addslashes(stripslashes($_GET['idUV']));
else $idUV = "";
if (isset($_GET['IDmat'])) $IDmat = addslashes(stripslashes($_GET['IDmat']));
else $IDmat = "";
if (isset($_GET['user_ID'])) $user_ID = addslashes(stripslashes($_GET['user_ID']));
elseif ($_SESSION['CnxGrp'] < 4) $user_ID = $_SESSION['CnxID'];
else $user_ID = "";


if (isset($_GET['start_date'])) $start_date = str_replace('%2F', '/', addslashes(stripslashes($_GET['start_date'])));
else $start_date = '';

if (!$start_date) $start_date = '01/'.date("m/Y");
?>







<script src="script/sweetalert2.min.js"></script>
<style>
.swal-text {
	text-align: center;
}
</style>


<?php
	$current_page = 'copies';
	$page_title = 'Mes copies';
?>

<?php include(RESSOURCE_PATH['PAGES_FOLDER'].'ged/page_top.php'); ?>




  <table class="table table-bordered table-striped">
    <?php
    $oldUvPma = "";
      $query  = "SELECT `_IDimage`, `_title`, `_date`, `_ext`, `_size`, `_attr`, `_ID` FROM `images` WHERE ";
      $query .= "_type = 'copies_eleves' ";
      if ($_SESSION['CnxAdm'] != 255) $query .= "AND `_ID` = '".$_SESSION['CnxID']."' ";
      if ($_SESSION['CnxAdm'] == 255 && isset($idUV) && $idUV != "" && $idUV != 0) $query .= "AND `_attr` LIKE '%".$idUV."_%' ";
      if ($_SESSION['CnxAdm'] == 255 && isset($IDmat) && $IDmat != "" && $IDmat != 0) $query .= "AND `_attr` LIKE '%_".$IDmat."%' ";
      if ($_SESSION['CnxAdm'] == 255 && isset($user_ID) && $user_ID != "" && $user_ID != 0) $query .= "AND `_ID` = '".$user_ID."' ";
			$query .= "AND DATE(`_date`) >= '".changeDateTypeFromFRToEN($start_date)."' ";
      $query .= "ORDER BY `_attr` ASC, `_date` ASC, `_ID` ASC ";
// echo $query;
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        if ($oldUvPma != $row[5])
        {
          $fileName_1 = explode("_", $row[5]);
          if ($fileName_1[0] != 0) $title = "UV - ".getUVNameByUVID($fileName_1[0]);
          elseif ($fileName_1[1] != 0) $title = getPoleNameByIdPole(getPoleIDByPMAID($fileName_1[1]))." - ".getMatNameByIdMat(getMatIDByPMAID($fileName_1[1]));
          echo "<tr style=\"background-color: #f9f9f9;\">";
            if ($_SESSION['CnxAdm'] != 255) echo "<th colspan=\"3\" style=\"text-align: center;\"><b>".$title."</b></th>";
            else echo "<th colspan=\"5\" style=\"text-align: center;\"><b>".$title."</b></th>";
					echo "</tr>";
          $oldUvPma = $row[5];
        }
        echo "<tr id=\"tr_".$row[0]."\">";
          echo "<td style=\"width: 75px;\"><a href=\"download_copie.php?user_file=".$row[0]."&file_name=".addslashes($row[1])."\"><img src=\"images/filetype/pdf.png\" style=\"width: 100%;\"></a></td>";
          echo "<td style=\"vertical-align: middle;\">".$row[1]."</td>";
          if ($_SESSION['CnxAdm'] == 255) echo "<td style=\"vertical-align: middle;\">".getUserNameByID($row[6])."</td>";
          echo "<td style=\"vertical-align: middle;\">".number_format($row[4] , 0, ',', '.')." KB"."</td>";

					if ($_SESSION['CnxAdm'] == 255) echo "<td style=\"vertical-align: middle; text-align: center; width: 1%;\"><button style=\"margin: 0 5px;\" class=\"btn btn-danger\" onclick=\"removeCopie(".$row[0].")\"><i class=\"fa fa-times\"></i></button></td>";
        echo "</tr>";
      }
    ?>



  </table>



</div>


<script>
// ---------------------------------------------------------------------------
// Fonction: Supprime la copie d'un élève
// IN:		   L'ID de la copie (INT) (POST)
// OUT: 		 Alert (INT)
// ---------------------------------------------------------------------------
function removeCopie(id_copie) {

	if ( confirm( "Êtes vous sûr de vouloir supprimer cette copie ?" ) ) {
		$.ajax({
			url : 'ged_ajax.php?action=removeCopie',
			type : 'POST', // Le type de la requête HTTP, ici devenu POST
			data : 'ID_copie=' + id_copie,

			dataType : 'html', // On désire recevoir du HTML
			success : function(code_html, statut){ // code_html contient le HTML renvoyé
				// alert("Return: " + code_html)
				if (code_html == "0") alert('Il y a eu une erreur, merci de réessayer');
				else
				{
					$("#tr_" + id_copie).fadeOut();
				}
			}
		});
	}
}

</script>
