<?php

$valid  = ( @$_POST["valid"] )			// bouton validation
	? $_POST["valid"]
	: @$_GET["valid"] ;
$validnew   = ( @$_POST["validnew"] )	// bouton validation & nouveau
	? $_POST["validnew"]
	: @$_GET["validnew"] ;
$nomGrp   = ( @$_POST["nomGrp"] )		// Nom du groupe
	? addslashes($_POST["nomGrp"])
	: @addslashes($_GET["nomGrp"]) ;
$IDgrp   = ( @$_POST["IDgrp"] )			// ID du groupe
	? (int) $_POST["IDgrp"]
	: (int) @$_GET["IDgrp"] ;
$UserTags   = ( @$_POST['UserTags'] )	// User du groupe
	? $_POST["UserTags"]
	: @$_GET["UserTags"] ;
$IDcentre = ( @$_POST["IDcentre"] )		// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]);

if ( ($valid OR $validnew) AND ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4 || $IDuser == $_SESSION["CnxID"]) )
{
	if(!$IDgrp) // Si pas d'identifiant groupe alors nouveau groupe
	{
		// CrÃ©ation du nouveau groupe
		$Query  = "INSERT INTO groupe_nom ";
		$Query .= (getParam("GROUPE_IDCENTRE") == 1) ? "VALUES ('', '$IDcentre', '$nomGrp', '', 'O', '') " : "VALUES ('', '0', '$nomGrp', '', 'O', '') ";		
		mysqli_query($mysql_link, $Query);
		$id_nom_groupe = mysqli_insert_id($mysql_link);
		
		// Mise Ã  jours des personnes du nouveau groupe	
		foreach($UserTags as $val)
		{
			$Query  = "INSERT INTO groupe ";
			$Query .= "VALUES ('$val', '0', '0', '$id_nom_groupe') ";
			mysqli_query($mysql_link, $Query);
		}
	}
	else // Sinon on modifie le groupe existant
	{
		// On modifie son nom Ã©ventuellement
		$Query  = "UPDATE groupe_nom ";
		$Query .= "SET _nom = '$nomGrp' ";
		$Query .= "WHERE _IDgrp = $IDgrp ";
		mysqli_query($mysql_link, $Query);
		
		// Supprime les lignes existantes
		$Query  = "DELETE FROM groupe ";
		$Query .= "WHERE _IDgrp = $IDgrp ";
		mysqli_query($mysql_link, $Query);

		// Mise Ã  jours des personnes du groupe	
		foreach($UserTags as $val)
		{
			$Query  = "INSERT INTO groupe ";
			$Query .= "VALUES ('$val', '0', '0', '$IDgrp') ";
			mysqli_query($mysql_link, $Query);
		}
	}
}
?>

<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/tag-it.pmt.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery.ui.autocomplete.html.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery.desoform.js"></script>

<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($STUDENT_GROUP)); ?>
	</div>
</div>

<div class="maincontent">
	
	<?php
	if ( ($valid OR $validnew) AND ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4 || $IDuser == $_SESSION["CnxID"]) )
	{
		if($valid) // Si simple validation > retour liste groupe
		{
			print("
				<div class=\"alert alert-success\">
					<center>".$msg->read($STUDENT_INPUTOKTEXT)."</center>
				</div>
				<META http-equiv=\"refresh\" content=\"1; URL=index.php?item=38&cmde=listgroupe&IDcentre=".$IDcentre."\">");
			exit;
		}
		else if($validnew) // Si validation pour nouveau > retour formulaire
		{
			print("
				<div class=\"alert alert-success\">
					<center>".$msg->read($STUDENT_INPUTOKTEXT)."</center>
				</div>
				<META http-equiv=\"refresh\" content=\"1; URL=index.php?item=38&cmde=groupe&IDcentre=".$IDcentre."\">");
			exit;
		}
	}
	else
	{
		?>
		
		<form id="formulaire" action="index.php" method="post">
			<p class="hidden"><input type="hidden" name="item" value="<?php echo $item; ?>" /></p>
			<p class="hidden"><input type="hidden" name="cmde" value="<?php echo $cmde; ?>" /></p>
			<p class="hidden"><input type="hidden" name="IDgrp" value="<?php echo $IDgrp; ?>" /></p>
			<?php if($IDgrp){ ?>
			<p class="hidden"><input type="hidden" name="IDcentre" value="<?php echo $IDcentre; ?>" /></p>
			<?php } ?>
			
			<a class="btn" style="color: black; margin-bottom: 10px" href="index.php?item=38&cmde=listgroupe&IDcentre=<?php echo $IDcentre; ?>">
				<i class="fa fa-chevron-left"></i> <?php print($msg->read($STUDENT_RETURNLIST)); ?>
			</a>
			<br /><br />
			
			<?php if(getParam("GROUPE_IDCENTRE") == 1){ ?>
			<label for="IDcentre">
				<strong><?php print($msg->read($STUDENT_CHOOSECENTER)); ?></strong>
			</label>
			<br />
			<?php
			$disabled = ($IDgrp) ? true : false;
			DisplayListCentre($IDcentre, $disabled);
			?>
			<br />
			<?php } ?>
			
			<!-- Nom du groupe -->
			<label for="nomGrp"><strong><?php print($msg->read($STUDENT_GRPNAME)); ?></strong></label><br />
			<input id="nomGrp" name="nomGrp" class="input-xlarge" type="text" value="<?php echo getNomByIDgrp($IDgrp); ?>">
			<br />
			
			<!-- Personnes du groupe -->
			<script type="text/javascript">
				jQuery(document).ready(function() {							
					jQuery("#UserTags").tagit({
						autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=11&IDcentre=<?php echo $IDcentre; ?>", html: 'html'},
						allowDuplicates: false,
						singleField: false,
						fieldName: "UserTags[]"
					});
					
					<?php
					// RÃ©cupÃ¨re les personne du groupe
					foreach(getUsersByIDgrp($IDgrp) as $key => $val)
					{
						?>
						jQuery("#UserTags").tagit("createTag", "<?php echo $val."<span class='hidden'>".$key."</span>," ?>");
						<?php
					}
					?>
				});
			</script>
			<style>
			.ui-autocomplete { max-height: 300px; overflow-y: scroll; overflow-x: hidden;}
			</style>
			
			<label for="NomGroupe"><strong><?php print($msg->read($STUDENT_LISTSTUDENT)); ?></strong></label><br />
			<ul id="UserTags" name="UserTags">
			</ul>
			
			<br />
			
			<!-- Bouton de validation et retour -->
			<input class="btn btn-success" type="submit" name="valid" value="<?php print($msg->read($STUDENT_INPUTOK)); ?>">
			<input class="btn btn-success" type="submit" name="validnew" value="<?php print($msg->read($STUDENT_INPUTOKNEW)); ?>">
		</form>
	<?php
	}
	?>
</div>
