<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007-2008 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : share.php
 *		projet   : page de mutualisation des ressources
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 27/12/07
 *		modif    : 
 */


$key       = @$_POST["key"];
$url       = @$_POST["url"];
$title     = @$_POST["title"];
$desc      = @$_POST["desc"];
$format    = @$_POST["format"];
$size      = @$_POST["size"];
$author    = @$_POST["author"];
$resource  = @$_POST["resource"];
$category  = @$_POST["category"];
$license   = @$_POST["license"];
$usability = @$_POST["usability"];
$mylang    = @$_POST["mylang"];
$level     = @$_POST["level"];

$IDlang    = @$_POST["IDlang"]
	? $_POST["IDlang"]
	: @$_GET["IDlang"] ;
$IDres     = @$_POST["IDres"]
	? $_POST["IDres"]
	: @$_GET["IDres"] ;
$IDcat     = @$_POST["IDcat"]
	? $_POST["IDcat"]
	: @$_GET["IDcat"] ;
$IDfmt     = @$_POST["IDfmt"]
	? $_POST["IDfmt"]
	: @$_GET["IDfmt"] ;
$text      = @$_POST["text"]
	? $_POST["text"]
	: @$_GET["text"] ;
$sort      = @$_POST["sort"]
	? $_POST["sort"]
	: @$_GET["sort"] ;

$skpage    = ( @$_GET["skpage"] )		// n° de la page affichée
	? (int) $_GET["skpage"]
	: 1 ;
$skshow    = ( @$_GET["skshow"] )		// n° du flash info
	? (int) $_GET["skshow"]
	: 1 ;
?>


<body style="background-color:#FFFFFF; margin-top:5px; margin-left:10%; width:80%;">

<?php
	require "msg/server.php";
	require_once "include/TMessage.php";

	$msg = new TMessage("msg/".$_SESSION["lang"]."/server.php");
?>


<div style="text-align: center;"><img src="download/logos/<?php echo rawurlencode($_SESSION["CfgIdent"]) ?>/logo01.jpg" title="" alt="" /></div>

<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($SERVER_P2P, $WEBSITE)); ?>
	</div>
</div>

<div class="maincontent">

	<form id="formulaire" action="" method="post">
<?php
	require_once "include/filext.php";
	require_once "include/urlencode.php";

	//--- mise à jour
	$result = mysqli_query($mysql_link, "select _IDdata from p2p_data where _key = '$key' AND _visible = 'O' limit 1");
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

	if ( $row ) {
		// les fichiers autorisés
		$files  = Array();

		$return = mysqli_query($mysql_link, "select distinctrow _ext from config_mime where _visible = 'O'");
		$myrow  = ( $return ) ? mysqli_fetch_row($return) : 0 ;

		while ( $myrow ) {
			$files = array_merge($files, Array($myrow[0]));
			$myrow = mysqli_fetch_row($return);
			}

		// insertion
		$date = date("Y-m-d H:i:s");

		for ($i = 0; $i < count($url); $i++) {
			$Query  = "insert into p2p_items ";
			$Query .= "values(
				'',
				'$row[0]',
				'".addslashes($resource[$i])."',
				'".addslashes($category[$i])."',
				'".addslashes(stripaccent($url[$i]))."',
				'".addslashes($title[$i])."',
				'".addslashes($desc[$i])."',
				'$format[$i]',
				'$size[$i]',
				'$mylang[$i]',
				'".addslashes($license[$i])."',
				'$usability[$i]',
				'$level[$i]',
				'".addslashes($author[$i])."',
				'$date')";

			if ( in_array(extension($url[$i]), $files) )
				mysqli_query($mysql_link, $Query);
			}
		}

	// total des ressources
	mysqli_query($mysql_link, "select _IDitem from p2p_items");

	$nbfile = mysqli_affected_rows($mysql_link);
	$link1  = "server.php?cmde=share&amp;lang=".$_SESSION["lang"];

	// total des établissements
	mysqli_query($mysql_link, "select _IDdata from p2p_data where _visible = 'O'");

	$school = mysqli_affected_rows($mysql_link);
	$link2  = "server.php?cmde=school&amp;lang=".$_SESSION["lang"];

	$msg->isPlural = (bool) ( $school > 1 );

	print("<p style=\"text-align:justify;\">
		". $msg->read($SERVER_AVAILFORMAT, Array($link1, strval($nbfile), strval($school), $link2)) ."
		</p>");

	// choix de la langue
	$result  = mysqli_query($mysql_link, "select distinctrow _lang from p2p_items");
	$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$IDlang  = ( $IDlang == "" ) ? $_SESSION["lang"] : $IDlang ;

	$langID  = "<label for=\"IDlang\">";
	$langID .= "<select id=\"IDlang\" name=\"IDlang\" onchange=\"document.forms.formulaire.submit()\" style=\"font-size:9px;\">";
	while ( $row ) {
		$check   = ( $IDlang == $row[0] ) ? "selected=\"selected\"" : "" ;
		$langID .= "<option value=\"$row[0]\" $check>$row[0]</option>";

		$row     = mysqli_fetch_row($result);
		}
	$langID .= "</select>";
	$langID .= "</label>";

	// choix ressource
	$query  = "select distinctrow _type from p2p_items ";
	$query .= "where _lang = '$IDlang'";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$resID  = "<label for=\"IDres\">";
	$resID .= "<select id=\"IDres\" name=\"IDres\" onchange=\"document.forms.formulaire.submit()\" style=\"font-size:9px;\">";
	$resID .= "<option value=\"\">&nbsp;</option>";
	while ( $row ) {
		$query  = "select _IDitem from p2p_items ";
		$query .= "where _type = '".addslashes($row[0])."' ";
		$query .= "AND _lang = '$IDlang'";

		$return = mysqli_query($mysql_link, $query);
		$count  = ( $return ) ? mysqli_num_rows($return) : 0 ;

		$check  = ( $IDres == $row[0] ) ? "selected=\"selected\"" : "" ;
		$resID .= "<option value=\"$row[0]\" $check>$row[0] ($count)</option>";

		$row    = mysqli_fetch_row($result);
		}
	$resID .= "</select>";
	$resID .= "</label>";

	// catégorie ressource
	$query  = "select distinctrow _cat from p2p_items ";
	$query .= "where _lang = '$IDlang' ";
	$query .= ( $IDres != "" ) ? "AND _type = '$IDres'" : "" ;

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$catID  = "<label for=\"IDcat\">";
	$catID .= "<select id=\"IDcat\" name=\"IDcat\" onchange=\"document.forms.formulaire.submit()\" style=\"font-size:9px;\">";
	$catID .= "<option value=\"\">&nbsp;</option>";
	while ( $row ) {
		$query  = "select _IDitem from p2p_items ";
		$query .= "where _cat = '".addslashes($row[0])."' ";
		$query .= "AND _lang = '$IDlang' ";
		$query .= ( $IDres != "" ) ? "AND _type = '$IDres'" : "" ;

		$return = mysqli_query($mysql_link, $query);
		$count  = ( $return ) ? mysqli_num_rows($return) : 0 ;

		$check  = ( $IDcat == $row[0] ) ? "selected=\"selected\"" : "" ;
		$catID  .= "<option value=\"$row[0]\" $check>$row[0] ($count)</option>";

		$row    = mysqli_fetch_row($result);
		}
	$catID  .= "</select>";
	$catID  .= "</label>";

	// choix format
	$query  = "select distinctrow _format from p2p_items ";
	$query .= "where _lang = '$IDlang' ";
	$query .= ( $IDres != "" ) ? "AND _type = '$IDres'" : "" ;
	$query .= ( $IDcat != "" ) ? "AND _cat = '$IDcat'" : "" ;

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$fmt    = "<label for=\"IDfmt\">";
	$fmt   .= "<select id=\"IDfmt\" name=\"IDfmt\" onchange=\"document.forms.formulaire.submit()\" style=\"font-size:9px;\">";
	$fmt   .= "<option value=\"\">&nbsp;</option>";
	while ( $row ) {
		$query  = "select _IDitem from p2p_items ";
		$query .= "where _format = '$row[0]' ";
		$query .= "AND _lang = '$IDlang' ";
		$query .= ( $IDres != "" ) ? "AND _type = '$IDres'" : "" ;
		$query .= ( $IDcat != "" ) ? "AND _cat = '$IDcat'" : "" ;

		$return = mysqli_query($mysql_link, $query);
		$count  = ( $return ) ? mysqli_num_rows($return) : 0 ;

		$check  = ( $IDfmt == $row[0] ) ? "selected=\"selected\"" : "" ;
		$fmt   .= "<option value=\"$row[0]\" $check>$row[0] ($count)</option>";

		$row    = mysqli_fetch_row($result);
		}
	$fmt   .= "</select>";
	$fmt   .= "</label>";

	// recherche ressource
	$search  = "<label for=\"text\">";
	$search .= "<input type=\"text\" id=\"text\" name=\"text\" value=\"$text\" size=\"30\" style=\"font-size:9px;\" /> ";
	$search .= "<input type=\"image\" src=\"images/search.gif\" title=\"\" alt=\"\" />";
	$search .= "</label>";

	// filtre ressource
	$filter  = "<label for=\"sort\">";
	$filter .= "<input type=\"text\" id=\"sort\" name=\"sort\" value=\"$sort\" size=\"5\" style=\"font-size:9px;\" /> ";
	$filter .= "<input type=\"image\" src=\"images/search.gif\" title=\"\" alt=\"\" />";
	$filter .= "</label>";

	print("
		<table summary=\"\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\">
		  <tr align=\"center\" style=\"background-color:#c0c0c0;\">
                <td style=\"width:1%;\">$langID</td>
                <td style=\"width:1%;\">$resID</td>
                <td style=\"width:1%;\">$catID</td>
                <td style=\"width:1%;\">$fmt</td>
                <td style=\"width:72%;\" align=\"left\">".$msg->read($SERVER_RESOURCE)." $search</td>
                <td style=\"width:14%;\">$filter</td>
                <td style=\"width:10%;\">".$msg->read($SERVER_SIZE)."</td>
		  </tr>
		");

	//--- lecture
	$text   = str_replace("*", "%", trim($text));

	$query  = "select _IDitem, _IDdata, _type, _cat, _url, _title, _text, _size, _lang, _IDlicense, _author, _date, _format, _level ";
	$query .= "from p2p_items ";
	$query .= "where _lang = '$IDlang' ";
	$query .= ( $IDres ) ? "AND _type = '$IDres' " : "" ;
	$query .= ( $IDcat ) ? "AND _cat = '$IDcat' " : "" ;
	$query .= ( $IDfmt ) ? "AND _format = '$IDfmt' " : "" ;
	$query .= ( $text ) ? "AND (_title like '%$text%' OR _text like '%$text%') " : "" ;
	$query .= ( $sort ) ? "AND (_level like '%$sort%') " : "" ;
	$query .= "order by _IDitem desc";

	// détermination du nombre de pages
	$result = mysqli_query($mysql_link, $query);
	$nbelem = ( $result ) ? mysqli_affected_rows($mysql_link) : 0 ;

	$page   = $nbelem;
	$show   = 1;

	if ( $nbelem ) {
		$page  = ( $page % $MAXPAGE )
			? (int) ($page / $MAXPAGE) + 1
			: (int) ($page / $MAXPAGE) ;

		$show  = ( $page % $MAXSHOW )
			? (int) ($page / $MAXSHOW) + 1
			: (int) ($page / $MAXSHOW) ;

		// initialisation
		$i     = 1;
		$first = 1 + (($skpage - 1) * $MAXPAGE);

		// se positionne sur la page ad hoc
		mysqli_data_seek($result, $first - 1);
		$row   = remove_magic_quotes(mysqli_fetch_row($result));

		while ( $row AND $i <= $MAXPAGE ) {
			$bgcol  = ( $i++ % 2 ) ? "item" : "menu" ;

			// établissement
			$return = mysqli_query($mysql_link, "select _ident, _url, _basedir from p2p_data where _IDdata = '$row[1]' limit 1");
			$myrow  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

			// licence
			$return = mysqli_query($mysql_link, "select _text from resource_license where _IDlicense = '$row[9]' AND _lang = '".$_SESSION["lang"]."' limit 1");
			$data   = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

             	// lien des ressources
			$http   = ( strlen($myrow[2]) )
				? "$myrow[1]/$myrow[2]/$row[4]"
				: "$myrow[1]/$row[4]" ;

			$target = "onclick=\"window.open(this.href, '_blank'); return false;\"";

			// lecture de l'auteur de la ressource
			$desc   = "$row[10] @ $row[11]<br/>";
			$desc  .= ( $data ) ? "$data[0]<br/>" : "" ;
			$desc  .= $myrow[0];

			// lecture de l'auteur de la ressource
			$info   = strlen($row[13])
				? "<a href=\"#\" class=\"overlib\"><img src=\"images/reguser.gif\" title=\"\" alt=\"\" /><span>$row[13]</span></a>"
				: "" ;

			print("
				<tr class=\"$bgcol\">
		                <td align=\"center\"><img src=\"images/lang/ico-$row[8].png\" title=\"$row[8]\" alt=\"$row[8]\" /></td>
		                <td>$row[2]</td>
		                <td>$row[3]</td>
		                <td align=\"center\"><img src=\"images/mime/$row[12].gif\" title=\"$row[12]\" alt=\"$row[12]\" /></td>
		                <td>
					<a href=\"".myurlencode($http)."\" class=\"overlib\" $target>$row[5]<span>$desc</span></a><br/>
					<span class=\"x-small\">$row[6]</span>
		                </td>
		                <td align=\"center\">$info</td>
		                <td align=\"center\">". number_format($row[7], 0, ",", " ") ."</td>
				  </tr>
				");

			$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
		}	// endif nbelem

	print("</table>");

	// bouton précédent
	$where = $skshow - 1;

	if ( $skshow == 1 )
		$prev = "";
	else {
		$skpg = 1 + (($skshow - 2) * $MAXSHOW);
		$prev = "[<a href=\"".myurlencode("server.php?cmde=share&IDlang=$IDlang&IDres=$IDres&IDcat=$IDcat&IDfmt=$IDfmt&skpage=$skpg&skshow=$where")."\">". $msg->read($SERVER_PREV) ."</a>]";
		}

	// liens directs sur n° de page
	$start = 1 + (($skshow - 1) * $MAXSHOW);
	$end   = $skshow * $MAXSHOW;

	$choix = ( $skpage == $start )
		? "<img src=\"images/nav_left.gif\" title=\"\" alt=\"\" /><b>$start</b><img src=\"images/nav_right.gif\" title=\"\" alt=\"\" />"
		: "<a href=\"".myurlencode("server.php?cmde=share&IDlang=$IDlang&IDres=$IDres&IDcat=$IDcat&IDfmt=$IDfmt&skpage=$start&skshow=$skshow")."\">$start</a>" ;

	for ($j = $start + 1; $j <= $end AND $j <= $page; $j++)
		$choix .= ( $skpage == $j )
			? "|<img src=\"images/nav_left.gif\" title=\"\" alt=\"\" /><b>$j</b><img src=\"images/nav_right.gif\" title=\"\" alt=\"\" />"
			: "|<a href=\"".myurlencode("server.php?cmde=share&IDlang=$IDlang&IDres=$IDres&IDcat=$IDcat&IDfmt=$IDfmt&skpage=$j&skshow=$skshow")."\">$j</a>" ;

	// bouton suivant
	$where = $skshow + 1;
	$next  = ( $skshow == $show )
		? ""
		: "[<a href=\"".myurlencode("server.php?cmde=share&IDlang=$IDlang&IDres=$IDres&IDcat=$IDcat&IDfmt=$IDfmt&skpage=$j&skshow=$where")."\">". $msg->read($SERVER_NEXT) ."</a>]" ;
?>

	<hr/>
		<?php if ( $nbelem ) print("<div style=\"text-align:center;\">$prev $choix $next</div>"); ?>
	<hr/>

	</form>

</div>
</body>
