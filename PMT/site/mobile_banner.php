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
 *		module   : mobile_banner.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    : 29/07/29 - Thomas Dazy - Remise à jour de la version mobile
 */


session_start();

// Vérification de la connexion
if (isset($currentPage) && $currentPage != "index")
{
	if ( @$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]) )
	{
		// Rien ok
	}
	else
	{
		header("Location: index_mobile.php?item=-1");
	}

}


// header('Content-Type: text/html;charset=ISO-8859-1');
require_once "page_session.php";


if ($_SESSION["CnxID"] != "" && $_SESSION["CnxID"] != 0 && isset($_SESSION['justConnected']) && $_SESSION['justConnected'] == true)
{
	unset($_SESSION['justConnected']);
	// Écriture dans les logs
	mysqli_query($mysql_link, "insert into stat_log values('".date("Y-m-d H:i:s")."', '".$_SESSION["CnxID"]."', '', '".@$_SERVER["REMOTE_ADDR"]."', 'C')");

	// Gestion des cookies
	$currentDay = date('d');
	$currentMonth = date('m');
	$currentYear = date('Y');
	$timeOutCookie = strtotime($currentYear."-".$currentMonth."-".$currentDay." 03:00:00 + 1 day");
	// identifiant (réel, pas celui utilisé lors de la co) + année + mois + jour + H + I + s en crypt
	$key = md5(rand().$ident.date('YmdHis'));
	setcookie('token_key', $key, $timeOutCookie, '/');

	$_COOKIE['token_key'] = $key;

	// On stoque le cookie dans la BDD pour pouvoir faire la correspondance après
	$query = "SELECT _userID FROM user_cookie WHERE _userID = '".$_SESSION['CnxID']."' ";
	$result = mysqli_query($mysql_link, $query);
	if (mysqli_num_rows($result) == 1)
	{
		// Requête d'UPDATE
		$query = "UPDATE user_cookie SET _token = '".$key."', _timeOut = '".$timeOutCookie."' WHERE _userID = '".$_SESSION['CnxID']."' ";
	}
	else
	{
		// Requête d'INSERT
		$query = "INSERT INTO user_cookie SET _token = '".$key."', _timeOut = '".$timeOutCookie."', _userID = '".$_SESSION['CnxID']."' ";
	}
	mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error());
}


require "msg/edt.php";
require_once "include/TMessage.php";
require_once "include/gallery.php";

// initialisation
$my_logo = $height = "";

// taille du logo par défaut
imageSize("images/download/".$_SESSION["CfgIdent"]."/logo01.jpg", $srcWidth, $srcHeight);

// images défilantes ou logo fixe
$result  = mysqli_query($mysql_link, "select _IDlogo from config_logo where _visible = 'O'");

// if ( $_SESSION["CfgLogo1"] == "O" ) {
	$height = "height:".$srcHeight."px;";

	if ( mysqli_num_rows($result) )	{
		$my_logo = "";
		}
	else {
		$file = $_SESSION["ROOTDIR"]."/download/logos/".$_SESSION["CfgIdent"]."/logo_large_dark.png";

		if ( file_exists($file) )
			$my_logo = "
				<a href=\"mobile.php\">
					<img src=\"".$_SESSION["ROOTDIR"]."/download/logos/".$_SESSION["CfgIdent"]."/logo_large_dark.png\" title=\"\" alt=\"$file\" style=\"/* margin-bottom: 5px; */ max-height: 3em;\"  />
				</a>";
		// }
	}
?>

<html class="ui-mobile">
<head>
	<title><?php echo $_SESSION['CfgTitle']; ?></title>
	<!-- <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0; maximum-scale=1, user-scalable=no"> -->
	<meta name='viewport' content='initial-scale=1, viewport-fit=cover'>
	<meta name="apple-mobile-web-app-capable" content="yes">

	<link rel="manifest" href="/mobile_manifest.json">

	<link rel="apple-touch-icon" href="download/logos/<?php echo $_SESSION["CfgIdent"]; ?>/mobile/180x180.jpg" />
  <link rel="apple-touch-icon" sizes="180x180" href="download/logos/<?php echo $_SESSION["CfgIdent"]; ?>/mobile/180x180.jpg" />
  <link rel="apple-touch-icon" sizes="76x76" href="download/logos/<?php echo $_SESSION["CfgIdent"]; ?>/mobile/76x76.jpg" />
  <link rel="apple-touch-icon" sizes="152x152" href="download/logos/<?php echo $_SESSION["CfgIdent"]; ?>/mobile/152x152.jpg" />
  <link rel="apple-touch-icon" sizes="58x58" href="download/logos/<?php echo $_SESSION["CfgIdent"]; ?>/mobile/58x58.jpg" />

	<!-- Font icon -->
	<link href="<?php echo $_SESSION["ROOTDIR"]; ?>/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


</head>
<body>
<script>
	// On évite de rediriger vers safari en mode webapp:
	(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(chref=d.href).replace(e.href,"").indexOf("#")&&(!/^[a-z\+\.\-]+:/i.test(chref)||chref.indexOf(e.protocol+"//"+e.host)===0)&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone");
</script>
<div data-role="page">
<div style="background-color: white; display: none;">
<?php
if(isset($current_page) && $current_page == "mobile")
{
	echo $my_logo;
	?>
	<div style="float: right; display: inline-block; height: 50px; margin-top: 10px">
		<a href="index.php?item=-1" data-role="button" data-icon="delete" data-iconpos="notext" data-inline="true" data-theme="b">Delete</a>
	</div>
	<?php
}
?>
</div>


<style>
#mainContent {
	padding: 0 env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
}
#navbarNavAltMarkup {
	padding: 0 env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
}
</style>

<script type="text/javascript">
$(document).ready(function(){
        // iOS web app full screen hacks.
        if(window.navigator.standalone == true) {
                // make all link remain in web app mode.
                $('a').click(function() {
                        window.location = $(this).attr('href');
            return false;
                });
        }
});
</script>
