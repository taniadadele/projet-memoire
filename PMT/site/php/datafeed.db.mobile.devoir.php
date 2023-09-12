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
 *		module   : datafeed.db.mobile.devoir.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    : 
 */
?>

<?php
include_once("dbconfig.devoir.php");
include_once("functions.php");


function listCalendarByRange($sd, $ed){

	$IDcentre = ( @$_POST["IDcentre"] )					// Identifiant du centre
		? (int) $_POST["IDcentre"]
		: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
	$IDedt    = 3;
	$IDitem   = $_SESSION["CnxClass"];
	$IDclass  = $_SESSION["CnxClass"];
	$IDuser   = ( @$_POST["IDuser"] )					// Identifiant de l'utilisateur
		? (int) $_POST["IDuser"]
		: (int) @$_GET["IDuser"] ;
	$IDdata   = (int) @$_GET["IDdata"];					// Identifiant de l'edt
	$generique   = "off";
		
  /********** Tableau semaine normale **********/
  $ret = array();
  $ret['events'] = array();
  $ret["issort"] =true;
  $ret["start"] = php2JsTime($sd);
  $ret["end"] = php2JsTime($ed);
  $ret['error'] = null;
  try{
    $db = new DBConnection();
    $db->getConnection();
	
	switch ( $IDedt ) {
		case 1 :	// les salles
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._semaine '_semaine' ";
				$sql .= "from edt_data edt, campus_data mat, campus_classe classe ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' and edt._IDitem = '".$IDitem."' ";
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '0' and edt._etat = '0' order by _IDdata";
			break;
		case 2 :	// le personnel
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._semaine '_semaine' ";
				$sql .= "from edt_data edt, campus_data mat ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' and edt._ID = '".$IDuser."' ";
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '0' and edt._etat = '0' order by _IDdata";
			break;
		default :	// les classes
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._semaine '_semaine' ";
				$sql .= "from edt_data edt, campus_data mat ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' ";
				if($IDitem != "0")
				{
					$sql .= "and edt._IDclass LIKE '%;".$IDitem.";%' ";
				}
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '0' and edt._etat = '0' order by _IDdata";
			break;
		}
	

	// echo $sql;
	//.php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."'";
    $handle = mysqli_query($mysql_link, $sql);
	$date_debut = php2MySqlTime($sd);
	
	// semaine S1 / S2
	$query  = "select _semaines from config_centre ";
	$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' AND _IDcentre = $IDcentre ";
	$query .= "order by _IDcentre";

	$result = mysqli_query($mysql_link, $query);
	$rows    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
	
	$res = json_decode($rows[0]);
	$sem = $res[intval(date("W", $sd))];

    while ($row = mysqli_fetch_object($handle)) {
		if(intval($sem) == $row->_semaine || intval($row->_semaine) == 0 || $generique == "on") // verifie si semaine 1 ou 2 pour cours générique
		{
			// Traitements convertion date time
			$date_debut = php2MySqlTime($sd);
			$date_event = date($date_debut); // objet date
			$date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row->_jour." day"); // ajout du nb de jours
			$date_event = date("Y-m-d", $date_event); // timesptamp en string
			
			// Heure de début
			$heure_debut_event = intval($row->_heure);
			if($heure_debut_event > 0)
			{
				if(!($heure_debut_event % 2))
				{
					$heure_debut_event = 8 + ($heure_debut_event / 2).":00";
				}
				else
				{
					$heure_debut_event = 7 + (($heure_debut_event + 1) / 2).":30";
				}
			}
			else if($heure_debut_event == 0)
			{
				$heure_debut_event = "08:00";
			}
			else
			{
				if(!($heure_debut_event % 2))
				{
					$heure_debut_event = 8 - (-$heure_debut_event / 2).":00";
				}
				else
				{
					$heure_debut_event = 8 - ((-$heure_debut_event + 1) / 2).":30";
				}
			}
			
			
			// Heure de fin
			$temp1 = intval(substr($heure_debut_event, 0, strpos($heure_debut_event, ":")));
			$temp1 = $temp1 + intval(substr($row->_delais, 0, strpos($row->_delais, ":")));
			
			$temp2 = intval(substr($heure_debut_event, strpos($heure_debut_event, ":")+1));
			$temp3 = substr($row->_delais, strpos($row->_delais, ":")+1);
			$temp3 = intval(substr($temp3, 0, strpos($temp3, ":")));
			$temp2 = $temp2+$temp3;
			
			if($temp2 == 60)
			{
				$temp2 = 0;
				$temp1++;
			}
			
			$heure_fin_event = $temp1.":".$temp2;
			
			// Verification si quelque chose à faire
			$nbdev = 0;
			$textdev = "";
			if($generique == "off")
			{
				$sql2 = "select * from ctn_items where _type = 1 and _nosemaine = ".date("W", $sd)."  and _IDcours = ".$row->_IDx;
				$handle2 = mysqli_query($mysql_link, $sql2);
				while ($row2 = mysqli_fetch_object($handle2))
				{
					$nbdev++;
					$textdev = $row2->_texte;
				}
			}
			
			if($IDedt == 1)
			{
				$titre_cours = "<b>".$row->_titre."</b><br/>".strtr($row->_ident, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');;
			}
			else
			{
				$titre_cours = "<b>".$row->_titre."</b>";
			}
			
			if($row->_jour == date("w", $sd)-1)
			{
				$ret['events'][] = array(
					$row->_IDx,
					$titre_cours,
					php2JsTime(mySql2PhpTime(date("Y-m-d", $sd)." ".$heure_debut_event)),
					php2JsTime(mySql2PhpTime(date("Y-m-d", $sd)." ".$heure_fin_event)),
					0,
					0, //more than one day event
					//$row->InstanceType,
					0,//Recurring event,
					$row->_color,
					1,//editable
					"", 
					'',//$attends
					$row->_IDdata,
					$nbdev,
					""
				);
			}
		}
    }
	}catch(Exception $e){
     $ret['error'] = $e->getMessage();
  }
  // echo $sql;
  // print_r($ret);
  
  
  /********** Tableau semaine patch SUPPRESSION **********/
	$ret2 = array();
	$ret2['events'] = array();
	$ret2["issort"] =true;
	$ret2["start"] = php2JsTime($sd);
	$ret2["end"] = php2JsTime($ed);
	$ret2['error'] = null;
	if($generique == "off")
	{
	  try{
		$db = new DBConnection();
		$db->getConnection();
		
	switch ( $IDedt ) {
		case 1 :	// les salles
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx' ";
				$sql .= "from edt_data edt, campus_data mat, campus_classe classe ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' and edt._IDitem = '".$IDitem."' ";
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' and edt._etat = '2' order by _IDdata";
			break;
		case 2 :	// le personnel
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx' ";
				$sql .= "from edt_data edt, campus_data mat ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' and edt._ID = '".$IDuser."' ";
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' and edt._etat = '2' order by _IDdata";
			break;
		default :	// les classes
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx' ";
				$sql .= "from edt_data edt, campus_data mat ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' ";
				if($IDitem != "0")
				{
					$sql .= "and edt._IDclass LIKE '%;".$IDitem.";%' ";
				}
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' and edt._etat = '2' order by _IDdata";
			break;
		}
		// echo $sql;
		//.php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."'";
		$handle = mysqli_query($mysql_link, $sql);
		$date_debut = php2MySqlTime($sd);
		
		while ($row = mysqli_fetch_object($handle)) {
			// Traitements convertion date time
			$date_debut = php2MySqlTime($sd);
			$date_event = date($date_debut); // objet date
			$date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row->_jour." day"); // ajout du nb de jours
			$date_event = date("Y-m-d", $date_event); // timesptamp en string
			
			// Heure de début
			$heure_debut_event = intval($row->_heure);
			if($heure_debut_event > 0)
			{
				if(!($heure_debut_event % 2))
				{
					$heure_debut_event = 8 + ($heure_debut_event / 2).":00";
				}
				else
				{
					$heure_debut_event = 7 + (($heure_debut_event + 1) / 2).":30";
				}
			}
			else if($heure_debut_event == 0)
			{
				$heure_debut_event = "08:00";
			}
			else
			{
				if(!($heure_debut_event % 2))
				{
					$heure_debut_event = 8 - (-$heure_debut_event / 2).":00";
				}
				else
				{
					$heure_debut_event = 8 - ((-$heure_debut_event + 1) / 2).":30";
				}
			}
			
			
			// Heure de fin
			$temp1 = intval(substr($heure_debut_event, 0, strpos($heure_debut_event, ":")));
			$temp1 = $temp1 + intval(substr($row->_delais, 0, strpos($row->_delais, ":")));
			
			$temp2 = intval(substr($heure_debut_event, strpos($heure_debut_event, ":")+1));
			$temp3 = substr($row->_delais, strpos($row->_delais, ":")+1);
			$temp3 = intval(substr($temp3, 0, strpos($temp3, ":")));
			$temp2 = $temp2+$temp3;
			
			if($temp2 == 60)
			{
				$temp2 = 0;
				$temp1++;
			}
			
			$heure_fin_event = $temp1.":".$temp2;

			// Verification si quelque chose à faire
			$nbdev = 0;
			$textdev = "";
			if($generique == "off")
			{
				$sql2 = "select * from ctn_items where _type = 1 and _IDcours = ".$row->_IDx;
				$handle2 = mysqli_query($mysql_link, $sql2);
				while ($row2 = mysqli_fetch_object($handle2))
				{
					$nbdev++;
					$textdev = $row2->_texte;
				}
			}

			if($IDedt == 1)
			{
				$titre_cours = "<b>".$row->_titre."</b><br/>".strtr($row->_ident, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');;
			}
			else
			{
				$titre_cours = "<b>".$row->_titre."</b>";
			}
			
			$ret2['events'][] = array(
				$row->_IDx,
				$titre_cours,
				php2JsTime(mySql2PhpTime($date_event." ".$heure_debut_event)),
				php2JsTime(mySql2PhpTime($date_event." ".$heure_fin_event)),
				0,
				0, //more than one day event
				//$row->InstanceType,
				0,//Recurring event,
				$row->_color,
				1,//editable
				"", 
				'',//$attends
				$row->_IDdata,
				$nbdev,
				""
			);
		}
		}catch(Exception $e){
		 $ret2['error'] = $e->getMessage();
	  }
	}

  /********** Tableau semaine patch AJOUT **********/

	$ret1 = array();
	$ret1['events'] = array();
	$ret1["issort"] =true;
	$ret1["start"] = php2JsTime($sd);
	$ret1["end"] = php2JsTime($ed);
	$ret1['error'] = null;
	if($generique == "off")
	{
	  try{
		$db = new DBConnection();
		$db->getConnection();
		
	switch ( $IDedt ) {
		case 1 :	// les salles
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx' ";
				$sql .= "from edt_data edt, campus_data mat, campus_classe classe ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' and edt._IDitem = '".$IDitem."' ";
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' and edt._etat = '1' order by _IDdata";
			break;
		case 2 :	// le personnel
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx' ";
				$sql .= "from edt_data edt, campus_data mat ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' and edt._ID = '".$IDuser."' ";
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' and edt._etat = '1' order by _IDdata";
			break;
		default :	// les classes
				$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._heure '_heure', edt._delais '_delais', ";
				$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx' ";
				$sql .= "from edt_data edt, campus_data mat ";
				$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
				$sql .= "and edt._IDcentre = '".$IDcentre."' ";
				if($IDitem != "0")
				{
					$sql .= "and edt._IDclass LIKE '%;".$IDitem.";%' ";
				}
				$sql .= "and edt._jour = ".(intval(date("w", $sd))-1) ." ";
				$sql .= "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' and edt._etat = '1' order by _IDdata";
			break;
		}
		
		// echo $sql;
		//.php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."'";
		$handle = mysqli_query($mysql_link, $sql);
		$date_debut = php2MySqlTime($sd);

		while ($row = mysqli_fetch_object($handle)) {
			// Traitements convertion date time
			$date_debut = php2MySqlTime($sd);
			$date_event = date($date_debut); // objet date
			$date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row->_jour." day"); // ajout du nb de jours
			$date_event = date("Y-m-d", $date_event); // timesptamp en string
			
			// Heure de début
			$heure_debut_event = intval($row->_heure);
			if($heure_debut_event > 0)
			{
				if(!($heure_debut_event % 2))
				{
					$heure_debut_event = 8 + ($heure_debut_event / 2).":00";
				}
				else
				{
					$heure_debut_event = 7 + (($heure_debut_event + 1) / 2).":30";
				}
			}
			else if($heure_debut_event == 0)
			{
				$heure_debut_event = "08:00";
			}
			else
			{
				if(!($heure_debut_event % 2))
				{
					$heure_debut_event = 8 - (-$heure_debut_event / 2).":00";
				}
				else
				{
					$heure_debut_event = 8 - ((-$heure_debut_event + 1) / 2).":30";
				}
			}
			
			
			// Heure de fin
			$temp1 = intval(substr($heure_debut_event, 0, strpos($heure_debut_event, ":")));
			$temp1 = $temp1 + intval(substr($row->_delais, 0, strpos($row->_delais, ":")));
			
			$temp2 = intval(substr($heure_debut_event, strpos($heure_debut_event, ":")+1));
			$temp3 = substr($row->_delais, strpos($row->_delais, ":")+1);
			$temp3 = intval(substr($temp3, 0, strpos($temp3, ":")));
			$temp2 = $temp2+$temp3;
			
			if($temp2 == 60)
			{
				$temp2 = 0;
				$temp1++;
			}
			
			$heure_fin_event = $temp1.":".$temp2;
			
			// Verification si quelque chose à faire
			$nbdev = 0;
			$textdev = "";
			if($generique == "off")
			{
				$sql2 = "select * from ctn_items where _type = 1 and _IDcours = ".$row->_IDx;
				$handle2 = mysqli_query($mysql_link, $sql2);
				while ($row2 = mysqli_fetch_object($handle2))
				{
					$nbdev++;
					$textdev = $row2->_texte;
				}
			}

			if($IDedt == 1)
			{
				$titre_cours = "<b>".$row->_titre."</b><br/>".strtr($row->_ident, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');;
			}
			else
			{
				$titre_cours = "<b>".$row->_titre."</b>";
			}
			
			$ret1['events'][] = array(
				$row->_IDx,
				$titre_cours,
				php2JsTime(mySql2PhpTime($date_event." ".$heure_debut_event)),
				php2JsTime(mySql2PhpTime($date_event." ".$heure_fin_event)),
				0,
				0, //more than one day event
				//$row->InstanceType,
				0,//Recurring event,
				$row->_color,
				1,//editable
				"", 
				'',//$attends
				$row->_IDdata,
				$nbdev,
				""
			);
		}
		}catch(Exception $e){
		 $ret1['error'] = $e->getMessage();
	  }
	}

	/********** Tableau final fusion semaine normal - semaine patch **********/
	// On commence par les suppressions
	$ret_json = array();
	$ret_json['events'] = array();
	$ret_json["issort"] =true;
	$ret_json["start"] = php2JsTime($sd);
	$ret_json["end"] = php2JsTime($ed);
	$ret_json['error'] = null;
	
	if(count($ret2["events"]) > 0)
	{
		foreach($ret["events"] as $a)
		{
			$doublon = false;
			foreach($ret2["events"] as $a2)
			{
				if(!$doublon)
				{
					if($a["11"] == $a2["11"])
					{
						// echo "**doublon**";
						$doublon = true;
					}
					else
					{
						$doublon = false;
						// echo "--pas doublon-";
					}
				}
			}
			
			if(!$doublon)
			{
				$ret_json["events"][] = $a;
			}
		}
	}
	else
	{
		$ret_json = $ret;
	}
	
	// Puis les ajouts

	foreach($ret1["events"] as $a1)
	{
		$ret_json["events"][] = $a1;
		//echo "pas doublon";
	}
	
	if($generique = "off")
	{
		// Traitement des vacances
		$query  = "select _vacances from config_centre ";
		$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' AND _IDcentre = $IDcentre ";
		$query .= "order by _IDcentre";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;
		
		$vac = json_decode($row[0]);
		$vac = objectToArray($vac);
		
		foreach($ret_json["events"] as $key => $val)
		{
			foreach($vac["start"] as $keyvac => $valvac)
			{
				$j = intval(substr($vac["start"][$keyvac], 0, 2));
				$m = intval(substr($vac["start"][$keyvac], 3, 5));
				$a = intval(substr($vac["start"][$keyvac], 6, 10));
				$time_start = mktime(0, 0, 0, $m, $j, $a);
				$j = intval(substr($vac["end"][$keyvac], 0, 2));
				$m = intval(substr($vac["end"][$keyvac], 3, 5));
				$a = intval(substr($vac["end"][$keyvac], 6, 10));
				$time_end = mktime(23, 59, 59, $m, $j, $a);

				if(date(js2PhpTime($val[2])) >= date($time_start) && date(js2PhpTime($val[3])) <= date($time_end ))
				{
					unset($ret_json["events"][$key]);
				}
			}
		}
	}
	
	// print_r($ret_json);
	return $ret_json;
}

function listCalendar($day, $type){
  $phpTime = js2PhpTime($day);
  //echo $phpTime . "+" . $type;
  switch($type){
    case "month":
      $st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
      break;
    case "week":
      //suppose first day of a week is monday 
      $monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
      //echo date('N', $phpTime);
      $st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
      $et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
      break;
    case "day":
      $st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
      break;
  }
  //echo $st . "--" . $et;
  return listCalendarByRange($st, $et);
}

function updateCalendar($id, $st, $et, $generique){
	$ret = array();
	$tab = DateCalendarToPmt($st, $et);
	
	try
	{
		$db = new DBConnection();
		$db->getConnection();
		
		$start  = date('Y-m-d H:i:s', js2PhpTime($st));
		$end    = date('Y-m-d H:i:s', js2PhpTime($et));
		$d_start    = new DateTime($start);
		$d_end      = new DateTime($end);
		$diff = $d_start->diff($d_end);
		$duree = $diff->format('%H').":".$diff->format('%I');
		
		if($generique == "on")
		{
			$sql = "update `edt_data` set _delais = '$duree', _jour = '".$tab["jour"]."', _heure = '".$tab["heure"]."', _annee = '".$tab["annee"]."' where `_IDx`=" . $id;
				if(mysqli_query($mysql_link, $sql)==false){
			  $ret['IsSuccess'] = false;
			  $ret['Msg'] = mysqli_error($mysql_link);
			}else{
				$ret['IsSuccess'] = true;
				$ret['Msg'] = 'Succefully';
			}

		}	
		else if($generique == "off")
		{
			// Recup IDdata
			$sql = "select * from `edt_data` where `_IDx`=" . $id;
			$handle = mysqli_query($mysql_link, $sql);

			while ($row = mysqli_fetch_object($handle)) {
				$IDdata =    $row->_IDdata;
				$IDedt =     $row->_IDedt;
				$IDcentre =  $row->_IDcentre;
				$IDmat =     $row->_IDmat;
				$IDclass =   $row->_IDclass;
				$IDitem =    $row->_IDitem;
				$ID =        $row->_ID;
				$semaine =   $row->_semaine;
				$group =     $row->_group;
				$jour =      $row->_jour;
				$heure =     $row->_heure;
				$delais =    $row->_delais;
				$visible =   $row->_visible;
				$nosemaine = $row->_nosemaine;
				$etat =      $row->_etat;
				$annee =     $row->_annee;
			}
			
			if($etat == 1)
			{
				$sql = "update `edt_data` set _delais = '$duree', _jour = '".$tab["jour"]."', _heure = '".$tab["heure"]."', _annee = '".$tab["annee"]."' where `_IDx`=" . $id;
				$handle = mysqli_query($mysql_link, $sql);
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
			else
			{
				$sql  = "insert into `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
				$sql .= "`_semaine`, `_group`, `_jour`, `_heure`, `_delais`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`) values ";
				$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$IDclass', '$IDitem', '$ID', '$semaine', '$group', '$jour', '$heure', '$duree', '$visible', '".date("W", js2PhpTime($st))."', '2', '', '$annee')";
				$handle = mysqli_query($mysql_link, $sql);
				
				$sql  = "insert into `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
				$sql .= "`_semaine`, `_group`, `_jour`, `_heure`, `_delais`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`) values ";
				$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$IDclass', '$IDitem', '$ID', '$semaine', '$group', '".$tab["jour"]."', '".$tab["heure"]."', '$duree', '$visible', '".date("W", js2PhpTime($st))."', '1', '', '".$tab["annee"]."')";
				
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
	}
	catch(Exception $e)
	{
		$ret['IsSuccess'] = false;
		$ret['Msg'] = $e->getMessage();
	}
	return $ret;
}

function updateDetailedCalendar($id, $st, $et, $sub, $ade, $dscr, $loc, $color, $tz){
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "update `jqcalendar` set"
      . " `starttime`='" . php2MySqlTime(js2PhpTime($st)) . "', "
      . " `endtime`='" . php2MySqlTime(js2PhpTime($et)) . "', "
      . " `subject`='" . mysqli_real_escape_string($sub) . "', "
      . " `isalldayevent`='" . mysqli_real_escape_string($ade) . "', "
      . " `description`='" . mysqli_real_escape_string($dscr) . "', "
      . " `location`='" . mysqli_real_escape_string($loc) . "', "
      . " `color`='" . mysqli_real_escape_string($color) . "' "
      . "where `id`=" . $id;
    //echo $sql;
		if(mysqli_query($mysql_link, $sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = mysqli_error($mysql_link);
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'Succefully';
    }
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}

function removeCalendar($id, $generique, $st){
	$ret = array();

	try
	{
		$db = new DBConnection();
		$db->getConnection();
		
		if($generique == "on")
		{
			$sql = "delete from `edt_data` where _etat = 0 and `_IDx`=" . $id;
				if(mysqli_query($mysql_link, $sql)==false){
			  $ret['IsSuccess'] = false;
			  $ret['Msg'] = mysqli_error($mysql_link);
			}else{
				$ret['IsSuccess'] = true;
				$ret['Msg'] = 'Succefully';
			}

		}
		else if($generique == "off")
		{
			// Recup IDdata
			$sql = "select * from `edt_data` where `_IDx`=" . $id;
			$handle = mysqli_query($mysql_link, $sql);

			while ($row = mysqli_fetch_object($handle)) {
				$IDdata =    $row->_IDdata;
				$IDedt =     $row->_IDedt;
				$IDcentre =  $row->_IDcentre;
				$IDmat =     $row->_IDmat;
				$IDclass =   $row->_IDclass;
				$IDitem =    $row->_IDitem;
				$ID =        $row->_ID;
				$semaine =   $row->_semaine;
				$group =     $row->_group;
				$jour =      $row->_jour;
				$heure =     $row->_heure;
				$delais =    $row->_delais;
				$visible =   $row->_visible;
				$nosemaine = $row->_nosemaine;
				$etat =      $row->_etat;
				$annee =      $row->_annee;
			}
			
			if($etat == 1)
			{
				$sql = "delete from `edt_data` where `_IDx`=" . $id;
				$handle = mysqli_query($mysql_link, $sql);
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
			else
			{
				$sql  = "insert into `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
				$sql .= "`_semaine`, `_group`, `_jour`, `_heure`, `_delais`, `_visible`, `_nosemaine`, `_etat`, `_IDx`) values ";
				$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$IDclass', '$IDitem', '$ID', '$semaine', '$group', '$jour', '$heure', '$delais', '$visible', '".date("W", js2PhpTime($st))."', '1', '','$annee')";
				$handle = mysqli_query($mysql_link, $sql);
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
	}
	catch(Exception $e)
	{
		$ret['IsSuccess'] = false;
		$ret['Msg'] = $e->getMessage();
	}
	
	return $ret;
}

?>