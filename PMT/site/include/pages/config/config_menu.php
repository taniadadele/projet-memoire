<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2008 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : config_menu.php
 *		projet   : paramétrage des menus
 *
 *		version  : 2.0
 *		auteur   : laporte
 *		creation : 19/08/06
 *		modif    :
 *					 29/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7 et maj du thème
 */

 // On vérifie que l'on soit bien un super-administrateur
 if ($_SESSION['CnxAdm'] != 255) header('Location: index.php');

// $IDconf    = ( @$_POST["IDconf"] )					// ID de la configuration
// 	? (int) $_POST["IDconf"]
// 	: (int) @$_GET["IDconf"] ;
//
// $IDmenu    = ( @$_POST["IDmenu"] )					// ID du menu
// 	? (int) $_POST["IDmenu"]
// 	: (int) @$_GET["IDmenu"];
// $IDsubmenu = ( @$_POST["IDsubmenu"] )				// ID du sous menu
// 	? (int) $_POST["IDsubmenu"]
// 	: (int) @$_GET["IDsubmenu"] ;
// $IDitem    = (int) @$_POST["IDitem"];				// ID du type de e-groupe
// $text      = addslashes(trim(@$_POST["text"]));			// intitulé du menu

$submit    = ( @$_GET["submit"] )					// bouton de validation
	? $_GET["submit"]
	: @$_POST["valid_x"] ;


	// On récupère les éléments dans le GET
	$get = array();
	foreach ($get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];

	// On récupère les éléments dans le POST
	$post = array('text');
	foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

	// On récupère les éléments dans le POST et si existe pas alors dans le GET
	$post_get = array('IDconf', 'IDmenu', 'IDsubmenu');
	foreach ($post_get as $value) {
		if (isset($_POST[$value]) && $_POST[$value]) $$value = $_POST[$value];
		elseif (isset($_GET[$value]) && $_GET[$value]) $$value = $_GET[$value];
	}

	// On récupère les éléments dans le GET et si existe pas alors dans le POST
	$get_post = array();
	foreach ($post_get as $value) {
		if (isset($_GET[$value]) && $_GET[$value]) $$value = $_GET[$value];
		elseif (isset($_POST[$value]) && $_POST[$value]) $$value = $_POST[$value];
	}

	if (isset($text)) $text = addslashes(trim($text));

  if (!isset($IDconf)) $IDconf = $_SESSION['CnxCentre'];
?>


<?php
	// // vérification des autorisations
	// admSessionAccess();

	switch ( $submit ) {
		case "deactivate" :
		case "activate" :
			if ( $submit == "deactivate" )
				mysqli_query($mysql_link, "update config_menu set _activate = 'N' where _IDmenu = '$IDmenu'");
			else
				mysqli_query($mysql_link, "update config_menu set _activate = 'O' where _IDmenu = '$IDmenu'");
			break;

		case "visible.gif" :
			mysqli_query($mysql_link, "update config_submenu set _visible = 'N' where _IDsubmenu = '$IDsubmenu' limit 1");
			break;
		case "invisible.gif" :
			mysqli_query($mysql_link, "update config_submenu set _visible = 'O' where _IDsubmenu = '$IDsubmenu' limit 1");
			break;

		case "erase" :
			if ( mysqli_query($mysql_link, "delete from config_menu where _IDmenu = '$IDmenu'") )
				mysqli_query($mysql_link, "delete from config_submenu where _IDmenu = '$IDmenu'");

			$IDmenu = 0;
			break;

		default :
			if ($submit) {
				// recherche de la table ad hoc
				$query  = "select _table from config_menu ";
				$query .= "where _IDmenu = '$IDmenu' AND _lang = '".$_SESSION["lang"]."' ";
				$query .= "limit 1";

				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

				$record = ( $row[0] == "config_submenu" ) ? "_IDsubmenu" : "_IDmenu" ;
				$sql    = ( $row[0] == "egroup_menu" ) ? "AND _IDitem = '$IDitem' " : "" ;

				// initialisation
				$query  = "update $row[0] ";
				$query .= "set _visible = 'N', _anonyme = 'N', _backoffice = 'N' ";
				$query .= "where _lang = '".$_SESSION["lang"]."' ";
				$query .= ( $row[0] == "config_submenu" ) ? "AND _IDmenu = '$IDmenu' " : "" ;
				$query .= $sql;

				mysqli_query($mysql_link, $query);

				// cacher les items des menus
				if (isset($_POST["show"])) {
					$cb = @$_POST["show"];
					for ($i = 0; $i < count($cb); $i++)
						mysqli_query($mysql_link, "update $row[0] set _visible = 'O' where $record = '".@$cb[$i]."' AND _lang = '".$_SESSION["lang"]."' $sql limit 1");
				}

				// accès anonymes

				if (isset($_POST['access'])) {
					$cb = @$_POST["access"];
					for ($i = 0; $i < count($cb); $i++)
						mysqli_query($mysql_link, "update $row[0] set _anonyme = 'O' where $record = '".@$cb[$i]."' AND _lang = '".$_SESSION["lang"]."' $sql limit 1");
				}

				// backoffice
				if (isset($_POST["back"])) {
					$cb = @$_POST["back"];
					for ($i = 0; $i < count($cb); $i++)
						mysqli_query($mysql_link, "update $row[0] set _backoffice = 'O' where $record = '".@$cb[$i]."' AND _lang = '".$_SESSION["lang"]."' $sql limit 1");
				}

				// suppression
				if (isset($_POST["cbox"])) {
					$cb = @$_POST["cbox"];
					for ($i = 0; $i < count($cb); $i++)
						mysqli_query($mysql_link, "delete from $row[0] where $record = '".@$cb[$i]."' AND _lang = '".$_SESSION["lang"]."' $sql limit 1");
				}
			}
			break;
	}

	if (isset($text) && strlen($text))
		mysqli_query($mysql_link, "update config_menu set _ident = '$text' where _IDmenu = '$IDmenu' AND _lang = '".$_SESSION["lang"]."' limit 1");
?>





<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($CONFIG_CONFIGMENUS); ?></h1>

<?php include("include/config_menu_top.php"); ?>

<form id="formulaire" action="index.php" method="post">
  <div class="card shadow mb-4">
    <div class="card-header py-3">

  		<?php
  			$hidden_form_items = array('item', 'cmde', 'IDconf', 'IDmenu');
  			foreach ($hidden_form_items as $value) {
  				if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
  			}
  		?>

  		<select id="IDmenu" name="IDmenu" class="custom-select col-md-3" onchange="document.forms.formulaire.submit()">
  			<option value="0" disabled><?php echo $msg->read($CONFIG_CHOOSEMENU); ?></option>
  			<?php
  			// affichage des modules actifs
  			$query  = "select _IDmenu, _ident, _activate, _table from config_menu where _activate = 'O' AND _lang = '".$_SESSION["lang"]."' order by _ident ";
  			$result = mysqli_query($mysql_link, $query);

  			if (mysqli_num_rows($result)) {
  				echo '<optgroup label="'. $msg->read($CONFIG_ACTIVATED) .'">';
  				while ($row = mysqli_fetch_row($result)) {

  					if (!isset($IDmenu) || !$IDmenu) $IDmenu = $row[0];

  					if ($IDmenu == $row[0]) {
  						$activate = $row[2];
  						$table    = $row[3];
  						$select   = 'selected';
  					}
  					else $select = '';
  					echo '<option value="'.$row[0].'" '.$select.'>'.str_replace($keywords_search, $keywords_replace, $row[1]).'</option>';
  				}
  				echo '</optgroup>';
  			}

  			// affichage des modules inactifs
  			$query  = "select _IDmenu, _ident, _activate, _table from config_menu where _activate = 'N' AND _lang = '".$_SESSION["lang"]."' order by _ident ";
  			$result = mysqli_query($mysql_link, $query);

  			if (mysqli_num_rows($result)) {
  				echo '<optgroup label="'.$msg->read($CONFIG_DEACTIVATED).'">';
  				while ($row = mysqli_fetch_row($result)) {
  					if (!isset($IDmenu) || !$IDmenu) $IDmenu = $row[0];

  					if ($IDmenu == $row[0]) {
  						$activate = $row[2];
  						$table    = $row[3];
  						$select   = 'selected';
  					}
  					else $select = '';
  					echo '<option value="'.$row[0].'" '.$select.'>'.str_replace($keywords_search, $keywords_replace, $row[1]).'</option>';
  				}
  				echo '</optgroup>';
  			}
  			?>
  		</select>


  		<?php
  		if (isset($activate) && $activate == 'O') {
  			$submit = 'deactivate';
  			$title = $msg->read($CONFIG_DEACTIVATE);
  		}
  		else {
  			$submit = 'activate';
  			$title = $msg->read($CONFIG_ACTIVATE);
  		}

  		echo '<a href="'.myurlencode('index.php?item='.(@$item).'&cmde='.(@$cmde).'&IDconf='.(@$IDconf).'&IDmenu='.(@$IDmenu).'&submit='.(@$submit)).'" class="btn btn-secondary">'.$title.'</a>';
  		echo '&nbsp;<a href="'.myurlencode('index.php?item='.(@$item).'&cmde=usrmenu&IDconf='.(@$IDconf).'&IDmenu='.(@$IDmenu)).'" class="btn btn-secondary"><i class="fas fa-pencil-alt"></i>&nbsp;'.$msg->read($CONFIG_MODIFY).'</a>';
  		echo '&nbsp;<a href="'.myurlencode('index.php?item='.(@$item).'&cmde=usrmenu&IDconf='.(@$IDconf)).'" class="btn btn-success"><i class="fas fa-plus"></i>&nbsp;'.$msg->read($CONFIG_ADDMENU).'</a>';
  		echo '&nbsp;<a href="'.myurlencode('index.php?item='.(@$item).'&cmde='.(@$cmde).'&IDconf='.(@$IDconf).'&IDmenu='.(@$IDmenu).'&submit=erase').'" class="btn btn-danger"><i class="fas fa-trash"></i>&nbsp;'.$msg->read($CONFIG_DELETE).'</a>';
  		?>
  	</form>
  </div>
  <div class="card-body">
  	<form id="select" action="index.php" method="post">
  		<?php
  			$hidden_form_items = array('item', 'cmde', 'IDconf', 'IDmenu');
  			foreach ($hidden_form_items as $value) {
  				if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
  			}
  		?>
      <table class="table table-striped">
  			<tr>
  				<th class="align-center" style="width:1%;"><i class="fas fa-trash" title="<?php echo $msg->read($CONFIG_DELETE); ?>"></i></th>
  				<th class="align-center" style="width:1%;"><i class="fas fa-eye" title="<?php echo $msg->read($CONFIG_SHOWLINK); ?>"></i></th>
  				<th class="align-center" style="width:1%;"><?php echo $msg->read($CONFIG_ANONYMOUS); ?></th>
  				<!-- <th class="align-center" style="width:1%;"><?php echo $msg->read($CONFIG_BACKOFFICE); ?></th> -->
  				<th>
  					<?php print($msg->read($CONFIG_SUBMENU)); ?>
  					<a href="<?php echo myurlencode('index.php?item='.(@$item).'&cmde=submenu&IDconf='.(@$IDconf).'&IDmenu='.(@$IDmenu)); ?>" class="btn btn-sm btn-success"><i class="fas fa-plus" title="<?php echo $msg->read($CONFIG_CREAT); ?>"></i>&nbsp;<?php echo $msg->read($CONFIG_CREAT); ?></a>
  				</th>
  			</tr>

  			<?php

        // lecture de la base de données des liens du menu
		switch ( $table ) {
			case "campus_menu" :
				$query  = "select _IDmenu, _ident, _link, _visible, _anonyme, _backoffice from campus_menu ";
				$query .= "where _lang = '".$_SESSION["lang"]."' ";
				$query .= "order by _IDmenu";
				break;

			case "egroup_menu" :
				$query  = "select _IDmenu, _ident, _link, _visible, _anonyme, _backoffice from egroup_menu ";
				$query .= "where _IDitem = '$IDitem' AND _lang = '".$_SESSION["lang"]."' ";
				$query .= "order by _IDmenu";
				break;

			default :
				$query  = "select _IDsubmenu, _ident, _link, _visible, _anonyme, _backoffice from config_submenu ";
				$query .= "where _IDmenu = '$IDmenu' AND _lang = '".$_SESSION["lang"]."' ";
				$query .= "order by _IDsubmenu";
				break;
			}

  			// $query  = "select _IDsubmenu, _ident, _link, _visible, _anonyme, _backoffice from config_submenu ";
  			// $query .= "where _IDmenu = '$IDmenu' AND _lang = '".$_SESSION["lang"]."' ";
  			// $query .= "order by _IDsubmenu";
  			$result = mysqli_query($mysql_link, $query);
  			while ($row = mysqli_fetch_row($result)) {
  				if ($row[3] == 'O') $chk1 = 'checked'; else $chk1 = ''; // affichage du lien
  				if ($row[4] == 'O') $chk2 = 'checked'; else $chk2 = '';	// accès anonyme
  				if ($row[5] == 'O') $chk3 = 'checked'; else $chk3 = '';	// accès backofice
  				// modification du lien
  				$maj = '<a href="'.myurlencode('index.php?item='.(@$item).'&IDconf='.(@$IDconf).'&IDsubmenu='.(@$row[0]).'&IDmenu='.(@$IDmenu).'&table='.(@$table).'&cmde=submenu').'"><i class="fas fa-pencil-alt" title="'.$msg->read($CONFIG_MODIFY).'"></i></a>';
  				echo '<tr>';
  					echo '<td><input type="checkbox" id="cbox_'.$row[0].'" name="cbox[]" value="'.$row[0].'"></td>';
  					echo '<td><input type="checkbox" id="show_'.$row[0].'" name="show[]" value="'.$row[0].'" '.$chk1.'></td>';
  					// echo '<td><input type="checkbox" id="access_'.$row[0].'" name="access[]" value="'.$row[0].'" '.$chk2.'></td>';
  					echo '<td><input type="checkbox" id="back_'.$row[0].'" name="back[]" value="'.$row[0].'" '.$chk3.'></td>';
  					echo '<td>'.$row[1].'&nbsp;'.$maj.'<br>'.htmlspecialchars($row[2]).'</td>';
  				echo '</tr>';
  			}
  			?>
      </table>
  		<hr>
  		<button type="submit" name="valid_x" value="submit" class="btn btn-success"><?php echo $msg->read($CONFIG_INPUTOK); ?></button>

    </div>
  </div>
</form>
