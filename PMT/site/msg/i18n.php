<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2006 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : i18n.php
 *		projet   : outil d'internationalisation
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 12/07/06
 *		modif    : 
 *
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>i18n</title>

	<!-- début meta -->
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Language" content="fr" />
	<meta http-equiv="pragma" content ="no-cache" />
	<meta http-equiv="CacheControl" content ="no-cache" />

	<meta name="author" content="Dominique Laporte" />
	<meta name="copyright" content="Copyright &copy; 2002-2006 Dominique Laporte (dominique.laporte@educagri.fr)" />
	<!-- fin meta -->
</head>

<body style="background-color:#FFFFFF; margin-top:5px;margin-left:5px;margin-right:5px;margin-bottom:5px;">

<?php
//---------------------------------------------------------------------------
require "../include/TMessage.php";
//---------------------------------------------------------------------------
function straccent($string)
{
/*
 * fonction :	remplace les caractères accentués dans une châine
 * in :		chaîne de caractère accentuée
 * out :		chaîne de caractère sans accent
 */

	// les caractères de remplacement
	$accent = Array('à','ä','â','é','è','ë','ê','ï','î','ö','ô','ù','ü','û','ç');
	$filtre = Array('a','a','a','e','e','e','e','i','i','o','o','u','u','u','c');

	// renvoie de la nouvelle chaîne
	return str_replace($accent, $filtre, $string);
}
//---------------------------------------------------------------------------
function myurlencode($url)
{
	return straccent( str_replace(Array('/',' ','&'), Array('%slash;','%20','&amp;'), $url) );
}
//---------------------------------------------------------------------------
function myurldecode($url)
{
	return str_replace(Array('%slash;','%20','&amp;'), Array('/',' ','&'), $url);
}
//---------------------------------------------------------------------------

$ROOTDIR = ".";

$lang   = ( @$_POST["lang"] )
	? $_POST["lang"]
	: "fr" ;
$file   = ( @$_POST["file"] )
	? $_POST["file"]
	: @$_GET["file"] ;
$item   = ( @$_POST["item"] )
	? (int) $_POST["item"]
	: (int) @$_GET["item"] ;
$texte  = @$_POST["texte"];
$titre  = @$_POST["titre"];
$submit = @$_POST["valid"];

// définition des messages
if ( is_file("$file") )
	require("$file");

$msg = new TMessage("$lang/$file", $ROOTDIR);
$msg->raw = true;

switch ( $submit ) {
	case "-" :
		if ( $file ) {
			unlink("$file");
			unlink("$lang/$file");
			}
	case "+" :
		$file = "";
		$msg  = new TMessage("$lang/$file", $ROOTDIR);
		break;

	case "<<" :
		$item = 0;
		break;

	case "<< Précédent" :
		if ( $item > 0 )
			$item--;
		break;

	case "Suivant >>" :
		if ( $item < $msg->count() )
			$item++;
		break;

	case ">>" :
		$item = $msg->count() - 1;
		break;

	case "Suppr" :
		$msg->delete($item);
		break;

	case "Valider" :
		if ( $item < $msg->count() )
			$msg->add($titre, $texte, $item);
		else
			$msg->add($titre, $texte);
		break;

	default :
		break;
	}

$texte = $msg->read($item);
$titre = @$msg->varDecl[$item];
?>

<table summary="">
 <tr>
  <td valign="top">
	<form id="formulaire" action="" method="post">
	<input type="hidden" name="item" value="<? print($item); ?>" />

	<select onchange="document.forms.formulaire.submit()" name="lang">
	<option value="fr">fr</option>
	</select>

	<?php
	if ( $submit == "+" )
		print("<input type=\"text\" name=\"file\" size=\"40\" value=\"$file\" />");
	else {
		print("<select onchange=\"document.forms.formulaire.submit()\" name=\"file\">");
		print("<option value=\"\">choisissez un fichier</option>");

		// ouverture du répertoire des fichiers messages
		$myDir = @opendir($lang);

		// lecture des répertoires
		while ( $entry = @readdir($myDir) )
			switch ( $entry ) {
				case "." :
				case ".." :
					break;

				default :
					// construction du menu déroulant
					if ( $entry == $file )
						print("<option value=\"$entry\" selected=\"selected\">$entry</option>");
					else
						print("<option value=\"$entry\">$entry</option>");
					break;
				}

		@closedir($myDir);

		print("</select>");
		print("<input type=\"submit\" value=\"+\" name=\"valid\" />");
		print("<input type=\"submit\" value=\"-\" name=\"valid\" />");
		}
	?>

	<br />

	<input type="submit" value="<<" name="valid" />
	<input type="submit" value="<< Précédent" name="valid" />
	<? if ( $msg->count() > -1 ) print($item + 1 ."/". $msg->count()); ?>
	<input type="submit" value="Suivant >>" name="valid" />
	<input type="submit" value=">>" name="valid" />

	<br />

	<textarea rows="10" name="texte" cols="40"><? print("$texte"); ?></textarea>

	<br />

	<input type="text" name="titre" size="40" value="<? print("$titre"); ?>" />
	<input type="submit" value="Suppr" name="valid" />

	<br />

	<input type="submit" value="Valider" name="valid" />

	</form>
  </td>

  <td valign="top">
	<?php
	for ($i = 0, $j = 1; $i < $msg->count(); $i++, $j++)
		print("<a href=\"".myurlencode("?file=$file&item=$i")."\">$j</a> : ".$msg->varDecl[$i]." <b>-></b> ".$msg->read($i)."<br />");
	?>
	&nbsp;
  </td>
 </tr>
</table>

</body>
</html>

