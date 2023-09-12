<?php
session_start();
// error_reporting(E_ALL);
//    ini_set("display_errors", 1);
// include_once('../../../config.php');
//
// include_once('../../../php/dbconfig.php');
// include_once('../../../php/functions.php');
// include_once('../../fonction/parametre.php');

//
//
// $post = array('start', 'end');
// foreach ($post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];


// print_r($db);
// On récupère les évènements grace à la classe Calendar
include __DIR__.'/../../../globals.php';
include __DIR__.'/../../class/loader.php';
$cal = new Calendar;
// // print_r($_POST);
// print_r($db);
//
echo $cal->getEvents($start, $end);

?>
