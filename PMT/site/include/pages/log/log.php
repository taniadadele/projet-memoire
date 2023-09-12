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
 *		module   : log.php
 *		projet   : la page de visualisation des logs de connexion
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/03/03
 *		modif    : 17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


$IP     = @$_GET["IP"];				// IP à mettre en listre brûlée
$sort   = (int) @$_GET["sort"];		// type de tri sur l'affichage
$skpage = ( @$_GET["skpage"] )		// n° de la page affichée
	? (int) $_GET["skpage"]
	: 1 ;
$skshow = ( @$_GET["skshow"] )		// n° du flash info
	? (int) $_GET["skshow"]
	: 1 ;
?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
		<?php print($msg->read($LOG_TITLE)); ?>
		<?php
			// il faut les droits admin
			if ( $_SESSION["CnxAdm"] == 255 )
				print("
					<a href=\"".$_SESSION["ROOTDIR"]."/log_csv.php?sid=".$_SESSION["sessID"]."\" onclick=\"window.open(this.href, '_blank'); return false;\">
					<img src=\"".$_SESSION["ROOTDIR"]."/images/post-in.gif\" title=\"". $msg->read($LOG_CSV) ."\" alt=\"". $msg->read($LOG_CSV) ."\" />
					</a>");
		?>
	</div>
</div>

<div class="maincontent">

	<?php
		$timestamp = date('Y-m-d H:i:s', strtotime('-5 minutes'));
		$query = "SELECT COUNT(_ID) FROM user_session WHERE _action = 'C' AND _lastaction >= '".$timestamp."' ";
		$result = mysqli_query($mysql_link, $query);
		$connected = mysqli_num_rows($result);
	?>
	<span class="badge">Nombres de personnes connectés: <?php echo $connected; ?></span>


	<p style="margin-top:0px; margin-bottom:10px; text-align:justify">
	<?php print($msg->read($LOG_SORT)); ?>
	</p>

	<?php

		$link1_color = $link2_color = $link3_color = '#bd1f20';
		switch ($sort) {
			case 0: $link1_color = 'grey'; break;
			case 1: $link2_color = 'grey'; break;
			case 2: $link3_color = 'grey'; break;
		}


		$link1 = '<a href="index.php?item='.$item.'&amp;sort=0" style="color: '.$link1_color.';">▲</a>';
		$link2 = '<a href="index.php?item='.$item.'&amp;sort=1" style="color: '.$link2_color.';">▲</a>';
		$link3 = '<a href="index.php?item='.$item.'&amp;sort=2" style="color: '.$link3_color.';">▲</a>';
	?>

	<table class="table table-hover table-striped">
	  <tr>
			<td class="align-center" style="width:2%;"><?php print("$link1"); ?></td>
			<td class="align-center" style="width:28%;"><?php print($msg->read($LOG_DATE)); ?></td>
			<td class="align-center" style="width:2%;"><?php print("$link2"); ?></td>
			<td class="align-center" style="width:28%;"><?php print($msg->read($LOG_USER)); ?></td>
			<td class="align-center" style="width:2%;"><?php print("$link3"); ?></td>
			<td class="align-center" style="width:28%;"><?php print($msg->read($LOG_STATION)); ?></td>
			<td class="align-center" style="width:10%;"><?php print($msg->read($LOG_ACTION)); ?></td>
	  </tr>

		<?php
			// mise en liste brûlée
			if ( $_SESSION["CnxAdm"] == 255 AND $IP )
				setvisibleIP($IP, "N");

			// lecture de la base de données
			$Query  = "select _date, _ID, _IPv6, _action, _ident from stat_log ";

			switch ( $sort ) {
				case '1' :
					$Query .= "order by _ID asc, _date desc";
					break;
				case '2' :
					$Query .= "order by _IPv6 asc, _date desc";
					break;
				default :
					$Query .= "order by _date desc";
					break;
				}

			// détermination du nombre de pages
			$result = mysqli_query($mysql_link, $Query);
			$nbelem = ( $result ) ? mysqli_affected_rows($mysql_link) : 0 ;

			$show   = 0;

			if ( $nbelem ) {
				$page  = $nbelem;

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
				$row = mysqli_fetch_row($result);

				while ( $row AND $i <= $MAXPAGE ) {
					$bgcolor = ( $i++ % 2 ) ? "item" : "menu" ;

					switch ( $row[3] ) {
						case 'C' : $action = "Cnx"; break;
						case 'D' : $action = "Dnx"; break;
						case 'X' : $action = "Err"; break;
						default  : $action = "Exp"; break;
						}

					// basculer en liste brûlée
					$req     = $msg->read($LOG_BURNOUT, $row[2]);
					$req     = str_replace("'", "\'", $req);			// le script java n'aime pas les '
					$logout  = ( $_SESSION["CnxAdm"] == 255 AND isvisibleIP($row[2]) == "O" )
						? "<a href=\"index.php?item=$item&amp;IP=$row[2]\" onclick=\"return confirmLink(this, '$req');\"><img src=\"".$_SESSION["ROOTDIR"]."/images/logout.png\" title=\"$req\" alt =\"\" /></a>"
						: "" ;
					$login   = ( $row[1] )
						? "<a href=\"".myurlencode("index.php?item=1&cmde=account&ID=$row[1]")."\">".getUserNameByID($row[1], false)."</a>"
						: getUserNameByID($row[1]) . " ($row[4])" ;

					$export  = "<a href=\"".$_SESSION["ROOTDIR"]."/log_csv.php?sid=".$_SESSION["sessID"]."&amp;id=$row[1]\" onclick=\"window.open(this.href, '_blank'); return false;\">";
					$export .= '<i class="fa fa-upload"></i>';
					$export .= "</a>";

					print("
						<tr class=\"$bgcolor\">
	       			         <td style=\"width:30%;\" colspan=\"2\">".date2longfmt($row[0])."</td>
	       			         <td style=\"width:30%;\" colspan=\"2\">$export $login</td>
	       			         <td style=\"width:30%;\" colspan=\"2\">$logout ".resolveHostName($row[2])."</td>
	       			         <td style=\"width:10%;\" class=\"align-center\">$action</td>
		       		       </tr>
		       		       ");

					$row = mysqli_fetch_row($result);
					}
				}
            ?>
	</table>

	<?php
		// bouton précédent
		$where = $skshow - 1;
		if ( $skshow == 1 )
			$prev = "";
		else {
			$skpg = 1 + (($skshow - 2) * $MAXSHOW);
			$prev = "[<a href=\"index.php?item=$item&amp;sort=$sort&amp;skpage=$skpg&amp;skshow=$where\">". $msg->read($LOG_PREV) ."</a>]";
			}

		// liens directs sur n° de page
		$start = 1 + (($skshow - 1) * $MAXSHOW);
		$end   = $skshow * $MAXSHOW;

		$choix = ( $skpage == $start )
			? "<img src=\"".$_SESSION["ROOTDIR"]."/images/nav_left.gif\" title=\"«\" alt=\"«\" /><strong>$start</strong><img src=\"".$_SESSION["ROOTDIR"]."/images/nav_right.gif\" title=\"»\" alt=\"»\" />"
			: "<a href=\"index.php?item=$item&amp;sort=$sort&amp;skpage=$start&amp;skshow=$skshow\">$start</a>" ;

		for ($j = $start + 1; $j <= $end AND $j <= $page; $j++)
			$choix .= ( $skpage == $j )
				? "|<img src=\"".$_SESSION["ROOTDIR"]."/images/nav_left.gif\" title=\"«\" alt=\"«\" /><strong>$j</strong><img src=\"".$_SESSION["ROOTDIR"]."/images/nav_right.gif\" title=\"»\" alt=\"»\" />"
				: "|<a href=\"index.php?item=$item&amp;sort=$sort&amp;skpage=$j&amp;skshow=$skshow\">$j</a>" ;

		// bouton suivant
		$where = $skshow + 1;
		$next = ( $skshow == $show )
			? ""
			: "[<a href=\"index.php?item=$item&amp;sort=$sort&amp;skpage=$j&amp;skshow=$where\">". $msg->read($LOG_NEXT) ."</a>]" ;
	?>

	<hr/>

	<?php if ( $nbelem ) print("<div style=\"text-align:center;\">$prev $choix $next</div>"); ?>

</div>
