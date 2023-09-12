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


/*
 *		module   : flash_visu.php
 *		projet   : visualisation des flash-infos
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 25/09/03
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */
?>

<?php
//---------------------------------------------------------------------------
require_once "include/smileys.php";
require_once "include/spip.php";
require_once "include/flash.php";
//---------------------------------------------------------------------------
// lien de retour
if ( empty($IDsubmenu) )
	$IDsubmenu = 0;

$href   = "index.php?item=".(@$item)."&IDflash=$IDflash&IDinfos=".@$IDinfos."&IDsubmenu=$IDsubmenu";
//$href   = "index.php?item=$item&IDflash=$IDflash&IDinfos=".@$IDinfos;

switch ( @$_GET["submit"] ) {
	case "del" :
		$Query   = "delete from flash_items ";
//		$Query  .= "where _IDinfos = '".@$_GET["IDinfos"]."' ";
		$Query  .= "where _IDinfos = '$IDinfos' ";
		$Query  .= "AND _IDitem = '".@$_GET["IDitem"]."' ";
		$Query  .= ( $_SESSION["CnxAdm"] != 255 ) ? "AND _ID = '".$_SESSION["CnxID"]."' " : "" ;
		$Query  .= "limit 1";

		mysqli_query($mysql_link, $Query);
		break;

	case "visible" :
	case "invisible" :
		$visible = ( @$_GET["submit"] == "visible" ) ? "O" : "N" ;

		$Query   = "update flash_items ";
		$Query  .= "set _visible = '$visible' ";
//		$Query  .= "where _IDinfos = '".@$_GET["IDinfos"]."' ";
		$Query  .= "where _IDinfos = '$IDinfos' ";
		$Query  .= "AND _IDitem = '".@$_GET["IDitem"]."' ";
		$Query  .= ( $_SESSION["CnxAdm"] != 255 ) ? "AND _ID = '".$_SESSION["CnxID"]."' " : "" ;
		$Query  .= "limit 1";

		mysqli_query($mysql_link, $Query);
		break;

	case "up" :
	case "down" :
		setArticleOrder(@$_GET["IDinfos"], @$_GET["IDitem"], @$_GET["submit"]);
		break;

	default :
		break;
	}

// lecture de la publi
$Query  = "select _template, _chrono, _IDmod, _IDgrpwr, _poster from flash ";
$Query .= "where _IDflash = '$IDflash' ";
$Query .= "limit 1";

$result = mysqli_query($mysql_link, $Query);
$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

if ( $row ) {
	// on charge le modèle
	$file = $_SESSION["ROOTDIR"]."/$DOWNLOAD/spip/templates/".$_SESSION["lang"]."/$row[0]";

	if ( ($fp = fopen($file, "r")) != 0 ) {
	    	// log des clicks sur l'info de la publi
		mysqli_query($mysql_link, "update flash_data set _hit = _hit + 1 where _IDinfos = '$IDinfos' ");

	    	// lecture de l'info de la publi
		$Query   = "select _title, _align, _color, _snd, _repeat, _img, _date, _modif, _ID, _IP, _hit, _visible, _IDinfos ";
		$Query  .= "from flash_data ";
		$Query  .= "where _IDflash = '$IDflash' ";
		$Query  .= ( @$sublevel OR @$_GET["IDsubmenu"] == 0 ) ? "AND _IDinfos = '$IDinfos' " : "" ;
		$Query  .= ( @$sublevel == 0 AND @$_GET["IDsubmenu"] ) ? "AND _title = '#menu_item:".$_GET["IDsubmenu"]."' " : "" ;
		$Query  .= ( $_SESSION["CnxAdm"] != 255 AND $_SESSION["CnxID"] != $row[2] )
			? "AND _visible = 'O' "
			: "" ;

		$result  = mysqli_query($mysql_link, $Query);
		$info    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	    	// lecture des articles de la publi
		$Query   = "select _title, _texte, _color, _img, _IDitem, _ID, _IP, _date, _r, _e, _c, _modif, _raw, _visible, _IDinfos ";
		$Query  .= "from flash_items ";
		$Query  .= "where _IDinfos = '$info[12]' ";
		$Query  .= ( @$sublevel ) ? "AND _title = '#menu_item:".@$_GET["IDsubmenu"]."' " : "AND _title not like '#menu_item:%' " ;
		$Query  .= ( $_SESSION["CnxAdm"] != 255 AND $_SESSION["CnxID"] != $row[2] )
			? "AND _visible = 'O' OR (_visible = 'N' AND _ID = '".$_SESSION["CnxID"]."') "
			: "" ;
		if ( $row[1] == "S" )
			$Query  .= "order by _order";
		else {
			$Query  .= "order by _IDitem ";
			$Query  .= ( $row[1] == "O" ) ? "asc" : "desc" ;
			}

		$result  = mysqli_query($mysql_link, $Query);
		$article = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		$upper   = read_uppermenu("#menu_item:".@$_GET["IDsubmenu"]);

		// backoffice
		$config  = ( $upper )
			? "<a href=\"".myurlencode("index.php?item=19&IDsubmenu=$upper")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/haut.gif\" title=\"\" alt=\"\" /></a> "
			: "" ;
		$config .= ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $row[2] OR ($_SESSION["CnxAdm"] & 8) OR ($row[3] & pow(2, $_SESSION["CnxGrp"] - 1)) )
			? "<a href=\"".myurlencode("index.php?item=19&cmde=post&IDinfos=$IDinfos&IDsubmenu=".@$_GET["IDsubmenu"])."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/stats/post.gif\" title=\"\" alt=\"\" /></a> "
			: "" ;
		$config .= ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxID"] == $row[2] OR ($_SESSION["CnxAdm"] & 8) )
			? "<a href=\"".myurlencode("index.php?item=19&cmde=gestion&IDinfos=$IDinfos")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/tools.gif\" title=\"\" alt=\"\" /></a>"
			: "" ;

	    	// lecture des liens de la publi
		$Query   = "select _ident, _link ";
		$Query  .= "from config_submenu ";
		$Query  .= "where _IDmenu = '-".@$_GET["IDsubmenu"]."' ";
		$Query  .= "AND _visible = 'O' ";
		$Query  .= "order by _order";

		$myres   = mysqli_query($mysql_link, $Query);
		$myrow   = ( $myres ) ? remove_magic_quotes(mysqli_fetch_row($myres)) : 0 ;

		$mylinks = ( $myrow ) ? "[<a href=\"".myurlencode("index.php?$myrow[1]")."\">$myrow[0]</a>]" : "" ;

		do {
			$myrow    = remove_magic_quotes(mysqli_fetch_row($myres));
			$mylinks .= ( $myrow ) ? ".[<a href=\"".myurlencode("index.php?$myrow[1]")."\">$myrow[0]</a>]" : "" ;
			} while ( $myrow );

		// intitialisation structure de controle
		$loop    = false;

		// lecture du modèle
		while ( !feof($fp) ) {
			// lecture du modèle
			$line = fgets($fp, 512);
			// traitement du code
			switch ( find_meta($line) ) {
				case "##LINK EDIT##" :
					if (isset($lien)) echo str_replace('##LINK EDIT##', $lien, $line);
					break;


				case "##TITLE PUBLICATION##" :
					break;

				case "##TITLE INFO##" :
					$mytitle  = read_menutitle($info[0]);
					$mytitle .= ( @$sublevel ) ? "/" . read_menutitle("#menu_item:".@$_GET["IDsubmenu"]) : "" ;

					print( str_replace("##TITLE INFO##", $mytitle, $line) );
					break;

				case "##BACKOFFICE##" :
					print( str_replace("##BACKOFFICE##", $config, $line) );
					break;

				case "##LINKS##" :
					print( str_replace("##LINKS##", $mylinks, $line) );
					break;

				case "##LOOP##" :
					$temp = "";
					$loop = true;
					break;

				case "##TITLE ARTICLE##" :
					if ( $loop )
						if(isset($mobile) && $mobile)
						{
							// $temp .= "<ul data-role=\"listview\" data-theme=\"a\"><li data-theme=\"b\">$line</li></ul>";
							$temp .= "<h4>$line</h4>";
						}
						else
						{
							$temp .= "<h4>$line</h4>";
						}
					else
						print( str_replace("##TITLE ARTICLE##", $article[0], $line) );
					break;

				case "##TEXT ARTICLE##" :
					if ( $loop )
						if(isset($mobile) && $mobile)
						{
							$temp .= "<div style=\"padding: 10px\">".$line."</div>";
						}
						else
						{
							// $temp .= "<div style=\"padding-left: 15px\">".$line.'-----</div>';
							$temp .= $line;
						}
					else
						print( read_article($article, $line) );
					break;

				case "##ATTACHED##" :
				    	// lecture des PJ des articles
					if ( $loop )
						$temp .= $line;
					else
						print( read_attachment($article, $line, $href) );
					break;

				case "##AUTHOR##" :
					// affichage auteur + barre d'outils
					if ( $loop )
						$temp .= $line;
					else
						print( read_author($article, $line, $href) );
					break;

				case "##ENDLOOP##" :
					while ( $article ) {
						print( read_author($article, read_article($article, read_attachment($article, $temp, $href), true), "index.php?item=19&IDinfos=$IDinfos&IDsubmenu=".@$_GET["IDsubmenu"]."&IDflash=$IDflash") );

						$article = remove_magic_quotes(mysqli_fetch_row($result));
					}

					$loop = false;
					break;



				default :
					if ( $loop )
						$temp .= $line;
					else
						echo $line;
					break;
				}
			}

		fclose($fp);
		}
	}
//---------------------------------------------------------------------------
?>
