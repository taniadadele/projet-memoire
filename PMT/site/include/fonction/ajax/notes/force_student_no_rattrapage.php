<?php
session_start();

require_once "../../../../config.php";
require_once "../../../../include/sqltools.php";
require_once "../../../../php/functions.php";
// require_once "../relation.php";
include ('../../protection_input.php');
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
require("../../parametre.php");
require("../../relation.php");

require("../../pagination.php");

require("../../edt.php");

require("../../auth_tools.php");




if (isUserConnected())
{



  // On récupère les éléments en post
  $post_elements = array('IDeleve', 'year', 'period', 'mat', 'action');
  foreach ($post_elements as $value) $$value = addslashes($_POST[$value]);

  // Si on veux forcer la validation de la matière/pole
  if ($action == 'setForcedRattrapage') {
    // On crée la requête
    $datas = array(
      '_IDeleve'         => $IDeleve,
      '_year'            => $year,
      '_period'          => $period
    );

    if (substr($mat, 0, 2) == 'p_') $datas['_IDpole'] = substr($mat, 2);
    else $datas['_ID_pma'] = $mat;

    $query = 'INSERT INTO `notes_rattrapage` ';
    $query .= ' (';
    foreach ($datas as $key => $value) {
      $query .= '`'.$key.'`,';
    }
    $query = substr($query, 0, -1).') VALUES (';
    foreach ($datas as $key => $value) {
      if (is_numeric($value)) $query .= ''.$value.',';
      else $query .= '"'.$value.'",';
    }
    $query = substr($query, 0, -1).')';
    mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

  }
  // Si on veux supprimer la validation forcée de la matière/pole
  elseif ($action == 'removeForcedRattrapage') {
    if (substr($mat, 0, 2) == 'p_') $query = 'DELETE FROM notes_rattrapage WHERE _matt_validation = 1 AND _IDeleve = '.$IDeleve.' AND _year = '.$year.' AND _period = '.$period.' AND _IDpole = '.substr($mat, 2);
    else $query = 'DELETE FROM notes_rattrapage WHERE _matt_validation = 1 AND _IDeleve = '.$IDeleve.' AND _year = '.$year.' AND _period = '.$period.' AND _ID_pma = '.$mat;
    mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }
  // Si on veux vérifier si la validation de la matière/pole n'a pas déjà été forcée
  elseif ($action == 'checkIfAlreadyForced') {
    if (substr($mat, 0, 2) == 'p_') $query = 'SELECT * FROM notes_rattrapage WHERE _matt_validation = 1 AND _IDeleve = '.$IDeleve.' AND _year = '.$year.' AND _period = '.$period.' AND _IDpole = '.substr($mat, 2).' LIMIT 1';
    else $query = 'SELECT * FROM notes_rattrapage WHERE _matt_validation = 1 AND _IDeleve = '.$IDeleve.' AND _year = '.$year.' AND _period = '.$period.' AND _ID_pma = '.$mat.' LIMIT 1';
    $result = mysqli_query($mysql_link, $query);
    $validated = 'false';
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      $validated = 'true';
    }
    echo $validated;
  }



}

?>
