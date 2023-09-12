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
 *		module   : notes_barcode.htm
 *		projet   : Page d'import des copies pour traitement en lot
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 10/07/19
 */

?>

<?php
  if (isset($_POST['validate'])) $validate = $_POST['validate'];
  else $validate = "";

  if (isset($_POST['IDmatiere'])) $IDmatiere = $_POST['IDmatiere'];
  else $IDmatiere = "";

  if (isset($_POST['IDuv'])) $IDuv = $_POST['IDuv'];
  else $IDuv = "";

  if (isset($_GET['action'])) $action = $_GET['action'];
  else $action = "";



  // ---------------------------------------------------------------------------
  // Fonction: Si on a confirmer la distribution des copies
  // ---------------------------------------------------------------------------
  if ($action == "confirm")
  {
    // On supprime le fichier initial (dans la BDD et le fichier en lui-même)
    $query = "SELECT `_IDimage`, `_ext`, `_ID` FROM `images` WHERE `_type` = 'copies' ";
    $result = mysql_query($query);
  	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
  		$filePath = $DOWNLOAD."/copies/files/".$row[2]."/".$row[0].".".$row[1];
      unlink($filePath);
      setLog('info', 'Suppression fichier copies', array('description' => 'Suppression du fichier original des copies car fichier traité et découpé', 'fileID' => $row[0], 'fileOriginalOwner' => $row[2]));
  	}
    $query = "DELETE FROM `images` WHERE `_type` = 'copies' ";
    $req = mysql_query($query);

    // On va envoyer les différents fichiers au élèves
    $files = scandir($DOWNLOAD."/copies/output/");
    foreach($files as $file)
    {
      if ($file != "index.htm" && $file != "index.php" && $file != "images" && $file != "." && $file != "..")
      {
        // Fonction: On récupère les différents intitulés et les différentes données
        $fileName_1 = explode("_", $file);
        // $code = substr($fileName_1[2], 0, -4);
        $code = $fileName_1[2];

        // On récupère l'ID de l'utilisateur dans la liste envoyé en post
        $userID = $_POST['user_'.$code];
        if ($userID != 0)
        {
          if ($fileName_1[0] != 0) $exam_name = "UV - ".getUVNameByUVID($fileName_1[0]);
          elseif ($fileName_1[1] != 0) $exam_name = getPoleNameByIdPole(getPoleIDByPMAID($fileName_1[1]))." - ".getMatNameByIdMat(getMatIDByPMAID($fileName_1[1]));
          $fileSize = filesize($DOWNLOAD."/copies/output/".$file);
          $uv_pma = $fileName_1[0]."_".$fileName_1[1];
          $currentDate = date('Y_m_d');
          // Fonction: On crée l'entrée du fichier dans la BDD
          $query  = "INSERT INTO `images` SET `_IDimage` = NULL, `_type` = 'copies_eleves', `_ID` = '".$userID."', `_title` = '".$exam_name." - ".$currentDate.".pdf', `_droits` = 0, ";
          $query .= "`_attr` = '".$uv_pma."', `_date` = NOW(), `_ext` = 'pdf', `_share` = '', `_size` = '".$fileSize."', `_parent` = '' ";
          $req = mysql_query($query);

          // Fonction: Génère le répertoire de l'utilisateur lors de l'arrivée sur la page si celui-ci n'en a pas dans le dossier download/copies/eleves/id_de_l'utilisateur
          if (!file_exists("$DOWNLOAD/copies/eleves/".$userID)) {
            mkdir("$DOWNLOAD/copies/eleves/".$userID, 0777, true);
          }
          $fh = fopen("$DOWNLOAD/copies/eleves/".$userID."/index.htm", 'w');

          // Fonction: Déplacer le fichier PDF dans le répertoire prévue pour les copies
          $last_inserted_id = mysql_insert_id();
          copy($DOWNLOAD."/copies/output/".$file, $DOWNLOAD."/copies/eleves/".$userID."/".$last_inserted_id.".pdf");

          setLog('info', 'Envoi de la copie à l\'élève', array(
            'description' => 'Fichier de copies traité, on envoie le fichier à l\'élève en le copiant dans son sous-dossier et en inserant la valeur dans la BDD',
            'eleveID' => $userID,
            'copieID' => $last_inserted_id,
            'insertQuery' => $query
          ));

        }
      }
      // Fonction: Supprimer le fichier temporaire
      unlink($DOWNLOAD."/copies/output/".$file);
    }
    setParam('isCurrentlyWorking', 0);
    echo "<meta http-equiv=\"refresh\" content=\"0\">";
  }

  // ---------------------------------------------------------------------------
  // Fonction: Si on a annulé la distribution des copies
  // ---------------------------------------------------------------------------
  elseif ($action == "abort")
  {
    // On supprime le fichier initial (dans la BDD et le fichier en lui-même)
    $query = "SELECT `_IDimage`, `_ext`, `_ID` FROM `images` WHERE `_type` = 'copies' ";
    $result = mysql_query($query);
  	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
  		$filePath = $DOWNLOAD."/copies/files/".$row[2]."/".$row[0].".".$row[1];
      unlink($filePath);
  	}
    $query = "DELETE FROM `images` WHERE `_type` = 'copies' ";
    $req = mysql_query($query);

    // On va supprimer les différents fichiers
    $files = scandir($DOWNLOAD."/copies/output/");
    foreach($files as $file)
    {
      if ($file != "index.htm" && $file != "index.php" && $file != "." && $file != "..")
      {
        // Fonction: Supprimer le fichier temporaire
        unlink($DOWNLOAD."/copies/output/".$file);
      }
    }
    setParam('isCurrentlyWorking', 0);
    echo "<meta http-equiv=\"refresh\" content=\"0\">";
  }

?>

<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
    <b>
  		<?php print($msg->read($NOTES_IMPORT_RESULT_TITLE)); ?>
    </b>
	</div>
</div>

<hr />


  <div class="maincontent">
    <form action="?item=60&cmde=barcode&action=confirm" method="POST">

      <style>
        @font-face {
          font-family: Barcode;
          src: url(fonts/barcode.woff);
        }
      </style>


      <table class="table table-bordered table-striped">

        <tr>
          <?php
          $files = scandir($DOWNLOAD."/copies/output/");
          $compteur = 0;
          foreach($files as $file)
          {
            if ($file != "index.htm" && $file != "index.php" && $file != "images" && $file != "." && $file != ".." && $file != ".DS_Store")
            {
              if ($compteur == 0)
              {
                $fileName_1 = explode("_", $file);
                echo "<th colspan=\"4\" style=\"text-align: center;\">";
                  if ($fileName_1[0] != 0) echo "UV - ".getUVNameByUVID($fileName_1[0]);
                  elseif ($fileName_1[1] != 0) echo getPoleNameByIdPole(getPoleIDByPMAID($fileName_1[1]))." - ".getMatNameByIdMat(getMatIDByPMAID($fileName_1[1]));
                echo "</th>";
              }
              $compteur++;
            }
          }

          // Si on a pas de fichiers alors on a soit un traitement en cours soit aucuns traitement, donc on annule tout
          if ($compteur == 0)
          {
            setParam('isCurrentlyWorking', 0);
            echo "<meta http-equiv=\"refresh\" content=\"0\">";
          }
          ?>


        </tr>
        <tr></tr>
        <tr>
          <th style="text-align: center;">Copies: <?php echo $compteur; ?></th>
          <th style="text-align: center;" width="1"></th>
          <th style="text-align: center;">Nom - prénom de l'élève</th>
          <th style="text-align: center;">Code barre</th>
        </tr>


        <?php

          // On récupère la liste des élèves:
          $query = "SELECT _name, _fname, _ID, _IDclass FROM user_id WHERE _adm = 1 AND _IDclass != '0' AND _IDgrp = '1' ORDER BY _IDclass DESC, _name ASC ";
          $result = mysql_query($query);
          $list_eleves = Array();
          while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
            $tempArray = Array();
            $tempArray['ID'] = $row[2];
            $tempArray['name'] = $row[0];
            $tempArray['fname'] = $row[1];
            $tempArray['promo'] = $row[3];
            $list_eleves[] = $tempArray;
          }


          $files = scandir($DOWNLOAD."/copies/output/");
          foreach($files as $file) {
            if ($file != "index.htm" && $file != "index.php" && $file != "images" && $file != "." && $file != ".." && $file != ".DS_Store")
            {
              $fileName_1 = explode("_", $file);
              $code = $fileName_1[2];
              $userID = getUserIDByBarcode($code);
              if ($userID != 0) $userName = getUserNameByID($userID);
              else $userName = "";

              if ($fileName_1[3] == 1) $warning = "<a href=\"#\" class=\"tooltipForJS\" data-toggle=\"tooltip\" title=\"Un seul code barre a été détecté dans ce fichier\"><i class=\"fa fa-exclamation-triangle\" style=\"font-size: 20px;\"></i></a>";
              else $warning = "";


              echo "<tr>";
                echo "<td style=\"width: 75px;\"><a href=\"download_copie.php?file=".$file."\"><img src=\"images/type/pdf.png\" style=\"width: 100%;\"></a></td>";
                echo "<td style=\"vertical-align: middle;\">".$warning.$file."</td>";


                // if ($userName != "") echo "<td style=\"text-align: center; vertical-align: middle;\">".$userName."</td>";
                // else echo "<td style=\"text-align: center; font-family: Barcode; vertical-align: middle;\">(".$code.")</td>";

                echo "<td>";
                  if ($userName == "") echo "<div class=\"control-group error\">";
                    echo "<select name=\"user_".$code."\">";
                      if ($userName == "") echo "<option value=\"0\" selected>Introuvable</option>";
                      else echo "<option value=\"0\">Ne pas prendre en compte</option>";
                      foreach ($list_eleves as $key => $value)
                      {
                        if ($value['promo'] != $oldPromo || !isset($oldPromo))
                        {
                          if (isset($oldPromo)) echo "</optgroup>";
                          echo "<optgroup label=\"".getClassNameByClassID($value['promo'])."\">";
                          $oldPromo = $value['promo'];
                        }
                        if ($value['ID'] == $userID) $selected = "selected";
                        else $selected = "";
                        echo "<option value=\"".$value['ID']."\" ".$selected.">".$value['name']." ".$value['fname']."</option>";
                      }
                    echo "</select>";
                  if ($userName == "") echo "</div>";
                echo "</td>";


                echo "<td style=\"text-align: center; font-family: Barcode; vertical-align: middle;\">(".$code.")</td>";
              echo "</tr>";
            }
          }


        ?>

      </table>
      <div style="float: right;">
        <input type="submit" class="btn btn-success" style="color: #ffffff;" value="Confirmer les résultats">
        <!-- <a   href="index.php?item=60&cmde=barcode&action=confirm"></a> -->
        <a class="btn btn-danger" style="color: #ffffff;" href="index.php?item=60&cmde=barcode&action=abort">Annuler</a>
      </div>

    </form>
  </div>

  <!-- Script servant à activer les Tooltips -->
  <script>
    jQuery('.tooltipForJS').tooltip()
  </script>
