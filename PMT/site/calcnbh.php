<?php
header('Content-type: text/html; charset=UTF-8');
session_start();
require_once "page_session.php";
include_once("php/dbconfig.php");
include_once("php/functions.php");

if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
{
	require "msg/user.php";
	require_once "include/TMessage.php";

	$nbh    = trim(@$_GET["nbh"]);
	$nbm    = trim(@$_GET["nbm"]);

	$ID      = @$_POST["ID"]
		? (int) $_POST["ID"]
		: (int) @$_GET["ID"] ;
	$nameforfait 	= trim(@$_GET["nameforfait"]);
	$newnbh = trim(@$_GET["newnbh"]);
	$newnbm = trim(@$_GET["newnbm"]);
	$oldnbh = trim(@$_GET["oldnbh"]);
	$IDforfait = trim(@$_GET["IDforfait"]);
	$mail = trim(@$_GET["mail"]);
	$newmail = trim(@$_GET["newmail"]);
	$modif_email = trim(@$_POST["modif_email"]);
	$modif_pwd_old = trim(@$_POST["modif_pwd_old"]);
	$modif_pwd_new1 = trim(@$_POST["modif_pwd_new1"]);
	$modif_pwd_new2 = trim(@$_POST["modif_pwd_new2"]);

	$addr_modif_1 		= trim(@$_POST["addr_modif_1"]);
	$addr_modif_city 	= trim(@$_POST["addr_modif_city"]);
	$addr_modif_cp 		= trim(@$_POST["addr_modif_cp"]);

	$mobile_phone 		= trim($_POST['phone_modif']);

	$born_date 				= substr($_POST['born_modif'], 3, 2)."/".substr($_POST['born_modif'], 0, 2)."/".substr($_POST['born_modif'], 6, 4);
	$born_date				= trim(date('Y-m-d', strtotime($born_date)));

	if ($newnbh == "") $newnbh = 0;
	if ($newnbm == "") $newnbm = 0;

	if ($nbh != "")
	{
		?>
		<div id="div<?php echo "$nbh$IDforfait"; ?>">
			<input type="text" id="newnbh" value="<?php echo $nbh; ?>" style="width: 30px; margin-top: 10px; margin-bottom: 10px;">
			<strong> h </strong>
			<input type="text" id="newnbm" value="<?php echo $nbm; ?>" style="width: 30px; margin-top: 10px; margin-bottom: 10px;">
			<button id="modifhr_n">ok</a></button>

			<script>
			$("#modifhr_n").click(function()
			{
				var newnbh = document.getElementById('newnbh').value;
				var newnbm = document.getElementById('newnbm').value;
				jQuery.ajax({
					url: 'calcnbh.php?newnbh='+newnbh+'&newnbm='+newnbm+'&ID=<?php echo $ID; ?>&IDforfait=<?php echo $IDforfait; ?>',
					success  : function(data) { jQuery('#div<?php echo "$nbh$IDforfait"; ?>').html(data);}
					});
				return false;
			});
			</script>
		</div>
		<?php
	}


	if (($newnbh != "") && ($newnbm != "") && ($_SESSION["CnxAdm"] == 255)){

		$query  = "select _IDforfait from user_forfait ";
		$query .= "where _IDforfait = $IDforfait and _IDeleve = $ID ";
		$result = mysqli_query($mysql_link, $query);
		$num_rows = mysqli_num_rows($result);

		$time_h = ($newnbh * 60 * 60);
		$time_m = ($newnbm * 60);
		$time_hm = $time_h + $time_m;

		if ( $num_rows > 0)
		{
			$query  = "UPDATE user_forfait SET _solde = '$time_hm' WHERE _IDeleve = $ID AND _IDforfait = $IDforfait LIMIT 1;";
		} else
		{
			$query  = "insert into user_forfait values('$ID', '$IDforfait', '$time_hm')";
		}
		mysqli_query($mysql_link, $query);

		// Renseigne le log
		$querylog  = "INSERT INTO forfait_log VALUES ('', '$ID', '$IDforfait', '0', '$time_hm', '$time_hm', '".date("Y-m-d", time())."')";
		$resultlog = mysqli_query($mysql_link, $querylog);

		if ($newnbh == 0) $newnbh = "0";
		echo $newnbh."h".$newnbm;
	}



	if (($mail != "") && ($newmail == "")) {

	echo "<div id=\"formnewmail\">
	<input type=\"text\" id=\"newmail2\" value=\"$mail\" style=\"width: 100px; margin-top: 10px; margin-bottom: 10px;\">
	<button id=\"modifmail\">ok</a></button>

								<script>

							var element = document.getElementById('modifmail');
							element.onclick = function() {
								var newmail3 = document.getElementById('newmail2').value;

								jQuery.ajax({
									url: 'calcnbh.php?newmail='+newmail3+'&ID=$ID',
									success  : function(data) { jQuery('#formnewmail').html(data);}
									});
								return false;
							};

							</script></div>";
	}


	// Modif email
	if (($modif_email != "") && ($_SESSION["CnxID"] == $ID)) {
		$query  = "UPDATE user_id SET _email = '$modif_email' WHERE _ID = $ID";
		mysqli_query($mysql_link, $query);
	}






	// Modif adresse
	$query = "UPDATE rubrique_data SET _valeur = '".$addr_modif_1."' WHERE _IDdata = '".$ID."' AND _IDrubrique = '4' ";
	mysqli_query($mysql_link, $query);
	$query = "UPDATE rubrique_data SET _valeur = '".$addr_modif_cp."' WHERE _IDdata = '".$ID."' AND _IDrubrique = '5' ";
	mysqli_query($mysql_link, $query);
	$query = "UPDATE rubrique_data SET _valeur = '".$addr_modif_city."' WHERE _IDdata = '".$ID."' AND _IDrubrique = '6' ";
	mysqli_query($mysql_link, $query);

	$query = "UPDATE user_id SET _mobile = '".$mobile_phone."', _born = '".$born_date."' WHERE _ID = '".$ID."' ";
	mysqli_query($mysql_link, $query);

	// Modif password
	if (($modif_pwd_old != "") && ($modif_pwd_new1 != "") && ($modif_pwd_new2 != "") && ($_SESSION["CnxID"] == $ID)) {
		if($modif_pwd_new1 == $modif_pwd_new2) // v�rif mdp identique
		{
			// V�rif concordance mot de passe actuel
			$query  = "select _passwd ";
			$query .= "from user_id ";
			$query .= "where _ID = '$ID' AND _passwd = '".md5($modif_pwd_old)."' ";
			$query .= "limit 1";
			$result = mysqli_query($mysql_link, $query);

			if(mysqli_affected_rows($mysql_link) == 1)
			{
				$query  = "UPDATE user_id SET _passwd = '".md5($modif_pwd_new1)."' WHERE _ID = $ID";
				mysqli_query($mysql_link, $query);
			}
		}
	}
}
?>
