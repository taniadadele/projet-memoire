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
 *		module   : postit_post.php
 *		projet   : la page de saisie des post-it
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 15/06/06 - par hugues lecocq
 *                     migration PHP5
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 */


 //!!\\ SECTION INDISPENSABLE SI ON FAIT UN MAIL (pour les traductions): //!!\\
 require "msg/mail.php";
 $msg_mail  = new TMessage("msg/".$_SESSION["lang"]."/mail.php", $_SESSION["ROOTDIR"]);
 $msg_mail->msg_mail_search  = $keywords_search;
 $msg_mail->msg_mail_replace = $keywords_replace;


$IDroot   = ( @$_POST["IDroot"] ) 					// ID du répertoire racine
	? (int) $_POST["IDroot"]
	: (int) @$_GET["IDroot"] ;
$sort     = ( @$_POST["sort"] )     				// mode de tri d'affichage
	? (int) $_POST["sort"]
	: (int) @$_GET["sort"] ;
$IDcentre = ( @$_POST["IDcentre"] )					// Identifiant du centre
	? (int) $_POST["IDcentre"]
	: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]);
$IDtype   = ( @$_POST["IDtype"] )     				// type de post-it
	? (int) $_POST["IDtype"]
	: (int) (@$_GET["IDtype"] ? $_GET["IDtype"] : 1) ;
$IDdst    = ( @$_POST["IDdst"] )     				// identifiant des destinataires
	? (int) $_POST["IDdst"]
	: (int) @$_GET["IDdst"] ;
$IDgrp    = ( @$_POST["IDgrp"] )     				// identifiant des groupes
	? (int) $_POST["IDgrp"]
	: (int) @$_GET["IDgrp"] ;

$subject  = trim(@$_POST["subject"]);				// sujet du message
$texte_ckeditor    = stripslashes(trim(@$_POST["texte_ckeditor"]));					// texte du message
$sign     = @$_POST["sign"];						// signature du message
$priority = (int) @$_POST["priority"];				// niveau de priorité du message
$messagetype = @$_POST["messagetype"];				// type de message

$sex      = addslashes(@$_POST["sex"]);				// sexe
$name     = addslashes(trim(@$_POST["name"]));			// nom
$societe  = addslashes(trim(@$_POST["societe"]));		// societe
$address  = addslashes(trim(@$_POST["address"]));		// adresse
$tel      = addslashes(trim(@$_POST["tel"]));			// téléphone
$email    = addslashes(trim(@$_POST["email"]));			// mél
$motif    = addslashes(@$_POST["motif"]);				// motif appel

$submit   = ( @$_POST["submita"] )					// bouton de validation
	? $_POST["submita"]
	: @$_GET["submita"] ;


// Si on a transmis un ID de fichier dans l'URL c'est que l'on veut le partager

if (isset($_GET['id_file'])) $ID_file = addslashes(stripslashes($_GET['id_file']));
else $ID_file = "";

if ($ID_file != "")
{

  $query = "SELECT _IDimage, _ID, _title FROM images WHERE _ID = '".$_SESSION['CnxID']."' AND _IDimage = '".$ID_file."' LIMIT 1 ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

    $current_time = date('Y-m-d H:i:s');
    $chaine = $row[0].";".$row[1].";".$current_time;
    $file_link = openssl_encrypt($chaine, "AES-128-ECB" ,$CRYPT_KEY);


    $url =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
    $exploded_url = explode("index.php", $escaped_url);
    $mail_url = $exploded_url[0]."index.php?item=28&cmde=link_shared&file_id=".urlencode($file_link);


    $texte_ckeditor_post = "<a href=\"".$mail_url."\">Télécharger le fichier joint</a>";
  }


}






// Toujours vérifier si le répertoire utilisateur est existant
if(!is_dir("tmp/server/php/files/".$_SESSION["CnxID"]))
{
	mkdir("tmp/server/php/files/".$_SESSION["CnxID"], 0777, true);
}

function suppression($dossier_traite)
{
	$repertoire = opendir($dossier_traite);

	while(false !== ($fichier = readdir($repertoire)))
	{
		$chemin = $dossier_traite."/".$fichier;

		if($fichier!="." AND $fichier!=".." AND !is_dir($fichier))
		{
			unlink($chemin);
		}
	}
	closedir($repertoire);
}

function nettoyerChaine($chaine)
{
	$caracteres = array(
		'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
		'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '' => 'e',
		'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
		'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
		'' => 'oe', '' => 'oe',
		'$' => 's');

	$chaine = strtr($chaine, $caracteres);

	return $chaine;
}

$start   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
$end     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
$startday = intval(date("N", $start))-1;
$endday = intval(date("N", $end))-1;

// Récuperation message
if(!empty($_GET['IDpost']))
{
	if($_GET["submit"] == "reply" || $_GET["submit"] == "forward")
	{
		$query  = "SELECT p._texte, p._titre, p._IDexp, u._name, u._fname ";
		$query .= "FROM postit_items p, user_id u ";
		$query .= "WHERE u._ID = p._IDexp ";
		$query .= "AND p._IDpost = ".$_GET['IDpost']." ";
		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while ( $row )
		{
			$texte_ckeditor_post = "<p></p><hr />".$row[0];
			if($_GET["submit"] == "forward")
			{
				$titre_post = "Fwd: ".$row[1];
			}
			else
			{
				$titre_post = "Re: ".$row[1];
				$user_reply = $row[3]." ".$row[4]."<span class='hidden'>".$row[2]."</span>";
			}

			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
	}
}
?>

<script>
jQuery(document).ready(function() {
    jQuery('#formulaire').desoForm({
        'emptyField': 'Le champ est obligatoire',
        'submit': function($el, ok) {
            if(ok) {
                $el[0].submit();
            }
        }
    });
});
</script>

<!-- <script src="script/ckeditor/ckeditor.js"></script> -->


<div class="maincontent">

<?php
	// lecture du droit d'écriture
	$Query  = "select _IDgrpwr, _IDgrppj, _IDgrprd from postit ";
	$Query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' ";
	$Query .= "limit 1";

	$result = mysqli_query($mysql_link, $Query);
	$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	// vérification des autorisations
	verifySessionAccess(0, $auth[0]);

	// commande de l'utilisateur
	if( $submit )
	{
		// Sélection ck
		if(!empty($_POST['ck_admin']))
		{
			$ck_admin = 1;
		}

		if(!empty($_POST['ck_ens']))
		{
			$ck_ens = 1;
		}

		if(!empty($_POST['ck_eleve1']))
		{
			$ck_eleve1 = 1;
		}

		if(!empty($_POST['ck_eleve2']))
		{
			$ck_eleve2 = 1;
		}

		if(!empty($_POST['ck_fr']))
		{
			$ck_fr = 1;
		}

		if(!empty($_POST['ck_de']))
		{
			$ck_de = 1;
		}

		// Personne
		if(!empty($_POST['UserTags']))
		{
			foreach($_POST['UserTags'] as $val)
			{
				$tab_email[] = $val;
			}
		}

		// CK
		if(empty($_POST['UserTags']) && empty($_POST['ClassTags']) && empty($_POST['GroupTags']) && empty($_POST['DiffTags']) && empty($_POST['ClassProfTags']) && empty($_POST['MatProfTags']))
		{
			if($ck_admin || $ck_ens || $ck_eleve1 || $ck_eleve2 || $ck_fr || $ck_de)
			{
				$query  = "select _ID, _name, _fname from user_id ";
				$query .= "where 1 = 1 ";
				$query .= ($ck_fr && !$ck_de) ? "AND _lang = 'FR' " : "";
				$query .= ($ck_de && !$ck_fr) ? "AND _lang = 'DE' " : "";
				$query .= ($ck_eleve1 && !$ck_eleve2) ? "AND _IDcentre = 1 " : "";
				$query .= ($ck_eleve2 && !$ck_eleve1) ? "AND _IDcentre = 2 " : "";
				$query .= ($ck_admin && !$ck_ens) ? "AND _IDgrp = 4 " : "";
				$query .= ($ck_ens && !$ck_admin) ? "AND _IDgrp = 2 " : "";
				$query .= ($ck_ens && $ck_admin) ? "AND (_IDgrp = 2 OR _IDgrp = 4) " : "";
        $query .= "AND _adm >= '1' ";
				$query .= "order by _name, _fname";
				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row )
				{
					$tab_email[] = $row[0];
					$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
			}
		}

		// Classe
		if(!empty($_POST['ClassTags']))
		{
			foreach($_POST['ClassTags'] as $val)
			{
				$query  = "select _ID, _name, _fname from user_id ";
				$query .= "where _IDclass = '$val' ";
				$query .= "AND _visible = 'O' ";
        $query .= "AND _adm >= '1' ";
				$query .= "order by _name, _fname";
				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row )
				{
					$tab_email[] = $row[0];
					$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
			}
		}

		// Groupe
		if(!empty($_POST['GroupTags']))
		{
			foreach($_POST['GroupTags'] as $val)
			{
				$queryuser  = "SELECT u._ID, u._name, u._fname ";
				$queryuser .= "FROM user_id u, groupe g ";
				$queryuser .= "WHERE u._ID = g._IDeleve ";
				$queryuser .= "AND g._IDgrp = $val ";
				$queryuser .= ($ck_admin && !$ck_ens) ? "AND u._IDgrp = 4 " : "";
				$queryuser .= ($ck_ens && !$ck_admin) ? "AND u._IDgrp = 2 " : "";
				$queryuser .= ($ck_ens && $ck_admin) ? "AND (u._IDgrp = 2 OR u._IDgrp = 4) " : "";
        $query .= "AND u._adm >= '1' ";
				$queryuser .= "order by u._name";

				$resultuser = mysqli_query($mysql_link, $queryuser);
				$rowuser    = ( $resultuser ) ? remove_magic_quotes(mysqli_fetch_row($resultuser)) : 0 ;

				while ( $rowuser )
				{
					$tab_email[] = $rowuser[0];
					$rowuser = remove_magic_quotes(mysqli_fetch_row($resultuser));
				}
				$rowl = remove_magic_quotes(mysqli_fetch_row($resultl));
			}
		}

		// ***** Liste de diffusion *****
		if(!empty($_POST['DiffTags']))
		{
			foreach($_POST['DiffTags'] as $val)
			{
				// Sélection ck liste diffusion
				$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $val AND p._type = 'ck_admin' ";
				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;
				$ckl_admin = count($rowl[0]);

				$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $val AND p._type = 'ck_ens' ";
				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;
				$ckl_ens = count($rowl[0]);

				$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $val AND p._type = 'ck_eleve1' ";
				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;
				$ckl_eleve1 = count($rowl[0]);

				$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $val AND p._type = 'ck_eleve2' ";
				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;
				$ckl_eleve2 = count($rowl[0]);

				$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $val AND p._type = 'ck_fr' ";
				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;
				$ckl_fr = count($rowl[0]);

				$query  = "SELECT p._ID FROM postit_address p WHERE p._IDlidi = $val AND p._type = 'ck_de' ";
				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;
				$ckl_de = count($rowl[0]);

				// *** Sélection liste personne ***
				$query  = "SELECT p._ID, u._name, u._fname ";
				$query .= "FROM postit_address p, user_id u ";
				$query .= "WHERE p._ID = u._ID ";
				$query .= "AND p._IDlidi = $val ";
        $query .= "AND u._adm >= '1' ";
				$query .= "AND p._type = 'user' ";

				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;

				$list_user = "";

				while ( $rowl )
				{
					$tab_email[] = $rowl[0];
					$rowl = remove_magic_quotes(mysqli_fetch_row($resultl));
				}

				// *** CK liste ***
				// Vérif si pas autre chose
				$query  = "SELECT _IDlidi ";
				$query .= "FROM postit_address ";
				$query .= "WHERE _IDlidi = $val ";
				$query .= "AND (_type LIKE 'user' ";
				$query .= "OR _type LIKE 'class' ";
				$query .= "OR _type LIKE 'group' ";
				$query .= "OR _type LIKE 'classprof' ";
				$query .= "OR _type LIKE 'matprof') ";

				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;

				if(!count($rowl[0]))
				{
					if($ckl_admin || $ckl_ens || $ckl_eleve1 || $ckl_eleve2 || $ckl_fr || $ckl_de)
					{
						$query  = "select _ID, _name, _fname from user_id ";
						$query .= "where 1 = 1 ";
            $query .= "AND _adm >= '1' ";
						$query .= "order by _name, _fname";
						$result = mysqli_query($mysql_link, $query);
						$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

						while ( $row )
						{
							$tab_email[] = $row[0];
							$row = remove_magic_quotes(mysqli_fetch_row($result));
						}
					}
				}

				// *** Sélection liste class ***
				$query  = "SELECT p._ID, c._IDclass, c._ident ";
				$query .= "FROM postit_address p, campus_classe c ";
				$query .= "WHERE p._ID = c._IDclass ";
				$query .= "AND p._IDlidi = $val ";
				$query .= "AND p._type = 'class' ";
        $query .= "AND _adm >= '1' ";

				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;

				$list_class = "";

				while ( $rowl )
				{
					$query  = "select _ID, _name, _fname from user_id ";
					$query .= "where _IDclass = '$rowl[1]' ";
					$query .= "AND _visible = 'O' ";
          $query .= "AND _adm >= '1' ";
					$query .= "order by _name, _fname";
					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row )
					{
						$tab_email[] = $row[0];
						$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
					$rowl = remove_magic_quotes(mysqli_fetch_row($resultl));
				}

				// *** Sélection liste group ***
				$query  = "SELECT _ID ";
				$query .= "FROM postit_address ";
				$query .= "WHERE _IDlidi = $val ";
				$query .= "AND _type = 'group' ";

				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;

				$list_group = "";

				while ( $rowl )
				{
					$queryuser  = "SELECT u._ID, u._name, u._fname ";
					$queryuser .= "FROM user_id u, groupe g ";
					$queryuser .= "WHERE u._ID = g._IDeleve ";
					$queryuser .= "AND g._IDgrp = $rowl[0] ";
					$queryuser .= ($ckl_admin && !$ckl_ens) ? "AND u._IDgrp = 4 " : "";
					$queryuser .= ($ckl_ens && !$ckl_admin) ? "AND u._IDgrp = 2 " : "";
					$queryuser .= ($ckl_ens && $ckl_admin) ? "AND (u._IDgrp = 2 OR u._IDgrp = 4) " : "";
          $query .= "AND u._adm >= '1' ";
					$queryuser .= "order by u._name";

					$resultuser = mysqli_query($mysql_link, $queryuser);
					$rowuser    = ( $resultuser ) ? remove_magic_quotes(mysqli_fetch_row($resultuser)) : 0 ;

					while ( $rowuser )
					{
						$tab_email[] = $rowuser[0];
						$rowuser = remove_magic_quotes(mysqli_fetch_row($resultuser));
					}
					$rowl = remove_magic_quotes(mysqli_fetch_row($resultl));
				}

				// *** Sélection profs d'une classe ***
				$query  = "SELECT p._ID ";
				$query .= "FROM postit_address p ";
				$query .= "WHERE p._IDlidi = $val ";
				$query .= "AND p._type = 'classprof' ";

				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;

				$list_class = "";
				date_default_timezone_set('Europe/Paris');
				setlocale(LC_TIME, "fr_FR" );
				$good_date = date("Y-m-d H:i:s", time());

				$startx   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
				$endx     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
				$startday = intval(date("N", $startx))-1;
				$endday = intval(date("N", $endx))-1;

				while ( $rowl )
				{
					$query  = "SELECT distinctrow user_id._ID ";
					$query .= "FROM edt_data, campus_classe, user_id ";
					$query .= "WHERE (edt_data._ID = user_id._ID OR edt_data._IDrmpl = user_id._ID) ";
					$query .= "AND edt_data._IDclass LIKE '%;$rowl[0];%' ";
					$query .= "AND edt_data._etat = 1 ";
					$query .= "AND ((edt_data._jour >= $startday AND edt_data._nosemaine >= ".date("W", $startx)." AND edt_data._annee = ".date("Y", $startx).") ";
					$query .= "OR (edt_data._jour <= $endday AND edt_data._nosemaine <= ".date("W", $endx)." AND edt_data._annee = ".date("Y", $endx).")) ";
          $query .= "AND user_id._adm >= '1' ";
					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row )
					{
						$tab_email[] = $row[0];
						$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
					$rowl = remove_magic_quotes(mysqli_fetch_row($resultl));
				}

				// *** Sélection profs d'une matière ***
				$query  = "SELECT p._ID ";
				$query .= "FROM postit_address p ";
				$query .= "WHERE p._IDlidi = $val ";
				$query .= "AND p._type = 'matprof' ";

				$resultl = mysqli_query($mysql_link, $query);
				$rowl    = ( $resultl ) ? remove_magic_quotes(mysqli_fetch_row($resultl)) : 0 ;

				$list_class = "";
				date_default_timezone_set('Europe/Paris');
				setlocale(LC_TIME, "fr_FR" );
				$good_date = date("Y-m-d H:i:s", time());

				$startx   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
				$endx     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
				$startday = intval(date("N", $startx))-1;
				$endday = intval(date("N", $endx))-1;

				while ( $rowl )
				{
					$query  = "select distinctrow user_id._ID ";
					$query .= "from edt_data, campus_classe, user_id ";
					$query .= "WHERE (edt_data._ID = user_id._ID OR edt_data._IDrmpl = user_id._ID) ";
					$query .= "AND edt_data._IDmat = $val ";
					$query .= "AND edt_data._etat = 1 ";
					$query .= "AND ((edt_data._jour >= $startday AND edt_data._nosemaine >= ".date("W", $startx)." AND edt_data._annee = ".date("Y", $startx).") ";
					$query .= "OR (edt_data._jour <= $endday AND edt_data._nosemaine <= ".date("W", $endx)." AND edt_data._annee = ".date("Y", $endx).")) ";
          $query .= "AND user_id._adm >= '1' ";
					$result = mysqli_query($mysql_link, $query);
					$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

					while ( $row )
					{
						$tab_email[] = $row[0];
						$row = remove_magic_quotes(mysqli_fetch_row($result));
					}
					$rowl = remove_magic_quotes(mysqli_fetch_row($resultl));
				}
			}
		}

		// Profs d'une classe
		if(!empty($_POST['ClassProfTags']))
		{
			date_default_timezone_set('Europe/Paris');
			setlocale(LC_TIME, "fr_FR" );
			$good_date = date("Y-m-d H:i:s", time());

			$startx   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
			$endx     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
			$startday = intval(date("N", $startx))-1;
			$endday = intval(date("N", $endx))-1;

			foreach($_POST['ClassProfTags'] as $val)
			{
				$query  = "select distinctrow user_id._ID ";
				$query .= "from edt_data, campus_classe, user_id ";
				$query .= "WHERE (edt_data._ID = user_id._ID OR edt_data._IDrmpl = user_id._ID) ";
				$query .= "AND edt_data._IDclass LIKE '%;$val;%' ";
				$query .= "AND edt_data._etat = 1 ";
				$query .= "AND ((edt_data._nosemaine >= ".date("W", $startx)." AND edt_data._annee = ".date("Y", $startx).") ";
				$query .= "OR (edt_data._nosemaine <= ".date("W", $endx)." AND edt_data._annee = ".date("Y", $endx).")) ";
        $query .= "AND user_id._adm >= '1' ";
        // echo $query;
				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row )
				{
					$tab_email[] = $row[0];
					$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
			}
		}

		// Profs d'une matière
		if(!empty($_POST['MatProfTags']))
		{
			date_default_timezone_set('Europe/Paris');
			setlocale(LC_TIME, "fr_FR" );
			$good_date = date("Y-m-d H:i:s", time());

			$startx   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
			$endx     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
			$startday = intval(date("N", $startx))-1;
			$endday = intval(date("N", $endx))-1;

			foreach($_POST['MatProfTags'] as $val)
			{
				$query  = "select distinctrow user_id._ID ";
				$query .= "from edt_data, campus_classe, user_id ";
				$query .= "WHERE (edt_data._ID = user_id._ID OR edt_data._IDrmpl = user_id._ID) ";
				$query .= "AND edt_data._IDmat = $val ";
				$query .= "AND edt_data._etat = 1 ";
				$query .= ($ck_fr && !$ck_de) ? "AND user_id._lang = 'FR' " : "";
				$query .= ($ck_de && !$ck_fr) ? "AND user_id._lang = 'DE' " : "";
				$query .= ($ck_eleve1 && !$ck_eleve2) ? "AND user_id._IDcentre = 1 " : "";
				$query .= ($ck_eleve2 && !$ck_eleve1) ? "AND user_id._IDcentre = 2 " : "";
				$query .= ($ck_admin && !$ck_ens) ? "AND user_id._IDgrp = 4 " : "";
				$query .= ($ck_ens && !$ck_admin) ? "AND user_id._IDgrp = 2 " : "";
				$query .= ($ck_ens && $ck_admin) ? "AND (user_id._IDgrp = 2 OR user_id._IDgrp = 4) " : "";
				$query .= "AND ((edt_data._jour >= $startday AND edt_data._nosemaine >= ".date("W", $startx)." AND edt_data._annee = ".date("Y", $startx).") ";
				$query .= "OR (edt_data._jour <= $endday AND edt_data._nosemaine <= ".date("W", $endx)." AND edt_data._annee = ".date("Y", $endx).")) ";
        $query .= "AND user_id._adm >= '1' ";
				$result = mysqli_query($mysql_link, $query);
				$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

				while ( $row )
				{
					$tab_email[] = $row[0];
					$row = remove_magic_quotes(mysqli_fetch_row($result));
				}
			}
		}

    // Si on a coché la case Administration ou Enseignants alors on envois les mails aussi au personnes intéréssées
    if ((isset($ck_admin) && $ck_admin) || (isset($ck_ens) && $ck_ens))
    {
      $query  = "select distinctrow _ID ";
      $query .= "from user_id ";
      if ($ck_admin && !$ck_ens) $query .= "WHERE _IDgrp = 4 ";
      elseif (!$ck_admin && $ck_ens) $query .= "WHERE _IDgrp = 2 ";
      elseif ($ck_admin && $ck_ens) $query .= "WHERE _IDgrp = 4 OR _IDgrp = 2 ";
      $query .= "AND _adm >= '1' ";
      $result = mysqli_query($mysql_link, $query);
      $row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
      while ( $row )
      {
        $tab_email[] = $row[0];
        $row = remove_magic_quotes(mysqli_fetch_row($result));
      }
    }




		// Dédoublonnage valeur
		$tab_email = array_unique($tab_email);

		// Email des utilisateurs
		$list_email = "";
		$list_dest = "";
		foreach($tab_email as $val)
		{
			$queryuser  = "select _email, _name, _fname ";
			$queryuser .= "from user_id ";
			$queryuser .= "WHERE _ID = $val ";
      $query .= "AND _adm >= '1' ";
			$resultuser = mysqli_query($mysql_link, $queryuser);
			$rowuser    = ( $resultuser ) ? remove_magic_quotes(mysqli_fetch_row($resultuser)) : 0 ;

			while ( $rowuser )
			{
				if($rowuser[0] != "")
				{
					$list_email .= $rowuser[0].",";
					$list_dest .= $rowuser[1]." ".$rowuser[2].", ";
				}
				$rowuser = remove_magic_quotes(mysqli_fetch_row($resultuser));
			}
		}

		// print_r($tab_email);
		// echo $list_email;



		// Email de l'émetteur
		$queryuser  = "select _email, _name, _fname from user_id ";
		$queryuser .= "where _ID = ".$_SESSION["CnxID"];

		$resultuser = mysqli_query($mysql_link, $queryuser);
		$rowsuser    = ( $resultuser ) ? mysqli_fetch_row($resultuser) : 0 ;

		// Si Mail ou les deux
		if($messagetype == "M" || $messagetype == "MP")
		{
			// Encodage du sujet
            $subject = stripslashes($subject);
            $subject = html_entity_decode($subject);
            $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

			// To
			$to = $rowsuser[0];
			$to_name = $rowsuser[2]." ".$rowsuser[1];

			// clé aléatoire de limite
			$boundary = md5(uniqid(microtime(), TRUE));

			// Headers
			$headers = "From: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
			$headers .= "Reply-To: ".str_replace(" ", "-", $_SESSION['CfgTitle'])." <noreply@".$_SESSION["CfgWeb"].">\r\n";
			$headers .= 'Mime-Version: 1.0'."\r\n";
			$headers .= 'Content-Type: multipart/mixed;boundary='.$boundary."\r\n";
			$headers .= 'Reply-To: '.$to_name.' <'.$to.'>'."\r\n";


			// Message
			$msg = $msg_edt = "";

      // On récupère le nom
      $query = "SELECT _ident FROM config_centre WHERE _IDcentre = 1 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $CfgTitle = $row[0];
      }


			$msg .= $texte_ckeditor."\r\n";


			$list_email_exploded = explode(",", $list_email);

			// Si on envois l'edt à chaques personnes
			if ($_POST['sendEdt'] == "1")
			{
        $list_emails_logs = ";";

				foreach ($list_email_exploded as $key => $value) {
          $msg = $msg_edt = "";
          $msg .= $texte_ckeditor."\r\n";

					if ($value != "")
					{
						// On construit le tableau
						if ($_POST['sendEdt'] == "1")
						{
							$msg_edt .= sendEdtList($value);
              if ($msg_edt != false) $msg .= $msg_edt;
						}

						// On ajoute la signature au mail:
						$msg .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER);
            $msg .= $CfgTitle.'<br>';
						$msg .= $_SESSION["CfgAdr"] . "<br>";
						if (getParam('showLinkToSiteMail')) $msg .= "<a href=\"http://".$_SESSION['CfgWeb']."\">".$_SESSION["CfgWeb"]."</a>";

						// On construit le message final dans $body
						$body = "";

						$body .= "--$boundary\r\n";
						$body .= 'Content-type: text/html; charset=UTF-8'."\r\n";
							// $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
						$body .= "Content-Transfer-Encoding: base64\r\n\r\n";
						$body .= chunk_split(base64_encode($msg));



						// Pièce jointe
						$repertoire = opendir("tmp/server/php/files/".$_SESSION["CnxID"]);

            while(false !== ($fichier = readdir($repertoire)))
            {
              $chemin = "tmp/server/php/files/".$_SESSION["CnxID"]."/".$fichier;

              if($fichier != "." AND $fichier != ".." AND $fichier != "thumbnail" AND !is_dir($fichier))
              {
                $file_name = "tmp/server/php/files/".$_SESSION["CnxID"].$chemin;
                $myFile = $chemin;
                $myFileName = $fichier;
				$myFileName = utf8_decode($myFileName);

                if (file_exists($myFile))
                {
                  $file_size = filesize($myFile);


                  //read from the uploaded file & base64_encode content
                  $handle2 = fopen($myFile, "r");  // set the file handle only for reading the file
                  $content = fread($handle2, $file_size); // reading the file
                  fclose($handle2);                  // close upon completion
                  $file_type = filetype($myFile);
                  $encoded_content = chunk_split(base64_encode($content));

                  $body .= "--$boundary\r\n";
                  $body .="Content-Type: application/octet-stream; name=\"".$myFileName."\"\r\n";
                  $body .="Content-Disposition: attachment; filename=\"".$myFileName."\"\r\n";
                  $body .="Content-Transfer-Encoding: base64\r\n";
                  $body .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";
                  $body .= $encoded_content; // Attaching the encoded file with email

                }
              }
            }
						closedir($repertoire);

						if (getParam("MAIL_DEV_MODE") != "") $value = getParam("MAIL_DEV_MODE");
            if (($_POST['sendEdt'] == "1" && $msg_edt != false) || ($texte_ckeditor != "" && $texte_ckeditor != "<p></p>")) mail($value, utf8_decode($subject), $body, $headers);

            $list_emails_logs .= $value.";";

						$value = "";
						$to = "";
						// $msg = "";
					}
				}

        $query = "INSERT INTO mail_log SET _id = NULL, _date = NOW(), _type = '6', _dest_count = '".(substr_count($list_emails_logs, ';') - 1)."', _dest = '".$list_emails_logs."' ";
        mysqli_query($mysql_link, $query);

			}
			else
			{
				if (getParam("MAIL_DEV_MODE") == "") $headers .= 'Bcc: '.$list_email."\r\n";
				// On ajoute la signature au mail:
	      $texte_ckeditor .= $msg_mail->read($MAIL_SIGNATURE_NO_ANSWER);
        $texte_ckeditor .= $CfgTitle.'<br>';
	      $texte_ckeditor .= $_SESSION["CfgAdr"] . "<br>";
	      if (getParam('showLinkToSiteMail')) $texte_ckeditor .= "<a href=\"http://".$_SESSION['CfgWeb']."\">".$_SESSION["CfgWeb"]."</a><br>";

        $body = "";
        $body .= "--$boundary\r\n";
        $body .= 'Content-type: text/html; charset=UTF-8'."\r\n";
          // $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($texte_ckeditor));



        // Pièce jointe
        $repertoire = opendir("tmp/server/php/files/".$_SESSION["CnxID"]);

        while(false !== ($fichier = readdir($repertoire)))
        {
          // $chemin = $dossier_traite."/".$fichier;
          $chemin = "tmp/server/php/files/".$_SESSION["CnxID"]."/".$fichier;

          if($fichier != "." AND $fichier != ".." AND $fichier != "thumbnail" AND !is_dir($fichier))
          {
            $file_name = "tmp/server/php/files/".$_SESSION["CnxID"].$chemin;
            $myFile = $chemin;
            $myFileName = $fichier;
			$myFileName = utf8_decode($myFileName);

            if (file_exists($myFile))
            {
              $file_size = filesize($myFile);


              //read from the uploaded file & base64_encode content
              $handle2 = fopen($myFile, "r");  // set the file handle only for reading the file
              $content = fread($handle2, $file_size); // reading the file
              fclose($handle2);                  // close upon completion
              $file_type = filetype($myFile);
              $encoded_content = chunk_split(base64_encode($content));

              $body .= "--$boundary\r\n";
              $body .="Content-Type: application/octet-stream; name=\"".$myFileName."\"\r\n";
              $body .="Content-Disposition: attachment; filename=\"".$myFileName."\"\r\n";
              $body .="Content-Transfer-Encoding: base64\r\n";
              $body .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";
              $body .= $encoded_content; // Attaching the encoded file with email

            }
          }
        }
        closedir($repertoire);

        $list_emails_logs = str_replace(',', ';', $list_email);
        $list_emails_logs = ';'.$list_emails_logs.';';
        $list_emails_logs = str_replace(';;', ';', $list_emails_logs);
        $query = "INSERT INTO mail_log SET _id = NULL, _date = NOW(), _type = '6', _dest_count = '".(substr_count($list_emails_logs, ';') - 1)."', _dest = '".$list_emails_logs."' ";
        mysqli_query($mysql_link, $query);

				if (getParam("MAIL_DEV_MODE") != "") $to = getParam("MAIL_DEV_MODE");
				mail($to, utf8_decode($subject), $body, $headers);
			}


			// Suppression des PJ en local
			suppression("tmp/server/php/files/".$_SESSION["CnxID"]);
		}
// print_r($tab_email);
		// Si Postit ou les deux
		if($messagetype == "P" || $messagetype == "MP")
		{
			// envoie à un destinataire
			$ret = sendBroadcastMessage($tab_email, $subject, $texte_ckeditor, "N", 0);

      $list_emails_logs = ";";
      foreach ($tab_email as $key => $value) {
        $list_emails_logs .= getUserEmailByID($value).';';

      }
      $query = "INSERT INTO mail_log SET _id = NULL, _date = NOW(), _type = '7', _dest_count = '".(substr_count($list_emails_logs, ';') - 1)."', _dest = '".$list_emails_logs."' ";
      mysqli_query($mysql_link, $query);

			// Pièce jointe
      $repertoire = opendir("download/email/files/".$_SESSION["CnxID"]);

			$u = 0;
			while(false !== ($fichier = readdir($repertoire)))
			{
				// $chemin = $dossier_traite."/".$fichier;
        $chemin = $fichier;
				// $idpj = $ret[$u];

				if($fichier!="." AND $fichier!=".." AND $fichier != "thumbnail" AND !is_dir($fichier))
				{
					$file_name = "download/email/files/".$_SESSION["CnxID"].'/'.$chemin;
					if (file_exists($file_name) && substr($chemin, 0, 1) != '.' && $chemin != 'index.html' && $chemin != 'index.htm' && $chemin != 'index.php')
					{
						$file_type = filetype($file_name);
						$file_size = filesize($file_name);
						$info = new SplFileInfo($file_name);
						$ext = $info->getExtension();

						$handle = fopen($file_name, 'r') or die('File '.$file_name.'can t be open');
						$content = fread($handle, $file_size);
						$content = chunk_split(base64_encode($content));
						$f = fclose($handle);

						for ($i = 0; $i < count($ret); $i++)
						{
							$Query  = "INSERT INTO postit_pj ";
							$Query .= "values(NULL, '".$ret[$i]."', '$fichier', '$ext', '$file_size', NOW())";

							if ( !mysqli_query($mysql_link, $Query) )
							{
								sql_error($mysql_link);
							}
							else
							{
								// fichier destination
								$dest = "$DOWNLOAD/post-it/". mysqli_insert_id($mysql_link) ."_".nettoyerChaine($fichier);

								// copie du fichier temporaire -> répertoire de stockage
								// attention : il faut copier le fichier plusieurs fois
								if ( !copy($file_name, $dest) )
									$texte_ckeditor = $msg->read($POSTIT_ERRDOWNLOAD);
							}
						}
					}
					$u++;
				}
			}
			closedir($repertoire);
		}

		echo "<center>Message envoyé à : <br /><strong>$list_dest</strong></center>";


	}

	// saisie du formulaire si
	// - l'utilisateur n'a pas validé OU
	// - il y a une erreur de saisie => on redemande de compléter le formulaire
	if ( !$submit )
	{
		// Vérifier si l'utilisateur à un email
		$queryuser  = "select _email ";
		$queryuser .= "FROM user_id ";
		$queryuser .= "WHERE _ID = ".$_SESSION["CnxID"]." ";
		$queryuser .= "AND _email LIKE '%@%' ";

		// sélection des destinataires
		$resultuser = mysqli_query($mysql_link, $queryuser);
		$rowuser    = ( $resultuser ) ? mysqli_fetch_row($resultuser) : 0 ;

		if($rowuser)
		{
			// On supprime les PJ en local si elles n'ont pas été supprimée précedement
			suppression("tmp/server/php/files/".$_SESSION["CnxID"]);

?>




<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Titre</h1>
</div>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary"><?php echo $msg->read($POSTIT_EXP); ?>: <?php echo $_SESSION["CnxName"]; ?></h6>
  </div>
  <div class="card-body">
    <form id="formulaire" action="index.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="item"   value="<?php echo $item; ?>">
      <input type="hidden" name="cmde"   value="<?php echo $cmde; ?>">
      <input type="hidden" name="IDtype" value="<?php echo $IDtype; ?>">
      <input type="hidden" name="submita" value="1">


      <div class="form-group row">
        <label for="messagetype" class="col-sm-2 col-form-label">Type:</label>
        <div class="col-sm-10">
          <select class="custom-select" id="messagetype" name="messagetype" required>
            <option value="M"><?php echo $msg->read($POSTIT_TYPE1); ?></option>
            <option value="P"><?php echo $msg->read($POSTIT_TYPE2); ?></option>
            <option value="MP"><?php echo $msg->read($POSTIT_TYPE3); ?></option>
          </select>
        </div>
      </div>


      <hr>
      <h5>Destinataires:</h5>


      <div class="form-group row">
        <label for="UserTags" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_USER); ?></label>
        <div class="col-sm-10">
					<ul id="UserTags" name="UserTags"></ul>
        </div>
      </div>





















      <?php if ($_SESSION['CnxGrp'] > 1) { ?>
        <!-- Classe -->
        <div class="form-group row">
          <label for="ClassTags" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_CLASS); ?></label>
          <div class="col-sm-10">
            <ul id="ClassTags"></ul>
          </div>
        </div>

        <!-- Groupes -->
        <div class="form-group row">
          <label for="GroupTags" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_GROUP); ?></label>
          <div class="col-sm-10">
            <ul id="GroupTags"></ul>
          </div>
        </div>

        <!-- Liste de diffusion -->
        <div class="form-group row tr_hidden" style="display: none;">
          <label for="DiffTags" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_DIFF); ?></label>
          <div class="col-sm-10">
            <ul id="DiffTags"></ul>
          </div>
        </div>

        <!-- Tous les profs d'une classe -->
        <div class="form-group row tr_hidden" style="display: none;">
          <label for="ClassProfTags" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_CLASSPROF); ?></label>
          <div class="col-sm-10">
            <ul id="ClassProfTags"></ul>
          </div>
        </div>

        <!-- Tous les profs d'une matière -->
        <div class="form-group row tr_hidden" style="display: none;">
          <label for="MatProfTags" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_MATPROF); ?></label>
          <div class="col-sm-10">
            <ul id="MatProfTags"></ul>
          </div>
        </div>

        <div class="form-inline">
          <!-- Toute l'administration -->
          <div class="form-check mb-2 mr-sm-2">
            <input class="form-check-input" type="checkbox" name="ck_admin" id="ck_admin" <?php if(isset($ck_admin) && $ck_admin) echo "checked=\"checked\""; ?>>
            <label class="form-check-label" for="ck_admin">
              <?php echo $msg->read($POSTIT_ADMIN); ?>
            </label>
          </div>

          <!-- Tous les enseignants -->
          <div class="form-check mb-2 mr-sm-2">
            <input class="form-check-input" type="checkbox" name="ck_ens" id="ck_ens" <?php if(isset($ck_ens) && $ck_ens) echo "checked=\"checked\""; ?>>
            <label class="form-check-label" for="ck_ens">
              <?php echo $msg->read($POSTIT_ENS); ?>
            </label>
          </div>
        </div>



        <button class="btn btn-secondary" id="btn_hidden" type="button">
          <i class="fas fa-plus" id="show_hide_btn"></i>
        </button>
      <?php } ?>


      <hr>
      <h5>Message:</h5>

      <div class="form-group row">
        <label for="subject" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_SUBJECT); ?></label>
        <div class="col-sm-10">
          <input type="text" name="subject" id="subject" class="form-control" required value="<?php echo stripslashes(@$titre_post); ?>">
        </div>
      </div>


      <div class="form-group row">
        <label for="texte_ckeditor" class="col-sm-2 col-form-label"><?php echo $msg->read($POSTIT_MESSAGE); ?></label>
        <div class="col-sm-10">
          <textarea type="text" name="texte_ckeditor" id="texte_ckeditor" class="form-control" required><?php echo stripslashes(@$texte_ckeditor_post); ?></textarea>
        </div>
      </div>



      <?php if ($_SESSION['CnxAdm'] == 255 || $_SESSION['CnxGrp'] == 4) { ?>
        <div class="form-check mb-2 mr-sm-2" id="sendEdt">
          <input class="form-check-input" type="checkbox" name="sendEdt" id="sendEdt" value="1">
          <label class="form-check-label" for="ck_ens">
            Joindre l'EDT de la semaine à venir
          </label>
        </div>
      <?php } ?>




      <hr>





    </form>




    <!-- blueimp Gallery styles -->
    <link rel="stylesheet" href="css/fileupload/blueimp/blueimp-gallery.min.css">
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="css/fileupload/jquery.fileupload.css">
    <link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui.css">
    <!-- CSS adjustments for browsers with JavaScript disabled -->
    <noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-noscript.css"></noscript>
    <noscript><link rel="stylesheet" href="css/fileupload/jquery.fileupload-ui-noscript.css"></noscript>



    <div style="border: grey 3px dashed; line-height: 28px; padding: 15px; border-radius: 10px;">
      <!-- The file upload form used as target for the file upload widget -->
      <form id="fileupload" action="index.php" method="POST" enctype="multipart/form-data">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar" style="margin-left: 5px">
          <div class="row mb-2">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success btn-sm mr-2 fileinput-button">
              <i class="glyphicon glyphicon-plus"></i>
              <span><?php echo $msg->read($POSTIT_ADDFILE); ?></span>
              <input type="file" name="files[]" multiple>
            </span>
            <button type="reset" class="btn btn-warning btn-sm mr-2 cancel" style="display: none;">
              <i class="glyphicon glyphicon-ban-circle"></i>
              <span><?php echo $msg->read($POSTIT_CANCEL); ?></span>
            </button>
            <button type="button" class="btn btn-danger btn-sm mr-2 delete" style="display: none;">
              <i class="glyphicon glyphicon-trash"></i>
              <span><?php echo $msg->read($POSTIT_DELETE); ?></span>
            </button>
            <input type="checkbox" class="toggle" style="display: none;">
            <!-- The global file processing state -->
            <span class="fileupload-process"></span>
          </div>
          <!-- The global progress state -->
          <div class="row mb-2 fileupload-progress">
            <!-- The global progress bar -->
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
              <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
            <!-- The extended global progress state -->
            <div class="progress-extended">&nbsp;</div>
          </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table"><tbody class="files"></tbody></table>
      </form>

    </div>
    <!-- The blueimp Gallery widget -->
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
      <div class="slides"></div>
      <h3 class="title"></h3>
      <a class="prev"></a>
      <a class="next"></a>
      <a class="close">×</a>
      <a class="play-pause"></a>
      <ol class="indicator"></ol>
    </div>
    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
      <tr class="template-upload">
        <td>
          <span class="preview"></span>
        </td>
        <td>
          <p class="name">{%=file.name%}</p>
          <strong class="error text-danger"></strong>
        </td>
        <td>
          <p class="size">Processing...</p>
          <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
          {% if (!i && !o.options.autoUpload) { %}
            <button class="btn btn-primary btn-sm start" disabled>
              <i class="glyphicon glyphicon-upload"></i>
              <span>Start</span>
            </button>
          {% } %}
          {% if (!i) { %}
            <button class="btn btn-warning btn-sm cancel">
              <i class="glyphicon glyphicon-ban-circle"></i>
              <span>Cancel</span>
            </button>
          {% } %}
        </td>
      </tr>
    {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
      <tr class="template-download">
        <td>
          <span class="preview">
            {% if (file.thumbnailUrl) { %}
              <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
            {% } %}
          </span>
        </td>
        <td>
          <p class="name">
            {% if (file.url) { %}
              <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            {% } else { %}
              <span>{%=file.name%}</span>
            {% } %}
          </p>
          {% if (file.error) { %}
            <div><span class="label label-danger">Error</span> {%=file.error%}</div>
          {% } %}
        </td>
        <td>
          <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
          {% if (file.deleteUrl) { %}
            <button class="btn btn-danger btn-sm delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
              <i class="glyphicon glyphicon-trash"></i>
              <span><?php echo $msg->read($POSTIT_DELETE); ?></span>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle" style="display: none;">
          {% } else { %}
            <button class="btn btn-warning cancel">
              <i class="glyphicon glyphicon-ban-circle"></i>
              <span><?php echo $msg->read($POSTIT_CANCEL); ?></span>
            </button>
          {% } %}
        </td>
      </tr>
    {% } %}
    </script>

          <?php $showGedScripts = true; ?>
          <?php $ged_type_fiche = 'email'; ?>
          <?php $ged_num_fiche = $_SESSION['CnxID']; ?>



    <div class="mt-3">
      <button type="submit" form="formulaire" class="btn btn-success">Valider</button>
      <a href="<?php echo myurlencode("index.php?item=$item&IDroot=$IDroot&sort=$sort"); ?>" class="btn btn-danger">Fermer</a>
    </div>


  </div>
</div>









				<?php
			}
			else
			{
				echo "<div class=\"alert alert-error\">".$msg->read($POSTIT_MAILREQUIRE)."</div>";
			}
		}
?>

</div>



<script>
checkIfWeCanSendEDT();

$("#messagetype").change(function() {
	checkIfWeCanSendEDT();
});

function checkIfWeCanSendEDT() {
	var messagetype = $("#messagetype").val();
	if (messagetype == "M") $("#sendEdt").show();
	else $("#sendEdt").hide();
}



function fileListReady() {
  console.log('Ready');
}

</script>





<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#ClassTags").tagit({
      autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=6", html: 'html'},
      allowDuplicates: false,
      singleField: false,
      fieldName: "ClassTags[]"
    });

    <?php
    if (isset($list_class)) {
      $ar_list_class = explode(",", $list_class);
      foreach($ar_list_class as $val)
      {
        ?>
        jQuery("#ClassTags").tagit("createTag", "<?php echo $val; ?>");
        <?php
      }
    }
    ?>
  });
</script>


<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#UserTags").tagit({
      autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=5", html: 'html'},
      allowDuplicates: false,
      singleField: false,
      fieldName: "UserTags[]"
    });

    <?php
    if (isset($list_user) && strpos($list_user, ',') !== false) {
      $ar_list_user = explode(",", $list_user);
      foreach($ar_list_user as $val) { ?>
        jQuery("#UserTags").tagit("createTag", "<?php echo $val; ?>");
      <?php } ?>
    <?php } ?>

    <?php if(!empty($_GET['IDpost']) && @$_GET["submit"] == "reply") { ?>
      jQuery("#UserTags").tagit("createTag", "<?php echo $user_reply; ?>");
    <?php } ?>
  });
</script>




<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#GroupTags").tagit({
      autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=7", html: 'html'},
      allowDuplicates: false,
      singleField: false,
      fieldName: "GroupTags[]"
    });

    <?php
    if (isset($list_group)) {
      $ar_list_group = explode(";", $list_group);
      foreach($ar_list_group as $val)
      {
        ?>
        jQuery("#GroupTags").tagit("createTag", "<?php echo $val; ?>");
        <?php
      }
    }

    ?>
  });
</script>
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#DiffTags").tagit({
      autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=8", html: 'html'},
      allowDuplicates: false,
      singleField: false,
      fieldName: "DiffTags[]"
    });

    <?php
    if (isset($list_group)) {
      $ar_list_diff = explode(";", $list_diff);
      foreach($ar_list_diff as $val)
      {
        ?>
        jQuery("#DiffTags").tagit("createTag", "<?php echo $val; ?>");
        <?php
      }
    }
    ?>
  });
</script>
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#ClassProfTags").tagit({
      autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=9", html: 'html'},
      allowDuplicates: false,
      singleField: false,
      fieldName: "ClassProfTags[]"
    });

    <?php
    if (isset($list_group)) {
      $ar_list_class = explode(",", $list_classprof);
      foreach($ar_list_class as $val)
      {
        ?>
        jQuery("#ClassProfTags").tagit("createTag", "<?php echo $val; ?>");
        <?php
      }
    }
    ?>
  });
</script>
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#MatProfTags").tagit({
      autocomplete: {delay: 0, minLength: 1, source: "getInfos.php?type=10", html: 'html'},
      allowDuplicates: false,
      singleField: false,
      fieldName: "MatProfTags[]"
    });

    <?php
    if (isset($list_group)) {
      $ar_list_class = explode(",", $list_matprof);
      foreach($ar_list_class as $val)
      {
        ?>
        jQuery("#MatProfTags").tagit("createTag", "<?php echo $val; ?>");
        <?php
      }
    }
    ?>
  });
</script>


<script>
jQuery( document ).ready(function() {
  jQuery("#btn_hidden").click(function ()
  {
    if ( jQuery( ".tr_hidden" ).is( ":hidden" ) )
    {
      jQuery("#show_hide_btn").removeClass("fa-plus").addClass("fa-minus");
      jQuery(".tr_hidden").slideDown("slow");
    }
    else
    {
      jQuery("#show_hide_btn").removeClass("fa-minus").addClass("fa-plus");
      jQuery(".tr_hidden").slideUp("slow");
    }
  });
});
</script>

<script>
$(document).ready(function() {
  CKEDITOR.replace('texte_ckeditor', {width: '100%'});
})
</script>
