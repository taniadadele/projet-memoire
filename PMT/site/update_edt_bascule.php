<?php
/**
 * Script pour basculer les évènements de l'EDT sur la promo inférieure
 * tout en gardant la même matière.
 * Cette bascule se fait à partir de la date en configuration 'dateBasculeAnnee'
 *
 */

session_start();
require_once "config.php";
require_once "include/sqltools.php";
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);
require_once 'include/fonction.php';

if ($_SESSION['CnxAdm'] == 255)
{
  $date_bascule = changeDateTypeFromFRToEN(getParam('dateBasculeAnnee'));
  echo '<b>DATE DE BASCULE:</b> '.date('d/m/Y', strtotime($date_bascule)).'<br>';

  // On formate la date
  $date_bascule       = strtotime($date_bascule);
  $date_annee         = date('Y', $date_bascule);
  $date_nosemaine     = date('W', $date_bascule);
  $date_jour          = date('N', $date_bascule);

  $query = "SELECT _IDx, _IDclass FROM `edt_data` WHERE _annee = '".$date_annee."' AND ((_nosemaine = '".$date_nosemaine."' AND _jour >= '".$date_jour."') OR _nosemaine > '".$date_nosemaine."') ";
  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $IDx = $row[0];
    $class_list_string = ';';

    foreach (explode(';', $row[1]) as $key => $value)
      if ($value != '')
        $class_list_string .= getClassIDByNiveauNumber(getClassNiveauByClassID($value) - 1).';';

    $query_update = "UPDATE `edt_data` SET _IDclass = '".$class_list_string."' WHERE _IDx = '".$IDx."' ";
    mysqli_query($mysql_link, $query_update) or die('Erreur SQL !<br>'.$query_update.'<br>'.mysqli_error($mysql_link));
  }


  echo 'Bascule OK !';


}
