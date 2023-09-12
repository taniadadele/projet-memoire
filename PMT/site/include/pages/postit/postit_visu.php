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
 *		module   : postit_visu.php
 *		projet   : la page de visualisation et de réponse aux messages des post-it
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 19/10/03
 *		modif    : 15/06/06 - par hugues lecocq
 *                     migration PHP5
 *                     17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */



	// On récupère les éléments dans le post et le get
	$post_get = array('IDroot', 'sort', 'IDpost', 'note', 'submit');
	foreach ($post_get as $value) {
		if (isset($_POST[$value])) $$value = $_POST[$value];
		elseif (isset($_GET[$value])) $$value = $_GET[$value];
	}


	// Lecture du message
	$message = $db->getRow("SELECT `_titre` as `titre`, `_texte` as `texte`, `_date` as `date`, `_IDexp` as `IDexp`, `_IDpost` as `IDpost`, `_IP` as `IP`, `_sign` as `sign`, `_IDdst` as `IDdst`, `_ack` as `ack`, `_vu` as `vu`, `_priority` as `priority`, `_IDdata` as `IDdata` FROM `postit_items` WHERE `_IDpost` = ?i ORDER BY `_IDpost` desc ", $IDpost);

	// lecture de l'expéditeur ou du destinataire du message
	if ($message->IDexp == $_SESSION['CnxID']) $who = getUserNameByID($message->IDdst);
	else $who = getUserNameByID($message->IDexp);

	// acquittement de lecture par le destinataire
	if ($message->date == $message->ack AND $message->IDdst == $_SESSION["CnxID"]) $db->query("update postit_items set _ack = NOW() where _IDpost= ?i limit 1", $message->IDpost);
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<div class="floar: left;">
		<h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($POSTIT_SHOWPOSTIT); ?></h1>
	</div>
  <div style="float: right; text-align: right;">
    <div>
			<?php if ($message->IDexp != $_SESSION["CnxID"]) { ?>
				<a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="<?php echo myurlencode('index.php?item='.$item.'&IDpost='.$message->IDpost.'&IDdst='.$message->IDdst.'&cmde=post&submit=reply'); ?>">
					<i class="fas fa-reply fa-sm text-white-50"></i>&nbsp;<?php echo $msg->read($POSTIT_REPLY); ?>
				</a>
				<a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="<?php echo myurlencode('index.php?item='.$item.'&IDpost='.$message->IDpost.'&cmde=post&submit=forward'); ?>">
					<i class="fas fa-share fa-sm text-white-50"></i>&nbsp;<?php echo $msg->read($POSTIT_FORWARD); ?>
				</a>
			<?php } ?>
    </div>
  </div>
</div>

<div class="card shadow mb-4">
  <div class="card-header py-3">
		<?php if ($message->IDexp == $_SESSION["CnxID"]) $exp_dest_col_name = $msg->read($POSTIT_DEST); else $exp_dest_col_name = $msg->read($POSTIT_EXP); ?>
		<?php
		if($message->IDdst <= -10000)
		{
			$who = '';
			// Recherche les personnes du message multiple
			$users_temp = $db->getAll("select u._name as name, u._fname as fname FROM user_id u , postit_items p WHERE u._ID = p._IDdst AND p._IDdata = ?i AND p._IDdst > 0 ", $message->IDdata);
			foreach ($users_temp as $user_temp) $who .= $user_temp->name.' '.$user_temp->fname;
			$who = substr($who, 0, -2);
		}
		?>
    <h6 class="m-0 font-weight-bold text-primary"><?php echo getBackLink($item, $cmde, true); ?><?php echo $exp_dest_col_name; ?>: <?php echo $who; ?> | <?php echo $msg->read($POSTIT_SUBJECT); ?>: <?php echo $message->titre; ?> | Envoyé le: <?php echo date2longfmt($message->date); ?></h6>
  </div>
  <div class="card-body">
		<?php echo $message->texte; ?>
		<?php
			// lecture des PJ
			if ($pjs = $db->getAll("select `_IDpj` as `IDpj`, `_ext` as `ext`, `_size` as `size`, `_title` as `title` from postit_pj where _IDpost = ?i order by _IDpost", $message->IDpost)) {
				echo '<tr>';
					echo '<td style="/* border: 1px solid #c0c0c0; */" class="valign-top" colspan="2">';

						echo '<table class="table table-bordered table-striped">';
							foreach ($pjs as $pj) {
								echo '<tr>';
									$size   = $msg->read($POSTIT_BYTE, number_format($pj->size, 0, ',', ' '));
									$path = 'download_postit.php?IDpj='.$pj->IDpj;
									$img = 'ged_thumbnail.php?action=postitImage&file_name='.$pj->title.'&index='.$pj->IDpj;
									echo '<td style="width: 100px;">';
										echo '<a href="'.(@$path).'" class="overlib" target="_blank"><img src="'.(@$img).'" style="width: 100px;" title="'.$pj->title.'" alt="'.$pj->title.'" /></a>';
									echo '</td>';
									echo '<td style="vertical-align: middle;">';
										echo '<a href="'.(@$path).'" target="_blank">';
											echo $pj->title;
											echo '<br>';
											echo '<p class="small mb-0">'.$size.'</p>';
										echo '</a>';
									echo '</td>';
								echo '</tr>';
							}
						echo '</table>';
					echo '</td>';
				echo '</tr>';
			}
		?>
  </div>
</div>
