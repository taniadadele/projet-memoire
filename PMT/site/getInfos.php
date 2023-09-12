<?php
header('Content-type: text/html; charset=utf-8');
session_start();
require_once "page_session.php";
include_once("php/dbconfig.php");
include_once("php/functions.php");

if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
{
	require "msg/user.php";
	require_once "include/TMessage.php";
	$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/user.php");

	$IDclass = @$_GET["IDclass"];
	$IDuser  = @$_GET["IDuser"];
	$IDcentre  = (int)@$_GET["IDcentre"];
	$str  = @$_GET["term"];
	$type    = (int) @$_GET["type"];
	$start   = mktime(0, 0, 0, getParam("START_M"), getParam("START_D"), getParam("START_Y"));
	$end     = mktime(23, 59, 59, getParam("END_M"), getParam("END_D"), getParam("END_Y"));
	$startday = intval(date("N", $start))-1;
	$endday = intval(date("N", $end))-1;

	if($type == 1)
	{
		if ($_SESSION['CnxGrp'] > 1)
		{
			$query  = $db->parse("SELECT DISTINCTROW `user_id`.`_ID` as `user_id`, `user_id`.`_name` as `user_name`, `user_id`.`_fname` as `user_fname`, `campus_data`.`_titre` as `titre` ");
			$query .= $db->parse("FROM user_id, user_group, edt_data, campus_data ");
			$query .= $db->parse("WHERE user_group._IDcat = '2' ");
			$query .= $db->parse("AND user_id._IDgrp = user_group._IDgrp AND edt_data._ID = user_id._ID AND edt_data._IDmat = campus_data._IDmat ");
			$query .= $db->parse("AND edt_data._IDclass LIKE ?s ", '%;'.$IDclass.';%');
			$query .= $db->parse("AND edt_data._etat = 1 ");
			$query .= $db->parse("AND ((edt_data._jour >= ?i AND edt_data._nosemaine >= ?i AND edt_data._annee = ?i) ", $startday, date("W", $start), date("Y", $start));
			$query .= $db->parse("OR (edt_data._jour <= ?i AND edt_data._nosemaine <= ?i AND edt_data._annee = ?i)) ", $endday, date("W", $end), date("Y", $end));
			$query .= $db->parse("AND campus_data._lang = ?s order by user_id._name ", $_SESSION["lang"]);
			$datas = $db->getAll($query);
			foreach ($datas as $data) {
				echo '<tr class="bg-transparent">';
					echo '<td>'.$data->user_name.'&nbsp;'.$data->user_fname.'</td>';
					echo '<td class="text-right">'.$data->titre.'</td>';
				echo '</tr>';
			}
		}
	}

	elseif($type == 2)
	{
		/*** Module forfait ***/
		if(getParam("FORFAIT") == 1)
		{
			if($_SESSION["CnxAdm"] >= 255 || $_SESSION["CnxID"] == $IDuser)
			{
				print ("<tr>
							<td class=\"align-center btn-primary\" style=\"padding: 10px;\"></td>
							<td class=\"align-center btn-primary\" style=\"padding: 10px;\">Mouvement (20 derniers)</td>
							<td class=\"align-center btn-primary\" style=\"padding: 10px;\">Nouveau solde</td>
							<td class=\"align-center btn-primary\" style=\"padding: 10px;\">Cours</td>
						</tr>");

				$query  = "SELECT fl._mvt, fl._solde, fl._IDcours, fl._IDforfait, fl._date ";
				$query .= "FROM forfait_log fl  ";
				$query .= "WHERE fl._IDuser = $IDuser ";
				$query .= "ORDER BY fl._IDlog desc LIMIT 20";

				$result2 = mysqli_query($mysql_link, $query);
				$row2    = ( $result2 ) ? remove_magic_quotes(mysqli_fetch_row($result2)) : 0 ;

				while ( $row2 )
				{
					$return = mysqli_query($mysql_link, "select campus_data._titre, ctn_items._note, ctn_items._IDcours from campus_data, ctn_items where ctn_items._IDitem = '$row[12]' AND campus_data._IDmat = ctn_items._IDmat AND campus_data._lang = '".$_SESSION["lang"]."' limit 1");
					$cours  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

					$return = mysqli_query($mysql_link, "SELECT campus_data._titre FROM user_id, edt_data, campus_data WHERE edt_data._ID = user_id._ID AND edt_data._IDmat = campus_data._IDmat AND edt_data._IDx = $row2[2] ");
					$cours  = ( $return ) ? remove_magic_quotes(mysqli_fetch_row($return)) : 0 ;

					$resulta = mysqli_query($mysql_link, "SELECT _Nom FROM forfait WHERE _IDforfait = $row2[3] ");
					$rowb    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;

					$solde = $row2[0];
					$time_h = $solde/60/60;
					if(strpos($time_h, ".")>0)
					{
						$time_h = substr($time_h, 0, strpos($time_h, "."));
						$time_m = abs(($solde/60)%60);
					}
					else
					{
						$time_m = "00";
					}

					$solde2 = $row2[1];
					$time_h2 = $solde2/60/60;
					if(strpos($time_h2, ".")>0)
					{
						$time_h2 = substr($time_h2, 0, strpos($time_h2, "."));
						$time_m2 = abs(($solde2/60)%60);
					}
					else
					{
						$time_m2 = "00";
					}

					echo "<tr>";
							echo "<td style=\"border-left: 0px;\">".date("d/m/Y", strtotime($row2[4]))."</td>";
							if($row2[0] < 0)
							{
								echo "<td style=\"border-left: 0px;\"><span class=\"label label-important\">".$time_h."h".$time_m."</span></td>";
							}
							else
							{
								echo "<td style=\"border-left: 0px;\"><span class=\"label label-success\">".$time_h."h".$time_m."</span></td>";
							}
							echo "<td style=\"border-left: 0px;\"><i class=\"icon-shopping-cart\"></i> ".$time_h2."h".$time_m2."</td>";
							echo "<td style=\"border-left: 0px;\"><i class=\"icon-tags\"></i> <strong>".$cours[0]."</strong> $rowb[0]</td>";
					echo "</tr>";

					$row2 = remove_magic_quotes(mysqli_fetch_row($result2));
				}
			}
		}

		$colspan = (getParam("FORFAIT") == 1) ? 4 : 2;
		$colspantd = (getParam("FORFAIT") == 1) ? 3 : 1;

		if(count(getGroupsByIDuser($IDuser)))
		{
			print ("<tr><td class=\"align-center btn-primary\" colspan=\"$colspan\" style=\"padding: 10px;\">".$msg->read($USER_GROUPS)."</td></tr>");
		}

		/*** Affichage groupes ***/
		foreach(getGroupsByIDuser($IDuser) as $key => $val)
		{
			print("
				<tr>
					<td style=\"border-left: 0px;\"><strong><i class=\"icon-tags\"></i> ".$val."</strong></td>
					<td style=\"text-align: right; border-left: 0px;\" colspan=\"$colspantd\"><i class=\"icon-user\"></i> ".implode(", ", getUsersByIDgrp($key))."</td>
				</tr>");
		}

	}

	elseif($type == 3)
	{
		$query  = "select distinctrow edt_data._IDclass, campus_data._titre, edt_data._group ";
		$query .= "from edt_data, campus_classe, campus_data ";
		$query .= "WHERE edt_data._IDmat = campus_data._IDmat ";
		$query .= "AND (edt_data._ID = $IDuser OR edt_data._IDrmpl = $IDuser) ";
		$query .= "AND edt_data._etat = 1 ";
		$query .= "AND edt_data._attribut = 0 ";
		$query .= "AND ((edt_data._nosemaine >= ".date("W", $start)." AND edt_data._annee = ".date("Y", $start).") ";
		$query .= "OR (edt_data._nosemaine <= ".date("W", $end)." AND edt_data._annee = ".date("Y", $end).")) ";
		$query .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";
		$query .= "ORDER BY edt_data._IDclass, edt_data._group ";
		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		if($row)
		{
			print ("<tr><td class=\"btn-primary\" colspan=\"4\" style=\"padding: 10px;\">".$msg->read($USER_CLASS)."</td></tr>");
			while ( $row )
			{
				if($row[2] == 1)
				{
					$drap = "<img alt=\"[fr]\" title=\"fr\" src=\"images/lang/ico-fr.png\">";
				}
				else if($row[2] == 2)
				{
					$drap = "<img alt=\"[de]\" title=\"de\" src=\"./images/lang/ico-de.png\">";
				}
				else
				{
					$drap = "";
				}
				$txt_classe = "";
				foreach(explode(";", $row[0]) as $val)
				{
					if($val != "")
					{
						$query  = "SELECT distinctrow campus_classe._ident ";
						$query .= "FROM campus_classe ";
						$query .= "WHERE campus_classe._IDclass = $val ";
						$resulta = mysqli_query($mysql_link, $query);
						$rowa    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;
						$txt_classe .= "$rowa[0] ";
					}
				}

				print("<tr><td width=\"1%\">$drap</td><td style=\"border-left: 0px;\"><strong>$txt_classe</strong></td><td style=\"text-align: right; border-left: 0px;\">$row[1]"."</td><td></td></tr>");
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
		}

		$query  = "select distinctrow edt_data._IDclass, campus_data._titre, campus_data._IDmat, edt_data._group ";
		$query .= "from edt_data, campus_classe, campus_data ";
		$query .= "WHERE edt_data._IDmat = campus_data._IDmat ";
		$query .= "AND (edt_data._ID = $IDuser OR edt_data._IDrmpl = $IDuser) ";
		$query .= "AND edt_data._etat = 1 ";
		$query .= "AND edt_data._attribut = 1 ";
		$query .= "AND ((edt_data._nosemaine >= ".date("W", $start)." AND edt_data._annee = ".date("Y", $start).") ";
		$query .= "OR (edt_data._nosemaine <= ".date("W", $end)." AND edt_data._annee = ".date("Y", $end).")) ";
		$query .= "AND campus_data._lang = '".$_SESSION["lang"]."' ";
		$query .= "ORDER BY edt_data._IDclass, edt_data._group ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		if($row)
		{
			print ("<tr><td class=\"align-center btn-primary\" colspan=\"4\" style=\"padding: 10px;\">".$msg->read($USER_GROUPS)."</td></tr>");
			while ( $row )
			{
				if($row[3] == 1)
				{
					$drap = "<img alt=\"[fr]\" title=\"fr\" src=\"images/lang/ico-fr.png\">";
				}
				else if($row[3] == 2)
				{
					$drap = "<img alt=\"[de]\" title=\"de\" src=\"./images/lang/ico-de.png\">";
				}
				else
				{
					$drap = "";
				}
				$txt_classe = "";
				foreach(explode(";", $row[0]) as $val)
				{
					if($val != "")
					{
						$query  = "SELECT distinctrow campus_classe._ident, campus_classe._IDclass ";
						$query .= "FROM campus_classe ";
						$query .= "WHERE campus_classe._IDclass = $val ";
						$resulta = mysqli_query($mysql_link, $query);
						$rowa    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;
						$txt_classe .= "$rowa[0] ";
					}
				}

				print("<tr><td width=\"1%\">$drap</td><td style=\"border-left: 0px;\">"."<strong>$txt_classe</strong></td><td style=\"text-align: right; border-left: 0px;\">$row[1]")."</td><td style=\"width: 1%;\"><a class=\"icon-pencil\" href=\"index.php?item=38&cmde=groupe&IDcentre=".$_SESSION["CnxCentre"]."&IDclass=$row[0]&IDmat=$row[2]&IDuser=$IDuser&fiche=on\"></a></td></tr>";
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
		}
	}

	elseif($type == 4) // user absence
	{
		$query  = "select _ID, _name, _fname, _IDclass ";
		$query .= "from user_id ";
		$query .= "WHERE _name LIKE '".utf8_decode($str)."%' AND _IDgrp = 1 AND (_adm = 1 OR _adm = 255) ";
		$query .= "ORDER BY _name ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while ( $row )
		{

			$txt_classe = "";
			foreach(explode(";", $row[3]) as $val)
			{
				if($val != "")
				{
					$query  = "SELECT distinctrow campus_classe._ident, campus_classe._IDclass ";
					$query .= "FROM campus_classe ";
					$query .= "WHERE campus_classe._IDclass = $val ";
					$resulta = mysqli_query($mysql_link, $query);
					$rowa    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;
					$txt_classe .= "$rowa[0] ";
				}
			}
			$row['value'] = stripslashes($row[1]." ".$row[2]." " .$txt_classe);
			$row['id']=(int) $row[0];
			$row_set[] = $row;
			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
		echo json_encode($row_set);
	}
	elseif($type == 5)
	{
		$query  = "select _ID, _name, _fname, _IDclass ";
		$query .= "from user_id ";
		$query .= "WHERE _name LIKE '".utf8_decode($str)."%' OR _fname LIKE '".utf8_decode($str)."%' ";
		$query .= "ORDER BY _name ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while ( $row )
		{
			$rowa['label'] = stripslashes($row[1]." ".$row[2]."<span class='hidden'>".$row[0]."</span>");
			$row_set[] = $rowa;
			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
		echo json_encode($row_set);
	}
	elseif($type == 6)
	{
		$query  = "select _IDclass, _ident ";
		$query .= "from campus_classe ";
		$query .= "WHERE _ident LIKE '%".utf8_decode($str)."%' AND _visible = 'O' ";
		$query .= "ORDER BY _code ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while ( $row )
		{
			// Ne pas chenger la ligne suivante!!!
			$rowa['label'] = stripslashes($row[1]."<span class='hidden'>".$row[0]."</span>");
			$row_set[] = $rowa;
			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
		echo json_encode($row_set);
	}

	elseif($type == 7)
	{
		foreach(getGroups(0, $str) as $key => $val)
		{
			$rowa['label'] = "$val<span class='hidden'>$key</span>";
			$row_set[] = $rowa;
		}

		echo json_encode($row_set);
	}

	elseif($type == 8)
	{
		$query  = "SELECT _IDlidi, _nom FROM postit_lidi ";
		$query .= "WHERE _nom LIKE '".utf8_decode($str)."%' ";
		$query .= "AND (_public = 'O' OR _ID = ".$_SESSION["CnxID"].") ";
		$query .= "ORDER by _nom";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while ( $row )
		{
			$rowa['label'] = stripslashes($row[1]."<span class='hidden'>".$row[0]."</span>");
			$row_set[] = $rowa;
			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
		echo json_encode($row_set);
	}

	elseif($type == 9)
	{
		$query  = "select _IDclass, _ident ";
		$query .= "from campus_classe ";
		$query .= "WHERE _ident LIKE '%".utf8_decode($str)."%' ";
		$query .= "ORDER BY _ident ";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while ( $row )
		{
			$rowa['label'] = stripslashes($row[1]."<span class='hidden'>".$row[0]."</span>");
			$row_set[] = $rowa;
			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
		echo json_encode($row_set);
	}

	elseif($type == 10)
	{
		$query  = "select _IDmat, _titre from campus_data ";
		$query .= "where _visible = 'O' AND _lang = '".$_SESSION["lang"]."' ";
		$query .= "AND _titre LIKE '".utf8_decode($str)."%' ";
		$query .= "AND _type <= '1' ";
		$query .= "order by _titre";

		$result = mysqli_query($mysql_link, $query);
		$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

		while ( $row )
		{

			$rowa['label'] = stripslashes($row[1]."<span class='hidden'>".$row[0]."</span>");
			$row_set[] = $rowa;
			$row = remove_magic_quotes(mysqli_fetch_row($result));
		}
		echo json_encode($row_set);
	}

	elseif($type == 11) // Eleve pour groupe tag-it
	{
		if ($_SESSION['CnxGrp'] > 1)
		{
			$query  = "select u._ID, u._name, u._fname, u._IDclass, c._ident ";
			$query .= "from user_id u, campus_classe c ";
			$query .= "WHERE u._IDclass = c._IDclass ";
			$query .= "AND (u._name LIKE '".utf8_decode($str)."%' OR u._fname LIKE '".utf8_decode($str)."%') ";
			$query .= "AND c._lang = '".$_SESSION["lang"]."' ";
			$query .= "AND u._IDgrp = 1 ";
			$query .= (getParam("GROUPE_IDCENTRE") == 1) ? "AND u._IDcentre = $IDcentre " : "";
			$query .= "ORDER BY u._name ";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row )
			{
				$rowa['label'] = stripslashes($row[1]." ".$row[2]." (".$row[4].")<span class='hidden'>".$row[0]."</span>");
				$row_set[] = $rowa;
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
			echo json_encode($row_set);
		}
	}

	elseif($type == 12)
	{
		$expire = time() -36000 ; // valable 10 heures
		$dir = "./cache/";
		if (is_dir($dir))
		{
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					echo $file."<br>";
					if ((filetype($dir . $file) == "file") && (filemtime($dir . $file) < $expire) && ($file != "index.html")) unlink($dir . $file);
				}
				closedir($dh);
			}
		}
	}

	elseif($type == 13) // Eleve pour cours particulier saisie edt
	{
		if ($_SESSION['CnxGrp'] > 1)
		{
			$query  = "select _ID, _name, _fname, _IDclass ";
			$query .= "from user_id ";
			$query .= "WHERE (_name LIKE '".utf8_decode($str)."%' OR _fname LIKE '".utf8_decode($str)."%') ";
			$query .= "AND _IDgrp = 1 ";
			$query .= "AND _IDcentre = $IDcentre ";
			$query .= "ORDER BY _name ";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row )
			{

				$txt_classe = "";
				foreach(explode(";", $row[3]) as $val)
				{
					if($val != "")
					{
						$query  = "SELECT distinctrow campus_classe._ident, campus_classe._IDclass ";
						$query .= "FROM campus_classe ";
						$query .= "WHERE campus_classe._IDclass = $val ";
						$resulta = mysqli_query($mysql_link, $query);
						$rowa    = ( $resulta ) ? remove_magic_quotes(mysqli_fetch_row($resulta)) : 0 ;
						$txt_classe .= "$rowa[0] ";
					}
				}
				$row['value'] = stripslashes($row[1]." ".$row[2]." (".$txt_classe.")");
				$row['id']=(int) $row[0];
				$row_set[] = $row;
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
			echo json_encode($row_set);
		}
	}

	elseif($type == 14) // Mati�res en fonction de la formation
	{
		if ($_SESSION['CnxGrp'] > 1)
		{
			$query  = "SELECT c._IDmat, c._titre ";
			$query .= "FROM campus_data c, class_mat_user m ";
			$query .= "WHERE c._IDmat = m._IDRmat ";
			$query .= "AND m._IDRclass = $IDclass ";
			$query .= "ORDER BY c._titre ";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			echo "<option value=\"\">-- Sélectionnez une matière --</option>";
			while($row)
			{
				echo "<option value=\"$row[0]\">$row[1]</option>";
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
		}
	}
	elseif($type == 15) // Mati�res complet sans prendre en compte la formation
	{
		if ($_SESSION['CnxGrp'] > 1)
		{
			$query  = "SELECT c._IDmat, c._titre ";
			$query .= "FROM campus_data c ";
			$query .= "ORDER BY c._titre ";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			echo "<option value=\"\">-- Sélectionnez une matière --</option>";
			while($row)
			{
				echo "<option value=\"$row[0]\">$row[1]</option>";
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
		}
	}


	elseif($type == 16) // Liste des professeurs
	{
		if ($_SESSION['CnxGrp'] > 1)
		{
			$query  = "SELECT `_ID`, `_name`, `_fname`, `_IDgrp` ";
			$query .= "FROM `user_id` WHERE `_IDgrp` >= 2 ";
			$query .= "AND (`_name` LIKE '".utf8_decode($str)."%' OR `_fname` LIKE '".utf8_decode($str)."%') ";

			$result = mysqli_query($mysql_link, $query);
			$row    = ( $result ) ? remove_magic_quotes(mysqli_fetch_row($result)) : 0 ;

			while ( $row )
			{
				$rowa['label'] = stripslashes($row[1]." ".$row[2]."<span class='hidden'>".$row[0]."</span>");
				if ($row[3] != 1) $row_set[] = $rowa;
				$row = remove_magic_quotes(mysqli_fetch_row($result));
			}
			echo json_encode($row_set);
		}
	}
}




?>
