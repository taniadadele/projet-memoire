<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2003-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
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
?>

<?php
/*
 *		module   : spip.php
 *		projet   : interprétation des raccourcis typographiques
 *
 *		version  : 1.3
 *		auteur   : laporte
 *		creation : 25/09/03
 *		modif    : 26/11/03 - par D. Laporte
 *                     fix du bug des smileys en conflit avec le raccourci typo des notes de bas de page
 *
 *		           20/03/04 - par D. Laporte
 *                     ajout des meta pour les dépêches
 *
 *                     20/06/06 - par Didier Roy
 * 		           migration PHP5
 *
 * 		           17/07/06 - par Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


//---------------------------------------------------------------------------
function stripHTMLtags($string)
{
	//fonction qui supprime le code HTML
	$texte  = "";
	$mots   = explode("<", $string);
	$nbmots = count($mots);

	for ($m = 0; $m < $nbmots; $m++) {
		$mot    = $mots[$m];
		$fin    = strpos($mot, ">", 0);

		if ( $fin > 0 )
			$mot = substr($mot, $fin + 1);

		$texte .= "$mot";
		}

	return trim($texte);
}
//---------------------------------------------------------------------------
function find_meta($line)
{
	// recherche des mot-clefs dans les modèles
	$string = array(
		"##TITLE PUBLICATION##",
		"##TITLE INFO##", "#TOP_ANCHOR",
		"##IMAGE ARTICLE##", "#TITLE_ANCHOR",
		"##TITLE ARTICLE##", "##TEXT ARTICLE##", "##ATTACHED##",
		"##TITLE NEWS##", "##TEXT NEWS##", "##TEXT TOP##",
		"##LOOP##", "##ENDLOOP##",
		"##BACKOFFICE##", "##LINKS##", "##AUTHOR##", '##LINK EDIT##'
		);

	for ($i = 0; $i < count($string); $i++)
		if ( strstr($line, $string[$i]) )
			return $string[$i];

	return "";
}
//---------------------------------------------------------------------------
function find_specialchar($chaine)
{
	$in  = Array(
		'[(www)]',
		'[(#)]',
		'[(&)]',
		'[(*)]',
		'[(?)]',
		'[()]',
		'[(!)]',
		'[(:)]',
		'[(.)]',
		'[(@)]');
	$out = Array(
		'<img src="images/spip/url.png" title="" alt="" />',
		'<img src="images/spip/tel.gif" title="" alt="" />',
		'<img src="images/spip/fax.gif" title="" alt="" />',
		'<img src="images/spip/doc.gif" title="" alt="" />',
		'<img src="images/spip/aide.gif" title="" alt="" />',
		'<img src="images/spip/euro.gif" title="" alt="" />',
		'<img src="images/spip/warn.gif" title="" alt="" />',
		'<img src="images/spip/note.gif" title="" alt="" />',
		'<img src="images/spip/loupe.gif" title="" alt="" />',
		'<img src="images/spip/mail.gif" title="" alt="" />');

	return str_replace($in, $out, $chaine);
}
//---------------------------------------------------------------------------
function make_content($chaine)
{
	require $_SESSION["ROOTDIR"]."/msg/spip.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/spip.php");

	$content = "";
	$i = 0;
	$j = 1;

	if ( $chaine ) {
		$more     = "<span style=\"cursor: pointer;\" onclick=\"$('content')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/updown.png\" title=\"\" alt=\"\" /></span>";

		$content .= "<div style=\"background-color:#f9f9f9; width:25%; padding:4px; border-style:solid; border-width:1px; border-color:#000000; margin-bottom: 20px;\">";

		$content .= "<p style=\"margin-bottom:5px;\" class=\"center\"><a name=\"content\"></a>". $msg->read($SPIP_SUMMARY) ." $more</p>";
		$content .= "<div id=\"content\" style=\"display:block;\">";
		$content .= "<table summary=\"\">";

		for ( $token = strtok($chaine, "\n"); $token != ""; $token = strtok("\n") ) {
			if ( substr($token, 0, 1) == "*" ) {
				$i++;
				$j = 1;
				}

			$texte    = substr($token, 1, strlen($token));
			$content .= ( substr($token, 0, 1) == "*" )
				? "<tr><td>$i&nbsp;</td><td><a href=\"#$token\">$texte</a></td></tr>"
				: "<tr><td>$i." . $j++ . ".&nbsp;</td><td><a href=\"#$token\">$texte</a></td></tr>" ;
			}

		$content .= "</table>";
		$content .= "</div>";

		$content .= "</div>";
		}

	return $content;
}
//---------------------------------------------------------------------------
function find_paragraph($chaine, $sep)
{
	$len    = strlen($sep);
	$p1     = substr($chaine, 0, $len);
	$p2     = substr($chaine, strlen($chaine) - $len - 1, $len);

	if ( $p1 == $sep AND $p2 == $sep ) {
		$texte  = substr($chaine, $len, strlen($chaine) - 2*$len - 1);
		$markup = "$sep$texte$sep";

		$name   = ( $sep == "===" ) ? ".$texte" : "*$texte" ;
		$texte  = ( $sep == "===" )
			? "<em><span class=\"medium\">$texte</span></em>"
			: "<span class=\"large\">$texte</span>" ;

		$more    = "<span style=\"cursor: pointer;\" onclick=\"$('$name')._display.toggle(); return false;\"><img src=\"".$_SESSION["ROOTDIR"]."/images/updown.jpg\" title=\"\" alt=\"\" /></span>";

		$chaine  = "<table summary=\"\" width=\"100%\">";
		$chaine .= "<tr>";
		$chaine .= "<td><a name=\"$name\"></a><strong>$texte</strong> $more <a href=\"#content\"><img src=\"".$_SESSION["ROOTDIR"]."/images/haut.gif\" title=\"\" alt=\"\" /></a></td>";
		$chaine .= "<td align=\"right\"><p style=\"margin: 0;\"><input type=\"hidden\" name=\"wiki_link_modify\" value=\"$markup\" /></p></td>";
		$chaine .= "</tr>";
		$chaine .= "</table>";

		$chaine .= "<hr style=\"margin: 0;\"/>";
		$chaine .= "<div id=\"$name\" style=\"display:block;\">";

		return $chaine;
		}

	return "";
}
//---------------------------------------------------------------------------
function find_image($chaine)
{
	global	$mysql_link;

	// recherche de la balise des illustrations
	$line  = $chaine;

	do 	{
		$start = strpos($line, "<image ");

		if ( $start ) {
			$token = substr($line, $start + 1, strlen($line));
			$end   = strpos($token, ">");

			if ( $end ) {
				$found = substr($token, 0, $end);
				$num   = substr($token, 5, strpos($token, "|") - 5);

				switch ( substr($token, strpos($token, "|") + 1, strpos($token, ">") - strpos($token, "|") - 1) ) {
					case "gauche" : $align = "left";   break;
					case "droite" : $align = "right";  break;
					default       : $align = "center"; break;
					}

				$Query  = "select _IDitem, _title, _texte, _ext from flash_pj ";
				$Query .= "where _IDpj = '$num' ";

				$result = mysqli_query($mysql_link, $Query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				if ( is_file($_SESSION["ROOTDIR"]."/$DOWNLOAD/spip/img/$row[0]-$num[$j].$row[3]") )
					$line  = str_replace(
						$found,
						"img src=\"".$_SESSION["ROOTDIR"]."/$DOWNLOAD/spip/img/$row[0]-$num[$j].$row[3]\" title=\"$row[2]\"  align=\"$align\"",
						$line);
				else
					$line  = str_replace($found, "<a href=\"#\">$found</a>", $line);
				}
			}
		} while ( $start );

	return $line;
}
//---------------------------------------------------------------------------
function find_pj($IDitem)
{
	global	$mysql_link;

	require $_SESSION["ROOTDIR"]."/msg/spip.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/spip.php");

	// traitement des pièces jointes
	$Query  = "select _IDpj, _title, _texte, _ext, _size from flash_pj ";
	$Query .= "where _IDitem = '$IDitem' ";
	$Query .= "AND _visible = 'O' AND _attach = 'O' ";
	$Query .= "order by _IDpj asc";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	if ( mysqli_affected_rows($mysql_link) ) {
		print("
		      <table summary=\"\" width=\"100%\" class=\"boxtitle\" cellspacing=\"0\" cellpadding=\"2\">
			  <tr>
		          <td  style=\"background-color:#eeeeee; width:100%\">
		            <img src=\"".$_SESSION["ROOTDIR"]."/images/spip/pj.gif\" title=\"\" alt =\"\" />". $msg->read($SPIP_ATTACHED) ."
		          </td>
		        </tr>
			</table>
			");

		while ( $row ) {
			print("
				<div style=\"margin-top:1px; margin-bottom:5px; padding:5px; border-style:solid; border-color:#000000; border-width:1px;\">
					<a href=\"".$_SESSION["ROOTDIR"]."/$DOWNLOAD/spip/doc/$IDitem-$row[0].$row[3]\" onclick=\"window.open(this.href, '_blank'); return false;\">
					<img src=\"".$_SESSION["ROOTDIR"]."/images/mime/$row[3].gif\" title=\"". $msg->read($SPIP_OPENATTACH) ."\" alt=\"". $msg->read($SPIP_OPENATTACH) ."\" />
					</a>
					$row[1] ". $msg->read($SPIP_BYTE, strval($row[4])) ."<br/>$row[2]
				</div>
				");

			$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
		}
}
//---------------------------------------------------------------------------
function find_shortcut($string, &$note)
{
	// caractère gras (controle insuffisant mais traitement rapide !)
	if ( strpos($string, "{{") < strpos($string, "}}") )
		$string = str_replace("{{", "<strong>", str_replace("}}", "</strong>", $string));

	// caractère italique (controle insuffisant mais traitement rapide !)
	if ( strpos($string, "{") < strpos($string, "}") )
		$string = str_replace("{", "<em>", str_replace("}", "</em>", $string));

	// étoile
	if ( strpos($string, "[*]") == "" )
		$string = str_replace("\*", "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/star.gif\" title=\"\" alt =\"\" />", $string);

	// liens hypertextes internes (controle insuffisant mais traitement rapide !)
	do {
		$len    = strlen($string);

		$start  = strpos($string, "[[");
		$offset = ( strstr($string, "[[->") || strpos($string, "[[-&gt;") ) ? 2 : 0 ;

		$end    = $start + strpos(substr($string, $start, $len), "]]");

		$text   = strpos(substr($string, $start + 2, $end - $start), "|");

		$title  = ( $text )
			? "title=\"" .substr($string, $start + $text + 3, $end - $start - $text - 3). "\""
			: "" ;

		if ( is_integer($start) AND $start < $end ) {
			$temp    = $string;
			$length  = ( $text )
				? $text
				: $end - $start - 2;
			$link    = substr($temp, $start + $offset + 2, $length - $offset);

			$string  = substr($temp, 0, $start);
			$string .= ( $offset )
				? "<a href=\"$link\" $title onclick=\"window.open(this.href, '_blank'); return false;\">$link <img src=\"".$_SESSION["ROOTDIR"]."/images/link_server.png\" alt=\"\" title=\"\" /></a>"
				: "<a href=\"wiki_link_internal&amp;wiki_tag=$link\" $title>$link</a>" ;
			$string .= substr($temp, $end + 2, strlen($temp));
			}
		} while ( $start AND $start < $end );

	// liens hypertextes externes (controle insuffisant mais traitement rapide !)
	do {
		$len   = strlen($string);
		$gt    = ( strpos($string, "->", $start) ) ? "->" : "-&gt;" ;

		$start = ( strpos($string, "[") === false ) ? -1 : strpos($string, "[") ;
		$at    = @strpos($string, $gt, $start);
		$end   = @strpos($string, "]", $start);

		$list  = explode($gt, substr($string, $start + 1, $end - $start - 1));
		$link  = explode("|", @$list[1]);

		$http  = ( strstr($link[0], "http://") OR strstr($link[0], "https://") )
			? ""
			: "http://" ;

		// texte
		$text  = ( $list[0] )
			? $list[0]
			: $link[0] ;

		// info bulle
		$title = ( @$link[1] )
			? "title=\"$link[1]\""
			: "" ;

		if ( $start > -1 AND $start < $at AND $at < $end ) {
			$temp   = $string;
			$string = ( strstr($link[0], "@") )
				? substr($temp, 0, $start) . "<a href=\"mailto:$link[0]\" $title>$text</a>" . substr($temp, $end + 1, strlen($temp))
				: substr($temp, 0, $start) . "<a href=\"$http". $link[0] ."\" $title onclick=\"window.open(this.href, '_blank'); return false;\">$text <img src=\"".$_SESSION["ROOTDIR"]."/images/link.png\" alt=\"\" title=\"\" /></a>" . substr($temp, $end + 1, strlen($temp)) ;
			}
		} while ( $start AND $start < $at AND $at < $end );

	// notes de bas de page (controle insuffisant mais traitement rapide !)
	$note = "";
	do {
		$len   = strlen($string);

		$start = strpos($string, "[(");
		$at    = $start + strpos(substr($string, $start, $len), ")");
		$end   = $start + strpos(substr($string, $start, $len), "]");

		if ( $start AND $start < $at AND $at < $end ) {
			$temp   = $string;
			$value  = substr($temp, $start + 2, $at - $start - 2);

			$note   = "[<a href=\"#nh$value\" name=\"nb$value\">$value</a>] " . substr($temp, $at + 1, $end - $at - 1) . "<br/>";
			$string = substr($temp, 0, $start + 1) . "<a href=\"#nb$value\" name=\"nh$value\">$value</a>" . substr($temp, $end, strlen($temp)) ;
			}
		} while ( $start AND $start < $at AND $at < $end );

	return $string;
}
//---------------------------------------------------------------------------
function find_typo($chaine, &$piedpg, $html = false, $nl = "<br/>")
{
	/*
	 * function : traitement des raccourcis typographiques
	 */

	// initialisation
	$title   = "";
	$content = "";
	$string  = "";
	$piedpg  = "";
	$cpt     = 1;
	$line    = 0;
	$nbpar   = 0;

	// traitement ligne par ligne
//	$chaine  = ( $html ) ? htmlspecialchars($chaine) : $chaine ;
	$chaine  = str_replace("\r\n", "\n", $chaine);
	$chaine  = str_replace("\n", "\r\n", $chaine);

	// caractères spéciaux
	$chaine  = find_specialchar($chaine);

	for ( $token = strtok($chaine, "\n"); $token != ""; $token = strtok("\n") ) {
		$token = trim(str_replace('\r', '', $token)) . "\r";

		// longueur de la chaîne à traiter
		$len   = strlen($token);

		// remise à zéro des puces numérotées
		if ( substr($token, 0, 2) != "-#" )
			$cpt = 1;

		// remise à zéro des lignes du tableau
		if ( substr($token, 0, 1) != "|" ) {
			if ( $line )
				$string .= "</table>";

			$line = 0;
			}

		switch ( substr($token, 0, $len -1) ) {
			//traitement des lignes horizontales
			case "----" :
				$string .= "<hr/>";
				break;
			case "---" :
				$string .= "<hr style=\"width:80%;\" />";
				break;
			case "--" :
				$string .= "<hr style=\"width:50%;\" />";
				break;

			default :
				switch ( substr($token, 0, 1) ) {
					// traitement du titre
					case "@" :
						$title = "<p class=\"center\"><span class=\"medium\"><strong>". substr($token, 1, strlen($token)) ."</strong></span></p><hr/>";
						break;

					// traitement des puces
					case "-" :
						$offset = 2;

						switch ( substr($token, 1, 1) ) {
							// traitement des puces
							case "#" :
								$string .= $cpt++ . ".";
								break;
							case ">" :
								$string .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/puce.gif\" title=\"\" alt =\"*\" />";
								break;
							case "&" :
								$offset  = 5;
								$string .= ( substr($token, 1, 4) == "&gt;" )
									? "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/puce.gif\" title=\"\" alt =\"*\" />"
									: "" ;
								break;
							case "$" :
								$string .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/dot.gif\" title=\"\" alt =\"*\" />";
								break;
							case "§" :
								$string .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/jaune.gif\" title=\"\" alt =\"*\" />";
								break;
							case "." :
								$string .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/rond.gif\" title=\"\" alt =\"*\" />";
								break;
							case ":" :
								$string .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/rond2.gif\" title=\"\" alt =\"*\" />";
								break;
							case "*" :
								$string .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/spip/rouge.gif\" title=\"\" alt =\"*\" />";
								break;

							default :
								$offset = 0;
								break;
							}

						$string .= find_shortcut(substr($token, $offset, $len - $offset), $note) . $nl;
						break;

					// traitement des tableaux
					case "|" :
						// couleur des cellules
						$bgcolor = ( $line % 2 ) ? "item" : "spip" ;

						$string .= ( $line == 0 )
							? "<table summary=\"\" width=\"100%\"><tr style=\"background-color:".$_SESSION["CfgColor"].";\">"
							: "<tr class=\"$bgcolor\">" ;

						// tri par colonne
						$mysort = "<a href=\"#\" onclick=\"ts_resortTable(this); return false;\"><span></span></a>";

						// remplissage tableau
						$liste = explode("|", $token);

						$nb  = 1;
						while ( @$liste[$nb] )
							$nb++;
						$nb -= 2;

						$width = ( $nb > 1 )
							? (int) (100 / $nb)
							: 100 ;

						for ( $i = 1; $i <= $nb; $i++ ) {
							$string .= "<td align=\"center\" style=\"width:$width%;\">";
							$string .= ( $line )
								? find_shortcut($liste[$i], $note)
								: "$mysort <div style=\"color:#FFFFFF;\">" . find_shortcut($liste[$i], $note) . "</div>" ;
							$string .= "</td>";
							}

						$string .= "</tr>";
						$line++;
						break;

					// traitement des paragraphes
					case "=" :
						// fin de paragraphe
						if ( $nbpar ) {
							$string .= "</div>";
							$nbpar--;
							}

						if ( ($paraph = find_paragraph($token, "===")) == "" )
							if ( ($paraph = find_paragraph($token, "==")) == "" )
								$paraph = $token . $nl;
							else {
								$content .= "*" . substr($token, 2, strlen($token) - 4 - 1) . "\n";
								$nbpar++;
								}
						else {
							$content .= "." . substr($token, 3, strlen($token) - 6 - 1) . "\n";
							$nbpar++;
							}

						$string .= $paraph;
						break;

					// traitement du texte
					default :
						$string .= find_shortcut($token, $note) . $nl;
						break;
					}
				break;
			}

		// note de bas de page
		$piedpg .= $note;
		}	// endfor traitement par ligne

	// fin de paragraphe
	if ( $nbpar)
		$string .= "</div>";

	// fin de tableau
	if ( $line )
		$string .= "</table>";

	return stripslashes($title . make_content($content) . trim($string));
}
//---------------------------------------------------------------------------
?>
