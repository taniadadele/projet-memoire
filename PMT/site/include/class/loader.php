<?php
// Chargement des classes PHP

$classes_to_load = array('alert', 'mysql', 'calendar');

foreach ($classes_to_load as $class) {
  require $class.'.class.php';
}


?>
