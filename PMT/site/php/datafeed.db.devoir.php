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
 *		module   : datafeed.db.php
 *
 *		version  : 1.0
 *		auteur   : IP-Solutions
 *		creation : 30/10/2013
 *		modif    : 
 */
?>

<?php
session_start();
include_once("dbconfig.devoir.php");
include_once("functions.php");
$_SESSION["showdate"] = js2PhpTime($_POST["showdate"]);
function listCalendarByRange($sd, $ed){

	$IDcentre = ( @$_POST["IDcentre"] )					// Identifiant du centre
		? (int) $_POST["IDcentre"]
		: (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
	$IDedt    = ( @$_POST["IDedt"] )					// type d'edt
		? (int) $_POST["IDedt"]
		: (int) @$_GET["IDedt"];
	$IDclass  = (int) (@$_GET["IDclass"]) ? $_GET["IDclass"] : $_SESSION["CnxClass"] ;
	$IDitem = $IDclass;
	$IDuser   = ( @$_POST["IDuser"] )					// Identifiant de l'utilisateur
		? (int) $_POST["IDuser"]
		: (int) @$_GET["IDuser"] ;
	$IDdata   = (int) @$_GET["IDdata"];					// Identifiant de l'edt
	$generique   = ( @$_POST["generique"] )					// Identifiant de l'utilisateur
		?  $_POST["generique"]
		:  @$_GET["generique"] ;
	$isModif   = ( @$_POST["isModif"] )					
		?  $_POST["isModif"]
		:  @$_GET["isModif"] ;
	$isDevoir   = ( @$_POST["isDevoir"] )					
		?  $_POST["isDevoir"]
		:  @$_GET["isDevoir"] ;

	if($_SESSION["CnxGrp"] == 2 || $_SESSION["CnxAdm"] == 255)
	{
		$isDevoir = true;
	}
	
	$mois = substr($sd, 0, strpos($sd, "/"));
	$jour = substr($sd, strpos($sd, "/")+1, strpos($sd, "/"));
	$annee = substr($sd, strpos($sd, "/")+1);
	$annee = substr($annee, strpos($annee, "/")+1, 4);
	$mktime = $sd;
	
  /********** Tableau semaine normale **********/

	if($generique == "modif")
	{

	}
	else
	{
		/********** Tableau semaine patch AJOUT **********/

		$ret1 = array();

		if($generique != "on")
		{
			try
			{
				$db = new DBConnection();
				$db->getConnection();
				switch ( $IDedt )
				{
					case 1 :	// les salles
							$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', ";
							$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDclass '_IDclass', edt._IDrmpl '_IDrmpl', edt._IDitem '_IDitem' ";
							$sql .= "from edt_data edt, campus_data mat, campus_classe classe ";
							$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
							$sql .= "and edt._IDitem = '".$IDitem."' ";
							$sql .= "and edt._jour = ".(intval(date("w", $mktime))-1)." and edt._nosemaine = '".date("W", $mktime)."' and edt._annee = '".date("Y", $mktime)."' and (edt._etat = '1' OR edt._etat = '3') order by _jour, _debut";
						break;
					case 2 :	// le personnel
							$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', ";
							$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDrmpl '_IDrmpl', edt._IDitem '_IDitem', edt._IDclass '_IDclass' ";
							$sql .= "from edt_data edt, campus_data mat ";
							$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
							$sql .= "and edt._ID = '".$IDuser."' ";
							$sql .= "and edt._jour = ".(intval(date("w", $mktime))-1)." and edt._nosemaine = '".date("W", $mktime)."' and edt._annee = '".date("Y", $mktime)."' and (edt._etat = '1' OR edt._etat = '3') order by _jour, _debut";
						break;
					default :	// les classes
							$sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', ";
							$sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDitem '_IDitem', edt._IDrmpl '_IDrmpl', edt._IDitem '_IDitem' ";
							$sql .= "from edt_data edt, campus_data mat ";
							$sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
							$sql .= "and edt._IDclass LIKE '%;".$IDitem.";%' ";
							$sql .= "and edt._jour = ".(intval(date("w", $mktime))-1)." and edt._nosemaine = '".date("W", $mktime)."' and edt._annee = '".date("Y", $mktime)."' and (edt._etat = '1' OR edt._etat = '3') order by _jour, _debut";
						break;
				}
				
				// echo $sql;
				// echo php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."'";
				$handle = mysqli_query($mysql_link, $sql);
				$date_debut = php2MySqlTime($mktime);

				while ($row = mysqli_fetch_object($handle))
				{

					$date_event = date("Y-m-d", $mktime); // timesptamp en string

					// Heure de début
					$heure_debut_event = $row->_debut;	
					
					// Heure de fin					
					$heure_fin_event = $row->_fin;

					// Verification si quelque chose à faire
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
					
					if($IDedt == 1)
					{
						$array_class = @split(";", $row->_IDclass);
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
									$nom_class .= htmlentities($rowsclass[0], ENT_NOQUOTES, "iso-8859-1")."<br />";
								}
							}
						}
						
						$titre_cours = "<b>".htmlentities($row->_titre, ENT_NOQUOTES, "iso-8859-1")."</b> <br/>".htmlentities(strtoupper($rowsuser[0]), ENT_NOQUOTES, "iso-8859-1")." <br/><i>".$nom_class."</i>";
					}
					else if($IDedt == 3)
					{
						// Nom de la salle
						$querysalle  = "select _title from edt_items ";
						$querysalle .= "where _IDitem = ".$row->_IDitem;
						
						$resultsalle = mysqli_query($mysql_link, $querysalle);
						$rowsalle    = ( $resultsalle ) ? mysqli_fetch_row($resultsalle) : 0 ;
						$titre_cours = "<b>".htmlentities($row->_titre, ENT_NOQUOTES, "iso-8859-1")."</b> <br/>".htmlentities(strtoupper($rowsuser[0]), ENT_NOQUOTES, "iso-8859-1")." <br/>".htmlentities($rowsalle[0], ENT_NOQUOTES, "iso-8859-1");
					}
					else
					{
						$array_class = @split(";", $row->_IDclass);
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
									$nom_class .= htmlentities($rowsclass[0], ENT_NOQUOTES, "iso-8859-1")." ";
								}
							}
						}
						
						// Nom de la salle
						$querysalle  = "select _title from edt_items ";
						$querysalle .= "where _IDitem = ".$row->_IDitem;
						
						$resultsalle = mysqli_query($mysql_link, $querysalle);
						$rowsalle    = ( $resultsalle ) ? mysqli_fetch_row($resultsalle) : 0 ;
						
						$titre_cours = "<b>".htmlentities($row->_titre, ENT_NOQUOTES, "iso-8859-1")."</b> <br/>$nom_class <br/>".htmlentities($rowsalle[0], ENT_NOQUOTES, "iso-8859-1");
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
						"",
						$isModif,
						$isDevoir,
						$row->_ID,
						$_SESSION["CnxID"]
					);
				}
			}
			catch(Exception $e)
			{
				$ret1['error'] = $e->getMessage();
			}
		}
		
		// Puis les ajouts

		foreach($ret1["events"] as $a1)
		{
			$ret_json["events"][] = $a1;
			//echo "pas doublon";
		}
		

	}
	
	if($generique != "modif")
	{
		// Trie pour ordre croissant
		$ret_json_final = array();
		$ret_json_final['events'] = array();
		$tab_trie = array();
		$date = array();
		$id = array();
		
		foreach($ret_json["events"] as $key => $val)
		{
			$tab_trie[] = array("id" => $key, "date" => intval(js2PhpTime($val[2]))); 
		}
		
		// Obtient une liste de colonnes
		foreach ($tab_trie as $key => $val) { 
		   $id[$key] = $val['id']; 
		   $date[$key] = $val['date'];  
		}
		
		array_multisort($date, SORT_ASC, SORT_NUMERIC, $id, SORT_ASC, $tab_trie); 
		
		foreach($tab_trie as $key => $val)
		{
			$ret_json_final["events"][] = $ret_json["events"][$val["id"]];
		}
	}
	
	// print_r($ret_json_final);
	return $ret_json_final;
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
  // echo $st . "--" . $et;
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
		$duree = date('H:i:s', js2PhpTime($et));
		
		if($generique == "on")
		{
			$sql = "update `edt_data` set _fin = '$duree', _jour = '".$tab["jour"]."', _debut = '".date('H:i:s', js2PhpTime($st))."', _annee = '".$tab["annee"]."' where `_IDx`=" . $id;
				if(mysqli_query($mysql_link, $sql)==false){
			  $ret['IsSuccess'] = false;
			  $ret['Msg'] = mysqli_error($mysql_link);
			}else{
				$ret['IsSuccess'] = true;
				$ret['Msg'] = 'Succefully';
			}

		}	
		else if($generique != "on")
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
				$heure =     $row->_debut;
				$delais =    $row->_fin;
				$visible =   $row->_visible;
				$nosemaine = $row->_nosemaine;
				$etat =      $row->_etat;
				$annee =     $row->_annee;
			}
			
			if($etat == 1)
			{
				$sql = "update `edt_data` set _fin = '$duree', _etat = '3', _jour = '".$tab["jour"]."', _debut = '".date('H:i:s', js2PhpTime($st))."', _annee = '".$tab["annee"]."' where `_IDx`=" . $id;
				// echo $sql;
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
				$sql .= "`_semaine`, `_group`, `_jour`, `_debut`, `_fin`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`) values ";
				$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$IDclass', '$IDitem', '$ID', '$semaine', '$group', '$jour', '$heure', '$duree', '$visible', '".date("W", js2PhpTime($st))."', '2', '', '$annee', '')";
				$handle = mysqli_query($mysql_link, $sql);
				
				$sql  = "insert into `edt_data` (`_IDdata`, `_IDedt`, `_IDcentre`, `_IDmat`, `_IDclass`, `_IDitem`, `_ID`, ";
				$sql .= "`_semaine`, `_group`, `_jour`, `_debut`, `_fin`, `_visible`, `_nosemaine`, `_etat`, `_IDx`, `_annee`) values ";
				$sql .= "('$IDdata', '$IDedt', '$IDcentre', '$IDmat', '$IDclass', '$IDitem', '$ID', '$semaine', '$group', '".$tab["jour"]."', '".date('H:i:s', js2PhpTime($st))."', '$duree', '$visible', '".date("W", js2PhpTime($st))."', '3', '', '".$tab["annee"]."', '')";
				
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