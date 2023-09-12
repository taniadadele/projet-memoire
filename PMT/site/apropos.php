<?php
require_once "page_session.php";
include_once("php/dbconfig.php");
include_once("php/functions.php");
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $_SESSION["ROOTDIR"]; ?>/css/themes/<?php echo $_SESSION["CfgTheme"]; ?>/styles/verdana.css" />
<link href="<?php echo $_SESSION["ROOTDIR"]; ?>/css/themes/<?php echo $_SESSION["CfgTheme"]; ?>/styles/bootstrap.css" rel="stylesheet" />

<br />
<center>
<a href="http://www.promethee-solutions.fr/ent-libre" target="_blank" class="btn" style="width: 330px; padding: 0px">
	<table class="width100">
		<tr>
			<td style="width: 40px">
				<img src="images/logo-pmt-solutions.png" style="border: 0" alt="promethee-solutions" />
			</td>
			<td style="color: black; font-size: 12px; vertical-align: middle">
				Support - Hébergement / Prométhée-Solutions
			</td>
		</tr>
	</table>
</a>
<br /><br />

<?php
print("
<script type=\"text/javascript\" src=\"".$_SESSION["ROOTDIR"]."/script/popup.js\"></script>


<p style=\"margin-top:0px; margin-bottom:10px;\">
	<img src=\"".$_SESSION["ROOTDIR"]."/images/gplv3.png\" title=\"GPL3\" alt=\"GPL3\" width=\"80\" /><br />
	<a href=\"http://validator.w3.org/check?uri=http%3A%2F%2Fwww.promethee-solutions.fr%2Ftest%2F\">
		<img src=\"".$_SESSION["ROOTDIR"]."/images/w3c.jpg\" title=\"W3C\" alt=\"W3c\" width=\"80\" style=\"margin-top: 4px\" />
	</a><br />
	<a href=\"http://www.mozilla-europe.org/".$_SESSION["lang"]."/firefox/\">
		<img src=\"".$_SESSION["ROOTDIR"]."/images/firefox.png\" title=\"get Firefox\" alt=\"get Firefox\" /></a><br />

	<a href=\"#\" title=\"Prométhée\" onclick=\"popWin('".$_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/license.txt', '580', '600'); return false;\">
		<img src=\"".$_SESSION["ROOTDIR"]."/images/promethee.png\" title=\"get Prométhée\" alt=\"get Prométhée\" />
	</a><br/>
	<span class=\"x-small\">".$message->read($PAGE_VERSION, $VERSION)."</span>
</p>

</center>");
?>
