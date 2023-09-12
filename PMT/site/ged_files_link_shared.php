<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2019 by Thomas Dazy (contact@thomasdazy.fr)

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
 *		module   : ged_files_link_shared.php
 *		projet   : la page de téléchargement de fichiers partagés avec le lien de partage
 *
 *		version  : 1.1
 *		auteur   : Thomas Dazy
 *		creation : 10/09/2019
 *
 */




  if (isset($_GET['file_id'])) $file_token = addslashes(stripslashes($_GET['file_id']));
  else $file_token = "";





  $current_time = date('Y-m-d H:i:s');
  $chaine = $row[0].";".$row[1].";".$current_time;
  $file_link = openssl_encrypt($chaine, "AES-128-ECB" ,$CRYPT_KEY);


  if ($file_token != "")
  {
    $decrypted_chaine = openssl_decrypt($file_token, "AES-128-ECB" ,$CRYPT_KEY);
    $temp = explode(';', $decrypted_chaine);

    $fileID    = $temp[0];
    $userID    = $temp[1];
    $timeShare = $temp[2];

    if (time() <= strtotime($timeShare.' + '.$TIMEOUT_GED.' minute'))
    {
      $query = "SELECT _title, _ext FROM images WHERE _IDimage = '".$fileID."' AND _ID = '".$userID."' LIMIT 1 ";
      $result = mysqli_query($mysql_link, $query);
      while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {




        $NEWthumbnailUrl = "download/ged/files/".$userID."/thumbnail/".$fileID.".".$row[1];

				if(file_exists($NEWthumbnailUrl)) $thumbnail_url = "ged_thumbnail.php?fileID=".base64_encode($fileID);

				else $thumbnail_url = "images/filetype/".strtolower($row[1]).".png";







        echo "<div class=\"maintitle\">";
          echo "<div style=\"text-align: center;\">";
            echo "<h4>Télécharger un fichier joint</h4>";
          echo "</div>";
        echo "</div>";
        echo "<div class=\"maincontent\">";
          echo "<hr />";

          echo "<div style=\"width: 100%; height: 100%; vertical-align: middle;\">";
            echo "<table style=\"margin: auto;\">";
              echo "<tr>";
                echo "<td>";
                  echo "<img src=\"$thumbnail_url\">";
                echo "</td>";
                echo "<td>";
                  echo "<b>".$row[0]."</b>";
                echo "</td>";
              echo "</tr>";

              echo "<tr>";
                echo "<td colspan=\"2\">";

                  echo "<a href=\"download_ged.php?action=link_file&token=".$file_token."\" class=\"btn btn-default\">Télécharger</a>";
                echo "</td>";
              echo "</tr>";
            echo "</table>";
          echo "</div>";
        echo "</div>";
      }
    }
    else echo "<div class=\"alert alert-danger\">Le fichier n'est plus disponible</div>";

  }


 ?>


 <style>

  td {
    padding: 5px 20px;
    text-align: center;
  }

 </style>
