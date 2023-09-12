<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006-2008 by Dominique Laporte (C-E-D@wanadoo.fr)

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
 *		module   : config_submenu.php
 *		projet   : la page de gestion des sous menus
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 18/08/06
 *		modif    :
 */

 // On vérifie que l'on soit bien un super-administrateur
 if ($_SESSION['CnxAdm'] != 255) header('Location: index.php');

$IDconf    = ( @$_POST["IDconf"] )				// ID de la configuration
	? (int) $_POST["IDconf"]
	: (int) @$_GET["IDconf"] ;
$IDmenu    = ( @$_POST["IDmenu"] )				// ID du menu
	? (int) $_POST["IDmenu"]
	: (int) @$_GET["IDmenu"] ;
$IDsubmenu = ( @$_POST["IDsubmenu"] )			// ID de l'item du menu ( < 0 si sous item)
	? (int) $_POST["IDsubmenu"]
	: (int) @$_GET["IDsubmenu"] ;

$cbrd      = @$_POST["cbrd"];					// droits des lecteurs
$title     = trim(addslashes(@$_POST["title"]));
$url       = trim(addslashes(@$_POST["url"]));
$isurl     = ( @$_POST["isurl"] ) ? $_POST["isurl"] : ($_SESSION["CnxAdm"] == 255 ? "O" : "N") ;
$visible   = ( @$_POST["visible"] ) ? "N" : "O" ;
$anonyme   = ( @$_POST["anonyme"] ) ? "O" : "N" ;
$IDitem    = ( int ) @$_POST["IDitem"];


if (isset($IDsubmenu)) $IDitem = $IDsubmenu;
// if (isset($_POST['IDitem'])) $IDitem = (int) $_POST['IDitem'];
// elseif (isset($_GET['IDitem'])) $IDitem = (int) $_GET['IDitem'];

$submit    = ( @$_POST["valid"] )
	? @$_POST["valid"]
	: "" ;
?>


<?php
//---------------------------------------------------------------------------
function findmain($idmenu)
{
	require "globals.php";

	$Query  = "select _IDmenu from config_submenu ";
	$Query .= "where _IDsubmenu = '".abs($idmenu)."' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	return ( $row[0] < 0 ) ? findmain($row[0]) : $row[0] ;
}
//---------------------------------------------------------------------------


	// recherche du menu principal
	$idmain = ( $IDmenu < 0 ) ? findmain($IDmenu) : $IDmenu ;

	$query  = "select _ident, _table from config_menu ";
	$query .= "where _IDmenu = '$idmain' ";
	$query .= "AND _lang = '".$_SESSION["lang"]."' ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);
	$menu   = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	// initialisation
	$status = ( (isset($IDitem) && $IDitem != '') OR $IDsubmenu > 0 )
		? $msg->read($CONFIG_LINKUPDT)
		: $msg->read($CONFIG_LINKNEW) ;

	// droits des lecteurs
	$grprd  = 0;
	if (isset($cbrd)) {
		for ($i = 0; $i < count($cbrd); $i++)
			$grprd += ( @$cbrd[$i] ) ? $cbrd[$i] : 0 ;
	}


	// test d'erreur sur champs non renseignés
	$error = ( !strlen($title) OR (!strlen($url) AND $isurl == "O") ) ? 1 : 0 ;

	// l'utilisateur a cliqué sur un lien
	switch ( $submit ) {
		case "valider" :
			if ( !$error ) {
				switch ( $menu[1] ) {
					case "egroup_menu" :
						break;

					case "campus_menu" :
						$status = $msg->read($CONFIG_INSERT) ." ";

						$query  = "insert into campus_menu ";
						$query .= "values(NULL, '$title', '$title', '$url', '$isurl', '$grprd', '$anonyme', 'N', '$visible', '0', '".$_SESSION["lang"]."')";

						if (!mysqli_query($mysql_link, $query)) mysqli_error($mysql_link);
						break;

					default :
						$status = $msg->read($CONFIG_INSERT) ." ";
						$mymenu = ( $IDsubmenu ) ? $IDsubmenu : $IDmenu ;

						$query  = "insert into config_submenu ";
						$query .= "values(NULL, '$mymenu', '$title', '$url', '$isurl', '0', '$grprd', '$anonyme', 'N', '', '$visible', '0', '".$_SESSION["lang"]."')";

						if (!mysqli_query($mysql_link, $query)) mysqli_error($mysql_link);
						else {
							$IDsub = $IDsubmenu = mysqli_insert_id($mysql_link);
							// mise à jour de la place de l'item dans le menu
							mysqli_query($mysql_link, "update config_submenu set _order = '$IDsub' where _IDsubmenu = '$IDsub' limit 1");
						}
						// ajout auto
						if ( $IDsubmenu < 0 ) $IDitem = 0;
						break;
					}
				}
			break;

		case "update" :
			if ( !$error ) {
				$status = $msg->read($CONFIG_MODIFICATION) ." ";

				// on force l'url sur la gestion de contenu
				$url    = ( $isurl == "O" ) ? $url : "item=19&IDsubmenu=$IDitem" ;
				$option = "";

				switch ( $menu[1] ) {
					case "campus_menu" :
					case "egroup_menu" :
						$record = "_IDmenu";
						break;
					default :
						$record = "_IDsubmenu";
						break;
				}
				$Query  = "update $menu[1] ";
				$Query .= "set _ident = '$title', _link = '$url', _url = '$isurl', _IDgrprd = '$grprd', _visible = '$visible', _anonyme = '$anonyme' ";
				$Query .= $option;
				$Query .= "where $record = '$IDitem' ";
				$Query .= "limit 1";

				if ( !mysqli_query($mysql_link, $Query) ) mysqli_error($mysql_link);

				}
			break;

		default :
			// suppression image
			if ( @$_GET["delimg"] )
				if ( mysqli_query($mysql_link, "update config_submenu set _image = '' where _IDmenu = '$IDmenu' limit 1") )
					@unlink("$DOWNLOAD/menu/".$_GET["delimg"]);
			break;
		}

	// lecture de la base de données des liens du menu
	switch ( $menu[1] ) {
		case "campus_menu" :
			$Query  = "select _ident, _link, _IDgrprd, _visible, _anonyme, _url ";
			$Query .= "from campus_menu ";
			$Query .= "where _lang = '".$_SESSION["lang"]."' ";
			$Query .= ( $IDitem ) ? "AND _IDmenu = '$IDitem' " : "AND _IDmenu = '$IDsubmenu' " ;
			$Query .= "limit 1";
			break;

		case "egroup_menu" :
			$Query  = "select _ident, _link, _IDgrprd, _visible, _anonyme, _url ";
			$Query .= "from egroup_menu ";
			$Query .= "where _IDitem = '3' AND _lang = '".$_SESSION["lang"]."' ";
			$Query .= ( $IDitem ) ? "AND _IDmenu = '$IDitem' " : "AND _IDmenu = '$IDsubmenu' " ;
			$Query .= "limit 1";
			break;

		default :
			$Query  = "select _ident, _link, _IDgrprd, _visible, _anonyme, _url, _image ";
			$Query .= "from config_submenu ";
			$Query .= ( isset($IDitem) && $IDitem ) ? "where _IDsubmenu = '$IDitem' " : "where _IDsubmenu = '$IDsubmenu' " ;
			$Query .= "order by _ident ";
			$Query .= "limit 1";
			break;
		}

	$result  = mysqli_query($mysql_link, $Query);
	$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	if ( $row ) {
		// initialisation des champs de saisie
		$title   = $row[0];
		$url     = $row[1];
		$grprd   = $row[2];
		$visible = $row[3];
		$anonyme = $row[4];
		$isurl   = $row[5];
		$image   = @$row[6];
		}
	else
		if ( !$error ) {
			// réinitialisation des champs de saisie
			$title   = "";
			$url     = "";
			$grprd   = "";
			$visible = "O";
			$anonyme = "N";
			$isurl   = "O";
			}
		else {
			$title   = stripslashes($title);
			$url     = stripslashes($url);
			}
?>



<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($CONFIG_MANAGEMENT); ?></h1>


<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo $msg->read($CONFIG_STATUS).' : '.$status; ?></h6>
  </div>
  <div class="card-body">


		<form id="item_form" action="index.php" method="get">
			<?php
				$hidden_form_items = array('item', 'cmde', 'IDconf', 'IDmenu', 'IDitem');
				foreach ($hidden_form_items as $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
			?>
			<!-- Sélection du sous-menu -->
			<div class="form-group">
				<label for="IDsubmenu"><?php echo $msg->read($CONFIG_LIST); ?></label>
				<select class="form-control" id="IDsubmenu" name="IDsubmenu" onchange="document.forms.item_form.submit()">
					<?php if ($IDsubmenu == 0) echo '<option value="0">&nbsp;</option>'; ?>
					<?php
					// recherche des sous menu
					switch ($menu[1]) {
						case "campus_menu": $query  = "select _IDmenu, _ident from campus_menu where _lang = '".$_SESSION["lang"]."' order by _ident asc"; break;
						case "egroup_menu": $query  = "select _IDmenu, _ident from egroup_menu where _lang = '".$_SESSION["lang"]."' AND _IDitem = '3' order by _ident asc"; break;
						default:
							$query  = "select _IDsubmenu, config_submenu._ident from config_submenu ";
							$query .= "where _IDmenu = '$IDmenu' ";
							$query .= "AND _lang = '".$_SESSION["lang"]."' ";
							$query .= ( $IDsubmenu < 0 ) ? "AND _IDsubmenu = abs($IDsubmenu) " : "" ;
							$query .= "order by _ident asc";
							break;
						}
					$result = mysqli_query($mysql_link, $query);
					while ($mymenu = mysqli_fetch_row($result)) {
						switch ( $menu[1] ) {
							case "campus_menu":
							case "egroup_menu":
								if ($IDitem == $mymenu[0]) $selected = 'selected'; else $selected = '';
								break;
							default:
								if (abs($IDsubmenu) == $mymenu[0] || $IDitem == $mymenu[0]) $selected = 'selected'; else $selected = '';
								break;
							}
							echo '<option value="'.$mymenu[0].'" '.$selected.'>'.$msg->getTrad($mymenu[1]).'</option>';
						}
						?>
				</select>
			</div>
		</form>

		<hr>

		<form id="formulaire" action="index.php" method="post">
			<?php
				$hidden_form_items = array('item', 'cmde', 'IDconf', 'IDmenu', 'IDsubmenu', 'IDitem');
				foreach ($hidden_form_items as $value) if (isset($$value)) echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
			?>
			<input type="hidden" name="valid" value="<?php if ((isset($IDitem) && $IDitem) OR $IDsubmenu > 0) echo 'update'; else echo 'valider'; ?>">
			<!-- Intitulé -->
			<div class="form-group">
				<label for="title"><?php echo $msg->read($CONFIG_IDENT); ?></label>
				<input type="text" class="form-control <?php if ($error AND !strlen($title)) echo 'is-invalid'; ?>" id="title" name="title" size="50" value="<?php echo $title; ?>" required>
				<div id="titleFeedback" class="invalid-feedback"><?php echo $msg->read($CONFIG_ERRIDENT); ?></div>
			</div>



			<!-- URL -->
			<div class="form-group">
				<label for="url"><?php echo $msg->read($CONFIG_URL); ?></label>
				<input type="text" class="form-control <?php if ($error AND !strlen($url) AND $isurl == "O" && false) echo 'is-invalid'; ?>" id="url" name="url" value="<?php echo $url; ?>">
				<div id="urlFeedback" class="invalid-feedback"><?php echo $msg->read($CONFIG_ERRURL); ?></div>
			</div>
			<input type="hidden" name="isurl" value='O'>



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
			<button type="submit" class="btn btn-success"><?php if ($IDmenu) echo $msg->read($CONFIG_UPDTMENU); else echo $msg->read($CONFIG_ADDMENU); ?></button>

		</form>


  </div>
</div>




<!-- <div class="maincontent">

	<form id="formulaire" action="index.php" method="post" enctype="multipart/form-data">


	  <table class="width100">
	   <tr>
	     <td style="width:10%;"></td>
	     <td style="width:80%;">

		  <table class="width100">


		<?php
			if ( $menu[1] == "config_submenu" ) {
				if ( strlen(@$image) ) {
					$img  = "<a href=\"".myurlencode("index.php?item=$item&cmde=$cmde&IDconf=$IDconf&IDmenu=$IDmenu&IDsubmenu=$IDsubmenu&delimg=$image")."\">";
					$img .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"".$msg->read($CONFIG_DELETE)."\" alt=\"".$msg->read($CONFIG_DELETE)."\" />";
					$img .= "</a>";
					$img .= " ";
					$img .= "<a href=\"$DOWNLOAD/menu/$image\" onclick=\"window.open(this.href, '_blank'); return false;\">$image</a><br/>";
					}
				else
					$img  = "";

				print("
		  		    <tr style=\"background-color:#eeeeee;\">
				      <td style=\"width:100%;\" colspan=\"2\">
						". $msg->read($CONFIG_LOADIMAGE) ."
					</td>
				    </tr>

				    <tr>
				      <td colspan=\"2\">
			      		<p class=\"hidden\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$FILESIZE\" /></p>
						$img
						<input type=\"file\" name=\"UploadedFile\" size=\"50\" />
				      </td>
				    </tr>");
				}
		?>

  		    <tr style="background-color:#eeeeee;">
		      <td><?php print($msg->read($CONFIG_ACCESS)); ?></td>
		      <td><?php print($msg->read($CONFIG_PERMS)); ?></td>
		    </tr>

		    <tr>
		      <td>
	      	<?php
				// recherche des groupes
				$query  = "select _IDgrp, _ident from user_group ";
				$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
				$query .= "order by _IDgrp asc";

				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row ) {
					$checked = ( $grprd & pow(2, $row[0] - 1) ) ? "checked=\"checked\"" : "" ;

	             		print("<label for=\"cbrd_$row[0]\"><input type=\"checkbox\" id=\"cbrd_$row[0]\" name=\"cbrd[]\" value=\"". pow(2, $row[0] - 1) ."\" $checked /></label> $row[1]<br/>");

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
             	?>
		      </td>
		      <td class="valign-top">
				<label for="visible"><input type="checkbox" id="visible" name="visible" value="O" <?php print($visible == "N" ? "checked=\"checked\"" : ""); ?> /></label>
				<?php print($msg->read($CONFIG_LINKCLOSE)); ?><br/>
				<label for="anonyme"><input type="checkbox" id="anonyme" name="anonyme" value="O" <?php print($anonyme == "O" ? "checked=\"checked\"" : ""); ?> /></label>
				<?php print($msg->read($CONFIG_ANONYMOUS)); ?>
			</td>
		    </tr>

		  </table>

	     </td>
	     <td style="width:10%;"></td>
	   </tr>
	  </table>

	<hr style="width:80%; text-align:center;" />

         <table class="width100">
           <tr class="valign-middle">
              <td style="width:10%;" class="align-center">
              	<?php print("<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/valid.gif\" name=\"valid\" alt=\"".$msg->read($CONFIG_INPUTOK)."\" />"); ?>
              </td>
              <td>
              	<?php print($IDsubmenu > 0 ? $msg->read($CONFIG_LINKUPDT) : $msg->read($CONFIG_ADDLINK)); ?>.
              </td>
           </tr>

           <tr class="valign-middle">
              <td class="align-center">
              	<?php print("<a href=\"index.php?item=$item&amp;IDconf=$IDconf&amp;IDmenu=$IDmenu&amp;cmde=menu\">"); ?>
              	<?php print("<img src=\"".$_SESSION["ROOTDIR"]."/images/lang/".$_SESSION["lang"]."/cancel.gif\" title=\"\" alt=\"".$msg->read($CONFIG_INPUTCANCEL)."\" />"); ?></a>
              </td>
              <td>
              	<?php print($msg->read($CONFIG_PREV)); ?>
              </td>
           </tr>
         </table>

	</form>

</div> -->
