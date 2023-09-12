<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2009 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : admin_reset.php
 *		projet   : la page de gestion des tables à vider
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 30/10/09
 *		modif    : 
 */


$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) $_SESSION["CnxCentre"] ;

$trunc    = (int) @$_POST["trunc"];			// sélection globale

$submit   = @$_POST["valid_x"];			// bouton validation
//---------------------------------------------------------------------------
?>


<?php
	// vérification des autorisations
	admSessionAccess();

	// modification des utilisateurs
	if ( $submit ) {
		$cb   = @$_POST["cb"];
		$date = @$_POST["date"];

		for ($i = 0; $i < count($cb); $i++)
			if ( @$cb[$i] ) {
				$result = mysqli_query($mysql_link, "select _table from reset where _IDitem = '$cb[$i]' limit 1");
				$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

				$list   = explode(",", $row[0]);
				$year   = date("Y") - $date[$i];
				$count  = 0;

				for ($j = 0; $j < count($list); $j++) {
					// test de la date
					$return = mysqli_query($mysql_link, "select _date from @$list[$j] where EXISTS (select _date from @$list[$j])");
					$exist  = (bool) $return;

					$where  = ( $exist AND $date[$i] ) ? "_date < '$year-".date("m")."-".date("d")."'" : "true" ;

					if ( mysqli_query($mysql_link, "delete from $list[$j] where $where") )
						$count +=  mysqli_affected_rows($mysql_link);
					}

				if ( $count ) {
					if ( mysqli_query($mysql_link, "delete from reset_log where _IDitem = '$cb[$i]' limit 1") )
						mysqli_query($mysql_link, "insert into reset_log values('$cb[$i]', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '".date("Y-m-d H:i:s")."', '$IDcentre', '$count')");

					// et les pièces jointes
					}
				}
		}
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($ADMIN_MANAGER)); ?>
	</div>
</div>

<div class="maincontent">

	<form id="formulaire" action="index.php" method="post">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"  value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"  value=\"$cmde\" /></p>
			");
	?>

		<table class="width100">
		  <tr>
			<td style="width:50%;" class="align-right">
			    	<?php print($msg->read($ADMIN_CHOOSECENTER)); ?> 
			</td>
			<td style="width:50%;">
				<label for="IDcentre">
			  	<select id="IDcentre" name="IDcentre" onchange="document.forms.formulaire.submit()">
				<?php
					// lecture des centres constitutifs
					$query  = "select _IDcentre, _ident from config_centre ";
					$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "order by _IDcentre";

					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row ) {			
						printf("<option value=\"$row[0]\" %s>$row[1]</option>", ($IDcentre == $row[0]) ? "selected=\"selected\"" : "");

						$row = remove_magic_quotes(mysqli_fetch_row($result));
						}				
				?>
				</select> <img src="<?php echo $_SESSION["ROOTDIR"]; ?>/images/home.gif" title="" alt="" />
				</label>
			</td>
		  </tr>
		</table>

		<hr style="width:80%;" />

	<?php
		$check = ( $trunc ) ? "checked=\"checked\"" : "" ;

		print("
			<table class=\"width100\">
			  <tr style=\"background-color:#C0C0C0;\">
	                <td class=\"align-center\" style=\"white-space: nowrap;\">
             		<label for=\"trunc\"><input type=\"checkbox\" id=\"trunc\" name=\"trunc\" value=\"1\" onclick=\"document.forms.formulaire.submit();\" $check /></label>
				<img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"". $msg->read($ADMIN_SELECTALL) ."\" alt=\"". $msg->read($ADMIN_SELECTALL) ."\" />
	                </td>

	                <td style=\"width:25%;\">
				". $msg->read($ADMIN_TABLE) ."
	                </td>

	                <td style=\"width:59%;\">
				". $msg->read($ADMIN_LASTACTION) ."
	                </td>

	                <td class=\"align-center\" style=\"width:15%;\">
				". $msg->read($ADMIN_DATE) ."
	                </td>
			  </tr>
			");

		// recherche des tables
		$query  = "select _IDitem ";
		$query .= "from reset ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$count  = 0;
		while ( $row ) {
			$bgcol  = ( $count++ % 2 ) ? "item" : "menu" ;

			$check  = ( $trunc )
				? "checked=\"checked\""
				: "" ;

			// intitulé de la table
			$query  = "select _ident from config_submenu ";
			$query .= "where _link like 'item=$row[0]%' AND _lang = '".$_SESSION["lang"]."' ";
			$query .= "order by _link limit 1";

			$return = mysqli_query($mysql_link, $query);
			$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			// dernières actions sur les tables
			$query  = "select _ID, _IP, _date, _numrows ";
			$query .= "from reset_log ";
			$query .= "where _IDitem = '$row[0]' ";
			$query .= "limit 1";

			$return = mysqli_query($mysql_link, $query);
			$sql    = ( $return ) ? mysqli_fetch_row($return) : 0 ;

			$msg->isPlural = (bool) ($sql[3] > 1);

			$text   = ( $sql )
				? $msg->read($ADMIN_BY, Array(strval($sql[3]), date2longfmt($sql[2]), getUserNameByID($sql[0]), _getHostName($sql[1])))
				: "" ;

			if ( strlen($myrow[0]) ) {
				print("
					<tr class=\"$bgcol\">
						<td class=\"align-center\"><label for=\"cb_$count\"><input type=\"checkbox\" id=\"cb_$count\" name=\"cb[]\" value=\"$row[0]\" $check /></label></td>
						<td>$myrow[0]</td>
						<td>$text</td>
						<td class=\"align-center\">
							<label for=\"date_$count\">
							<select id=\"date_$count\" name=\"date[]\">");

						print("<option value=\"0\">". $msg->read($ADMIN_ALL) ."</option>");

						for ($i = 1; $i < 6; $i++) {
							$msg->isPlural = (bool) ($i > 1);

							print("<option value=\"$i\">". $msg->read($ADMIN_YEARS, strval($i)) ."</option>");
							}

				print("
							</select>
							</label>
						</td>
					</tr>");
				}

			$row = mysqli_fetch_row($result);
			}

		print("</table>");

		print("<hr />");

		print("
	            <table class=\"width100\">
      	        <tr>
	                <td style=\"width:10%;\" class=\"valign-middle\">
				<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($ADMIN_INPUTOK)."\" />
	                </td>
	                <td class=\"valign-middle\">". $msg->read($ADMIN_TRUNCATE) ."</td>
	              </tr>
	            </table>
	            ");
	?>
	</form>

</div>
