<?php
session_start();
require_once "page_session.php";
include_once("php/dbconfig.php");
include_once("php/functions.php");

$soundonly = (isset($_GET["soundonly"]) && $_GET["soundonly"] == "true") ? true : false;
$link = $_GET["link"];
$visioid = $_GET["visioid"];

if($_SESSION["CnxID"]) {
	$query  = "select _ID, _name, _fname, _email ";
	$query .= "from user_id ";
	$query .= "WHERE _ID = ".$_SESSION["CnxID"]." ";
	$query .= "LIMIT 1 ";

	$result = mysqli_query($mysql_link, $query);
	$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;
	
	$link = $link."-".md5($_SERVER['HTTP_HOST'].$visioid);

	if($link != "" && $visioid != "") {
		// if($link == $visio->link) { // Secure test
			?>
			<!DOCTYPE html>
			<html lang="fr" style="height: 100%">
				<head>
					<meta charset="utf-8">
					<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
					<title>Visio</title>
					<meta name="description" content="">
					<meta name="viewport" content="width=device-width">
					<script src="js/jQuery_3.5.1.js"></script>
					<style>
					body {
						background-color: #262f3d;
					}
					
					.content {
						padding: 0 !important;
					}
					
					.content .h3, .footer {
						display: none;
					}
					</style>
			  	</head>
				<body style="margin: 0;">
					<div id="visioplace" height="600"></div>

					<script src='https://meet.jit.si/external_api.js'></script>
					<script>

						const domain = 'meet.jit.si';
						const options = {
							roomName: '<?php echo $link; ?>',
							width: "100%",
							height: "100%",
							<?php if($soundonly) : ?>
							configOverwrite: {
								startAudioOnly: true
							},
							<?php endif; ?>
							parentNode: document.querySelector('#visioplace'),
							userInfo: {
								email: '<?php echo $row[3]; ?>',
								displayName: '<?php echo $row[2]." ".$row[1]; ?>'
							}
						};
						const api = new JitsiMeetExternalAPI(domain, options);

						windowAdjust = 4;

						// Initial height
						var screenHeight = $("html").height() - windowAdjust;
						$("#visioplace").height(screenHeight);

						// On window resize
						$(window).resize(function() {
							$("#visioplace").height($("html").height() - windowAdjust);
						});

						// Close window on Hangup
						api.on('readyToClose', () => {
							window.close();
						});
					</script>
				</body>
			</html>
			<?php
		// }
	}
}
include 'footer.php';
?>
