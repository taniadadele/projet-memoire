<?php

// session_start();
//
// require_once "../../config.php";
// require_once "../../include/sqltools.php";
// require_once "../../php/functions.php";
// // require_once "../relation.php";
// include ('protection_input.php');
// $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
// require("parametre.php");
// require("relation.php");
//
// require("pagination.php");
//
// require("edt.php");
//
// require("auth_tools.php");
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();
include_once("../../php/dbconfig.php");
include_once("../../php/functions.php");
include_once("../fonction/parametre.php");

switch ($_GET['action']) {
  case 'saveMenuShowMode':
    $showMode = $_GET['menuShowMode'];
    setUserParam($_SESSION['CnxID'], 'menuShowMode', $showMode, 'Mode d\'affichage du menu de gauche');

    break;

  default:
    // code...
    break;
}

?>
