<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2007 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : student_visu.php
 *		projet   : la page de visualisation des élèves
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 4/03/06
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


	// On récupère les éléments dans le post puis sinon dans le get
	$post_get = array('IDcentre', 'visu', 'IDsel', 'IDpromo', 'IDres', 'IDmat', 'regime', 'delegue', 'bourse', 'gender', 'currentPage', 'Lcmde');
	foreach ($post_get as $value) {
		if (isset($_POST[$value])) $$value = $_POST[$value];
		elseif (isset($_GET[$value])) $$value = $_GET[$value];
	}

	// On récupère les éléments dans le post
	$post = array();
	foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

	// On récupère les éléments dans le get
	$get = array('recuseralpha', 'IDalpha', 'IDeleve', 'submit');
	foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

	// Valeurs par défaut
	$default = array(
		'visu'         => 'O',
		'recuseralpha' => 'A',
		'IDeleve'      => 1,
		'currentPage'	 => 1
	);
	if (isset($_SESSION['STUDENT_gender'])) $default['gender'] = $_SESSION['STUDENT_gender'];
	if (isset($_SESSION['STUDENT_IDsel'])) $default['IDsel'] = $_SESSION['STUDENT_IDsel'];
	if (isset($_SESSION['STUDENT_IDalpha'])) $default['IDalpha'] = $_SESSION['STUDENT_IDalpha'];
	if (isset($_SESSION['CnxCentre'])) $default['IDcentre'] = $_SESSION['CnxCentre'];
	foreach ($default as $key => $value) if (!isset($$key)) $$key = $value;

	if (isset($IDalpha)) 	$_SESSION["STUDENT_IDalpha"] = $IDalpha;
	if (isset($IDsel)) 		$_SESSION["STUDENT_IDsel"] = $IDsel;
	if (isset($gender)) 	$_SESSION["STUDENT_gender"] = $gender;
	// if (isset($item)) 		$_SESSION["Litem"] = $item;		// Permet de savoir d'où on viens
	// if (isset($cmde)) 		$_SESSION["Lcmde"] = $cmde;		// Permet de savoir d'où on viens
?>

<script>
function openTR(id, IDclass)
{
	if (jQuery("#tr_"+id).is(':hidden'))
	{
		jQuery.ajax({
			url: "getInfos.php?IDclass="+IDclass+"&IDuser="+id+"&type=2",
			success  : function(data) {
				jQuery("#divtr_"+id).html(data);
				jQuery("#tr_"+id).toggle(400);
				jQuery("#icontr_"+id).attr("class", "icon-chevron-up");
			}
		});
	}
	else
	{
		jQuery("#tr_"+id).toggle(400);
		jQuery("#icontr_"+id).attr("class", "icon-chevron-down");
	}
	return false;
}
</script>

<?php
	// initialisation lien
	$href = 'IDcentre='.(@$IDcentre).'&visu='.(@$visu).'&IDsel='.(@$IDsel).'&IDpromo='.(@$IDpromo).'&IDres='.(@$IDres).'&IDmat='.(@$IDmat).'&regime='.(@$regime).'&gender='.(@$gender).'&delegue='.(@$delegue).'&bourse='.(@$bourse);

	// il faut les droits du gestionnaire
	if ($_SESSION["CnxAdm"] == 255 && isset($submit))
		switch ($submit) {
			case "del" :
				// compte utilisateur
					$Query  = "delete from user_id ";
					$Query .= "where _ID = '$IDeleve' ";
					$Query .= "limit 1";

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				break;

			default :
				break;
			}

	switch ($visu) {
		case "O" : $title = $msg->read($STUDENT_LIST1); break;
	 	case "N" : $title = $msg->read($STUDENT_LIST2); break;
	 	default :  $title = $msg->read($STUDENT_LIST3); break;
 	}




	// On récupère les classes
  $classes = array();
  $query = "SELECT _IDclass, _ident FROM campus_classe WHERE _visible = 'O' ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result)) $classes[$row[0]] = $row[1];
?>





<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800"><?php echo $title; ?></h1>

  <div style="float: right; text-align: right;">

    <div class="mb-3 d-print-none">
			<!-- Nouveau -->
			<?php if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) { ?>
				<?php $href_new = myurlencode('index.php?item='.$item.'&cmde=account&'.$href.'&fromPage='.$item); ?>
				<a class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm noprint" href="<?php echo $href_new; ?>">
	        <i class="fas fa-plus fa-sm text-white-50" title="Export"></i>&nbsp;Nouveau
	      </a>
			<?php } ?>

			<!-- Exporter -->
			<?php
				$href_export = $_SESSION["ROOTDIR"].'/exports.php?sid='.$_SESSION["sessID"].'&item='.$_GET['item'];
				if (isset($IDsel)) $href_export .= '&IDclass='.$IDsel;
				if (isset($_GET['cmde'])) $href_export .= '&cmde='.$_GET['cmde'];
				$temp = array('IDcentre', 'visu', 'IDsel', 'IDpromo', 'IDres', 'IDmat', 'IDalpha', 'recuseralpha', 'regime', 'gender', 'delegue', 'bourse', 'skpage', 'skshow', 'IDeleve');
				foreach ($temp as $value) if (isset($$value)) $href_export .= '&'.$value.'='.$$value;
			?>
      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="<?php echo $href_export; ?>">
        <i class="fas fa-upload fa-sm text-white-50" title="Export"></i>&nbsp;Exporter
      </a>

			<!-- Imprimer -->
      <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="#" onclick="window.print();return false;">
        <i class="fas fa-print fa-sm text-white-50" title="Imprimer"></i>&nbsp;Imprimer
      </a>
    </div>


    <div>
			<?php if (getParam('showCenter')) { ?>
	      <form id='formulairecenter' method="post" action="index.php" style='display: inline-block;'>
					<input type="hidden" name="item" value="<?php echo $item; ?>">
					<input type="hidden" name="IDalpha" value="<?php echo $IDalpha; ?>">
					<?php echo centerSelect($IDcentre, 'formulairecenter', 'IDcentre', 'IDcentre', false, 'btn d-none d-sm-inline-block btn-sm shadow-sm'); ?>
				</form>
			<?php } ?>


			<form id="formulaire" action="index.php?item=<?php echo $item; ?>" method="post" style="display: inline-block;">
				<?php
					$form_hidden_fields = array('item', 'IDalpha', 'IDcentre', 'Litem');
					foreach ($form_hidden_fields as $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
				?>

				<div class="form-row">
					<div class="col">
						<select id="IDsel" name="IDsel" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
							<option value="0"><?php echo $msg->read($STUDENT_ALLCLASSES); ?></option>
							<?php
							$query  = "select _IDclass, _ident from campus_classe where _IDcentre = '$IDcentre' ";
							if ($visu == 'O') $query .= "AND _visible = 'O' "; // pour les anciens élèves, certaines filières ont peut être disparues
							$query .= "order by _ident";
							$result = mysqli_query($mysql_link, $query);
							while ($cat = mysqli_fetch_row($result)) {
								if ($IDsel == $cat[0]) $selected = 'selected'; else $selected = '';
								echo '<option value="'.$cat[0].'" '.$selected.'>'.$cat[1].'</option>';
							}
							?>
						</select>
					</div>


					<?php
					// NOTE: VOIR QUAND CODE UTILE!!!
						if ( $visu == "N" ) {
							$query  = "select min(_date), max(_date) from user_promos ";

							$return = mysqli_query($mysql_link, $query);
							$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

							if ( $myrow ) {
								$min    = strval(substr($myrow[0], 0, 4));
								$max    = strval(substr($myrow[1], 0, 4));

								print("
									<label for=\"IDpromo\">
									<select id=\"IDpromo\" name=\"IDpromo\" onchange=\"document.forms.formulaire.submit()\">
										<option value=\"0\">". $msg->read($STUDENT_CHOOSEPERIOD) ."</option>");

								if ( $max )
									for ($k = $max; $k >= $min; $k--)
										printf("<option value=\"$k\" %s>$k</option>", $IDpromo == $k ? "selected=\"selected\"" : "");

								print("</label>");
								}
							}
					?>


					<div class="col">
						<select id="gender" name="gender" onchange="document.forms.formulaire.submit()" class="custom-select btn d-none d-sm-inline-block btn-sm shadow-sm">
							<option value="0"><?php echo $msg->read($STUDENT_SEX); ?></option>
							<option value="H" <?php if (isset($gender) && $gender == 'H') echo 'selected'; ?>><?php echo $msg->read($STUDENT_MALE); ?></option>
							<option value="F" <?php if (isset($gender) && $gender == 'F') echo 'selected'; ?>><?php echo $msg->read($STUDENT_FEMALE); ?></option>
						</select>
					</div>
				</div>
			</form>
    </div>

		<div>
			<!-- Recherche -->
			<form class="bs-docs-example" action="" method="get">
				<?php
					$form_hidden_fields = array('item', 'IDcentre', 'IDsel', 'IDpromo', 'visu', 'IDres', 'IDmat', 'regime', 'gender', 'delegue', 'bourse', 'mylang');
					foreach ($form_hidden_fields as $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
				?>
				<input type="hidden" name="recuseralpha" value="on">
				<input type="hidden" name="IDalpha" value="">
				<div class="input-group">
				  <input type="text" class="form-control" id="appendedInputButton" placeholder="<?php echo $msg->getTrad('_STUDENT'); ?>" aria-label="<?php echo $msg->getTrad('_STUDENT'); ?>" name="IDalpha" value="<?php if (isset($IDalpha)) echo stripslashes($IDalpha); ?>">
				  <div class="input-group-append">
				    <button class="btn btn-outline-secondary" type="submit">Ok</button>
				  </div>
				</div>
			</form>
		</div>

  </div>
</div>




<?php
	// On récupère les élèves
	$query  = "select distinctrow ";
	if (isset($IDpromo) && $IDpromo) $query .= "user_id._ID, user_promos._IDclass, ";
	else $query .= "user_id._ID, user_id._IDclass, ";
	$query .= "_name, _fname, _sexe, _born, _adr1, _adr2, _cp, _city, _tel, _regime, _bourse, _email, _IDgrp, user_id._delegue, user_id._lang, user_id._numen, user_id._adm " ;
	if (isset($IDpromo) && $IDpromo) $query .= "from user_id, campus_classe, user_promos ";
	else $query .= "from user_id, campus_classe ";
	$query .= "where user_id._visible = '$visu' ";
	$query .= "AND (campus_classe._IDcentre = '$IDcentre' AND campus_classe._IDclass = user_id._IDclass) ";
	if (isset($IDsel) && $IDsel) {
		if (isset($IDpromo) && $IDpromo) $query .= "AND (campus_classe._IDcentre = '$IDcentre' AND campus_classe._IDclass = user_id._IDclass) AND user_promos._IDclass = '$IDsel' ";
		else $query .= "AND user_id._IDclass = '$IDsel' ";
	}
	if (isset($IDpromo) && $IDpromo) $query .= "AND (user_promos._date = '$IDpromo-00-00' AND user_promos._IDeleve = user_id._ID) ";
	if (isset($gender) && $gender) $query .= "AND user_id._sexe = '$gender' ";
	if (isset($bourse) && $bourse) $query .= "AND user_id._regime = '$bourse' ";
	if (isset($delegue) && $delegue) $query .= "AND user_id._delegue = '$delegue' ";
	$query .= "AND _adm = '1' ";
	if (isset ($IDalpha) && $IDalpha) $query .= "AND (_name LIKE '%".$IDalpha."%' OR _fname LIKE '%".$IDalpha."%') ";		// Champ de recherche
	$query .= "order by user_id._name asc, user_id._name asc ";
	$result = mysqli_query($mysql_link, $query);
	$nbelem   = mysqli_num_rows($result);	// détermination du nombre de pages



	if ($currentPage != 'all') {
		$offset = ($currentPage - 1) * getParam('MAXPAGE');
		$query .= 'LIMIT '.getParam('MAXPAGE').' OFFSET '.$offset.' ';
	}
	$result = mysqli_query($mysql_link, $query);
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, true); ?><?php echo $msg->getTrad('_RESULTs').' : '.$nbelem; ?></h6>
  </div>
  <div class="card-body">


		<table class="table table-striped">
			<tr>
				<th class="d-print-none"></th>
				<th><?php echo $msg->getTrad('_CLASS'); ?></th>
				<th><?php echo $msg->read($STUDENT_NAME); ?></th>
				<th><?php echo $msg->read($STUDENT_MYDATE); ?></th>
				<th class="d-print-none">Option</th>
			</tr>



			<?php

			while ($row = mysqli_fetch_array($result)) {

				// suppression : il faut les droits du gestionnaire
				if ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 4) $delete = myurlencode('index.php?item='.$item.'&IDeleve='.$row[0].'&ID='.$row[14].'&submit=del&'.$href);
				else $delete = '';

				// Email de l'étudiant
				if ($row[13] != '') $studentemail = '<a href="mailto:'.$row[13].'"><i class="fas fa-envelope"></i></a>';
				else $studentemail = '';

				// Date de naissance
				if ($_SESSION['CnxGrp'] >= 2) $bornDate = getReadableBornDate($row[5]); else $bornDate = substr($row[5], 0, 4);

				// l'age de l'élève
				$age = date("Y") - strtok($row[5], "-");
				if (date("m") - strtok("-") < 0) $age = $age - 1;
				if ($age > 0 && $age < 100) $age = $msg->read($STUDENT_YEARSOLD, strval($age));

				$view_link = 'index.php?item=1&cmde=account&show=0&ID='.$row[0].'&Litem='.$item;
				$update_link = 'index.php?item='.$item.'&cmde=account&ID='.$row[0].'&Litem='.$item;

				echo '<tr>';
					echo '<td class="d-print-none">'.$studentemail.'</td>';	// L'émail de l'étudiant
					echo '<td>'.$classes[$row[1]].'</td>';									// Le nom de sa classe
					echo '<td>'.$row[2].' '.$row[3].'</td>';								// Le nom + prénom de l'étudiant
					echo '<td>'.$bornDate.' '.$age.'</td>';
					echo '<td class="d-print-none">';
						if ($_SESSION['CnxGrp'] > 1) echo '<a href="'.$view_link.'"><i class="fas fa-eye"></i></a>';
						if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) echo '&nbsp;<a href="'.$update_link.'"><i class="fas fa-pencil-alt"></i></a>';
						if ($_SESSION['CnxAdm'] == 255) echo '&nbsp;<a href="'.$delete.'" onclick="return confirm(\''.addslashes($msg->read($STUDENT_DELUSER, getUserNameByID($row[0], false))).'\');"><i class="fas fa-trash"></i></a>';
					echo '</td>';
				echo '</tr>';
			}
			?>
		</table>




  </div>
	<div class="card-footer text-muted">
		<?php
		$current_link = '?item='.$item.'&cmde='.$cmde;
		$elem_link = array('IDcentre', 'visu', 'IDres', 'IDmat', 'recuseralpha', 'regime', 'IDeleve', 'IDpromo', 'IDsel', 'gender', 'bourse', 'delegue', 'IDalpha');
		foreach ($elem_link as $value) if (isset($$value)) $current_link .= '&'.$value.'='.urlencode($$value);
		?>
		<?php echo getPagination($currentPage, $nbelem, $current_link, true, getParam('MAXPAGE')); ?>
  </div>
</div>
