<?php
session_start();

require_once "../../../config.php";
require_once "../../../include/sqltools.php";
require_once "../../../php/functions.php";
// require_once "../relation.php";
include ('../protection_input.php');
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
// require("../parametre.php");
// require("../relation.php");
//
// require("../pagination.php");
//
// require("../edt.php");
require("../change_year.php");

require("../../fonction.php");

if ($_SESSION['CnxAdm'] == 255 && $_SESSION['CnxGrp'] == 4)
{
  changeToNextYear();
}





?>
