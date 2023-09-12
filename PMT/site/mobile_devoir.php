<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2013 by IP-Solutions(contact@ip-solutions.fr)

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
 *		module   : mobile_devoir.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */

include("mobile_banner.php");
$currentPage = "devoirs";
include("mobile_menu.php");
//
// if ( @$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]) )
// {
// 	// Rien ok
// }
// else
// {
// 	header("Location: index.php?item=-1");
// }
?>



<div class="maincontent">
	<table class="table table-bordered table-striped">
		<tr>
			<th style="width: 1%;"></th>
			<th>Matière</th>
			<th>Contenu</th>
		</tr>

		<?php
			$dateForSelect = date("Y-m-d")." 00:00:00";

			$query = "SELECT ctn_items._IDitem, ctn_items._texte, ctn_items._devoirs, ctn_items._IDmat, ctn_items._date, edt_data._ID_pma, edt_data._text, edt_data._ID_examen, edt_data._etat FROM ctn_items INNER JOIN edt_data ON edt_data._IDx = ctn_items._IDcours WHERE ctn_items._visible = 'O' ";
			if ($_SESSION['CnxGrp'] == 1) $query .= "AND ctn_items._IDctn LIKE '%".$_SESSION['CnxClass']."%' ";
			else $query .= "AND ctn_items._ID = '".$_SESSION['CnxID']."' ";
			$query .= "AND ctn_items._date >= '".$dateForSelect."' ";
			$query .= "AND (edt_data._etat = 1 OR edt_data._etat = 5) ";

			$query .= "ORDER BY _date ASC ";

			// echo $query;
			$lastDate = "";
			$result = mysql_query($query, $mysql_link);
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				if ($lastDate != $row[4])
				{
					$lastDate = $row[4];
					echo "<tr><td colspan=\"3\" style=\"text-align: center; color: #004085; background-color: #cce5ff; border-color: #b8daff;\">";
						echo getDayNameByDayNumber(date('N', strtotime($row[4])) - 1)." ".date('d', strtotime($row[4]))." ".getMonthNameByMonthNumber(date('m', strtotime($row[4])));
					echo "</td></tr>";
				}


				echo "<tr id=\"row_".$row[0]."\">";
					echo "<td><button type=\"button\" onclick=\"toggleRow('".$row[0]."')\" class=\"btn btn-secondary\"><i class=\"fa fa-chevron-right image_rotate icon_row\" id=\"row_icon_".$row[0]."\" aria-hidden=\"true\"></i></button></td>";
					echo "<td style=\"text-align: center;\">".getMatNameByIdMat($row[3])."</td>";
					echo "<td style=\"text-align: center;\">".$row[1]."</td>";
				echo "</tr>";

				echo "<tr></tr>";

				echo "<tr id=\"row_info_".$row[0]."\" style=\"display: none;\" class=\"info_row hiddenRow\">";
					echo "<td style=\"text-align: center;\"></td>";
					echo "<td style=\"text-align: center;\" colspan=\"2\">".$row[2]."</td>";
				echo "</tr>";

			}





		?>



	</table>


<script>
	function toggleRow(row_id)
	{
		if (jQuery("#row_info_" + row_id).hasClass("hiddenRow"))
		{
			jQuery(".info_row").addClass("hiddenRow");
			jQuery(".icon_row").removeClass("image_rotated_90");

			jQuery("#row_icon_" + row_id).addClass("image_rotated_90");

			jQuery("#row_info_" + row_id).removeClass("hiddenRow");
			jQuery(".info_row").hide();
			jQuery("#row_info_" + row_id).show(1000);
			// $("#row_info_" + row_id).slideDown("fast");





		}
		else
		{
			jQuery("#row_icon_" + row_id).removeClass("image_rotated_90");
			jQuery("#row_info_" + row_id).addClass("hiddenRow");
			jQuery("#row_info_" + row_id).hide(400);
			jQuery(".info_row").hide();
		}
	}


</script>


<style>
.info_row {
	transition-property: all;
	transition-duration: .5s;
	transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
}

.hiddenRow {
	/* display: none; */
	max-height: 0px;
}

.image_rotate{
	/* transform: rotate(90deg); */
	transition: color 0.2s ease, background-color 0.2s ease, transform 0.3s ease;

	-webkit-transition: color 0.2s ease, background-color 0.2s ease, transform 0.3s ease;
}

.image_rotated_90 {
	transform: rotate(90deg);
	-ms-transform: rotate(90deg);
	-moz-transform: rotate(90deg);
	-webkit-transform: rotate(90deg);
	-o-transform: rotate(90deg);
}



</style>


</div>


<?php include("mobile_footer.php"); ?>
