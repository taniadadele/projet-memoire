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


  session_start();
  include_once("dbconfig.php");
  include_once("functions.php");
  include_once("../include/fonction/parametre.php");
  include_once("../include/fonction/edt.php");
  include_once("../include/fonction/relation.php");
  error_reporting(E_ALL);
     ini_set("display_errors", 1);

  // Connexion à la db
  // require("../config.php");
  // require_once("../include/sqltools.php");
  // $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
  include_once '../include/class/mysql.class.php';
  include_once 'dbconfig.php';

  if (@$_SESSION["sessID"] AND !empty($_SESSION["CnxAdm"]))
  {
    $IDclass  = ( @$_POST["IDclass"] )					// Identifiant de la classe
      ? (int) $_POST["IDclass"]
      : (int) @$_GET["IDclass"] ;
    $IDedt    = ( @$_POST["IDedt"] )					// type d'edt
      ? (int) $_POST["IDedt"]
      : (int) @$_GET["IDedt"];
    $IDuser   = ( @$_POST["IDuser"] )					// Identifiant de l'utilisateur
      ? (int) $_POST["IDuser"]
      : (int) @$_GET["IDuser"] ;

    if (getParam('edt_visible_par_tous'))
    {
      if ($_SESSION['CnxGrp'] == 1 && $_SESSION['CnxClass'] != $IDclass && $IDedt == 3)
      {
        exit(0);
      }
      if ($_SESSION['CnxGrp'] == 1 && ($IDedt == 1 || $IDedt == 2))
      {
        exit(0);
      }
    }


    if (isset($_POST["showdate"])) $_SESSION["setdate"] = js2PhpTime($_POST["showdate"]);

    function addCalendar($st, $et, $sub, $ade){
      global $mysql_link;
      $ret = array();
      try{
        $sql = "insert into `jqcalendar` (`subject`, `starttime`, `endtime`, `isalldayevent`) values ('"
        .mysqli_real_escape_string($sub)."', '"
        .php2MySqlTime(js2PhpTime($st))."', '"
        .php2MySqlTime(js2PhpTime($et))."', '"
        .mysqli_real_escape_string($ade)."' )";
        //echo($sql);
        if(mysqli_query($mysql_link, $sql)==false){
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($mysql_link);
        }else{
        $ret['IsSuccess'] = true;
        $ret['Msg'] = 'add success';
        $ret['Data'] = mysqli_insert_id($mysql_link);
        }
      } catch(Exception $e) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = $e->getMessage();
      }
      return $ret;
    }

    /***** Récupère le nom d'un groupe *****/
    function getNomByIDgrp($IDgrp)
    {
      global $mysql_link;
      $query  = "SELECT _nom ";
      $query .= "FROM groupe_nom ";
      $query .= "WHERE _IDgrp = $IDgrp ";

      $result = mysqli_query($mysql_link, $query);
      $row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

      return $row[0];
    }

    function addDetailedCalendar($st, $et, $sub, $ade, $dscr, $loc, $color, $tz){
      global $mysql_link;
      $ret = array();
      try{
        $sql = "insert into `jqcalendar` (`subject`, `starttime`, `endtime`, `isalldayevent`, `description`, `location`, `color`) values ('"
          .mysqli_real_escape_string($sub)."', '"
          .php2MySqlTime(js2PhpTime($st))."', '"
          .php2MySqlTime(js2PhpTime($et))."', '"
          .mysqli_real_escape_string($ade)."', '"
          .mysqli_real_escape_string($dscr)."', '"
          .mysqli_real_escape_string($loc)."', '"
          .mysqli_real_escape_string($color)."' )";

        if(mysqli_query($mysql_link, $sql)==false){
          $ret['IsSuccess'] = false;
          $ret['Msg'] = mysqli_error($mysql_link);
        } else {
          $ret['IsSuccess'] = true;
          $ret['Msg'] = 'add success';
          $ret['Data'] = mysqli_insert_id($mysql_link);
        }
      } catch(Exception $e) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = $e->getMessage();
      }
      return $ret;
    }

    function listCalendarByRange($sd, $ed, $type, $mysql_link){
      global $db;
      $IDcentre = ( @$_POST["IDcentre"] )					// Identifiant du centre
        ? (int) $_POST["IDcentre"]
        : (int) (@$_GET["IDcentre"] ? $_GET["IDcentre"] : $_SESSION["CnxCentre"]) ;
      $IDedt    = ( @$_POST["IDedt"] )					// type d'edt
        ? (int) $_POST["IDedt"]
        : (int) @$_GET["IDedt"];
      $IDitem   = ( @$_POST["IDitem"] )					// Identifiant de la salle, catégorie ou groupe classe
        ? (int) $_POST["IDitem"]
        : (int) @$_GET["IDitem"] ;
      $IDclass  = ( @$_POST["IDclass"] )					// Identifiant de la classe
        ? (int) $_POST["IDclass"]
        : (int) @$_GET["IDclass"] ;
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
      // echo $generique;
      /********** Tableau semaine normale **********/
      $ret = array();
      $ret['events'] = array();
      $ret["issort"] =true;
      $ret["start"] = php2JsTime($sd);
      $ret["end"] = php2JsTime($ed);
      $ret['error'] = null;

      if($generique == "modif")
      {

      }
      else
      {
        /********** Tableau semaine patch AJOUT **********/

        $ret1 = array();
        $ret1['events'] = array();
        $ret1["issort"] =true;
        $ret1["start"] = php2JsTime($sd);
        $ret1["end"] = php2JsTime($ed);
        $ret1['error'] = null;
        if($generique != "on")
        {
          try
          {
            switch ( $IDedt )
            {
              case 1 :	// les salles
                $sql  = "SELECT DISTINCTROW edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', ";
                $sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDclass '_IDclass', edt._IDrmpl '_IDrmpl', edt._IDitem '_IDitem', ";
                $sql .= "edt._MatRmpl '_MatRmpl', edt._IDmat '_IDmat', edt._etat '_etat', edt._plus '_plus', edt._group '_group', edt._nosemaine '_nosemaine', edt._annee '_annee' ";
                $sql .= "FROM edt_data edt, campus_data mat, campus_classe classe ";
                $sql .= "WHERE (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";

                // On ne veux voir que les cours visibles
                $sql .= "AND edt._visible = 'O' ";

                // Car souhaite afficher tout lorsque aucun filtre EDT
                if($_SESSION['CnxAdm'] == 255) {
                  $sql .= ($IDitem > 0) ? "AND edt._IDitem = '".$IDitem."' " : "";
                } else {
                  $sql .= "AND edt._IDitem = '".$IDitem."' ";
                }

                if ($type == "week")
                {
                  $dt = new DateTime('December 28th'.date("Y", $sd));
                  if (date("m", $sd) == 12 && date("m", $ed) == 01)
                  {
                    // On récupère le premier lundi de l'année et on affiche les cours avant ce jour
                    $date_temp = 'first monday of '.date('F', '1970-'.date('m', $ed).'-01').' '.date('Y', $ed);
                    $date_temp_2 = new DateTime($date_temp);
                    $end_day = $date_temp_2->format('d') - 1;

                    $weekNumber = $dt->format('W') + 1;
                    // $sql .= ($type == "week") ? "AND (edt._nosemaine = '".$weekNumber."' OR edt._nosemaine = '01') and (edt._annee = '".date("Y", $sd)."' OR edt._annee = '".date("Y", $ed)."') " : "";
                    $sql .= ($type == "week") ? "AND ((edt._nosemaine = '".$weekNumber."' and edt._annee = '".date("Y", $sd)."') OR (edt._nosemaine = '01' and edt._annee = '".date("Y", $ed)."' and edt._jour <= '".$end_day."')) " : "";
                  }
                  else
                  {
                    $sql .= ($type == "week") ? "AND edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' " : "";
                  }
                  // $sql .= ($type == "week") ? "AND edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' " : "";
                }

                if($type == "month") {
                  if(date("W", $sd) <= date("W", $ed)) {
                    $sql .= "AND (edt._nosemaine >= '".date("W", $sd)."' and edt._nosemaine <= '".date("W", $ed)."') and edt._annee = '".date("Y", $sd)."' ";
                  } else {
                    $sql .= "AND (edt._nosemaine >= '".date("W", $sd)."' and edt._nosemaine <= '52') and edt._annee = '".date("Y", $sd)."' ";
                  }
                }
                $sql .= ($type == "day") ? "AND (edt._jour = '".(intval(date("N", $sd))-1) ."' and edt._nosemaine = '".date("W", $sd)."') and edt._annee = '".date("Y", $sd)."' " : "";
                if (date("Y", $sd) == date("Y", $ed)) $sql .= "AND edt._annee = '".date("Y", $sd)."' ";


                if ($_SESSION['CnxGrp'] > 1) $sql .= "and (edt._etat = '1' OR edt._etat = '3' OR edt._etat = '4' OR edt._etat = '5' OR edt._etat = '6') ";
                elseif (getParam('student_see_pending_lessons')) $sql .= "and (edt._etat = '1' OR edt._etat = '3' OR edt._etat = '5') ";
                else $sql .= "and (edt._etat = '1' OR edt._etat = '5') ";


                $sql .= "order by _jour, _debut, _IDclass DESC ";
                break;



              case 2 :	// le personnel
                $sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', ";
                $sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDrmpl '_IDrmpl', edt._IDitem '_IDitem', edt._IDclass '_IDclass', ";
                $sql .= "edt._MatRmpl '_MatRmpl', edt._IDmat '_IDmat', edt._etat '_etat', edt._plus '_plus', edt._group '_group', edt._nosemaine '_nosemaine', edt._annee '_annee' ";
                $sql .= "from edt_data edt, campus_data mat ";
                $sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";
                if ($_SESSION['CnxAdm'] != 255 && !getParam('edt_visible_par_tous')) $IDuser = $_SESSION['CnxID'];

                // On ne veux voir que les cours visibles
                $sql .= "AND edt._visible = 'O' ";

                if($IDuser != 0)
                {
                  $sql .= "AND (edt._ID = '".$IDuser."' OR edt._IDrmpl = '".$IDuser."') ";
                }
                else
                {
                  // Car souhaite afficher tout lorsque aucun filtre EDT
                  if($_SESSION['CnxAdm'] != 255) {
                    $sql .= "and edt._ID = '".$IDuser."' ";
                  }
                }

                $dt = new DateTime('December 28th'.date("Y", $sd));
                if (date("m", $sd) == 12 && date("m", $ed) == 01)
                {
                  // On récupère le premier lundi de l'année et on affiche les cours avant ce jour
                  $date_temp = 'first monday of '.date('F', '1970-'.date('m', $ed).'-01').' '.date('Y', $ed);
                  $date_temp_2 = new DateTime($date_temp);
                  $end_day = $date_temp_2->format('d') - 1;


                  $weekNumber = $dt->format('W') + 1;
                  // $sql .= ($type == "week") ? "AND (edt._nosemaine = '".$weekNumber."' OR edt._nosemaine = '01') and (edt._annee = '".date("Y", $sd)."' OR edt._annee = '".date("Y", $ed)."') " : "";
                  $sql .= ($type == "week") ? "AND ((edt._nosemaine = '".$weekNumber."' and edt._annee = '".date("Y", $sd)."') OR (edt._nosemaine = '01' and edt._annee = '".date("Y", $ed)."' and edt._jour <= '".$end_day."')) " : "";
                }
                else
                {
                  $sql .= ($type == "week") ? "AND edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' " : "";
                }
                $sql .= ($type == "day") ? "and (edt._jour = '".(intval(date("N", $sd))-1) ."' and edt._nosemaine = '".date("W", $sd)."') and edt._annee = '".date("Y", $sd)."' " : "";

                $sql .= "and (edt._etat = '1' OR edt._etat = '3' OR edt._etat = '4' OR edt._etat = '5' OR edt._etat = '6') ";
                $sql .= "order by _jour, _debut, _IDclass DESC ";
                break;



              case 4 :	// Les étudiants
                // Forcer sur l'étudiant pour empêcher de voir d'autres cours, sinon on prend la classe de l'étudiant sélectionné
                if($_SESSION["CnxGrp"] == 1 && !getParam('edt_visible_par_tous')) $IDitem = $_SESSION["CnxClass"];
                else $IDitem = getClassIDByUserID($IDitem);

                if ($IDitem == "" && $_SESSION['CnxGrp'] == 1) $IDitem = $_SESSION['CnxClass'];

                $sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', ";
                $sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDitem '_IDitem', edt._IDclass '_IDclass', edt._IDrmpl '_IDrmpl', edt._IDitem '_IDitem', ";
                $sql .= "edt._MatRmpl '_MatRmpl', edt._IDmat '_IDmat', edt._etat '_etat', edt._plus '_plus', edt._group '_group', edt._nosemaine '_nosemaine', edt._annee '_annee' ";
                $sql .= "from edt_data edt, campus_data mat ";
                $sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";

                // On ne veux voir que les cours visibles
                $sql .= "AND edt._visible = 'O' ";

                $sql .= "and edt._IDcentre = '".$IDcentre."' and edt._IDclass LIKE '%;".$IDitem.";%' ";
                $sql .= ($type == "week") ? "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' " : "";
                if($type == "month") {
                  if(date("W", $sd) <= date("W", $ed)) {
                    $sql .= "and (edt._nosemaine >= '".date("W", $sd)."' and edt._nosemaine <= '".date("W", $ed)."') and edt._annee = '".date("Y", $sd)."' ";
                  } else {
                    $sql .= "and (edt._nosemaine >= '".date("W", $sd)."' and edt._nosemaine <= '52') and edt._annee = '".date("Y", $sd)."' ";
                  }
                }

                $dt = new DateTime('December 28th'.date("Y", $sd));
                if (date("m", $sd) == 12 && date("m", $ed) == 01)
                {
                  // On récupère le premier lundi de l'année et on affiche les cours avant ce jour
                  $date_temp = 'first monday of '.date('F', '1970-'.date('m', $ed).'-01').' '.date('Y', $ed);
                  $date_temp_2 = new DateTime($date_temp);
                  $end_day = $date_temp_2->format('d') - 1;

                  $weekNumber = $dt->format('W') + 1;
                  // $sql .= ($type == "week") ? "AND (edt._nosemaine = '".$weekNumber."' OR edt._nosemaine = '01') and (edt._annee = '".date("Y", $sd)."' OR edt._annee = '".date("Y", $ed)."') " : "";
                  $sql .= ($type == "week") ? "AND ((edt._nosemaine = '".$weekNumber."' and edt._annee = '".date("Y", $sd)."') OR (edt._nosemaine = '01' and edt._annee = '".date("Y", $ed)."' and edt._jour <= '".$end_day."')) " : "";
                }
                else
                {
                  $sql .= ($type == "week") ? "AND edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' " : "";
                }

                $sql .= ($type == "day") ? "and (edt._jour = '".(intval(date("N", $sd))-1) ."' and edt._nosemaine = '".date("W", $sd)."') and edt._annee = '".date("Y", $sd)."' " : "";

                if ($_SESSION['CnxGrp'] == 1) $sql .= "AND edt._IDmat != 123 ";

                if ($_SESSION['CnxGrp'] > 1) $sql .= "and (edt._etat = '1' OR edt._etat = '3' OR edt._etat = '4' OR edt._etat = '5' OR edt._etat = '6') ";
                elseif (getParam('student_see_pending_lessons')) $sql .= "and (edt._etat = '1' OR edt._etat = '3' OR edt._etat = '5') ";
                else $sql .= "and (edt._etat = '1' OR edt._etat = '5') ";


                $sql .= "order by _jour, _debut, _IDclass DESC ";

                // echo $sql;
                break;




              default :	// les classes
                if($_SESSION["CnxGrp"] == 1 && !getParam('edt_visible_par_tous')) { // Forcer sur l'étudiant pour empêcher de voir d'autres cours
                  $IDitem = $_SESSION["CnxClass"];
                }

                if ($IDitem == "" && $_SESSION['CnxGrp'] == 1) $IDitem = $_SESSION['CnxClass'];

                $sql  = "select distinctrow edt._IDdata '_IDdata', edt._jour '_jour', edt._debut '_debut', edt._fin '_fin', ";
                $sql .= "mat._titre '_titre', mat._color '_color', edt._IDx '_IDx', edt._ID '_ID', edt._IDitem '_IDitem', edt._IDclass '_IDclass', edt._IDrmpl '_IDrmpl', edt._IDitem '_IDitem', ";
                $sql .= "edt._MatRmpl '_MatRmpl', edt._IDmat '_IDmat', edt._etat '_etat', edt._plus '_plus', edt._group '_group', edt._nosemaine '_nosemaine', edt._annee '_annee' ";
                $sql .= "from edt_data edt, campus_data mat ";
                $sql .= "where (edt._IDmat = mat._IDmat and mat._lang = '".$_SESSION["lang"]."') ";

                // On ne veux voir que les cours visibles
                $sql .= "AND edt._visible = 'O' ";

                $sql .= "and edt._IDcentre = '".$IDcentre."' ";
                // Car souhaite afficher tout lorsque aucun filtre EDT
                if($_SESSION['CnxAdm'] == 255 || getParam('edt_visible_par_tous')) {
                  $sql .= ($IDitem > 0) ? "and edt._IDclass LIKE '%;".$IDitem.";%' " : "";
                } else {
                  $sql .= "and edt._IDclass LIKE '%;".$IDitem.";%' ";
                }
                $sql .= ($type == "week") ? "and edt._nosemaine = '".date("W", $sd)."' and edt._annee = '".date("Y", $sd)."' " : "";


                if($type == "month") {
                  if(date("W", $sd) <= date("W", $ed)) {
                    $sql .= "and (edt._nosemaine >= '".date("W", $sd)."' and edt._nosemaine <= '".date("W", $ed)."') and edt._annee = '".date("Y", $sd)."' ";
                  } else {
                    $sql .= "and (edt._nosemaine >= '".date("W", $sd)."' and edt._nosemaine <= '52') and edt._annee = '".date("Y", $sd)."' ";
                  }
                }

                $sql .= ($type == "day") ? "and (edt._jour = '".(intval(date("N", $sd))-1) ."' and edt._nosemaine = '".date("W", $sd)."') and edt._annee = '".date("Y", $sd)."' " : "";

                if ($_SESSION['CnxGrp'] == 1) $sql .= "AND edt._IDmat != 123 ";

                if ($_SESSION['CnxGrp'] == 1 && !getParam('edt_visible_par_tous'))
                {
                  $query  = "SELECT `_IDclass` ";
                  $query .= "FROM `user_id` ";
                  $query .= "WHERE `_ID` = '".$_SESSION['CnxID']."' ";
                  $result = mysqli_query($mysql_link, $query);
                  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                    $classIDforSql = $row[0];
                  }
                  $sql .= "AND edt._IDclass LIKE ';".$classIDforSql.";' ";
                }

                if ($_SESSION['CnxGrp'] > 1) $sql .= "and (edt._etat = '1' OR edt._etat = '3' OR edt._etat = '4' OR edt._etat = '5' OR edt._etat = '6') ";
                elseif (getParam('student_see_pending_lessons')) $sql .= "and (edt._etat = '1' OR edt._etat = '3' OR edt._etat = '5') ";
                else $sql .= "and (edt._etat = '1' OR edt._etat = '5') ";


                $sql .= "order by _jour, _debut, _IDclass DESC ";
                break;
            }
// echo $sql.PHP_EOL;
            $handle = mysqli_query($mysql_link, $sql);
            $date_debut = php2MySqlTime($sd);

            while ($row = mysqli_fetch_object($handle))
            {
              // Traitements convertion date time
              $date_debut = php2MySqlTime($sd);
              $date_event = date($date_debut); // objet date
              if($type == "day") {
                $date_event = strtotime(date("Y-m-d", strtotime($date_event)));
              } else if($type == "month") {
                $week_array = getStartAndEndDate(date("W", $sd), date("Y", $sd));
                $date_firstdaymonth = strtotime($week_array["week_start"]);

                $sem_debut = date("W", strtotime($date_debut));
                $sem_event = $row->_nosemaine;
                $sem_diff = $sem_event - $sem_debut;

                $date_event = strtotime(date("Y-m-d", $date_firstdaymonth) . " +".$row->_jour." day +$sem_diff week"); // ajout du nb de jours + semaine
              } else {
                $date_event = strtotime(date("Y-m-d", strtotime($date_event)) . " +".$row->_jour." day"); // ajout du nb de jours
              }
              $date_event = date("Y-m-d", $date_event); // timesptamp en string

              $date_event_year = date('Y', strtotime($date_event));
              $date_event_month = date('m', strtotime($date_event));

              $noSemaine   = $row->_nosemaine;
              $noYear      = $row->_annee;
              $noDay       = $row->_jour;

              // Traitements convertion date time
              if ($noSemaine < 10) $week_temp = "0".$noSemaine;
              else $week_temp = $noSemaine;
              $date_debut_2 = php2MySqlTime(strtotime($noYear.'W'.$week_temp));

              $date_event_2 = date($date_debut_2); // objet date
              $date_event_2 = strtotime(date("Y-m-d", strtotime($date_event_2)) . " +".$noDay." day"); // ajout du nb de jours

              // On teste si les dates correspondent
              $date_correspond = 1;
              if ($date_event_year != date('Y', $date_event_2) || abs($date_event_month - date('m', $date_event_2)) > 2) $date_correspond = 0;

              // Heure de début
              $heure_debut_event = $row->_debut;

              // Heure de fin
              $heure_fin_event = $row->_fin;

              // Verification si quelque chose à faire
              $nbdev = 0;
              $textdev = '';
              if($generique != 'on')
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

                  if($row2->_texte != "")
                  {
                    $textdev = $row2->_texte;
                    $nbdev++;
                  }
                }
              }


              $IDrmpl = $row->_IDrmpl;

              // Si matière remplaçante
              if($row->_MatRmpl != 0) $MatRmpl = $row->_MatRmpl;
              else $MatRmpl = $row->_IDmat;

              // Nom de la matière
              $querymat  = "select _titre, _color from campus_data ";
              $querymat .= "where _IDmat = ".$MatRmpl." AND _lang = '".$_SESSION["lang"]."'";

              $resultmat = mysqli_query($mysql_link, $querymat);
              $rowsmat    = ( $resultmat ) ? mysqli_fetch_row($resultmat) : 0 ;

              $queryTitre = "SELECT `_text` FROM `edt_data` WHERE `_IDdata` = ".$row->_IDdata." ";
              $resultTitre = mysqli_query($mysql_link, $queryTitre);
              $rowsTitre    = ( $resultTitre ) ? mysqli_fetch_row($resultTitre) : 0 ;


              $query_exam = "SELECT edt_data.`_ID_examen`, campus_examens.`_type`, campus_examens.`_nom`, campus_examens.`_oral` FROM `edt_data` INNER JOIN campus_examens ON edt_data.`_ID_examen` = campus_examens.`_ID_exam` WHERE `_IDdata` = ".$row->_IDdata." ";
              $result_exam = mysqli_query($mysql_link, $query_exam);
              $rows_exam    = ( $result_exam ) ? mysqli_fetch_row($result_exam) : 0 ;

              // On récupère l'intitulé de l'examen
              $examType = getParam('type-examen');
              $examType = json_decode($examType, TRUE);

              if ($MatRmpl == 122)
              {
                if ($rowsTitre[0] != "") $matName = "Agenda - ".$rowsTitre[0];
                else $matName = "Agenda";
              }
              elseif ($rows_exam[0] != 0 and $rows_exam[0] != NULL) $matName = "UV - ".$examType[$rows_exam[1]]." - ".$rows_exam[2]." - ".$rowsmat[0];
              else $matName = $rowsmat[0];


              $querymat2  = "select _etat from edt_data ";
              $querymat2 .= "where _IDx = ".$row->_IDx." ";

              $resultmat2 = mysqli_query($mysql_link, $querymat2);
              $rowsmat2    = ( $resultmat2 ) ? mysqli_fetch_row($resultmat2) : 0 ;

              $color_back = $rowsmat[1];

              // Nom de l'enseignant
              $data_user = $db->getRow("SELECT _name as name, _fname as fname FROM user_id WHERE _ID = ?i ", $row->_ID);
              if ($data_user) {
                if ($IDrmpl != 0)
                {
                  $data_second_user = $db->getRow("SELECT _name as name, _fname as fname FROM user_id WHERE _ID = ?i ", $IDrmpl);
                  $userName = strtoupper($data_user->name).' '.$data_user->fname." - ".strtoupper($data_second_user->name)." ".$data_second_user->fname;
                }
                else $userName = strtoupper($data_user->name).' '.$data_user->fname;
              }


              // On récupère le nom de la salle
              $salle = getRoomNameByID($row->_IDitem);
              // Si le nom de la salle contiens 'visio' alors on donne la possibilité de faire une visio
              if (strpos($salle, 'visio') !== false) $visio = true; else $visio = false;

              // Si filtre EDT sur 'Salle'
              if($IDedt == 1)
              {
                if($row->_IDclass != 0 || $row->_IDclass != ';') // Regarde si cours classe ou particulier
                {
                  $array_class = @explode(';', $row->_IDclass);
                  $nom_class = '';

                  foreach($array_class as $val)
                  {
                    if($val != '')
                    {
                      // Nom de la classe
                      $class_name_temp = getClassNameByClassID($val);
                      if($class_name_temp != '') $nom_class .= stripslashes($class_name_temp).'<br />';
                    }
                  }
                }
                // Si cours de groupe
                elseif($row->_group) $nom_class = stripslashes(getNomByIDgrp($row->_group));
                else
                {
                  $queryuser2  = "select _name, _fname from user_id ";
                  $queryuser2 .= "where _ID = ".$row->_plus;

                  $resultuser2 = mysqli_query($mysql_link, $queryuser2);
                  $rowsuser2    = ( $resultuser2 ) ? mysqli_fetch_row($resultuser2) : 0 ;
                  $nom_class = stripslashes(strtoupper($rowsuser2[0])." ".$rowsuser2[1]);
                }

                $titre_cours = '<b>'.stripslashes($matName).'</b> <br />'.stripslashes($userName).' <br /><i>'.$nom_class.'</i>';
              }
              // Si filtre EDT sur 'Classe'
              elseif($IDedt == 3) $titre_cours = '<b>'.stripslashes($matName).'</b> <br />'.stripslashes(@$userName).' <br />'.stripslashes($salle);
              else
              {
                if($row->_IDclass != 0 || $row->_IDclass != ';') // Regarde si cours classe ou particulier
                {
                  $array_class = @explode(';', $row->_IDclass);
                  $nom_class = '';

                  foreach($array_class as $val)
                  {
                    if($val != '')
                    {
                      // Nom de la classe
                      $temp_class_name = getClassNameByClassID($val);
                      if($temp_class_name != '') $nom_class .= stripslashes($temp_class_name).'<br />';
                    }
                  }
                }
                // Si cours de groupe
                else if($row->_group) $nom_class = stripslashes(getNomByIDgrp($row->_group));
                else
                {
                  $data_user_temp_2 = $db->getRow("SELECT `_name` as `name`, `_fname` as `fname` FROM `user_id` WHERE `_ID` = ?i ", $row->plus);
                  $nom_class = stripslashes(strtoupper($data_user_temp_2->name).' '.$data_user_temp_2->fname);
                }

                if($row->_etat == 2) $titre_cours = '<img src="./images/trash.png"> <b>'.stripslashes($matName).'</b> <br />'.stripslashes(strtoupper($data_user->name).' '.$data_user->fname).' <br />'.stripslashes($salle);
                else
                {
                  $IDrmpl = $row->_IDrmpl;

                  if ($IDrmpl != 0)
                  {
                    $data_second_user = $db->getRow("SELECT _name as name, _fname as fname FROM user_id WHERE _ID = ?i ", $IDrmpl);
                    $userName = strtoupper($data_user->name).' '.$data_user->fname.' - '.strtoupper($data_second_user->name).' '.$data_second_user->fname;
                  }
                  else $userName = strtoupper($data_user->name).' '.$data_user->fname;

                  $titre_cours = '<b>'.stripslashes($matName).'</b>';
                  if (stripslashes($userName)) $titre_cours .= '<br />'.stripslashes($userName);
                  if ($nom_class) $titre_cours .= '<br />'.substr($nom_class, 0, -6);
                  if (stripslashes($salle)) $titre_cours .= '<br />'.stripslashes($salle);
                }
              }

              // Si les dates correspondent, alors on affiche l'évènement
              if ($date_correspond)
              {
                $ret1['events'][] = array(
                  $row->_IDx,
                  $titre_cours,
                  php2JsTime(mySql2PhpTime($date_event.' '.$heure_debut_event)),
                  php2JsTime(mySql2PhpTime($date_event.' '.$heure_fin_event)),
                  0,
                  0, //more than one day event
                  //$row->InstanceType,
                  0,//Recurring event,
                  // $rowsmat[1],
                  $color_back,
                  1,//editable
                  '',
                  '',//$attends
                  $row->_IDdata,
                  $nbdev,
                  '',
                  $isModif,
                  $isDevoir,
                  $row->_ID,
                  $_SESSION['CnxID'],
                  $row->_etat,
                  '',
                  '',
                  '',
                  $visio
                );
              }
            }
          }
          catch(Exception $e) // Ne peux pas être sur une seule ligne!!!
          {
            $ret1['error'] = $e->getMessage();
          }
        }


        $ret_json = $ret;

        // Puis les ajouts
        foreach($ret1["events"] as $a1) $ret_json["events"][] = $a1;
      }

      if($generique != "modif")
      {
        // Trie pour ordre croissant
        $ret_json_final = array();
        $ret_json_final['events'] = array();
        $ret_json_final['issort'] =true;
        $ret_json_final['start'] = php2JsTime($sd);
        $ret_json_final['end'] = php2JsTime($ed);
        $ret_json_final['error'] = null;
        $tab_trie = array();
        $date = array();
        $id = array();

        foreach($ret_json['events'] as $key => $val) $tab_trie[] = array('id' => $key, 'date' => intval(js2PhpTime($val[2])));

        // Obtient une liste de colonnes
        foreach ($tab_trie as $key => $val) {
          $id[$key] = $val['id'];
          $date[$key] = $val['date'];
        }

        array_multisort($date, SORT_ASC, SORT_NUMERIC, $id, SORT_ASC, $tab_trie);

        foreach($tab_trie as $key => $val) $ret_json_final['events'][] = $ret_json['events'][$val['id']];
      }

      // echo "<pre>";
      // print_r($ret_json_final);
      // echo "</pre>";
      return $ret_json_final;
    }

    function listCalendar($day, $type, $mysql_link){
      $phpTime = js2PhpTime($day);
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
      return listCalendarByRange(@$st, @$et, $type, $mysql_link);
    }

    function updateCalendar($id, $st, $et, $generique){
      global $mysql_link;
      $ret = array();
      $tab = DateCalendarToPmt($st, $et);

      try
      {
        $start  = date('Y-m-d H:i:s', js2PhpTime($st));
        $end    = date('Y-m-d H:i:s', js2PhpTime($et));
        $d_start    = new DateTime($start);
        $d_end      = new DateTime($end);
        $duree = date('H:i:s', js2PhpTime($et));

        if($generique == "on")
        {
          // Vérifie si une demande devais être envoyée à l'intervenant et si c'est le cas, alors on renvois la demande (car le cours es modifié...)
          $query2 = "SELECT `_etat` FROM `edt_data` WHERE `_IDx` = '".$IDx."' ";
          $result2 = mysqli_query($mysql_link, $query2);
          while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
            if ($row2[0] != 1) $workflow = 3;
            else $workflow = 1;
          }

          $sql = "update `edt_data` set _etat = '".$workflow."', _fin = '$duree', _jour = '".$tab["jour"]."', _debut = '".date('H:i:s', js2PhpTime($st))."', _annee = '".$tab["annee"]."' where `_IDx`=" . $id;

          if(mysqli_query($mysql_link, $sql)==false){
            $ret['IsSuccess'] = false;
            $ret['Msg'] = mysqli_error($mysql_link);
          } else {
            $ret['IsSuccess'] = true;
            $ret['Msg'] = 'Succefully';


            if (isset($_SESSION['temp_mail_edt']) && $_SESSION['temp_mail_edt'] != '')
            {
              $datas = json_decode($_SESSION['temp_mail_edt'], TRUE);
$ret['test'] = $datas['to'];
              switch ($datas['to']) {
                case 'both': sendCheckMailToTeacherAndStudents($id, 'update'); break;
                case 'teacher': sendCheckMailToTeacher($id, 'update'); break;
                case 'students': sendCheckMailToStudents($id, 'update'); break;
              }
            }



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
            $texte = 		 $row->_text;
            $ID_pma = 	 $row->_ID_pma;
          }


          // Vérifie si une demande devais être envoyée à l'intervenant et si c'est le cas, alors on renvois la demande (car le cours est modifié...)
          $query2 = "SELECT `_etat` FROM `edt_data` WHERE `_IDx` = '".$id."' ";
          $result2 = mysqli_query($mysql_link, $query2);
          while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
            if ($row2[0] != 1) $workflow = 3;
            else $workflow = 1;
          }

          $sql = "update `edt_data` set _etat = '".$workflow."', _fin = '$duree', _jour = '".$tab["jour"]."', _debut = '".date('H:i:s', js2PhpTime($st))."', _annee = '".$tab["annee"]."' where `_IDx`=" . $id;

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



            if (isset($_SESSION['temp_mail_edt']) && $_SESSION['temp_mail_edt'] != '')
            {
              $datas = json_decode($_SESSION['temp_mail_edt'], TRUE);
              switch ($datas['to']) {
                case 'both': sendCheckMailToTeacherAndStudents($id, 'update', "session"); break;
                case 'teacher': sendCheckMailToTeacher($id, 'update', "session"); break;
                case 'students': sendCheckMailToStudents($id, 'update', "session"); break;
              }
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

    function updateDetailedCalendar($id, $st, $et, $sub, $ade, $dscr, $loc, $color, $tz) {
      global $mysql_link;
      $ret = array();
      try {
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
        if(mysqli_query($mysql_link, $sql)==false) {
          $ret['IsSuccess'] = false;
          $ret['Msg'] = mysqli_error($mysql_link);
        } else {
          $ret['IsSuccess'] = true;
          $ret['Msg'] = 'Succefully';
        }
      } catch(Exception $e) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = $e->getMessage();
      }
      return $ret;
    }

    function removeCalendar($id, $generique, $st)
    {
      global $mysql_link;
      $ret = array();
      try
      {
        $sql = "DELETE FROM `edt_data` WHERE `_IDx`= " . $id;

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
      catch(Exception $e)
      {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = $e->getMessage();
      }
      return $ret;
    }

    function removeCalendarDay($generique, $st, $ids){
      global $mysql_link;
      $ret = array();
      $tab_ids = explode(";", $ids);

      foreach($tab_ids as $id)
      {
        if($id != "")
        {
          try
          {
            $sql = "DELETE FROM `edt_data` WHERE `_IDx`= " . $id;

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
          catch(Exception $e)
          {
            $ret['IsSuccess'] = false;
            $ret['Msg'] = $e->getMessage();
          }
        }
      }
      return "OK";
    }



    header('Content-type:text/javascript;charset=UTF-8');
    $method = $_GET["method"];
    switch ($method) {
      case "add":
        // $ret = addCalendar($_POST["CalendarStartTime"], $_POST["CalendarEndTime"], $_POST["CalendarTitle"], $_POST["IsAllDayEvent"]);
        break;
      case "list":
        $ret = listCalendar(@$_POST["showdate"], @$_POST["viewtype"], $mysql_link);
        break;
      case "update":
        $ret = updateCalendar($_POST["calendarId"], $_POST["CalendarStartTime"], $_POST["CalendarEndTime"], @$_GET["generique"]);
        break;
      case "remove":
        $ret = removeCalendar( $_POST["calendarId"], @$_GET["generique"], $_POST["CalendarStartTime"]);
        break;
      case "removeDay":
        $ret = removeCalendarDay( @$_GET["generique"], $_POST["CalendarStartTime"], @$_GET["ids"]);
        break;
      case "adddetails":
        $st = $_POST["stpartdate"] . " " . $_POST["stparttime"];
        $et = $_POST["etpartdate"] . " " . $_POST["etparttime"];
        if(isset($_GET["id"])) {
          $ret = updateDetailedCalendar($_GET["id"], $st, $et,
          $_POST["Subject"], isset($_POST["IsAllDayEvent"])?1:0, $_POST["Description"],
          $_POST["Location"], $_POST["colorvalue"], $_POST["timezone"]);
        } else {
          $ret = addDetailedCalendar($st, $et,
          $_POST["Subject"], isset($_POST["IsAllDayEvent"])?1:0, $_POST["Description"],
          $_POST["Location"], $_POST["colorvalue"], $_POST["timezone"]);
        }
        break;
    }
    echo json_encode($ret);
  }
?>
