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
 *		module   : email_list.php
 *		projet   : gestion des annuaires
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/01/07
 *		modif    :
 */

$IDcentre = ( strlen(@$_POST["IDcentre"]) )		// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) $_SESSION["CnxCentre"] ;
$IDlidi   = ( @$_POST["IDlidi"] )     			// identifiant de la liste de diffusion
	? (int) $_POST["IDlidi"]
	: (int) @$_GET["IDlidi"] ;
$list     = addslashes(trim(@$_POST["list"]));     	// nom de la liste de diffusion
$public   = @$_POST["public"];				// item public de la liste de diffusion
$IDdst    = @$_POST["IDdst"];					// identifiant du groupe
$cb       = @$_POST["cb"];					// identifiants des mambres

$submit   = ( @$_POST["valid_x"] )				// bouton de validation
	? $_POST["valid_x"]
	: @$_GET["valid"] ;
?>


<?php
	// Suppression d'une liste de diffusion
	if (isset($submit) && $submit == 'del') {
		$query  = "delete from postit_lidi where _IDlidi = '$IDlidi' AND _ID = '".$_SESSION["CnxID"]."' limit 1";
		mysqli_query($mysql_link, $query);
	}
?>


<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($LIDI_LIDI); ?></h1>
  <div style="float: right; text-align: right;">
    <div>
      <a class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm noprint" href="index.php?item=11&cmde=gestion">
        <i class="fas fa-plus fa-sm text-white-50" title="New"></i>&nbsp;<?php echo $msg->read($LIDI_CREATELIST); ?>
      </a>
    </div>
  </div>
</div>

<?php
	$query  = "SELECT _IDlidi, _nom, _ID FROM postit_lidi ";
	$query .= "WHERE (_public = 'O' OR _ID = ".$_SESSION["CnxID"].") ";
	$query .= "ORDER by _nom";
	$result = mysqli_query($mysql_link, $query);
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
		<span style="display: inline-block; background-color: #dee7f8; border: 1px solid #cad8f3; border-radius: 6px; padding: 5px; margin: 1px;"><?php echo $msg->read($LIDI_USER); ?></span>
		<span style="display: inline-block; background-color: #def7e6; border: 1px solid #cbf2ce; border-radius: 6px; padding: 5px; margin: 1px;"><?php echo $msg->read($LIDI_CLASS); ?></span>
		<span style="display: inline-block; background-color: #f7dfde; border: 1px solid #f2cccb; border-radius: 6px; padding: 5px; margin: 1px;"><?php echo $msg->read($LIDI_GROUP); ?></span>
		<span style="display: inline-block; background-color: #f4ecdc; border: 1px solid #ffca60; border-radius: 6px; padding: 5px; margin: 1px;"><?php echo $msg->read($LIDI_CLASSPROF); ?></span>
		<span style="display: inline-block; background-color: #f4dcf3; border: 1px solid #ff6df7; border-radius: 6px; padding: 5px; margin: 1px;"><?php echo $msg->read($LIDI_MATPROF); ?></span>
		<span style="display: inline-block; background-color: #f7f7f7; border: 1px solid #cccccc; border-radius: 6px; padding: 5px; margin: 1px;"><?php echo $msg->read($LIDI_FUNCTION); ?></span>
  </div>
  <div class="card-body">

		<table class="table table-striped">
			<tr>
				<th></th>
				<th>Nom</th>
				<th>Diffusion</th>
				<th></th>
			</tr>
			<?php
				while ($row = mysqli_fetch_row($result)) {
					echo '<tr>';
						// Bouton modifier
						echo '<td>';
							// Si on a crée la liste de diffusion, on peux la modifier
							if($row[2] == $_SESSION['CnxID']) echo '<a class="btn" href="index.php?item='.$item.'&cmde=gestion&IDlidi='.$row[0].'" style="margin-bottom: 5px;"><i class="fas fa-pencil-alt"></i></a>';
						echo '</td>';

						// Le nom de la liste de diffusion
						echo '<td style="font-weight: bold;">'.$row[1].'</td>';

						// Les personnes/groupes concernés (destinataires)
						echo '<td>';
							// Sélection liste personne
							$resultl = mysqli_query($mysql_link, "SELECT p._ID, u._name, u._fname FROM postit_address p, user_id u WHERE p._ID = u._ID AND p._IDlidi = $row[0] AND p._type = 'user' ");
							while ($rowl = mysqli_fetch_row($resultl)) echo '<span data-toggle="tooltip" data-placement="top" title="'.$msg->read($LIDI_USER).'" style="display: inline-block; background-color: #dee7f8; border: 1px solid #cad8f3; border-radius: 6px; padding: 5px; margin: 1px;">'.$rowl[1].' '.$rowl[2].'</span>';

							// Sélection liste class
							$resultl = mysqli_query($mysql_link, "SELECT p._ID, c._IDclass, c._ident FROM postit_address p, campus_classe c WHERE p._ID = c._IDclass AND p._IDlidi = $row[0] AND p._type = 'class' ");
							while ($rowl = mysqli_fetch_row($resultl)) echo '<span data-toggle="tooltip" data-placement="top" title="'.$msg->read($LIDI_CLASS).'" style="display: inline-block; background-color: #def7e6; border: 1px solid #cbf2ce; border-radius: 6px; padding: 5px; margin: 1px;">'.$rowl[2].'</span>';

							// Sélection liste group
							$resultl = mysqli_query($mysql_link, "SELECT _ID FROM postit_address WHERE _IDlidi = $row[0] AND _type = 'group' ");
							while ($rowl = mysqli_fetch_row($resultl)) echo '<span data-toggle="tooltip" data-placement="top" title="'.$msg->read($LIDI_GROUP).'" style="display: inline-block; background-color: #f7dfde; border: 1px solid #f2cccb; border-radius: 6px; padding: 5px; margin: 1px;">'.getNomByIDgrp($rowl[0]).'</span>';

							// Sélection liste des profs d'une classe
							$resultl = mysqli_query($mysql_link, "SELECT p._ID, c._IDclass, c._ident FROM postit_address p, campus_classe c WHERE p._ID = c._IDclass AND p._IDlidi = $row[0] AND p._type = 'classprof' ");
							while ($rowl = mysqli_fetch_row($resultl)) echo '<span data-toggle="tooltip" data-placement="top" title="'.$msg->read($LIDI_CLASSPROF).'" style="display: inline-block; background-color: #f4ecdc; border: 1px solid #ffca60; border-radius: 6px; padding: 5px; margin: 1px;">'.$rowl[2].'</span>';

							// Sélection liste des profs d'une matière
							$resultl = mysqli_query($mysql_link, "SELECT p._ID, c._IDmat, c._titre FROM postit_address p, campus_data c WHERE p._ID = c._IDmat AND c._lang = '".$_SESSION["lang"]."' AND p._IDlidi = $row[0] AND p._type = 'matprof' ");
							while ($rowl = mysqli_fetch_row($resultl)) echo '<span data-toggle="tooltip" data-placement="top" title="'.$msg->read($LIDI_MATPROF).'" style="display: inline-block; background-color: #f4dcf3; border: 1px solid #ff6df7; border-radius: 6px; padding: 5px; margin: 1px;">'.$rowl[2].'</span>';

							// Sélection ck
							$temp = array('ck_admin', 'ck_ens', 'ck_eleve1', 'ck_eleve2', 'ck_fr', 'ck_de');
							foreach ($temp as $value) {
								$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $row[0] AND p._type = '".$value."' ";
								$resultl = mysqli_query($mysql_link, $query);
								if (mysqli_num_rows($resultl)) {
									switch ($value) {
										case 'ck_admin': $text = $msg->read($LIDI_ADMIN); break;
										case 'ck_ens': $text = $msg->read($LIDI_ENS); break;
									}
									echo '<span data-toggle="tooltip" data-placement="top" title="'.$msg->read($LIDI_FUNCTION).'" style="display: inline-block; background-color: #f7f7f7; border: 1px solid #cccccc; border-radius: 6px; padding: 5px; margin: 1px;">'.$text.'</span>';
								}
							}
						echo '</td>';

						// Suppression de la liste de diffusion
						echo '<td>';
							if($row[2] == $_SESSION['CnxID']) echo '<a class="btn" href="'.myurlencode('index.php?item='.$item.'&cmde='.$cmde.'&IDlidi='.$row[0].'&valid=del').'"><i class="fas fa-trash"></i></a>';
						echo '</td>';
					echo '</tr>';
					echo '<tr style="display: none;"></tr>';
				}
			?>
		</table>
  </div>
</div>
