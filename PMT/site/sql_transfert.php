<?php
header('Content-type: text/html; charset=utf-8');
session_start();
// echo 'soqdhfjlksj';
require_once "page_session.php";
include_once("php/dbconfig.php");
include_once("php/functions.php");
// echo 'ocucou';
if ($_GET['token'] == md5('followTheWhiteRabbit'))
{
  // $query = stripslashes($_GET['sql']);
  $query = $_GET['sql'];

// echo $query.'<br>';




  $query = str_replace('___20___', ' ', $query);
  $query = str_replace('___21___', " ", $query); // \r
  $query = str_replace('___22___', " ", $query); // \n
  $query = str_replace('___23___', "<br />", $query);
  $query = str_replace('___24___', "<i>", $query);
  $query = str_replace('___25___', "</i>", $query);
  $query = str_replace('___26___', "<b>", $query);
  $query = str_replace('___27___', "</b>", $query);
  $query = str_replace('___28___', " ", $query); // \n\r
  $query = str_replace('___29___', " ", $query); // \r\n
  $query = str_replace('___30___', "é", $query);
  $query = str_replace('___31___', "è", $query);
  $query = str_replace('___32___', "à", $query);
  $query = str_replace('___33___', "ç", $query);
  $query = str_replace('___34___', "<", $query);
  $query = str_replace('___35___', ">", $query);
  $query = str_replace('___36___', "!", $query);
  $query = str_replace('___37___', "=", $query);

  $query = str_replace('___38___', "ê", $query);
  $query = str_replace('___39___', "â", $query);
  $query = str_replace('___40___', "ô", $query);
  $query = str_replace('___41___', "û", $query);
  $query = str_replace('___42___', "î", $query);
  $query = str_replace('___43___', "'", $query);
  $query = str_replace('___44___', '"', $query);

  $query = str_replace('___45___', "\n", $query);
  $query = str_replace('___46___', "\r", $query);
  $query = str_replace('___47___', "\n\r", $query);
  $query = str_replace('___48___', "\r\n", $query);

  $query = str_replace('___49___', '’', $query);
  $query = str_replace('___50___', '`', $query);
  $query = str_replace('___51___', ',', $query);
  $query = str_replace('___52___', "\t", $query);

  $query = html_entity_decode($query);

  // echo '-----'.$query."------<br>";
  @mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  echo "OK<br>";
}
else echo "Wrong TOKEN";


?>
