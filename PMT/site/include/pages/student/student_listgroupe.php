<?php

$IDcentre = ( @$_POST["IDcentre"] )			// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
$IDgrp   = ( @$_POST["IDgrp"] )				// ID du groupe
	? (int) $_POST["IDgrp"]
	: (int) @$_GET["IDgrp"] ;
$item   = (int) @$_GET["item"];
$cmde   = @$_GET["cmde"];
$action   = @$_GET["action"];
$visible   = @$_GET["visible"];
$etat   = ( @$_GET["etat"] )				// Etat du groupe visible / invisible
	? @$_GET["etat"]
	: "visible" ;
	
// Suppression d'un groupe
if ( $action == "delete" AND ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4) )
{
	// Suppression des personnes du groupe
	$Query  = "DELETE FROM groupe ";
	$Query .= "WHERE _IDgrp = $IDgrp ";
	mysqli_query($mysql_link, $Query);
	
	// Suppression du groupe
	$Query  = "DELETE FROM groupe_nom ";
	$Query .= "WHERE _IDgrp = $IDgrp ";
	mysqli_query($mysql_link, $Query);
}

// Rendre visible un groupe
if ( $action == "visible" AND ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4) )
{
	// Modification etat visible groupe
	$Query  = "UPDATE groupe_nom ";
	$Query .= "SET _visible = 'O' ";
	$Query .= "WHERE _IDgrp = $IDgrp ";
	mysqli_query($mysql_link, $Query);
}

// Rendre invisible un groupe
if ( $action == "invisible" AND ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4) )
{
	// Modification etat invisible groupe
	$Query  = "UPDATE groupe_nom ";
	$Query .= "SET _visible = 'N' ";
	$Query .= "WHERE _IDgrp = $IDgrp ";
	mysqli_query($mysql_link, $Query);
}
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($STUDENT_GROUP)); ?>
	</div>
</div>

<div class="maincontent">

	<form id="formulaire" action="index.php" method="get">
	<?php
		print("
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"     value=\"$item\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"fiche\"     value=\"$fiche\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"     value=\"$cmde\" /></p>");
	?>

		<table class="width100">
		  <tr>
			<td style="width: 100px;">
				<a class="btn" href="index.php?item=38&cmde=groupe&IDcentre=<?php echo $IDcentre; ?>" style="color: black">
					<i class="fa fa-plus-square"></i> <?php echo $msg->read($STUDENT_INPUTNEW); ?>
				</a>
			</td>
			<?php if(getParam("GROUPE_IDCENTRE") == 1){ ?>
			<td class="align-right">
				<label for="IDcentre">
					<strong><?php print($msg->read($STUDENT_CHOOSECENTER)); ?></strong>
				</label>
				<?php DisplayListCentre($IDcentre); ?>
			</td>
			<?php } ?>
			<td class="align-right">
				<label for="IDcentre">
					<strong><?php print($msg->read($STUDENT_ETATGRP)); ?></strong>
				</label>
				<select name="etat" id="etat" onchange="document.forms.formulaire.submit()">
					<option value="all" <?php ($etat == "all") ? print 'selected="selected"' : ""; ?>><?php print($msg->read($STUDENT_ETATGRP_ALL)); ?></option>
					<option value="visible" <?php ($etat == "visible") ? print 'selected="selected"' : ""; ?>><?php print($msg->read($STUDENT_ETATGRP_V)); ?></option>
					<option value="invisible" <?php ($etat == "invisible") ? print 'selected="selected"' : ""; ?>><?php print($msg->read($STUDENT_ETATGRP_I)); ?></option>
				</select>
			</td>
		  </tr>
		</table>

		<hr style="width:80%; margin-top: 5px; margin-bottom: 5px; "/>

		<table class="table table-bordered" id="selection">
			<tr>
				<th class="align-center btn-primary" style="width: 40px; text-align: center;">
					
				</th>
				<th class="align-center btn-primary">
					<?php print($msg->read($STUDENT_GROUP)); ?>
				</th>
				<th class="align-center btn-primary" style="width: 84px">
				
				</th>
			</tr>
			<?php
			// Recherche des groupes
			$query  = "SELECT _IDgrp, _nom, _visible ";
			$query .= "FROM groupe_nom ";
			$query .= (getParam("GROUPE_IDCENTRE") == 1) ? "WHERE _IDcentre = $IDcentre " : "";		
			$query .= ($etat == "visible") ? "AND _visible = 'O'" : "";
			$query .= ($etat == "invisible") ? "AND _visible = 'N'" : "";
			$query .= "ORDER BY _IDgrp desc ";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			if($row)
			{
				while ( $row )
				{
					?>
					<tr>
						<td>
							<a class="btn" style="margin-bottom: 5px;" href="index.php?item=38&cmde=groupe&IDgrp=<?php echo $row[0]; ?>&IDcentre=<?php echo $IDcentre; ?>">
								<?php
								if($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4 || $row[3] == $_SESSION["CnxID"])
								{
									echo "<i class=\"icon-pencil\"></i>";
								}
								else
								{
									echo "<i class=\"icon-eye-open\"></i>";
								}
								?>
							</a>
						</td>
						<td style="vertical-align: middle">
							<?php							
							// Affiche nom du groupe
							echo "<span class=\"label label-inverse\"><i class=\"icon-tag icon-white\"></i> $row[1]</span> ";
							
							// Affiche personnes du groupe
							foreach(getUsersByIDgrp($row[0]) as $val)
							{
								echo "<span class=\"label\"><i class=\"icon-user icon-white\"></i> $val</span> ";
							}
							?>
						</td>
						<td>
							<?php
							if($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4 || $IDuser == $_SESSION["CnxID"])
							{
								$visible = ($row[2] == "O") ? "invisible" : "visible";
								$icon_visible = ($row[2] == "O") ? "icon-ok" : "icon-remove";
								?>
								<a class="btn" href="index.php?item=38&fiche=&cmde=listgroupe&IDcentre=<?php echo $IDcentre; ?>&IDgrp=<?php echo $row[0]; ?>&action=<?php echo $visible; ?>">
									<i class="<?php echo $icon_visible; ?>"></i>
								</a>
								<a class="btn" href="index.php?item=38&fiche=&cmde=listgroupe&IDcentre=<?php echo $IDcentre; ?>&IDgrp=<?php echo $row[0]; ?>&action=delete" onclick="return confirmLink(this, '<?php print($msg->read($STUDENT_DELETEGRP)); ?>');">
									<i class="icon-trash"></i>
								</a>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
					$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
			}				
			
			?>
		</table>
	</form>	
</div>
