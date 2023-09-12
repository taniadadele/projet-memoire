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
 *		module   : user_visu.php
 *		projet   : page de visualisation des comptes des utilisateurs
 *
 *		version  : 2.1
 *		auteur   : laporte
 *		creation : 20/10/02
 *		modif    : 16/08/05 - par D. Laporte
 *                     affichage par centre
 *		           17/07/06 - Nordine Zetoutou
 * 	                 migration des balises HTML en XHTML 1.0 strict
 *					 		 11/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7
 */


$IDcentre = ( strlen(@$_POST["IDcentre"]) )	// ID du centre
	? (int) $_POST["IDcentre"]
	: (int) (strlen(@$_GET["IDcentre"]) ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;


if (isset($_GET['IDalpha'])) $IDalpha = addslashes(stripslashes($_GET['IDalpha']));
elseif (isset($_POST['IDalpha'])) $IDalpha = addslashes(stripslashes($_POST['IDalpha']));
elseif (isset($_SESSION['USER_IDalpha'])) $IDalpha = addslashes(stripslashes($_SESSION['USER_IDalpha']));
else $IDalpha = "";

$_SESSION["USER_IDalpha"] = $IDalpha;
$recuseralpha  = ( @$_GET["recuseralpha"] )			// recherche user
	? $_GET["recuseralpha"]
	: "A" ;
$visu     = ( @$_POST["visu"] )			// type de visualisation
	? (int) $_POST["visu"]
	: (int) @$_GET["visu"] ;
$IDsel    = ( $visu )					// catégorie
	? (int) $_SESSION["CnxClass"]
	: (int) (strlen(@$_POST["IDsel"]) ? $_POST["IDsel"]: @$_SESSION["USER_IDsel"] );
$_SESSION["USER_IDsel"] = $IDsel;
$sort     = ( strlen(@$_POST["sort"]) )		// filtre affichage sur les dates de connexion
	? $_POST["sort"]
	: @$_SESSION["USER_sort"] ;
$_SESSION["USER_sort"] = $sort;
$sortadm  = ( strlen(@$_POST["sortadm"]) )	// filtre affichage sur les droits utilisateur
	? $_POST["sortadm"]
	: @$_GET["sortadm"] ;
$mylang   = ( strlen(@$_POST["mylang"]) )		// filtre affichage sur la langue
	? $_POST["mylang"]
	: @$_GET["mylang"] ;

$ID       = ( @$_POST["ID"] )				// ID de l'utilisateur
	? (int) $_POST["ID"]
	: (int) @$_GET["ID"] ;
$authuser = ( @$_POST["authuser"] )			// validation des comptes utilisateurs
	? (int) $_POST["authuser"]
	: (int) @$_GET["authuser"] ;


$skshow   = ( @$_GET["skshow"] )			// n° du flash info
	? (int) $_GET["skshow"]
	: 1 ;

// Pagination
if (isset($_GET['skpage']) && $_GET['skpage'] != '') $skpage = $_GET['skpage'];
elseif (getUserParam($_SESSION['CnxID'], 'USER_skpage') !== null && is_numeric(getUserParam($_SESSION['CnxID'], 'USER_skpage'))) $skpage = getUserParam($_SESSION['CnxID'], 'USER_skpage');
else $skpage = 1;
setUserParam($_SESSION['CnxID'], 'USER_skpage', $skpage, 'La valeur de pagination sur la page de liste des utilisateurs');


if (isset($_GET['IDpromotion'])) $IDpromotion = addslashes(stripslashes($_GET['IDpromotion']));
elseif (isset($_POST['IDpromotion'])) $IDpromotion = addslashes(stripslashes($_POST['IDpromotion']));
elseif (isset($_SESSION['USER_filter_IDpromotion'])) $IDpromotion = addslashes(stripslashes($_SESSION['USER_filter_IDpromotion']));
else $IDpromotion = 0;
$_SESSION['USER_filter_IDpromotion'] = $IDpromotion;



// Select du type de compte
if (isset($_GET['accountType'])) $accountType = $_GET['accountType'];
elseif (isset($_POST['accountType'])) $accountType = $_POST['accountType'];
elseif (isset($_SESSION['accountType']))$accountType = $_SESSION["accountType"];
else $accountType = "all";
$_SESSION["accountType"] = $accountType;
$statusID = 0;
switch ($accountType) {
	case 'all':
		$IDsel    = 0;
		$statusID = "all";
		break;

	// Pour les élèves
	case 'E_all':
		$IDsel    = 1;
		$statusID = "all";
		break;
	case 'E_0':
		$IDsel    = 1;
		$statusID = 0;
		break;
	case 'E_1':
		$IDsel    = 1;
		$statusID = 1;
		break;
	case 'E_-1':
		$IDsel    = 1;
		$statusID = -1;
		break;
	case 'E_-2':
		$IDsel    = 1;
		$statusID = -2;
		break;
	case 'E_-3':
		$IDsel    = 1;
		$statusID = -3;
		break;

	// Pour les enseignants
	case 'P_all':
		$IDsel    = 2;
		$statusID = "all";
		break;
	case 'P_0':
		$IDsel    = 2;
		$statusID = 0;
		break;
	case 'P_1':
		$IDsel    = 2;
		$statusID = 1;
		break;
	case 'P_-1':
		$IDsel    = 2;
		$statusID = -1;
		break;
	case 'P_-2':
		$IDsel    = 2;
		$statusID = -2;
		break;

	// Pour les administratifs
	case 'A':
		$IDsel    = 4;
		$statusID = "all";
		break;

	// Au cas ou
	default:
		$IDsel    = 0;
		$statusID = "all";
		break;

}

if (isset($_POST['ModeList'])) $ModeList = $_POST['ModeList'];
elseif (getUserParam($_SESSION['CnxID'], 'USER_ModeList') !== null) $ModeList = getUserParam($_SESSION['CnxID'], 'USER_ModeList');
else $ModeList = 'list';

setUserParam($_SESSION['CnxID'], 'USER_ModeList', $ModeList, 'Le mode de vue de l\'utilisateur sur la liste des utilisateurs (trombinoscope ou liste)');

$submit   = ( @$_POST["valid"] )			// bouton de validation
	? $_POST["valid"]
	: @$_GET["submit"] ;

$_SESSION["Litem"] = 1;
?>


<?php
	//---------------------------------------------------------------------------
	function sendMail($id)
	{
	/*
	 * fonction :	envoie de l'avertissement d'une ouverture de compte
	 * in :		$id : ID du compte utilisateur
	 */

		require "globals.php";

		require "msg/user.php";
		require_once "include/TMessage.php";

		$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/user.php");

		// lecture du mél de l'utilisateur
		$query  = "select _email from user_id ";
		$query .= "where _ID = '$id' ";
		$query .= "limit 1";

		$result = mysqli_query($mysql_link, $query);
		$row    = mysqli_fetch_row($result);

		if ( $row ) {
			require_once "lib/libmail.php";

			// entête du message
			$subject = $msg->read($USER_SENDPWD);

			// entête du corps du message
			$texte   = $msg->read($USER_WELCOME, $_SESSION["CfgWeb"]) ."\n";

			// pied de page du corps du message
			$texte  .= "\n--\n";
			$texte  .= $_SESSION["CfgAdr"] . "\n";
			$texte  .= $_SESSION["CfgWeb"];

			$mymail  = new Mail(); // create the mail

			$mymail->From("noreply@".$_SESSION["CfgWeb"]);
			$mymail->Subject(stripslashes($subject));
			$mymail->Body(stripslashes($texte), $CHARSET);	// set the body

			$mymail->Send();	// send the mail
			}
	}
	//---------------------------------------------------------------------------



	// On récupère les différents intitulés de groupes pour l'affichage
	$query  = "select _IDgrp, _ident, _delay from user_group where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
	if ($_SESSION['CnxGrp'] == 2) $query .= "AND `_IDgrp` = 1 ";
	$query .= "order by _IDgrp";
	$result = mysqli_query($mysql_link, $query);
	$user_group = array();
	while ($cat = mysqli_fetch_row($result)) $user_group[$cat[0]] = $cat[1];



	// seul l'administrateur peut efectuer ces commandes
	if ( strlen($submit) AND $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) {
		// modification des droits d'un utilisateur
		if (isset($_POST) && isset($_POST['my_id'])) {
			$my_id = $_POST["my_id"];
			$date  = date("Y-m-d H:i:s");
			for ($i = 0; $i < count($my_id); $i++)
				if (isset($my_id[$i]) && $my_id[$i] != '')
				{
					if (isset($_POST["chk_$my_id[$i]"])) $admin = $_POST["chk_$my_id[$i]"];
					else continue;
					// compte utilisateur
					if ($admin == 255)
					{
						$query  = "update user_id ";
						$query .= "set _adm = '$admin', _visible = 'O', `_IDgrp` = 4 ";
						$query .= ( $authuser AND $admin ) ? ", _create = '$date' " : "" ;
						$query .= "where _ID = '$my_id[$i]' ";
						$query .= "limit 1";
					}
					else
					{
						// -3 = Anciens étudiants
						// -2 = Exclus
						// 0 = En attente
						// 1 = Courant
						switch ($admin) {
							case '-3':
								$admin_select = "-3";
								break;
							case '-2':
								$admin_select = "-2";
								break;
							case '0':
								$admin_select = "0";
								break;
							case '1':
								$admin_select = "1";
								break;

							default:
								// code...
								break;
						}
						if ($admin != "")
						{
							$query  = "update user_id ";
							$query .= "set _adm = '$admin_select', _visible = 'O' ";
							$query .= ( $authuser AND $admin ) ? ", _create = '$date' " : "" ;
							$query .= "where _ID = '$my_id[$i]' ";
							$query .= "limit 1";
						}
					}
					if ( mysqli_query($mysql_link, $query) ) {
						// envoi email d'ouverture de compte
						if ( $authuser AND $admin )
							sendMail($my_id[$i]);
					}
				}
		}

		// suppression d'un compte
		if ( $submit == "delete" ) {
			// compte utilisateur
			$query  = "delete from user_id ";
			$query .= "where _ID = '$ID' ";
			$query .= "limit 1";

			if ( !mysqli_query($mysql_link, $query) )
				sql_error($mysql_link);
			else {
				echo '<script>
				Toast.fire({
				  icon: \'success\',
				  title: "L\'utilisateur à bien été supprimé"
				})
				</script>';
			}
		}
	}

	if($ModeList == "trombi" && 1 == 0)
	{
		?>
		<style>
			.block_user {
				display: inline-block;
				width: 24%
			}

			.block_droits, .block_infos {
				display: none !important;
			}

			.block_photo {
				width: 100% !important;
				text-align: center;
			}

			.block_nomtrombi {
				display: block !important;
			}
		</style>
		<?php
	}
	?>

<form id="formulaire" class="bs-docs-example noprint" action="index.php?skpage=<?php if (isset($_GET['skpage'])) echo $_GET['skpage']; ?>" method="post">
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
	  <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($USER_ROLE, $_SESSION["CfgIdent"]); ?></h1>

		<div style="float: right; text-align: right;">
			<div class="mb-3" style="text-align: right;">
				<?php if ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) { ?>
					<a href="<?php echo myurlencode("index.php?item=38&Litem=1&cmde=account&show=0"); ?>" class="noprint">
						<button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm noprint" type="button" title="<?php echo $msg->read($USER_ADDRECORD); ?>">
							<i class="fa fa-plus-square" title="<?php echo $msg->read($USER_ADDRECORD); ?>"></i> Nouvel utilisateur
						</button>
					</a>
				<?php } ?>
				<a href="<?php echo $_SESSION["ROOTDIR"]; ?>/exports.php?item=1&cmde=&IDcentre=<?php echo $IDcentre; ?>&IDalpha=<?php echo $IDalpha; ?>&recuseralpha=<?php echo $recuseralpha; ?>&visu=<?php echo $visu; ?>&IDsel=<?php echo $IDsel; ?>&sort=<?php echo $sort; ?>&sortadm=<?php echo $sortadm; ?>&mylang=<?php echo $mylang; ?>&ID=<?php echo $ID; ?>&authuser=<?php echo $authuser; ?>&skpage=<?php echo $skpage; ?>
					&skshow=<?php echo $skshow; ?>&ModeList=<?php echo $ModeList; ?>&accountType=<?php echo $accountType; ?>" onclick="window.open(this.href, '_blank'); return false;" class="noprint">
					<button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" type="button" title="Exporter" >
						<i class="fa fa-upload" title="Exporter"></i> Exporter
					</button>
				</a>
				<button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" title="Imprimer" type="button" onclick="window.print();return false;">
					<i class="fa fa-print" title="Imprimer"></i> Imprimer
				</button>
			</div>

			<div class="mb-3">
				<input type="hidden" name="item" value="<?php print $item; ?>">
				<input type="hidden" name="IDcentre" value="<?php print $IDcentre; ?>">
				<input type="hidden" name="IDcentre" value="<?php print $IDcentre; ?>">
				<input type="hidden" name="IDsel" value="<?php print $IDsel; ?>">
				<input type="hidden" name="accountType" value="<?php echo $accountType; ?>">
				<input type="hidden" name="IDpromotion" value="<?php echo $IDpromotion; ?>">
				<input type="hidden" name="authuser" value="<?php print $authuser; ?>">
				<input type="hidden" name="visu" value="<?php print $visu; ?>">
				<input type="hidden" name="sort" value="<?php print $sort; ?>">
				<input type="hidden" name="sortadm" value="<?php print $sortadm; ?>">
				<input type="hidden" name="mylang" value="<?php print $mylang; ?>">
				<input type="hidden" name="recuseralpha" value="on">
				<input type="hidden" name="item"     value="<?php echo $item; ?>" />
				<input type="hidden" name="authuser" value="<?php echo $authuser; ?>" />
				<input type="hidden" name="ModeList" id="ModeList" value="<?php echo $ModeList; ?>" />
				<input type="hidden" name="IDcentre" value="<?php echo $IDcentre; ?>" />

				<div class="btn-toolbar" role="toolbar">
					<div class="form-row">
						<!-- Mode d'affichage -->
						<div class="col">
							<div class="btn-group mr-2" role="group" aria-label="Basic example">
								<button class="d-none d-sm-inline-block btn btn-<?php if ($ModeList != 'trombi') echo 'secondary'; else echo 'light'; ?> shadow-sm noprint" onclick="$('#ModeList').val('');document.forms.formulaire.submit()"><i class="fa fa-list"></i></button>
								<button class="d-none d-sm-inline-block btn btn-<?php if ($ModeList == 'trombi') echo 'secondary'; else echo 'light'; ?> shadow-sm noprint" onclick="$('#ModeList').val('trombi');document.forms.formulaire.submit()"><i class="fa fa-users"></i></button>
							</div>
						</div>
						<!-- Recherche -->
						<div class="col">
							<div class="input-group">
								<div class="input-group-prepend">
									<button class="btn btn-outline-secondary" type="submit">Ok</button>
								</div>
								<input type="text" id="appendedInputButton" name="IDalpha" class="form-control" placeholder="" value="<?php echo stripslashes($IDalpha); ?>">
							</div>
						</div>

						<!-- Filtre par promotion -->
						<?php if ($IDsel == 1) { ?>
							<!-- Menu de select de la promotion -->
							<div class="col">
								<select name="IDpromotion" onchange="document.forms.formulaire.submit()" class="custom-select">
									<?php
										$selected_option = "";
										if ($IDpromotion == 0) $selected_option = "selected";
										if ($_SESSION['CnxGrp'] != 1) echo "<option value=\"0\" ".$selected_option.">Toutes les promotions</option>";
										$selected_option = "";
										$query = "SELECT DISTINCT _IDclass FROM user_id WHERE _IDclass != 0 AND _IDclass != '' AND _adm = '1' AND _IDgrp = '1' ORDER BY _IDclass DESC ";
										$result = mysqli_query($mysql_link, $query);
										while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
											if ($IDpromotion == $row[0]) $selected_option = "selected";
											else $selected_option = "";
											if (checkIfClassIsVisibleByID($row[0])) echo "<option value=\"".$row[0]."\" ".$selected_option.">".getClassNameByClassID($row[0])."</option>";
										}
									?>
								</select>
							</div>
						<?php } else {
						// Si on affiche pas le filtre des promotion alors on le force à 0 pour le réinitialiser
							$_SESSION['USER_filter_IDpromotion'] = 0;
							$IDpromotion = 0;
						}
						?>
						<!-- Menu de select de type de compte -->
						<div class="col">
							<select id="accountType" name="accountType" onchange="document.forms.formulaire.submit()" class="custom-select">
								<?php if ($_SESSION['CnxGrp'] > 2) { ?>
									<option disabled>Type de compte</option>
									<option value="all">Tous les comptes</option>
									<option disabled>──────────────</option>
								<?php } ?>
								<optgroup label="Groupe des étudiants">
									<option value="E_all" <?php if ($accountType == "E_all") echo "selected"; ?>>Tous les étudiants</option>
									<?php if ($_SESSION['CnxGrp'] > 2) { ?>
										<option value="E_-1" <?php if ($accountType == "E_-1") echo "selected"; ?>>En attente validation mail</option>
										<option value="E_0" <?php if ($accountType == "E_0") echo "selected"; ?>>En attente validation admin</option>
										<option value="E_1" <?php if ($accountType == "E_1") echo "selected"; ?>>Courant</option>
										<option value="E_-2" <?php if ($accountType == "E_-2") echo "selected"; ?>>Exclus</option>
										<option value="E_-3" <?php if ($accountType == "E_-3") echo "selected"; ?>>Anciens étudiants</option>
									<?php } ?>
								</optgroup>
								<?php if ($_SESSION['CnxGrp'] > 2) { ?>
									<option disabled>──────────────</option>
									<optgroup label="Groupe des formateurs">
										<option value="P_all" <?php if ($accountType == "P_all") echo "selected"; ?>>Tous les formateurs</option>
										<option value="P_-1" <?php if ($accountType == "P_-1") echo "selected"; ?>>En attente validation mail</option>
										<option value="P_0" <?php if ($accountType == "P_0") echo "selected"; ?>>En attente validation admin</option>
										<option value="P_1" <?php if ($accountType == "P_1") echo "selected"; ?>>Courant</option>
										<option value="P_-2" <?php if ($accountType == "P_-2") echo "selected"; ?>>En veille</option>
									</optgroup>
									<option disabled>──────────────</option>
									<option value="A" <?php if ($accountType == "A") echo "selected"; ?>>Groupe des administratifs</option>
								<?php } ?>
							</select>
						</div>
						<!-- Tri -->
						<div class="col">
							<select id="sort" name="sort" onchange="document.forms.formulaire.submit()" class="custom-select">
								<?php
									echo '<option value="0">'.$msg->read($USER_SELECTDATE).'</option>';
									$list = explode('|', $msg->read($USER_FILTER));
									for ($i = 1; $i < count($list); $i++) {
										$select = ( $sort == $i ) ? 'selected="selected"' : '' ;
										echo '<option value="'.$i.'" '.$select.'>'.$list[$i].'</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<?php
		// on classe par ordre alphabétique
		if ($recuseralpha == "on" ) $recuseralpha = "like '$IDalpha%'"; else $recuseralpha = ">= '$IDalpha'";
		$Query  = "select distinctrow ";
		$Query .= "user_id._ID, user_id._cnx, user_id._title, user_id._fonction, user_id._sexe, user_id._adm, user_id._IDgrp, user_id._lastcnx, user_id._create, user_id._delay, user_id._lang, user_id._email, user_id._IDcentre, user_id._tel, ";
		$Query .= "user_id._mobile, user_id._IDclass, user_id._name ";
		$Query .= "from user_id, user_group " ;
		$Query .= "where (user_id._IDgrp = user_group._IDgrp ";
		$Query .= "OR user_id._IDgrp = 0) ";
		$Query .= ( $authuser ) ? "AND user_id._create = '0000-00-00 00:00:00' " : "AND user_id._create != '0000-00-00 00:00:00' " ;
		$Query .= ( $IDsel ) ? "AND user_id._IDgrp = '$IDsel' " : "" ;
		if ($statusID != "all" or $statusID == "0") $Query .= "AND user_id._adm = '".$statusID."' ";
		$Query .= ( $IDcentre ) ? "AND (user_id._IDcentre = '$IDcentre' OR user_id._centre & pow(2, $IDcentre - 1)) " : "" ;
		$Query .= ( strlen($IDalpha) ) ? "AND (user_id._name LIKE '%$IDalpha%' OR user_id._fname LIKE '%$IDalpha%' OR user_id._email LIKE '%$IDalpha%') " : "" ;	// Recherche
		if ($IDpromotion != 0) $Query .= "AND _IDclass = '".$IDpromotion."' ";		// Id de la promotion
		$Query .= ( $mylang ) ? "AND user_id._lang = '$mylang' " : "" ;
		$Query .= ( $_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4) ? "" : "AND user_id._adm " ;
		if ($_SESSION['CnxGrp'] == 2) $Query .= "AND user_id._IDgrp = '1' ";		// Un prof ne peux voir que les élèves:
		$Query .= "AND user_id._visible = 'O' " ;

		switch ($sortadm) {
			case 1 : 		$Query .= "AND user_id._adm = '0' "; 															break;
			case 2 : 		$Query .= "AND user_id._adm = '1' "; 															break;
			case 3 : 		$Query .= "AND (user_id._adm & 2) AND user_id._adm != '255' "; 		break;
			case 5 : 		$Query .= "AND (user_id._adm & 4) AND user_id._adm != '255' "; 		break;
			case 9 : 		$Query .= "AND (user_id._adm & 8) AND user_id._adm != '255' "; 		break;
			case 256 : 	$Query .= "AND user_id._adm = '255' "; 														break;
			default :	break;
		}
		switch ($sort) {
			case 1 :		$Query .= "order by user_id._ID, user_id._name";							break;
			case 2 :		$Query .= "order by user_id._ID desc, user_id._name";					break;
			case 3 :		$Query .= "order by user_id._lastcnx desc, user_id._name";		break;
			case 4 :		$Query .= "order by user_id._lastcnx, user_id._name";					break;
			default :		$Query .= "order by user_id._name";														break;
		}

		$result = mysqli_query($mysql_link, $Query);
		// détermination du nombre de pages
		$page   = ( $result ) ? mysqli_affected_rows($mysql_link) : 0 ;
		$show   = 1;
		?>




	<div class="card shadow mb-4">
	  <div class="card-header py-3">
	    <h6 class="m-0 font-weight-bold text-primary">Résultats: <?php echo $page; ?></h6>
	  </div>
	  <div class="card-body" style="<?php if ($ModeList == 'trombi') echo 'text-align: center;'; ?>">


      <?php


      if ( $result AND $page ) {

        $page  = ( $page % $MAXPAGE )
          ? (int) ($page / $MAXPAGE) + 1
          : (int) ($page / $MAXPAGE) ;

        $show  = ( $page % $MAXSHOW )
          ? (int) ($page / $MAXSHOW) + 1
          : (int) ($page / $MAXSHOW) ;

        // initialisation
        $i     = 1;
        $first = 1 + (($skpage - 1) * $MAXPAGE);

        if ($skpage == "all") $first = 0;
        if ($skpage == "all") $MAXPAGE = 1000000000000;

        // se positionne sur la page ad hoc
        mysqli_data_seek($result, $first - 1);
        // $row   = remove_magic_quotes(mysqli_fetch_row($result));

        while ($row = mysqli_fetch_row($result) AND $i <= $MAXPAGE) {

          // on récupère l'image de profil de l'utilisateur
          $photo = 'ged_thumbnail.php?action=userImage&fileID='.base64_encode($row[0]);

          // suppression
          if ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 AND $_SESSION["CnxID"] != $row[0]) $delete_link = myurlencode("index.php?item=$item&submit=delete&ID=$row[0]&authuser=$authuser&IDcentre=$IDcentre&IDsel=$IDsel&IDalpha=$IDalpha&sort=$sort");
          else $delete_link = '';

          // envoie d'un email
          $mailto = ( getAccess() == 2 AND $row[11] != "" )
            ? "<a href=\"mailto:$row[11]\" class=\"noprint\"><i class=\"fa fa-envelope\" title=\"".$msg->read($USER_SENDEMAIL)."\"></i></a>"
            : "" ;



          if ($ModeList != "trombi") {
            echo '<div class="row" id="user_row_'.$row[0].'">';
              echo '<div class="col-2" style="text-align: center;">';
                echo '<img alt="'.getUserNameByID($row[0]).'" src="'.$photo.'" class="rounded-circle img-responsive mt-2" width="128" height="128">';
                echo '<div class="mt-2">';
                  echo getUserNameByID($row[0]);
                echo '</div>';
              echo '</div>';
              echo '<div class="col-8">';

                echo '<table>';
                  echo '<tr>';
                    echo '<th>'.$msg->read($USER_GROUP).'</th>';
                    echo '<td>'.$msg->getTrad($user_group[$row[6]]).'</td>';
                  echo '</tr>';

                  // Nom + Prénom
                  echo '<tr>';
                    echo '<th>'.$msg->read($USER_NAME).'</th>';
                    echo '<td>'.getUserNameByID($row[0]).' '.$mailto.'</td>';
                  echo '</tr>';

                  // Titre de l'utilisateur
                  if (isset($row[2]) && $row[2] != '') {
                    echo '<tr>';
                      echo '<th>'.$msg->read($USER_TITLE).'</th>';
                      echo '<td>'.$row[2].'</td>';
                    echo '</tr>';
                  }

                  // Classe/promotion de l'utilisateur
                  if ($row[6] == 1) {
                    echo '<tr>';
                      echo '<th>'.$msg->read($USER_CLASS).'</th>';
                      echo '<td>'.getCodeUserID($row[0]).'</td>';
                    echo '</tr>';
                  }

                  // Téléphone
                  if (isset($row[13]) && $row[13] != '') {
                    echo '<tr>';
                      echo '<th>'.$msg->read($USER_TEL).'</th>';
                      echo '<td>'.tel($row[13]).'</td>';
                    echo '</tr>';
                  }

                  // Téléphone mobile
                  if (isset($row[14]) && $row[14] != '') {
                    echo '<tr>';
                      echo '<th>'.$msg->read($USER_MOBILE).'</th>';
                      echo '<td>'.tel($row[14]).'</td>';
                    echo '</tr>';
                  }
                echo '</table>';
              echo '</div>';

              echo '<div class="col-2">';
                echo '<div class="form-group">';
                  echo '<label for="accountTypeSelect">Type de compte :</label>';
                  if ($row[6] != 4)
                  {
                    if ($row[0] == $_SESSION['CnxID'] || $row[5] == -1) $select_state = 'disabled';
                    else $select_state = '';
                    if ((($row[5] == '0' || $row[5] == '-1') && $row[6] == 1) && getParam('lockNewAccountValidationAdmin')) $select_state = 'disabled';

                    echo '<select class="form-control" id="accountTypeSelect" '.$select_state.' name="chk_'.$row[0].'"  onchange="$(\'#formulaire\').submit();">';

                      $selected_minus_3 = "";
                      $selected_minus_2 = "";
                      $selected_0 = "";
                      $selected_1 = "";
                      switch ($row[5]) {
                        case '-3':		$selected_minus_3 = "selected";		break;
                        case '-2':		$selected_minus_2 = "selected";		break;
                        case '0':			$selected_0 = "selected";					break;
                        case '1':			$selected_1 = "selected";					break;
                        default:			$selected_minus_3 = "selected";		break;
                      }

                      if ($row[5] == -1) $selected_minus_1 = $selected_0 = $selected_1 = $selected_minus_2 = $selected_minus_3 = "disabled";


                      switch ($row[6]) {
                        // Si étudiant
                        case '1':
                          if ($row[5] == -1) echo "<option ".$selected_minus_1." value=\"-1\" selected disabled>En attente validation mail</option>";
                            echo "<option ".$selected_0." value=\"0\">En attente (admin)</option>";
                            echo "<option ".$selected_1." value=\"1\">Courant</option>";
                            echo "<option ".$selected_minus_2." value=\"-2\">Exclus</option>";
                            echo "<option ".$selected_minus_3." value=\"-3\">Anciens étudiants</option>";
                          break;
                        // Si prof
                        case '2':
                          if ($row[5] == -1) echo "<option ".$selected_minus_1." value=\"-1\" selected disabled>En attente validation mail</option>";
                            echo "<option ".$selected_0." value=\"0\">En attente (admin)</option>";
                            echo "<option ".$selected_1." value=\"1\">Courant</option>";
                            echo "<option ".$selected_minus_2." value=\"-2\">En veille</option>";
                          break;
                        default:
                          // code...
                          break;
                      }

                    echo '</select>';
                  }
                  else
                  {
                    if ($_SESSION["CnxAdm"] == 255 && $row[0] != $_SESSION['CnxID'])
                    {
                      if ($row[0] == $_SESSION['CnxID']) $select_state = "disabled";
                      else $select_state = "";
                      echo '<select class="form-control" id="accountTypeSelect" '.$select_state.' name="chk_'.$row[0].'" onchange="$(\'#formulaire\').submit();">';
                        if ($row[5] == 255) $super_adm = "selected";
                        else $super_adm = "";
                        if ($row[5] == 255) $adm_sel = "";
                        else $adm_sel = "selected";

                        echo "<option ".$adm_sel." value=\"1\">Administrateur</option>";
                        echo "<option ".$super_adm." value=\"255\">Super-administrateur</option>";
                      echo "</select>";
                    }
                    else
                    {
                      echo '<select class="form-control" id="accountTypeSelect" disabled>';
                        if ($row[5] == 255) echo '<option>Super-administrateur</option>';
                        else echo '<option>Administrateur</option>';
                      echo '</select>';
                    }
                  }

                echo '</div>';

                // Actions sur le compte
                echo '<div class="btn-group" role="group" aria-label="Actions sur le compte">';
                  echo '<a href="index.php?item=1&cmde=account&show=0&ID='.$row[0].'&Litem=1" class="btn btn-primary" title="Voir le compte"><i class="fas fa-eye"></i></a>';
                  echo '<a href="index.php?item=38&cmde=account&ID='.$row[0].'&Litem=1" class="btn btn-warning" title="Modifier le compte"><i class="fas fa-pencil-alt"></i></a>';
                  echo '<a href="'.$delete_link.'" onclick="return confirmDeletion(\'deleteAccountButton_'.$row[0].'\');" id="deleteAccountButton_'.$row[0].'" class="btn btn-danger" title="Supprimer le compte"><i class="fas fa-trash"></i></a>';
                echo '</div>';

                echo '<input type="hidden" name="my_id[]" value="'.$row[0].'" />';

              echo '</div>';
            echo '</div>';
            echo '<hr id="hr_user_id_'.$row[0].'">';
          }


          else {
            echo '<div class="" style="width: calc(100% / 6); margin: calc(((100% / 6) / 6) / 2); padding: 5px; display: inline-block; border-radius: 5px; border: 0px solid #e9ecef; text-align: center;">';
              echo '<a href="index.php?item=1&cmde=account&show=0&ID='.$row[0].'&Litem=1" class="link-unstyled">';
                echo '<img src="'.$photo.'" style="width: 100%; border-radius: 100%;">';
                echo '<div class="mt-2">';
                  echo getUserNameByID($row[0]);
                echo '</div>';
              echo '</a>';
            echo '</div>';
          }
          $i++;
        }
      }
      ?>
    </div>
	</div>
</form>






	<?php


		// bouton précédent

		// if ( $skshow == 1 || $skpg == "all")
		if (!getParam('showPrevNextButtonPagination') || $skpage == 1 || $skpage == 'all')
			$prev = "";
		else {
			$skpg = 1 + (($skshow - 2) * $MAXSHOW);
			$prev = '<li class="page-item"><a class="page-link" href="'.myurlencode("index.php?item=$item&IDsel=$IDsel&IDcentre=$IDcentre&IDalpha=$IDalpha&sort=$sort&skpage=".($skpage - 1)."&skshow=1&authuser=$authuser&ModeList=$ModeList").'"><i class="fas fa-angle-double-left"></i>&nbsp;'.$msg->read($USER_PREV).'</a></li>';
		}

		// liens directs sur n° de page
		$start = 1 + (($skshow - 1) * $MAXSHOW);
		$end   = $skshow * $MAXSHOW;


		$choix = ( $skpage == $start )
			? '<li class="page-item active"><a class="page-link" href="#">'.$start.'</a></li>'
			: '<li class="page-item"><a class="page-link" href="'.myurlencode("index.php?item=$item&IDsel=$IDsel&IDcentre=$IDcentre&IDalpha=$IDalpha&sort=$sort&skpage=$start&skshow=$skshow&authuser=$authuser&ModeList=$ModeList").'">'.$start.'</a></li>';


		for ($j = $start + 1; $j <= $end AND $j <= $page; $j++)
			if ( $skpage == $j ) $choix .= '<li class="page-item active"><a class="page-link" href="#">'.$j.'</a></li>';
			else $choix .= '<li class="page-item"><a class="page-link" href="'.myurlencode("index.php?item=$item&IDsel=$IDsel&IDcentre=$IDcentre&IDalpha=$IDalpha&sort=$sort&skpage=$j&skshow=$skshow&authuser=$authuser&ModeList=$ModeList").'">'.$j.'</a></li>';

		// bouton suivant
		// $next  = ( $skshow == $show || $skpg == "all")
		$next  = (!getParam('showPrevNextButtonPagination') || $skpage == ($end - 1) || $skpage == 'all')
			? ""
			: '<li class="page-item"><a class="page-link" href="'.myurlencode("index.php?item=$item&IDsel=$IDsel&IDcentre=$IDcentre&IDalpha=$IDalpha&sort=$sort&skpage=".($skpage + 1)."&skshow=1&authuser=$authuser&ModeList=$ModeList").'">'.$msg->read($USER_NEXT).'&nbsp;<i class="fas fa-angle-double-right"></i></a></li>';


	?>

		<hr style="width: 80%;" />

		<nav aria-label="Pagination">
			<ul class="pagination justify-content-center">
				<?php echo $prev.$choix.$next; ?>
				<li class="page-item <?php if ($skpage == 'all') echo 'active'; ?>"><a class="page-link" href="<?php echo myurlencode("index.php?item=$item&IDsel=$IDsel&IDcentre=$IDcentre&IDalpha=$IDalpha&sort=$sort&skpage=all&skshow=$skshow&authuser=$authuser&ModeList=$ModeList"); ?>">Tout afficher</a></li>
			</ul>
		</nav>




<script>
	// Confirmation de la suppression d'un utilisateur
	function confirmDeletion(buttonID) {
		event.preventDefault();
		Swal.fire({
		  title: 'Voulez-vous vraiment supprimer cet utilisateur ?',
		  showDenyButton: true,
			icon: 'question',
		  confirmButtonText: `Oui, supprimer`,
		  denyButtonText: `Non, le garder`,
			customClass: {
				confirmButton: 'btn btn-danger mr-3',
				denyButton: 'btn btn-success'
			},
	  	buttonsStyling: false

		}).then((result) => {
		  /* Read more about isConfirmed, isDenied below */
		  if (result.isConfirmed) {
				var link = $('#' + buttonID).attr('href');
				window.location.href = link;
		  } else if (result.isDenied) {
		    Swal.fire('L\'utilisateur n\'a pas été supprimé', '', 'info');
		  }
		})
	}
</script>
