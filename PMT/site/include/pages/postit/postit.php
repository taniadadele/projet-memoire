<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2003-2008 by Dominique Laporte(C-E-D@wanadoo.fr)
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
 *		module   : postit.php
 *		projet   : page d'affichage des post-it reçus et envoyés
 *
 *		version  : 1.1
 *		auteur   : laporte
 *		creation : 18/10/03
 *		modif    : 30/12/05 - par D. Laporte
 *                     gestion des droits
 *                     15/06/06 - par hugues lecocq
 *                     migration PHP5
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


$IDroot = ( @$_POST["IDroot"] ) 		// ID du répertoire racine
	? (int) $_POST["IDroot"]
	: (int) @$_GET["IDroot"] ;
$IDdata = ( @$_POST["IDdata"] )		// ID du répertoire parent
	? (int) $_POST["IDdata"]
	: (int) @$_GET["IDdata"] ;
$sort   = ( @$_POST["sort"] )     		// mode de tri d'affichage
	? (int) $_POST["sort"]
	: (int) @$_GET["sort"] ;

if ($sort == "") $sort = 2;

$wait   = ( @$_POST["wait"] )    		// post-it en attente
	? (int) $_POST["wait"]
	: (int) @$_GET["wait"] ;

$skpage = ( @$_GET["skpage"] )		// n° de la page affichée
	? (int) $_GET["skpage"]
	: 1 ;
$skshow = ( @$_GET["skshow"] )		// n° du flash info
	? (int) $_GET["skshow"]
	: 1 ;

$submit = ( @$_POST["del_x"] )		// bouton de validation
	? "del"
	: (@$_POST["move_x"] ? "move" : @$_GET["submit"]) ;



// on efface le post-it
switch ( $submit ) {
	case "rmdir" :
		$query  = "delete from postit_data where _IDdata = '$IDdata' AND _ID = '".$_SESSION["CnxID"]."' limit 1 ";
		mysqli_query($mysql_link, $Query);
		break;

	case "cancel" :
		$IDpost = @$_GET["IDpost"];		// identifiant du message
		$query  = "delete from postit_items where _IDpost = '$IDpost' AND _IDexp = '".$_SESSION["CnxID"]."' limit 1 ";
		if (mysqli_query($mysql_link, $Query)) {
			$res = mysqli_query($mysql_link, "select _IDpj, _ext from postit_pj where _IDpost = '$IDpost'");
			while ($row = mysqli_fetch_row($res)) {
				if (mysqli_query($mysql_link, "delete from postit_pj where _IDpj = '$row[0]' limit 1"))
					unlink("$DOWNLOAD/post-it/$row[0].$row[1]");
			}
		}
		break;

	case "del" :
		$IDpost = @$_POST["IDpost"];		// identifiant des messages instantannés
		$IDdst  = @$_POST["IDdst"];		// identifiant des destinataires

		for ($i = 1; $i <= $MAXPAGE; $i++ )
			if (@$IDpost[$i]) {
				$Query  = "update postit_items ";
				$Query .= (@$IDdst[$i] == $_SESSION["CnxID"]) ? "set _deldst = 'O' " : "set _delexp = 'O' " ;
				$Query .= "where _IDpost = '$IDpost[$i]' limit 1 ";

				if ( !mysqli_query($mysql_link, $Query) )
					mysqli_error($mysql_link);
				else {
					mysqli_query($mysql_link, "delete from postit_items where _deldst = 'O' AND _delexp = 'O'");
					if ( mysqli_affected_rows($mysql_link) ) {
						$res = mysqli_query($mysql_link, "select _IDpj, _ext from postit_pj where _IDpost = '$IDpost[$i]'");
						while ($row = mysqli_fetch_row($res)) {
							if (mysqli_query($mysql_link, "delete from postit_pj where _IDpj = '$row[0]' limit 1"))
								unlink("$DOWNLOAD/post-it/$row[0].$row[1]");
						}
					}
				}
			}
		break;

	default :
		$page_cmde = @$_POST["page_cmde"];
		$ident     = trim(addslashes(@$_POST["ident"]));

		// création répertoire
		if ($page_cmde == $msg->read($POSTIT_CREATE) AND $ident != "") {
			$Query  = "insert into postit_data ";
			$Query .= "values('', '$IDroot', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '".date("Y-m-d H:i:s")."', '$ident')";

			if ( !mysqli_query($mysql_link, $Query) )
				sql_error($mysql_link);
		}

		// modification répertoire
		if ( $page_cmde == $msg->read($POSTIT_MODIFICATION) AND $ident != "" ) {
			$Query  = "update postit_data set _ident = '$ident' where _IDdata = '$IDdata' AND _ID = '".$_SESSION["CnxID"]."' limit 1 ";
			if ( !mysqli_query($mysql_link, $Query) )
				sql_error($mysql_link);
		}

		// déplacement dans répertoire
		if ( $page_cmde == $msg->read($POSTIT_MOVE) ) {
			$dir = @$_POST["newdir"];
			$cbi = @$_POST["mvitem"];

			for ($i = 0; $i < count($cbi); $i++) {
				$Query  = "update postit_items set _IDdata = '$dir' where _IDpost = '$cbi[$i]' limit 1 ";
				if ( !mysqli_query($mysql_link, $Query) )
					sql_error($mysql_link);
			}
		}
		break;
}

?>







<form id="formulaire" action="index.php" method="post">
	<?php
		$temp = array('item', 'IDroot', 'IDdata', 'sort', 'skpage', 'skshow');
		foreach ($temp as $value) if (isset($$value))	echo '<input type="hidden" name="'.$value.'" value="'.$$value.'">';
	?>

	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
	  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($POSTIT_POSTITLIST); ?></h1>

	  <div style="float: right; text-align: right;">

	    <div class="mb-3">

				<a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="index.php?item=4&cmde=post">
					<i class="fa fa-plus fa-sm text-white-50"></i> <?php echo $msg->read($POSTIT_INPUTNEW); ?>
				</a>
	    </div>

	    <div class="mb-3">
				<select name="sort" class="custom-select" onchange="document.forms.formulaire.submit()">
					<option value="1" <?php if ($sort == 1) echo "selected"; ?>>Tous</option>
					<option value="2" <?php if ($sort == 2) echo "selected"; ?>>Reçu</option>
					<option value="3" <?php if ($sort == 3) echo "selected"; ?>>Envoyé</option>
				</select>
	    </div>
	  </div>
	</div>



<?php
	//---- lecture des post-it
	$query  = "select _IDpost, _IDexp, _IDdst, _titre, _vu, _date, _ack, _priority, _IDdata from postit_items ";
	$query .= "WHERE (_timer = _date OR _timer <= '".date("Y-m-d H:i:s")."') ";
	switch ( $sort ) {
		case 2 : // visualisation des messages reçus
			if ($SHOWPOST && $wait) $query .= 'AND _vu = _date '; else $query .= "AND (_IDdst = '".$_SESSION["CnxID"]."' AND _deldst = 'N') ";
			$exp_dest_col_name = $msg->read($POSTIT_EXP);
			break;
		case 3 : // visualisation des messages expédiés
			if ($SHOWPOST && $wait) $query .= 'AND _vu = _date '; else $query .= "AND (_IDexp = '".$_SESSION["CnxID"]."' AND _delexp = 'N') ";
			$exp_dest_col_name = $msg->read($POSTIT_DEST);
			break;
		default : // visualisation de tous les messages
			if ($SHOWPOST && $wait) $query .= 'AND _vu = _date '; else $query .= "AND ((_IDexp = '".$_SESSION["CnxID"]."' AND _delexp = 'N') OR (_IDdst = '".$_SESSION["CnxID"]."' AND _deldst = 'N')) ";
			$exp_dest_col_name = $msg->read($POSTIT_EXPDST);
			break;
	}
	$query .= "order by _date desc, _IDpost desc, _IDdata asc ";
	$query_counter = $query;
	$query .= "LIMIT ".$MAXSHOW." OFFSET ".(($skpage * $MAXSHOW) - $MAXSHOW);

	// détermination du nombre de pages + nb de résultats
	$result = mysqli_query($mysql_link, $query);
	$result_counter = mysqli_query($mysql_link, $query_counter);
	$nbelem = mysqli_affected_rows($mysql_link);

	$page   = $nbelem;
	$show   = 1;
	$affiche = true;
	$lastdata = 0;
?>

	<div class="card shadow mb-4">
	  <div class="card-header py-3">
	    <h6 class="m-0 font-weight-bold text-primary">Résultats: <?php if (isset($nbelem) && $nbelem) echo $nbelem; ?></h6>
	  </div>
	  <div class="card-body">

			<table class="table table-stripped">
				<tr>
					<!-- <th><i class="fas fa-trash"></i></th> -->
					<th></th>
					<th></th>
					<th><?php echo $msg->read($POSTIT_MESSAGE); ?></th>
					<th><?php echo $exp_dest_col_name; ?></th>
					<?php if (getParam('postit_list_show_acquitement')) { ?> <th><?php echo $msg->read($POSTIT_MSGACK); ?></th><?php } ?>
				</tr>

				<?php

					if ( $nbelem ) {
						$page  = ( $page % $MAXPAGE )
							? (int) ($page / $MAXPAGE) + 1
							: (int) ($page / $MAXPAGE) ;

						$show  = ( $page % $MAXSHOW )
							? (int) ($page / $MAXSHOW) + 1
							: (int) ($page / $MAXSHOW) ;

						// initialisation
						$j     = 1;
						$first = 1 + (($skpage - 1) * $MAXPAGE);
						$pos   = $first;

						// se positionne sur la page ad hoc
						mysqli_data_seek($result, $first - 1);
						// $row = remove_magic_quotes(mysqli_fetch_row($result));

						while ($row = mysqli_fetch_row($result) AND $j <= $MAXPAGE)
						{
							if($lastdata != 0 && $lastdata == $row[8]) $affiche = false;
							else $affiche = true;

							// Regrouppement message multiple
							if($row[2] <= -10000) $lastdata = $row[8];

							// post-it reçu ou expédié
							if ($row[1] == $_SESSION["CnxID"]) $img = "fa-upload";
							else $img = "fa-download";

							// qui est l'expéditeur ou le destinataire
							switch ( $row[1] ) {
								case '0' :
									// message système automatique
									$who = $msg->read($POSTIT_MSGSYS);
									break;
								default :
									// les listes de diffusion
									if ( $row[2] < 0 ) {
										if (-$row[2] < 1000) $list = -$row[2];
										elseif (-$row[2] < 10000) $list = -($row[2] + 1000);
										elseif (-$row[2] < 100000) $list = -($row[2] + 10000);
										else $list = -($row[2] + 100000);

										if (-$row[2] < 1000) $query = "select _ident from user_group where _IDgrp = '$list' AND _lang = '".$_SESSION["lang"]."' limit 1";
										elseif (-$row[2] < 10000) $query = "select _ident from campus_classe where _IDclass = '$list' limit 1";
										elseif (-$row[2] < 100000) $query = "select _nom from postit_lidi where _IDlidi = '$list' limit 1";
										else $query = "select _ident from egroup_data where _IDdata = '$list' limit 1";

										$res   = mysqli_query($mysql_link, $query);
										$list  = ($res) ? remove_magic_quotes(mysqli_fetch_row($res)) : 0;
										$who   = $msg->read($POSTIT_LIDI, $list[0]);
									}
									// les utilisateurs
									else {
										if ($SHOWPOST && $wait) $id = $row[2];
										elseif ($row[1] == $_SESSION["CnxID"]) $id = $row[2];
										else $id = $row[1];

										$who = getUserNameByID($id);
									}
									break;
							}

							// message visualisé ?
							if ($row[2] < 0) $vu  = '';
							else $vu  = ( $row[5] == $row[4] ) ? $msg->read($POSTIT_SENT) : $msg->read($POSTIT_HIT, $row[4]);

							// message acquitté ?
							if ( $row[2] < 0 ) $ack = '';
							else $ack = ($row[5] == $row[6]) ? '<i class="fas fa-times"></i>&nbsp;'.$msg->read($POSTIT_NOTACK) : '<i class="fas fa-check"></i>&nbsp;'.$row[6] ;

							// PJ acquittée ?
							$res    = mysqli_query($mysql_link, "select _IDpj, _ext from postit_pj where _IDpost = '$row[0]' order by _IDpj limit 1");
							$pj     = ($res) ? mysqli_fetch_row($res) : 0 ;

							$path   = ($pj) ? $DOWNLOAD.'/post-it/'.$pj[0].$pj[1] : '' ;

							$query  = "select download._date from download, download_data where download._ID = '$row[2]' AND download_data._file = '$path' AND download._IDdown = download_data._IDdown order by download._IDdown asc limit 1";
							$res    = mysqli_query($mysql_link, $query);
							$down   = ( $res ) ? mysqli_fetch_row($res) : 0 ;

							if ($pj) $ipj = '<i class="fas fa-paperclip"></i>'; else $ipj = '';

							// suppression du post-it pour les lidies ou à condition que le message ai été acquitté ou que le délai max soit passé
							if ($row[2] < 0 || $row[5] != $row[6] || ($MAXPOST && $row[5] <= date("Y-m-d H:i:s", (time() - $MAXPOST)))) $isrm = ''; else $isrm = 'disabled';

							// accès au post-it
							if ($SHOWPOST && $wait) $link = $row[3]; else $link = '<a href="'.myurlencode("index.php?item=$item&IDpost=$row[0]&IDroot=$IDroot&sort=$sort&cmde=visu").'">'.$row[3].'</a>';

							// annuler un envoi
							if ($row[5] == $row[4]) $cancel = '<a href="'.myurlencode('index.php?item='.$item.'&IDpost='.$row[0].'&IDroot='.$IDroot.'&sort='.$sort.'&submit=cancel').'"><i class="fas fa-trash"></i></a>';
							else $cancel = '';

							echo '<tr>';
								echo '<input type="hidden" name="IDdst['.$j.']" value="'.$row[2].'">';
								echo '<td>';
									if ($isrm != 'disabled') echo '<label for="IDpost_'.$row[0].'"><input type="checkbox" id="IDpost_'.$row[0].'" name="IDpost['.$j.']" value="'.$row[0].'" '.$isrm.'></label>';
								echo '</td>';
								echo '<td><i class="fas '.$img.'"></i></td>';
								echo '<td>'.$link.' '.$ipj.' '.$cancel.'<br><span class="x-small">'.$msg->read($POSTIT_SEND, date2longfmt($row[5])).'</span></td>';

								echo '<td>';
									if($row[2] <= -10000)
									{
										// Recherche les personnes du message multiple
										if ($row[8]) $p_IDdata = $row[8];
										else $p_IDdata = 'NULL';
										$queryuser  = "select u._ID, u._name, u._fname FROM user_id u , postit_items p WHERE u._ID = p._IDdst AND p._IDdata = $p_IDdata AND p._IDdst > 0 ";
										// sélection des destinataires
										$resultuser = mysqli_query($mysql_link, $queryuser);
										$i = 0;
										while ($rowuser = mysqli_fetch_row($resultuser))
										{
											if($i < 5) echo $rowuser[1].' '.$rowuser[2].', ';
											else if($i == 5) echo '...';
											$i++;
										}
										echo '<br><span class="x-small">'.$vu.'</span>';
									}
									else echo $who.'<br><span class="x-small">'.$ack.'</span>';
								echo '</td>';
								if (getParam('postit_list_show_acquitement')) echo '<td>'.$ack.'</td>';
							echo '</tr>';
							$j++;
						}
					}
				?>
			</table>

			Avec la sélection:
			<button class="btn btn-danger" name="del_x" value="del">Supprimer</button>
	  </div>
		<div class="card-footer text-muted">
			<?php
				$link_infos = 'index.php?item='.$item.'&IDroot='.$IDroot.'&sort='.$sort.'&wait='.$wait.'&skshow=1';
				echo getPagination($skpage, $nbelem, $link_infos, 0);
			?>
	  </div>
	</div>

</form>
