<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 Thomas Dazy (contact@thomasdazy.fr)

   This file is part of Prométhée.

   Prom�th�e is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Prom�th�e is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Prom�th�e.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/


/*
 *		module   : download_copies_admin.htm
 *		projet   : la page de téléchargement des copies de l'élève
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 17/10/2019
 *		modif    :
 */

session_start();
$userID = $_GET['userID'];

require_once "config.php";
require_once "include/sqltools.php";
include_once("php/functions.php");
include 'include/fonction.php';
// connexion à la base de données
$mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);


if ($_SESSION['CnxAdm'] == 255)
{
  $zip = new ZipArchive;
  if ($zip->open('tmp/'.$userID.'.zip', ZipArchive::CREATE) === TRUE)
  {
    $query = "SELECT _IDimage, _title, _ext FROM images WHERE _ID = '".$userID."' AND _type = 'copies_eleves' ";
    $result = mysqli_query($mysql_link, $query);
    $compteur = 0;
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
      // On ajoute chaques fichiers et on leurs redonnent leurs nom (on rajoute le compteur devant le nom pour éviter les problèmes de fichiers qui on le même nom et s'écrasent les uns les autres)
      $zip->addFile('download/copies/eleves/'.$userID.'/'.$row[0].'.'.$row[2], $compteur."_".$row[1]);
      $compteur++;
    }
    $zip->close();
    $file = 'tmp/'.$userID.'.zip';
    $fileName = getUserNameByID($userID).'_copies.zip';

    header('Content-Type: application/force-download');
		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition: attachment; filename='.basename(str_replace(' ', '_', $fileName)));
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
		readfile("$file");

    unlink($file);
  }

}


?>
