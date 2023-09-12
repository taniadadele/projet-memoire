<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2013 by IP-Solutions(contact@ip-solutions.fr)

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
 *		module   : index_mobile.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 */

$currentPage = "index";
include("mobile_banner.php");
require("include/fonction/auth_tools.php");

if ($_GET['page'] == "rstpswd")
{
	$location = "mobile_user_lost.php";

	$getWith = "";
	foreach ($_GET as $key => $value) {
		if ($getWith != "") $getWith .= "&";
		$getWith .= $key."=".$value;
	}
	if ($getWith != "") $location .= "?".$getWith;
	// echo $getWith;


	echo "<meta http-equiv=\"refresh\" content=\"0;URL=".$location."\">";

}



if ($_GET['item'] == "-1" && $_SESSION['CnxID'])
{
	// déconnexion CAS :
	$_SESSION["logout"] = true;

	// log de déconnexion
	$_SESSION["sessID"] = "";

	$lastaction = date("Y-m-d H:i:s");

	if ( !mysqli_query($mysql_link, "insert into stat_log values('$lastaction', '".$_SESSION["CnxID"]."', '', '".@$_SERVER["REMOTE_ADDR"]."', 'D')") )
		sql_error($mysql_link);

	// on efface la session (sécurisation / browser)
	// $_SESSION["sessID"]     = eraseSessionID($_SESSION["sessID"]);

	$_SESSION["CnxID"]      = "";		// ID utilisateur
	$_SESSION["CnxIP"]      = "";		// @IP de l'utilisateur
	$_SESSION["CnxAdm"]     = "";		// Droits de connexion de l'utilisateur
	$_SESSION["CnxName"]    = "";		// Nom de connexion de l'utilisateur
	$_SESSION["CnxGrp"]     = "";		// Groupe de connexion de l'utilisateur
	$_SESSION["CnxSex"]     = "";		// Sexe de l'utilisateur (A pour une connexion Anonyme)
	$_SESSION["CnxSign"]    = "";		// Signature des mails
	$_SESSION["CnxPers"]    = "";		// connexion persistante pour l'utilisateur
	$_SESSION["CnxCentre"]  = "";		// centre de formation
	$_SESSION["CnxPasswd"]  = "";		// mot de passe (vérification si vide)
	$_SESSION["CampusName"] = "";		// le e-campus
	$_SESSION["CnxClass"]   = "";		// classe de l'élève
	$_SESSION["egroup"]     = "";


}

if ( is_file("msg/user.php") )
	require "msg/user.php";

$msg  = new TMessage("msg/".$_SESSION["lang"]."/user.php", $_SESSION["ROOTDIR"]);
$msg->msg_search  = $keywords_search;
$msg->msg_replace = $keywords_replace;


$erreur_mobile = "";

if($_POST)
{
	$id       = ( @$_POST["id"] )		// ID utilisateur
		? $_POST["id"]
		: @$id ;

	$pwd      = ( @$_POST["pwd"] )	// mot de passe
		? $_POST["pwd"]
		: @$pwd ;

	$id       = addslashes(trim($id));
	$pwd      = addslashes(trim($pwd));

	// pour éviter les injections SQL
	$pwd = md5(str_replace(" ", "-", trim($pwd)));

	// vérification de l'identité
	$query  = "select _ID, _date, _cnx, _persistent, _sexe, _adm, _IDcentre, _name, _IDgrp, _passwd, _signature, _delay, _fname, _IDclass ";
	$query .= "from user_id ";
	// - si un mode d'authentification externe est trouvé
	// - et l'utilisateur authentifié
	if ( isset ($bAuthMode) )
		$query .= ( $bAuthentifie == false )
			// on supprime $id ainsi on lui recharge le formulaire de login
			? "where _ident = '' "
			// le mot de passe n'est plus indispensable
			: "where (_ident = '$id' OR _email = '$id') " ;
	else
		$query .= "where (_ident = '$id' OR _email = '$id') AND _passwd = '$pwd' ";
	$query .= "AND _adm > 0 ";
	$query .= "limit 1";

	if ( $DEBUG )
		print($query);

	$result = mysqli_query($mysql_link, $query);

	// a-t-on trouvé une valeur ?
	if ( mysqli_affected_rows($mysql_link) == 1 ) {
		$row    = remove_magic_quotes(mysqli_fetch_row($result));

		// on récupère les informations sur l'utilisateur...
		// attention aux comptes non validés
		if ($row[5]) {
			$_SESSION["CnxID"]     = $row[0];						// ID de l'utilisateur
			$_SESSION["CnxPers"]   = $row[3];						// connexion persistante pour l'utilisateur
			$_SESSION["CnxSex"]    = $row[4];						// Sexe de l'utilisateur (A pour une connexion Anonyme)
			$_SESSION["CnxAdm"]    = $row[5];						// Droits de connexion de l'utilisateur
			$_SESSION["CnxCentre"] = $row[6];						// centre de formation
			$_SESSION["CnxName"]   = formatUserName($row[7], $row[12]);		// Nom de connexion de l'utilisateur
			$_SESSION["CnxGrp"]    = $row[8];						// Groupe de connexion de l'utilisateur
			$_SESSION["CnxPasswd"] = $row[9];						// mot de passe (vérification si vide)
			$_SESSION["CnxSign"]   = $row[10];						// signature forum, ...
			$_SESSION["CnxClass"]  = $row[13];						// classe de l'élève
		}

		// ... puis on met à jour la date de dernière connexion
		$date    = date("Y-m-d H:i:s");

		$query   = "update user_id ";
		$query  .= "set _lastcnx = ";
		$query  .= ( $row[2] ) ? "'$row[1]', " : "'$date', " ;
		$query  .= "_date = '$date', ";
		$query  .= "_cnx = _cnx + 1 ";
		$query  .= "where _ID = '".$_SESSION["CnxID"]."' ";
		$query  .= "limit 1";

		if ( !mysqli_query($mysql_link, $query) )
			sql_error($mysql_link);
		else {
			// vérification du mode maintenance
			if ( $MAINTENANCE AND $row[5] != 255 )
				$erreur_mobile = "<p style=\"color:#FF0000\">". $msg->read($USER_NOPERM) ."</p>";
			else {
				// recherche des droits d'accès
				$query  = "select _dstart, _dend, _hstart, _hend from user_denied ";
				$query .= "where _IDcentre = '".$_SESSION["CnxCentre"]."' AND _IDgrp = '".$_SESSION["CnxGrp"]."' ";
				$query .= "limit 1";

				@mysqli_query($mysql_link, $query);

				if ( mysqli_affected_rows($mysql_link) AND $row[5] != 255 )
					$erreur_mobile = "<p style=\"color:#FF0000;\">". $msg->read($USER_DENIED) ."</p>";
				else
					// vérification du compte
					if ( $_SESSION["CnxAdm"] ) {
						// enregistrement de l'adresse IP de connexion
						$_SESSION["CnxIP"]  = SessionIP();

						if ( !mysqli_query($mysql_link, "update user_id set _IP = '".$_SESSION["CnxIP"]."' where _ID = '".$_SESSION["CnxID"]."' limit 1") )
							sql_error($mysql_link);

						// enregistrement de la session de l'utilisateur
						$_SESSION["sessID"] = createUniqueSessID();


						// enregistrement des logs
						if ( $TIMELOG ) {
							// on efface les logs trops anciens
							$Query  = "DELETE FROM stat_log ";
							$Query .= "WHERE _date < '". date("Y-m-d H:i:s", (time() - $TIMELOG)) ."' ";

							if ( !mysqli_query($mysql_link, $Query) )
								sql_error($mysql_link);
							}

						if ( !mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '".$_SESSION["CnxID"]."', '', '".@$_SERVER["REMOTE_ADDR"]."', 'C')") )
							sql_error($mysql_link);

						// accés au menu de l'intranet
						$erreur_mobile = "<script type=\"text/javascript\"> window.location.replace('mobile.php', '_self'); </script>";
						}
					else
						$erreur_mobile = "<p style=\"color:#FF0000;\"><strong>". $msg->read($USER_ACCOUNTCLOSE) ."</strong></p>";
				}
			}
		}
	// sinon on affiche l'erreur
	else {
	if ( $HOSTING AND !$idschool )
		$erreur_mobile = "<p style=\"color:#FF0000;\"><strong>". $msg->read($USER_NOTVALIDSCHOOL) ."</strong></p>";
	else {
		$erreur_mobile = "<p style=\"color:#FF0000; text-align: center\"><strong>Erreur Login / Password</strong></p>";

		// on trace...
		mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '".$_SESSION["CnxID"]."', '$id', '".@$_SERVER["REMOTE_ADDR"]."', 'X')");
		}
	}
}

// echo "<div style=\"background-color: white\"><center>$my_logo</center></div>";

?>
<div class="mainDiv">
	<form id="formulaire" method="POST" action="index_mobile.php">


		<div class="align-center" style="width: 100%; text-align: center; margin-bottom: 15px;">
			<?php echo $my_logo ?><br />
			<?php print($msg->read($USER_LOGIN)); ?>
			<!-- <br> -->
			<?php //echo $_SESSION["CfgLogin"]; ?>
		</div>


		<?php
			if ($_GET['validpwd'] == "on")
			{
				print("<div class=\"alert alert-success\" style=\"text-align: center;\"><strong>". $msg->read($USER_PASSWORD_MODIF_VALID) ."</strong></div>");
			}

			if ($_GET['validpwd'] == "mail_send")
			{
				print("<div class=\"alert alert-success\" style=\"text-align: center;\"><strong>". $msg->read($USER_PASSWORD_MODIF_MAIL) ."</strong></div>");
			}
		?>


		<div class="form-group">
	    <input type="text" class="form-control" id="ID" name="id" aria-describedby="emailHelp" placeholder="Identifiant">
	  </div>
	  <div class="form-group">
	    <input type="password" class="form-control" id="PW" name="pwd" placeholder="Mot de passe">

	  </div>
		<div style="text-align: center; width: 100%;">
			<button type="submit" style="float: center;" class="btn btn-primary">Se connecter</button>
			<a href="mobile_user_lost.php" style="float: center;" class="btn btn-default">Mot de passe oublié</a>
		</div>

	</form>

	<div class="align-center" style="width: 100%; text-align: center; margin-top: 20px; margin-bottom: 15px;">
		Option 'création de compte' non disponible sur mobile
	</div>
</div>
<style>

body {
	background-image: url('images/background-login.jpg');
	background-repeat: no-repeat;
	background-position: center;
	background-size: cover;
}

body::before {
	content: "";
	display: block;
	position: absolute;
	z-index: -1;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	background-color: rgba(0,0,0,0.1);
}

.mainDiv {
	width: 90%;

	position: absolute;
	top: 50%;
	left: 50%;
	-ms-transform: translateX(-50%) translateY(-50%);
	-webkit-transform: translate(-50%,-50%);
	transform: translate(-50%,-50%);
	background-color: white;

	border-radius: 10px;

	padding: 33px 55px 33px 55px;
	box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
	-moz-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
	-webkit-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
	-o-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
	-ms-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
}

</style>
<?php echo $erreur_mobile; ?>

<?php include("mobile_footer.php"); ?>
