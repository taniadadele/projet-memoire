<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2003-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
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
?>

<?php
/*
 *		module   : galery.php
 *		projet   : fonctions de manipulation d'images
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/09/03
 *		modif    : 17/07/06 - par Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


//---------------------------------------------------------------------------
function imageSize($file, &$srcWidth, &$srcHeight)
{
/*
 * fonction :	détermine la taille d'une image
 * in :		$file : path de l'image source
 *                $srcWidth : largeur vignette, $srcHeight : hauteur vignette
 */

	require_once $_SESSION["ROOTDIR"]."/include/filext.php";

	// recherche de la librairie GD
	$extension = get_loaded_extensions();
	if ( !in_array('gd', $extension) )
		return;

	// création de l'objet image à partir de l'extension
	$listimage = explode("|", $file);

	$path      = ( count($listimage) > 1 ) ? $listimage[0] : $file ;
	$file_ext  = ( count($listimage) > 1 ) ? $listimage[1] : $file ;

	switch ( strtolower(extension($file_ext)) ) {
		case "gif" :
			$srcImage = @imagecreatefromgif( $path );
			break;
		case "jpg" :
		case "jpeg" :
			$srcImage = @imagecreatefromjpeg( $path );
			break;
		default :
			$srcImage = @imagecreatefrompng( $path );
			break;
		}

	if ( $srcImage ) {
		// taille de l'image
		$srcWidth    = imagesx( $srcImage );
		$srcHeight   = imagesy( $srcImage );

		// libération de la mémoire
		imagedestroy( $srcImage );
		}
}
//---------------------------------------------------------------------------
function vignette($path, $dest, $file, &$srcWidth, &$srcHeight, $maxWidth = 0, $maxHeight = 0)
{
/*
 * fonction :	extraction de l'extension du nom de fichier
 * in :		$path : path de l'image source, $dest : répertoire des vignettes, $file : nom de fichier destination
 *                $srcWidth : largeur vignette, $srcHeight : hauteur vignette
 *                $maxWidth : largeur max vignette, $maxHeight : hauteur max vignette
 * out :		1 si vignette créée, 0 sinon
 */

	global	$MAXIMGWDTH;				// largeur max des vignettes (en pixels)
	global	$MAXIMGHGTH;				// hauteur max des vignettes (en pixels)

	require_once $_SESSION["ROOTDIR"]."/include/filext.php";
	require_once $_SESSION["ROOTDIR"]."/include/urlencode.php";

	require $_SESSION["ROOTDIR"]."/msg/vignette.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/vignette.php");

	$maxWidth  = ( $maxWidth ) ? $maxWidth : $MAXIMGWDTH ;
	$maxHeight = ( $maxHeight ) ? $maxHeight : $MAXIMGHGTH;

	// création de l'objet image à partir de l'extension
	$listimage = explode("|", $path);

	$path      = ( count($listimage) > 1 ) ? $listimage[0] : $path ;
	$file_ext  = extension(count($listimage) > 1 ? $listimage[1] : $file);

	switch ( strtolower($file_ext) ) {
		case "gif" :
//			$srcImage = @imagecreatefromgif( $path );
			$srcImage = imagecreatefromgif( $path );
			break;
		case "jpg" :
		case "jpeg" :
//			$srcImage = @imagecreatefromjpeg( $path );
			$srcImage = imagecreatefromjpeg( $path );
			break;
		default :
//			$srcImage = @imagecreatefrompng( $path );
			$srcImage = imagecreatefrompng( $path );
			break;
		}

	// copie des vignettes
	if ( $srcImage ) {
		// taille de l'image
		$srcWidth    = imagesx( $srcImage );
		$srcHeight   = imagesy( $srcImage );

		$ratioWidth  = $srcWidth  / $maxWidth;
		$ratioHeight = $srcHeight / $maxHeight;

		// taille maximale dépassée ?
		if ( $ratioWidth > 1 OR $ratioHeight > 1 ) {
			if( $ratioWidth < $ratioHeight ) {
				$destWidth  = (int) ($srcWidth / $ratioHeight);
				$destHeight = $maxHeight;
				}
			else {
				$destWidth  = $maxWidth;
				$destHeight = (int) ($srcHeight / $ratioWidth);
				}
			}
		else {
			$destWidth  = $srcWidth;
			$destHeight = $srcHeight;
			}

		// attention à la version de GD installée sur le serveur hébergeur
//		if ( $HTTP_POST_VARS['gd'] == 2 AND $file_ext != "gif" ) {
		if ( function_exists('imagecreatetruecolor') AND $file_ext != "gif" ) {
			// Partie 1 : GD 2.0 ou supérieur, résultat très bons
			$destImage = imagecreatetruecolor($destWidth, $destHeight);
			if ( !imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight) )
				print("<span style=\"color:#FF0000;\">". $msg->read($VIGNETTE_ERRIMAGE, Array($destWidth, $destHeight)) ."</span>");
			}
		else {
			// Partie 2 : GD inférieur à 2, résultat très moyens
			$destImage = imagecreate($destWidth, $destHeight);
			if ( !imagecopyresized($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight) )
				print("<span style=\"color:#FF0000;\">". $msg->read($VIGNETTE_ERRCOPY, Array($destWidth, $destHeight)) ."</span>");
			}

		// création et sauvegarde de l'image finale sous forme de vignette
		$dest_file = $dest . "/" . substr($file, 0, strrpos($file, ".")) . "." . extension($file);

		switch ( $file_ext ) {
			case "gif" :
				// toutes les fonctions GIF ont été supprimées de la bibliothèque GD version 1.6,
				$ret = ImageGIF($destImage, stripaccent($dest_file));
				break;
			case "jpg" :
			case "jpeg" :
				// Le support JPEG n'est disponible que si PHP est compilé avec GD-1.8 ou plus récent.
				$ret = ImageJPEG($destImage, stripaccent($dest_file));
				break;
			default :
				$ret = ImagePNG($destImage, stripaccent($dest_file));
				break;
			}

		// test de la création de l'image
		if ( !$ret )
			print("<span style=\"color:#FF0000;\">". $msg->read($VIGNETTE_ERRCREATE, $file_ext) ."</span>");

		// libération de la mémoire
		imagedestroy( $srcImage );
		imagedestroy( $destImage );

		//code retour
		return 1;
		}
	else
		print("<span style=\"color:#FF0000;\">". $msg->read($VIGNETTE_ERRFORMAT, $file_ext) ."</span>");

	//code retour
	return 0;
}
//---------------------------------------------------------------------------
function importGallery($IDdata, $src, $move = false, $maxsize = 0)
{
/*
 * fonction :	importation d'images dans une galerie
 * in :		$IDdata : ID de la galerie, $src : répertoire source
 */

	require $_SESSION["ROOTDIR"]."/globals.php";

	require_once $_SESSION["ROOTDIR"]."/include/filext.php";

	// la création de la DB peut prendre du temps => on supprime le tps max d'exécution des requêtes
	// attention : safe_mode doit être désactivé
	$safe_mode  = ini_get("safe_mode");
	$time_limit = ini_get("max_execution_time");

	if ( $safe_mode != "1" )
		set_time_limit(0);

	$dest  = $_SESSION["ROOTDIR"]."/$DOWNLOAD/galerie/$IDdata";
	$path  = ( strlen($src) )
		? substr(str_replace("\\", "/", $src), 0, strrpos(str_replace("\\", "/", $src), "/"))
		: "$DOWNLOAD/$IMGUPLOAD" ;

	$myDir = @opendir( $path );

	// lecture des répertoires
	while ( $entry = @readdir($myDir) ) {
		switch ( strtolower(extension($entry)) ) {
			case "jpeg" :
			case "jpg" :
			case "png" :
			case "gif" :
				// on détermine le répertoire de stockage des images
				if ( !is_dir($dest) )
					mymkdir($dest, $CHMOD);

				// on détermine le répertoire de stockage des vignettes
				$small = "$dest/vignettes";
				if ( !is_dir($small) )
					mymkdir($small, $CHMOD);

				// fichier destination
				$src  = "$path/$entry";
				$dst  = stripaccent(strtolower("$dest/$entry"));

				// on efface les fichiers existants pour éviter des conflits
				if ( file_exists($dst) )
					unlink($dst);

				// copie du fichier temporaire -> répertoire de stockage
				if ( $maxsize ) {
					imageSize($src, $srcWidth, $srcHeight);

					$ratio     = ( $srcWidth > $maxsize ) ? (float) ($maxsize / $srcWidth) : 1.0 ;
					$maxWidth  = (int) ($srcWidth * $ratio);
					$maxHeight = (int) ($srcHeight * $ratio);

					$isOk = vignette(
						$src,
						$dest,
						stripaccent(strtolower($entry)),
						$srcWidth,
						$srcHeight,
						$maxWidth,
						$maxHeight);
					}
				else
					$isOk = copy($src, $dst);

				if ( $isOk )
					if ( vignette($dst, $small, stripaccent(strtolower($entry)), $srcWidth, $srcHeight) ) {
						// initialisation des champs
						$date     = date("Y-m-d H:i:s");
						$filesize = filesize($src);

						// et on insére une nouvelle image dans la base de données
						$Query  = "insert into gallery_items ";
						$Query .= "values('', '$IDdata', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '$entry', '$filesize', '$srcWidth', '$srcHeight', '0', '', '', 'O')";

						if ( !mysqli_query($mysql_link, $Query) )
							sql_error($mysql_link);
						else
							if ( $move )
								unlink($src);
						}	// endif copy
				break;

			default :
				break;
			}	// endswitch
		}	// endwhile readdir

	// fermeture du répertoire
	@closedir($myDir);

	// réinitialisation du tps max d'exécution des requêtes
	if ( $safe_mode != "1" )
		set_time_limit($time_limit);
}
//---------------------------------------------------------------------------








// Redimenssionne une image (source: https://www.php.net/manual/fr/function.imagecopyresampled.php)
function image_resize($src, $dst, $width, $height, $crop=0) {

  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

  $type = strtolower(substr(strrchr($src,"."),1));
  if($type == 'jpeg') $type = 'jpg';
  switch($type){
    case 'bmp': $img = imagecreatefromwbmp($src); break;
    case 'gif': $img = imagecreatefromgif($src); break;
    case 'jpg': $img = imagecreatefromjpeg($src); break;
    case 'png': $img = imagecreatefrompng($src); break;
    default : return "Unsupported picture type!";
  }

  // resize
  if($crop){
    if($w < $width or $h < $height) return "Picture is too small!";
    $ratio = max($width/$w, $height/$h);
    $h = $height / $ratio;
    $x = ($w - $width / $ratio) / 2;
    $w = $width / $ratio;
  }
  else{
    if($w < $width and $h < $height) return "Picture is too small!";
    $ratio = min($width/$w, $height/$h);
    $width = $w * $ratio;
    $height = $h * $ratio;
    $x = 0;
  }

  $new = imagecreatetruecolor($width, $height);

  // preserve transparency
  if($type == "gif" or $type == "png"){
    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
    imagealphablending($new, false);
    imagesavealpha($new, true);
  }

  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

  switch($type){
    case 'bmp': imagewbmp($new, $dst); break;
    case 'gif': imagegif($new, $dst); break;
    case 'jpg': imagejpeg($new, $dst); break;
    case 'png': imagepng($new, $dst); break;
  }
  return true;
}



?>
