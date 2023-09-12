<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Didier Roy (miraceti@free.fr)
   Copyright (c) 2006 by Nordine Zetoutou (nordine.zetoutou@educagri.fr)

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
 *		module   : campus_class.php
 *		projet   : la page de visualisation des classes
 *
 *		version  : 1.1
 *		auteur   : laporte
 *		creation : 1/04/06
 *		modif    : 08/06/06 - par Didier Roy
 *		           migration PHP5
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *					 		 24/10/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7 et maj du thème
 */

	// On récupère les éléments dans le post puis dans le get
	$post_get = array('IDsel', 'submit');
	foreach ($post_get as $value) {
		if (isset($_POST[$value])) $$value = $_POST[$value];
		elseif (isset($_GET[$value])) $$value = $_GET[$value];
	}

	// On récupère les éléments dans le post
	$post = array('IDcentre', 'idclass', 'idpp', 'idpp_2');
	foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

	// On récupère les éléments dans le get
	$get = array('id');
	foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

	// On récupère les valeurs par défaut
	$default = array(

	);
	foreach ($default as $key => $value) if (!isset($$key) && isset($value)) $$key = $value;

	// On récupère les valeurs par défaut dans la session
	$default_session = array(
		'IDcentre' => 'CnxCentre',
		'IDsel' => 'CAMPUS_CLASSE_IDsel'
	);
	foreach ($default_session as $key => $value) if (!isset($$key) && isset($_SESSION[$value])) $$key = $_SESSION[$value];


	// On stoque les filtres
	if (isset($IDsel)) $_SESSION['CAMPUS_CLASSE_IDsel'] = $IDsel;
?>

<script>
	function openTR(id)
	{
		if ($("#tr_" + id).is(':hidden'))
		{
			$.ajax({
				url: "getInfos.php?IDclass=" + id + "&type=1",
				success : function(data) {
					$("#divtr_" + id).html(data);
					$("#tr_" + id).toggle(400);
					$("#icontr_" + id).attr("class", "fas fa-chevron-down");
				}
			});
		}
		else
		{
			$("#tr_" + id).toggle(400);
			$("#icontr_" + id).attr("class", "fas fa-chevron-right");
		}
		return false;
	}
</script>

<?php
	// modification des utilisateurs
	if ($_SESSION["CnxAdm"] == 255 && isset($submit)) {
		switch ($submit) {
			case "delpp" :
				$db->query("UPDATE `campus_classe` SET _IDpp = NULL WHERE `_IDclass` = ?i LIMIT 1 ", $id);
				break;
			case "delpp_2" :
				$db->query("UPDATE `campus_classe` SET _IDpp_2 = NULL WHERE `_IDclass` = ?i LIMIT 1 ", $id);
				break;

			case "update" :
				foreach ($idclass as $i) {
					if (isset($idpp[$i]) && $idpp[$i]) $db->query("UPDATE campus_classe SET `_IDpp` = ?i WHERE `_IDclass` = ?i ", $idpp[$i], $idclass[$i]);
					if (isset($idpp_2[$i]) && $idpp_2[$i]) $db->query("UPDATE campus_classe SET `_IDpp_2` = ?i WHERE `_IDclass` = ?i ", $idpp_2[$i], $idclass[$i]);
				}
				break;
		}
	}

?>


	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
	  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($CAMPUS_CLASSLIST); ?></h1>
	  <div style="float: right; text-align: right;">
	    <div class="mb-3">
				<form id="formulaire" action="index.php" method="post">
					<input type="hidden" name="item" value="<?php echo $item; ?>">
					<input type="hidden" name="cmde" value="<?php echo $cmde; ?>">
					<div class="form-row">
						<div class="col" style="<?php if (!$showCenter) echo 'display: none;'; ?>">
							<?php echo centerSelect(@$IDcentre, 'formulaire', 'IDcentre', 'IDcentre', false, 'btn d-none d-sm-inline-block btn-sm shadow-sm'); ?>
						</div>
						<?php if ($_SESSION['CnxAdm'] == 255) { ?>
							<div class="col">
								<select name="IDsel" id="IDsel" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
									<option value="0"><?php echo $msg->read($CAMPUS_CHOOSECAT); ?></option>
									<?php
									$groupes = $db->getAll("SELECT _IDgrp, _ident FROM user_group WHERE _visible = 'O' AND _lang = ?s AND _IDcat = 2 ORDER BY _IDgrp asc ", $_SESSION['lang']);
									foreach ($groupes as $groupe) {
										if ($IDsel == $groupe->_IDgrp) $selected = 'selected'; else $selected = '';
										echo '<option value="'.$groupe->_IDgrp.'" '.$selected.'>'.$msg->getTrad($groupe->_ident).'</option>';
									}
									?>
								</select>
							</div>
						<?php } ?>
					</div>
				</form>
	    </div>
	  </div>
	</div>

	<?php
		// On récupère la liste des profs/membres de l'administration
		$user_list = array();
		if (isset($IDsel) && $IDsel) $temp_query = $db->parse("AND _IDgrp = ?i ", $IDsel); else $temp_query = '';
		$users = $db->getAll("SELECT `_ID`, `_name`, `_fname` FROM `user_id` WHERE _adm >= 1 ?p ", $temp_query);
		foreach ($users as $user) $user_list[$user->_ID] = $user->_fname.'&nbsp;'.$user->_name;

		// On récupère les classes par ordre alpha
		$classes = $db->getAll("SELECT _IDclass, _ident, _IDpp, _IDpp_2, _code FROM campus_classe WHERE _visible = 'O' AND _IDcentre = ?i ORDER BY _ident", $IDcentre);
		$nbelem = count($classes);
	?>
	<form id="selection" action="index.php" method="post">
		<div class="card shadow mb-4">
		  <div class="card-header py-3">
		    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, true); ?>Résultats: <?php echo $nbelem; ?></h6>
		  </div>
		  <div class="card-body">
					<input type="hidden" name="item" value="<?php echo $item; ?>">
					<input type="hidden" name="cmde" value="<?php echo $cmde; ?>">
					<input type="hidden" name="IDcentre" value="<?php echo $IDcentre; ?>">
					<input type="hidden" name="IDsel" value="<?php echo $IDsel; ?>">


					<table class="table table-striped">
						<tr>
							<th></th>
							<th><?php echo $msg->getTrad('_CLASS'); ?></th>
							<th><?php echo mb_ucfirst($msg->read($CAMPUS_PRIMTEACHER)); ?>&nbsp;1</th>
							<th><?php echo mb_ucfirst($msg->read($CAMPUS_PRIMTEACHER)); ?>&nbsp;2</th>
							<th><?php echo $msg->read($CAMPUS_NUMBERS); ?></th>
						</tr>

						<?php
						foreach ($classes as $classe) {
							echo '<tr></tr>';
							echo '<tr style="display: none;">';
								echo '<td>';
									echo '<p class="hidden">';
										echo '<input type="hidden" name="idclass['.$classe->_IDclass.']" value="'.$classe->_IDclass.'" />';
									echo '</p>';
								echo '</td>';
							echo '</tr>';

							echo '<tr>';
								$class_link = myurlencode('index.php?item=38&IDcentre='.$IDcentre.'&IDsel='.$classe->_IDclass.'&cmde=show');
								echo '<td><a onclick="openTR('.$classe->_IDclass.')"><i class="fas fa-chevron-right" id="icontr_'.$classe->_IDclass.'"></i></a></td>';
								echo '<td>';
									echo '<a href="'.$class_link.'">'.$classe->_ident.'</a>';
								echo '</td>';
								echo '<td>';
									if ($classe->_IDpp) {
										if (isset($user_mail)) unset($user_mail);
										$delete_link = myurlencode('index.php?item='.$item.'&cmde='.$cmde.'&submit=delpp&id='.$classe->_IDclass.'&IDsel='.(@$IDsel));
										echo $user_list[$classe->_IDpp];
										$user_mail = getUserMailByID($classe->_IDpp);
										if ($user_mail) echo '&nbsp;<a href="mailto:'.$user_mail.'"><i class="fas fa-envelope"></i></a>';
										if ($_SESSION["CnxAdm"] == 255) echo '&nbsp;<a href="'.$delete_link.'"><i class="fas fa-trash"></i></a>';
									} else {
										if ($_SESSION["CnxAdm"] == 255 && isset($IDsel) && $IDsel) {
											echo '<select id="idpp_'.$classe->_IDclass.'" name="idpp['.$classe->_IDclass.']" class="custom-select">';
											echo '<option value="0">'.mb_ucfirst($msg->read($CAMPUS_PRIMTEACHER)).' 1</option>';
											foreach ($user_list as $user_id => $user_name) {
												if ($classe->_IDpp == $user_id) $selected = 'selected'; else $selected = '';
												echo '<option value="'.$user_id.'" '.$selected.'>'.$user_name.'</option>';
											}
											echo '</select>';
										}
									}
								echo '</td>';

								echo '<td>';
									if ($classe->_IDpp_2) {
										if (isset($user_mail)) unset($user_mail);
										$delete_link = myurlencode('index.php?item='.$item.'&cmde='.$cmde.'&submit=delpp_2&id='.$classe->_IDclass.'&IDsel='.(@$IDsel));
										echo $user_list[$classe->_IDpp_2];
										$user_mail = getUserMailByID($classe->_IDpp_2);
										if ($user_mail) echo '&nbsp;<a href="mailto:'.$user_mail.'"><i class="fas fa-envelope"></i></a>';
										if ($_SESSION["CnxAdm"] == 255) echo '&nbsp;<a href="'.$delete_link.'"><i class="fas fa-trash"></i></a>';
									} else {
										if ($_SESSION["CnxAdm"] == 255 && isset($IDsel) && $IDsel) {
											echo '<select id="idpp_2_'.$classe->_IDclass.'" name="idpp_2['.$classe->_IDclass.']" class="custom-select">';
											echo '<option value="0">'.mb_ucfirst($msg->read($CAMPUS_PRIMTEACHER)).' 2</option>';
											foreach ($user_list as $user_id => $user_name) {
												if ($classe->_IDpp_2 == $user_id) $selected = 'selected'; else $selected = '';
												echo '<option value="'.$user_id.'" '.$selected.'>'.$user_name.'</option>';
											}
											echo '</select>';
										}
									}
								echo '</td>';

								// Nombre d'élèves dans la promo
								echo '<td>';
									echo $db->getRow("SELECT COUNT(`_ID`) as `count` FROM `user_id` WHERE `_visible` = 'O' AND `_IDgrp` = '1' AND `_adm` >= 1 AND `_IDclass` = ?i ", $classe->_IDclass)->count;
								echo '</td>';
							echo '</tr>';

							// Liste des prof de la classe dynamique
							echo '<tr id="tr_'.$classe->_IDclass.'" style="display: none;">';
								echo '<td colspan="5">';
									echo '<table class="table mb-0" id="divtr_'.$classe->_IDclass.'"></table>';
								echo '</td>';
							echo '</tr>';
						}
						?>
					</table>
		  </div>
			<?php if (isset($IDsel) && $IDsel) { ?>
			  <div class="card-footer text-muted">
					<input type="hidden" name="submit" value="update">
					<button class="btn btn-success" name="valid"><?php echo $msg->getTrad('_SAVE'); ?></button>
			  </div>
			<?php } ?>
		</div>
	</form>
