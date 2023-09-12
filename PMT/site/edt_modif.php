<?php
session_start();
if($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4)
{
	class DBConnection{
		function getConnection(){
		include("config.php");
		  //change to your database server/user name/password
			mysql_connect($SERVER, $USER, $PASSWD) or
			 die("Could not connect: " . mysqli_error($mysql_link));
		//change to your database name
			mysqli_select_db($DATABASE) or 
				 die("Could not select database: " . mysqli_error($mysql_link));
		}
	}
	include_once("php/functions.php");
	require "msg/edt.php";
	require_once "include/TMessage.php";
	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/edt.php");
	
	$showdate = php2MySqlTime(js2PhpTime($_GET["showdate"]));
	$showdate = substr($showdate, 0, strpos($showdate, " "));
	
	$txt_fullabs = ";";
	$libre = $msg->read($EDT_LIBRE);

	function addDetailedCalendar($st, $et, $sub, $ade, $dscr, $loc, $color, $tz){
	  $ret = array();
	  try{
		$db = new DBConnection();
		$db->getConnection();
		$sql = "insert into `jqcalendar` (`subject`, `starttime`, `endtime`, `isalldayevent`, `description`, `location`, `color`) values ('"
		  .mysqli_real_escape_string($sub)."', '"
		  .php2MySqlTime(js2PhpTime($st))."', '"
		  .php2MySqlTime(js2PhpTime($et))."', '"
		  .mysqli_real_escape_string($ade)."', '"
		  .mysqli_real_escape_string($dscr)."', '"
		  .mysqli_real_escape_string($loc)."', '"
		  .mysqli_real_escape_string($color)."' )";
		//echo($sql);
			if(mysqli_query($mysql_link, $sql)==false){
		  $ret['IsSuccess'] = false;
		  $ret['Msg'] = mysqli_error($mysql_link);
		}else{
		  $ret['IsSuccess'] = true;
		  $ret['Msg'] = 'add success';
		  $ret['Data'] = mysqli_insert_id($mysql_link);
		}
		}catch(Exception $e){
		 $ret['IsSuccess'] = false;
		 $ret['Msg'] = $e->getMessage();
	  }
	  return $ret;
	}

	function listCalendarByRange($sd, $ed, $IDcentre){
		$generique = "modif";
		global $txt_fullabs;
		global $libre;
		
		if($generique == "modif")
		{
			/********** Tableau semaine MODIFICATION **********/

			$ret1 = array();
			
			if($generique != "on")
			{
				try
				{
					$db = new DBConnection();
					$db->getConnection();
					

					$dd = intval(date("w", $sd))-1;
					
					// les classes
					$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', mat._code '_code', ";
					$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDitem '_IDitem', edt._IDrmpl '_IDrmpl', edt._etat '_etat', edt._IDclass '_IDclass', edt._attribut '_attribut' ";
					$sql .= "from edt_data edt, campus_data mat ";
					$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
					$sql .= "and edt._IDcentre = $IDcentre ";
					$sql .= "and edt._jour = '".$dd."' AND edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' and (edt._etat = '3' OR edt._etat = '2') ORDER BY edt._IDclass ASC, edt._jour ASC, edt._debut ASC, edt._etat ASC";

					// echo $sql;
					// echo php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."'";
					$handle = mysqli_query($mysql_link, $sql);
					$date_debut = php2MySqlTime($sd);

					while ($row = mysqli_fetch_object($handle))
					{
						// Traitements convertion date time
						$date_debut = php2MySqlTime($sd);
						$date_event = date($date_debut); // objet date
						$date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row->_jour." day"); // ajout du nb de jours
						$date_event = date("Y-m-d", $date_event); // timesptamp en string
						
						// Heure de dÃ©but
						$heure_debut_event = $row->_debut;	
						
						// Heure de fin					
						$heure_fin_event = $row->_fin;
						
						// Verification si quelque chose Ã  faire
						$nbdev = 0;
						$textdev = "";
						if($generique != "on")
						{
							$sql2 = "select * from ctn_items where _type = 0 and _IDcours = ".$row->_IDx;
							$handle2 = mysqli_query($mysql_link, $sql2);
							while ($row2 = mysqli_fetch_object($handle2))
							{
								if($row2->_devoirs != "")
								{
									$textdev = $row2->_devoirs;
									$nbdev++;
								}
							}
						}

						// Si remplacant
						if($row->_IDrmpl != 0 && $row->_IDrmpl != 10000)
						{
							$IDrmpl = $row->_IDrmpl;
						}
						else
						{
							$IDrmpl = $row->_ID;
						}
						
						// Nom de l'enseignant
						$queryuser  = "select _name from user_id ";
						$queryuser .= "where _ID = ".$IDrmpl;
						
						$resultuser = mysqli_query($mysql_link, $queryuser);
						$rowsuser    = ( $resultuser ) ? mysqli_fetch_row($resultuser) : 0 ;
											
						// Remplacement
						if($row->_etat == 3 && $row->_IDrmpl != $row->_ID && $row->_IDrmpl != 10000)
						{
							$txt_fullabs .= $row->_ID.";";
						}

						// Suppression
						if($row->_etat == 2 && $row->_IDrmpl == 10000)
						{
							$txt_fullabs .= $row->_ID.";";
						}
						
						if($row->_etat == 2)
						{
							$titre_cours = "<strong>".$libre."</strong>";
						}
						else
						{
							$titre_cours = "<b>".htmlentities($row->_code, ENT_NOQUOTES, "iso-8859-1")."</b> ".htmlentities(strtoupper($rowsuser[0]), ENT_NOQUOTES, "iso-8859-1");
						}
						
						// Traitement numÃ©ro horaire  
						if($heure_debut_event == "07:50:00" && $heure_fin_event == "08:35:00")
						{
							$horaire = 1;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "08:35:00" && $heure_fin_event == "09:20:00")
						{
							$horaire = 2;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "09:40:00" && $heure_fin_event == "10:25:00")
						{
							$horaire = 3;
							$typehoraire = 1;
						}					
						elseif($heure_debut_event == "10:25:00" && $heure_fin_event == "11:10:00")
						{
							$horaire = 4;
							$typehoraire = 1;
						}					
						elseif($heure_debut_event == "11:30:00" && $heure_fin_event == "12:15:00")
						{
							$horaire = 5;
							$typehoraire = 1;
						}					
						elseif($heure_debut_event == "12:15:00" && $heure_fin_event == "13:00:00")
						{
							$horaire = 6;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "13:00:00" && $heure_fin_event == "13:50:00")
						{
							$horaire = 7;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "13:50:00" && $heure_fin_event == "14:35:00")
						{
							$horaire = 8;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "14:35:00" && $heure_fin_event == "15:20:00")
						{
							$horaire = 9;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "15:25:00" && $heure_fin_event == "16:10:00")
						{
							$horaire = 10;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "16:15:00" && $heure_fin_event == "17:00:00")
						{
							$horaire = 11;
							$typehoraire = 1;
						}
						elseif($heure_debut_event == "17:00:00" && $heure_fin_event == "17:45:00")
						{
							$horaire = 12;
							$typehoraire = 1;
						}
						
						// Horaire double
						if($heure_debut_event == "07:50:00" && $heure_fin_event == "09:20:00")
						{
							$horaire = 1;
							$typehoraire = 2;
						}
						elseif($heure_debut_event == "09:40:00" && $heure_fin_event == "11:10:00")
						{
							$horaire = 3;
							$typehoraire = 2;
						}
						elseif($heure_debut_event == "11:30:00" && $heure_fin_event == "13:00:00")
						{
							$horaire = 5;
							$typehoraire = 2;
						}								
						elseif($heure_debut_event == "13:50:00" && $heure_fin_event == "15:20:00")
						{
							$horaire = 9;
							$typehoraire = 2;
						}					
						elseif($heure_debut_event == "16:15:00" && $heure_fin_event == "17:45:00")
						{
							$horaire = 11;
							$typehoraire = 2;
						}
						
						if($typehoraire == 1)
						{
							$ret1[$row->_IDclass][$horaire] = array(
								$row->_IDx,
								$titre_cours,
								php2JsTime(mySql2PhpTime($date_event." ".$heure_debut_event)),
								php2JsTime(mySql2PhpTime($date_event." ".$heure_fin_event)),
								$row->_ID,
								$row->_IDrmpl,
								$row->_IDclass,
								$row->_etat,
								$row->_attribut,
								""
							);
						}
						elseif($typehoraire == 2)
						{
							$ret1[$row->_IDclass][$horaire] = array(
								$row->_IDx,
								$titre_cours,
								php2JsTime(mySql2PhpTime($date_event." ".$heure_debut_event)),
								php2JsTime(mySql2PhpTime($date_event." ".$heure_fin_event)),
								$row->_ID,
								$row->_IDrmpl,
								$row->_IDclass,
								$row->_etat,
								$row->_attribut,
								""
							);
							
							$ret1[$row->_IDclass][$horaire+1] = array(
								$row->_IDx,
								$titre_cours,
								php2JsTime(mySql2PhpTime($date_event." ".$heure_debut_event)),
								php2JsTime(mySql2PhpTime($date_event." ".$heure_fin_event)),
								$row->_ID,
								$row->_IDrmpl,
								$row->_IDclass,
								$row->_etat,
								$row->_attribut,
								""
							);
						}
					}
				}
				catch(Exception $e)
				{
					$ret1['error'] = $e->getMessage();
				}
			}
		}
		
		$ret2 = array();
		// Completer les cases vides
		foreach($ret1 as $keyclass => $valclass)
		{
			for($i=1; $i <= 12; $i++)
			{
				if(!isset($valclass[$i]))
				{
					$ret1[$keyclass][$i] = array(
						"",
						"",
						"",
						"",
						"",
						"",
						"",
						"",
						"",
						""
					);
				}
			}
			
			$tab_sort = $ret1[$keyclass];
			ksort($tab_sort);
			$ret2[$keyclass] = $tab_sort;
		}

		$ret1 = $ret2;
		
		// VÃ©rifier l'absence professeur de groupe
		foreach($ret1 as $keyclass => $valclass)
		{
			foreach($valclass as $keyevent => $valevent)
			{
				if($valevent[7] == 2)
				{
					foreach($valclass as $keyevent2 => $valevent2)
					{
						// if($valevent[0] != $valevent2[0] && $valevent[4] == $valevent2[4] && $valevent2[5] != 0 && $valevent2[5] != 10000 && $valevent2[4] != $valevent2[5])
						// {
							// Nom de l'enseignant
							$queryuser  = "select _name from user_id ";
							$queryuser .= "where _ID = ".$valevent[4];
							
							$resultuser = mysqli_query($mysql_link, $queryuser);
							$rowsuser    = ( $resultuser ) ? mysqli_fetch_row($resultuser) : 0 ;
							
							$ret1[$keyclass][$keyevent][9] = $valevent[4];
						// }
					}
				}
			}
		}
		
		return $ret1;
	}

	function listCalendar($day, $type, $IDcentre){
		$phpTime = js2PhpTime($day);
		$st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
		$et = $st;
		return listCalendarByRange($st, $et, $IDcentre);
	}

	function getClassAbs($date)
	{
		$query  = "SELECT _texte ";
		$query .= "FROM edt_modif ";
		$query .= "WHERE _zone = 'absclasse' ";
		$query .= "AND _date = '$date' ";
		$query .= "LIMIT 1 ";

		$result = mysqli_query($mysql_link, $query);
		$rows    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
		$texte = "";

		if($rows) // si donnÃ©e existantes
		{
			$texte = $rows[0];
		}

		return $texte;
	}
	
	function getZoneAbs($date, $IDclass)
	{
		$query  = "SELECT _texte ";
		$query .= "FROM edt_modif ";
		$query .= "WHERE _zone = 'zone' ";
		$query .= "AND _IDclass = '$IDclass' ";
		$query .= "AND _date = '$date' ";
		$query .= "LIMIT 1 ";

		$result = mysqli_query($mysql_link, $query);
		$rows    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
		$texte = "";

		if($rows) // si donnÃ©e existantes
		{
			$texte = $rows[0];
		}

		return $texte;
	}
	
	// header('Content-type:text/javascript;charset=UTF-8');
	$ret = listCalendar($_GET["showdate"], "day", 1);
	$phpTime = js2PhpTime($day);
	// $st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));

	// print_r($ret);
	
	?>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery.js"></script>
			<script type="text/javascript" src="<?php echo $_SESSION["ROOTDIR"]; ?>/script/jquery.color.js"></script>
			
			<script>
			$( document ).ready(function() {
				$("#absclasse").dblclick(function() {
					
					// GÃ©nÃ©ration texte
					var zone = $("#absclasse").html();
					$("#absclasse").html("<textarea id=\"texteclass\">" + zone + "</textarea>");
					$("#texteclass").focus();
					
					// event touche validation
					$('#texteclass').keyup(function(e) {    
						if(e.keyCode == 13) {
							$.ajax({
								method: "GET",
								url: "php/edt_modif_setclassabs.php?date=<?php echo $showdate; ?>&texte=" + $("#texteclass").val()
							})
							.done(function( msg ) {
								$("#absclasse").html($("#texteclass").val());
							});
						}
					});
					$('#texteclass').focusout(function(e) {    
						$.ajax({
							method: "GET",
							url: "php/edt_modif_setclassabs.php?date=<?php echo $showdate; ?>&texte=" + $("#texteclass").val()
						})
						.done(function( msg ) {
							$("#absclasse").html($("#texteclass").val());
						});
					});
				});
				
				// Zone
				$(".zone").each(function() {
					$(this).dblclick(function() {
						
						// GÃ©nÃ©ration texte
						var zone = $(this).html();
						var ident = $(this);
						$(this).html("<textarea id=\"texteclass_"+ $(this).attr("attr") +"\">" + zone + "</textarea>");
						$("#texteclass_"+ $(this).attr("attr")).focus();
						
						//event touche validation
						$("#texteclass_"+ ident.attr("attr")).keyup(function(e) {    
							if(e.keyCode == 13) {
								$.ajax({
									method: "GET",
									url: "php/edt_modif_setzoneabs.php?date=<?php echo $showdate; ?>&texte=" + $("#texteclass_"+ ident.attr("attr")).val() + "&IDclass=" + ident.attr("attr")
								})
								.done(function( msg ) {
									ident.html($("#texteclass_"+ ident.attr("attr")).val());
									ident.animate({ "backgroundColor": "#38D15E" }, "slow" );
									ident.animate({ "backgroundColor": "white" }, 3000 );
								});
							}
						});
						$("#texteclass_"+ ident.attr("attr")).focusout(function(e) {    
							$.ajax({
								method: "GET",
								url: "php/edt_modif_setzoneabs.php?date=<?php echo $showdate; ?>&texte=" + $("#texteclass_"+ ident.attr("attr")).val() + "&IDclass=" + ident.attr("attr")
							})
							.done(function( msg ) {
								ident.html($("#texteclass_"+ ident.attr("attr")).val());
								ident.animate({ "backgroundColor": "#38D15E" }, "slow" );
								ident.animate({ "backgroundColor": "white" }, 3000 );
							});
						});
					});
				});
			});
			</script>
			
			<style>
			
			body {
				font-family: 'Arial';
				margin: 10px;
			}
			
			table {
				background-color: transparent;
				border-collapse: collapse;
				border-spacing: 0;
				max-width: 100%;
			}
			
			.first {
				background-color: #363636;
				background-image: linear-gradient(to bottom, #444444, #222222);
				background-repeat: repeat-x;
				border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
				color: #ffffff;
				text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
				height: 30px;
				vertical-align: middle;
			}
			
			.absprof {
				background-color: #5bb75b;
				background-image: linear-gradient(to bottom, #62c462, #51a351);
				background-repeat: repeat-x;
				border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
				color: #ffffff;
				text-align: center;
				font-weight: bold;
				text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
				height: 30px;
				vertical-align: middle;
			}
			
			.absclasse {
				background-color: #006dcc;
				background-image: linear-gradient(to bottom, #0088cc, #0044cc);
				background-repeat: repeat-x;
				border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
				color: #ffffff;
				text-align: center;
				font-weight: bold;
				text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
				height: 30px;
				vertical-align: middle;
			}
			
			.absclasse textarea {
				width: 100%;
			}
			
			.tabletop {
				width: 100%;
				border: 1px solid #878787;
				text-align: center;
				font-weight: bold;
				font-size: 20px;
				border-collapse: collapse;
			}
			
			.tabletop td {
				border-top: 0px;
				border-bottom: 0px;
				border-left: 1px solid silver;
				border-right: 1px solid silver;
				vertical-align: top;
				padding: 0;
			}
			
			.tableclass td {
				border: none;
				height: 35px;
				vertical-align: middle;
				text-align: center;
			}
			
			.tableclass .simple {
				height: 35px;
				background-color: #f5f5f5;
				font-size: 14px;
			}
			
			.tableclass .double {
				height: 70px;
				background-color: #f5f5f5;
				font-size: 14px;
			}
			
			.tableclass {
				width: 100%;
				border-collapse: collapse;
			}
			
			.tableclass td {
				border-bottom: 1px solid #878787;
			}
			
			.tdkeyclass {
				background-color: #f5f5f5;
				background-image: linear-gradient(to bottom, #e6e6e6, silver);
				background-repeat: repeat-x;
				border-color: silver silver #b3b3b3;
				border-image: none;
				border-style: solid;
				border-width: 1px;
				box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
				color: #333333;
				font-weight: bold;
				font-size: 14px;
			}
			
			.tablenum {
				width: 100%;
				border-collapse: collapse;
			}
			
			.tablenum td {
				height: 35px;
				text-align: center;
				font-weight: bold;
				background-color: #f5f5f5;
				background-image: linear-gradient(to bottom, #e6e6e6, silver);
				background-repeat: repeat-x;
				border-color: silver silver #b3b3b3;
				border-image: none;
				border-style: solid;
				border-width: 0px;
				box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
				color: #333333;
				font-weight: bold;
			}
			
			.zone textarea {
				width: 100%;
			}
			</style>
		</head>
			<body>
				<table class="tabletop" style="border: 0px;">
					<tr>
						<td colspan="<?php echo count($ret)+1; ?>" class="first" style="border: 0px;"><?php print(utf8_encode($msg->read($EDT_EDTMODIF))); ?> <?php echo $_GET["showdate"]; ?></td>
					</tr>
					<tr>
						<td colspan="<?php echo count($ret)+1; ?>" style="border: 0px;">
							<table width="100%">
								<tr>
									<td width="60%" class="absprof" style="border: 0px;">
									<?php print($msg->read($EDT_ABSPROF)); ?>
									</td>
									<td width="40%" class="absclasse" style="border: 0px;">
									<?php print($msg->read($EDT_ABSCLASSE)); ?>
									</td>
								</tr>
								<tr>
									<td width="60%" class="absprof" style="border: 0px;">
										<?php
										$array_abs = @split(";", $txt_fullabs);
										$array_abs = array_unique($array_abs);
										$nom_absnom = "";
										
										foreach($array_abs as $val)
										{
											if($val != "")
											{
												// Nom de l'enseignant
												$queryuser  = "select _name from user_id ";
												$queryuser .= "where _ID = $val";
												
												$resultuser = mysqli_query($mysql_link, $queryuser);
												$rowsuser    = ( $resultuser ) ? mysqli_fetch_row($resultuser) : 0 ;
												
												if($rowsuser[0] != "")
												{
													$nom_absnom .= htmlentities(strtoupper($rowsuser[0]), ENT_NOQUOTES, "iso-8859-1").", ";
												}
											}
										}
										echo substr($nom_absnom, 0, strlen($nom_absnom)-2);
										?>
									</td>
									<td width="40%" class="absclasse" id="absclasse" style="border: 0px;"><?php echo getClassAbs($showdate); ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="<?php echo count($ret)+1; ?>" style="border: 0px; height: 15px;"></td>
					</tr>
				</table>
				
				<table class="tabletop" style="border: 0px;">
					<?php if(count($ret) > 0){ ?>
					<tr>
						<td style="width: 1%; min-width: 28px; max-width: 28px; border: 0px;">
							<table class="tablenum">
								<tr>
									<td><?php print($msg->read($EDT_CL)); ?></td>
								</tr>
								<tr>
									<td>Gr</td>
								</tr>
								<tr>
									<td>1</td>
								</tr>
								<tr>
									<td>2</td>
								</tr>
								<tr>
									<td>3</td>
								</tr>
								<tr>
									<td>4</td>
								</tr>
								<tr>
									<td>5</td>
								</tr>
								<tr>
									<td>6</td>
								</tr>
								<tr>
									<td>7</td>
								</tr>
								<tr>
									<td>8</td>
								</tr>
								<tr>
									<td>9</td>
								</tr>
								<tr>
									<td>10</td>
								</tr>
								<tr>
									<td>11</td>
								</tr>
								<tr>
									<td>12</td>
								</tr>
								<tr>
									<td></td>
								</tr>
							</table>
						</td>
						<?php
						foreach($ret as $keyclass => $valclass)
						{
							echo "<td width='". 100/count($ret) ."%'>";
							echo "<table class=\"tableclass\">";
							echo "<tr>";
							echo "<td class=\"tdkeyclass\">";
							$array_class = @split(";", $keyclass);
							$nom_class = "";
							
							foreach($array_class as $val)
							{
								if($val != "")
								{
									// Nom de la classe
									$queryclass  = "select _ident from campus_classe ";
									$queryclass .= "where _IDclass = ".$val;
									
									$resultclass = mysqli_query($mysql_link, $queryclass);
									$rowsclass    = ( $resultclass ) ? mysqli_fetch_row($resultclass) : 0 ;
									
									if($rowsclass[0] != "")
									{
										$nom_class .= htmlentities($rowsclass[0], ENT_NOQUOTES, "iso-8859-1")."/";
									}
								}
							}
							echo substr($nom_class, 0, strlen($nom_class)-1);
							echo "</td></tr>";
							echo "<td class=\"tdkeyclass\">";
							// Recherche si prof absent de groupe
							$nom_abs = ";";
							foreach($valclass as $keyevent => $valevent)
							{
								if($valevent[9] != "")
								{
									$nom_abs .= $valevent[9].";";
								}
							}
							$array_abs = @split(";", $nom_abs);
							$array_abs = array_unique($array_abs);
							$nom_absnom = "";
							
							foreach($array_abs as $val)
							{
								if($val != "")
								{
									// Nom de l'enseignant
									$queryuser  = "select _name from user_id ";
									$queryuser .= "where _ID = $val";
									
									$resultuser = mysqli_query($mysql_link, $queryuser);
									$rowsuser    = ( $resultuser ) ? mysqli_fetch_row($resultuser) : 0 ;
									
									if($rowsuser[0] != "")
									{
										$nom_absnom .= htmlentities(strtoupper($rowsuser[0]), ENT_NOQUOTES, "iso-8859-1").", ";
									}
								}
							}
							echo substr($nom_absnom, 0, strlen($nom_absnom)-2);
							echo "</td></tr>";
							foreach($valclass as $keyevent => $valevent)
							{
								// Salle
								$salle = "";
								if ($valevent[1] != "")
								{
									$querysalle  = "SELECT s._title FROM edt_data e, edt_items s ";
									$querysalle .= "WHERE e._IDitem = s._IDitem ";
									$querysalle .= "AND e._IDx = $valevent[0] ";
									
									$resultsalle = mysqli_query($mysql_link, $querysalle);
									$rowssalle    = ( $resultsalle ) ? mysqli_fetch_row($resultsalle) : 0 ;
									
									if($rowssalle[0] != "")
									{
										$salle = htmlentities($rowssalle[0], ENT_NOQUOTES, "iso-8859-1");
									}
									
									if(strpos($valevent[1], $libre) == true)
									{
										$salle = "";
									}
									else
									{
										$salle = " <i>$salle</i>";
									}
								}
							
								echo "<tr>";
								if($valevent[0] == $valclass[$keyevent+1][0] && $valclass[$keyevent+1][0] != "")
								{
									echo "<td class=\"double\">$valevent[1] $salle</td>";
								}
								elseif($valevent[0] == $valclass[$keyevent-1][0] && $valclass[$keyevent-1][0] != "")
								{
									// On affiche pas la deuxiÃ¨me heure double
								}
								elseif($valevent[0] != $valclass[$keyevent+1][0] && $valevent[0] != "")
								{
									echo "<td class=\"simple\">$valevent[1] $salle</td>";
								}
								else
								{
									echo "<td></td>";
								}
								echo "</tr>";
							}
							echo "<tr><td class=\"zone\" attr=\"".str_replace(";", "-", $keyclass)."\">".getZoneAbs($showdate, str_replace(";", "-", $keyclass))."</td></tr>";
							echo "</table></td>";
						}
						?>
					</tr>
						<td></td>
					</tr>
				</table>
				<?php } ?>
					
				<?php
				// Centre 2
				$ret = listCalendar($_GET["showdate"], "day", 2);
				?>
				<?php if(count($ret) > 0){ ?>
				<table class="tabletop" style="border: 0px;">
					<tr>
						<td colspan="<?php echo count($ret)+1; ?>" style="border: 0px; height: 15px;"></td>
					</tr>
					<tr>
						<td style="width: 1%; min-width: 28px; max-width: 28px; border: 0px;">
							<table class="tablenum">
								<tr>
									<td><?php print($msg->read($EDT_CL)); ?></td>
								</tr>
								<tr>
									<td>Gr</td>
								</tr>
								<tr>
									<td>1</td>
								</tr>
								<tr>
									<td>2</td>
								</tr>
								<tr>
									<td>3</td>
								</tr>
								<tr>
									<td>4</td>
								</tr>
								<tr>
									<td>5</td>
								</tr>
								<tr>
									<td>6</td>
								</tr>
								<tr>
									<td>7</td>
								</tr>
								<tr>
									<td>8</td>
								</tr>
								<tr>
									<td>9</td>
								</tr>
								<tr>
									<td>10</td>
								</tr>
								<tr>
									<td>11</td>
								</tr>
								<tr>
									<td>12</td>
								</tr>
								<tr>
									<td></td>
								</tr>
							</table>
						</td>
						<?php
						foreach($ret as $keyclass => $valclass)
						{
							echo "<td width='". 100/count($ret) ."%'>";
							echo "<table class=\"tableclass\">";
							echo "<tr>";
							echo "<td class=\"tdkeyclass\">";
							$array_class = @split(";", $keyclass);
							$nom_class = "";
							
							foreach($array_class as $val)
							{
								if($val != "")
								{
									// Nom de la classe
									$queryclass  = "select _ident from campus_classe ";
									$queryclass .= "where _IDclass = ".$val;
									
									$resultclass = mysqli_query($mysql_link, $queryclass);
									$rowsclass    = ( $resultclass ) ? mysqli_fetch_row($resultclass) : 0 ;
									
									if($rowsclass[0] != "")
									{
										$nom_class .= htmlentities($rowsclass[0], ENT_NOQUOTES, "iso-8859-1")."/";
									}
								}
							}
							echo substr($nom_class, 0, strlen($nom_class)-1);
							echo "</td></tr>";
							echo "<td class=\"tdkeyclass\">";
							// Recherche si prof absent de groupe
							$nom_abs = ";";
							foreach($valclass as $keyevent => $valevent)
							{
								if($valevent[9] != "")
								{
									$nom_abs .= $valevent[9].";";
								}
							}
							$array_abs = @split(";", $nom_abs);
							$array_abs = array_unique($array_abs);
							$nom_absnom = "";
							
							foreach($array_abs as $val)
							{
								if($val != "")
								{
									// Nom de l'enseignant
									$queryuser  = "select _name from user_id ";
									$queryuser .= "where _ID = $val";
									
									$resultuser = mysqli_query($mysql_link, $queryuser);
									$rowsuser    = ( $resultuser ) ? mysqli_fetch_row($resultuser) : 0 ;
									
									if($rowsuser[0] != "")
									{
										$nom_absnom .= htmlentities(strtoupper($rowsuser[0]), ENT_NOQUOTES, "iso-8859-1").", ";
									}
								}
							}
							echo substr($nom_absnom, 0, strlen($nom_absnom)-2);
							echo "</td></tr>";
							foreach($valclass as $keyevent => $valevent)
							{
								// Salle
								$salle = "";
								if ($valevent[1] != "")
								{
									$querysalle  = "SELECT s._title FROM edt_data e, edt_items s ";
									$querysalle .= "WHERE e._IDitem = s._IDitem ";
									$querysalle .= "AND e._IDx = $valevent[0] ";
									
									$resultsalle = mysqli_query($mysql_link, $querysalle);
									$rowssalle    = ( $resultsalle ) ? mysqli_fetch_row($resultsalle) : 0 ;
									
									if($rowssalle[0] != "")
									{
										$salle = htmlentities($rowssalle[0], ENT_NOQUOTES, "iso-8859-1");
									}
									
									if(strpos($valevent[1], $libre) == true)
									{
										$salle = "";
									}
									else
									{
										$salle = " <i>$salle</i>";
									}
								}
							
								echo "<tr>";
								if($valevent[0] == $valclass[$keyevent+1][0] && $valclass[$keyevent+1][0] != "")
								{
									echo "<td class=\"double\">$valevent[1] $salle</td>";
								}
								elseif($valevent[0] == $valclass[$keyevent-1][0] && $valclass[$keyevent-1][0] != "")
								{
									// On affiche pas la deuxiÃ¨me heure double
								}
								elseif($valevent[0] != $valclass[$keyevent+1][0] && $valevent[0] != "")
								{
									echo "<td class=\"simple\">$valevent[1] $salle</td>";
								}
								else
								{
									echo "<td></td>";
								}
								echo "</tr>";
							}
							echo "<tr><td class=\"zone\" attr=\"".str_replace(";", "-", $keyclass)."\">".getZoneAbs($showdate, str_replace(";", "-", $keyclass))."</td></tr>";
							echo "</table></td>";
						}
						?>
					</tr>
				</table>
				<?php } ?>
			</body>
	</html>
	<?php
}
?>