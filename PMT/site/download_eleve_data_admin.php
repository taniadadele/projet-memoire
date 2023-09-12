<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 Thomas Dazy (contact@thomasdazy.fr)

   This file is part of Prométhée.

   Prométhée is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Prométhée is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/


/*
 *		module   : download_eleve_data_admin.php
 *		projet   : la page de téléchargement des données utilisateur
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 17/10/2019
 *		modif    :
 */

  session_start();
  error_reporting(0);

  // Permet de laisser le script tourner même si l'utilisateur coupe la connexion (pour supprimer le fichier à la fin même si timeout ou si l'utilisateur annule le téléchargement)
  ignore_user_abort(true);

  $userID = $_GET['userID'];

  if ($_SESSION['CnxAdm'] == 255)
  {

    require_once "config.php";
    require_once "include/sqltools.php";
    include_once("php/functions.php");
    include 'include/fonction.php';
    // connexion à la base de données
    $mysql_link = connectDatabase($SERVER, $USER, $PASSWD, $DATABASE, $SERVPORT, $PERSISTENT);

    $start_Y = getParam('START_Y');

    function empty_dir($dir)
    {
      if (is_dir($dir))
      {
        $file_list = scandir($dir);
        foreach ($file_list as $key => $value) {
          if (is_dir($dir.'/'.$value) && $value != "." && $value != "..")
          {
            empty_dir($dir.'/'.$value);
          }
          elseif ($value != "." && $value != "..")
          {
            unlink($dir.'/'.$value);
          }
        }
        rmdir($dir);
      }
      elseif(file_exists($dir) && substr($dir, 0, -1) != '.')
      {
        unlink($dir);
      }
    }

    // On vide le dossier de l'élève pour avoir un export propre
    empty_dir('tmp/'.$userID);
    // On crée le dossier de l'élève (étant donné que l'on l'a supprimé juste avant)
    mkdir('tmp/'.$userID);
    $fh = fopen("tmp/".$userID."/index.htm", 'w');
    // On crée le dossier de stoquage des infos
    mkdir('tmp/user_data');
    $fh = fopen("tmp/user_data/".$userID."/index.htm", 'w');

    // ----------------------------------------------------------------------------
    // On récupère les URL que l'on va apeller pour récupérer les données
    // ----------------------------------------------------------------------------
    $listUrl = array();
    // Emploi du temps
      $listUrl[] = "exports.php?item=29&cmde=&sortOrder=0&sortBy=0&idpromotion=".getUserClassIDByUserID($userID)."&idmatiere=0&idpole=0&idprof=0&date_1=01.".getParam('START_M').".".getParam('START_Y')."&date_2=31.".getParam('END_M').".".getParam('END_Y')."&time_1=07:00&time_2=23:45&status=0&lessonStatus=0&currentPage=1&nbElemPerPage=&type_UV=0&tofolder=".$userID;
    // Syllabus
      $listUrl[] = "exports.php?item=69&cmde=&idsyllabus=&IDpole=0&IDpromotion=".getClassNiveauByClassID(getUserClassIDByUserID($userID))."&IDmatiere=0&tofolder=".$userID;
    // Anciens bulletins
      $query = "SELECT _year FROM user_log WHERE _IDuser = '".$userID."' AND _year < '".$start_Y."' ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $listUrl[] = "notes_pdf.php?IDcentre=1&IDclass=282&IDeleve=".$userID."&year=".$row[0]."&period=0&tofolder=".$userID;
      }
    // Bulletin actuel
      $listUrl[] = "notes_pdf.php?IDcentre=1&IDclass=282&IDeleve=".$userID."&year=".$start_Y."&period=0&tofolder=".$userID;

    // Absences
      $listUrl[] = "absent_export.php?IDeleve=".$userID."&year=".$start_Y."&tofolder=".$userID;

    // GED administrateur
      $query = "SELECT _IDimage, _title, _ext FROM images WHERE _ID = '".$userID."' AND _type = 'user' ";
      $result = mysqli_query($mysql_link, $query);
      $compteur = 0;
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        // On ajoute chaques fichiers et on leurs redonnent leurs nom (on rajoute le compteur devant le nom pour éviter les problèmes de fichiers qui on le même nom et s'écrasent les uns les autres)
        copy('download/user_ged/files/'.$userID.'/'.$row[0].'.'.$row[2], 'tmp/'.$userID.'/GED_'.$compteur."_".$row[1]);
        $compteur++;
      }
    // Copies de l'élève
      $query = "SELECT _IDimage, _title, _ext FROM images WHERE _ID = '".$userID."' AND _type = 'copies_eleves' ";
      $result = mysqli_query($mysql_link, $query);
      $compteur = 0;
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        // On ajoute chaques fichiers et on leurs redonnent leurs nom (on rajoute le compteur devant le nom pour éviter les problèmes de fichiers qui on le même nom et s'écrasent les uns les autres)
        copy('download/copies/eleves/'.$userID.'/'.$row[0].'.'.$row[2], 'tmp/'.$userID.'/copie_'.$compteur."_".$row[1]);
        $compteur++;
      }
    // ----------------------------------------------------------------------------


    // ----------------------------------------------------------------------------
    // On fait les appels pour récupérer les données
    // ----------------------------------------------------------------------------
    $fullCurrentUrl = $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
    $url_temp = explode('download_eleve_data_admin', $fullCurrentUrl);
    $url_start = $url_temp[0];
    // On fait les appels curl pour générer les fichiers
    foreach ($listUrl as $key => $value) {
       $url = $web = $url_start.$value;

       // Permet de garder l'identification dans l'appel cURL
       $useragent = $_SERVER['HTTP_USER_AGENT'];
       $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
       session_write_close();

       $curl = curl_init();
       curl_setopt($curl, CURLOPT_URL, $url);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($curl, CURLOPT_HEADER, false);

       curl_setopt($curl,CURLOPT_USERAGENT, $useragent);
       curl_setopt( $curl, CURLOPT_COOKIE, $strCookie );

       // execute and return string (this should be an empty string '')
       $str = curl_exec($curl);
       curl_close($curl);
    }
    // ----------------------------------------------------------------------------


    // ----------------------------------------------------------------------------
    // On crée l'archive des données utilisateurs
    // ----------------------------------------------------------------------------
    $zip = new ZipArchive;
    if ($zip->open('tmp/user_data/'.$userID.'.zip', ZipArchive::CREATE) === TRUE)
    {
      foreach (scandir('tmp/'.$userID) as $key => $value) {
        if (substr($value, 0, -1) != '.' && $value != '..' && $value != '.' && $value != 'index.htm')
        {
          if (substr($value, 0, 9) == 'bulletin_') $fileName = 'bulletin/'.substr($value, 9);
          elseif (substr($value, 0, 6) == 'copie_') $fileName = 'copies/'.substr($value, 6);
          elseif (substr($value, 0, 4) == 'GED_') $fileName = 'ged_administrative/'.substr($value, 4);
          else $fileName = $value;
          // On ajoute chaques fichiers
          $zip->addFile('tmp/'.$userID.'/'.$value, $fileName);
        }

      }
      $zip->close();
    }
    // ----------------------------------------------------------------------------


    // ----------------------------------------------------------------------------
    // On vide le répertoire initial
    // ----------------------------------------------------------------------------
    empty_dir('tmp/'.$userID);
    // ----------------------------------------------------------------------------


    // ----------------------------------------------------------------------------
    // On télécharge le fichier créer
    // ----------------------------------------------------------------------------
    $file = 'tmp/user_data/'.$userID.'.zip';
    $fileName = "donnees_de_l'utilisateur_".getUserNameByID($userID).'.zip';

    header('Content-Type: application/force-download');
    header("Content-Transfer-Encoding: binary");
    header('Content-Disposition: attachment; filename='.basename(str_replace(' ', '_', $fileName)));
    header('Pragma: no-cache');
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    readfile("$file");
    // ----------------------------------------------------------------------------


    // ----------------------------------------------------------------------------
    // On supprime le fichier
    // ----------------------------------------------------------------------------
    unlink($file);
    // ----------------------------------------------------------------------------
  }


?>
