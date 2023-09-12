<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : log_ip.php
 *		projet   : la page de gestion des IP en liste brûlée
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 26/05/07
 *		modif    : 
 */


$burn   = (int) @$_POST["burn"];		// sélection globale
$submit = @$_POST["valid_x"];		// bouton validation
//---------------------------------------------------------------------------
?>


<?php
	// vérification des autorisations
	admSessionAccess();

	// modification des utilisateurs
	if ( $submit ) {
		$cb = @$_POST["cb"];

		if ( mysqli_query($mysql_link, "update ip_denied set _visible = 'O'") )
			for ($i = 0; $i < count($cb); $i++) {
				$Query  = "UPDATE ip_denied ";
				$Query .= "SET _visible = 'N' ";
				$Query .= "WHERE _IP = '$cb[$i]' ";
				$Query .= "LIMIT 1";

				mysqli_query($mysql_link, $Query);
				}
		}
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($LOG_MANAGER)); ?>
	</div>
</div>

<div class="maincontent">

	<p style="margin-top:0px;"><?php print($msg->read($LOG_DISCLAIMER)); ?></p>

	<form id="formulaire" action="index.php" method="post">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"$cmde\" /></p>
			");

		$check = ( $burn ) ? "checked=\"checked\"" : "" ;

		print("
			<table class=\"width100\">
			  <tr>
	                <td class=\"align-center\" style=\"width:10%;background-color:#C0C0C0;\">
             		<label for=\"burn\"><input type=\"checkbox\" id=\"burn\" name=\"burn\" value=\"1\" onclick=\"document.forms.formulaire.submit();\" $check /></label>
				<img src=\"".$_SESSION["ROOTDIR"]."/images/logout.png\" title=\"". $msg->read($LOG_BURNOUT) ."\" alt=\"". $msg->read($LOG_BURNOUT) ."\" />
	                </td>

	                <td style=\"width:50%;background-color:#C0C0C0;\">
				<strong>IP</strong>
	                </td>

	                <td class=\"align-center\" style=\"width:40%;background-color:#C0C0C0;\">
				". $msg->read($LOG_DATE) ."
	                </td>
			  </tr>
			");

		// affichage des IP
		$query  = "select _IP, _IPv6, _date, _visible ";
		$query .= "from ip_denied ";
		$query .= "order by _IP desc";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$count  = 0;
		while ( $row ) {
			$bgcol = ( $count++ % 2 ) ? "item" : "menu" ;

			$check = ( $burn OR $row[3] == "N" )
				? "checked=\"checked\""
				: "" ;

			print("
				<tr class=\"$bgcol\">
					<td class=\"align-center\"><label for=\"cb_$count\"><input type=\"checkbox\" id=\"cb_$count\" name=\"cb[]\" value=\"$row[0]\" $check /></label></td>
					<td>".resolveHostName($row[1])."</td>
					<td class=\"align-center\">".date2longfmt($row[2])."</td>
				</tr>
				");

			$row = mysqli_fetch_row($result);
			}

		print("
	            </table>

			<hr />
			");

		print("
	            <table class=\"width100\">
      	        <tr>
	                <td style=\"width:10%;\" class=\"valign-middle\">
				<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($LOG_INPUTOK)."\" />
	                </td>
	                <td class=\"valign-middle\">". $msg->read($LOG_RECORD) ."</td>
	              </tr>
	            </table>
	            ");
	?>
	</form>

</div>