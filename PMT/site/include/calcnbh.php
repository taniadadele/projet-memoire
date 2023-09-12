<?php
header('Content-type: text/html; charset=UTF-8');
session_start();
require_once "page_session.php";
include_once("php/dbconfig.php");
include_once("php/functions.php");
require "msg/user.php";
require_once "include/TMessage.php";


$nbh    = trim(@$_GET["nbh"]);
$type   = trim(@$_GET["type"]);
$ID 	= trim(@$_GET["ID"]);
$nameforfait 	= trim(@$_GET["nameforfait"]);
$newnbh = trim(@$_GET["newnbh"]);
$oldnbh = trim(@$_GET["oldnbh"]);

if ($nbh != "") {

echo "<div id=\"div$nbh\">
<input type=\"text\" id=\"newnbh\" value=\"$nbh\" style=\"width: 30px;\">
<button id=\"modifhr_n\">ok</a></button>

							<script>							
						var element = document.getElementById('modifhr_n');
						element.onclick = function() {
							var newnbh = document.getElementById('newnbh').value;
							alert (newnbh);
							return false;
						};
						
						element.onclick = function() {
							var newnbh = document.getElementById('newnbh').value;
							jQuery.ajax({
								url: 'include/calcnbh.php?newnbh='+newnbh+'&type=$type&ID=$ID&nameforfait=$nameforfait&oldnbh=$nbh',
								success  : function(data) { jQuery('#div$nbh').html(data);}
								});
							return false;
						};
						
						</script></div>";						
}	else {
	

	if ($type="P") {
		$query  = "UPDATE user_forfait SET _soldePart = '$newnbh' WHERE _IDeleve = 2624 AND _IDforfait = 'FL1' AND _soldePart = $oldnbh AND _soldePart = 0 LIMIT 1;";
	} 
	if ($type="G") {
		$query  = "UPDATE user_forfait SET _soldeGrp = '$newnbh' WHERE _IDeleve = 2624 AND _IDforfait = 'FL1' AND _soldeGrp = $oldnbh AND _soldePart = 0 LIMIT 1;";
	}

	mysqli_query($mysql_link, $query);
					
					
					
					
	echo $query;
	
}					
?>















