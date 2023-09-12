<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2008 by Dominique Laporte(C-E-D@wanadoo.fr)

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
 *		module   : school.php
 *		projet   : page de visualisation des établissements
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 9/02/08
 *		modif    : 
 */


$skpage   = ( @$_GET["skpage"] )		// n° de la page affichée
	? (int) $_GET["skpage"]
	: 1 ;
$skshow   = ( @$_GET["skshow"] )		// n° du flash info
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

<?php
	require_once "include/urlencode.php";
	require_once "include/calendar_tools.php";

	// total des ressources
	mysqli_query($mysql_link, "select _IDitem from p2p_items");

	$nbfile = mysqli_affected_rows($mysql_link);
	$link1  = "server.php?cmde=share&amp;lang=".$_SESSION["lang"];

	// total des établissements
	mysqli_query($mysql_link, "select _IDdata from p2p_data where _visible = 'O' AND _public = 'N'");

	$school = mysqli_affected_rows($mysql_link);
	$link2  = "server.php?cmde=school&amp;lang=".$_SESSION["lang"];

	$msg->isPlural = (bool) ( $school > 1 );

	print("<p style=\"text-align:justify;\">
		". $msg->read($SERVER_AVAILFORMAT, Array($link1, strval($nbfile), strval($school), $link2)) ."
		</p>");

	print("
		<table summary=\"\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\">
		  <tr align=\"center\" style=\"background-color:#c0c0c0;\">
                <td style=\"width:10%;\">".$msg->read($SERVER_CREATE)."</td>
                <td style=\"width:20%;\">".$msg->read($SERVER_IDENT)."</td>
                <td style=\"width:40%;\">".$msg->read($SERVER_ADDRESS)."</td>
                <td style=\"width:10%;\">".$msg->read($SERVER_RESOURCE)."</td>
                <td style=\"width:20%;\">".$msg->read($SERVER_ADMIN)."</td>
		  </tr>
		");

	//--- lecture
	$query  = "select _ident, _adresse, _tel, _fax, _url, _webmaster, _create, _IDdata ";
	$query .= "from p2p_data ";
	$query .= "where _visible = 'O' " ;
	$query .= "order by _create desc";

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

             	// lien sur l'établissement
			$target = "onclick=\"window.open(this.href, '_blank'); return false;\"";

			// détermination du nombre de ressources
			$query  = "select _IDitem from p2p_items ";
			$query .= "where _IDdata = '$row[7]'";

			$return = mysqli_query($mysql_link, $query);
			$nbelem = ( $return ) ? mysqli_affected_rows($mysql_link) : 0 ;

			print("
				<tr class=\"$bgcol\">
		                <td align=\"center\" class=\"x-small\">". date2longfmt($row[6]) ."</td>
		                <td><a href=\"$row[4]\" $target>$row[0]</a></td>
		                <td>
					$row[1]<br/>
					<span class=\"x-small\">$row[2] - $row[3]</span>
		                </td>
		                <td align=\"center\">$nbelem</td>
		                <td><a href=\"mailto:$row[5]\">$row[5]</a></td>
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
		$prev = "[<a href=\"".myurlencode("server.php?cmde=school&skpage=$skpg&skshow=$where")."\">". $msg->read($SERVER_PREV) ."</a>]";
		}

	// liens directs sur n° de page
	$start = 1 + (($skshow - 1) * $MAXSHOW);
	$end   = $skshow * $MAXSHOW;

	$choix = ( $skpage == $start )
		? "<img src=\"images/nav_left.gif\" title=\"\" alt=\"\" /><b>$start</b><img src=\"images/nav_right.gif\" title=\"\" alt=\"\" />"
		: "<a href=\"".myurlencode("server.php?cmde=school&skpage=$start&skshow=$skshow")."\">$start</a>" ;

	for ($j = $start + 1; $j <= $end AND $j <= $page; $j++)
		$choix .= ( $skpage == $j )
			? "|<img src=\"images/nav_left.gif\" title=\"\" alt=\"\" /><b>$j</b><img src=\"images/nav_right.gif\" title=\"\" alt=\"\" />"
			: "|<a href=\"".myurlencode("server.php?cmde=school&skpage=$j&skshow=$skshow")."\">$j</a>" ;

	// bouton suivant
	$where = $skshow + 1;
	$next  = ( $skshow == $show )
		? ""
		: "[<a href=\"".myurlencode("server.php?cmde=school&skpage=$j&skshow=$where")."\">". $msg->read($SERVER_NEXT) ."</a>]" ;
?>

	<hr/>
		<?php if ( $nbelem ) print("<div style=\"text-align:center;\">$prev $choix $next</div>"); ?>
	<hr/>

</div>
</body>
