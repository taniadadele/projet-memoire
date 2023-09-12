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
 *		module   : postit.php
 *		projet   : fonctions de manipulation des post-it
 *
 *		version  : 2.0
 *		auteur   : laporte
 *		creation : 18/10/03
 *		modif    : 22/01/06 - par D. Laporte
 *                     fonction canRead et canPost
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


//---------------------------------------------------------------------------
function sendMessage($IDdst, $subject, $texte, $sign = "N", $priority = 0, $AR = "O")
{
/*
 * fonction :	envoie d'un post-it
 * in :		$IDdst, identifiant du destinataire (< 0 si liste de diffusion)
 *			$subject, sujet du message
 *			$texte, texte du message
 *			$sign, signer le message
 *			$AR, avec Accusé de Réception
 * out :		0 si erreur, ID post-it si OK
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// date de création du message
	$date    = date("Y-m-d H:i:s");

	$subject = addslashes(trim($subject));
	$texte   = addslashes(trim($texte));

	$deldst  = ( $IDdst < 0 ) ? "O" : "N" ;
	$delexp  = ( $AR == "O" ) ? "N" : "O" ;


	$query   = "insert into postit_items ";
	// $query  .= "values(NULL, '0', '$IDdst', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '1', '$subject', '$texte', '$priority', '$sign', '$date', '$date', '$deldst', '$delexp', '$date')";
	$query  .= "values(NULL, NULL, '$IDdst', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', NOW(), '1', '$subject', '".utf8_encode(nl2br(preg_replace( "/\r|\n/", "<br />", $texte)))."', '$priority', '$sign', NULL, NOW(), '$deldst', '$delexp', NOW())";

	// on interdit de s'écrire à soi même (si param)
	if ((!getParam('postit_can_write_to_myself') && $IDdst != $_SESSION["CnxID"]) || getParam('postit_can_write_to_myself')) {
		mysqli_query($mysql_link, $query);
		return mysqli_insert_id($mysql_link);
	}
	else return 0;
}
//---------------------------------------------------------------------------
function sendBroadcastEgroup($IDgroup, $IDdst, $subject, $texte, $sign, $priority = 0)
{
/*
 * fonction :	envoie d'un post-it aux membres d'un e-groupe
 * in :		$IDgroup, identifiant du e-groupe
 *			$IDdst, identifiant des destinataires
 *			$subject, sujet du message
 *			$texte, texte du message
 *			$sign, signer le message
 * out :		liste des ID des messages envoyés
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	$query  = "select _ID from egroup_user ";
	$query .= "where _IDdata = '$IDgroup' ";
	$query .= ( $IDdst > 0 ) ? "AND _access = '$IDdst'" : "AND _access > '0'" ;

	// sélection des destinataires
	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$IDlist = Array();

	// envoi des messages
	$count  = 0;
	while ( $row ) {
		if ( $insert = sendMessage($row[0], $subject, $texte, $sign, $priority, "N") )
			$IDlist[$count++] = $insert;

		$row = mysqli_fetch_row($result);
		}

	// copie du message envoyé à la liste
	if ( $count )
		$IDlist[$count++] = sendMessage(-100000 - $IDgroup, $subject, $texte, $sign, $priority, "O");

	return $IDlist;
}
//---------------------------------------------------------------------------
function sendBroadcastCentre($IDcentre, $lidie, $subject, $texte, $sign, $priority = 0)
{
/*
 * fonction :	envoie d'un post-it à une liste de diffusion automatique
 * in :		$IDcentre, identifiant du centre
 *			$lidie, identifiant de la liste de diffusion
 *			$subject, sujet du message
 *			$texte, texte du message
 *			$sign, signer le message
 * out :		liste des ID des messages envoyés
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	if ( $lidie < -10000 ) {
		// liste des matières
		$IDlist = -($lidie + 10000);

		$query  = "select _ID from user_id ";
		$query .= "where _adm AND _sexe != 'A' ";
		$query .= "AND _IDmat like '% $IDlist %' ";
		}
	else
		if ( $lidie < -1000 ) {
			// liste des classes
			$IDlist = -($lidie + 1000);

			$query  = "select user_id._ID from user_id ";
			$query .= "where user_id._adm AND user_id._sexe != 'A' AND _IDgrp = '1' ";
			$query .= "AND user_id._IDclass = '$IDlist' AND user_id._IDcentre = '$IDcentre' ";
			}
		else {
			// liste des groupes
			$IDlist = -$lidie;

			$query  = "select _ID from user_id ";
			$query .= "where _adm AND _sexe != 'A' ";
			$query .= "AND _IDgrp = '$IDlist' AND (_IDcentre = '$IDcentre' OR _centre & pow(2, $IDcentre - 1)) ";
			}

	// sélection des destinataires
	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$IDlist = Array();

	// envoi des messages
	$count  = 0;
	while ( $row ) {
		if ( $insert = sendMessage($row[0], $subject, $texte, $sign, $priority, "N") )
			$IDlist[$count++] = $insert;

		$row = mysqli_fetch_row($result);
		}

	// copie du message envoyé à la liste
	if ( $count )
		$IDlist[$count++] = sendMessage($lidie, $subject, $texte, $sign, $priority, "O");

	return $IDlist;
}
//---------------------------------------------------------------------------
function sendBroadcast($lidie, $subject, $texte, $sign = "N", $priority = 0)
{
/*
 * fonction :	envoie d'un post-it à une liste de diffusion personnelle
 * in :		$lidie, identifiant de la liste de diffusion
 *			$subject, sujet du message
 *			$texte, texte du message
 *			$sign, signer le message
 * out :		liste des ID des messages envoyés
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// accusé de réception
	$result = mysqli_query($mysql_link, "select _AR from postit_lidi where _IDlidi = '$lidie'");
	$mylist = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// envoi des messages
	$query  = "select _ID from postit_address ";
	$query .= "where _IDlidi = '$lidie'";

	// sélection des destinataires
	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$IDlist = Array();

	// envoi des messages
	$count  = 0;
	while ( $row ) {
		if ( $insert = sendMessage($row[0], $subject, $texte, $sign, $priority, $mylist[0]) )
			$IDlist[$count++] = $insert;

		$row = mysqli_fetch_row($result);
		}

	// copie du message envoyé à la liste
	if ( $count )
		$IDlist[$count++] = sendMessage(-10000 - $lidie, $subject, $texte, $sign, $priority, "O");

	return $IDlist;
}
//---------------------------------------------------------------------------
function sendBroadcastMessage($tab, $subject, $texte, $sign = "N", $priority = 0)
{
/*
 * fonction :	envoie d'un post-it à une liste de diffusion personnelle
 * in :		$lidie, identifiant de la liste de diffusion
 *			$subject, sujet du message
 *			$texte, texte du message
 *			$sign, signer le message
 * out :		liste des ID des messages envoyés
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// envoi des messages
	$query  = "select MAX(_IDdata) from postit_items ";
	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
	$id_insert = $row[0] + 1;

	$IDlist = Array();

	// envoi des messages
	$count  = 0;
	foreach ( $tab as $val )
	{
		if ( $insert = sendMessage($val, $subject, $texte, $sign, $priority, "O", $id_insert) )
			$IDlist[$count++] = $insert;
	}

	// copie du message envoyé à la liste
	if ( $count > 1 )
		$IDlist[$count++] = sendMessage(-10000 - $_SESSION["CnxID"], $subject, $texte, $sign, $priority, "O", $id_insert);

	return $IDlist;
}
//---------------------------------------------------------------------------
function sendAlertMessage($IDdst, $subject, $texte)
{
/*
 * fonction :	envoie d'un post-it d'alerte
 * in :		$IDdst, identifiant du destinataire
 *			$subject, sujet du message
 *			$texte, texte du message
 * out :		0 si erreur, 1 si OK
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	if ( $IDdst ) {
		// date de création du message
		$date    = date("Y-m-d H:i:s");

		$subject = addslashes(trim($subject));
		$texte   = addslashes(trim($texte));

		$Query   = "insert into postit_items ";
		$Query  .= "values('', '0', '$IDdst', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '0', '$subject', '$texte', '1', 'N', '$date', '$date', 'N', 'O', '$date')";

		return mysqli_query($mysql_link, $Query);
		}

	return 0;
}
//---------------------------------------------------------------------------
function sendWarningMessage($subject, $texte)
{
/*
 * fonction :	envoie d'un post-it d'alerte au webmaster
 * in :		$IDdst, identifiant du destinataire
 *			$subject, sujet du message
 *			$texte, texte du message
 * out :		0 si erreur, 1 si OK
 */
	require $_SESSION["ROOTDIR"]."/globals.php";

	// recherche du webmaster
	$result = mysqli_query($mysql_link, "select _ID from user_id where _adm = '255' order by _ID limit 1");
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	return sendAlertMessage((int) $row[0], $subject, $texte);
}
//---------------------------------------------------------------------------
function canRead($IDdst)
{
/*
 * fonction :	détermine si un utilisateur possède les droits de lecture des post-it
 * in :		$IDdst : ID du destinataire
 * out :		true si droit ok, false sinon
 */

	global	$mysql_link;

	// recherche des droits de lecture du destinataire
	$Query  = "select distinctrow user_id._IDgrp, postit._IDgrprd ";
	$Query .= "from user_id, postit ";
	$Query .= "where user_id._ID = '$IDdst' ";
	$Query .= "AND user_id._IDcentre = postit._IDcentre ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	return (bool) ($row[1] & pow(2, $row[0] - 1));
}
//---------------------------------------------------------------------------
function canPost($IDpost)
{
/*
 * fonction :	détermine si un utilisateur possède les droits d'écriture des post-it
 * in :		$IDpost : ID de l'expéditeur
 * out :		true si droit ok, false sinon
 */

	global	$mysql_link;

	// les anonymes ne peuvent écrire
	if ( $_SESSION["CnxSex"] != "A" ) {
		// recherche des droits d'écriture de l'expéditeur
		$Query  = "select distinctrow user_id._IDgrp, postit._IDgrpwr ";
		$Query .= "from user_id, postit ";
		$Query .= "where user_id._ID = '$IDpost' ";
		$Query .= "AND user_id._IDcentre = postit._IDcentre ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		return (bool) ($row[1] & pow(2, $row[0] - 1));
		}

	return false;
}
//---------------------------------------------------------------------------
function newMail()
{
/*
 * fonction :	affichage des nouveaux postit sur la page
 */

	global	$mysql_link;

	require_once $_SESSION["ROOTDIR"]."/include/calendar_tools.php";

	require $_SESSION["ROOTDIR"]."/msg/postit.php";
	require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/postit.php");

	// si l'utiisateur n'et pas identifié : on quitte
	if ( !$_SESSION["CnxID"] )
		return;

	// date et heure du jour
	$date   = date("Y-m-d H:i:s");

	// recherche des post-it à afficher
	$query  = "select _IDpost, _IDexp, _IP, _date, _titre, _vu from postit_items ";
	$query .= "where _IDdst = '".@$_SESSION["CnxID"]."' AND _date = _ack ";
	$query .= "AND (_timer = _date OR _timer <= '$date') ";
	$query .= "order by _IDpost desc ";
	// $query .= "limit 1";

	$compteur = 0;

	$result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $compteur++;
  }

	if ($compteur != 0)
	{
		if ($compteur > 9) $compteur = '9+';
		echo '<span id="new_messages_counter_hidden" message_count="'.$compteur.'" style="display: none;"></span>';
	}

}
//---------------------------------------------------------------------------




function getNewMessagesPopupElements() {
	global $mysql_link;

	// date et heure du jour
	$date   = date("Y-m-d H:i:s");

	// recherche des post-it à afficher
	$query  = "select _IDpost, _IDexp, _IP, _date, _titre, _vu from postit_items ";
	$query .= "where _IDdst = '".@$_SESSION["CnxID"]."' AND _date = _ack ";
	$query .= "AND (_timer = _date OR _timer <= '$date') ";
	$query .= "order by _IDpost desc LIMIT 10";

	$toReturn = '';
	$compteur = 0;
	$result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$link = myurlencode("index.php?item=4&cmde=visu&IDpost=".$row[0]);

		$toReturn .= '<a class="dropdown-item d-flex align-items-center" href="'.$link.'">';
			$toReturn .= '<div class="dropdown-list-image mr-3">';
				$toReturn .= '<img class="rounded-circle" src="'.getUserPictureLink($row[1]).'" alt="">';
				$toReturn .= '<div class="status-indicator bg-warning"></div>';
			$toReturn .= '</div>';
			$toReturn .= '<div>';
				$toReturn .= '<div class="text-truncate">'.$row[4].'</div>';
				$toReturn .= '<div class="small text-gray-500">'.getUserNameByID($row[1]).' · '.date('d/m/Y H:i', strtotime($row[3])).'</div>';
			$toReturn .= '</div>';
		$toReturn .= '</a>';

		$compteur++;
  }

	// Si aucuns nouveaux messages, on l'affiche
	if ($compteur == 0) {
		$toReturn .= '<a class="dropdown-item d-flex align-items-center" href="#">';
			$toReturn .= '<div class="text-center w-100">Aucuns nouveaux messages</div>';
		$toReturn .= '</a>';
	}

	return $toReturn;

}


?>
