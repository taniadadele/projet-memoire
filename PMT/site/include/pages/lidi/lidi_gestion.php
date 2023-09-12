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
 *		module   : email_lidi.php
 *		projet   : gestion des annuaires
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/01/07
 *    modif    :
 *					 29/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7 et maj du thème
 */

	$IDcentre = ( strlen(@$_POST["IDcentre"]) )		// Identifiant du centre
		? (int) $_POST["IDcentre"]
		: (int) $_SESSION["CnxCentre"] ;


	if (isset($_POST['IDlidi']) && $_POST['IDlidi'] != 0) $IDlidi = $_POST['IDlidi'];
	elseif (isset($_GET['IDlidi']) && $_GET['IDlidi'] != 0) $IDlidi = $_GET['IDlidi'];

	$list     = addslashes(trim(@$_POST["list"]));     	// nom de la liste de diffusion

	// POST
	$temp = array('public', 'IDdst', 'cb');
	foreach ($temp as $value) if (isset($_POST[$value])) $$value = $_POST[$value];


?>


<?php
	// l'utilisateur a validé
	if (isset($_POST) && isset($_POST['list']))
	{
		if (isset($public) && $public == 'on') $public = 'O';
		if (isset($IDlidi)) {
			$query  = "update postit_lidi ";
			$query .= "set _nom = '$list', _public = '$public' ";
			$query .= "where _IDlidi = '$IDlidi' ";
			$query .= "limit 1";
		}
		else $query  = "insert into postit_lidi values(NULL, '".$_SESSION["CnxID"]."', '$list', 'N', '$public', 'N') ";


		if (mysqli_query($mysql_link, $query))
		{
			if (!isset($IDlidi)) $IDlidi = mysqli_insert_id($mysql_link);

			// Suppresion anciennes données
			mysqli_query($mysql_link, "delete from postit_address where _IDlidi = '$IDlidi' ");

			// Ajout des nouvelles
			if(isset($_POST['UserTags'])) foreach($_POST['UserTags'] as $val) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '$val', 'user')");
			if(isset($_POST['ClassTags'])) foreach($_POST['ClassTags'] as $val) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '$val', 'class')");
			if(isset($_POST['GroupTags'])) foreach($_POST['GroupTags'] as $val) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '$val', 'group')");
			if(isset($_POST['ClassProfTags'])) foreach($_POST['ClassProfTags'] as $val) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '$val', 'classprof')");
			if(isset($_POST['MatProfTags'])) foreach($_POST['MatProfTags'] as $val) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '$val', 'matprof')");

			if(isset($_POST['ck_admin'])) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '0', 'ck_admin')");
			if(isset($_POST['ck_ens'])) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '0', 'ck_ens')");
			if(isset($_POST['ck_eleve1'])) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '0', 'ck_eleve1')");
			if(isset($_POST['ck_eleve2'])) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '0', 'ck_eleve2')");
			if(isset($_POST['ck_fr'])) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '0', 'ck_fr')");
			if(isset($_POST['ck_de'])) mysqli_query($mysql_link, "insert into postit_address values('$IDlidi', '0', 'ck_de')");
		}
	}


	if (isset($IDlidi)) {
		// initialisation
		$result = mysqli_query($mysql_link, "select _nom, _public, _ID from postit_lidi where _IDlidi = '$IDlidi' limit 1");
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
		$list_userID = $row[2];
		$list   = $row[0];
		$public = ( $row[1] ) ? $row[1] : "N" ;


		$temp = array(
			'list_user' => "SELECT p._ID, u._name, u._fname FROM postit_address p, user_id u WHERE p._ID = u._ID AND p._IDlidi = $IDlidi AND p._type = 'user' ",
			'list_class' => "SELECT p._ID, c._IDclass, c._ident FROM postit_address p, campus_classe c WHERE p._ID = c._IDclass AND p._IDlidi = $IDlidi AND p._type = 'class' ",
			'list_group' => "SELECT _ID FROM postit_address WHERE _IDlidi = $IDlidi AND _type = 'group' ",
			'list_classprof' => "SELECT p._ID, c._IDclass, c._ident FROM postit_address p, campus_classe c WHERE p._ID = c._IDclass AND p._IDlidi = $IDlidi AND p._type = 'classprof' ",
			'list_matprof' => "SELECT p._ID, c._IDmat, c._titre FROM postit_address p, campus_data c WHERE p._ID = c._IDmat AND c._lang = '".$_SESSION["lang"]."' AND p._IDlidi = $IDlidi AND p._type = 'matprof' "
		);
		foreach ($temp as $var_name => $query) {
			$result = mysqli_query($mysql_link, $query);
			$$var_name = '';
			while ($row = mysqli_fetch_row($result)) {
				switch ($var_name) {
					case 'list_user': $$var_name .= $row[1].' '.$row[2].'<span class=\'hidden\'>'.$row[0].'</span>,'; break;
					case 'list_group': $$var_name .= getGroupNameByID($row[0])."<span class='hidden'>".$row[0]."</span>;"; break;
					default: $$var_name .= $row[2].'<span class=\'hidden\'>'.$row[1].'</span>,'; break;
				}
			}
		}



		// Sélection ck
		$temp = array('ck_admin', 'ck_ens', 'ck_eleve1', 'ck_eleve2', 'ck_fr', 'ck_de');
		foreach ($temp as $value) {
			$query  = "SELECT COUNT(p._ID) FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = '".$value."' ";
			$result = mysqli_query($mysql_link, $query);
			if (mysqli_num_rows($result)) {
				$$value = mysqli_fetch_row($result)[0];
			}
		}
	}

	$editable = false;
	if ((isset($list_userID) && $list_userID == $_SESSION['CnxID']) || !isset($list_userID)) $editable = true;
?>



<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<div style="float: left;">
	  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($LIDI_LIDI); ?></h1>
		<a href="index.php?item=<?php echo $item; ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint"><i class="fas fa-chevron-left"></i>&nbsp;<?php echo $msg->read($LIDI_RETURN); ?></a>
	</div>
</div>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Titre</h6>
  </div>
  <div class="card-body">



		<form id="formulaire" action="index.php" method="post">
			<?php foreach (array('item', 'cmde', 'IDlidi') as $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">'; ?>

			<!-- Nom de la liste -->
			<div class="form-group">
				<label for="list"><?php echo $msg->read($LIDI_NAMELIST); ?></label>
				<input type="text" class="form-control" name="list" id="list" size="40" value="<?php echo $list; ?>" <?php if(!$editable) echo 'disabled'; ?> required>
			</div>

			<!-- Utilisateurs -->
			<div class="form-group">
				<label for="UserTags"><?php echo $msg->read($LIDI_USER); ?></label>
				<ul id="UserTags" name="UserTags">
				</ul>
			</div>

			<!-- Classes -->
			<div class="form-group">
				<label for="ClassTags"><?php echo $msg->read($LIDI_CLASS); ?></label>
				<ul id="ClassTags" name="ClassTags">
				</ul>
			</div>

			<!-- Groupes -->
			<div class="form-group">
				<label for="GroupTags"><?php echo $msg->read($LIDI_GROUP); ?></label>
				<ul id="GroupTags" name="GroupTags">
				</ul>
			</div>

			<!-- Profs d'une classe -->
			<div class="form-group">
				<label for="ClassProfTags"><?php echo $msg->read($LIDI_CLASSPROF); ?></label>
				<ul id="ClassProfTags" name="ClassProfTags">
				</ul>
			</div>

			<!-- Profs d'une matière -->
			<div class="form-group">
				<label for="MatProfTags"><?php echo $msg->read($LIDI_MATPROF); ?></label>
				<ul id="MatProfTags" name="MatProfTags">
				</ul>
			</div>

			<!-- Administrateurs -->
			<div class="form-group form-check">
				<input type="checkbox" class="form-check-input" name="ck_admin" id="ck_admin" <?php if (isset($ck_admin) && $ck_admin) echo 'checked'; ?> <?php if (!$editable) echo 'disabled'; ?>>
				<label class="form-check-label" for="ck_admin"><?php echo $msg->read($LIDI_ADMIN); ?></label>
			</div>

			<!-- Enseignants -->
			<div class="form-group form-check">
				<input type="checkbox" class="form-check-input" name="ck_ens" id="ck_ens" <?php if (isset($ck_ens) && $ck_ens) echo 'checked'; ?> <?php if (!$editable) echo 'disabled'; ?>>
				<label class="form-check-label" for="ck_ens"><?php echo $msg->read($LIDI_ENS); ?></label>
			</div>

			<!-- Liste publique -->
			<div class="form-group form-check">
				<input type="checkbox" class="form-check-input" name="public" id="public" <?php if (isset($public) && $public == 'O') echo 'checked'; ?> <?php if (!$editable) echo 'disabled'; ?>>
				<label class="form-check-label" for="public"><?php echo $msg->read($LIDI_PUBLICLIST).' '.$msg->read($LIDI_PUBLICUSE); ?></label>
			</div>

			<!-- Validation -->
			<button type="submit" class="btn btn-success"><?php echo $msg->read($LIDI_INPUTOK); ?></button>
			<!-- Retour -->
			<a href="index.php?item=<?php echo $item; ?>" class="btn btn-danger"><?php echo $msg->read($LIDI_INPUTCANCEL); ?></a>
		</form>



		<?php
		// Tableau listant les destinataires
		if($_SESSION["CnxGrp"] > 1 && isset($IDlidi) && $IDlidi)	// Si on fait partie de l'administration
		{
			// Sélection ck liste diffusion
			$temp = array(
				'ckl_admin' => "SELECT COUNT(p._ID) FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'ck_admin' ",
				'ckl_ens' => "SELECT COUNT(p._ID) FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'ck_ens' ",
				'ckl_eleve1' => "SELECT COUNT(p._ID) FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'ck_eleve1' ",
				'ckl_eleve2' => "SELECT COUNT(p._ID) FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'ck_eleve2' ",
				'ckl_fr' => "SELECT COUNT(p._ID) FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'ck_fr' ",
				'ckl_de' =>"SELECT COUNT(p._ID) FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'ck_de' "
			);

			foreach ($temp as $var_name => $query) {
				$result = mysqli_query($mysql_link, $query);
				$$var_name = mysqli_fetch_row($result)[0];
			}

			// Utilisateurs
			$query  = "SELECT p._ID, u._name, u._fname FROM postit_address p, user_id u WHERE p._ID = u._ID AND p._IDlidi = $IDlidi AND p._type = 'user' ";
			$result = mysqli_query($mysql_link, $query);
			while ($row = mysqli_fetch_row($result)) $tab_email[] = $row[0];

			// *** CK liste ***
			// Vérif si pas autre chose
			$query  = "SELECT COUNT(_IDlidi) FROM postit_address WHERE _IDlidi = $IDlidi AND (_type LIKE 'user' OR _type LIKE 'class' OR _type LIKE 'group' OR _type LIKE 'classprof' OR _type LIKE 'matprof') ";
			$resultl = mysqli_query($mysql_link, $query);
			$num_rows = mysqli_fetch_row($resultl)[0];
			if(!$num_rows)
			{
				if($ckl_admin || $ckl_ens)
				{
					$query  = "select _ID, _name, _fname from user_id ";
					$query .= "where 1 = 1 ";
					$query .= ($ckl_admin && !$ckl_ens) ? "AND _IDgrp = 4 " : "";
					$query .= ($ckl_ens && !$ckl_admin) ? "AND _IDgrp = 2 " : "";
					$query .= ($ckl_ens && $ckl_admin) ? "AND (_IDgrp = 2 OR _IDgrp = 4) " : "";
					$query .= "order by _name, _fname";
					$result = mysqli_query($mysql_link, $query);
					while ($row = mysqli_fetch_row($result)) $tab_email[] = $row[0];
				}
			}

			// Classes
			$query  = "SELECT p._ID, c._IDclass, c._ident FROM postit_address p, campus_classe c WHERE p._ID = c._IDclass AND p._IDlidi = $IDlidi AND p._type = 'class' ";
			$result = mysqli_query($mysql_link, $query);
			while ($row = mysqli_fetch_row($result))
			{
				$query  = "select _ID, _name, _fname from user_id where _IDclass = '$row[1]' AND _visible = 'O' AND _IDgrp = 1 order by _name, _fname ";
				$result_1 = mysqli_query($mysql_link, $query);
				while ($row_1 = mysqli_fetch_row($result_1)) $tab_email[] = $row_1[0];
			}

			// Groupes
			$query  = "SELECT _ID FROM postit_address WHERE _IDlidi = $IDlidi AND _type = 'group' ";
			$result = mysqli_query($mysql_link, $query);
			while ($row = mysqli_fetch_row($result))
			{
				$queryuser  = "SELECT u._ID, u._name, u._fname FROM user_id u, groupe g WHERE u._ID = g._IDeleve AND g._IDgrp = $row[0] order by u._name ";
				$resultuser = mysqli_query($mysql_link, $queryuser);
				while ($rowuser = mysqli_fetch_row($resultuser)) $tab_email[] = $rowuser[0];
			}

			// Profs d'une classe
			$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'classprof' ";
			$result = mysqli_query($mysql_link, $query);
			date_default_timezone_set('Europe/Paris');
			setlocale(LC_TIME, "fr_FR");
			$startx   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
			$endx     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
			$startday = intval(date("N", $startx))-1;
			$endday = intval(date("N", $endx))-1;

			while ($row = mysqli_fetch_row($result))
			{
				$query  = "select distinctrow user_id._ID ";
				$query .= "from edt_data, campus_classe, user_id ";
				$query .= "WHERE (edt_data._ID = user_id._ID OR edt_data._IDrmpl = user_id._ID) ";
				$query .= "AND edt_data._IDclass LIKE '%;$row[0];%' ";
				$query .= "AND edt_data._etat = 1 ";
				$query .= "AND ((edt_data._jour >= $startday AND edt_data._nosemaine >= ".date("W", $startx)." AND edt_data._annee = ".date("Y", $startx).") ";
				$query .= "OR (edt_data._jour <= $endday AND edt_data._nosemaine <= ".date("W", $endx)." AND edt_data._annee = ".date("Y", $endx).")) ";
				$result_1 = mysqli_query($mysql_link, $query);
				while ($row_1 = mysqli_fetch_row($result_1)) $tab_email[] = $row_1[0];
			}

			// Profs d'une matière
			$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $IDlidi AND p._type = 'matprof' ";
			$result = mysqli_query($mysql_link, $query);

			date_default_timezone_set('Europe/Paris');
			setlocale(LC_TIME, "fr_FR");

			$startx   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
			$endx     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
			$startday = intval(date("N", $startx))-1;
			$endday = intval(date("N", $endx))-1;

			while ($row = mysqli_fetch_row($result))
			{
				$query  = "select distinctrow user_id._ID ";
				$query .= "from edt_data, campus_classe, user_id ";
				$query .= "WHERE (edt_data._ID = user_id._ID OR edt_data._IDrmpl = user_id._ID) ";
				$query .= "AND edt_data._IDmat = $IDlidi ";
				$query .= "AND edt_data._etat = 1 ";
				$query .= "AND ((edt_data._jour >= $startday AND edt_data._nosemaine >= ".date("W", $startx)." AND edt_data._annee = ".date("Y", $startx).") ";
				$query .= "OR (edt_data._jour <= $endday AND edt_data._nosemaine <= ".date("W", $endx)." AND edt_data._annee = ".date("Y", $endx).")) ";
				$result_1 = mysqli_query($mysql_link, $query);
				while ($row_1 = mysqli_fetch_row($result_1)) $tab_email[] = $row_1[0];
			}

			$tab_email = array_unique($tab_email); // Dédoublonnage valeur
			?>
			<h5 class="mt-3"><?php echo $msg->read($LIDI_DEST); ?> :</h5>
			<table class="table table-striped mt-3">
				<?php
					foreach($tab_email as $val)
					{
						$queryuser  = "select _email, _name, _fname from user_id WHERE _ID = $val ";
						$resultuser = mysqli_query($mysql_link, $queryuser);
						while ($rowuser = mysqli_fetch_row($resultuser))
						{
							if($rowuser[0] != '') echo '<tr><th>'.$rowuser[1].' '.$rowuser[2].'</th><td>'.$rowuser[0].'</td></tr>';
						}
					}
				?>
			</table>
		<?php } ?>
  </div>
</div>




<script type="text/javascript">
	// Utilisateurs
	$(document).ready(function() {
		$("#UserTags").tagit({
			autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=5", html: 'html'},
			allowDuplicates: false,
			<?php if (!$editable) echo 'readOnly: true,';  ?>
			singleField: false,
			fieldName: "UserTags[]"
		});
		<?php if (isset($list_user)) foreach (explode(",", $list_user) as $val) { ?>
			$("#UserTags").tagit("createTag", "<?php echo $val; ?>");
		<?php } ?>

		// Classes
		$("#ClassTags").tagit({
			autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=6", html: 'html'},
			allowDuplicates: false,
			<?php if (!$editable) echo 'readOnly: true,';  ?>
			singleField: false,
			fieldName: "ClassTags[]"
		});
		<?php if (isset($list_class)) foreach (explode(",", $list_class) as $val) { ?>
			$("#ClassTags").tagit("createTag", "<?php echo $val; ?>");
		<?php } ?>

		// Groupes
		$("#GroupTags").tagit({
			autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=7", html: 'html'},
			allowDuplicates: false,
			<?php if (!$editable) echo 'readOnly: true,';  ?>
			singleField: false,
			fieldName: "GroupTags[]"
		});
		<?php if (isset($list_group)) foreach (explode(";", $list_group) as $val) { ?>
			$("#GroupTags").tagit("createTag", "<?php echo $val; ?>");
		<?php } ?>

		// Profs d'une classe
		$("#ClassProfTags").tagit({
			autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=9", html: 'html'},
			allowDuplicates: false,
			<?php if (!$editable) echo 'readOnly: true,';  ?>
			singleField: false,
			fieldName: "ClassProfTags[]"
		});
		<?php if (isset($list_classprof)) foreach (explode(",", $list_classprof) as $val) { ?>
			$("#ClassProfTags").tagit("createTag", "<?php echo $val; ?>");
		<?php } ?>

		// Profs d'une matière
		$("#MatProfTags").tagit({
			autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=10", html: 'html'},
			allowDuplicates: false,
			<?php if (!$editable) echo 'readOnly: true,';  ?>
			singleField: false,
			fieldName: "MatProfTags[]"
		});
		<?php if (isset($list_matprof)) foreach (explode(",", $list_matprof) as $val) { ?>
			$("#MatProfTags").tagit("createTag", "<?php echo $val; ?>");
		<?php } ?>
	});
</script>
