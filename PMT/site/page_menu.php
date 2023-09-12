<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2002-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : page_menu.php
 *		projet   : les menus
 *
 *		version  : 2.2
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 23/12/02 - par D. Laporte
 *                     ajout du menu de la gestion des stages et de l'aide en ligne
 *
 *		           15/02/03 - par D. Laporte
 *                     ajout de la liste des élèves
 *
 *		           30/03/03 - par D. Laporte
 *                     ajout du menu documents
 *
 *		           11/11/03 - par D. Laporte
 *                     ajout du menu statistiques
 *
 *		           6/12/03 - par D. Laporte
 *                     ajout du menu wiki
 *
 *		           11/12/03 - par D. Laporte
 *                     ajout de l'aide en ligne
 *
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */
?>



<?php
$_SESSION["CfgPuce"]    = $_SESSION["ROOTDIR"]."/css/themes/puce/dash.gif";
$_SESSION["CfgHeader"]  = $_SESSION["ROOTDIR"]."/css/themes/header/blurred.jpg";
//---------------------------------------------------------------------------
function find_browser($user_agent)
{
	// fonction en php qui permet de savoir le navigateur + sa version !
	// On cherche à chaque fois une chaîne de la forme   "amaya[ \/]([0-9\.]+)"  (les parenthèses servent à récupérer la version du navigateur
	// Ces navigateurs transmettent un HTTP_USER_AGENT où leur nom se trouve sous la forme <nom>(espace ou / ou \)<n° version>
	// Les autres navigateurs peuvent être cherchés plus loin

	$liste_navigateurs = array("Amaya", "AvantGo", "Bluefish", "Dillo", "Galeon", "iCab", "ICE Browser", "Konqueror", "Lynx", "Opera", "Oregano", "WebTv", "Wget", "FireFox", "Safari") ;

	for ($i = 0, $type_navigateur = "" ; $i < sizeof($liste_navigateurs) ; $i++)
		if ( mb_eregi($liste_navigateurs[$i] . "[ \/]([0-9\.]+)", $user_agent, $version) )
	      	return array("type" => $liste_navigateurs[$i],  "version" => $version[1]) ;

	if ( mb_eregi("MSIE[ \/]([0-9\.]+)", $user_agent, $version) )
		return array("type" => "M$-IE",  "version" => $version[1]) ;

	// Mozilla ou Netscape
	if ( mb_eregi("Mozilla/([0-9.]+)", $user_agent, $version) && !mb_eregi("compatible", $user_agent) ) {
		if ( mb_eregi("netscape[[:alnum:]]*[/\ ]([0-9.]+)", $user_agent, $version) ) //Netscape v. 6+
	     		return array("type" => "Netscape",  "version" => $version[1]) ;
		if ( mb_eregi("rv:([0-9.]+)", $user_agent, $version) || mb_eregi("[^[]]m([0-9.]+)", $user_agent, $version) )
	            return array("type" => "Mozilla",  "version" => $version[1]) ;
		return array("type" => "Netscape",  "version" => $version[1]) ;
		}

	// Moteur de balayage
	if ( mb_eregi("(bot|google|slurp|scooter|spider|infoseek|arachnoidea|altavista)", $user_agent) )
		return array("type" => "crawler",  "version" => 0) ;

	// Navigateur inconnu
	return array("type" => "??",  "version" => 0) ;
}
//---------------------------------------------------------------------------

function makeSubmenu($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$texte  = "<ul class=\"submenu\">";

	$Query  = "select _ident, _link, _visible, _IDsubmenu from config_submenu ";
	$Query .= "where _IDmenu = '$idmenu' ";
	$Query .= "AND _lang= '".$_SESSION["lang"]."' ";
	$Query .= ( $_SESSION["CnxAdm"] == 255 OR ($_SESSION["CnxAdm"] & 8) ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
	$Query .= ( $_SESSION["CnxAdm"] == 255 OR ($_SESSION["CnxAdm"] & 8) ) ? "" : "AND _visible = 'O' " ;
	$Query .= ( $_SESSION["CnxSex"] != "A" ) ? "" : "AND _anonyme = 'O' " ;
	$Query .= "order by _order";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$i = 0;
	while ( $row ) {
		$color  = ( $i++ % 2 ) ? "item" : "item2" ;

		$row[1] = myurlencode($row[1]);

		// lien externe
		$href   = ( strstr($row[1], "http://") != "" OR strstr($row[1], "https://") != "" )
			? $row[1]
			: "index.php?$row[1]" ;

		$target = ( strstr($row[1], "http://") != "" OR strstr($row[1], "https://") != "" )
			? "onclick=\"window.open(this.href, '_blank'); return false;\""
			: "" ;

		$mylink = ( $_SESSION["CnxAdm"] == 255 OR ($_SESSION["CnxAdm"] & 8) )
			? "<a><img src=\"".$_SESSION["ROOTDIR"]."/images/settings.png\" title=\"".$msg->read($PAGE_CONFIG)."\" alt=\"".$msg->read($PAGE_CONFIG)."\" /></a>"
			: "<img src=\"".$_SESSION["CfgPuce"]."\" title=\"\" alt=\"*\" />" ;

		$access = ( $row[2] == "O" ) ? "visible" : "invisible" ;
		$alt    = ( $row[2] == "O" ) ? "O" : "X" ;

		$tools  = "<a href=\"".myurlencode("index.php?cmde=delitem&iditem=$row[3]")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"".$msg->read($PAGE_DELITEM)."\" alt=\"".$msg->read($PAGE_DELITEM)."\" /></a> ";
		$tools .= "<a href=\"".myurlencode("index.php?cmde=setorderitem&ident=$row[3]&order=up")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/up.png\" title=\"".$msg->read($PAGE_MENUUP)."\" alt=\"".$msg->read($PAGE_MENUUP)."\" /></a> ";
		$tools .= "<a href=\"".myurlencode("index.php?cmde=setorderitem&ident=$row[3]&order=down")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/down.png\" title=\"".$msg->read($PAGE_MENUDOWN)."\" alt=\"".$msg->read($PAGE_MENUDOWN)."\" /></a> ";
		$tools .= "<a href=\"".myurlencode("index.php?item=21&cmde=submenu&IDconf=".$_SESSION["CfgID"]."&IDmenu=$idmenu&IDsubmenu=$row[3]")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/stats/post.gif\" title=\"".$msg->read($PAGE_UPDATEITEM)."\" alt=\"".$msg->read($PAGE_UPDATEITEM)."\" /></a> ";
		$tools .= ( $_SESSION["CnxAdm"] == 255 OR ($_SESSION["CnxAdm"] & 8) )
			? "<a href=\"".myurlencode("index.php?cmde=setsubmenu&ident=$row[3]&access=$access")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/$access.gif\" title=\"\" alt=\"$alt\" /></a>"
			: "<img src=\"".$_SESSION["CfgPuce"]."\" title=\"\" alt=\"*\" />" ;

		$tools  = ( $_SESSION["CnxAdm"] == 255 OR ($_SESSION["CnxAdm"] & 8) ) ? "<div class=\"cfgitems\"><span>$tools</span></div>" : "" ;

		$texte .=
				"<li style=\"margin-bottom:3px;\" class=\"$color\">
					<div style=\"float:left; margin-right:3px;\" class=\"backitems\">
						$mylink
						$tools
			        	</div>

				  	<a href=\"$href\" title=\"$href\" $target>$row[0]</a>
				</li>";

		$row    = remove_magic_quotes(mysqli_fetch_row($result));
		}

	$texte .= "</ul>";

	return ( $i ) ? "<img src=\"".$_SESSION["ROOTDIR"]."/images/droit.gif\" title=\"\" alt=\"->\" />$texte" : "" ;
}
//---------------------------------------------------------------------------
function menu($idmenu, $sort)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$mytext = "";

	// le menu des utilisateurs
	$Query   = "select _service from user_config ";
	$Query  .= "where _IDconf = '".$_SESSION["CnxID"]."' ";
	$Query  .= "limit 1";

	$result  = mysqli_query($mysql_link, $Query);
	$myrow   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// les items du menu
	$Query  = "select _ident, _link, _image, _visible, _IDsubmenu, _backoffice, _url, _type from config_submenu ";
	$Query .= "where _IDmenu = '$idmenu' ";
	$Query .= "AND _lang= '".$_SESSION["lang"]."' ";
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND _visible = 'O' " ;
	$Query .= ( $_SESSION["CnxSex"] != "A" ) ? "" : "AND _anonyme = 'O' " ;
	$Query .= ( $sort == "O" )
		? "order by _ident"
		: "order by _order";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$i = 0;
	while ( $row ) {
		$color  = ( $i % 2 ) ? "item" : "menu" ;

		$row[1] = myurlencode($row[1]);

		// lien externe
		$href    = ( strstr($row[1], "http://") != "" OR strstr($row[1], "https://") != "" )
			? $row[1]
			: "index.php?$row[1]" ;

		$target  = ( strstr($row[1], "http://") != "" OR strstr($row[1], "https://") != "" )
			? "onclick=\"window.open(this.href, '_blank'); return false;\""
			: "" ;

		$access = ( $row[3]  == "N" ) ? "invisible" : "visible" ;
		$alt    = ( $row[3]  == "N" ) ? "X" : "O" ;
		$icon   = ( $_SESSION["CnxAdm"] == 255 AND $row[7] != 1 )
			? "<a href=\"".myurlencode("index.php?cmde=setsubmenu&ident=$row[4]&access=$access")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/$access.gif\" title=\"$access\" alt=\"$alt\" /></a>"
			: "<img src=\"".$_SESSION["CfgPuce"]."\" title=\"\" alt=\"*\" />" ;

		$mylink = ( $_SESSION["CnxAdm"] == 255 AND $row[7] != 1 )
			? "<a><img src=\"".$_SESSION["ROOTDIR"]."/images/settings.png\" title=\"".$msg->read($PAGE_CONFIG)."\" alt=\"".$msg->read($PAGE_CONFIG)."\" /></a>"
			: "" ;

		switch ( $_SESSION["CnxAdm"] ) {
			case 255 :
				$tools  = "<a href=\"".myurlencode("index.php?cmde=delitem&iditem=$row[4]")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/corb.gif\" title=\"".$msg->read($PAGE_DELITEM)."\" alt=\"-\" /></a> ";
				$tools .= ( $sort == "N" )
					? "<a href=\"".myurlencode("index.php?cmde=setorderitem&ident=$row[4]&order=up")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/up.png\" title=\"".$msg->read($PAGE_MENUUP)."\" alt=\"".$msg->read($PAGE_MENUUP)."\" /></a> "
					: "" ;
				$tools .= ( $sort == "N" )
					? "<a href=\"".myurlencode("index.php?cmde=setorderitem&ident=$row[4]&order=down")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/down.png\" title=\"".$msg->read($PAGE_MENUDOWN)."\" alt=\"".$msg->read($PAGE_MENUDOWN)."\" /></a> "
					: "" ;
				$tools .= "<a href=\"".myurlencode("index.php?item=21&cmde=submenu&IDconf=".$_SESSION["CfgID"]."&IDmenu=$idmenu&IDsubmenu=-$row[4]")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/addrecord.gif\" title=\"".$msg->read($PAGE_ADDITEM)."\" alt=\"+\" /></a> ";
				$tools .= "<a href=\"".myurlencode("index.php?item=21&cmde=submenu&IDconf=".$_SESSION["CfgID"]."&IDmenu=$idmenu&IDsubmenu=$row[4]")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/stats/post.gif\" title=\"".$msg->read($PAGE_UPDATEITEM)."\" alt=\"!\" /></a> ";
				$tools .= ( $row[5] == "O" )
					? "<a href=\"index.php?$row[1]&amp;cmde=gestion\"><img src=\"".$_SESSION["ROOTDIR"]."/images/tools.gif\" title=\"".$msg->read($PAGE_BACKOFFICE)."\" alt=\"?\" /></a>"
					: "" ;

				$tools  = "<div class=\"cfg\"><span>$tools</span></div>";
				break;

			default :
				$tools = ( $_SESSION["CnxAdm"] & 8 AND $row[6] == "N" )
					? "<a href=\"".myurlencode("index.php?item=21&cmde=submenu&IDconf=".$_SESSION["CfgID"]."&IDmenu=$idmenu&IDsubmenu=-$row[4]")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/addrecord.gif\" title=\"".$msg->read($PAGE_ADDITEM)."\" alt=\"+\" /></a> "
					: "" ;
				break;
			}

		// Recherche du type de menu
		$Query2  = "select _order from config_menu ";
		$Query2 .= "where _IDmenu = '$idmenu' ";
		$Query2 .= "AND _lang= '".$_SESSION["lang"]."' ";

		$result2 = mysqli_query($mysql_link, $Query2);
		$row2    = ( $result2 ) ? remove_magic_quotes(mysqli_fetch_row($result2)) : 0 ;

		// attention aux préférences des utilisateurs
		if ( $row[7] != 2 OR in_array($row[4], explode(" ", $myrow[0])) ) {
			// image ou texte
			if ( strlen($row[2]) )
				$mytext .= "<p style=\"margin:5px; text-align:center;\"><a href=\"".myurlencode($href)."\" $target><img src=\"$DOWNLOAD/menu/$row[2]\" title=\"$row[0]\" alt=\"$row[0]\" /></a></p>";
			else
				/*$mytext .= "
				      <div class=\"$color\">
				        <div style=\"float:right; margin:0px 0px 0px 0px;\" class=\"backoff\">
						$mylink
						$tools
				        </div>

				        <div class=\"mymenu\">
						<div>
							$icon <a href=\"$href\" title=\"$href\" $target>". str_replace($keywords_search, $keywords_replace, $row[0]) ."</a>
							". makeSubmenu(-$row[4]) ."
						</div>
				        </div>
				      </div>";*/

				$mytext .= "<li>";
				if(isset($_SESSION["config"]) && $_SESSION["config"] == "on")
				{
				$mytext .= "<div style=\"float:right; margin:0px 0px 0px 0px; position: absolute; right: 0;\" class=\"backoff\">
						$mylink
						$tools
						</div>";
				}

				if ( strlen($row[2]) )
				{
					$mytext .= "</li>";
				}
				else
				{
					if($row2[0] > 0)
					{
						$mytext .= "<a href=\"$href\" title=\"$href\" $target style=\"/* margin-left: -1px; width: 302px */\" class=\"menu_left\"><i class=\"fa fa-external-link\"></i><span class=\"text_menu_left\"> ". str_replace($keywords_search, $keywords_replace, $row[0]) ."</span></a>
						". makeSubmenu(-$row[4]) ."
						</li>";
					}
					else
					{
						$mytext .= "<a href=\"$href\" $target> ". str_replace($keywords_search, $keywords_replace, $row[0]) ."</a>
						". makeSubmenu(-$row[4]) ."
						</li>";
					}
				}

			$i++;
			}

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	// texte d'accompagnement
	$Query  = "select _text from config_menu ";
	$Query .= "where _IDmenu = '$idmenu' ";
	$Query .= "AND _lang= '".$_SESSION["lang"]."' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	if ( strlen($row[0]) )
		$mytext .= "<p style=\"margin:0px; text-align:center;\" class=\"x-small\">$row[0]</p>";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu2($idmenu, $sort)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$mytext = "";

	// lecture des FIL d'info en continu
	$Query  = "select _ident, _link, _backoffice, _visible, _IDsubmenu from config_submenu ";
	$Query .= "where _IDmenu = '$idmenu' ";
	$Query .= "AND _lang= '".$_SESSION["lang"]."' ";
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND _visible = 'O' " ;
	$Query .= ( $_SESSION["CnxSex"] != "A" ) ? "" : "AND _anonyme = 'O' " ;
	$Query .= ( $sort == "O" )
		? "order by _ident"
		: "order by _order";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$i = 0;
	while ( $row ) {
		$color  = ( $i++ % 2 ) ? "item" : "menu" ;

		$row[1] = myurlencode($row[1]);

		$access = ( $row[3]  == "N" ) ? "invisible" : "visible" ;
		$alt    = ( $row[3]  == "N" ) ? "X" : "O" ;
		$icon   = ( $_SESSION["CnxAdm"] == 255 )
			? "<a href=\"index.php?cmde=setsubmenu&amp;ident=$row[4]&amp;access=$access\" style=\"position: absolute;\" onmouseover=\"this.style.background='none'\"><img src=\"".$_SESSION["ROOTDIR"]."/images/$access.gif\" title=\"$access\" alt=\"$alt\" style=\"position: absolute\" /></a>"
			: "" ;
		$icon2 = "";
		$padding   = ( $_SESSION["CnxAdm"] == 255 )
			? "padding-left: 20px"
			: "padding-left: 5px" ;
		$margin   = ( $_SESSION["CnxAdm"] == 255 )
			? "margin-left: -1px"
			: "margin-left: -2px" ;

		if($_SESSION["config"] == "on")
		{
			$tools  = ( $_SESSION["CnxAdm"] == 255 AND $row[2] == "O" )
			? "<a href=\"index.php?$row[1]&amp;cmde=gestion\"><img src=\"".$_SESSION["ROOTDIR"]."/images/tools.gif\" title=\"".$msg->read($PAGE_CONFIG)."\" alt=\"?\" /></a>"
     	    		: "&nbsp;" ;
		}
		else
		{
			$tools = "";
		}

		// On compte le nombre de fil d'actu non lu
		$query_news = "SELECT COUNT(*) FROM `flash_fil` WHERE ";
		$query_news .= "`_IDfil` NOT IN (SELECT _IDfil FROM `flash_filvu` WHERE _ID = '".$_SESSION['CnxID']."') ";
		if ($_SESSION['CnxGrp'] == 1) $query_news .= "AND (_IDflash = (SELECT _IDflash FROM flash WHERE _IDgrp = '1') OR _IDflash = (SELECT _IDflash FROM flash WHERE _IDgrp = '0')) ";
		if ($_SESSION['CnxGrp'] == 2) $query_news .= "AND (_IDflash = (SELECT _IDflash FROM flash WHERE _IDgrp = '1') OR _IDflash = (SELECT _IDflash FROM flash WHERE _IDgrp = '0') OR _IDflash = (SELECT _IDflash FROM flash WHERE _IDgrp = '2')) ";

		$result_news = mysqli_query($mysql_link, $query_news);
		while ($row_news = mysqli_fetch_array($result_news, MYSQLI_NUM)) {
		  $notViewed = $row_news[0];
		}
		if ($notViewed != 0) $notViewedBadge = "<span class=\"badge\">".$notViewed."</span>";
		else $notViewedBadge = "";



		if ( $_SESSION["CnxAdm"] == 255 OR $access == "visible" )
			$mytext .= "
			      <li>
			        <div style=\"float:right;\">
					  $tools $notViewedBadge
			        </div>
				  <a href=\"index.php?$row[1]\" title=\"index.php?$row[1]\" class=\"menu_left\"><i class=\"fa fa-info-circle\"></i><span class=\"text_menu_left\">$icon2 $row[0]</span></a>
			      </li>";

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	// lecture des flash info
	$Query  = "select _IDflash, _title, _IDgrp from flash ";
	$Query .= "where _visible = 'O' AND _type = 'F' ";
	$Query .= "AND _lang = '".$_SESSION["lang"]."' ";
	$Query .= ( $_SESSION["CnxAdm"]== 255 ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] -1).") " ;
	$Query .= "order by _title";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	while ( $row ) {
		$color  = ( $i++ % 2 ) ? "item" : "menu" ;

		// $link    = "<a href=\"index.php?item=0&amp;IDflash=$row[0]\" title=\"index.php?item=0&amp;IDflash=$row[0]\" style=\"margin-left: 0px; margin-right: -1px; padding-left: 15px\" > ";
		// $link   .= $msg->read($PAGE_FLASH, $row[1]);
		// $link   .= "</a>";
		$link    = "<a href=\"index.php?item=0&amp;IDflash=$row[0]\" title=\"index.php?item=0&amp;IDflash=$row[0]\" class=\"menu_left\" ><i class=\"fa fa-info-circle\"></i><span class=\"text_menu_left\"> ";
		$link   .= $msg->read($PAGE_FLASH, $row[1]);
		$link   .= "</span></a>";

		// On affiche pas les flash dans le menu de gauche
		// $mytext .= "<li>";
		// $mytext .= "$link";
		// $mytext .= "</li>";

		$row  = remove_magic_quotes(mysqli_fetch_row($result)) ;
		}

	return $mytext;
}
//---------------------------------------------------------------------------
function menu5($idmenu, $sort)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$mytext = "";

	$Query  = "select _ident, _link, _backoffice, _visible, _IDsubmenu from config_submenu ";
	$Query .= "where _IDmenu = '$idmenu' ";
	$Query .= "AND _lang= '".$_SESSION["lang"]."' ";
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND _visible = 'O' " ;
	$Query .= ( $_SESSION["CnxSex"] != "A" ) ? "" : "AND _anonyme = 'O' " ;
	$Query .= ( $sort == "O" )
		? "order by _ident"
		: "order by _order";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$i = 0;
	while ( $row ) {
		$color  = ( $i++ % 2 ) ? "item" : "menu" ;

		$row[1] = myurlencode($row[1]);

		$access = ( $row[3]  == "N" ) ? "invisible" : "visible" ;
		$alt    = ( $row[3]  == "N" ) ? "X" : "O" ;
		$icon   = ( $_SESSION["CnxAdm"] == 255 )
			? "<a href=\"index.php?cmde=setsubmenu&amp;ident=$row[4]&amp;access=$access\"><img src=\"".$_SESSION["ROOTDIR"]."/images/$access.gif\" title=\"$access\" alt=\"$alt\" /></a>"
			: "<img src=\"".$_SESSION["CfgPuce"]."\" title=\"\" alt=\"*\" />" ;

		if ( $_SESSION["CnxAdm"] == 255 OR $access == "visible" ) {
			// lien externe
			$href    = ( strstr($row[1], "http://") != "" OR strstr($row[1], "https://") != "" )
				? $row[1]
				: "index.php?$row[1]" ;

			$target  = ( strstr($row[1], "http://") != "" OR strstr($row[1], "https://") != "" )
				? "onclick=\"window.open(this.href, '_blank'); return false;\""
				: "" ;

			$mytext .= "<p class=\"$color\">$icon <a href=\"$href\" title=\"$href\" $target>$row[0]</a></p>";
			}

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	return $mytext;
}
//---------------------------------------------------------------------------
function menu7($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$IDpoll = ( $_POST["IDpoll"] )
		? (int) $_POST["IDpoll"]
		: (int) $_GET["IDpoll"] ;

	// fermeture du sondage
	if ( $IDpoll AND $_GET["cmde"] == "close" ) {
		$Query  = "update sondage_data ";
		$Query .= "set _visible = 'N' ";
		$Query .= "where _IDpoll = '$IDpoll' ";
		$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND _IDmod = '".$_SESSION["CnxID"]."'";

		mysqli_query($mysql_link, $Query);
		}

	// lecture du sondage
	$Query  = "select _IDpoll, _title, _single, _result from sondage_data ";
	$Query .= "where _IDdata = '0' AND _lang = '".$_SESSION["lang"]."' ";
	$Query .= "AND (_IDgrpwr & ".pow(2, $_SESSION["CnxGrp"] - 1).") AND _visible = 'O' ";
	$Query .= "order by _IDpoll desc limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	// attention aux variables globales du module du sondage
	$IDpoll = (int) $row[0];
	$single = $row[2];
	$end    = $row[3];

	// le nombre total de votes
	$result = mysqli_query($mysql_link, "select _ID from sondage_vote where _IDpoll = '$IDpoll'");
	$nbtot  = ( $result ) ? mysqli_num_rows($result) : 0 ;

	$mytext = ( !$IDpoll )
		? "<p style=\"margin:0px; text-align:center;\">". $msg->read($PAGE_POLL) ."</p>"
		: "<p style=\"margin:0px; text-align:center; background-color:#eeeeee;\" class=\"x-small\">$row[1]</p>" ;

	$mytext .= "
		      <form id=\"vote\" action=\"index.php\" method=\"post\">
				<p class=\"hidden\"><input type=\"hidden\" name=\"item\"   value=\"99\" /></p>
				<p class=\"hidden\"><input type=\"hidden\" name=\"IDpoll\" value=\"$IDpoll\" /></p>
				<p class=\"hidden\"><input type=\"hidden\" name=\"IDdata\" value=\"0\" /></p>";

	// affichage des questions du sondage
	$result = mysqli_query($mysql_link, "select _IDq, _q from sondage_items where _IDpoll = '$IDpoll' order by _IDq asc");
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	$mytext .= ( mysqli_affected_rows($mysql_link) ) ? "<table class=\"width100\">" : "" ;

	$i = 0;
	while ( $row ) {
		$mytext .= ( $single == "O" )
      		? "
 				<tr>
					<td style=\"width:3%;\"><label for=\"vote_rb_$i\"><input type=\"radio\" id=\"vote_rb_$i\" name=\"rb\" value=\"$row[0]\" /></label></td>
					<td style=\"width:97%;\" class=\"valign-middle\"><span class=\"x-small\">$row[1]</span></td>
				</tr>"
      		: "
	      		<tr>
					<td style=\"width:3%;\"><label for=\"vote_rb_$i\"><input type=\"checkbox\" id=\"vote_rb_$i\" name=\"cb[]\" value=\"$row[0]\" /></label></td>
					<td style=\"width:97%;\" class=\"valign-middle\"><span class=\"x-small\">$row[1]</span></td>
				</tr>" ;

		$i++;
		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	$mytext .= ( mysqli_affected_rows($mysql_link) ) ? "</table>" : "" ;

	// on teste si l'utilisateur a déjà voté
	$text    = $msg->read($PAGE_VOTED);
	$result  = mysqli_query($mysql_link, "select _ID from sondage_vote where _IDpoll = '$IDpoll' AND _ID = '".$_SESSION["CnxID"]."'");

	// n'a pas voté OU
	// anonyme (ie : organisme)
	if ( !mysqli_affected_rows($mysql_link) OR $_SESSION["CnxSex"] == "A" )
		// attention au sondage fermé
		$text = ( $IDpoll )
			? "<input type=\"submit\" value=\"".$msg->read($PAGE_VOTE)."\" name=\"submit\" style=\"font-size:9px;\" />"
			: "&nbsp;" ;

	$mytext .= "<hr style=\"width:80%;\" />";

	// on tire un lien uniquement si il y a eu un vote
	// et qu'il est possible de voir les résultats avant la clôture
	// ou qu'il n'y a aucun sondage ouvert
	$mytext .= "<p style=\"margin-top:4px; margin-bottom:0px; text-align:center;\">$text<br/>";

	$mytext .= ( ($nbtot AND $end == "N") OR !$IDpoll )
     		? "<span class=\"x-small\"><a href=\"index.php?item=99&amp;IDdata=0&amp;IDpoll=$IDpoll\">".$msg->read($PAGE_RESULT)."</a></span><br/>"
     		: "<span class=\"x-small\">".$msg->read($PAGE_RESULT)."</span><br/>" ;

	if ( $IDpoll )
     		$mytext .= "<span class=\"x-small\">".$msg->read($PAGE_NBPOLL, strval($nbtot))."</span>";

	$mytext .= "</p>";
	$mytext .= "</form>";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu9($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	require_once "msg/".$_SESSION["lang"]."/ephemeride.php";

	$saint = strtok(ephemeride(date("d"), date("m")), "|");
	$event = strtok("|");

	if ( $event ) {
		$event = trim(addslashes($event));
		$over  = "<span>$event</span>";
		}
	else
		$over = "";

	// quel jour est-il Madame Persil ?
	$mois = explode(",", $msg->read($PAGE_MONTH));

	$mytext = "
	      <p style=\"text-align:center; margin:0;\">
		".$msg->read($PAGE_TODAY)."
		<a href=\"#\" class=\"overlib\"><img src=\"".$_SESSION["ROOTDIR"]."/images/calendar/".date("d").".gif\" title=\"\" alt=\"$event\" />$over</a>
		<strong>". $mois[date("n") - 1] ."</strong> ".$msg->read($PAGE_FEST)."
		</p>

		<p style=\"margin-top:5px; margin-bottom:5px; text-align:center;\"><em>$saint</em></p>";

	$Query  = "select _ident, _link, _backoffice, _visible, _IDsubmenu ";
	$Query .= "from config_submenu ";
	$Query .= "where _IDmenu = '$idmenu' ";
	$Query .= "AND _lang= '".$_SESSION["lang"]."' ";
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
	$Query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND _visible = 'O' " ;
	$Query .= ( $_SESSION["CnxSex"] != "A" ) ? "" : "AND _anonyme = 'O' " ;
	$Query .= "order by _ident";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	while ( $row ) {
		$row[1] = myurlencode($row[1]);

		$return = mysqli_query($mysql_link, "select _IDdata from humour_data where _ident = '$row[0]' limit 1");
		$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

		$access = ( $row[3]  == "N" ) ? "invisible" : "visible" ;
		$alt    = ( $row[3]  == "N" ) ? "X" : "O" ;
		$open   = ( $_SESSION["CnxAdm"] == 255 )
			? "<a href=\"index.php?cmde=setsubmenu&amp;ident=$row[4]&amp;access=$access\"><img src=\"".$_SESSION["ROOTDIR"]."/images/$access.gif\" title=\"$access\" alt=\"$alt\" /></a>"
     			: "<img src=\"".$_SESSION["CfgPuce"]."\" title=\"\" alt=\"*\" />" ;

		if ( $_SESSION["CnxAdm"] == 255 OR $access == "visible" )
			$mytext .= "
				<p style=\"margin-top:0px; margin-bottom:0px; text-align:center;\">$open
				<a href=\"#\" onclick=\"popWin('".$_SESSION["ROOTDIR"]."/humour.php?IDdata=$myrow[0]&amp;lang=".$_SESSION["lang"]."', '450', '150'); return false;\">
				$row[0]</a>
				</p>";

		$row = remove_magic_quotes(mysqli_fetch_row($result));
		}

	return $mytext;
}
//---------------------------------------------------------------------------
function menu10($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$mytext = "
		<form id=\"search\" action=\"index.php\" method=\"post\">
			<p class=\"hidden\"><input type=\"hidden\" name=\"item\"    value=\"91\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"cmde\"    value=\"find\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"subject\" value=\"ON\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"texte\"   value=\"ON\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"rb\"      value=\"1\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"xrb\"     value=\"1\" /></p>
			<p class=\"hidden\"><input type=\"hidden\" name=\"nbmsg\"   value=\"50\" /></p>

		      <p style=\"text-align:center; margin:0px;\" class=\"x-small\">". $msg->read($PAGE_QUICK) ."</p>

		      <table class=\"width100\">
		        <tr>
		          <td>

				<table class=\"width100\">
			        <tr>
			          <td class=\"align-center\">
					<input style=\"font-size: 11px; color: #000000; font-family: Arial\"
					onblur=\"if ( this.value == '' ) this.value = '".$msg->read($PAGE_KEYWORD)."';\"
			            onfocus=\"if ( this.value == '".$msg->read($PAGE_KEYWORD)."' ) this.value = '';\"
			            size=\"16\" value=\"".$msg->read($PAGE_KEYWORD)."\" name=\"words\" />
			          </td>
			          <td class=\"align-center\">
					<img src=\"".$_SESSION["ROOTDIR"]."/images/search.gif\" title=\"".$msg->read($PAGE_MYKEYWORD)."\" alt=\"".$msg->read($PAGE_MYKEYWORD)."\" />
			          </td>
			        </tr>

			        <tr>
			          <td class=\"align-center\">
					<label for=\"rub\">
					<select style=\"font-size: 11px;\" id=\"rub\" name=\"rub\">
				            <option value=\"0\" selected=\"selected\">".$msg->read($PAGE_TOPIC)."</option>
				            <option value=\"1\">".$msg->read($PAGE_FLASH)."</option>
				            <option value=\"2\">".$msg->read($PAGE_RESOURCE)."</option>
				            <option value=\"3\">".$msg->read($PAGE_FORUM)."</option>
			      	      <option value=\"4\">".$msg->read($PAGE_GALLERY)."</option>
			            	<option value=\"5\">".$msg->read($PAGE_ARTICLE)."</option>
					</select>
					</label>
			          </td>
			          <td class=\"align-center\">
					<input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/go.gif\" title=\"".$msg->read($PAGE_SENDSEARCH)."\" alt=\"".$msg->read($PAGE_SENDSEARCH)."\" />
			          </td>
			        </tr>
				</table>

		          </td>
		        </tr>
		      </table>

		</form>";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu12($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$user = sizeofSessionID();

	$msg->isPlural = (bool) ( $user > 1 );

	$mytext  = "<p style=\"margin-top: 0px; margin-bottom: 0px; text-align:center;\">";
	$mytext .= $msg->read($PAGE_CURENTLY)." ";
	$mytext .= ( $user > 1 )
		? "$user <a href=\"#\" onclick=\"popWin('".$_SESSION["ROOTDIR"]."/user_connect.php?sid=".$_SESSION["sessID"]."&amp;lang=".$_SESSION["lang"]."', '450', '300'); return false;\">".$msg->read($PAGE_MEMBER)."</a>"
		: "$user ".$msg->read($PAGE_MEMBER) ;
	$mytext .= " <img src=\"".$_SESSION["ROOTDIR"]."/images/whosonline.gif\" title=\"\" alt=\"\" /><br/>".$msg->read($PAGE_ONLINE);

	$user = sizeofSessionID("", "O");

	$msg->isPlural = (bool) ( $user > 1 );

	if ( $user )
		$mytext .= "<br/>".$msg->read($PAGE_ANONYMOUS, strval($user));

	$word = ( $user ) ? $msg->read($PAGE_AND) : $msg->read($PAGE_WITH) ;
	$user = sizeofSessionID("N");

	$msg->isPlural = (bool) ( $user > 1 );

	if ( $user )
		$mytext .= "<br/>".$msg->read($PAGE_GHOST, Array($word, strval($user)));

	list($iscreat, $nil) = explode(":", $AUTHUSER);
	if ( $iscreat )
		$mytext .= "<br/><span class=\"x-small\">[<a href=\"index.php?item=1000&amp;cmde=new\">". $msg->read($PAGE_CREATACCOUNT) ."</a>]</span>";

	$mytext.= "</p>";

	if ( $GEOLOC )
		$mytext.= "
			<p style=\"margin-top: 15px; margin-bottom: 0px; text-align:center;\">
			<br/><script type=\"text/javascript\" src=\"http://www.sitegeo.com/carte.js?site=$GEOLOC\"></script>
			</p>";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu13($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	// Get the Browser data
	$array   = find_browser(getenv("HTTP_USER_AGENT"));
	$browser = $array['type'];
	$version = $array['version'];

	// Get the Operating System data
	if( mb_ereg("Win", getenv("HTTP_USER_AGENT"))) $os = "Windows";
	elseif((mb_ereg("Mac", getenv("HTTP_USER_AGENT"))) || (mb_ereg("PPC", getenv("HTTP_USER_AGENT")))) $os = "Mac";
	elseif(mb_ereg("Linux", getenv("HTTP_USER_AGENT"))) $os = "Linux";
	elseif(mb_ereg("FreeBSD", getenv("HTTP_USER_AGENT"))) $os = "FreeBSD";
	elseif(mb_ereg("SunOS", getenv("HTTP_USER_AGENT"))) $os = "SunOS";
	elseif(mb_ereg("IRIX", getenv("HTTP_USER_AGENT"))) $os = "IRIX";
	elseif(mb_ereg("BeOS", getenv("HTTP_USER_AGENT"))) $os = "BeOS";
	elseif(mb_ereg("OS/2", getenv("HTTP_USER_AGENT"))) $os = "OS2";
	elseif(mb_ereg("AIX", getenv("HTTP_USER_AGENT"))) $os = "AIX";
	else $os = "Autre";

	// affichage des infos
	$mytext = "
		<p style=\"margin:0; text-align: center;\" class=\"x-small\">
		".$msg->read($PAGE_IP)."<br/>
		".$_SERVER["REMOTE_ADDR"]."<br/><br/>
		".$msg->read($PAGE_STATION)."<br/>
		"._getHostName($_SESSION["CnxIP"])."<br/><br/>
		".$msg->read($PAGE_BROWSER)."<br/>
		$browser $version <img src=\"".$_SESSION["ROOTDIR"]."/images/stats/". strtolower($browser) .".gif\"  title=\"".strtolower($browser)."\" alt=\"\" /><br/><br/>
		".$msg->read($PAGE_OS)."<br/>
		$os <img src=\"".$_SESSION["ROOTDIR"]."/images/stats/". strtolower($os) .".gif\" title=\"".strtolower($os)."\" alt=\"\" />
		</p>";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu15($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");
	$mytext = "";

	// liste des actualités à afficher
	$query   = "select _ident, _item from marquee ";
	$query  .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' " ;
	$query  .= "order by _IDitem";

	$return  = mysqli_query($mysql_link, $query);
	$myrow   = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	while ( $myrow ) {
		switch ( $myrow[0] ) {
			case "forum" :
				// liste des derniers messages postés
				$mytext .= $msg->read($PAGE_LASTMSG)."<br/>";

				$query   = "select forum_items._IDmsg, forum_items._IDforum, forum_items._ID, forum_items._date, forum_items._title, ";
				$query  .= "forum_data._IDroot, forum_data._title ";
				$query  .= "from forum_items, forum_data ";
				$query  .= "where forum_items._visible = 'O' ";
				$query  .= "AND forum_items._IDforum = forum_data._IDforum ";
				$query  .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (forum_data._IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
				$query  .= "order by forum_items._IDmsg desc ";
				$query  .= "limit $myrow[1]";

				$result  = mysqli_query($mysql_link, $query);
				$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row ) {
					// qui est le modérateur
					$who    = getUserNameByID($row[2]);

					$mailto = ( $_SESSION["CnxID"] != $row[2] AND $_SESSION["CnxSex"] != "A" )
						? "<a href=\"index.php?item=4&amp;IDpost=".$_SESSION["CnxID"]."&amp;IDdst=$row[2]&amp;cmde=post\">$who</a>"
						: $who ;

					// le texte à afficher
					$SBtext[0] = "<a href=\"index.php?item=3&amp;IDforum=$row[1]&amp;IDroot=$row[5]&amp;IDmsg=$row[0]&amp;nbelem=1&amp;pos=1&amp;parent=$row[0]&amp;cmde=show\">$row[4]</a>";
					$SBtext[1] = "<a href=\"index.php?item=3&amp;IDforum=$row[1]&amp;IDroot=$row[5]&amp;cmde=visu\">$row[6]</a>";
					list($SBtext[2], $SBtext[3]) = explode(" ", $row[3]);

					$mytext .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/goto.gif\" title=\"\" alt=\"*\" /> <strong>$SBtext[0]</strong><br/>";
					$mytext .= $msg->read($PAGE_POSTBY, Array($mailto, $SBtext[1])) ."<br/>";
					$mytext .= $msg->read($PAGE_POSTAT, Array($SBtext[2], $SBtext[3])) ."<br/>";

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
				break;

			case "doc" :
				// liste des derniers documents postés
				$mytext .= "<br/>". $msg->read($PAGE_LASTDOC) ."<br/>";

				$query   = "select resource_items._IDitem, resource_items._file, resource_items._ID, resource_items._date, resource_items._title, resource_items._IDcat, resource_items._ver, resource_data._nom, resource_data._IDres ";
				$query  .= "from resource_items, resource_data ";
				$query  .= "where resource_items._visible = 'O' ";
				$query  .= "AND resource_items._IDgroup = '0' ";
				$query  .= "AND resource_items._IDcat = resource_data._IDcat ";
				$query  .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (resource_data._IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
				$query  .= "AND (";
				$query  .= "(resource_data._IDres = '1' AND resource_items._IDgrprd like '% ". $_SESSION["CnxGrp"] ." %') " ;
				$query  .= "OR (resource_data._IDres = '2')";
				$query  .= ")";
				$query  .= "order by resource_items._IDitem desc ";
				$query  .= "limit $myrow[1]";

				$result  = mysqli_query($mysql_link, $query);
				$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row ) {
					// qui est le modérateur
					$who    = getUserNameByID($row[2]);

					$mailto = ( $_SESSION["CnxID"] != $row[2] AND $_SESSION["CnxSex"] != "A" )
						? "<a href=\"".myurlencode("index.php?item=4&IDpost=".$_SESSION["CnxID"]."&IDdst=$row[2]&cmde=post")."\">$who</a>"
						: $who ;

					// quelle est la ressource spécifique
					$query  = "select _titre, _texte from resource ";
					$query .= "where _IDres = '$row[8]' AND _lang = '".$_SESSION["lang"]."' ";
					$query .= "limit 1";

					$sqlret = mysqli_query($mysql_link, $query);
					$data   = ( $sqlret ) ? remove_magic_quotes(mysqli_fetch_row($sqlret)) : 0 ;

					// le texte à afficher
					$path   = stripaccent("$DOWNLOAD/ressources/$data[0]/$row[7]/v$row[6]-$row[1]");
					$target = "onclick=\"window.open(this.href, '_blank'); return false;\"";

					$SBtext[0] = "<a href=\"".myurlencode("index.php?file=$path")."\" $target>$row[4]</a>";
					$SBtext[1] = "<a href=\"".myurlencode("index.php?item=31&IDres=$row[8]&IDcat=$row[5]")."\">$row[7]</a>";
					list($SBtext[2], $SBtext[3]) = explode(" ", $row[3]);

					$mytext .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/goto.gif\" title=\"\" alt=\"*\" /> <strong>$SBtext[0]</strong><br/>";
					$mytext .= $msg->read($PAGE_POSTBY, Array($mailto, $SBtext[1])) ."<br/>";
					$mytext .= $msg->read($PAGE_POSTAT, Array($SBtext[2], $SBtext[3])) ."<br/>";

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
				break;

			case "flash" :
				// liste des derniers flash-infos postés
				$mytext .= "<br/>".$msg->read($PAGE_LASTNEWS)."<br/>";

				$query   = "select distinctrow flash_data._IDflash, flash_data._ID, flash_data._date, flash_data._title, flash._title ";
				$query  .= "from flash, flash_data ";
				$query  .= "where flash._lock = 'N' AND flash._private = 'N' AND flash._type = 'F' ";
				$query  .= "AND flash_data._IDflash = flash._IDflash ";
				$query  .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (flash._IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
				$query  .= "order by flash_data._IDinfos desc ";
				$query  .= "limit $myrow[1]";

				$result  = mysqli_query($mysql_link, $query);
				$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row ) {
					// qui est le modérateur
					$who    = getUserNameByID($row[1]);

					$mailto = ( $_SESSION["CnxID"] != $row[2] AND $_SESSION["CnxSex"] != "A" )
						? "<a href=\"index.php?item=4&amp;IDpost=".$_SESSION["CnxID"]."&amp;IDdst=$row[1]&amp;cmde=post\">$who</a>"
						: $who ;

					// le texte à afficher
					$SBtext[0] = "$row[3]";
					$SBtext[1] = "<a href=\"index.php?item=0&amp;IDflash=$row[0]\">$row[4]</a>";
					list($SBtext[2], $SBtext[3]) = explode(" ", $row[2]);

					$mytext .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/goto.gif\" title=\"\" alt=\"*\" /> <strong>$SBtext[0]</strong><br/>";
					$mytext .= $msg->read($PAGE_POSTBY, Array($mailto, $SBtext[1])) ."<br/>";
					$mytext .= $msg->read($PAGE_POSTAT, Array($SBtext[2], $SBtext[3])) ."<br/>";

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
				break;

			case "fil" :
				// liste des dernières actus postées
				$mytext .= "<br/>".$msg->read($PAGE_LASTUPDATE)."<br/>";

				$query   = "select flash_fil._IDfil, flash_fil._ID, flash_fil._date, flash_fil._title, flash_fil._IDflash, flash._title ";
				$query  .= "from flash, flash_fil ";
				$query  .= "where flash_fil._IDflash ";
				$query  .= "AND flash_fil._IDflash = flash._IDflash ";
				$query  .= "order by flash_fil._IDfil desc ";
				$query  .= "limit $myrow[1]";

				$result  = mysqli_query($mysql_link, $query);
				$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row ) {
					// qui est le modérateur
					$who    = getUserNameByID($row[1]);

					$mailto = ( $_SESSION["CnxID"] != $row[1] AND $_SESSION["CnxSex"] != "A" )
						? "<a href=\"index.php?item=4&amp;IDpost=".$_SESSION["CnxID"]."&amp;IDdst=$row[1]&amp;cmde=post\">$who</a>"
						: $who ;

					// le texte à afficher
					$SBtext[0] = "<a href=\"index.php?item=15&amp;IDflash=$row[4]&amp;IDfil=$row[0]\">$row[3]</a>";
					$SBtext[1] = "<a href=\"index.php?item=15&amp;IDflash=$row[4]\">$row[5]</a>";
					list($SBtext[2], $SBtext[3]) = explode(" ", $row[2]);

					$mytext .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/goto.gif\" title=\"\" alt=\"*\" /> <strong>$SBtext[0]</strong><br/>";
					$mytext .= $msg->read($PAGE_POSTBY, Array($mailto, $SBtext[1])) ."<br/>";
					$mytext .= $msg->read($PAGE_POSTAT, Array($SBtext[2], $SBtext[3])) ."<br/>";

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
				break;

			case "galerie" :
				// liste des dernières photothèques
				$mytext .= "<br/>".$msg->read($PAGE_LASTGALLERY)."<br/>";

				$query   = "select distinctrow ";
				$query  .= "gallery._IDgal, gallery._title, gallery_data._IDmod, gallery_data._title, gallery_data._IDdata, gallery_data._date ";
				$query  .= "from gallery, gallery_data ";
				$query  .= "where gallery_data._IDgroup = '0' ";
				$query  .= "AND gallery._IDgal = gallery_data._IDgal ";
				$query  .= "AND gallery._lang = '".$_SESSION["lang"]."' ";
				$query  .= ( $_SESSION["CnxAdm"] != 255 )
					? "AND (gallery_data._IDmod = '".$_SESSION["CnxID"]."' OR (gallery_data._visible = 'O' AND (gallery_data._IDgrprd & ". pow(2, $_SESSION["CnxGrp"] - 1) ."))) "
					: "" ;
				$query  .= "order by gallery_data._IDdata desc ";
				$query  .= "limit $myrow[1]";

				$result  = mysqli_query($mysql_link, $query);
				$row     = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row ) {
					// qui est le modérateur
					$who    = getUserNameByID($row[2]);

					$mailto = ( $_SESSION["CnxID"] != $row[2] AND $_SESSION["CnxSex"] != "A" )
						? "<a href=\"index.php?item=4&amp;IDpost=".$_SESSION["CnxID"]."&amp;IDdst=$row[2]&amp;cmde=post\">$who</a>"
						: $who ;

					// le texte à afficher
					$SBtext[0] = "<a href=\"index.php?item=5&amp;cmde=visu&amp;IDgroup=0&amp;IDgal=$row[0]&amp;IDdata=$row[4]\">$row[3]</a>";
					$SBtext[1] = "<a href=\"index.php?item=5&amp;IDgroup=0&amp;IDgal=$row[0]\">$row[1]</a>";
					list($SBtext[2], $SBtext[3]) = explode(" ", $row[5]);

					$mytext .= "<img src=\"".$_SESSION["ROOTDIR"]."/images/goto.gif\" title=\"\" alt=\"*\" /> <strong>$SBtext[0]</strong><br/>";
					$mytext .= $msg->read($PAGE_POSTBY, Array($mailto, $SBtext[1])) ."<br/>";
					$mytext .= $msg->read($PAGE_POSTAT, Array($SBtext[2], $SBtext[3])) ."<br/>";

					$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
				break;

			default :
				break;
			}

		$myrow = mysqli_fetch_row($return);
		}

	return "<div class=\"x-small\" style=\"text-align:center;\">$mytext</div>";
}
//---------------------------------------------------------------------------
function menu17($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$mytext = "";

	require "campus_content.php";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu18($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg     = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	// recherche du email de l'administrateur
	$return  = mysqli_query($mysql_link, "select _email from user_id where _adm = '255' order by _ID limit 1");
	$myrow   = ( $return ) ? mysqli_fetch_row($return) : 0 ;

	$mytext  = "<p class=\"x-small\" style=\"margin:0;\">".$msg->read($PAGE_IDEA)."</p>";
	$mytext .= "<p class=\"x-small\">".$msg->read($PAGE_WELCOME)."</p>";
	$mytext .= "<p class=\"x-small\" style=\"margin:0; text-align:center;\">";
	$mytext .= "<a href=\"mailto:$myrow[0]\">".$msg->read($PAGE_SEND)."</a>";
	$mytext .= "</p>";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu19($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$mytext = "";

	require "egroup_content.htm";

	return $mytext;
}
//---------------------------------------------------------------------------
function menu21($idmenu)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg     = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$mytext  = "<form id=\"donate\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">";
	$mytext .= "<p><input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\" /></p>";
	$mytext .= "<p><input type=\"hidden\" name=\"encrypted\" value=\"-----BEGIN PKCS7-----MIIHPwYJKoZIhvcNAQcEoIIHMDCCBywCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYATHYBxEQcFNXVYo5aVn/Ik0x/f2GODCyHc4pKwdzgYACl/tHr5D1oFPHmPVGstcOLc2ayC2jvEoAywdxmTrmY/FGb+fZJ+JcNq14oOT4y2gtTynMHILBkaaEZnxSd0yc3uITqCfYBoUNO8WQUYKYJNdRkfjvTC/+6bAI9zsb5t7TELMAkGBSsOAwIaBQAwgbwGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIJ/t3lq9NveyAgZgqJcMTeaohYUAqbzp3HqoAm8Bz9/kud38O3irkqGhJwWKkI6Kn0Y+X0T7F+gC3YavXKkxqWUwiG2GKbkz3UFT0/cHV1uQ0d8jK5NuP2y/SSPiOMJyRAKMn5eizJ8auf91POyeSSWwThLbN//42w9dCtY1PqyPSFDuYADyg0OE+PT64rLm7VJfAcDDAa18c+vvLb3dLsZ+fq6CCA4cwggODMIIC7KADAgECAgEAMA0GCSqGSIb3DQEBBQUAMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTAeFw0wNDAyMTMxMDEzMTVaFw0zNTAyMTMxMDEzMTVaMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAwUdO3fxEzEtcnI7ZKZL412XvZPugoni7i7D7prCe0AtaHTc97CYgm7NsAtJyxNLixmhLV8pyIEaiHXWAh8fPKW+R017+EmXrr9EaquPmsVvTywAAE1PMNOKqo2kl4Gxiz9zZqIajOm1fZGWcGS0f5JQ2kBqNbvbg2/Za+GJ/qwUCAwEAAaOB7jCB6zAdBgNVHQ4EFgQUlp98u8ZvF71ZP1LXChvsENZklGswgbsGA1UdIwSBszCBsIAUlp98u8ZvF71ZP1LXChvsENZklGuhgZSkgZEwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tggEAMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEAgV86VpqAWuXvX6Oro4qJ1tYVIT5DgWpE692Ag422H7yRIr/9j/iKG4Thia/Oflx4TdL+IFJBAyPK9v6zZNZtBgPBynXb048hsP16l2vi0k5Q2JKiPDsEfBhGI+HnxLXEaUWAcVfCsQFvd2A1sxRr67ip5y2wwBelUecP3AjJ+YcxggGaMIIBlgIBATCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwCQYFKw4DAhoFAKBdMBgGCSqGSIb3DQEJAzELBgkqhkiG9w0BBwEwHAYJKoZIhvcNAQkFMQ8XDTA2MDgyMjIwMTc1MlowIwYJKoZIhvcNAQkEMRYEFMltaDQG9vZPkLI9LofNhJuqbg+XMA0GCSqGSIb3DQEBAQUABIGAJwC05K0TF8iXwPNeVF9U4YDd3hA3Bi7cFDSA1Pa/B+YDQrcFGX34O7Xmw0QjODIskbnsDYxXRHzc8LV53AXR1x5OVP5CcmdL2dVlXhSh2hCOiA8reqpnRBobVA59+wl2vdZXBDRIhMLUmRcDPGuTDzgAiIOC3KZ4+Z624h2V1SY=-----END PKCS7-----\" /></p>";
	$mytext .= "<p style=\"text-align: center; margin: 0 0 0 0px;\"><input type=\"image\" src=\"".$_SESSION["ROOTDIR"]."/images/paypal.png\" name=\"submit\" alt=\"PayPal\" /></p>";
	$mytext .= "</form>";

	$mytext .= "<p class=\"x-small\" style=\"text-align: center; margin: 0 5 0 0px;\">".$msg->read($PAGE_SUPPORT)."</p>";

	return $mytext;
}
//---------------------------------------------------------------------------













function setmenu($query)
{
	require "globals.php";

	require "msg/page.php";
	require_once "include/TMessage.php";

	$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

	$return = mysqli_query($mysql_link, $query);
	$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

	$nbcount = ( $return ) ? mysqli_affected_rows($mysql_link) : 0 ;

	$count   = 1;
	while ( $myrow ) {
		// initialisation
		$idmenu = (int) $myrow[0];
		$mymenu = str_replace($keywords_search, $keywords_replace, $myrow[1]);

		$access = isMenuVisible($idmenu);
		$alt    = ( $access == "visible" ) ? "O" : "X" ;
		$myopen = ( $_SESSION["CnxAdm"] == 255 )
			? "<a href=\"index.php?cmde=setmenu&amp;ident=$idmenu&amp;access=$access\"><img src=\"".$_SESSION["ROOTDIR"]."/images/$access.gif\" title=\"\" alt=\"$alt\" /></a>"
	          	: "&nbsp;" ;

		$expand = isUserMenuVisible($idmenu);

		$mytext = "";
		$href   = $myrow[5];
		$ico_titre = "";

		if ( $_SESSION["CnxAdm"] == 255 OR $access == "visible" )
			switch ( $idmenu ) {
				case "1" :
					// echo "<!-- le menu principal -->";
					$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					break;

				case "2" :
					// echo "<!-- le menu des flash infos -->";
					$mytext = ( $expand == "close" ) ? menu2($idmenu, $myrow[6]) : "" ;
					$ico_titre = "<i class=\"fa fa-info-circle\"></i>";
					break;

				case "3" :
					// echo "<!-- le menu des documents -->";
					$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					break;

				case "4" :
					// echo "<!-- accès entreprises -->";
					$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					break;

				case "5" :
					// echo "<!-- le menu des aides en ligne -->";
					$mytext = ( $expand == "close" ) ? menu5($idmenu, $myrow[6]) : "" ;
					break;

				case "7" :
					// echo "<!-- le sondage -->";

					$mytext = ( $expand == "close" ) ? menu7($idmenu) : "" ;

					// lecture du sondage
					$Query  = "select _IDpoll, _close from sondage_data ";
					$Query .= "where _IDdata = '0' AND _lang = '".$_SESSION["lang"]."' ";
					$Query .= "AND (_IDgrpwr & ".pow(2, $_SESSION["CnxGrp"] - 1).") AND _visible = 'O' ";
					$Query .= "order by _IDpoll desc limit 1";

					$result = mysqli_query($mysql_link, $Query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					$href  .= ( strlen($href) ) ? "&IDpoll=$row[0]" : "" ;
				     	$myopen = ( $_SESSION["CnxAdm"] == 255 AND $row[1] == "0000-00-00 00:00:00" )
				     		? "<a href=\"index.php?IDpoll=$row[0]&amp;cmde=close\"><img src=\"".$_SESSION["ROOTDIR"]."/images/unlocked.gif\" title=\"".$msg->read($PAGE_CLOSEPOLL)."\" alt=\"".$msg->read($PAGE_CLOSEPOLL)."\" /></a>"
				     		: "" ;
					break;

				case "8" :
					// echo "<!--  partie administrateur -->";
					if ( $_SESSION["CnxAdm"] == 255 )
						$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					break;

				case "9" :
					// echo "<!-- éphéméride -->";
					$mytext = ( $expand == "close" ) ? menu9($idmenu) : "" ;
					break;

				case "10" :
					// echo "<!-- recherche rapide -->";
					$mytext = ( $expand == "close" ) ? menu10($idmenu) : "" ;
					break;

				case "11" :
					// echo "<!-- le menu des élèves -->";
					$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					break;

				case "12" :
					// echo "<!-- liste des utilisateurs connectés -->";
					$mytext = ( $expand == "close" ) ? menu12($idmenu) : "" ;
					break;

				case "13" :
					// echo "<!-- le menu statistique  -->";
					$mytext = ( $expand == "close" ) ? menu13($idmenu) : "" ;
					break;

				case "14" :
					// echo "<!-- Logiciels Libres -->";
					$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					break;

				case "15" :
					// echo "<!-- les dernières infos -->";
					$mytext = ( $expand == "close" ) ? menu15($idmenu) : "" ;
					break;

				case "16" :
					require_once "calendar.php";

					$mycal_year  = $_POST["mycal_year"] ? $_POST["mycal_year"] : $_GET["mycal_year"] ;
					$mycal_month = $_POST["mycal_month"] ? $_POST["mycal_month"] : $_GET["mycal_month"] ;

					if ( $mycal_year ) {
						$mycal_year  = ( $mycal_month > 11 )
							? ($mycal_year + 1)
							: ($mycal_month < 0 ? $mycal_year - 1 : $mycal_year) ;

						$mycal_month = ( $mycal_month < 0 )
							? 11
							: ($mycal_month % 12) ;
						}

					// echo "<!-- calendrier -->";
					$mytext = ( $expand == "close" )
						? mkcalendar($mycal_year, $mycal_month)
						: "" ;
					break;

				case "17" :
					// echo "<!-- campus virtuel -->";
					$mytext = ( $expand == "close" ) ? menu17($idmenu) : "" ;
					break;

				case "18" :
					// echo "<!-- boîte à idées -->";
					$mytext = ( $expand == "close" ) ? menu18($idmenu) : "" ;
					break;

				case "19" :
					// echo "<!-- e-groupe -->";
					$mytext = ( $expand == "close" ) ? menu19($idmenu) : "" ;
					break;

				case "21" :
					// echo "<!-- donation -->";
					$mytext = ( $expand == "close" ) ? menu21($idmenu) : "" ;
					break;

				case "24" :
					// echo "<!-- Liens -->";
					$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					$ico_titre = "<i class=\"fa fa-external-link-square\"></i>";
					break;

				default :
					// echo "<!-- menu Utilisateurs -->";
					$mytext = ( $expand == "close" ) ? menu($idmenu, $myrow[6]) : "" ;
					break;
				}

		if ( $mytext OR $expand == "open" ) {
			// les liens sur les blocs de menu
			$myopen = ( $_SESSION["CnxAdm"] == 255 )
				? "<a href=\"index.php?cmde=setmenu&amp;ident=$idmenu&amp;access=$access\"><img src=\"".$_SESSION["ROOTDIR"]."/images/$access.gif\" title=\"\" alt=\"$alt\" /></a>"
		          	: "<img src=\"".$_SESSION["ROOTDIR"]."/images/menu/icon/$myrow[4]\" title=\"\" alt=\"*\" style=\"margin-left:2px; margin-top:2px;\" />" ;

			$mylink = ( $_SESSION["CnxAdm"] == 255 )
				? "<a><img src=\"".$_SESSION["ROOTDIR"]."/images/settings.png\" title=\"".$msg->read($PAGE_CONFIG)."\" alt=\"".$msg->read($PAGE_CONFIG)."\" /></a>"
				: "" ;

			if(isset($_SESSION["config"]) && $_SESSION["config"] == "on")
			{
				$tools  = ( $myrow[3] < 0 )
					? "<a href=\"index.php?cmde=setordermenu&amp;ident=$idmenu&amp;order=up\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/left.png\" title=\"".$msg->read($PAGE_LEFT)."\" alt=\"".$msg->read($PAGE_LEFT)."\" /></a> "
					: "<a href=\"index.php?cmde=setordermenu&amp;ident=$idmenu&amp;order=up\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/up.png\" title=\"".$msg->read($PAGE_MENUUP)."\" alt=\"".$msg->read($PAGE_MENUUP)."\" /></a> ";
				$tools  .= ( $myrow[3] < 0 )
					? "<a href=\"index.php?cmde=setordermenu&amp;ident=$idmenu&amp;order=down\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/right.png\" title=\"".$msg->read($PAGE_RIGHT)."\" alt=\"".$msg->read($PAGE_RIGHT)."\" /></a> "
					: "<a href=\"index.php?cmde=setordermenu&amp;ident=$idmenu&amp;order=down\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/down.png\" title=\"".$msg->read($PAGE_MENUDOWN)."\" alt=\"".$msg->read($PAGE_MENUDOWN)."\" /></a> ";
				$tools .= ( $myrow[3] < 0 )
					? "<a href=\"index.php?cmde=setordermenu&amp;ident=$idmenu&amp;order=left\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/bottom.png\" title=\"".$msg->read($PAGE_LEFT)."\" alt=\"«\" /></a> "
					: "<a href=\"index.php?cmde=setordermenu&amp;ident=$idmenu&amp;order=right\"><img src=\"".$_SESSION["ROOTDIR"]."/images/menu/top.png\" title=\"".$msg->read($PAGE_TOP)."\" alt=\"»\" /></a> " ;
				$tools .= "<a href=\"index.php?item=21&amp;cmde=submenu&amp;IDconf=".$_SESSION["CfgID"]."&amp;IDmenu=$idmenu\"><img src=\"".$_SESSION["ROOTDIR"]."/images/addrecord.gif\" title=\"".$msg->read($PAGE_ADDITEM)."\" alt=\"+\" /></a> ";
				$tools .= "<a href=\"index.php?item=21&amp;cmde=usrmenu&amp;IDconf=".$_SESSION["CfgID"]."&amp;IDmenu=$idmenu\"><img src=\"".$_SESSION["ROOTDIR"]."/images/stats/post.gif\" title=\"".$msg->read($PAGE_UPDATEITEM)."\" alt=\"!\" /></a> ";
				$tools .= ( strlen($href) )
					? "<a href=\"".myurlencode("index.php?$href")."\"><img src=\"".$_SESSION["ROOTDIR"]."/images/tools.gif\" title=\"".$msg->read($PAGE_BACKOFFICE)."\" alt=\"?\" /></a>"
					: "" ;

				$tools  = ( $_SESSION["CnxAdm"] == 255 && isset($_SESSION["config"]) && $_SESSION["config"] == "on") ? "<div class=\"cfg\"><span>$tools</span></div>" : "" ;
			}

			if($myrow[3] < 0)
			{
				$mymenu = "<a href=\"#\" id=\"drop$count\" role=\"button\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" data-hover=\"dropdown\" data-delay=\"100\" data-close-others=\"false\">$mymenu<b class=\"caret\"></b></a>";

				if(isset($_SESSION["config"]) && $_SESSION["config"] == "on")
				{
					$mymenu .= 	"<div style=\"float:right; position: absolute; top: 10px; right: -5px\" class=\"backoff\">
									$mylink
									$tools
									</div>";
				}
			}
			else
			{
				// $mymenu = "<a href=\"index.php?cmde=setusermenu&amp;ident=$idmenu&amp;access=$expand&reload=on\">$ico_titre $mymenu</a>";
			}

			// HAUT
			if($myrow[3] < 0)
			{
				echo "<li class=\"dropdown\">
                      $mymenu";

				if ( $mytext AND $access == "visible" )
				{

					echo "<ul class=\"dropdown-menu\" role=\"menu\">$mytext</ul>
                    </li>";
				}
			}
			else	// GAUCHE
			{
				// echo "<div class=\"popover\" style=\"display: block; position: relative; max-width: 330px; width: 330px; margin-bottom: 15px\">
				// 		<ul class=\"nav nav-list\">
				// 			<li class=\"nav-header\" style=\"padding-top: 0px;\">";
				echo "<li class=\"nav-header\">";
							if(isset($_SESSION["config"]) && $_SESSION["config"] == "on")
							{
								echo "<div style=\"float:right;\" class=\"backoff\">
								$mylink
								$tools
								</div>";
							}

							// echo "
							// 	<h3 class=\"popover-title\">
							// 	$mymenu
							// 	</h3>
							// </li>";
							echo "
								<li class=\"nav-header left-menu-subtitle\">— ".$mymenu." —</li>";

				if ( $mytext AND $access == "visible" ) {
				//print("<div class=\"boxcontent\" style=\"margin:1px; padding:2px; background-color:".$_SESSION["CfgBgcolor"].";\">");

				if ( $myrow[2] == "O" )
				{
					echo "
				          <div style=\"height:200px; text-align:center;\">
						<marquee behavior=\"scroll\" direction=\"up\" scrollamount=\"2\" scrolldelay=\"5\" onmouseover=\"this.stop()\" onmouseout=\"this.start()\" class=\"align-center\">
						$mytext
						</marquee>
				          </div>";
				}
				else
				{
					echo $mytext;
				}

				//print("</div>");
				}
				// echo '</ul></div>';
			}
		}	// endif open

		$count++;
		$myrow = remove_magic_quotes(mysqli_fetch_row($return));
		}
}
//---------------------------------------------------------------------------
?>







<?php

  function getMenu($idmenu_global = 0, $showSelectedMenu = true) {
    global $mysql_link;
    require "globals.php";

		$msg    = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/page.php");

    $query  = "select _IDmenu, _ident, _marquee, _order, _img, _backoffice, _sort from config_menu ";
		$query .= "where _activate = 'O' ";
		// $query .= "AND _lang = '".$_SESSION["lang"]."' AND _order < 0 ";
		$query .= "AND _lang = '".$_SESSION["lang"]."' ";
		$query .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
    $query .= "AND _visible = 'O' ";
		$query .= "order by _order asc";

    $result = mysqli_query($mysql_link, $query);
    while($row = mysqli_fetch_row($result)) {
      $submenu = $active_menu = $show = '';

      // les items du menu
      $query_item  = "select _ident, _link, _image, _visible, _IDsubmenu, _backoffice, _url, _type from config_submenu ";
      $query_item .= 'where _IDmenu = "'.$row[0].'" ';
      $query_item .= "AND _lang= '".$_SESSION["lang"]."' ";
      $query_item .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND (_IDgrprd & ".pow(2, $_SESSION["CnxGrp"] - 1).") " ;
      $query_item .= ( $_SESSION["CnxAdm"] == 255 ) ? "" : "AND _visible = 'O' " ;
      $query_item .= "AND _visible = 'O' ";
      // $query_item .= ($_SESSION["CnxSex"] != "A") ? "" : "AND _anonyme = 'O' " ;
      $query_item .= "order by _order";

      $result_item = mysqli_query($mysql_link, $query_item);
      while($row_item = mysqli_fetch_row($result_item)) {
        if ($idmenu_global == $row_item[4]) $active = $active_menu = 'active'; else $active = '';

				if ($row_item[7] == 1) $submenu .= '<h6 class="collapse-header">'.$msg->getTrad($row_item[0]).'</h6>';
        else $submenu .= '<a class="collapse-item '.$active.'" href="index.php?'.$row_item[1].'&idmenu='.$row_item[4].'">'.$msg->getTrad($row_item[0]).'</a>';
      }

      echo '<li class="nav-item '.$active_menu.'">';
      if ($active_menu == 'active') $collapsed = ''; else $collapsed = 'collapsed';
    		echo '<a class="nav-link '.$collapsed.'" href="#" data-toggle="collapse" data-target="#collapse'.$row[0].'" aria-expanded="true" aria-controls="collapseUtilities">';
    			echo '<i class="fas fa-fw '.$row[4].'"></i>';
    			echo '<span>'.$msg->getTrad($row[1]).'</span>';
    		echo '</a>';

        if ($active_menu == 'active' && $showSelectedMenu) $show = 'show';
    		echo '<div id="collapse'.$row[0].'" class="collapse '.$show.'" aria-labelledby="heading'.$row[0].'" data-parent="#accordionSidebar">';
    			echo '<div class="bg-white py-2 collapse-inner rounded">';

          echo $submenu;

    				// echo '<h6 class="collapse-header">Custom Utilities:</h6>';
    				// echo '<a class="collapse-item" href="utilities-color.html">Colors</a>';
    				// echo '<a class="collapse-item" href="utilities-border.html">Borders</a>';
    				// echo '<a class="collapse-item" href="utilities-animation.html">Animations</a>';
    				// echo '<a class="collapse-item" href="utilities-other.html">Other</a>';
    			echo '</div>';
    		echo '</div>';
    	echo '</li>';
    }



  }



?>
