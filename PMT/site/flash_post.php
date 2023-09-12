<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
   Copyright (c) 2006 by Hugues Lecocq(hugues.lecocq@laposte.net)
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
 *		module   : flash_post.php
 *		projet   : la page de saisie des flash infos
 *
 *		version  : 1.2
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 28/05/03 - par D. Laporte
 *                     mise en place des smileys
 *
 *		           7/03/04 - par D. Laporte
 *                     mise en place de l'éditeur HTML
 *
 *                     15/06/06 - par hugues lecocq
 *                     migration PHP5
 *
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *					 		 11/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7
 */


$IDflash = ( @$_POST["IDflash"] )		// identifiant des flash infos
	? (int) $_POST["IDflash"]
	: (int) @$_GET["IDflash"] ;
$IDinfos = ( @$_POST["IDinfos"] )		// identifiant des rubriques des publications par internet
	? (int) $_POST["IDinfos"]
	: (int) @$_GET["IDinfos"] ;
$IDitem = ( @$_POST["IDitem"] )		// Identifiant d'un article
	? (int) $_POST["IDitem"]
	: (int) @$_GET["IDitem"] ;
$edit = ( strlen(@$_POST["edit"]) )		// mode d'édition : basique ou avancé
	? (int) $_POST["edit"]
	: (int) (strlen(@$_GET["edit"]) ? $_GET["edit"] : $WYSIWYG) ;
$titre = ( @$_POST["titre"] )			// titre de l'item flash_data
	? $_POST["titre"]
	: @$_GET["titre"] ;
$article = ( @$_POST["article"] )		// titre de l'annonce
	? $_POST["article"]
	: @$_GET["article"] ;
$texte_ckeditor = ( @$_POST["texte_ckeditor"] )			// texte de l'annonce
	? stripslashes($_POST["texte_ckeditor"])
	: @$_GET["texte_ckeditor"] ;
$submit = ( @$_POST["submit"] )		// bouton de validation
	? $_POST["submit"]
	: @$_GET["submit"] ;
?>

<?php
//---------------------------------------------------------------------------
function attachment($IDitem)
{
/*
 * fonction :	enregistrement des PJ
 * in :		$IDitem : ID de l'article
 * out :		tableau du path des différentes PJ
 */

	require "globals.php";

	// transfert d'une Pièce Jointe
	$file = @$_FILES["UploadPJ"]["tmp_name"];
	$pj   = Array();

	if(@$_FILES["UploadPJ"]["tmp_name"])
	{
		if (!file_exists($DOWNLOAD.'/flash/')) {
			mkdir($DOWNLOAD.'/flash/');
			fopen($DOWNLOAD.'/flash/index.php', "w");
		}
		for ($j = 0; $j < count($file); $j++)
			if ($_FILES["UploadPJ"]["name"][$j]) {
				// extension du fichier en PJ
				$ext    = extension(@$_FILES["UploadPJ"]["name"][$j]);
				$pjdesc = addslashes(trim(@$_POST["PJdesc"][$j]));

				$Query  = "insert into flash_pj ";
				$Query .= "values(NULL, '$IDitem', '".@$_FILES["UploadPJ"]["name"][$j]."', '$pjdesc', '$ext', '".@$_FILES["UploadPJ"]["size"][$j]."', '', 'O', 'O')";

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				else {
					// fichier destination
					$dest   = "$DOWNLOAD/flash/". mysqli_insert_id($mysql_link) .".$ext";
					$pj[$j] = $dest;

					// copie du fichier temporaire -> répertoire de stockage
					if ( move_uploaded_file($file[$j], $dest) )
						mychmod($dest, $CHMOD);
					}
				}
	}

	return $pj;
}
//---------------------------------------------------------------------------
function removePJ($IDinfos, $IDpj = 0)
{
/*
 * fonction :	supprime les PJ d'un article
 * in :		$IDitem, id de l'article
 * in :		$IDpj, id de la pièce jointe
 * out :		1 si requête correcte, 0 sinon
 */
	require "globals.php";

	$query  = "select _IDitem from flash_items where _IDinfos = '".$IDinfos."' LIMIT 1 ";
	$result  = mysqli_query($mysql_link, $query);
	$IDflash = mysqli_fetch_row($result)[0];

	$query  = "select _IDpj, _ext from flash_pj where _IDitem = '$IDflash' ";
	if ($IDpj) $query .= "AND _IDpj = '".$IDpj."' ";
	$query .= "order by _IDpj";
	$result  = mysqli_query($mysql_link, $query);
	while ($row = mysqli_fetch_row($result)) {
		if (file_exists($DOWNLOAD.'/flash/'.$row[0].'.'.$row[1])) unlink($DOWNLOAD.'/flash/'.$row[0].'.'.$row[1]);
	}

	$query  = "delete from flash_pj where _IDitem = '$IDflash' ";
	if ($IDpj) $query .= "AND _IDpj = '".$IDpj."' ";

	return mysqli_query($mysql_link, $query);
}
//---------------------------------------------------------------------------


// Si on supprime une PJ:
if (isset($_GET['removepj'])) removePJ($IDinfos, $_GET['removepj']);



	// lecture de la base de données des flash info
	$query  = "select _title, _IDmod, _PJ, _rss, _IDgrpwr from flash ";
	$query .= "where _IDflash = '$IDflash' AND _visible = 'O' ";
	$query .= "limit 1";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$title  = $row[0];
	$is_pj  = $row[2];
	$is_rss = $row[3];

	$modo   = (bool) ( $row[1] == -1 AND ($_SESSION["CnxAdm"] & 4) AND ($row[4] & pow(2, $_SESSION["CnxGrp"] - 1)) );

	// si pas d'autorisation => bye, bye
	if ( $_SESSION["CnxAdm"] != 255 AND $_SESSION["CnxID"] != $row[1] AND $modo == false )
		logadmSessionAccess();

	// initialisation du statut et des erreurs
	$status = $Query = "";
	$error1 = $error2 = $error3 = "";

	$titre   = addslashes(trim($titre));
	$article = addslashes(trim($article));
	$texte_ckeditor   = addslashes(trim($texte_ckeditor));

	if ( !isset($IDinfos) ) {
		// sélection de la rubrique
		$Query   = "select _IDinfos from flash_data ";
		$Query  .= "where _IDflash = '$IDflash' ";
		$Query  .= "order by _IDinfos desc ";
		$Query  .= "limit 1";

		$result  = mysqli_query($mysql_link, $Query);
		$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$IDinfos = $row[0];
		}

	// l'utilisateur a cliqué sur un lien
	switch ( $submit ) {
		case "NewRub" :	// ajout d'une nouvelle rubrique
			// initialisation des contrôles
			$IDinfos = 0;

			// et on enchaîne...

		case "NewItem" :	// ajout d'un nouvel article dans une rubrique
			$IDitem  = -1;
			break;

		case "ShowRub" :		// validation d'une rubrique
			$Query  = "UPDATE flash_data ";
			$Query .= ( @$_GET["show"] == "N" ) ? "SET _visible = 'O' " : "SET _visible = 'N' " ;
			$Query .= "where _IDinfos = '$IDinfos' ";
			$Query .= "limit 1";

			if ( !mysqli_query($mysql_link, $Query) )
				sql_error($mysql_link);
			break;

		case "ShowItem" :		// validation d'un article
			$Query  = "UPDATE flash_items ";
			$Query .= ( @$_GET["show"] == "N" ) ? "SET _visible = 'O' " : "SET _visible = 'N' " ;
			$Query .= "where _IDitem = '$IDitem' ";
			$Query .= "limit 1";

			if ( !mysqli_query($mysql_link, $Query) )
				sql_error($mysql_link);
			break;

		case "DelRub" :
			$status = $msg->read($FLASH_ERASE) ." ";

			$Query  = "select _IDitem from flash_items ";
			$Query .= "where _IDinfos = '$IDinfos' ";
			$Query .= "order by _IDitem";

			$result = mysqli_query($mysql_link, $Query);
			$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

			while ($row = mysqli_fetch_row($return)) {
				// suppression des PJ
				removePJ($row[0]);
			}

			$Query  = "delete from flash_items ";
			$Query .= "where _IDinfos = '$IDinfos' ";

			if ( mysqli_query($mysql_link, $Query) ) {
				$Query  = "delete from flash_data ";
				$Query .= "where _IDinfos = '$IDinfos' ";
				$Query .= "limit 1";

				if ( mysqli_query($mysql_link, $Query) ) {
					$IDinfos = 0;
					$IDitem  = -1;
					}
				}
			break;

		case "DelItem" :
			$status = $msg->read($FLASH_ERASE) ." ";

			// suppression des PJ
			removePJ($IDitem);

			$Query  = "DELETE from flash_items ";
			$Query .= "where _IDitem = '$IDitem' ";
			$Query .= "limit 1";

			if ( !mysqli_query($mysql_link, $Query) ) {
				$status .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />";
				sql_error($mysql_link);
				}
			else {
				$status .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />";
				$IDitem  = 0;
				}
			break;

		case "Modifier" :		// modification
		case "UpdItem" :		// modification du titre de l'article
			// permet de modifier l'appartion de l'ordre chrono des annonces
			$Query  = "UPDATE flash_data ";
			$Query .= "SET _modif = '". date("Y-m-d H:i:s", time()) ."' ";
			$Query .= "where _IDinfos = '$IDinfos' ";
			$Query .= "limit 1";

			if ( !mysqli_query($mysql_link, $Query) )
				sql_error($mysql_link);
			break;

		default :
			break;
		}

	// saisie d'un article
	if ( @$_POST["valid_x"] ) {
		// on récupère la date pour maj
		$date = date("Y-m-d H:i:s", time());

		// si c'est une nouvelle rubrique => on enregistre
		if ( !$IDinfos ) {
			// vérification de la saisie
			if ( !strlen($titre) )
				$error1 = "<span style=\"color:#FF0000\">". $msg->read($FLASH_ERRTITLE) ."</span>";

			// insertion d'une nouvelle rubrique dans la base de données
			if ( $error1 == "" ) {
				$Query  = "INSERT INTO flash_data ";
				$Query .= "VALUES('', '$IDflash', '$date', '$date', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$titre', '', '', '', '', 'O', '0', 'O') ";

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				else
					$IDinfos = mysqli_insert_id($mysql_link);
				}
			}
		// sinon on effectue la mise à jour
		else
			if ( strlen($titre) ) {
				$Query  = "update flash_data ";
				$Query .= "set _title = '$titre', ";
				$Query .= "_modif = '". date("Y-m-d H:i:s", time()) ."', ";
				$Query .= "_ID = '".$_SESSION["CnxID"]."', ";
				$Query .= "_IP = '".$_SESSION["CnxIP"]."' ";
				$Query .= "where _IDinfos = '$IDinfos' ";
				$Query .= "limit 1";

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				}

		// si c'est un nouvel article => on enregistre
		if ( !$IDitem ) {
			$status = $msg->read($FLASH_INSERT) ." ";

			// vérification de la saisie
			if ( !strlen($article) )
				$error2 = "<span style=\"color:#FF0000\">". $msg->read($FLASH_ERRIDENT) ."</span>";
			if ( !strlen($texte_ckeditor) )
				$error3 = "<span style=\"color:#FF0000\">". $msg->read($FLASH_ERRTEXT) ."</span>";

			// insertion d'un nouvel article dans la base de données
			if ( $IDinfos AND $error1 == "" AND $error2 == "" AND $error3 == "" ) {
				// mode SPIP ou WYSIWYG
				$raw    = ( $edit ) ? "N" : "O" ;

				$Query  = "INSERT INTO flash_items ";
				$Query .= "VALUES ('', '$IDinfos', '$date', '$date', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$article', '$texte_ckeditor', '$raw', '', '', '', '', '', '0', 'O') ";

				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
				else {
					$IDitem = mysqli_insert_id($mysql_link);

					mysqli_query($mysql_link, "update flash_items set _order = '$IDitem' limit 1");

					// transfert d'une Pièce Jointe
					attachment($IDitem);
					}

				// alimentation flux rss
				if ( $is_rss == "O" ) {
					$query  = "insert into rss_items ";
					$query .= "values('', '0', '$title, $article', '', '$texte_ckeditor', '".addslashes(getUserNameByID($_SESSION["CnxID"], false))."', '".addslashes($msg->read($FLASH_SUBMIT))."', '".date("Y-m-d H:i:s", time())."', '".$_SESSION["lang"]."')";

					mysqli_query($mysql_link, $query);
					}
				}
			}
		// sinon on effectue la mise à jour
		else {
			$status = $msg->read($FLASH_MODIFY) ." ";

			// seuls les propriétaires peuvent modifier leurs articles
			$Query  = "UPDATE flash_items ";
			$Query .= "SET _modif = '$date', ";
			$Query .= "_ID = '".$_SESSION["CnxID"]."', ";
			$Query .= "_IP = '".getUserIP()."' ";
			$Query .= ( strlen($article) ) ? ", _title = '$article' " : " " ;
			$Query .= ( strlen($texte_ckeditor) )   ? ", _texte = '$texte_ckeditor' "   : " " ;
			$Query .= "where _IDitem = '$IDitem' ";
			$Query .= "limit 1" ;
// echo $Query;
			if ( !mysqli_query($mysql_link, $Query) )
				sql_error($mysql_link);
			else
				// transfert d'une Pièce Jointe
				attachment($IDitem);
			}

		// icone du statut
		$status .= ( $IDinfos AND $error1 == "" AND $error2 == "" AND $error3 == "" )
			? "<img src=\"".$_SESSION["ROOTDIR"]."/images/ok.gif\" title=\"\" alt=\"\" />"
			: "<img src=\"".$_SESSION["ROOTDIR"]."/images/bad.gif\" title=\"\" alt=\"\" />" ;

		// on revient sur l'article en cours
//			$IDitem  = 0;
		}

	// nbr d'articles dans la rubrique
	if (!strlen($status)) {
		$Query   = "select _IDitem from flash_items ";
		$Query  .= "where _IDinfos = '$IDinfos' ";

		$result  = mysqli_query($mysql_link, $Query);
		$nbrec   = ( $result ) ? mysqli_num_rows($result) : 0 ;

		// initialisation des contrôles
		$msg->isPlural = (bool) ($nbrec > 1);
		$status  = $msg->read($FLASH_NBTEXT, strval($nbrec));
	}

	// sélection de la rubrique
	$Query   = "select _title, _align, _color, _visible from flash_data ";
	$Query  .= "where _IDinfos = '$IDinfos' ";
	$Query  .= "limit 1" ;

	$result  = mysqli_query($mysql_link, $Query);
	$myrow   = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$titre   = $myrow[0];
	$align   = ( $myrow[1] ) ? $myrow[1] : "G" ;
	$color   = ( $myrow[2] ) ? $myrow[2] : "#FFFFFF" ;

	// sélection de l'article
	$Query   = "select _title, _texte, _color, _IDitem, _ID, _visible, _raw from flash_items ";
	$Query  .= ( $IDitem ) ? "where _IDitem = '$IDitem' " : "where _IDinfos = '$IDinfos' " ;
	$Query  .= ( $IDitem ) ? "" : "order by _IDitem asc " ;
	$Query  .= "limit 1" ;

	$result  = mysqli_query($mysql_link, $Query);
	$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$article = $row[0];
	$texte_ckeditor   = $row[1];
	$color2  = ( $row[2] ) ? $row[2] : "#FFFFFF" ;
	$IDitem  = $row[3];
	$IDmod   = $row[4];
	$visible = $row[5];

	$raw     = $row[6];
?>




<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($FLASH_SUBMIT, $title); ?> <small class="text-muted"><?php print($msg->read($FLASH_FORMPOST)); ?></small></h1>


<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php print($msg->read($FLASH_AUTHOR)." ".$_SESSION["CnxName"]); ?> - <?php print($msg->read($FLASH_STATUS)." $status"); ?></h6>
  </div>
  <div class="card-body">

		<form id="formulaire" action="index.php" method="post" enctype="multipart/form-data">

			<?php
				foreach (array('item',  'cmde',  'IDflash',  'IDinfos',  'IDitem',  'edit') as $value)
					echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
			?>

			<div class="form-group">
				<label for="article"><?php print($msg->read($FLASH_FLASHTITLE, $error1)); ?></label>
				<input type="text" class="form-control" id="article" name="article" placeholder="" value="<?php echo stripslashes($article); ?>" required>
			</div>


			<div class="form-group">
				<label for="texte_ckeditor"><?php echo $msg->read($FLASH_TEXTANNOUNCE); ?></label>
				<textarea class="form-control" id="texte_ckeditor" name="texte_ckeditor" rows="3"><?php echo $texte_ckeditor; ?></textarea>
			</div>

			<script>
				$(document).ready(function(){
					CKEDITOR.replace( 'texte_ckeditor', {
						height: 500,
						allowedContent: true
					} );
				})
			</script>

			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $FILESIZE; ?>" />
			<div class="custom-file">
			  <input type="file" class="custom-file-input" name="UploadPJ[]" id="customFile">
			  <label class="custom-file-label" for="customFile" data-browse="Choisir">Ajouter une <?php echo strtolower($msg->read($FLASH_ATTACHMENT)); ?></label>
			</div>
			<?php
				// Pièce Jointe sur un message
				if ( $is_pj ) {
					// lecture des Pièces Jointes
					$result = mysqli_query($mysql_link, "select _IDpj, _ext, _size, _title from flash_pj where _IDitem = '$IDitem'");
					if ( mysqli_num_rows($result) ) {
						echo '<table style="width: 100%;" class="table table-stripped">';

						echo '<tr>';
							echo '<th colspan="2">';
								echo $msg->read($FLASH_ATTACHMENT);
							echo '</th>';
						echo '</tr>';

							while ( $row = mysqli_fetch_row($result) ) {
								echo '<tr>';
									echo '<td>';
										$path  = $_SESSION["ROOTDIR"]."/$DOWNLOAD/flash/$row[0].$row[1]";
										echo "<a href=\"".$path."\" target=\"_blank\"><i class=\"fa fa-paperclip\" style=\"font-size: 35px; margin: 10px;\"></i>&nbsp;".$row[3]."</a>";
									echo '</td>';
									echo '<td>';
										echo '<a href="'.myurlencode("index.php?item=".(@$item)."&cmde=post&IDinfos=".(@$IDinfos)."&IDflash=".(@$IDflash)."&IDsubmenu=".(@$idmenu)."&IDpj=-".(@$row[0])."&removepj=".(@$row[0])."&ext=".$row[1]).'"><i class="fa fa-trash" style="font-size: 25px; margin: 10px;"></i></a>';
									echo '</td>';
								echo '</tr>';
							}
						echo '</table>';
					}
				}
			?>
			<button type="submit" name="valid_x" value="1" class="btn btn-success mt-3">Enregistrer</button>

		</form>
  </div>
</div>
