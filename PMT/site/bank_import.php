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
 *		module   : bank_import.php
 *		projet   : Page d'import des relevés bancaires
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 18/09/19
 */

?>


<div class="maintitle" style="background-image: url('<?php echo $_SESSION["CfgHeader"]; ?>'); background-repeat: repeat;">
	<div style="text-align: center;">
    <b>
  		Import des relevés bancaires
    </b>
	</div>
</div>

<hr />

<?php
  if (isset($_GET['action'])) $action = $_GET['action'];
  else $action = "";

  if ($action == "upload")
  {
    // On crée le répertoire des imports bancaires s'il n'existe pas déjà
    if (!file_exists("tmp/bank")) {
      mkdir("tmp/bank", 0777, true);
    }
    // On crée le fichier index.htm à la racine de ce même répertoire s'il n'existe pas déjà
    $fh = fopen("tmp/bank/index.htm", 'w');



    $uploaddir = 'tmp/bank/';
    $uploadfile = $uploaddir . basename($_FILES['file_upload']['name']);

    $csv_mimetypes = array(
      'text/csv',
      'text/plain',
      'application/csv',
      'text/comma-separated-values',
      'application/excel',
      'application/vnd.ms-excel',
      'application/vnd.msexcel',
      'text/anytext',
      'application/octet-stream',
      'application/txt',
    );

    // Si le fichier est bien un CSV
    if (in_array($_FILES['file_upload']['type'], $csv_mimetypes)) {

      if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $uploadfile)) {
        // echo "Le fichier est valide, et a été téléchargé
        //       avec succès. Voici plus d'informations :\n";
        echo "<div class=\"alert alert-success\"><i class=\"fa fa-check\"></i>&nbsp;Le fichier à bien été envoyé</div><hr>";
        $status_upload = "success";
      } else {
        // echo "Attaque potentielle par téléchargement de fichiers.
        //       Voici plus d'informations :\n";
        $status_upload = "error";
      }
    }
    else
    {
      $status_upload = "error";
      echo "<div class=\"alert alert-warning\"><i class=\"fa fa-warning\"></i>&nbsp;Veuillez n'envoyer que des fichiers CSV</div><hr>";
    }
  }




  if ($action == "save_data")
  {
    // Pour compter le nombre d'INSERT
    $count_of_upload = 0;
    // Pour chaques valeurs envoyés:
    for ($i = 0; $i <= $_POST['count_of_data']; $i++) {
      $newDate = substr($_POST['date_'.$i], 6, 4)."-".substr($_POST['date_'.$i], 3, 2)."-".substr($_POST['date_'.$i], 0, 2);
      $date = date('Y-m-d', strtotime($newDate));
      if ($_POST['ID_eleve_'.$i] != 0)
      {
        $libelle = preg_replace('/\s+/', ' ', $_POST['libele_'.$i]);
        // On les inserts dans al BDD
        $query  = "INSERT INTO `bank_data`(`_ID`, `_date`, `_libele`, `_price`, `_IDeleve`, `_status`, `_attr`) ";
        $query .= "VALUES (NULL, '".$date."', '".addslashes($libelle)."', '".str_replace(',', '.', $_POST['price_'.$i])."', '".$_POST['ID_eleve_'.$i]."', '1', NULL) ";
        mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
        $count_of_upload++;
      }

    }
    // On supprime le fichier original
    unlink($_POST['file_dir']);
    echo "<div class=\"alert alert-success\"><i class=\"fa fa-check\"></i>&nbsp;".$count_of_upload." imports réussis</div>";
    echo "<a href=\"?item=61\" class=\"btn btn-danger\" style=\"color: white;\"><i class=\"fa fa-chevron-left\"></i>&nbsp;Retour à la liste</a>";
  }

?>




  <?php if ($action != "upload" && $action != "save_data") { ?>

    <div class="maincontent">
      <form action="?item=61&cmde=import&action=upload" enctype="multipart/form-data" method="POST">
        <!-- <strong>Sélectionnez votre fichier à importer</strong> -->
        <h3>Sélectionnez votre fichier à importer</h3>

        <br>

        <input type="file" name="file_upload" style="margin-bottom: 10px;" required>

        <br>

        <input class="btn btn-success" type="submit" value="Envoyer">

    </div>

  <?php } elseif ($status_upload == "success") { ?>

    <div class="maincontent">
      <form action="?item=61&cmde=import&action=save_data" method="POST">
        <strong>Résultats:</strong><br><br>
        <table style="width: 100%;" class="table table-bordered table-striped">

          <tr>
            <th>Date</th>
            <th>Intitulé</th>
            <th>Élève</th>
            <th>Montant</th>
          </tr>


          <?php
            // On récupère la liste des élèves:
            $query = "SELECT _name, _fname, _ID, _IDclass FROM user_id WHERE _adm = 1 AND _IDclass != '0' AND _IDgrp = '1' ORDER BY _IDclass DESC, _name ASC ";
            $result = mysqli_query($mysql_link, $query);
            $list_eleves = Array();
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
              $tempArray = Array();
              $tempArray['ID'] = $row[2];
              $tempArray['name'] = $row[0];
              $tempArray['fname'] = $row[1];
              $tempArray['promo'] = $row[3];
							$tempArray['barcode'] = getUserBarCodeByID($row[2]);
              $list_eleves[] = $tempArray;
            }

            $file_started = "no";
            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen($uploadfile,'r');
            $ID_counter = 0;
            while ( ($data = fgetcsv($handle, 0, ';') ) !== FALSE )
            {

              // Si on a dépasé l'en-tête
              if ($file_started == "yes")
              {
                // On ne prend que les crédits et pas les débits
                if ($data[2] >= 0)
                {
                  // On stoque les info utiles dans des variables
                  $date_operation     = trim($data[0]);
                  $libele_operation   = trim($data[1]);
                  $montant_operation  = trim($data[2]);

									$userID_operation = "";

                  // On cherche dans le libélé un nombre de 11 caractères:
                  $tempArray = explode(" ", $libele_operation);
                  foreach ($tempArray as $key => $value) {
                    // Si le texte fait bien 11 caractères de long et est bien un nombre alors on le stoque pour l'afficher
                    // if (strlen(trim($value)) == 11 && is_numeric(trim($value)))
                    // {
                    //   $userID_operation = getUserIDByINE($value);
                    // }
                    // else $userID_operation = "";
										if (!is_numeric(substr($value, 0, 1)) && is_numeric(substr($value, -1, 1)))
										{
											foreach ($list_eleves as $key_2 => $value_2) {
												if ($value_2['barcode'] == strtolower(trim($value))) $userID_operation = $value_2['ID'];
											}
										}

                  }

                  echo "<tr>";
                    echo "<td>".$date_operation."</td>";
                    echo "<td>".$libele_operation."</td>";

                    // On fait un SELECT avec la liste des élèves et l'élève détécté sélectionné
                    echo "<td>";
                      echo "<select name=\"ID_eleve_".$ID_counter."\">";
                        echo "<option value=\"0\">Ne pas prendre en compte</option>";
                        foreach ($list_eleves as $key => $value) {
                          if ($value['promo'] != $oldPromo || !isset($oldPromo))
                          {
                            if (isset($oldPromo)) echo "</optgroup>";
                            echo "<optgroup label=\"".getClassNameByClassID($value['promo'])."\">";
                            $oldPromo = $value['promo'];
                          }
                          if ($value['ID'] == $userID_operation) $selected = "selected";
                          else $selected = "";
                          echo "<option value=\"".$value['ID']."\" ".$selected.">".$value['name']." ".$value['fname']."</option>";
                        }
                      echo "</select>";
                    echo "</td>";


                    // echo "<td>".getUserNameByID($userID_operation)."</td>";
                    echo "<td>".$montant_operation." €</td>";
                  echo "</tr>";

                  echo "<input type=\"hidden\" name=\"date_".$ID_counter."\" value=\"".$date_operation."\">";
                  echo "<input type=\"hidden\" name=\"libele_".$ID_counter."\" value=\"".$libele_operation."\">";
                  echo "<input type=\"hidden\" name=\"price_".$ID_counter."\" value=\"".$montant_operation."\">";

                  $ID_counter++;
                }



              }
              if ($data[0] == "Date") $file_started = "yes";


            }
            ini_set('auto_detect_line_endings',FALSE);


          ?>

        </table>

        <input type="hidden" name="count_of_data" value="<?php echo $ID_counter; ?>">
        <input type="hidden" name="file_dir" value="<?php echo $uploadfile; ?>">
        <input type="submit" style="float: right;" class="btn btn-success" value="Confirmer">
      </form>
    </div>



  <?php } ?>
