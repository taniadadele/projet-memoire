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
 *		module   : syllabus.php
 *		projet   : Page de visualisation des syllabus
 *
 *		version  : 1.0
 *		auteur   : Thomas Dazy
 *		creation : 28/02/19
 */





 if (isset($_GET['idsyllabus'])) $idSyllabus = $_GET['idsyllabus'];

 if (isset($_GET['submit']) && $_GET['submit'] == "del")
 {
 	$query = "DELETE FROM `campus_syllabus` WHERE `_IDSyllabus` = '".$idSyllabus."' ";
 	@mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
 }


$temp_post = array('IDpole', 'IDpromotion', 'IDmatiere');
foreach ($temp_post as $value) if (isset($_POST[$value])) $$value = $_POST[$value];

$temp_get = array('submit', 'action');
foreach ($temp_get as $value) if (isset($_GET[$value])) $$value = $_GET[$value];


// Fonction appelée quand click sur "Générer les syllabus manquants"
if (isset($action) && $action == "generateMissing")
{
  $query = "SELECT t1._ID_pma
            FROM pole_mat_annee t1
            LEFT JOIN campus_syllabus t2 ON t2._IDPMA = t1._ID_pma
            WHERE t2._IDPMA IS NULL";

  $result = mysqli_query($mysql_link, $query);
  while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $query2 = "INSERT INTO `campus_syllabus`(`_IDSyllabus`, `_IDPMA`, `_objectifs`, `_programme`, `_visible`, `_idUser`, `_periode_1`, `_periode_2`, `_periode_total`) VALUES (NULL, '$row[0]', '', '', 'O', '', '0', '0', '0')";
  	@mysqli_query($mysql_link, $query2) or die('Erreur SQL !<br>'.$query2.'<br>'.mysqli_error($mysql_link));
  }

}



?>













<form id="formulaire" action="index.php?item=<?php echo $item; ?>&tri=on" method="post">



  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?php echo $msg->read($SYLLABUS_SYLLABUS); ?></h1>

    <div style="float: right; text-align: right;">


      <!-- Boutons d'action -->
      <div class="mb-3">
        <?php if ($_SESSION['CnxAdm'] == 255) { ?>
          <a href="<?php echo myurlencode("index.php?item=$item&action=generateMissing"); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint">
              Générer les syllabus manquant
          </a>
        <?php } ?>

        <?php if ($_SESSION["CnxAdm"] == 255 || $_SESSION["CnxGrp"] == 2 || $_SESSION["CnxGrp"] == 4) { ?>
          <a class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm noprint" href="<?php echo myurlencode("index.php?item=$item&cmde=new"); ?>">
            <i class="fas fa-plus fa-sm text-white-50" title="Create"></i>&nbsp;Nouveau
          </a>

          <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="<?php echo myurlencode($_SESSION["ROOTDIR"]."/exports.php?item=69&cmde=&idsyllabus=".@$idSyllabus."&IDpole=".@$IDpole."&IDpromotion=".@$IDpromotion."&IDmatiere=".@$IDmatiere); ?>">
            <i class="fas fa-upload fa-sm text-white-50" title="Export"></i>&nbsp;Exporter
          </a>
        <?php } ?>
        <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm noprint" href="#" onclick="window.print();return false;">
          <i class="fas fa-print fa-sm text-white-50" title="Imprimer"></i>&nbsp;Imprimer
        </a>
      </div>


      <div class="form-row">
        <!-- Supprimer les filtres -->
        <?php if ((isset($IDpromotion) && $IDpromotion != 0) or (isset($IDmatiere) && $IDmatiere != 0) or (isset($IDpole) && $IDpole != 0)) { ?>
          <a href="<?php echo myurlencode("index.php?item=$item"); ?>" class="btn btn-secondary noprint"><i class="fa fa-times" aria-hidden="true"></i></a>
        <?php } ?>

        <!-- Tri pas année -->
        <div class="col">
          <?php echo getNiveauSelect('IDpromotion', 'IDpromotion', @$IDpromotion, 1, 'formulaire'); ?>
        </div>

        <!-- Pôles -->
        <div class="col">
          <select id="IDpole" name="IDpole" class="custom-select" onchange="document.forms.formulaire.submit()">
            <?php
              if ($IDpole == "0") $selected = "selected";
              echo  '<option value="0" '.$selected.'>Tous les pôles</option>';
              $selected = $alreadyShown = '';
              $query  = "SELECT DISTINCT `_ID`, `_name`  FROM `pole` WHERE `_visible` = 'O' ";
              $result = mysqli_query($mysql_link, $query);
              while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                $query2 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_pole` = '".$row[0]."' ";
                $query2Where = "";
                if ($IDpromotion != 0)
                {
                  if ($query2Where == "") $query2Where = "AND ";
                  $query2Where .= "`_ID_year` = '".$IDpromotion."' ";
                }
                if ($IDmatiere != 0) $query2Where = "AND `_ID_matiere` = '".$IDmatiere."' ";
                $query2 .= $query2Where;
                $result2 = mysqli_query($mysql_link, $query2);
                while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
                  $query3 = "SELECT * FROM `campus_syllabus` WHERE `_IDPMA` = '".$row2[0]."' LIMIT 1 ";
                  $result3 = mysqli_query($mysql_link, $query3);
                  if (mysqli_num_rows($result3) != 0) {
                    if ($IDpole == $row[0]) $selected = "selected";
                    else $selected = "";
                    if ($toShowOption != $row[1]) echo '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
                    $toShowOption = $row[1];
                  }
                }
              }
            ?>
          </select>
        </div>


        <!-- Matières -->
        <div class="col">
          <select id="IDmatiere" name="IDmatiere" class="custom-select" onchange="document.forms.formulaire.submit()">
            <?php
            $toShowOption = "";
            $Query  = "SELECT `_IDmat`, `_titre` FROM `campus_data` WHERE `_visible` = 'O' ";
            $Query .= "ORDER BY `_IDmat` ";
            $result = mysqli_query($mysql_link, $Query);
            echo '<option value="0">'.$msg->read($SYLLABUS_ALL_MATIERES).'</option>';
            while ($cat = mysqli_fetch_row($result)) {
              $query2 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE `_ID_matiere` = '".$cat[0]."' ";
              $query2Where = "";
              if ($IDpromotion != 0)
              {
                if ($query2Where == "") $query2Where = "AND ";
                $query2Where .= "`_ID_year` = '".$IDpromotion."' ";
              }
              if ($IDpole != 0) $query2Where .= "AND `_ID_pole` = '".$IDpole."' ";
              $query2 .= $query2Where;
              $result2 = mysqli_query($mysql_link, $query2);
              while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
                $query3 = "SELECT * FROM `campus_syllabus` WHERE `_IDPMA` = '".$row2[0]."' LIMIT 1 ";
                $result3 = mysqli_query($mysql_link, $query3);
                if (mysqli_num_rows($result3) != 0) {
                  if ($toShowOption != $cat[1])
                  {
                    if ($IDmatiere == $cat[0]) $selected = 'selected'; else $selected = '';
                    echo '<option value="'.$cat[0].'" '.$selected.'>'.$cat[1].'</option>';
                  }
                  $toShowOption = $cat[1];
                }
              }
            }
            ?>
          </select>
        </div>


      </div>
    </div>
  </div>





  <?php
    // On récupère les données
    // Le join ici sert au tri sur PMA
    $query  = "SELECT s.* FROM `campus_syllabus` as s JOIN `pole_mat_annee` as p on s.`_IDPMA` = p.`_ID_PMA` WHERE `_IDPMA` != '' ";
    if ((isset($IDpromotion) && $IDpromotion != 0) || (isset($IDpole) && $IDpole != 0) || (isset($IDmatiere) && $IDmatiere != 0))
    {
      $querySearch = $querySearch2 = "";
      $query2 = "SELECT `_ID_pma` FROM `pole_mat_annee` WHERE ";
      if ($IDpromotion != 0) $querySearch .= "`_ID_year` = '".$IDpromotion."' ";
      if ($IDpole != 0)
      {
        if ($querySearch != "") $querySearch .= "AND ";
        $querySearch .= "`_ID_pole` = '".$IDpole."' ";
      }
      if ($IDmatiere != 0)
      {
        if ($querySearch != "") $querySearch .= "AND ";
        $querySearch .= "`_ID_matiere` = '".$IDmatiere."' ";
      }
      $query2 .= $querySearch;

      $result2 = mysqli_query($mysql_link, $query2);
      while ($row2 = mysqli_fetch_array($result2, MYSQLI_NUM)) {
        if($querySearch2 != "") $querySearch2 .= "OR ";
        $querySearch2 .= "`_IDPMA` = '".$row2[0]."' ";
      }
      $query .= "AND ".$querySearch2."";
    }
    $promotion = substr(getClassNiveauByClassID($_SESSION['CnxClass']), 0, 1);
    $query .= "ORDER BY p.`_ID_year` ASC, p.`_ID_pole` ASC, p.`_ID_matiere` ASC ";
    $periode_1 = $periode_2 = $periode_total = '00:00';
    $result = mysqli_query($mysql_link, $query);
    $num_rows = mysqli_num_rows($result);

    $listPeriodes = json_decode(getParam('periodeList'), true);
  ?>



  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Résultats: <?php echo $num_rows; ?></h6>
    </div>
    <div class="card-body">






      <?php
    if ( $result /* AND $page */ ) {



        echo '<table class="table table-bordered table-striped">';
          echo '<tr>';
            echo '<th>Année</th>';
            echo '<th>Pôle</th>';
            echo '<th>Matière</th>';
            echo '<th>Professeurs</th>';
            if (!getParam('showOnlyTotalPeriodGeneral')) echo '<th>'.$listPeriodes[1].'</th>';
            if (!getParam('showOnlyTotalPeriodGeneral')) echo '<th>'.$listPeriodes[2].'</th>';
            echo '<th>PT</th>';
            echo '<th class="noprint">Options</th>';
          echo '</tr>';

          while ( $row = mysqli_fetch_row($result) ) {
            $query2 = "SELECT * FROM `pole_mat_annee` WHERE `_ID_pma` = ".$row[1]." ";
            $result2 = mysqli_query($mysql_link, $query2);
            $row2   = remove_magic_quotes(mysqli_fetch_row($result2));
            $annee = getNiveauNameByNumNiveau($row2[1]);

            $query3 = "SELECT `_name` FROM `pole` WHERE `_ID` = ".$row2[2]." ";
            $result3 = mysqli_query($mysql_link, $query3);
            $row3   = remove_magic_quotes(mysqli_fetch_row($result3));
            $poleName = $row3[0];


            $query4 = "SELECT `_titre` FROM `campus_data` WHERE `_IDmat` = ".$row2[3]." ";
            $result4 = mysqli_query($mysql_link, $query4);
            $row4   = remove_magic_quotes(mysqli_fetch_row($result4));
            $matiereName = $row4[0];

            // Liste des profs:
            $profArray = explode(";", $row[5]);
            $profListe = "";
            foreach ($profArray as $key => $idProf) {
              if ($idProf != "") $profListe .= "<i class=\"fa fa-user\">&nbsp;".getUserNameByID($idProf)."</i>&nbsp;";
            }


            // suppression : il faut les droits du gestionnaire
            $deleteURL = ( $_SESSION["CnxAdm"] & 8 or strpos($row[5], ";".$_SESSION['CnxID'].";") !== false or $_SESSION['CnxAdm'] == 255 )
              ? myurlencode("index.php?item=$item&idsyllabus=$row[0]&submit=del")
              : "" ;


            // echo "<tr>";
            echo "<tr id=\"row_".$row[0]."\">";

              echo "<td>".$annee."</td>";
              echo "<td>".$poleName."</td>";
              echo "<td>".$matiereName."</td>";
              echo "<td class=\"align-right\">".$profListe."</td>";

              // Périodes
              if (!getParam('showOnlyTotalPeriodGeneral')) echo "<td>".$row[6]."</td>";
              if (!getParam('showOnlyTotalPeriodGeneral')) echo "<td>".$row[7]."</td>";
              echo "<td>".$row[8]."</td>";

              $temp_periode_1   = explode(':', $row[6]);
              $temp_periode_2   = explode(':', $row[7]);
              $temp_periode_tot = explode(':', $row[8]);

              $temp = explode(':', $periode_1);
              $hours_number = $temp[0] + $temp_periode_1[0];
              $minutes_number = @$temp[1] + @$temp_periode_1[1];
              while ($minutes_number >= 60)
              {
                $hours_number += 1;
                $minutes_number = $minutes_number - 60;
              }
              $periode_1 = $hours_number.':'.$minutes_number;

              $temp = explode(':', $periode_2);
              $hours_number = $temp[0] + $temp_periode_2[0];
              $minutes_number = @$temp[1] + @$temp_periode_2[1];
              while ($minutes_number >= 60)
              {
                $hours_number += 1;
                $minutes_number = $minutes_number - 60;
              }
              $periode_2 = $hours_number.':'.$minutes_number;

              $temp = explode(':', $periode_total);
              $hours_number = @$temp[0] + @$temp_periode_tot[0];
              $minutes_number = @$temp[1] + @$temp_periode_tot[1];
              while ($minutes_number >= 60)
              {
                $hours_number += 1;
                $minutes_number = $minutes_number - 60;
              }
              $periode_total = $hours_number.':'.$minutes_number;

              echo '<td style="width: 85px; text-align: center;" class="noprint" >';


              if ($_SESSION["CnxAdm"] == 255 OR $_SESSION["CnxGrp"] == 4 or strpos($row[5], ";".$_SESSION['CnxID'].";") !== false) {
                echo '<div style="display: inline-block; margin-top: 4px;margin-left: 4px;"><a href="index.php?item=69&cmde=view&idsyllabus='.$row[0].'" class="fa fa-eye" style="text-decoration: none; color: inherit;"></a></div>';
                echo '<div style="display: inline-block; margin-top: 4px;margin-left: 4px;"><a href="index.php?item=69&cmde=new&idsyllabus='.$row[0].'" class="fas fa-pencil-alt" style="text-decoration: none; color: inherit;"></a></div>';
                echo '<div style="display: inline-block; margin-top: 4px;margin-left: 4px; cursor: pointer;" id="deleteButton_'.$row[0].'" class="deleteButton fas fa-trash" idToDelete="'.$row[0].'\" deleteUrl="'.$deleteURL.'"><span class="icon-trash"></span></a></div>';
              }
              else echo "<div style=\"display: inline-block; margin-top: 4px;margin-left: 4px;\"><a href=\"index.php?item=69&cmde=view&idsyllabus=$row[0]\" class=\"fa fa-eye\" style=\"text-decoration: none; color: #333;\"></a></div>";
              echo "</td>";
            echo "</tr>";

            // Liste des prof de l'élève dynamique
            echo "<tr id=\"tr_$row[0]\" style=\"display: none;\">";
            echo "	<td></td>";
            echo "	<td colspan=\"6\" style=\"background-color: white;\"><table class=\"table table-striped\" style=\"margin-bottom: 0px;\" id=\"divtr_$row[0]\"></table></td>";
            echo "</tr>";
          }

          // Affichage des périodes totales
          echo '<tr>';
            echo '<td colspan="4" style="text-align: right;"><b>TOTAL:</b></td>';
            if (!getParam('showOnlyTotalPeriodGeneral')) echo '<td>'.$periode_1.'</td>';
            if (!getParam('showOnlyTotalPeriodGeneral')) echo '<td>'.$periode_2.'</td>';
            echo '<td>'.$periode_total.'</td>';
            echo '<td></td>';
          echo '</tr>';
        echo '</table>';
        }
      ?>
    </div>
  </div>
</form>



<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
<script src="script/sweetalert2.min.js"></script>

<style>
.swal-text {
  text-align: center;
}


</style>

<script src="script/bootstrap-notify-min.js"></script>


<script>
  jQuery('.deleteButton').click(function () {

    var deleteURL = jQuery(this).attr('deleteUrl');
    var deleteID = jQuery(this).attr('idToDelete');

    swal({
      title: "Attention",
      text: "Vous allez supprimer cet élément de façon définitive!\n Êtes-vous sûr ?",
      icon: "warning",
      buttons: ["Annuler", true],
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        jQuery.ajax({
          url : deleteURL,
          type : 'POST', // Le type de la requête HTTP, ici devenu POST
          data : '',
          dataType : 'html', // On désire recevoir du HTML
          success : function(code_html, statut){ // code_html contient le HTML renvoyé
            swal("L'élément à bien été supprimé !", {
              icon: "success",
            });
            jQuery("#row_" + deleteID).fadeOut();
          }
        });
      } else {
        swal("L'élément n'à pas été supprimé !");
      }
    });
  });



</script>
