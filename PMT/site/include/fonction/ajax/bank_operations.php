<?php
session_start();

require_once "../../../config.php";
require_once "../../../include/sqltools.php";
require_once "../../../php/functions.php";
// require_once "../relation.php";
include ('../protection_input.php');
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
require("../parametre.php");
require("../relation.php");

require("../pagination.php");

require("../edt.php");

require("../auth_tools.php");


if (isUserConnected())
{

  if (isset($_GET['action'])) $action = addslashes(stripslashes($_GET['action']));
  elseif (isset($_POST['action'])) $action = addslashes(stripslashes($_POST['action']));
  else $action = "";
  if (isset($_GET['idData'])) $idData = addslashes(stripslashes($_GET['idData']));
  elseif (isset($_POST['idData'])) $idData = addslashes(stripslashes($_POST['idData']));
  else $idData = "";

  if (isset($_GET['date_edit'])) $date_edit = addslashes(stripslashes($_GET['date_edit']));
  elseif (isset($_POST['date_edit'])) $date_edit = addslashes(stripslashes($_POST['date_edit']));
  else $date_edit = "";
  if (isset($_GET['libelle_edit'])) $libelle_edit = addslashes(stripslashes($_GET['libelle_edit']));
  elseif (isset($_POST['libelle_edit'])) $libelle_edit = addslashes(stripslashes($_POST['libelle_edit']));
  else $libelle_edit = "";
  if (isset($_GET['student_edit'])) $student_edit = addslashes(stripslashes($_GET['student_edit']));
  elseif (isset($_POST['student_edit'])) $student_edit = addslashes(stripslashes($_POST['student_edit']));
  else $student_edit = "";
  if (isset($_GET['price_edit'])) $price_edit = addslashes(stripslashes($_GET['price_edit']));
  elseif (isset($_POST['price_edit'])) $price_edit = addslashes(stripslashes($_POST['price_edit']));
  else $price_edit = "";
  if (isset($_GET['ID_edit'])) $ID_edit = addslashes(stripslashes($_GET['ID_edit']));
  elseif (isset($_POST['ID_edit'])) $ID_edit = addslashes(stripslashes($_POST['ID_edit']));
  else $ID_edit = "";

  // ---------------------------------------------------------------------------
  // Fonction: Supprime une donnée des relevés bancaires
  // IN:		   L'ID de la donnée (INT)
  // OUT: 		 -
  // ---------------------------------------------------------------------------
  if ($action == "removeData")
  {
    if ($_SESSION['CnxAdm'] == 255)
    {
      $query = "DELETE FROM `bank_data` WHERE `_ID` = '".$idData."' ";
      mysql_query($query, $mysql_link) or die('Erreur SQL !<br>'.$query.'<br>'.mysql_error());
    }

  }


  // ---------------------------------------------------------------------------
  // Fonction: Renvois le formulaire de modification d'une donnée
  // IN:		   L'ID de la donnée (INT)
  // OUT: 		 Le formulaire (TEXT)
  // ---------------------------------------------------------------------------
  if ($action == "getEditFormData" && $_SESSION['CnxAdm'] == 255)
  {
    // On récupère la liste des élèves:
    $query = "SELECT _name, _fname, _ID, _IDclass FROM user_id WHERE _adm = 1 AND _IDclass != '0' AND _IDgrp = '1' ORDER BY _IDclass DESC, _name ASC ";
    $result = mysql_query($query);
    $list_eleves = Array();
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $tempArray = Array();
      $tempArray['ID'] = $row[2];
      $tempArray['name'] = $row[0];
      $tempArray['fname'] = $row[1];
      $tempArray['promo'] = $row[3];
      $list_eleves[] = $tempArray;
    }

    $query = "SELECT _ID, _date, _libele, _price, _IDeleve FROM bank_data WHERE _ID = '".$idData."' LIMIT 1 ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      echo "<label for=\"date_edit\"><b>Date:</b><br>";
        echo "<input id=\"date_edit\" type=\"date\" value=\"".$row[1]."\">";
      echo "</label><br>";

      echo "<label for=\"libelle_edit\" style=\"width: 97%;\"><b>Libellé:</b><br>";
        echo "<input id=\"libelle_edit\" type=\"text\" style=\"width: 100%;\" value=\"".$row[2]."\">";
      echo "</label>";

      echo "<label for=\"student_edit\" style=\"width: 97%;\"><b>Élève:</b><br>";
        echo "<select id=\"student_edit\">";
          foreach ($list_eleves as $key => $value) {
            if ($value['promo'] != $oldPromo || !isset($oldPromo))
            {
              if (isset($oldPromo)) echo "</optgroup>";
              echo "<optgroup label=\"".getClassNameByClassID($value['promo'])."\">";
              $oldPromo = $value['promo'];

            }
            if ($value['ID'] == $row[4]) $selected = "selected";
            else $selected = "";
            echo "<option value=\"".$value['ID']."\" ".$selected.">".$value['name']." ".$value['fname']."</option>";
          }
        echo "</select>";

      echo "</label>";

      echo "<label for=\"price_edit\"><b>Montant:</b><br>";
        echo "<input type=\"number\" id=\"price_edit\" step=\"0.01\" value=\"".$row[3]."\">";
      echo "</label>";

      echo "<input type=\"hidden\" id=\"ID_edit\" value=\"".$row[0]."\">";
    }
  }

  if ($action == "applyEditFormData" && $_SESSION['CnxAdm'] == 255)
  {
    $query = "UPDATE bank_data SET _date = '".$date_edit."', _libele = '".$libelle_edit."', _IDeleve = '".$student_edit."', _price = '".$price_edit."' WHERE _ID = '".$ID_edit."' ";
    mysql_query($query, $mysql_link) or die('Erreur SQL !<br>'.$query.'<br>'.mysql_error());
    echo $ID_edit;
  }

  if ($action == "applyNewFormData" && $_SESSION['CnxAdm'] == 255)
  {
    // Correction format de date si format français (si '/' présent)
    if (strpos($date_edit, '/') !== false) {
      $temp = explode('/', $date_edit);
      $date_edit = $temp[2].'-'.$temp[1].'-'.$temp[0];
    }

    $date_edit = date('Y-m-d', strtotime($date_edit));
    $attr = array('prom_name' => getClassNameByUserID($student_edit));
    $query = "INSERT INTO bank_data SET _date = '".$date_edit."', _libele = '".$libelle_edit."', _IDeleve = '".$student_edit."', _price = '".$price_edit."', _attr = '".json_encode($attr)."' ";
    mysql_query($query, $mysql_link) or die('Erreur SQL !<br>'.$query.'<br>'.mysql_error());
    $row_id = mysql_insert_id();
    echo "<tr id=\"tr_".$row_id."\">";
      echo "<td id=\"date_".$row_id."\">".date('d/m/Y', strtotime($date_edit))."</td>";
      echo "<td id=\"libelle_".$row_id."\">".$libelle_edit."</td>";
      echo "<td id=\"user_name_".$row_id."\">".getUserNameByID($student_edit)."</td>";
      echo "<td id=\"user_promo_".$row_id."\" style=\"width: 12%;\">".getClassNameByUserID($student_edit)."</td>";
      echo "<td id=\"price_".$row_id."\" style=\"text-align: right;\">".$price_edit." €</td>";

      if ($_SESSION['CnxAdm'] == 255)
      {
        echo "<td>";
          echo "<a href=\"#\" onclick=\"editData('".$row_id."');\"><i class=\"fa fa-pencil\"></i></a>&nbsp;";
          echo "<a href=\"#\" onclick=\"removeData('".$row_id."');\"><i class=\"fa fa-trash\"></i></a>";

        echo "</td>";
      }

    echo "</tr>";
  }


  if ($action == "getNewData")
  {
    $query = "SELECT _date, _libele, _IDeleve, _price FROM bank_data WHERE _ID = '".$idData."' ";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $newArray = array();
      $newArray['date'] = date('d/m/Y', strtotime($row[0]));
      $newArray['libelle'] = $row[1];
      $newArray['user_name'] = getUserNameByID($row[2]);
      $newArray['price'] = $row[3];
      $newArray['user_promo'] = getClassNameByUserID($row[2]);
      echo json_encode($newArray);
    }
  }




}
?>
