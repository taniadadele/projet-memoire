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
 *		module   : edit.db.php
 *
 *		version  : 2.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    :
 *					 		 11/09/20 - Thomas Dazy (contact@thomasdazy.fr) (IP-Solutions)
 *									 passage en PHP 7
 */
session_start();
include_once("php/dbconfig.php");
include_once("php/functions.php");
require("include/fonction/auth_tools.php");

// Si on affiche les erreurs, les JS ne fonctionnent plus
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// print_r($_POST);
function getCalendarByRange($id){
  global $mysql_link;
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "select * from `edt_data` where `_IDx` = " . $id;
    $handle = mysqli_query($mysql_link, $sql);
    //
    $row = mysqli_fetch_object($handle);
	}catch(Exception $e){
  }
  return $row;
}
if(@$_GET["id"] && $_GET["type"] == "edit"){
  $event = getCalendarByRange($_GET["id"]);
}

require_once "page_session.php";
require_once "include/fonction.php";
$startx   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
$endx     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
$startday = intval(date("N", $startx))-1;
$endday = intval(date("N", $endx))-1;

$sid  = ( @$_POST["sid"] )			// session de l'utilisateur
	? $_POST["sid"]
	: @$_GET["sid"] ;

$IDedt  = ( @$_POST["IDedt"] )			// ID du type d'edt
	? $_POST["IDedt"]
	: @$_GET["IDedt"] ;

$IDcentre  = ( @$_POST["IDcentre"] )			// ID du centre
	? $_POST["IDcentre"]
	: @$_GET["IDcentre"] ;

$IDdata  = ( @$_POST["IDdata"] )			// ID de l'emploi du temps
	? $_POST["IDdata"]
	: @$_GET["IDdata"] ;

$IDx  = ( @$_POST["IDx"] )			// ID de l'emploi du temps
	? $_POST["IDx"]
	: @$_GET["IDx"] ;
$IDxx  = ( @$_POST["IDxx"] )			// ID de l'emploi du temps
	? $_POST["IDxx"]
	: @$_GET["IDxx"] ;

$j        = @$_GET["j"];			// jour
$h        = @$_GET["h"];			// heure

$IDitem   = ( @$_POST["IDitem"] )		// ID de la salle
	? (int) $_POST["IDitem"]
	: (int) @$_GET["IDitem"] ;
$IDclass  = ( @$_POST["IDclass"] )		// ID de la classe
	? (int) $_POST["IDclass"]
	: (int) @$_GET["IDclass"] ;
$IDmat  = ( @$_POST["IDmat"] )		// ID de la matière
	? (int) $_POST["IDmat"]
	: (int) @$_GET["IDmat"] ;
$IDuser   = ( @$_POST["IDuser_0"] )		// ID de l'utilisateur
	? (int) $_POST["IDuser_0"]
	: (int) @$_GET["IDuser_0"] ;

$IDuserRmpl = ( @$_POST["IDuser_1"] )		// ID de l'utilisateur
	? (int) $_POST["IDuser_1"]
	: (int) @$_GET["IDuser_1"] ;

if ($IDuserRmpl == "") $IDuserRmpl = 0;
if ($IDuserRmpl == $IDuser) $IDuserRmpl = 0;



$IDmat    = (int) @$_POST["IDmat"];		// ID de la matière
$idweek   = (int) @$_POST["idweek"];	// type de semaine
$idgroup  = (int) @$_POST["idgroup"];	// groupe classe
$delay    = @$_POST["delay"];			// durée du cours
$ck_groupe    = @$_POST["ck_groupe"];	// Groupe

$etat   = (int) @$_POST["etat"];



$generique  = ( @$_POST["generique"] )
	? $_POST["generique"]
	: @$_GET["generique"] ;

$start  = ( @$_POST["start"] )
	? $_POST["start"]
	: @$_GET["start"] ;

$end  = ( @$_POST["end"] )
	? $_POST["end"]
	: @$_GET["end"] ;

$time  = ( @$_POST["time"] )
	? (int) $_POST["time"]
	: (int) @$_GET["time"] ;

$orgens  = ( @$_POST["orgens"] )
	? (int) $_POST["orgens"]
	: (int) @$_GET["orgens"] ;

$orgmat  = ( @$_POST["orgmat"] )
	? (int) $_POST["orgmat"]
	: (int) @$_GET["orgmat"] ;

$IDgrp  = ( @$_POST["IDgrp"] )
	? (int)$_POST["IDgrp"]
	: (int)@$_GET["IDgrp"] ;

$IDeleve  = ( @$_POST["tags-id_0"] )
	? (int) $_POST["tags-id_0"]
	: (int) @$_GET["tags-id_0"] ;

$date_finperiode  = ( @$_POST["date_finperiode"] )
	? $_POST["date_finperiode"]
	: @$_GET["date_finperiode"] ;

$nbrep  = ( @$_POST["nbrep"] )
	? (int) $_POST["nbrep"]
	: (int) @$_GET["nbrep"] ;

$quinzaine  = ( @$_POST["quinzaine"] )
	? (int) $_POST["quinzaine"]
	: (int) @$_GET["quinzaine"] ;


if (isset($_POST['workflow']) && $_POST['workflow'] == "yes") $workflow = 3;
else $workflow = 1;

if (isset($_POST['tellTeacherAndStudents']) && $_POST['tellTeacherAndStudents'] == "yes") $tellTeacherAndStudents = "yes";
else $tellTeacherAndStudents = "no";

if (isset($_POST['tellTeacher']) && $_POST['tellTeacher'] == "yes") $tellTeacher = "yes";
else $tellTeacher = "no";

if (isset($_POST['tellStudents']) && $_POST['tellStudents'] == "yes") $tellStudents = "yes";
else $tellStudents = "no";






if($IDeleve != 0)
{
	$IDgrp = 0;
}

if (isset($_POST['IDuv'])) $ID_examen = $_POST['IDuv'];
if (isset($_POST['agendaText'])) $texte     = $_POST['agendaText'];
if (isset($_POST['IDpma'])) $IDpma     = $_POST['IDpma'];


$submit   = $_POST["valid_x"];		// bouton de validation
$_SESSION["CnxCentre"] = $IDcentre;

require "page_banner.php";
require "msg/edt.php";
require_once "include/TMessage.php";

// qui suis-je ?
$query  = "select distinctrow _adm, _IDgrp from user_id, user_session ";
$query .= "where _IDsess = '$sid' " ;
$query .= "AND user_id._ID = user_session._ID ";
$query .= "order by _lastaction ";
$query .= "limit 1";

$result = mysqli_query($mysql_link, $query);
$auth   = ( $result ) ? mysqli_fetch_row($result) : 0 ;

// vérification des droits
$query   = "select _IDmod, _IDgrpwr from edt ";
$query  .= "where _IDedt = '$IDedt' " ;
$query  .= "limit 1";

$result  = mysqli_query($mysql_link, $query);
$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

if( $auth[0] == 255 OR $_SESSION["CnxGrp"] == 4 OR $_SESSION["CnxGrp"] == 2 OR $auth[0] == $row[0] OR ($row[1] & pow(2, $auth[1] - 1)) )
{

}
else
{
	exit;
}

$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/edt.php");
$msg->msg_search  = $keywords_search;
$msg->msg_replace = $keywords_replace;

if ( $IDxx ) // Si modification lors de l'affichage on charge les données du cours
{
	$query   = "select _IDmat, _IDclass, _fin, _semaine, _IDitem, _group, _ID, _attribut, _etat, _plus, _text, _ID_examen, _ID_pma, _IDrmpl from edt_data ";
	$query  .= "where _IDx = '$IDxx' " ;
	$query  .= "limit 1";


	$result  = mysqli_query($mysql_link, $query);
	$row     = ( $result ) ? mysqli_fetch_row($result) : 0 ;

	$IDmat        = (int) $row[0];
	$IDclass      = $row[1];
	$delay        = $row[2];
	$idweek       = (int) $row[3];
	$IDitem       = (int) $row[4];
	$IDgrp        = (int) $row[5];
	$IDuser       = (int) $row[6];
	$ck_groupe    = (int) $row[7];
	$etat         = (int) $row[8];

  $IDuserRmpl = (int) $row[13];

  $workflow = $etat;

	$IDeleve      = (int) $row[9];
  $texte        = $row[10];
  $ID_examen    = $row[11];
  if ($ID_examen != 0 and $ID_examen != NULL) $typeElem = "uv";
  elseif (getMatTypeByMatID($IDmat) == 3 or getMatTypeByMatID($IDmat) == 2) $typeElem = "agenda";
  else $typeElem = "";

  if (getMatTypeByMatID($IDmat) == 3) $typeAgenda = 3;
  elseif (getMatTypeByMatID($IDmat) == 2) $typeAgenda = 2;

  $IDpma        = $row[12];
}


// On récupère les plages horraires depuis les paramètres
$tab_horaire = json_decode(getparam('plageHoraire'), TRUE);


// error_reporting(E_ALL);
// ini_set("display_errors", 1);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="modalEventModifHtml">
	<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Calendar Details</title>
	<!-- <link rel="stylesheet" type="text/css" media="screen" href="css/$-ui.css" /> -->
    <!-- <link href="css/main.css" rel="stylesheet" type="text/css" /> -->
    <!-- <link href="css/dp.css" rel="stylesheet" /> -->
    <!-- <script src="script/$.js" type="text/javascript"></script> -->

    <script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- <script src="script/Plugins/wdCalendar_lang_<?php print($_SESSION["lang"]); ?>.js" type="text/javascript"></script>
    <script src="script/Plugins/$.calendar<?php echo $samedidimanche; ?>.js" type="text/javascript"></script> -->


	<script>
	var matched, browser;

	$.uaMatch = function( ua ) {
		ua = ua.toLowerCase();

		var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
			/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
			/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
			/(msie) ([\w.]+)/.exec( ua ) ||
			ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
			[];

		return {
			browser: match[ 1 ] || "",
			version: match[ 2 ] || "0"
		};
	};

	matched = $.uaMatch( navigator.userAgent );
	browser = {};

	if ( matched.browser ) {
		browser[ matched.browser ] = true;
		browser.version = matched.version;
	}

	// Chrome is Webkit, but Webkit is also Safari.
	if ( browser.chrome ) {
		browser.webkit = true;
	} else if ( browser.webkit ) {
		browser.safari = true;
	}

	$.browser = browser;
	</script>
    <script src="script/Plugins/Common.js" type="text/javascript"></script>




	<script type="text/javascript">

	//<![CDATA[

	/* The following function creates a new input field and then calls datePickerController.create();
	   to dynamically create a new datePicker widgit for it */
	// function newline() {
	// 		var total = document.getElementById("newline-wrapper").getElementsByTagName("table").length;
	// 		total++;
  //
	// 		// Clone the first div in the series
	// 		var tbl = document.getElementById("newline-wrapper").getElementsByTagName("table")[0].cloneNode(true);
  //
	// 		// DOM inject the wrapper div
	// 		document.getElementById("newline-wrapper").appendChild(tbl);
  //
	// 		var buts = tbl.getElementsByTagName("a");
	// 		if(buts.length) {
	// 				buts[0].parentNode.removeChild(buts[0]);
	// 				buts = null;
	// 		}
  //
	// 		// Reset the cloned label's "for" attributes
	// 		var labels = tbl.getElementsByTagName('label');
  //
	// 		for(var i = 0, lbl; lbl = labels[i]; i++) {
	// 				// Set the new labels "for" attribute
	// 				if(lbl["htmlFor"]) {
	// 						lbl["htmlFor"] = lbl["htmlFor"].replace(/[0-9]+/g, total);
	// 				} else if(lbl.getAttribute("for")) {
	// 						lbl.setAttribute("for", lbl.getAttribute("for").replace(/[0-9]+/, total));
	// 				}
	// 		}
  //
	// 		// Reset the input's name and id attributes
	// 		var inputs = tbl.getElementsByTagName('input');
	// 		for(var i = 0, inp; inp = inputs[i]; i++) {
	// 				// Set the new input's id and name attribute
	// 				inp.id = inp.name = inp.id.replace(/[0-9]+/g, total);
	// 				if(inp.type == "text") inp.value = "";
	// 		}
  //
	// 		// Call the create method to create and associate a new date-picker widgit with the new input
	// 		// datePickerController.create(document.getElementById("date-" + total));
  //     //
	// 		// datePickerController.create(document.getElementById("stpartdate"));
	// 		// datePickerController.create(document.getElementById("date_finperiode"));
  //     //
	// 		// var dp = datePickerController.datePickers["dp-normal-1"];
  //
	// 		// No more than 5 inputs
	// 		if(total == 50) document.getElementById("newline").style.display = "none";
  //     $('#date-' + total).daterangepicker({
  //       opens: 'left',
  //       locale: lang_datepicker,
  //       "singleDatePicker": true,
  //       "autoApply": false
  //     }).val('').on('cancel.daterangepicker', function(ev, picker) {
  //       $(this).val('');
  //     });
  //
	// 		$("#date-" + total).change(function() {
	// 			if(!$("#date-" + (total+1)).length)
	// 			{
	// 				newline();
	// 			}
	// 		});
  //
  //
  //
	// 		// Stop the event
	// 		return false;
	// }

	function createNewLineButton() {
			var nlw = document.getElementById("newline-wrapper");

			var a = document.createElement("a");
			a.href="#";
			a.id = "newline";
			a.className = "fa fa-plus-square";
			a.onclick = newline;
			nlw.parentNode.appendChild(a);

			// a.appendChild(document.createTextNode(""));
			a = null;
	}

	// datePickerController.addEvent(window, 'load', createNewLineButton);

	//]]>

        if (!DateAdd || typeof (DateDiff) != "function") {
            var DateAdd = function(interval, number, idate) {
                number = parseInt(number);
                var date;
                if (typeof (idate) == "string") {
                    date = idate.split(/\D/);
                    eval("var date = new Date(" + date.join(",") + ")");
                }
                if (typeof (idate) == "object") {
                    date = new Date(idate.toString());
                }
                switch (interval) {
                    case "y": date.setFullYear(date.getFullYear() + number); break;
                    case "m": date.setMonth(date.getMonth() + number); break;
                    case "d": date.setDate(date.getDate() + number); break;
                    case "w": date.setDate(date.getDate() + 7 * number); break;
                    case "h": date.setHours(date.getHours() + number); break;
                    case "n": date.setMinutes(date.getMinutes() + number); break;
                    case "s": date.setSeconds(date.getSeconds() + number); break;
                    case "l": date.setMilliseconds(date.getMilliseconds() + number); break;
                }
                return date;
            }
        }
        function getHM(date)
        {
             var hour =date.getHours();
             var minute= date.getMinutes();
             var ret= (hour>9?hour:"0"+hour)+":"+(minute>9?minute:"0"+minute) ;
             return ret;
        }
    </script>
    <style type="text/css">
  		.calpick {
  			width:16px;
  			height:16px;
  			border:none;
  			cursor:pointer;
  			background:url("sample-css/cal.gif") no-repeat center 2px;
  			margin-left:-22px;
  			margin-top: -12px;
  		}

  		body {
  			overflow-y: scroll;
  		}

  		#IDgrp {
  			width: 340px;
  		}
		</style>

		<script>
		// Tableau horaire
		var tab_horaire = new Array();


    <?php
      // On récupère les plages horraires depuis les paramètres
      $compteur = "1";
      foreach($tab_horaire as $key => $val) {
    ?>
      tab_horaire[<?php echo $compteur; ?>] = new Array();
      tab_horaire[<?php echo $compteur; ?>]["start"] = "<?php echo $val["start"]; ?>";
      tab_horaire[<?php echo $compteur; ?>]["end"] = "<?php echo $val["end"]; ?>";
    <?php
        $compteur++;
      }
    ?>


		function setTimeStartEnd(val) {
			$("#stparttime").val(tab_horaire[val]["start"]);
			$("#etparttime").val(tab_horaire[val]["end"]);
		}
		</script>
	</head>
	<body>
	<?php
  // echo 'ici';
	if($submit)
	{
    // echo 'ici';
		if($IDx) // Modification d'un cours
		{
			// initialisation
			if ( $IDitem < 0 ) {
				$IDuser = (int) abs($IDitem / 100);
				$IDitem = abs($IDitem + $IDuser);
			}
			$stpartdate = @$_POST["stpartdate"];
			$stparttime = @$_POST["stparttime"];
			$etparttime = @$_POST["etparttime"];

			$a = $stpartdate." ".$stparttime;
			$b = $stpartdate." ".$etparttime;

			$tab = DateCalendarToPmt($a, $b);

			$startx = $start;
			$start  = date('Y-m-d H:i:s', js2PhpTime($a));
			$end    = date('Y-m-d H:i:s', js2PhpTime($b));

			$d_start    = new DateTime($start);
			$d_end      = new DateTime($end);
			$duree = date('H:i:s', js2PhpTime($b));


			// Recup IDdata
			$sql = "SELECT _IDdata FROM `edt_data` ORBER BY _IDdata desc LIMIT 1";
			$handle = mysqli_query($mysql_link, $sql);

			while ($row = mysqli_fetch_object($handle)) {
				$IDdata =    intval($row->_IDdata) + 1;
			}


      // traitement classes multiples
      $string_class = ";";
      $listeClassesAlreadyShown = ";";
      foreach($_POST as $key => $val)
      {
      	if(substr($key, 0, 8) == "IDclass_")
      	{
          if($val != "" and $val != 0 and strpos($listeClassesAlreadyShown, $val) === false)
      		{
      			$string_class .= $val.";";
            $listeClassesAlreadyShown .= $val.";";
      		}
      	}
      }

      $query2 = "SELECT `_etat` FROM `edt_data` WHERE `_IDx` = '".$IDx."' ";
      $result2 = mysqli_query($mysql_link, $query2);
      while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
        if ($row2[0] != 1) $workflow = 3;
        elseif ($workflow != 3) $workflow = 1;
      }

      // Si on est pas un admin, alors on ne peut que mettre des indisponibilités:
      if ($_SESSION['CnxAdm'] != 255 && $_SESSION['CnxGrp'] != 4)
      {
        $IDmat        = "123";
        $IDuser       = $_SESSION['CnxID'];
        $IDuserRmpl   = 0;
        $IDclass      = ";0;";
        $string_class = ";0;";
      }

      // IDclass_0
      // IDpma
      if ($IDpma != 0 && $IDpma != '') $string_class = ";".getClassIDByPMAID($IDpma).";";
      if (isset($_POST['IDclassForced']) && $_POST['IDclassForced'] != 0) $string_class = ';'.$_POST['IDclassForced'].';';

      if ($ck_groupe == "") $ck_groupe = 0;
      if (!isset($ID_examen) || $ID_examen == "") $ID_examen = 0;
			$sql  = "UPDATE edt_data SET _IDmat = '$IDmat', _IDclass = '$string_class', _ID = '$IDuser', _semaine = '$idweek', _group = '$idgroup', _jour = '".$tab["jour"]."', ";
			$sql .= "_debut = '".date('H:i:s', js2PhpTime($a))."', _fin = '$duree', _etat = '".$workflow."', _attribut = '$ck_groupe', _plus = '$IDeleve', _IDitem = '$IDitem', _text = '".$texte."', _ID_examen = '".$ID_examen."', ";
      if ($IDpma != 0) $sql .= "_ID_pma = '".$IDpma."' ";
      else $sql .= "_ID_pma = NULL ";
      $sql .= ", _IDrmpl = '".$IDuserRmpl."' WHERE _IDx = $IDx ";

			if(mysqli_query($mysql_link, $sql)==false)
			{
				$ret['IsSuccess'] = false;
				$ret['Msg'] = mysqli_error($mysql_link);
			}
			else
			{
				$ret['IsSuccess'] = true;
				$ret['Msg'] = 'Succefully';

        // On envois les mails de confirmation de modification de cours au personnes concernées
        if ($tellTeacher == "yes") $test = sendCheckMailToTeacher($IDx, 'update');
        if ($tellStudents == "yes") $test = sendCheckMailToStudents($IDx, 'update');

			}
		}
		else // Création d'un nouveau cours
		{
			// initialisation
			if ( $IDitem < 0 ) {
				$IDuser = (int) abs($IDitem / 100);
				$IDitem = abs($IDitem + $IDuser);
			}

			$stpartdate = @$_POST["stpartdate"];
			$stparttime = @$_POST["stparttime"];
			$etparttime = @$_POST["etparttime"];

			$a = $stpartdate." ".$stparttime;
			$b = $stpartdate." ".$etparttime;

			$tab = DateCalendarToPmt($a, $b);

			$startx = $start;
			$start  = date('Y-m-d H:i:s', js2PhpTime($a));
			$end    = date('Y-m-d H:i:s', js2PhpTime($b));
			$d_start    = new DateTime($start);
			$d_end      = new DateTime($end);
			$duree = date('H:i:s', js2PhpTime($b));

			// Recup IDdata
			$sql = "select _IDdata from `edt_data` order by _IDdata desc limit 1";
			$handle = mysqli_query($mysql_link, $sql);

			while ($row = mysqli_fetch_array($handle)) {
				$IDdata = (int) $row[0] + 1;
			}

      if ($IDdata == "") $IDdata = 0;

			// traitement classes multiples
			$string_class = ";";
      $listeClassesAlreadyShown = ";";
			foreach($_POST as $key => $val)
			{
				if(substr($key, 0, 8) == "IDclass_")
				{
					if($val != "" && $val != 0 && strpos($listeClassesAlreadyShown, $val) === false)
					{
						$string_class .= $val.";";
            $listeClassesAlreadyShown .= $val.";";
					}
				}
			}

      if ($IDpma != 0 && $IDpma != '') $string_class = ";".getClassIDByPMAID($IDpma).";";

      $string_class = str_replace(";0;", ";", $string_class);
      $dt = new DateTime('December 28th'.date("Y"));
      if (substr($a, 3, 2) == 12 && date("W", js2PhpTime($a)) < 10) $weekNumber = $dt->format('W') + 1;
      else $weekNumber = date("W", js2PhpTime($a));

      if ($ck_groupe == "") $ck_groupe = 0;
      if ($ID_examen == "") $ID_examen = 0;

      // Si on séléctionne Agenda alors on le force (idem pour les indisponibilités)
      if ($_POST['typeAgenda'] != 0) $IDmat = $_POST['typeAgenda'];
if (isset($_POST['IDclassForced']) && $_POST['IDclassForced'] != 0) $string_class = ';'.$_POST['IDclassForced'].';';

			$sql  = "INSERT INTO `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
			$sql .= "`_semaine`, `_group`, `_jour`, `_debut`, `_fin`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`, `_attribut`, `_IDrmpl`, `_MatRmpl`, `_plus`, `_IDxParent`, `_text`, `_ID_examen`, `_ID_pma`) VALUES ";
			$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$string_class', '$IDitem', '$IDuser', '0', '$IDgrp', '".$tab["jour"]."', '".date('H:i:s', js2PhpTime($a))."', ";
			// $sql .= "'$duree', 'O', '".date("W", js2PhpTime($a))."', '1', '', '".date("Y", js2PhpTime($a))."', '$ck_groupe', '0', '0', '$IDeleve', '', '".$texte."', '".$ID_examen."', '".$IDpma."')";
      $sql .= "'$duree', 'O', '".$weekNumber."', '".$workflow."', NULL, '".date("Y", js2PhpTime($a))."', '".$ck_groupe."', '$IDuserRmpl', '0', '$IDeleve', '0', '".$texte."', '".$ID_examen."', ";




      if ($IDpma != 0) $sql .= "'".$IDpma."') ";
      else $sql .= "NULL) ";




			if(mysqli_query($mysql_link, $sql)==false)
			{
				$ret['IsSuccess'] = false;
				$ret['Msg'] = mysqli_error($mysql_link);
			}
			else
			{

				$ret['IsSuccess'] = true;
				$ret['Msg'] = 'Succefully';
				$id_parent = mysqli_insert_id($mysql_link);


        // On envois les mails de création de cours au personnes concernés
        if ($tellTeacher == "yes") $test = sendCheckMailToTeacher(mysqli_insert_id($mysql_link), 'insert');
        if ($tellStudents == "yes") $test = sendCheckMailToStudents(mysqli_insert_id($mysql_link), 'insert');

			}

			// **** Gestion des reports ****
			foreach($_POST as $key => $val)
			{
				$split_date = explode("-", $key);

				if($split_date[0] == "date" && $val != "")
				{
					$a = $val." ".$stparttime;
					$b = $val." ".$etparttime;

					$tab = DateCalendarToPmt($a, $b);

					$startx = $val;
					$start  = date('Y-m-d H:i:s', js2PhpTime($a));
					$end    = date('Y-m-d H:i:s', js2PhpTime($b));
					$d_start    = new DateTime($start);
					$d_end      = new DateTime($end);
					$diff = $d_start->diff($d_end);

					$duree = date('H:i:s', js2PhpTime($b));

					// traitement classes multiples
					$string_class = ";";
          $listeClassesAlreadyShown = ";";
					foreach($_POST as $key => $val)
					{
						if(substr($key, 0, 8) == "IDclass_")
						{
              if($val != "" and $val != 0 and strpos($listeClassesAlreadyShown, $val) === false)
							{
								$string_class .= $val.";";
                $listeClassesAlreadyShown .= $val.";";
							}
						}
					}

          if ($IDpma != 0 && $IDpma != '') $string_class = ";".getClassIDByPMAID($IDpma).";";
          if (isset($_POST['IDclassForced']) && $_POST['IDclassForced'] != 0) $string_class = ';'.$_POST['IDclassForced'].';';

          // Recup IDdata
          $sql = "select _IDdata from `edt_data` order by _IDdata desc limit 1";
          $handle = mysqli_query($mysql_link, $sql);
          while ($row = mysqli_fetch_object($handle)) {
            $IDdata = intval($row->_IDdata) + 1;
          }

					$sql  = "INSERT INTO `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
					$sql .= "`_semaine`, `_group`, `_jour`, `_debut`, `_fin`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`, `_attribut`, `_IDrmpl`, `_MatRmpl`, `_plus`, `_IDxParent`, `_text`, `_ID_examen`, `_ID_pma`) VALUES ";
					$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$string_class', '$IDitem', '$IDuser', '0', '$IDgrp', '".$tab["jour"]."', '".date('H:i:s', js2PhpTime($a))."', ";
					$sql .= "'$duree', 'O', '".date("W", js2PhpTime($a))."', '".$workflow."', '', '".date("Y", js2PhpTime($a))."', '$ck_groupe', '$IDuserRmpl', '', '', '$id_parent', '".$texte."', '".$ID_examen."', ";
          if ($IDpma != 0) $sql .= "'".$IDpma."') ";
          else $sql .= "NULL) ";

					if(mysqli_query($mysql_link, $sql)==false)
					{
						$ret['IsSuccess'] = false;
						$ret['Msg'] = mysqli_error($mysql_link);
					}
					else
					{
						$ret['IsSuccess'] = true;
						$ret['Msg'] = 'Succefully';
					}
				}
			}

			// **** Gestion des répétitions ****
			if($date_finperiode && !$nbrep) // Si repet par fin de période
			{
				$joursemaine = date("N", strtotime($start));	// Recherche jour de la première date

				$i = mktime(0, 0, 0, date("m", strtotime($start)), date("d", strtotime($start)) , date("Y", strtotime($start)));
				$j = js2PhpTime($date_finperiode." 00:00:00");

				// $pas c'est 1 jour en time stamp
				// $fin, c'est une semaine. En gros, on commencera la boucle à 0 et on testera
				// les jours 1 par 1 jusqu'à arriver à la fin des 7 jours de la semaine.
				$pas=60*60*24;
				$fin=$i+(60*60*24*6);

				// recherche du premier jour choisi de la période donnée
				// si on tombe sur le bon, on sort de la boucle
				for($deb=$i; $deb<= $fin; $deb+=$pas)
				{
					if(date("N", $deb) == $joursemaine)
					{
						$premier = $deb;
						break;
					}
				}
				// ici, on a un pas de 7 jours, histoire de tomber tout le temps sur le même jour de la semaine.
				// par exemple, on sort tous les mercredis de la période choisie.
				$pas=60*60*24*7;
				$joursemainesql = $joursemaine - 1;

				// récupération de tous les jours choisis pour la période donnée
				$firstrepet = 0;
				for($premier; $premier <= $j; $premier+=$pas)
				{
					if($firstrepet > 0) // On exclu le premier déjà ajouté en tant que parent
					{
						$a = date("d/m/Y", $premier)." ".$stparttime;
						$b = date("d/m/Y", $premier)." ".$etparttime;
						$tab = DateCalendarToPmt($a, $b);

            if ($IDpma != 0 && $IDpma != '') $string_class = ";".getClassIDByPMAID($IDpma).";";
            if (isset($_POST['IDclassForced']) && $_POST['IDclassForced'] != 0) $string_class = ';'.$_POST['IDclassForced'].';';

            // Recup IDdata
            $sql = "select _IDdata from `edt_data` order by _IDdata desc limit 1";
            $handle = mysqli_query($mysql_link, $sql);
            while ($row = mysqli_fetch_object($handle)) {
              $IDdata = intval($row->_IDdata) + 1;
            }

						$sql  = "INSERT INTO `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
						$sql .= "`_semaine`, `_group`, `_jour`, `_debut`, `_fin`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`, `_attribut`, `_IDrmpl`, `_MatRmpl`, `_plus`, `_IDxParent`, `_text`, `_ID_examen`, `_ID_pma`) VALUES ";
						$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$string_class', '$IDitem', '$IDuser', '0', '$IDgrp', '$joursemainesql', '".date('H:i:s', js2PhpTime($a))."', ";
						$sql .= "'$duree', 'O', '".date("W", js2PhpTime($a))."', '".$workflow."', NULL, '".date("Y", js2PhpTime($a))."', '$ck_groupe', '$IDuserRmpl', '0', '', '$id_parent', '".$texte."', '".$ID_examen."', ";
            if ($IDpma != 0) $sql .= "'".$IDpma."') ";
            else $sql .= "NULL) ";

						if($quinzaine == 1) { // Alterner par quinzaine
							if($firstrepet % 2 == 0) {
								mysqli_query($mysql_link, $sql);
							}
						}
						else {
							mysqli_query($mysql_link, $sql);
						}
					}

					$firstrepet++;
				}
			}

			if($nbrep && !$date_finperiode) // Si uniquement nombre de repet
			{
				$joursemaine = date("N", strtotime($start));	// Recherche jour de la première date

				// on ajouter le nombre de semaine à répeter
				$i = mktime(0, 0, 0, date("m", strtotime($start)), date("d", strtotime($start)) , date("Y", strtotime($start)));

				// $pas c'est 1 jour en time stamp
				// $fin, c'est une semaine. En gros, on commencera la boucle à 0 et on testera
				// les jours 1 par 1 jusqu'à arriver à la fin des 7 jours de la semaine.
				$pas=60*60*24;
				$fin=$i+(60*60*24*6);

				// recherche du premier jour choisi de la période donnée
				// si on tombe sur le bon, on sort de la boucle
				for($deb=$i; $deb<= $fin; $deb+=$pas)
				{
					if(date("N", $deb) == $joursemaine)
					{
						$premier = $deb;
						break;
					}
				}
				// ici, on a un pas de 7 jours, histoire de tomber tout le temps sur le même jour de la semaine.
				// par exemple, on sort tous les mercredis de la période choisie.
				$pas=60*60*24*7;

				// récupération de tous les jours choisis pour la période donnée
				$firstrepet = 0;

				if($quinzaine == 1) { // Si alertance par quinzaine alors il faut doubler les répétitions
					$nbrep = $nbrep * 2;
				}

				for($premier; $firstrepet <= $nbrep; $premier+=$pas)
				{
					if($firstrepet == 0)
					{
						$jourrepet = $tab["jour"];
					}

					if($firstrepet > 0) // On exclu le premier déjà ajouté en tant que parent
					{
						$a = date("d/m/Y", $premier)." ".$stparttime;
						$b = date("d/m/Y", $premier)." ".$etparttime;
						$tab = DateCalendarToPmt($a, $b);

            if ($IDpma != 0 && $IDpma != '') $string_class = ";".getClassIDByPMAID($IDpma).";";
            if (isset($_POST['IDclassForced']) && $_POST['IDclassForced'] != 0) $string_class = ';'.$_POST['IDclassForced'].';';

						$sql  = "INSERT INTO `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
						$sql .= "`_semaine`, `_group`, `_jour`, `_debut`, `_fin`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`, `_attribut`, `_IDrmpl`, `_MatRmpl`, `_plus`, `_IDxParent`, `_text`, `_ID_examen`, `_ID_pma`) VALUES ";
						$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$string_class', '$IDitem', '$IDuser', '0', '$IDgrp', '$jourrepet', '".date('H:i:s', js2PhpTime($a))."', ";
						$sql .= "'$duree', 'O', '".date("W", js2PhpTime($a))."', '".$workflow."', '', '".date("Y", js2PhpTime($a))."', '$ck_groupe', '', '', '', '$id_parent', '".$texte."', '".$ID_examen."', ";
            if ($IDpma != 0) $sql .= "'".$IDpma."') ";
            else $sql .= "NULL) ";

						if($quinzaine == 1) { // Alterner par quinzaine
							if($firstrepet % 2 == 0) {
								mysqli_query($mysql_link, $sql);
							}
						}
						else {
							mysqli_query($mysql_link, $sql);
						}
					}

					$firstrepet++;
				}
			}
		}
		?>

    <?php // DEBUG ?>

		<script>
  		// parent.$("#gridcontainer").reload();
  		// CloseModelWindow(null,true);

      parent.$('body').find('#reloadIframeBtn').click();
		</script>
		<?php
	}
	?>

  <button type="button" id="showEditNewEventModal" class="btn btn-primary" data-toggle="modal" data-target="#editNewEventModal" style="display: none;">
    Launch demo modal
  </button>
  <!-- Modal -->
  <div class="modal fade" id="editNewEventModal"data-backdrop="static" data-backdrop="static" tabindex="-1" aria-labelledby="editNewEventModal" aria-hidden="false">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editNewEventModalLabel"><?php if (isset($IDx) && $IDx != '') echo 'Modification d\'évènement'; else echo 'Création d\'évènement'; ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">



          <div style="padding: 4px;">
        		<form action="edit.db.php?<?php echo isset($event)?"&id=".$event->Id:""; ?>" class="fform" id="fmEdit" name="fmEdit" method="post">
        			<div class="tabbable">
        				<ul class="nav nav-tabs">
        					<li class="nav-item active">
        						<a href="#tab1" class="nav-link active" data-toggle="tab">
        							<i class="fa fa-info-circle"></i> <?php echo $msg->read($EDT_NOMINFO); ?>
        						</a>
        					</li>
        					<?php
        					if(!$IDxx)
        					{
        						?>
        						<li class="nav-item">
        							<a href="#tab2" class="nav-link" data-toggle="tab">
        								<i class="fa fa-repeat"></i> <?php echo $msg->read($EDT_NOMREPORT_MANU); ?>
        							</a>
        						</li>
        						<li class="nav-item">
        							<a href="#tab3" class="nav-link" data-toggle="tab">
        								<i class="fa fa-repeat"></i> <?php echo $msg->read($EDT_NOMREPORT_HEBDO); ?>
        							</a>
        						</li>
        						<?php
        					}
        					?>
        				</ul>
        				<div class="tab-content" style="padding-top: 10px; /* height:90%; */">
        					<div id="tab1" class="tab-pane active">

        						<input type="hidden" name="generique" value="<?php echo $generique; ?>" />
        						<input type="hidden" name="start" value="<?php echo $start; ?>" />
        						<input type="hidden" name="end" value="<?php echo $end; ?>" />
        						<input type="hidden" name="IDitem" value="<?php echo $IDitem; ?>" />
        						<input type="hidden" name="IDuser" value="<?php echo $IDuser; ?>" />
        						<input type="hidden" name="IDcentre" value="<?php echo $IDcentre; ?>" />
        						<input type="hidden" name="IDclass" value="<?php echo $IDclass; ?>" />
        						<input type="hidden" name="IDedt" value="<?php echo $IDedt; ?>" />
        						<input type="hidden" name="sid" value="<?php echo $sid; ?>" />
        						<input type="hidden" name="etat" value="<?php echo $etat; ?>" />
        						<?php
        						if($IDxx)
        						{
        							?>
        							<input type="hidden" name="IDx" value="<?php echo $IDxx; ?>" />
        							<?php
        						}
        						?>

        						<?php if(isset($event)){
        							$sarr = explode(" ", date("d/m/Y H:i:s", strtotime(php2JsTime(mySql2PhpTime($event->StartTime)))));
        							$earr = explode(" ", date("d/m/Y H:i:s", strtotime(php2JsTime(mySql2PhpTime($event->EndTime)))));
        						}
        						else
        						{
        							$sarr = explode(" ", date("d/m/Y H:i:s", strtotime($start)));
        							$earr = explode(" ", date("d/m/Y H:i:s", strtotime($end)));
        						}
        						?>

                    <div id="indisponible" style="width: 100%; text-align: center; font-size: 30px; min-height: 3px;"></div>

                    <ul class="nav nav-pills nav-fill">
                      <li class="nav-item">
                        <a class="nav-link active" id="matiereBtn" data-toggle="pill" onclick="showMat();" href="#">Matière</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="uvBtn" data-toggle="pill" onclick="showUV();" href="#">UV</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="agendaBtn" data-toggle="pill" onclick="showAgenda();" href="#">Agenda</a>
                      </li>
                    </ul>

                    <hr class="mt-3 mb-3">
        						<table style="width: 100%;">
        							<!-- Date -->
        							<tr>
							          <td class="w-25"><label for="stpartdate"><?php echo $msg->read($EDT_LABEL_DATE); ?></label></td>
        								<td>
        									<input id="timezone" name="timezone" type="hidden" value="" />

                          <div class="form-row">
          									<input MaxLength="10" class="form-control mr-3" id="stpartdate" name="stpartdate" style="width:111px; /* height: 30px; padding: 6px 4px 4px 6px; */" type="text" value="<?php echo $sarr[0]; ?>" />

                            <script>
                              $(function() {
                                $('#stpartdate').daterangepicker({
                                  opens: 'left',
                                  locale: lang_datepicker,
                                  "singleDatePicker": true,
                                  "autoApply": true
                                }).on('change', function(){
                                  checkIfClassBusy();
                                  checkIfIntervenantBusy('0');
                                  checkIfIntervenantBusy('1');
                                  checkIfRoomBusy();
                                });
                              });
                            </script>
          									<input id="stparttime" name="stparttime" class="form-control mr-3" style="width:90px;" type="text" value="<?php echo $sarr[1]; ?>" />
          									<input id="etparttime" name="etparttime" class="form-control mr-3" style="width:90px;" type="text" value="<?php echo $earr[1]; ?>" />
                            <script>
                              $(document).ready(function(){
                                $('#stparttime').timepicker({
                                  'timeFormat': timepicker['timeFormat'],
                                  'step': 15
                                }).on('change', function(){
                                  checkIfClassBusy();
                                  checkIfIntervenantBusy('0');
                                  checkIfIntervenantBusy('1');
                                  checkIfRoomBusy();
                                });
                              });

                              $(document).ready(function(){
                                $('#etparttime').timepicker({
                                  'timeFormat': timepicker['timeFormat'],
                                  'step': 15
                                }).on('change', function(){
                                  checkIfClassBusy();
                                  checkIfIntervenantBusy('0');
                                  checkIfIntervenantBusy('1');
                                  checkIfRoomBusy();
                                });
                              });
                            </script>



          									<select name="time" id="timeSelect" class="form-control custom-select" onchange="setTimeStartEnd(this.options[this.selectedIndex].value);" style="width: 70px;">
          										<option value="0"></option>
          										<?php
  										          foreach($tab_horaire as $key => $val) echo '<option value="'.$key.'" title="'.$val["start"].' - '.$val["end"].'">'.$val["lib"].'</option>';
          										?>
          									</select>
                          </div>
        								</td>
        							</tr>

                      <!-- Classe -->
                      <input type="hidden" name="IDclass_0" id="IDclass_0" value="0">

                      <tr><td colspan="2"><hr class="mt-3 mb-3"></td></tr>









        							<!-- Classe -->
        							<tr style="display: none;" id="classRow">
        								<td class="align-top"><label for="IDclass_0" class="input_class"><?php echo $msg->read($EDT_LABEL_CLASSE); ?></label>&nbsp;<span id="add_class"><i class="fa fa-plus-square"></i></span></td>
        								<td>
                          <div id="div_class">
          									<?php
          									// traitement classes multiples
          									$num_class = 1;
          									$string_class = substr($IDclass, 1, strlen($IDclass)-2);
          									$string_class = explode(";", $string_class);

          									foreach($string_class as $key => $val)
          									{
          										$js_class = '<option value="0">Sélectionnez une classe</option>';

          										$query  = "select _IDclass, _ident, _code from campus_classe where _visible = 'O' order by _code ASC ";
          										$result = mysqli_query($mysql_link, $query);
          										while ($row = mysqli_fetch_row($result)) {
                                if ($val == $row[0]) $selected = 'selected'; else $selected = '';
        												$js_class .= '<option '.$selected.' value="'.$row[0].'">'.$codeclasse.addslashes($row[1]).'</option>';
          										}
          										$js_class .= '</select>';
          										?>

          										<script>
            										num_class = <?php echo $num_class; ?>;
                                var checkPromBusyDiv = '<div id="promoDispo_' + num_class + '" class="col-4"></div>';
                                $("#div_class").append('<div class="row mb-2"><div class="col-7"><select <?php if($IDeleve != 0){ echo "disabled=\"disabled\""; }?> id="IDclass_'+num_class+'" name="IDclass_'+num_class+'" onchange="checkIfPromoBusy('+num_class+');" class="input_class custom-select"><?php echo $js_class; ?></div>' + checkPromBusyDiv + '</div>');
            										num_class++;
          										</script>

          										<?php
          										$num_class++;
          									}

          									?>
          									<script>
            									$("#add_class").click(function() {
                                var checkPromBusyDiv = '<div id="promoDispo_' + num_class + '" class="col-4"></div>';
            										$("#div_class").append('<div class="row mb-2"><div class="col-7"><select id="IDclass_' + num_class + '" name="IDclass_' + num_class + '" onchange="checkIfPromoBusy(' + num_class + ');" class="input_class custom-select"><?php echo $js_class; ?></div>' + checkPromBusyDiv + '</div>');
            										num_class++;
            									});
          									</script>
                          </div>
        								</td>
        							</tr>







        							<!-- Groupe -->
        							<tr style="display: none;">
        								<td><label for="IDgrp"><strong><?php echo $msg->read($EDT_OR); ?></strong> <?php echo $msg->read($EDT_LABEL_GRP); ?></label></td>
        								<td>
        									<?php
        									DisplayListGroupe($IDgrp, $IDcentre);
        									?>
        									<input type="hidden" name="orgens" id="orgens" value="0" />
        									<input type="hidden" name="orgmat" id="orgmat" value="0" />
        								</td>
        							</tr>

        							<!-- Cours particulier -->
                      <!-- TODO: Remettre la notion de cours particulier en place -->
        							<tr style="display: none;">
        								<td><label for="IDeleve"><strong><?php echo $msg->read($EDT_OR); ?></strong> <?php echo $msg->read($EDT_LABEL_PART); ?></td>
        								<td>
        									<div class="ui-widget" id="liste_eleve">
        										<label for="tags_0">
        											<input id="tags_0" name="tags_0" value="<?php ($IDeleve) ? print getUserNameByID($IDeleve) : ""; ?>">
        										</label>
        										<input type="hidden" id="tags-id_0" name="tags-id_0" value="<?php echo $IDeleve; ?>" />
        									</div>

        									<script src="script/jquery-ui/jquery.js"></script>
        									<script src="script/jquery-ui.min.js"></script>
        								</td>
        							</tr>

                      <!-- UV -->
                      <tr style="display: none;" id="uvRow">
                        <td><label for="IDuv">UV</label></td>
                        <td>
                          <?php if (isset($ID_examen)) $temp = $ID_examen; else $temp = ''; ?>
                          <div class="row mb-2">
                            <div class="col-7">
                              <?php echo getUVSelect('IDuv', 'IDuv', $temp, 0, 'formulaire'); ?>
                            </div>
                          </div>
                        </td>
                      </tr>

                      <script>
                      // Si on sélectionne un UV, on affiche le menu pour sélectionner une promo
                      $('#IDuv').on('change', function() {
                        getClassForceSelectValueUV();
                      });

                      function getClassForceSelectValueUV(IDx = 0) {
                        $('#prom_force_tr').show();
                        var UVid = $('#IDuv').val();
                        $.ajax({
                          url : 'include/fonction/ajax/edt.php?action=getClassIDByYearAndUV',
                          type : 'POST',
                          data : 'year=<?php if (isset($year)) echo $year; ?>&UVid=' + UVid + '&IDx=' + IDx,
                          dataType : 'html',
                          success : function(code_html, statut){
                            $('#IDclassForced').val(code_html);
                          }
                        });
                      }
                      </script>


                      <!-- Agenda/Indisponible -->
                      <tr style="display: none;" id="agendaIndisponibleRow">
                        <td><label for="typeAgenda">Type d'agenda</label></td>
                        <td>
                          <div class="row mb-2">
                            <div class="col-7">
                              <select name="typeAgenda" id="typeAgenda" class="custom-select">
                                <option value="0">Sélectionez votre type d'agenda</option>
                                <option value="122" typeOfEvent="3" <?php if ($typeAgenda == 3) echo "selected"; ?>>Agenda</option>
                                <option value="123" typeOfEvent="2" <?php if ($typeAgenda == 2) echo "selected"; ?>>Indisponible</option>
                              </select>
                            </div>
                          </div>
                        </td>
                      </tr>

                      <!-- Agenda titre -->
                      <tr style="display: none;" id="agendaRow">
                        <td><label for="agendaText">Titre de l'évènement</label></td>
                        <td>
                          <div class="row">
                            <div class="col-7">
                              <input value="<?php if (isset($texte)) echo $texte; ?>" name="agendaText" id="agendaText" type="text" class="form-control">
                            </div>
                          </div>
                        </td>
                      </tr>



                      <!-- Matière -->
                      <tr id="matRow">
                        <td>
                          <label for="IDmat">
                            <?php echo $msg->read($EDT_LABEL_MAT); ?>
                          </label>
                        </td>
                        <td>
                          <div class="row mb-2">
                            <div class="col-7">
                              <?php if (isset($IDpma)) $ID_selected = $IDpma; else $ID_selected = 0; ?>
                              <?php echo showPMAList("IDpma", $ID_selected, 0); ?>
                            </div>
                            <div id="classDispo" class="col-5"></div>
                          </div>
                        </td>
                      </tr>

                      <input type="hidden" name="IDmat" id="IDmat" value="<?php echo $IDmat; ?>">



                      <!-- Classe si force avec matière -->
                      <tr id="prom_force_tr" style="display: none;">
                        <td>
                          <label for="IDmat">
                            Classe <span id="force_prom" style="text-decoration: underline; font-size: 10px; display: inline-block;">forcer la classe</span>
                          </label>
                        </td>
                        <td>
                          <div class="row">
                            <div class="col-7"><?php echo getClassSelect('IDclassForced', 'IDclassForced', 0, 0, '', 0, 1); ?></div>
                          </div>
                        </td>
                      </tr>


                      <tr><td colspan="2"><hr class="mt-3 mb-3"></td></tr>


                      <?php
                        if (substr_count($IDclass, ';') == 2) {
                          echo '<script>
                            $("#IDclassForced").val("'.$string_class[0].'");
                            $("#prom_force_tr").show();
                          </script>';
                        }
                      ?>

                      <script>

                      function getClassForceSelectValuePMA(IDx = 0) {
                        $('#prom_force_tr').show();
                        var pma = $('#IDpma').val();
                        var date = $('input[name="stpartdate"]').val();
                        $.ajax({
                          url : 'include/fonction/ajax/edt.php?action=getClassIDByYearAndPMA',
                          type : 'POST',
                          data : 'year=<?php if (isset($year)) echo $year; ?>&pma=' + pma + '&date=' + date + '&IDx=' + IDx,
                          dataType : 'html',
                          success : function(code_html, statut){
                            $('#IDclassForced').val(code_html);
                          }
                        });
                      }
                        // Quand on sélectionne un PMA, on affiche le select de la promo avec la bonne promo sélectionnée
                        $('#IDpma').on('change', function() {
                          getClassForceSelectValuePMA();
                        });

                        // Au moment de submit le form, on retire l'attr disabled au select pour que la valeur passe bien dans le formulaire
                        $(function ($) {
                          $('#fmEdit').bind('submit', function () {
                            $(this).find('#IDclassForced').prop('disabled', false);
                          });
                        });


                        // Quand on clique sur le bouton pour activer le "forçage" d'une promo
                        $('#force_prom').click(function() {
                          if (!$(this).hasClass('activated'))
                          {
                            if (confirm('Êtes vous sûr de vouloir forcer une autre classe ?')) {
                              $('#IDclassForced').removeAttr('disabled');
                              $('#force_prom').addClass('activated');
                            }
                          }
                        });
                      </script>




        							<!-- Enseignant -->
        							<tr id="teacherRow_0" class="teacherRow" style="display: none;">
        								<td><label for="IDuser"><?php echo $msg->read($EDT_LABEL_ENS); ?> <button type="button" id="addATeacher" onclick="addATeacher();" class="btn"><i class="fa fa-plus" aria-hidden="true"></i></button></label></td>
        								<td>
                          <div class="row mb-2">
                            <div id="teacherBox"></div>
                            <div class="col-7">
                              <select class="custom-select" name="IDuser_0" id="IDuser_0" onchange="checkIfIntervenantBusy('0');" required></select>
                            </div>
                            <div id="profDispo_0" class="col-5"></div>
                          </div>
        								</td>
        							</tr>

                      <!-- Enseignant 2 -->
        							<tr id="teacherRow_1" class="teacherRow" style="display: none;">
        								<td><label for="IDuser"><?php echo $msg->read($EDT_LABEL_ENS); ?></label></td>
        								<td>
                          <div class="row mb-2">
                            <div id="teacherBox"></div>
                            <div class="col-7">
                              <select class="custom-select" name="IDuser_1" id="IDuser_1" onchange="checkIfIntervenantBusy('1');" required></select>
                            </div>
                            <div id="profDispo_1" class="col-5"></div>
                          </div>
        								</td>
        							</tr>


        							<!-- Salle -->
        							<tr id="roomRow">
        								<td><label for="IDitem"><?php echo $msg->read($EDT_LABEL_SALLE); ?></label></td>
        								<td>
                          <div class="row mb-2">
                            <div class="col-7">
            									<?php
            									print("
            										<select id=\"IDitem\" name=\"IDitem\" class=\"custom-select\" required=\"required\" onchange=\"checkIfRoomBusy();\">
            											<option value=\"0\"></option>
            										");

            										$query  = "select _IDitem, _title from edt_items ";
            										$query .= "where _IDcentre = '$IDcentre' ";
            										$query .= "AND _lang = '".$_SESSION["lang"]."' order by _title ";
            										$result = mysqli_query($mysql_link, $query);
            										while ($row = mysqli_fetch_row($result)) {
                                  if ($IDitem == $row[0]) $selected = 'selected'; else $selected = '';
                                  echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
          											}


            									?>
                              </select>
                            </div>
                            <div id="roomDispo" class="col-5"></div>
                          </div>
        								</td>
        							</tr>



                      <tr><td colspan="2"><hr class="mt-3 mb-3"></td></tr>

                      <!-- Workflow -->
        							<tr id="workflow">
        								<td><label for="workflow_ask">Demande auprès de l'intervenant</label></td>
        								<td>
                          <?php if ($workflow == 1) { ?>
                            <input type="checkbox" name="workflow" value="yes"> Demander confirmation à l'intervenant ?</input>
                          <?php } ?>
                          <?php if ($workflow == 3) { ?>
                            <span class="alert alert-warning"><i class="fa fa-question" aria-hidden="true"></i> Demande en cours d'envois</span>
                          <?php } ?>
                          <?php if ($workflow == 4) { ?>
                            <span class="alert alert-info"><i class="fa fa-envelope-o" aria-hidden="true"></i> Demande envoyée</span>
                          <?php } ?>
                          <?php if ($workflow == 5) { ?>
                            <span class="alert alert-success"><i class="fa fa-check" aria-hidden="true"></i> Demande acceptée</span>
                          <?php } ?>
                          <?php if ($workflow == 6) { ?>
                            <span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i> Demande refusée</span>
                          <?php } ?>
        								</td>
        							</tr>


                      <tr><td colspan="2"><hr class="mt-3 mb-3"></td></tr>

                      <tr id="tellTeacher">
        								<td>
        									<label for="tellTeacher_ask">
                            Prévenir
        									</label>
        								</td>
        								<td><input type="checkbox" name="tellTeacher" value="yes" <?php if (getParam('autoTellTeacherAndStudents') && $_SESSION['CnxGrp'] >= 4) echo "checked"; ?>> Prévenir l'intervenant ?</input></td>

        							</tr>

                      <tr id="tellStudents">
        								<td>
        									<label for="tellStudents_ask">
                            Prévenir
        									</label>
        								</td>
        								<td><input type="checkbox" name="tellStudents" value="yes" <?php if (getParam('autoTellTeacherAndStudents') && $_SESSION['CnxGrp'] >= 4) echo "checked"; ?>> Prévenir les étudiants ?</input></td>
        							</tr>
        						</table>

        					</div> <!-- Close tab 1 -->

        					<div id="tab2" class="tab-pane">
        						<table width="100%">
        							<tr>
        								<td style="width: 360px; vertical-align: top;">
        									<div id="newline-wrapper">
        										<table class="split-date-wrap" cellpadding="0" cellspacing="0" border="0" class="align-center">
        											<tbody>
        												<tr>
        													<td>
        														<input type="text" class="form-control mb-2" value="" id="date-1" name="date-1" value="" />
        														<script>

                                    $(function() {
                                      $('#date-1').daterangepicker({
                                        opens: 'left',
                                        locale: lang_datepicker,
                                        "singleDatePicker": true,
                                        "autoApply": false
                                      }).val('').on('change', function(){
                                        if(!$("#date-2").length && $('#date-1').val() != '')
        																{
        																	// newline();
        																}
                                      }).on('cancel.daterangepicker', function(ev, picker) {
                                        $(this).val('');
                                      });
                                    });

        															$("#date-1").change(function() {
        																if(!$("#date-2").length)
        																{
        																	// newline();
        																}
        															});
        														</script>
        													</td>
        													<td> </td>
        												</tr>
        											</tbody>
        										</table>
        									</div>
        								</td>
        								<td style="padding-left: 30px; vertical-align: top">
        									<div class="alert alert-info small" style="height: auto;">
        										<?php echo $msg->read($EDT_HELP_REPORT); ?>
        									</div>
        								</td>
        							</tr>
        						</table>

        					</div> <!-- Close tab 2 -->

        					<div id="tab3" class="tab-pane">

        						<table width="100%">
        							<tr>
        								<td class="align-left" style="width: 180px">
        									<label for="date_finperiode" style="margin-left: 24px;">
        										<?php echo $msg->read($EDT_LABEL_FINPERIODE); ?>
        									</label>
        								</td>
        								<td>
        									<input class="form-control" id="date_finperiode" name="date_finperiode" type="text" style="width: 111px;" />
                          <script>
                          $(function() {
                            $('#date_finperiode').daterangepicker({
                              opens: 'left',
                              locale: lang_datepicker,
                              "singleDatePicker": true,
                              "autoApply": false
                            }, function(date) {
                              $("#nbrep").prop('disabled', 'disabled');

                            }).val('').on('cancel.daterangepicker', function(ev, picker) {
                              $(this).val('');
                            }).on('apply.daterangepicker', function(){
                              $("#nbrep").prop('disabled', 'disabled');
                            }).on('cancel.daterangepicker', function(){
                              $("#nbrep").prop('disabled', false);
                            });
                          });
                          </script>
        								</td>
        								<td rowspan="2" style="padding-left: 30px;">
        									<div class="alert alert-info small" style="height: auto;">
        										<?php echo $msg->read($EDT_HELP_REPET); ?>
        									</div>
        								</td>
        							</tr>
        							<tr>
        								<td class="align-left" style="width: 180px">
        									<label for="nprep" style="margin-left: 24px;">
        										<?php echo $msg->read($EDT_LABEL_NBREP); ?>
        									</label>
        								</td>
        								<td>
        									<input type="number" id="nbrep" name="nbrep" class='form-control' min="0" />
        								</td>
        							</tr>
        							<tr>
        								<td class="align-left" style="width: 180px">
        									<label for="nprep" style="margin-left: 24px;">
        										<?php echo $msg->read($EDT_LABEL_QUINZAINE); ?>
        									</label>
        								</td>
        								<td>
        									<input type="checkbox" id="quinzaine" name="quinzaine" value="1" />
        								</td>
        							</tr>
        						</table>

        					</div> <!-- Close tab 3 -->
        				</div> <!-- Close tab Tab -->
        			</div> <!-- Close tab Globale -->





        <input type="hidden" name="valid_x" value="1" />




        	<script>
        	// $(function() {
        	// 	$( "#tags_0" ).autocomplete({
        	// 		source: "getInfos.php?type=13&IDcentre=<?php echo $IDcentre; ?>",
        	// 		select: function (event, ui) {
        	// 			$("#tags-id_0").val(ui.item.id);
        	// 			$("#tags_0").val(ui.item.value);
          //
        	// 			if($("#tags_0").val() != "")
        	// 			{
        	// 				$(".input_class").each(function( index ) {
        	// 					$(this).prop('disabled', 'disabled');
        	// 				});
        	// 				$("#IDgrp").prop('disabled', 'disabled');
        	// 			}
        	// 			else
        	// 			{
        	// 				$(".input_class").each(function( index ) {
        	// 					$(this).prop('disabled', false);
        	// 				});
        	// 				$("#IDgrp").prop('disabled', false);
        	// 				$("#tags-id_0").val("");
        	// 			}
        	// 		}
        	// 	});
        	// });

        	// Evenement sur cours particulier
        	// $("#tags_0").on('input',function(e){
        	// 	if($("#tags_0").val() != "")
        	// 	{
        	// 		$(".input_class").each(function( index ) {
        	// 			$(this).prop('disabled', 'disabled');
        	// 		});
        	// 		$("#IDgrp").prop('disabled', 'disabled');
        	// 	}
        	// 	else
        	// 	{
        	// 		$(".input_class").each(function( index ) {
        	// 			$(this).prop('disabled', false);
        	// 		});
        	// 		$("#IDgrp").prop('disabled', false);
        	// 		$("#tags-id_0").val("");
        	// 	}
        	// });

        	// Evenement sur classe
        	$("#IDclass_0").change( function(){
        		if($("#IDclass_0").val() != "")
        		{
        			$("#IDgrp").prop('disabled', 'disabled');
        			$("#tags_0").prop('disabled', 'disabled');
        			$.ajax({url: "getInfos.php?type=14&IDclass="+ $("#IDclass_0").val(), success: function(result){
        				$("#IDmat").html(result);
        			}});
        		}
        		else
        		{
        			$("#IDgrp").prop('disabled', false);
        			$("#tags_0").prop('disabled', false);
        			$.ajax({url: "getInfos.php?type=15", success: function(result){
        				$("#IDmat").html(result);
        			}});
        		}
        	});

        	// Au chargement de page si classe déja présente
        	$("#IDclass_0").ready( function(){
        		if($("#IDclass_0").val() != "")
        		{
        			$("#IDgrp").prop('disabled', 'disabled');
        			$("#tags_0").prop('disabled', 'disabled');
        			$.ajax({url: "getInfos.php?type=14&IDclass="+ $("#IDclass_0").val(), success: function(result){
        				$("#IDmat").html(result);
        				<?php
        				if($IDmat)
        				{
        					?>
        					if($("#IDmat").val() == "")
        					{
        						$("#IDmat option[value='<?php echo $IDmat; ?>']").prop("selected", true);
        					}
        					<?php
        				}
        				?>
        			}});
        		}
        		else
        		{
        			$("#IDgrp").prop('disabled', false);
        			$("#tags_0").prop('disabled', false);
        			$.ajax({url: "getInfos.php?type=15", success: function(result){
        				$("#IDmat").html(result);
        				<?php
        				if($IDmat)
        				{
        					?>
        					if($("#IDmat").val() == "")
        					{
        						$("#IDmat option[value='<?php echo $IDmat; ?>']").prop("selected", true);
        					}
        					<?php
        				}
        				?>
        			}});
        		}
        	});

        	// Evenement sur groupe
        	$("#IDgrp").change( function(){
        		if($("#IDgrp").val() != 0)
        		{
        			$(".input_class").each(function( index ) {
        				$(this).prop('disabled', 'disabled');
        			});
        			$("#tags_0").prop('disabled', 'disabled');
        			$("#add_class").css('display', 'none');
        		}
        		else
        		{
        			$(".input_class").each(function( index ) {
        				$(this).prop('disabled', false);
        			});
        			$("#tags_0").prop('disabled', false);
        			$("#add_class").css('display', 'inline-block');
        		}
        	});

        	// Evenement sur date fin de période
        	$("#date_finperiode").on('change', function() {
        		if($("#date_finperiode").val() != "")
        		{
        			$("#nbrep").prop('disabled', 'disabled');
        		}
        		else
        		{
        			$("#nbrep").prop('disabled', false);
        		}
        	});

        	// $("#date_finperiode").change(function() {
        	// 	if($("#date_finperiode").val() != "")
        	// 	{
        	// 		$("#nbrep").prop('disabled', 'disabled');
        	// 	}
        	// 	else
        	// 	{
        	// 		$("#nbrep").prop('disabled', false);
        	// 	}
        	// });

        	// Evenement sur nb repetition
        	$("#nbrep").keyup(function() {
        		if($("#nbrep").val() != "" && $("#nbrep").val() != 0)
        		{
        			$("#date_finperiode").prop('disabled', 'disabled');
        		}
        		else
        		{
        			$("#date_finperiode").prop('disabled', false);
        		}
        	});

        	$("#nbrep").change(function() {
        		if($("#nbrep").val() != "" && $("#nbrep").val() != 0)
        		{
        			$("#date_finperiode").prop('disabled', 'disabled');
        		}
        		else
        		{
        			$("#date_finperiode").prop('disabled', false);
        		}
        	});

        	<?php
        	// Vérouillage ouverture edition
        	if($IDxx)
        	{
        		?>
        		$(document).ready(function() {
        			if($("#IDclass_0").val() != "")
        			{
        				$("#IDgrp").prop('disabled', 'disabled');
        				$("#tags_0").prop('disabled', 'disabled');
        			}
        			else if($("#IDgrp").val() != 0)
        			{
        				$(".input_class").each(function( index ) {
        					$(this).prop('disabled', 'disabled');
        				});
        				$("#tags_0").prop('disabled', 'disabled');
        				$("#add_class").css('display', 'none');
        			}
        			else if($("#tags_0").val() != "")
        			{
        				$(".input_class").each(function( index ) {
        					$(this).prop('disabled', 'disabled');
        				});
        				$("#IDgrp").prop('disabled', 'disabled');
        			}
        		});
        		<?php
        	}
        	?>

        	function VerifForm()
        	{
        		var forminvalid = 0;
        		if($("#IDmat").val() == "")
        		{
        			$("#IDmat").css("border","1px solid red");
        			forminvalid++;
        		}
        		else
        		{
        			$("#IDmat").css("border","1px solid #cccccc");
        		}

        		if($("#IDuser").val() == "")
        		{
        			$("#IDuser").css("border","1px solid red");
        			forminvalid++;
        		}
        		else
        		{
        			$("#IDuser").css("border","1px solid #cccccc");
        		}

        		if($("#IDitem").val() == "")
        		{
        			$("#IDitem").css("border","1px solid red");
        			forminvalid++;
        		}
        		else
        		{
        			$("#IDitem").css("border","1px solid #cccccc");
        		}

        		if(!forminvalid)
        		{
        			$("#fmEdit").submit();
        		}
        	}
        	</script>







        <script>


        // Si on clique sur le bouton matières
        function showMat()
        {
          hideEverything();
          desactiveBtn();
          $("#matiereBtn").addClass("active");

          $("#matRow").show();
          $("#roomRow").show();
          if ($("#IDpma").val() > 0)
          {
            // Si un PMA est sélectionné, on affiche la ligne pour forcer une classe
            $("#prom_force_tr").show();
            getClassForceSelectValuePMA(<?php echo $IDxx; ?>);

            $("#teacherRow_0").show();
            if ($("#IDuser_1").val() > 0)
            {
              $("#teacherRow_1").show();
            }
          }
          checkIfRoomBusy();
          checkIfClassBusy();

        }

        // Si on clique sur le bouton UV
        function showUV()
        {
          hideEverything();
          desactiveBtn();
          $("#uvBtn").addClass("active");
          $("#roomRow").show();
          $("#uvRow").show();
          if ($("#IDuv").val() > 0) {
            $("#prom_force_tr").show();  // Si un UV est sélectionné, on affiche la ligne pour forcer une classe
            getClassForceSelectValueUV(<?php echo $IDxx; ?>);
          }
          checkIfRoomBusy();
          checkIfClassBusy();

        }

        // Si on clique sur le bouton Agenda
        function showAgenda()
        {
          hideEverything();
          desactiveBtn();
          $("#agendaBtn").addClass("active");
          $("#roomRow").show();
          $("#classRow").show();

          $("#IDpma").val(0);

          $("#IDuv").val(0);

          checkIfRoomBusy();
          checkIfClassBusy();


          // On affiche la liste des profs avec les profs du syllabus lié affichés différement
          $("#teacherRow_0").show();
          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getProfSelect',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'numOfSelect=0',
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html, statut){ // code_html contient le HTML renvoyé
              $("#IDuser_0").html(code_html);
            }
          });
          $("#agendaIndisponibleRow").show();


          <?php
            if ($IDuserRmpl != 0) echo "$(\"#teacherRow_1\").show();";
          ?>

          checkIfIntervenantBusy(0);
          checkIfIntervenantBusy(1);
        }

        // Fonction qui retire la classe active sur tous les boutons
        function desactiveBtn()
        {
          $("#matiereBtn").removeClass("active");
          $("#uvBtn").removeClass("active");
          $("#agendaBtn").removeClass("active");
        }

        // Fonction qui cache tous les champs qui sont cachés à un moment ou un autre
        function hideEverything()
        {
          $("#matRow").hide();
          $("#roomRow").hide();
          $("#uvRow").hide();
          $("#teacherRow_0").hide();
          $(".teacherRow").hide();
          $("#agendaRow").hide();
          $("#agendaIndisponibleRow").hide();
          $("#classRow").hide();
          // Ligne pour forcer une classe
          $("#prom_force_tr").hide();
          $("#IDclassForced").val(0);
        }





        // Quand on sélectionne une liaison pma
        $("#IDpma").change(function(){
          var PMAID = this.value;
          loadPMAData(PMAID);
        });

        // Lorsque l'on change l'ID de l'UV, on charge les différents éléments
        $("#IDuv").change(function(){
          var UVID = this.value;
          loadUVData(UVID);
        });





        // Fonction qui charge les données de la liaison PMA
        function loadPMAData(PMAID) {
          // On change la valeur du champ caché IDmat
          $.ajax({
        		url : 'include/fonction/ajax/update_pma.php?action=getMatIDByPMAID',
        		type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'IDPMA=' + PMAID,
        		dataType : 'html', // On désire recevoir du HTML
        		success : function(code_html, statut){ // code_html contient le HTML renvoyé

              $("#IDmat").attr("value", code_html);
        		}
        	});

          // On change la valeur du champ caché IDclass_0
          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getClassIDByPMAID',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'IDPMA=' + PMAID,
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html, statut){ // code_html contient le HTML renvoyé

              $("#IDclass_0").attr("value", code_html);
            }
          });

          // On affiche la liste des profs avec les profs du syllabus lié affichés différement
          $("#teacherRow_0").show();
          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getProfSelectByPMAID',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'IDPMA=' + PMAID + "&IDprof=<?php echo substr($_GET['IDclass'], 1, -2); ?>",
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html, statut){ // code_html contient le HTML renvoyé
              $("#IDuser_0").html(code_html);
              checkIfIntervenantBusy("0");

            }
          });

          // On affiche la liste des profs avec les profs du syllabus lié affichés différement
          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getProfSelectByPMAID',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'IDPMA=' + PMAID + "&IDprof=<?php echo substr($_GET['IDclass'], 1, -2); ?>",
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html, statut){ // code_html contient le HTML renvoyé
              $("#IDuser_1").html(code_html);
              checkIfIntervenantBusy("1");

            }
          });


        }


        // Fonction qui charge les données de l'UV
        function loadUVData(UVID) {
          // On récupère l'ID du PMA
          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getPMAIDByUVID',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'IDUV=' + UVID,
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html_1, statut){ // code_html contient le HTML renvoyé
              // On affiche la liste des profs avec les profs du syllabus lié affichés différement
              $("#teacherRow_0").show();
              $.ajax({
                url : 'include/fonction/ajax/update_pma.php?action=getProfSelectByPMAID',
                type : 'POST', // Le type de la requête HTTP, ici devenu POST
                data : 'IDPMA=' + code_html_1,
                dataType : 'html', // On désire recevoir du HTML
                // async : false,
                success : function(code_html_2, statut){ // code_html contient le HTML renvoyé
                  // $("#teacherBox").html(code_html);
                  $("#IDuser_0").html(code_html_2);

                }
              });

              // On récupère l'ID de la matière
              $.ajax({
            		url : 'include/fonction/ajax/update_pma.php?action=getMatIDByPMAID',
            		type : 'POST', // Le type de la requête HTTP, ici devenu POST
                data : 'IDPMA=' + code_html_1,
            		dataType : 'html', // On désire recevoir du HTML
            		success : function(code_html_3, statut){ // code_html contient le HTML renvoyé
                  $("#IDmat").attr("value", code_html_3);
            		}
            	});

              // On récupère l'ID de la classe
              $.ajax({
                url : 'include/fonction/ajax/update_pma.php?action=getClassIDByPMAID',
                type : 'POST', // Le type de la requête HTTP, ici devenu POST
                data : 'IDPMA=' + code_html_1,
                dataType : 'html', // On désire recevoir du HTML
                success : function(code_html_4, statut){ // code_html contient le HTML renvoyé
                  $("#IDclass_0").attr("value", code_html_4);
                }
              });

            }
          });
        }






        // Fonction qui charge la liste des profs avec le prof déjà séléctionné
        function loadTeacherByPMAIDWithSelected(PMAID) {

          var ID_event = '<?php echo $IDxx; ?>';

          // On affiche la liste des profs avec les profs du syllabus lié affichés différement
          $("#teacherRow_0").show();
          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getProfSelectByPMAIDWithSelect',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'IDPMA=' + PMAID + '&ID_event=' + ID_event + '&numOfSelect=0',
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html, statut){ // code_html contient le HTML renvoyé
              // $("#teacherBox").html(code_html);
              $("#IDuser_0").html(code_html);
              checkIfIntervenantBusy("0");


            }
          });

          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getProfSelectByPMAIDWithSelect',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : 'IDPMA=' + PMAID + '&ID_event=' + ID_event + '&numOfSelect=1',
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html, statut){ // code_html contient le HTML renvoyé

              // $("#teacherBox").html(code_html);
              $("#IDuser_1").html(code_html);
              checkIfIntervenantBusy("1");


            }
          });


        }


        // Si on change le type d'agenda (Agenda/Indisponible)
        $("#typeAgenda").change(function(){
          var typeOfEvent = $('option:selected', this).attr("typeOfEvent");

          if (this.value != '0') {
            $("#IDmat").attr("value", this.value);
          }
          if (typeOfEvent == 3)
          {
            $("#agendaRow").show();
          }
          else
          {
            $("#agendaRow").hide();
          }
          $("#IDclass_0").attr("value", 0);
        });





        // Fonction qui vérifie et affiche si l'intervenant à déjà quelque chose sur ce créneau horraire
        function checkIfIntervenantBusy(numOfRow) {
          var date = $("#stpartdate").val();
          var heure_debut = $("#stparttime").val();
          var heure_fin = $("#etparttime").val();
          var ID_prof = $("#IDuser_" + numOfRow).val();
          var ID_event = '<?php echo $IDxx; ?>';

          if (ID_prof > 0)
          {
            $.ajax({
              url : 'include/fonction/ajax/update_pma.php?action=checkIfProfBusy',
              type : 'POST', // Le type de la requête HTTP, ici devenu POST
              data : 'date=' + date + '&heure_debut=' + heure_debut + '&heure_fin=' + heure_fin + '&ID_prof=' + ID_prof + '&ID_event=' + ID_event,
              dataType : 'html', // On désire recevoir du HTML
              success : function(code_html, statut){ // code_html contient le HTML renvoyé
                if (code_html.indexOf("success") >= 0) {
                  $("#profDispo_" + numOfRow).html("<span class=\"alert alert-success\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i> Intervenant disponible</span>");
                }
                else
                {
                  $("#profDispo_" + numOfRow).html("<span class=\"alert alert-danger\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i> Intervenant indisponible</span>");
                }
              }
            });
          }

          checkIfClassBusy();

        }

        // Fonction qui vérifie et affiche si la salle est déjà occupée sur ce créneau horraire
        function checkIfRoomBusy() {
          var date = $("#stpartdate").val();
          var heure_debut = $("#stparttime").val();
          var heure_fin = $("#etparttime").val();
          var ID_room = $("#IDitem").val();

          var ID_event = '<?php echo $IDxx; ?>';
          if (ID_room > 0)
          {
            $.ajax({
              url : 'include/fonction/ajax/update_pma.php?action=checkIfRoomBusy',
              type : 'POST', // Le type de la requête HTTP, ici devenu POST
              data : 'date=' + date + '&heure_debut=' + heure_debut + '&heure_fin=' + heure_fin + '&ID_room=' + ID_room + '&ID_event=' + ID_event,
              dataType : 'html', // On désire recevoir du HTML
              success : function(code_html, statut){ // code_html contient le HTML renvoyé
                if (code_html.indexOf("success") >= 0) {
                  $("#roomDispo").html("<span class=\"alert alert-success\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i> Salle disponible</span>");
                }
                else
                {
                  $("#roomDispo").html("<span class=\"alert alert-danger\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i> Salle indisponible</span>");
                }
              }
            });
          }
          else
          {
            // $("#roomDispo").html("<span class=\"alert alert-success\" style=\"opacity: 0;\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i> Salle disponible</span>");
          }

        }




        // ---------------------------------------------------------------------------
        // Fonction: Vérifie si la classe est déjà occupée sur le créneau horraire
        // IN:		   -
        // OUT: 		 Affichage UI
        // ---------------------------------------------------------------------------
        function checkIfClassBusy() {
          var date = $("#stpartdate").val();
          var heure_debut = $("#stparttime").val();
          var heure_fin = $("#etparttime").val();
          var ID_pma = $("#IDpma").val();
          var ID_event = '<?php echo $IDxx; ?>';
          if (ID_pma > 0)
          {
            $.ajax({
              url : 'include/fonction/ajax/update_pma.php?action=checkIfClassBusy',
              type : 'POST', // Le type de la requête HTTP, ici devenu POST
              data : 'date=' + date + '&heure_debut=' + heure_debut + '&heure_fin=' + heure_fin + '&IDPMA=' + ID_pma + '&ID_event=' + ID_event,
              dataType : 'html', // On désire recevoir du HTML
              success : function(code_html, statut){ // code_html contient le HTML renvoyé
                if (code_html.indexOf("success") >= 0) {
                  $("#classDispo").html("<span class=\"alert alert-success\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i> Classe disponible</span>");
                }
                else
                {
                  $("#classDispo").html("<span class=\"alert alert-danger\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i> Classe indisponible</span>");
                }
              }
            });
          }
        }


        // ---------------------------------------------------------------------------
        // Fonction: Vérifie si la promotion est déjà occupé sur le créneau horraire
        // IN:		   Numéro du select dans lequel récupérer l'ID de la promo
        // OUT: 		 Affichage UI
        // ---------------------------------------------------------------------------
        function checkIfPromoBusy(numClass) {
          var date = $("#stpartdate").val();
          var heure_debut = $("#stparttime").val();
          var heure_fin = $("#etparttime").val();
          var ID_promo = $("#IDclass_" + numClass).val();
          var ID_event = '<?php echo $IDxx; ?>';
          if (ID_promo > 0)
          {
            $.ajax({
              url : 'include/fonction/ajax/update_pma.php?action=checkIfPromoBusy',
              type : 'POST', // Le type de la requête HTTP, ici devenu POST
              data : 'date=' + date + '&heure_debut=' + heure_debut + '&heure_fin=' + heure_fin + '&ID_promo=' + ID_promo + '&ID_event=' + ID_event,
              dataType : 'html', // On désire recevoir du HTML
              success : function(code_html, statut){ // code_html contient le HTML renvoyé
                if (code_html.indexOf("success") >= 0) {
                  $("#promoDispo_" + numClass).html("<span class=\"alert alert-success\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i> Classe disponible</span>");
                }
                else
                {
                  $("#promoDispo_" + numClass).html("<span class=\"alert alert-danger\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i> Classe indisponible</span>");
                }
              }
            });
          }
        }


        <?php
          $num_class = 1;
          foreach($string_class as $key => $val)
          {

            echo "checkIfPromoBusy(".$num_class.");";
            $num_class++;
          }
        ?>




        // Si on modifie un élément, on affiche la liste des profs
        if ('<?php echo $IDxx; ?>') {
          $("#teacherRow_0").show();
        }



        // Lorsque l'on change la date ou l'heure, on refais toutes les vérifications
        $("#stpartdate").change(function(){   // Pour la date
          checkIfRoomBusy();
          checkIfClassBusy();
          checkIfIntervenantBusy("0");
          if ($("#teacherRow_1").css("display") != "none")
          {
            checkIfIntervenantBusy("1");
          }

        });


        // Lorsque l'on change l'horraire:
        function onTimeChange() {
          checkIfRoomBusy();
          checkIfClassBusy();
          checkIfIntervenantBusy("0");
          if ($("#teacherRow_1").css("display") != "none")
          {
            checkIfIntervenantBusy("1");
          }
        }


        $("#IDitem").change(function(){   // Pour la salle
          checkIfRoomBusy();
          checkIfClassBusy();
          checkIfIntervenantBusy("0");
          if ($("#teacherRow_1").css("display") != "none")
          {
            checkIfIntervenantBusy("1");
          }
        });

        // Quand on sélectionne une tranche horraire dans le select de droite
        $("#timeSelect").change(function(){
          checkIfRoomBusy();
          checkIfClassBusy();
          checkIfIntervenantBusy("0");
          if ($("#teacherRow_1").css("display") != "none")
          {
            checkIfIntervenantBusy("1");
          }
        })



        // Au chargement de la fenêtre:
          // On vérifie que la salle soit libre
          checkIfRoomBusy();

          // On vérifie que la classe soit libre:
          checkIfClassBusy();


          // On vérifie que l'intervenant soit libre
          checkIfIntervenantBusy("0");
          if ($("#teacherRow_1").css("display") != "none")
          {
            checkIfIntervenantBusy("1");
          }


        // Si on es connecté en tant que prof, alors on ne peut que mettre sur l'edt des indisponibilités
        if (<?php echo $_SESSION['CnxAdm']; ?> != 255 && <?php echo $_SESSION['CnxGrp']; ?> != 4)
        {
          hideEverything();
          $("#workflow").hide();
          $("#matiereBtn").hide();
          $("#uvBtn").hide();
          $("#agendaBtn").hide();
          $("hr").hide();
          $("#roomRow").hide();
          $("#agendaIndisponibleRow").hide();
          $("#teacherRow_0").hide();
          $("#teacherRow_1").hide();
          $("#tellTeacherAndStudents").hide();
          $("#tellTeacher").hide();
          $("#tellStudents").hide();

          $.ajax({
            url : 'include/fonction/ajax/update_pma.php?action=getProfSelect',
            type : 'POST', // Le type de la requête HTTP, ici devenu POST
            data : '',
            dataType : 'html', // On désire recevoir du HTML
            success : function(code_html, statut){ // code_html contient le HTML renvoyé
              $("#IDuser_0").html(code_html);
              $("#IDuser_0").val(<?php echo $_SESSION["CnxID"]; ?>);

            }
          });

          $("#IDuser_1").val(0);
          $("#typeAgenda").val(123);
          $("#IDitem").val(11);
          $("#IDmat").attr("value", 123);
          $("#IDclass_0").attr("value", 0);
          $("#indisponible").html("Indisponible");
          $("#IDuser_1").attr("value", 0);
        }


        $("#addATeacher").click(function(){
          addATeacher();
        });


        function addATeacher() {

          var numberOfCurrentTeacher = $(".teacherRow").length;


          $("#addATeacher").hide();
          $("#teacherRow_1").show();

          if ($("#agendaBtn").hasClass("active") || $('#uvBtn').hasClass('active'))
          {
            <?php if ((!isset($typeElem) || $typeElem == "") and (!isset($IDxx) || $IDxx == "")) { ?>
              // On affiche la liste des profs avec les profs du syllabus lié affichés différement
              $("#teacherRow_1").show();
              $.ajax({
                url : 'include/fonction/ajax/update_pma.php?action=getProfSelect',
                type : 'POST', // Le type de la requête HTTP, ici devenu POST
                data : 'numOfSelect=1',
                dataType : 'html', // On désire recevoir du HTML
                success : function(code_html, statut){ // code_html contient le HTML renvoyé
                  $("#IDuser_1").html(code_html);
                }
              });
            <?php } else { ?>



            <?php } ?>


          }

          else
          {
            $.ajax({
              url : 'include/fonction/ajax/update_pma.php?action=getProfSelectByPMAIDWithSelect',
              type : 'POST', // Le type de la requête HTTP, ici devenu POST
              data : 'IDPMA=' + PMAID + '&ID_event=' + ID_event + '&numOfSelect=1',
              dataType : 'html', // On désire recevoir du HTML
              success : function(code_html, statut){ // code_html contient le HTML renvoyé

                // $("#teacherBox").html(code_html);
                $("#IDuser_1").html(code_html);
                checkIfIntervenantBusy("1");


              }
            });
          }




        }


        // Si on modif un élément:
        <?php
          // Si on modifie un élément et que c'est un UV
          if (isset($typeElem) && $typeElem == "uv")
          {
            // On affiche l'onglet UV
            echo "showUV();";
            // On charge les différents éléments
            // echo "loadUVData($(\"#IDuv\").val(););";
            echo
            "
              var IDUV = $(\"#IDuv\").val();
              loadTeacherByPMAIDWithSelected(IDUV);
              checkIfIntervenantBusy(\"0\");
              checkIfClassBusy();
            ";
          }
        ?>
        // Si on modifie un élément et que c'est un agenda indisponible
        <?php if (isset($typeElem) && $typeElem == "agenda") echo "showAgenda();"; ?>
        <?php
          // Si on modifie un élément et que c'est un agenda et non indisponible
          if (getMatTypeByMatID($IDmat) == 3)
          {
            echo "$(\"#agendaRow\").show();";
          }

          // Si on modifie un élément et que c'est une matière normale
          if ((isset($typeElem) && $typeElem != "") || (isset($IDxx) && $IDxx != ""))
          {
            echo
            "
              var IDPMA = $(\"#IDpma\").val();
              loadTeacherByPMAIDWithSelected(IDPMA);
              checkIfIntervenantBusy(\"0\");
              checkIfClassBusy();
            ";
          }
        ?>
        <?php
          if ($IDuserRmpl != 0) echo "addATeacher();";
        ?>

        if (<?php echo $_SESSION['CnxAdm']; ?> != 255 && <?php echo $_SESSION['CnxGrp']; ?> != 4)
        {
          $("#teacherRow_0").hide();
          $("#teacherRow_1").hide();
        }

        <?php //echo "alert(\"".$typeElem." - ".$IDxx."\");"; ?>
        </script>




          </div>





          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button class="btn btn-success" type="button" name="send" onclick="VerifForm();"><?php echo $msg->read($EDT_INPUTOK); ?></button>
          </div>
        </div>
      </div>
    </div>

  </form>
</body>
</html>

<script>
// Une fois fermé, on supprime le modal
$(document).ready(function(){
  $('#editNewEventModal').on('hidden.bs.modal', function(){
    $('#editNewEventModal').remove();
    $('#showEditNewEventModal').remove();
    $('.modal-backdrop').remove();
    $('#modalEventModifHtml').remove();
    // parent.$("#gridcontainer").reload();
    // $('#showreflashbtn').click();
  });
});
</script>



<!-- Theme -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


<!-- Datepicker - https://www.daterangepicker.com -->
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="js/daterangepicker/locale-<?php echo $_SESSION['lang']; ?>.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />

<!-- Timepicker - https://github.com/jonthornton/jquery-timepicker -->
<script type="text/javascript" src="js/jquery.timepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.timepicker.min.css" />

<!-- Bootstrap select - https://developer.snapappointments.com/bootstrap-select/ -->
<script type="text/javascript" src="js/bootstrap-select/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-select.min.css" />


<script>
$(document).ready(function(){
  $('.selectpicker').selectpicker('render');
})


</script>
<style>
.dropdown-toggle {
  width: 100% !important;
}
.bootstrap-select {
  width: 100% !important;
}
#add_class:hover {
  cursor: pointer;
}

.alert {
  display: inline-block;
  height: 38px;
  margin-bottom: 0px;
  padding: 7px 9px;
}

.form_input_row {
  margin-bottom: 10px;
}
</style>
