<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2007 by Dominique Laporte (C-E-D@wanadoo.fr)

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
 *		module   : config_usrmenu.php
 *		projet   : la page de gestion des sous menus
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 22/04/07
 *		modif    :
 */

 // On vérifie que l'on soit bien un super-administrateur
 if ($_SESSION['CnxAdm'] != 255) header('Location: index.php');

$IDconf  = ( @$_POST["IDconf"] )
	? (int) $_POST["IDconf"]
	: (int) @$_GET["IDconf"] ;
$IDmenu  = ( @$_POST["IDmenu"] )
	? $_POST["IDmenu"]
	: @$_GET["IDmenu"] ;

$cbrd    = @$_POST["cbrd"];					// droits des lecteurs
$title   = trim(addslashes(@$_POST["title"]));
$text    = trim(addslashes(@$_POST["text"]));
$img     = @$_POST["img"];
$visible = ( @$_POST["visible"] ) ? "N" : "O" ;
$anonyme = ( @$_POST["anonyme"] ) ? "O" : "N" ;
$marquee = ( @$_POST["marquee"] ) ? "O" : "N" ;
$sort    = ( @$_POST["sort"] ) ? "O" : "N" ;
$rbox    = @$_POST["rbox"];

$submit  = ( @$_POST["valid_x"] )
	? ($IDmenu ? "update" : "new")
	: "" ;
?>


<?php

	// initialisation
  if ($IDmenu) $status = $msg->read($CONFIG_MODIFICATION); else $status = $msg->read($CONFIG_NEWMENU);

	// droits des lecteurs
	$grprd  = 0;
  if (isset($cbrd) && $cbrd != '') {
    for ($i = 0; $i < count($cbrd); $i++)
  		$grprd += ( @$cbrd[$i] ) ? $cbrd[$i] : 0 ;
  }


	// test d'erreur sur champs non renseignés
	$error  = ( !strlen($title) ) ? 1 : 0 ;

	// l'utilisateur a cliqué sur un lien
	switch ( $submit ) {
		case "new" :
			if (!$error) {
				$status = $msg->read($CONFIG_INSERT);

				$Query  = "insert into config_menu ";
				$Query .= "values(NULL, '$title', '$text', '0', '$marquee', '$img', '$grprd', '$anonyme', '', '$sort', '$visible', 'O', 'config_submenu', '0', '".$_SESSION["lang"]."')";

				if (!mysqli_query($mysql_link, $Query)) mysqli_error($mysql_link);
				else {
					$my_menu = mysqli_insert_id($mysql_link);
					$order   = ( $rbox == "G" ) ? $my_menu : -$my_menu ;

					$query   = "update config_menu ";
					$query  .= "set _order = '$order' ";
					$query  .= "where _IDmenu = '$my_menu' ";
					$query  .= "limit 1";
          $IDmenu = $my_menu;
					mysqli_query($mysql_link, $query);
					}
				}
			break;

		case "update" :
			$status = $msg->read($CONFIG_MODIFICATION);

			$order  = (int) @$_POST["order"];
			if ( ($order > 0 AND $rbox == "D") OR ($order < 0 AND $rbox == "G") )
				setUserMenuOrder($IDmenu, $rbox == "D" ? "right" : "left");

			$query  = "update config_menu ";
			$query .= strlen($title) ? "set _ident = '$title', " : "set " ;
			$query .= "_text = '$text', _sort = '$sort', _IDgrprd = '$grprd', _visible = '$visible', _anonyme = '$anonyme', _marquee = '$marquee', _img = '$img' ";
			$query .= "where _IDmenu = '$IDmenu' ";
			$query .= "AND _lang = '".$_SESSION["lang"]."' ";
			$query .= "limit 1";
			if (!mysqli_query($mysql_link, $query)) mysqli_error($mysql_link);
			break;

		default :
			break;
		}

	$query  = "select _ident, _text, _order, _visible, _marquee, _img, _anonyme, _sort, _IDgrprd ";
	$query .= "from config_menu ";
	$query .= "where _IDmenu = '$IDmenu' ";
	$query .= "AND _lang = '".$_SESSION["lang"]."' ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	if ($row) {
		// initialisation des champs de saisie
		$title   = $row[0];
		$text    = $row[1];
		$rbox    = ($row[2] > 0) ? 'G' : 'D';
		$visible = $row[3];
		$marquee = $row[4];
		$img     = ($row[5]) ? $row[5] : '';
		$anonyme = $row[6];
		$sort    = $row[7];
		$grprd   = $row[8];
	}
	else
		if (!$error) {
			// réinitialisation des champs de saisie
			$title   = '';
			$text    = '';
			$rbox    = 'D';
			$visible = 'O';
			$marquee = 'N';
			$img     = '';
			$anonyme = 'O';
			$sort    = 'O';
			$grprd   = 255;
		}
?>



<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php print($msg->read($CONFIG_MANAGEMENT)); ?></h1>


<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo $msg->read($CONFIG_STATUS).' : '.$status; ?></h6>
  </div>
  <div class="card-body">
    <form id="formulaire" action="" method="post">
      <input type="hidden" name="valid_x" value="<?php if ($IDmenu) echo 'update'; else echo 'new'; ?>">


      <!-- Intitulé -->
      <?php if ($IDmenu == 0 OR @$_GET["action"] == "update") { ?>
        <?php if ($error AND !strlen($title)) $error_title = true; else $error_title = false; ?>
        <div class="form-group">
          <label for="title"><?php echo $msg->read($CONFIG_IDENT); ?></label>
          <input type="text" class="form-control <?php if ($error_title) echo 'is-invalid'; ?>" id="title" name="title" size="50" value="<?php echo $title; ?>" required>
          <?php if ($error_title) { ?>
            <div id="titleFeedback" class="invalid-feedback"><?php echo $msg->read($CONFIG_ERRIDENT); ?></small>
          <?php } ?>
        </div>
      <?php } else { ?>
        <div class="form-group">
          <label for="IDmenu"><?php echo $msg->read($CONFIG_IDENT); ?></label>
          <select class="form-control custom-select" id="IDmenu" name="IDmenu" onchange="document.forms.formulaire.submit()">
            <?php
              $query  = "select _IDmenu, _ident, _activate from config_menu where _activate = 'O' AND _lang = '".$_SESSION["lang"]."' order by _ident";
              $result = mysqli_query($mysql_link, $query);
              while ($row = mysqli_fetch_row($result)) {
                if ($IDmenu == $row[0]) $selected = 'selected'; else $selected = '';
                echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
              }
            ?>
          </select>
        </div>

        <a href="<?php echo myurlencode('index.php?item='.$item.'&cmde=usrmenu&IDconf='.$IDconf.'&IDmenu='.$IDmenu.'&action=update'); ?>" class="btn btn-secondary"><i class="fas fa-pencil-alt"></i>&nbsp;<?php echo $msg->read($CONFIG_MODIFY); ?></a>
      <?php } ?>


      <hr>

      <!-- Pictogramme -->
      <div class="form-group">
        <label for="img"><?php echo $msg->read($CONFIG_PICTURE); ?></label>
        <input type="text" class="form-control" id="img" name="img" value="<?php echo $img; ?>">
        <small id="imgHelp" class="form-text text-muted"><?php echo $msg->read($CONFIG_FONTAWESOMEREQUIRED); ?></small>
      </div>


      <hr>

      <!-- Droits d'accès -->
      <h6><?php echo $msg->read($CONFIG_ACCESS); ?></h6>
      <?php
      // recherche des groupes
      $query  = "select _IDgrp, _ident from user_group where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' order by _IDgrp asc ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_row($result)) {
        if ($grprd & pow(2, $row[0] - 1)) $checked = 'checked'; else $checked = '';
        echo '<div class="custom-control custom-checkbox">';
          echo '<input type="checkbox" class="custom-control-input" name="cbrd[]" id="cbrd_'.$row[0].'" value="'.pow(2, $row[0] - 1).'" '.$checked.'>';
          echo '<label class="custom-control-label" for="cbrd_'.$row[0].'">'.$row[1].'</label>';
        echo '</div>';
      }
      ?>

      <hr>

      <!-- Boutons de fin de formulaire -->
      <a href="index.php?item=<?php echo $item; ?>&IDconf=<?php echo $IDconf; ?>&IDmenu=<?php echo $IDmenu; ?>&cmde=menu" class="btn btn-danger"><i class="fas fa-chevron-left"></i>&nbsp;<?php echo $msg->read($CONFIG_BACK); ?></a>
      <button type="submit" name="valid" class="btn btn-success"><?php if ($IDmenu) echo $msg->read($CONFIG_UPDTMENU); else echo $msg->read($CONFIG_ADDMENU); ?></button>
    </form>
  </div>
</div>
