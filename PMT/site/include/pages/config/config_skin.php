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
 *		module   : config_skin.php
 *		projet   : paramétrage de l'interface intranet
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 25/01/03
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */

// On vérifie que l'on soit bien un super-administrateur
if ($_SESSION['CnxAdm'] != 255) header('Location: index.php');

// $IDconf  = ( @$_POST["IDconf"] )	// ID de la configuration
// 	? (int) $_POST["IDconf"]
// 	: (int) @$_GET["IDconf"] ;

$IDtheme = (int) @$_POST["IDtheme"];
$rblang  = @$_POST["rblang"] ? $_POST["rblang"] : $_SESSION["lang"] ;

// POST
$temp = array('IDtheme', 'btnleft', 'bgcolor', 'page', 'logo1', 'logo2', 'submit', 'align');
foreach ($temp as $value) {
	if (isset($_POST[$value])) $$value = addslashes(tripslashes($_POST[$value]));
}

// POST puis GET
$temp = array('valid_x', 'IDconf');
foreach ($temp as $value) {
	if (isset($_POST[$value])) $$value = $_POST[$value];
	elseif (isset($_GET[$value])) $$value = $_GET[$value];
}




// POST avec addslashes et trim
$temp = array('login', 'nologin', 'ident', 'title', 'texte', 'adresse', 'cp', 'ville', 'tel', 'fax', 'web', 'email', 'puce', 'fond', 'header', 'tdcolor', 'bandeau', 'modetheme', 'admin');
foreach ($temp as $value) if (isset($_POST[$value])) $$value = addslashes(trim($_POST[$value]));

$align   = @$_POST["align"] ? $_POST["align"] : "C" ;


// Si on a pas de configuration sélectionnée alors on en sélectionne une de base
if (!isset($IDconf)) {
	$query = "SELECT _IDconf from config where _visible = 'O' LIMIT 1 ";
	$result  = mysqli_query($mysql_link, $query);
	$row  = mysqli_fetch_row($result);
	$IDconf = $row[0];
}

?>


<?php
	// l'utilisateur a validé
	if (isset($valid_x) && $valid_x != '') {
		switch ($valid_x) {
			case 'submitForm':
				// mysqli_query($mysql_link, "update config set _visible = 'N' where _lang = '$rblang'");

				if ($IDconf) {
					// mise à jour de la config
					$query  = "update config set ";
					$temp_query = '';
					$temp = array('IDtheme', 'texte', 'login', 'nologin', 'title', 'adresse', 'cp', 'ville', 'tel', 'fax', 'web', 'email', 'webmaster', 'ident');
					foreach ($temp as $value) if (isset($$value)) {
						if (isset($$value) && (($value == 'ident' && strlen($$value)) || $value != 'ident')) $temp_query .= ", _".$value." = '".addslashes(stripslashes($$value))."'";
					}
					$query .= substr($temp_query, 1);
					if (isset($logo1) && $logo1 == 'O') $query .= ', _logo1 = "O"'; else $query .= ', _logo1 = "N"';
					$query .= ", _lang = 'fr'";
					$query .= " where _IDconf = '$IDconf' ";
					$query .= "limit 1";
					mysqli_query($mysql_link, $query);

					$result = mysqli_query($mysql_link, "select _ident from config where _IDconf = '$IDconf' limit 1");
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					if (!is_dir('download/logos/'.getLogoDir($row[0]))) {
						mkdir('download/logos/'.getLogoDir($row[0]));
						fopen('download/logos/'.getLogoDir($row[0]).'/index.html', 'w');
						fopen('download/logos/'.getLogoDir($row[0]).'/theme_custom.css', 'w');
					}

					// fichiers à transférer
					if (isset($_FILES["UploadedFile_large"]["tmp_name"])) 			$file_large 			= $_FILES["UploadedFile_large"]["tmp_name"];
					if (isset($_FILES["UploadedFile_square"]["tmp_name"])) 			$file_square 			= $_FILES["UploadedFile_square"]["tmp_name"];
					if (isset($_FILES["UploadedFile_large_dark"]["tmp_name"])) 	$file_large_dark 	= $_FILES["UploadedFile_large_dark"]["tmp_name"];
					if (isset($_FILES["UploadedFile_square_dark"]["tmp_name"]))	$file_square_dark = $_FILES["UploadedFile_square_dark"]["tmp_name"];
					if (isset($_FILES["UploadedFile_signature"]["tmp_name"]))		$file_signature 	= $_FILES["UploadedFile_signature"]["tmp_name"];
					if (isset($_FILES["UploadedFile_favicon"]["tmp_name"]))			$file_favicon 		= $_FILES["UploadedFile_favicon"]["tmp_name"];

					if (isset($file_large) && $file_large) 							move_uploaded_file($file_large, 'download/logos/'.getLogoDir($row[0]).'/logo_large.png');
					if (isset($file_square) && $file_square) 						move_uploaded_file($file_square, 'download/logos/'.getLogoDir($row[0]).'/logo_square.png');
					if (isset($file_large_dark) && $file_large_dark) 		move_uploaded_file($file_large_dark, 'download/logos/'.getLogoDir($row[0]).'/logo_large_dark.png');
					if (isset($file_square_dark) && $file_square_dark) 	move_uploaded_file($file_square_dark, 'download/logos/'.getLogoDir($row[0]).'/logo_square_dark.png');
					if (isset($file_signature) && $file_signature) 			move_uploaded_file($file_signature, 'download/logos/'.getLogoDir($row[0]).'/signature.png');
					if (isset($file_favicon) && $file_favicon)		 			move_uploaded_file($file_favicon, 'download/logos/'.getLogoDir($row[0]).'/favicon.png');
				}
				else {
					// Création de la config
					if (strlen($ident)) {
						$values_list = $values_name = '';
						$temp = array('IDtheme', 'texte', 'login', 'nologin', 'title', 'adresse', 'cp', 'ville', 'tel', 'fax', 'web', 'email', 'webmaster', 'ident', 'lang');
						$lang = 'fr';
						foreach ($temp as $value) if (isset($$value)) {
							$values_name .= ', _'.$value.' ';
							$values_list .= ', "'.addslashes(stripslashes($$value)).'" ';
						}
						$query = 'INSERT INTO `config` (_IDconf'.$values_name.') VALUES (NULL'.$values_list.') ';

						if (mysqli_query($mysql_link, $query)) {
							$IDconf = ( $IDconf ) ? $IDconf : mysqli_insert_id($mysql_link) ;

							// sélection de la nouvelle config
							$result = mysqli_query($mysql_link, "select _ident from config where _IDconf = '$IDconf' limit 1");
							$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0;

							mkdir('download/logos/'.getLogoDir($row[0]));
							fopen('download/logos/'.getLogoDir($row[0]).'/index.html', 'w');
							fopen('download/logos/'.getLogoDir($row[0]).'/theme_custom.css', 'w');

							// fichiers à transférer
							if (isset($_FILES["UploadedFile_large"]["tmp_name"])) 			$file_large 			= $_FILES["UploadedFile_large"]["tmp_name"];
							if (isset($_FILES["UploadedFile_square"]["tmp_name"])) 			$file_square 			= $_FILES["UploadedFile_square"]["tmp_name"];
							if (isset($_FILES["UploadedFile_large_dark"]["tmp_name"])) 	$file_large_dark 	= $_FILES["UploadedFile_large_dark"]["tmp_name"];
							if (isset($_FILES["UploadedFile_square_dark"]["tmp_name"]))	$file_square_dark = $_FILES["UploadedFile_square_dark"]["tmp_name"];
							if (isset($_FILES["UploadedFile_signature"]["tmp_name"]))		$file_signature 	= $_FILES["UploadedFile_signature"]["tmp_name"];
							if (isset($_FILES["UploadedFile_favicon"]["tmp_name"]))			$file_favicon 		= $_FILES["UploadedFile_favicon"]["tmp_name"];

							if (isset($file_large) && $file_large) 							move_uploaded_file($file_large, 'download/logos/'.getLogoDir($row[0]).'/logo_large.png');
							if (isset($file_square) && $file_square) 						move_uploaded_file($file_square, 'download/logos/'.getLogoDir($row[0]).'/logo_square.png');
							if (isset($file_large_dark) && $file_large_dark) 		move_uploaded_file($file_large_dark, 'download/logos/'.getLogoDir($row[0]).'/logo_large_dark.png');
							if (isset($file_square_dark) && $file_square_dark) 	move_uploaded_file($file_square_dark, 'download/logos/'.getLogoDir($row[0]).'/logo_square_dark.png');
							if (isset($file_signature) && $file_signature) 			move_uploaded_file($file_signature, 'download/logos/'.getLogoDir($row[0]).'/signature.png');
							if (isset($file_favicon) && $file_favicon)		 			move_uploaded_file($file_favicon, 'download/logos/'.getLogoDir($row[0]).'/favicon.png');
						}
					}
				}
				break;

			case 'useasdefault':
				$query = "UPDATE config set _visible = 'N' WHERE 1 ";
				mysqli_query($mysql_link, $query);
				$query = 'UPDATE config set _visible = "O" WHERE _IDconf = "'.$IDconf.'" ';
				mysqli_query($mysql_link, $query);
				break;

			case 'removeconfig':
				$query = 'DELETE from config WHERE _IDconf = "'.$IDconf.'" ';
				mysqli_query($mysql_link, $query);
				echo '<meta http-equiv="refresh" content="0;URL=index.php?item='.$item.'&cmde='.$cmde.'">';
				break;

			default:
				// code...
				break;
		}
	}




	// On récupère les infos de la configuration sélectionnée
	if (isset($IDconf) && $IDconf != '') {
		$sql  = "select _adresse, _cp, _ville, _tel, _fax, _web, _email, _title, _texte, _login, _nologin, _webmaster from config where _IDconf = '$IDconf' limit 1 ";
		$res  = mysqli_query($mysql_link, $sql);
		$cfg  = mysqli_fetch_row($res);
		if ($cfg) {
			$temp = array('adresse', 'cp', 'ville', 'tel', 'fax', 'web', 'email', 'title', 'texte', 'login', 'nologin', 'admin');
			foreach ($temp as $key => $value) if (isset($cfg[$key])) $$value = $cfg[$key];
		}
	}
?>


<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?php echo $msg->read($CONFIG_TUNING); ?></h1>

<?php include("include/config_menu_top.php"); ?>

<div class="card shadow mb-4">
  <div class="card-body">
		<?php echo $alert->info('Modification', 'Si vous modifiez le nom de la configuration, vous devrez envoyer à nouveau les fichiers logos et signature !'); ?>
		<form id="formulaire" action="index.php?item=21&cmde=skin" method="post" enctype="multipart/form-data">
			<input type="hidden" name="mylang" value="<?php print("$rblang"); ?>">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $FILESIZE; ?>">
			<input type="hidden" name="ident" value="<?php if (isset($ident)) echo stripslashes($ident); ?>">

			<!-- Choix de la configuration -->
			<?php
			switch ( @$_GET["submit"] ) {
				case "update" :
					// recherche de la config
					$sql  = "select _adresse, _cp, _ville, _tel, _fax, _web, _email, _title, _texte, _login, _nologin, _webmaster, _ident from config where _IDconf = '$IDconf' limit 1 ";
					$res  = mysqli_query($mysql_link, $sql);
					$cfg  = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;
					if ($cfg) {
						$temp = array('adresse', 'cp', 'ville', 'tel', 'fax', 'web', 'email', 'title', 'texte', 'login', 'nologin', 'admin');
						foreach ($temp as $key => $value) if (isset($cfg[$key])) $$value = $cfg[$key];
					}
					// Pas de break ici car si on est dans le cas de 'new', alors on veux aussi exécuter le code au dessus!

				case "new" :
					if (isset($cfg[12])) $ident = stripslashes($cfg[12]); else $ident = '';
					echo '<div class="form-group">';
						echo '<label for="ident">'.$msg->read($CONFIG_CONFIGNAME).'</label>';
						echo '<input type="text" class="form-control" id="ident" name="ident" value="'.$ident.'" size="20">';
					echo '</div>';
					break;

				default :
					// affichage des config
					$query  = "select _IDconf, _ident, _visible from config where _ident != '' ";
					$result = mysqli_query($mysql_link, $query);
					// initialiation
					$IDconf = (@$_POST["mylang"] AND @$_POST["mylang"] != $rblang) ? $row[0] : $IDconf ;

					echo '<div class="form-group">';
						echo '<label for="IDconf">'.$msg->read($CONFIG_CHOOSECONFIG).'</label>';
						echo '<select class="form-control custom-select" id="IDconf" name="IDconf" onchange="changeSelectedConfig()">';

							while ($row = mysqli_fetch_row($result)) {
								if ($row[2] == 'O') $check = '&nbsp;('.$msg->read($CONFIG_CURRENTCONFIG).')'; else $check = '';
								if ($IDconf == $row[0]) $select = 'selected'; else $select = '';
								if ($IDconf == $row[0]) {
									// recherche de la config
									$sql  = "select _adresse, _cp, _ville, _tel, _fax, _web, _email, _title, _texte, _login, _nologin, _webmaster from config where _IDconf = '$IDconf' limit 1 ";
									$res  = mysqli_query($mysql_link, $sql);
									$cfg  = ( $res ) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0 ;
									if ($cfg) {
										$temp = array('adresse', 'cp', 'ville', 'tel', 'fax', 'web', 'email', 'title', 'texte', 'login', 'nologin', 'admin');
										foreach ($temp as $key => $value) if (isset($cfg[$key])) $$value = $cfg[$key];
									}
								}
								echo '<option value="'.$row[0].'" '.$select.'>'.stripslashes($row[1]).' '.$check.'</option>';
							}
						echo '</select>';
					echo '</div>';
					break;
			}
			?>
			<a href="<?php echo myurlencode('index.php?item='.$item.'&cmde=skin&submit=new'); ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>&nbsp;<?php echo $msg->read($CONFIG_NEWCONFIG); ?></a>
			<a href="<?php echo myurlencode('index.php?item='.$item.'&cmde=skin&IDconf='.$IDconf.'&submit=update'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;<?php echo $msg->read($CONFIG_RENAMECONFIG); ?></a>
			<a href="<?php echo myurlencode('index.php?item='.$item.'&cmde=skin&IDconf='.$IDconf.'&valid_x=useasdefault'); ?>" class="btn btn-warning btn-sm"><i class="fas fa-cog"></i>&nbsp;<?php echo $msg->read($CONFIG_USEASDEFAULT); ?></a>
			<a href="<?php echo myurlencode('index.php?item='.$item.'&cmde=skin&IDconf='.$IDconf.'&valid_x=removeconfig'); ?>" onclick="return confirm('<?php echo $msg->read($CONFIG_SUREQUESTION); ?>')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i>&nbsp;<?php echo $msg->read($CONFIG_REMOVECONFIG); ?></a>
			<a href="<?php echo myurlencode('index.php?item='.$item.'&cmde=skin_css&id='.$IDconf); ?>" class="btn btn-info btn-sm"><i class="fas fa-code"></i>&nbsp;Customiser les css</a>

			<hr>



			<!-- Titre des fenêtres -->
			<div class="form-group">
				<label for="title"><?php echo $msg->read($CONFIG_WINTITLE); ?></label>
				<input type="text" class="form-control" id="title" name="title" value="<?php if (isset($title)) echo stripslashes($title); ?>">
			</div>

			<!-- Message d'accueil -->
			<div class="form-group">
				<label for="texte"><?php echo $msg->read($CONFIG_HOMEMSG); ?></label>
				<input type="text" class="form-control" id="texte" name="texte" value="<?php if (isset($texte)) echo stripslashes($texte); ?>">
			</div>

			<!-- Message à la connexion -->
			<div class="form-group">
				<label for="login"><?php echo $msg->read($CONFIG_CONNECTION); ?></label>
				<textarea class="form-control" id="login" name="login" rows="2"><?php if (isset($login)) echo stripslashes($login); ?></textarea>
			</div>

			<!-- Message de maintenance -->
			<div class="form-group">
				<label for="nologin"><?php echo $msg->read($CONFIG_MAINTENANCE); ?></label>
				<textarea class="form-control" id="nologin" name="nologin" rows="2"><?php if (isset($nologin)) echo stripslashes($nologin); ?></textarea>
			</div>

			<!-- Nom de l'administrateur du site -->
			<div class="form-group">
				<label for="admin"><?php echo $msg->read($CONFIG_WEBMASTER); ?></label>
				<input type="text" class="form-control" id="admin" name="admin" value="<?php if (isset($admin)) echo stripslashes($admin); ?>">
			</div>

			<!-- Adresse -->
			<div class="form-group">
				<label for="adresse"><?php echo $msg->read($CONFIG_MYADDRESS); ?></label>
				<input type="text" class="form-control" id="adresse" name="adresse" value="<?php if (isset($adresse)) echo stripslashes($adresse); ?>">
			</div>

			<!-- Code postal et ville -->
			<div class="form-row">
				<div class="form-group col-md-2">
					<label for="cp"><?php echo $msg->read($CONFIG_ZIPCODE); ?></label>
					<input type="text" class="form-control" id="cp" name="cp" value="<?php if (isset($cp)) echo stripslashes($cp); ?>" size="6">
				</div>
				<div class="form-group col-md-10">
					<label for="ville"><?php echo $msg->read($CONFIG_CITY); ?></label>
					<input type="text" class="form-control" id="ville" name="ville" value="<?php if (isset($ville)) echo stripslashes($ville); ?>">
				</div>
			</div>

			<!-- Numéro de téléphone -->
			<div class="form-group">
				<label for="tel"><?php echo $msg->read($CONFIG_TEL); ?></label>
				<input type="text" class="form-control" id="tel" name="tel" value="<?php if (isset($tel)) echo stripslashes($tel); ?>">
			</div>

			<!-- Numéro de fax -->
			<div class="form-group">
				<label for="fax"><?php echo $msg->read($CONFIG_FAX); ?></label>
				<input type="text" class="form-control" id="fax" name="fax" value="<?php if (isset($fax)) echo stripslashes($fax); ?>">
			</div>

			<!-- Site web -->
			<div class="form-group">
				<label for="web"><?php echo $msg->read($CONFIG_WEBSITE); ?></label>
				<input type="text" class="form-control" id="web" name="web" value="<?php if (isset($web)) echo stripslashes($web); ?>">
			</div>

			<!-- Email de l'administrateur -->
			<div class="form-group">
				<label for="email"><?php echo $msg->read($CONFIG_EMAIL); ?></label>
				<input type="email" class="form-control" id="email" name="email" value="<?php if (isset($email)) echo stripslashes($email); ?>">
			</div>

			<!-- Logo principal large -->
			<div class="form-group">
				<label for="exampleInputEmail1"><?php echo $msg->read($CONFIG_LOGOLARGE); ?></label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="UploadedFile_large" name="UploadedFile_large">
					<label class="custom-file-label" for="UploadedFile_large" data-browse="<?php echo $msg->read($CONFIG_BROWSE); ?>"><?php echo $msg->read($CONFIG_CHOOSEFILE); ?></label>
				</div>
			</div>

			<!-- Logo principal carré -->
			<div class="form-group">
				<label for="exampleInputEmail1"><?php echo $msg->read($CONFIG_LOGOSQUARE); ?></label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="UploadedFile_square" name="UploadedFile_square">
					<label class="custom-file-label" for="UploadedFile_square" data-browse="<?php echo $msg->read($CONFIG_BROWSE); ?>"><?php echo $msg->read($CONFIG_CHOOSEFILE); ?></label>
				</div>
			</div>

			<!-- Logo principal large sombre -->
			<div class="form-group">
				<label for="exampleInputEmail1"><?php echo $msg->read($CONFIG_LOGOLARGEDARK); ?></label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="UploadedFile_large_dark" name="UploadedFile_large_dark">
					<label class="custom-file-label" for="UploadedFile_large_dark" data-browse="<?php echo $msg->read($CONFIG_BROWSE); ?>"><?php echo $msg->read($CONFIG_CHOOSEFILE); ?></label>
				</div>
			</div>

			<!-- Logo principal carré sombre -->
			<div class="form-group">
				<label for="exampleInputEmail1"><?php echo $msg->read($CONFIG_LOGOSQUAREDARK); ?></label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="UploadedFile_square_dark" name="UploadedFile_square_dark">
					<label class="custom-file-label" for="UploadedFile_square_dark" data-browse="<?php echo $msg->read($CONFIG_BROWSE); ?>"><?php echo $msg->read($CONFIG_CHOOSEFILE); ?></label>
				</div>
			</div>

			<!-- Image de signature -->
			<div class="form-group">
				<label for="exampleInputEmail1"><?php echo $msg->read($CONFIG_SIGNATURE); ?></label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="UploadedFile_signature" name="UploadedFile_signature">
					<label class="custom-file-label" for="UploadedFile_signature" data-browse="<?php echo $msg->read($CONFIG_BROWSE); ?>"><?php echo $msg->read($CONFIG_CHOOSEFILE); ?></label>
				</div>
			</div>

			<!-- Favicon -->
			<div class="form-group">
				<label for="exampleInputEmail1"><?php echo $msg->read($CONFIG_FAVICON); ?></label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="UploadedFile_favicon" name="UploadedFile_favicon">
					<label class="custom-file-label" for="UploadedFile_favicon" data-browse="<?php echo $msg->read($CONFIG_BROWSE); ?>"><?php echo $msg->read($CONFIG_CHOOSEFILE); ?></label>
					<small id="emailHelp" class="form-text text-muted"><?php echo $msg->read($CONFIG_FAVICON_HELP); ?></small>
				</div>
			</div>
			<script>
				// Lorsque l'on upload un fichier, on affiche le nom du fichier dans le champ
				$('#UploadedFile_large').on('change',function(){
					var fileName = $(this).val();
					var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
					$(this).next('.custom-file-label').html(cleanFileName);
				})
				$('#UploadedFile_square').on('change',function(){
					var fileName = $(this).val();
					var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
					$(this).next('.custom-file-label').html(cleanFileName);
				})
				$('#UploadedFile_large_dark').on('change',function(){
					var fileName = $(this).val();
					var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
					$(this).next('.custom-file-label').html(cleanFileName);
				})
				$('#UploadedFile_square_dark').on('change',function(){
					var fileName = $(this).val();
					var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
					$(this).next('.custom-file-label').html(cleanFileName);
				})
				$('#UploadedFile_signature').on('change',function(){
					var fileName = $(this).val();
					var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
					$(this).next('.custom-file-label').html(cleanFileName);
				})
				$('#UploadedFile_favicon').on('change',function(){
					var fileName = $(this).val();
					var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
					$(this).next('.custom-file-label').html(cleanFileName);
				})
			</script>

			<input type="hidden" name="valid_x" value=''>
			<!-- Boutons valider et retour à l'accueil -->
			<button type="button" onclick="submitForm()" class="btn btn-success"><?php echo $msg->read($CONFIG_INPUTOK); ?></button>
			<a href="index.php" class="btn btn-danger"><?php echo $msg->read($CONFIG_INPUTCANCEL); ?></a>
		</form>
  </div>
</div>


<script>
function submitForm() {
	$('input[name="valid_x"]').val('submitForm');
	$('#formulaire').submit();
}

function changeSelectedConfig() {
	var IDconf = $('#IDconf').val();
	window.location.href = 'index.php?item=<?php echo $item; ?>&cmde=<?php echo $cmde; ?>&IDconf=' + IDconf;
}

</script>
