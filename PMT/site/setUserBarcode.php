<?php

$query = "SELECT _name, _fname, _ID, _IDclass FROM user_id WHERE _adm = 1 AND _IDgrp = 1 ";

$result = mysqli_query($mysql_link, $query);

while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
  $code = substr($row[1], 0, 1).$row[0];
  $code = str_replace(' ', '', $code);
  $code = str_replace('-', '', $code);
  $code = str_replace('.', '', $code);
  $code = strtolower(trim($code));
  $code .= getClassNiveauByClassID($row[3]);

  echo $row[0]." ".$row[1]." ".$row[3]." - ".$code."<br>";

  $query_check = "SELECT _valeur FROM rubrique_data WHERE _IDdata = '".$row[2]."' AND _IDrubrique = 10 ";
  $result_check = mysqli_query($mysql_link, $query_check);
  $numRows = mysqli_num_rows($result_check);
  if ($numRows > 0)
  {
    $query = "UPDATE rubrique_data SET _valeur = '".$code."' WHERE _IDdata = '".$row[2]."' AND _IDrubrique = 10 ";
    mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }
  else
  {
    $query = "INSERT INTO `rubrique_data`(`_IDrubrique`, `_IDdata`, `_valeur`) VALUES ('10', '".$row[2]."', '".$code."') ";
    mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
  }


}
